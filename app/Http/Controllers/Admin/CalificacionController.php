<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Calificacion;
use App\Models\Evaluacion;
use App\Models\Seccion;
use App\Models\Inscripcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CalificacionController extends Controller
{
    /**
     * Mostrar listado de calificaciones
     */
    public function index(Request $request)
    {
        $query = Calificacion::with([
            'estudiante',
            'evaluacion.seccion.curso',
            'evaluacion.tipoEvaluacion'
        ])
        ->whereHas('estudiante')
        ->whereHas('evaluacion.seccion.curso');

        // Filtros
        if ($request->filled('estudiante')) {
            $query->whereHas('estudiante', function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->estudiante . '%')
                  ->orWhere('apellido', 'like', '%' . $request->estudiante . '%')
                  ->orWhere('email', 'like', '%' . $request->estudiante . '%');
            });
        }

        if ($request->filled('evaluacion_id')) {
            $query->where('evaluacion_id', $request->evaluacion_id);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('nota_min')) {
            $query->where('nota', '>=', $request->nota_min);
        }

        if ($request->filled('nota_max')) {
            $query->where('nota', '<=', $request->nota_max);
        }

        $calificaciones = $query->latest()->paginate(15);

        $evaluaciones = Evaluacion::with(['seccion.curso'])
            ->whereHas('seccion.curso')
            ->orderBy('fecha_evaluacion', 'desc')
            ->get();

        $stats = [
            'total' => Calificacion::whereHas('estudiante')
                ->whereHas('evaluacion')->count(),
            'calificadas' => Calificacion::whereHas('estudiante')
                ->whereHas('evaluacion')
                ->where('estado', 'calificada')->count(),
            'pendientes' => Calificacion::whereHas('estudiante')
                ->whereHas('evaluacion')
                ->where('estado', 'pendiente')->count(),
            'promedio_general' => number_format(
                Calificacion::whereHas('estudiante')
                    ->whereHas('evaluacion')
                    ->avg('nota') ?? 0,
                2
            )
        ];

        return view('admin.calificaciones.index', compact('calificaciones', 'evaluaciones', 'stats'));
    }

    /**
     * Mostrar formulario de crear calificación
     */
    // En tu CalificacionController.php

public function create(Request $request)
{
    // Obtener todas las evaluaciones con sus relaciones
    $evaluaciones = Evaluacion::with(['seccion.curso', 'seccion.profesor', 'tipoEvaluacion'])
        ->orderBy('fecha_evaluacion', 'desc')
        ->get();
    
    // Si se seleccionó una evaluación, cargar estudiantes disponibles
    $evaluacion = null;
    $estudiantes = collect();
    
    if ($request->has('evaluacion_id')) {
        $evaluacion = Evaluacion::with(['seccion.curso', 'seccion.periodo'])
            ->findOrFail($request->evaluacion_id);
        
        // Obtener estudiantes inscritos en la sección que NO tienen calificación en esta evaluación
        $estudiantesYaCalificados = Calificacion::where('evaluacion_id', $evaluacion->id)
            ->pluck('estudiante_id')
            ->toArray();
        
        $estudiantes = Inscripcion::where('seccion_id', $evaluacion->seccion_id)
            ->where('estado', 'inscrito')
            ->whereNotIn('estudiante_id', $estudiantesYaCalificados)
            ->with('estudiante')
            ->get()
            ->pluck('estudiante')
            ->filter(); // Eliminar nulls
    }
    
    return view('admin.calificaciones.create', compact('evaluaciones', 'evaluacion', 'estudiantes'));
}

