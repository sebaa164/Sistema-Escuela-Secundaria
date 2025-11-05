<?php

namespace App\Http\Controllers\Profesor;

use App\Http\Controllers\Controller;
use App\Models\Calificacion;
use App\Models\Evaluacion;
use App\Models\Inscripcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CalificacionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (Auth::user()->tipo_usuario !== 'profesor') {
                abort(403, 'Acceso denegado.');
            }
            return $next($request);
        });
    }

    /**
     * Lista de calificaciones por evaluación
     */
    public function index($evaluacionId)
    {
        $evaluacion = Evaluacion::with([
            'seccion.curso',
            'tipoEvaluacion',
            'calificaciones.estudiante'
        ])
        ->whereHas('seccion', function($query) {
            $query->where('profesor_id', Auth::id());
        })
        ->findOrFail($evaluacionId);

        $calificaciones = $evaluacion->calificaciones()
            ->with('estudiante')
            ->orderBy('estudiante_id')
            ->get();

        return view('profesor.calificaciones.index', compact('evaluacion', 'calificaciones'));
    }

    /**
     * Formulario para calificar individualmente
     */
    public function edit($id)
    {
        $calificacion = Calificacion::with([
            'evaluacion.seccion.curso',
            'evaluacion.tipoEvaluacion',
            'estudiante'
        ])
        ->whereHas('evaluacion.seccion', function($query) {
            $query->where('profesor_id', Auth::id());
        })
        ->findOrFail($id);

        return view('profesor.calificaciones.edit', compact('calificacion'));
    }

    /**
     * Actualizar calificación individual
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nota' => 'nullable|numeric|min:0|max:' . ($request->nota_maxima ?? 100),
            'comentarios' => 'nullable|string|max:1000',
            'tiempo_empleado' => 'nullable|integer|min:0'
        ]);

        $calificacion = Calificacion::whereHas('evaluacion.seccion', function($query) {
                $query->where('profesor_id', Auth::id());
            })
            ->findOrFail($id);

        $calificacion->update([
            'nota' => $request->nota,
            'comentarios' => $request->comentarios,
            'tiempo_empleado' => $request->tiempo_empleado,
            'fecha_calificacion' => now(),
            'estado' => $request->nota !== null ? 'calificada' : 'pendiente'
        ]);

        // Actualizar nota final de la inscripción si es necesario
        $this->actualizarNotaFinal($calificacion);

        return redirect()->route('profesor.calificaciones.index', $calificacion->evaluacion_id)
            ->with('success', 'Calificación actualizada correctamente.');
    }

    /**
     * Calificación masiva por lotes
     */
    public function calificarLote(Request $request, $evaluacionId)
    {
        $request->validate([
            'calificaciones' => 'required|array',
            'calificaciones.*.id' => 'required|exists:calificaciones,id',
            'calificaciones.*.nota' => 'nullable|numeric|min:0',
            'calificaciones.*.comentarios' => 'nullable|string|max:500'
        ]);

        $evaluacion = Evaluacion::whereHas('seccion', function($query) {
                $query->where('profesor_id', Auth::id());
            })
            ->findOrFail($evaluacionId);

        DB::beginTransaction();
        try {
            $actualizadas = 0;
            
            foreach ($request->calificaciones as $datos) {
                $calificacion = Calificacion::where('id', $datos['id'])
                    ->where('evaluacion_id', $evaluacionId)
                    ->first();

                if ($calificacion && isset($datos['nota']) && $datos['nota'] !== null) {
                    $calificacion->update([
                        'nota' => $datos['nota'],
                        'comentarios' => $datos['comentarios'] ?? null,
                        'fecha_calificacion' => now(),
                        'estado' => 'calificada'
                    ]);

                    $this->actualizarNotaFinal($calificacion);
                    $actualizadas++;
                }
            }

            DB::commit();
            return redirect()->back()->with('success', "Se actualizaron {$actualizadas} calificaciones correctamente.");

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error al actualizar las calificaciones.');
        }
    }

    /**
     * Aplicar nota a todos los estudiantes
     */
    public function aplicarATodos(Request $request, $evaluacionId)
    {
        $request->validate([
            'nota' => 'required|numeric|min:0',
            'comentarios' => 'nullable|string|max:500',
            'solo_pendientes' => 'boolean'
        ]);

        $evaluacion = Evaluacion::whereHas('seccion', function($query) {
                $query->where('profesor_id', Auth::id());
            })
            ->findOrFail($evaluacionId);

        $query = $evaluacion->calificaciones();
        
        if ($request->solo_pendientes) {
            $query->where('estado', 'pendiente');
        }

        $calificaciones = $query->get();
        
        DB::beginTransaction();
        try {
            foreach ($calificaciones as $calificacion) {
                $calificacion->update([
                    'nota' => $request->nota,
                    'comentarios' => $request->comentarios,
                    'fecha_calificacion' => now(),
                    'estado' => 'calificada'
                ]);

                $this->actualizarNotaFinal($calificacion);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Calificaciones aplicadas correctamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error al aplicar las calificaciones.');
        }
    }

    /**
     * Importar calificaciones desde CSV
     */
    public function importarCsv(Request $request, $evaluacionId)
    {
        $request->validate([
            'archivo_csv' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        $evaluacion = Evaluacion::whereHas('seccion', function($query) {
                $query->where('profesor_id', Auth::id());
            })
            ->findOrFail($evaluacionId);

        $archivo = $request->file('archivo_csv');
        $datos = array_map('str_getcsv', file($archivo->getRealPath()));
        $encabezados = array_shift($datos);

        // Validar formato del CSV
        if (!in_array('email', $encabezados) || !in_array('nota', $encabezados)) {
            return redirect()->back()->with('error', 'El archivo CSV debe contener las columnas "email" y "nota".');
        }

        $emailIndex = array_search('email', $encabezados);
        $notaIndex = array_search('nota', $encabezados);
        $comentarioIndex = array_search('comentarios', $encabezados);

        DB::beginTransaction();
        try {
            $procesadas = 0;
            $errores = [];

            foreach ($datos as $fila) {
                $email = trim($fila[$emailIndex]);
                $nota = is_numeric($fila[$notaIndex]) ? (float)$fila[$notaIndex] : null;
                $comentarios = $comentarioIndex !== false ? $fila[$comentarioIndex] : null;

                if ($nota === null) continue;

                $calificacion = Calificacion::whereHas('estudiante', function($query) use ($email) {
                        $query->where('email', $email);
                    })
                    ->where('evaluacion_id', $evaluacionId)
                    ->first();

                if ($calificacion) {
                    $calificacion->update([
                        'nota' => $nota,
                        'comentarios' => $comentarios,
                        'fecha_calificacion' => now(),
                        'estado' => 'calificada'
                    ]);

                    $this->actualizarNotaFinal($calificacion);
                    $procesadas++;
                } else {
                    $errores[] = "No se encontró estudiante con email: {$email}";
                }
            }

            DB::commit();
            
            $mensaje = "Se procesaron {$procesadas} calificaciones correctamente.";
            if (count($errores) > 0) {
                $mensaje .= " Errores: " . implode(', ', array_slice($errores, 0, 3));
            }

            return redirect()->back()->with('success', $mensaje);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error al importar las calificaciones.');
        }
    }

    /**
     * Exportar plantilla CSV
     */
    public function exportarPlantilla($evaluacionId)
    {
        $evaluacion = Evaluacion::with([
            'calificaciones.estudiante'
        ])
        ->whereHas('seccion', function($query) {
            $query->where('profesor_id', Auth::id());
        })
        ->findOrFail($evaluacionId);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="plantilla_calificaciones.csv"'
        ];

        $callback = function() use ($evaluacion) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['nombre', 'apellido', 'email', 'nota', 'comentarios']);

            foreach ($evaluacion->calificaciones as $calificacion) {
                fputcsv($file, [
                    $calificacion->estudiante->nombre,
                    $calificacion->estudiante->apellido,
                    $calificacion->estudiante->email,
                    $calificacion->nota ?? '',
                    $calificacion->comentarios ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Marcar calificación como revisada
     */
    public function marcarRevisada($id)
    {
        $calificacion = Calificacion::whereHas('evaluacion.seccion', function($query) {
                $query->where('profesor_id', Auth::id());
            })
            ->where('estado', 'calificada')
            ->findOrFail($id);

        $calificacion->update(['estado' => 'revisada']);

        return response()->json(['success' => true]);
    }

    /**
     * Actualizar nota final en la inscripción
     */
    private function actualizarNotaFinal($calificacion)
    {
        $inscripcion = Inscripcion::where('estudiante_id', $calificacion->estudiante_id)
            ->where('seccion_id', $calificacion->evaluacion->seccion_id)
            ->first();

        if ($inscripcion) {
            $notaFinal = $inscripcion->calcularPromedioEvaluaciones();
            $inscripcion->update(['nota_final' => $notaFinal]);
        }
    }

    /**
     * Estadísticas de calificaciones
     */
    public function estadisticas($evaluacionId)
    {
        $evaluacion = Evaluacion::with('calificaciones')
            ->whereHas('seccion', function($query) {
                $query->where('profesor_id', Auth::id());
            })
            ->findOrFail($evaluacionId);

        $calificaciones = $evaluacion->calificaciones->whereNotNull('nota');
        
        return response()->json([
            'total_estudiantes' => $evaluacion->calificaciones->count(),
            'calificadas' => $calificaciones->count(),
            'pendientes' => $evaluacion->calificaciones->where('estado', 'pendiente')->count(),
            'promedio' => $calificaciones->avg('nota'),
            'nota_mayor' => $calificaciones->max('nota')
        ]);
    }

}