// Método auxiliar para debugging (temporal)
public function debugEvaluaciones()
{
    $evaluaciones = Evaluacion::with(['seccion.curso'])->get();
    
    dd([
        'total_evaluaciones' => $evaluaciones->count(),
        'evaluaciones' => $evaluaciones->toArray(),
        'primera_evaluacion' => $evaluaciones->first(),
    ]);
}

    /**
     * Guardar nueva calificación
     */
    public function store(Request $request)
    {
        $request->validate([
            'evaluacion_id' => 'required|exists:evaluaciones,id',
            'estudiante_id' => 'required|exists:usuarios,id',
            'nota' => 'required|numeric|min:0|max:100',
            'fecha_calificacion' => 'nullable|date',
            'estado' => 'required|in:pendiente,calificada,revisada',
            'comentarios' => 'nullable|string|max:1000'
        ]);

        // Verificar que la evaluación tiene sección válida
        $evaluacion = Evaluacion::with('seccion')->find($request->evaluacion_id);
        if (!$evaluacion || !$evaluacion->seccion) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['evaluacion_id' => 'La evaluación seleccionada no tiene una sección válida.']);
        }

        // Verificar que no exista ya una calificación
        $existe = Calificacion::where('evaluacion_id', $request->evaluacion_id)
            ->where('estudiante_id', $request->estudiante_id)
            ->exists();

        if ($existe) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['estudiante_id' => 'Este estudiante ya tiene una calificación registrada para esta evaluación.']);
        }

        try {
            DB::beginTransaction();

            $calificacion = Calificacion::create([
                'evaluacion_id' => $request->evaluacion_id,
                'estudiante_id' => $request->estudiante_id,
                'nota' => $request->nota,
                'fecha_calificacion' => $request->fecha_calificacion ?? now(),
                'estado' => $request->estado,
                'comentarios' => $request->comentarios
            ]);

            // Recalcular nota final
            $this->recalcularNotaFinal($calificacion->estudiante_id, $evaluacion->seccion_id);

            DB::commit();

            return redirect()->route('admin.calificaciones.index')
                ->with('success', 'Calificación registrada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al guardar calificación: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error al guardar la calificación: ' . $e->getMessage()]);
        }
    }

    /**
     * Mostrar detalle de calificación
     */
    public function show(Calificacion $calificacion)
    {
        try {
            // Intentar cargar todas las relaciones
            $calificacion->load([
                'estudiante',
                'evaluacion.seccion.curso',
                'evaluacion.seccion.profesor',
                'evaluacion.seccion.periodo',
                'evaluacion.tipoEvaluacion'
            ]);

            // Detectar problemas
            $problemas = [];
            
            if (!$calificacion->estudiante) {
                $problemas[] = [
                    'tipo' => 'estudiante',
                    'mensaje' => 'El estudiante asociado no existe o fue eliminado',
                    'id_faltante' => $calificacion->estudiante_id
                ];
            }

            if (!$calificacion->evaluacion) {
                $problemas[] = [
                    'tipo' => 'evaluacion',
                    'mensaje' => 'La evaluación asociada no existe o fue eliminada',
                    'id_faltante' => $calificacion->evaluacion_id
                ];
            } elseif (!$calificacion->evaluacion->seccion) {
                $problemas[] = [
                    'tipo' => 'seccion',
                    'mensaje' => 'La sección de la evaluación no existe o fue eliminada',
                    'id_faltante' => $calificacion->evaluacion->seccion_id ?? 'N/A'
                ];
            } elseif (!$calificacion->evaluacion->seccion->curso) {
                $problemas[] = [
                    'tipo' => 'curso',
                    'mensaje' => 'El curso de la sección no existe o fue eliminado',
                    'id_faltante' => $calificacion->evaluacion->seccion->curso_id ?? 'N/A'
                ];
            }

            // Si hay problemas, mostrar vista especial
            if (!empty($problemas)) {
                return view('admin.calificaciones.show-con-problemas', compact('calificacion', 'problemas'));
            }

            // Vista normal
            $otrasCalificaciones = Calificacion::where('estudiante_id', $calificacion->estudiante_id)
                ->whereHas('evaluacion', function($q) use ($calificacion) {
                    $q->where('seccion_id', $calificacion->evaluacion->seccion_id);
                })
                ->where('id', '!=', $calificacion->id)
                ->with(['evaluacion.tipoEvaluacion'])
                ->get();

            return view('admin.calificaciones.show', compact('calificacion', 'otrasCalificaciones'));

        } catch (\Exception $e) {
            Log::error("Error al mostrar calificación {$calificacion->id}: " . $e->getMessage());
            
            return redirect()->route('admin.calificaciones.index')
                ->with('error', 'Error al cargar la calificación: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Calificacion $calificacion)
    {
        try {
            // Intentar cargar todas las relaciones
            $calificacion->load([
                'estudiante',
                'evaluacion.seccion.curso',
                'evaluacion.seccion.profesor'
            ]);

            // Detectar problemas
            $problemas = [];
            
            if (!$calificacion->estudiante) {
                $problemas[] = [
                    'tipo' => 'estudiante',
                    'mensaje' => 'El estudiante asociado no existe o fue eliminado',
                    'id_faltante' => $calificacion->estudiante_id
                ];
            }

            if (!$calificacion->evaluacion) {
                $problemas[] = [
                    'tipo' => 'evaluacion',
                    'mensaje' => 'La evaluación asociada no existe o fue eliminada',
                    'id_faltante' => $calificacion->evaluacion_id
                ];
            } elseif (!$calificacion->evaluacion->seccion) {
                $problemas[] = [
                    'tipo' => 'seccion',
                    'mensaje' => 'La sección de la evaluación no existe o fue eliminada',
                    'id_faltante' => $calificacion->evaluacion->seccion_id ?? 'N/A'
                ];
            } elseif (!$calificacion->evaluacion->seccion->curso) {
                $problemas[] = [
                    'tipo' => 'curso',
                    'mensaje' => 'El curso de la sección no existe o fue eliminado',
                    'id_faltante' => $calificacion->evaluacion->seccion->curso_id ?? 'N/A'
                ];
            }

            // Si hay problemas, mostrar vista de bloqueo
            if (!empty($problemas)) {
                return view('admin.calificaciones.no-se-puede-editar', compact('calificacion', 'problemas'));
            }

            // Formulario de edición normal
            return view('admin.calificaciones.edit', compact('calificacion'));

        } catch (\Exception $e) {
            Log::error("Error al editar calificación {$calificacion->id}: " . $e->getMessage());
            
            return redirect()->route('admin.calificaciones.index')
                ->with('error', 'Error al cargar el formulario de edición: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar una calificación existente
     */
    public function update(Request $request, Calificacion $calificacion)
    {
        try {
            // Validación
            $validated = $request->validate([
                'nota' => 'required|numeric|min:0|max:100',
                'estado' => 'required|in:pendiente,calificada,revisada',
                'fecha_calificacion' => 'nullable|date',
                'comentarios' => 'nullable|string|max:1000',
            ], [
                'nota.required' => 'La nota es obligatoria',
                'nota.numeric' => 'La nota debe ser un número',
                'nota.min' => 'La nota mínima es 0',
                'nota.max' => 'La nota máxima es 100',
                'estado.required' => 'El estado es obligatorio',
                'estado.in' => 'El estado debe ser: pendiente, calificada o revisada',
                'fecha_calificacion.date' => 'La fecha de calificación no es válida',
                'comentarios.max' => 'Los comentarios no pueden exceder 1000 caracteres',
            ]);

            DB::beginTransaction();

            // Preparar datos para actualizar
            $datos = [
                'nota' => $validated['nota'],
                'estado' => $validated['estado'],
                'comentarios' => $validated['comentarios'] ?? null,
            ];

            // Si hay fecha de calificación, agregarla
            if (!empty($validated['fecha_calificacion'])) {
                $datos['fecha_calificacion'] = $validated['fecha_calificacion'];
            }

            // Actualizar calificación
            $calificacion->update($datos);

            // Recalcular nota final del estudiante en esta sección
            if ($calificacion->evaluacion && $calificacion->evaluacion->seccion_id) {
                $this->recalcularNotaFinal($calificacion->estudiante_id, $calificacion->evaluacion->seccion_id);
            }

            DB::commit();

            return redirect()
                ->route('admin.calificaciones.show', $calificacion)
                ->with('success', '✅ Calificación actualizada exitosamente');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar calificación: ' . $e->getMessage());
            
            return back()
                ->with('error', '❌ Error al actualizar la calificación: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Eliminar calificación
     */
    public function destroy(Calificacion $calificacion)
    {
        try {
            DB::beginTransaction();

            $estudianteId = $calificacion->estudiante_id;
            $evaluacion = $calificacion->evaluacion;
            $seccionId = $evaluacion ? $evaluacion->seccion_id : null;

            // Eliminar la calificación
            $calificacion->delete();

            // Recalcular nota final si tenemos datos válidos
            if ($estudianteId && $seccionId) {
                $this->recalcularNotaFinal($estudianteId, $seccionId);
            }

            DB::commit();

            return redirect()->route('admin.calificaciones.index')
                ->with('success', 'Calificación eliminada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar calificación: ' . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Error al eliminar la calificación: ' . $e->getMessage()]);
        }
    }

    /**
     * Recalcular la nota final de un estudiante en una sección
     */
    private function recalcularNotaFinal($estudiante_id, $seccion_id)
    {
        try {
            // Buscar la inscripción
            $inscripcion = Inscripcion::where('estudiante_id', $estudiante_id)
                ->where('seccion_id', $seccion_id)
                ->first();

            if (!$inscripcion) {
                return;
            }

            // Obtener todas las evaluaciones de la sección
            $evaluaciones = Evaluacion::where('seccion_id', $seccion_id)->get();

            if ($evaluaciones->isEmpty()) {
                return;
            }

            $notaFinal = 0;
            $porcentajeTotal = 0;

            foreach ($evaluaciones as $evaluacion) {
                // Buscar calificación del estudiante en esta evaluación
                $calificacion = Calificacion::where('estudiante_id', $estudiante_id)
                    ->where('evaluacion_id', $evaluacion->id)
                    ->first();

                if ($calificacion && $calificacion->nota !== null) {
                    // Calcular contribución al promedio ponderado
                    $contribucion = ($calificacion->nota * $evaluacion->porcentaje) / 100;
                    $notaFinal += $contribucion;
                    $porcentajeTotal += $evaluacion->porcentaje;
                }
            }

            // Solo actualizar si hay calificaciones
            if ($porcentajeTotal > 0) {
                // Si no está completo el 100%, ajustar proporcionalmente
                if ($porcentajeTotal < 100) {
                    $notaFinal = ($notaFinal * 100) / $porcentajeTotal;
                }

                $inscripcion->update([
                    'nota_final' => round($notaFinal, 2)
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error al recalcular nota final: ' . $e->getMessage());
        }
    }

    /**
     * Mostrar formulario de calificación masiva
     */
    public function masiva(Request $request)
    {
        $evaluaciones = Evaluacion::with(['seccion.curso', 'seccion.periodo'])
            ->whereHas('seccion.curso')
            ->orderBy('fecha_evaluacion', 'desc')
            ->get();

        $evaluacion = null;
        $estudiantes = collect();

        if ($request->filled('evaluacion_id')) {
            $evaluacion = Evaluacion::with([
                'seccion.curso',
                'seccion.inscripciones.estudiante',
                'seccion.inscripciones.calificaciones' => function($q) use ($request) {
                    $q->where('evaluacion_id', $request->evaluacion_id);
                }
            ])
            ->whereHas('seccion.curso')
            ->find($request->evaluacion_id);

            if ($evaluacion) {
                $estudiantes = $evaluacion->seccion->inscripciones()
                    ->with(['estudiante', 'calificaciones' => function($q) use ($request) {
                        $q->where('evaluacion_id', $request->evaluacion_id);
                    }])
                    ->whereHas('estudiante')
                    ->get();
            }
        }

        return view('admin.calificaciones.masiva', compact('evaluaciones', 'evaluacion', 'estudiantes'));
    }

    /**
     * Procesar calificación masiva
     */
    public function procesarMasiva(Request $request)
    {
        $request->validate([
            'evaluacion_id' => 'required|exists:evaluaciones,id',
            'calificaciones' => 'required|array',
            'calificaciones.*.estudiante_id' => 'required|exists:usuarios,id',
            'calificaciones.*.nota' => 'nullable|numeric|min:0|max:100',
            'calificaciones.*.comentarios' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $evaluacion = Evaluacion::find($request->evaluacion_id);
            $guardadas = 0;
            $actualizadas = 0;

            foreach ($request->calificaciones as $data) {
                if (!isset($data['nota']) || $data['nota'] === '' || $data['nota'] === null) {
                    continue;
                }

                $calificacion = Calificacion::updateOrCreate(
                    [
                        'evaluacion_id' => $request->evaluacion_id,
                        'estudiante_id' => $data['estudiante_id']
                    ],
                    [
                        'nota' => $data['nota'],
                        'comentarios' => $data['comentarios'] ?? null,
                        'estado' => 'calificada',
                        'fecha_calificacion' => now()
                    ]
                );

                if ($calificacion->wasRecentlyCreated) {
                    $guardadas++;
                } else {
                    $actualizadas++;
                }

                // Recalcular nota final
                if ($evaluacion && $evaluacion->seccion_id) {
                    $this->recalcularNotaFinal($data['estudiante_id'], $evaluacion->seccion_id);
                }
            }

            DB::commit();

            $mensaje = "Proceso completado: {$guardadas} calificaciones nuevas, {$actualizadas} actualizadas";

            return redirect()->route('admin.calificaciones.index')
                ->with('success', $mensaje);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en calificación masiva: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error al procesar las calificaciones: ' . $e->getMessage()]);
        }
    }

    /**
     * Mostrar reporte de calificaciones
     */
    public function reporte(Request $request)
    {
        $secciones = Seccion::with(['curso', 'periodo', 'profesor'])
            ->whereHas('curso')
            ->whereHas('periodo', function($q) {
                $q->where('estado', 'activo')
                  ->orWhere('fecha_fin', '>=', now()->subMonths(3));
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $seccion = null;
        $estadisticas = [];

        if ($request->filled('seccion_id')) {
            $seccion = Seccion::with([
                'curso',
                'profesor',
                'periodo',
                'evaluaciones.tipoEvaluacion'
            ])
            ->whereHas('curso')
            ->find($request->seccion_id);

            if ($seccion) {
                $inscripciones = $seccion->inscripciones()
                    ->with([
                        'estudiante',
                        'calificaciones' => function($q) use ($seccion) {
                            $q->whereHas('evaluacion', function($eq) use ($seccion) {
                                $eq->where('seccion_id', $seccion->id);
                            });
                        },
                        'calificaciones.evaluacion'
                    ])
                    ->whereHas('estudiante')
                    ->get();
                
                $totalEstudiantes = $inscripciones->count();
                $notaMinima = config_sistema('nota_minima_aprobacion', 60);
                $aprobados = $inscripciones->filter(fn($i) => $i->nota_final && $i->nota_final >= $notaMinima)->count();
                $reprobados = $totalEstudiantes - $aprobados;
                $promedioSeccion = $inscripciones->where('nota_final', '>', 0)->avg('nota_final');

                $estadisticas = [
                    'total_estudiantes' => $totalEstudiantes,
                    'aprobados' => $aprobados,
                    'reprobados' => $reprobados,
                    'promedio_seccion' => $promedioSeccion ? number_format($promedioSeccion, 2) : 'N/A',
                    'inscripciones' => $inscripciones
                ];
            }
        }

        return view('admin.calificaciones.reporte', compact('secciones', 'seccion', 'estadisticas'));
    }
}