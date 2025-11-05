<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Models\Inscripcion;
use App\Models\Seccion;
use App\Models\PeriodoAcademico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AsistenciaController extends Controller
{
    /**
     * Mostrar listado de asistencias con filtros
     */
    public function index(Request $request)
    {
        // Si se seleccionó una sección específica, mostrar asistencias detalladas
        if ($request->filled('seccion_id')) {
            return $this->showSeccionAsistencias($request);
        }

        // Mostrar lista de secciones disponibles
        // Nota: no es posible usar "orderBy('curso.nombre')" directamente porque 'curso' es una relación.
        // Hacemos un LEFT JOIN con la tabla 'cursos' para ordenar por su nombre en la consulta SQL.
        $seccionesQuery = Seccion::with(['curso', 'periodo'])
            ->where('secciones.estado', 'activo')
            ->when($request->filled('periodo_id'), function($query) use ($request) {
                return $query->where('periodo_academico_id', $request->periodo_id);
            });

        $secciones = $seccionesQuery
            ->leftJoin('cursos', 'secciones.curso_id', '=', 'cursos.id')
            ->select('secciones.*')
            ->orderBy('cursos.nombre')
            ->orderBy('secciones.codigo_seccion')
            ->get();

        // Filtros disponibles
        $periodos = PeriodoAcademico::orderBy('fecha_inicio', 'desc')->get();

        // Estadísticas globales
        $stats = [
            'total' => Asistencia::count(),
            'presente' => Asistencia::where('estado', 'presente')->count(),
            'tardanza' => Asistencia::where('estado', 'tardanza')->count(),
            'ausente' => Asistencia::where('estado', 'ausente')->count(),
        ];

        return view('admin.asistencias.index', compact('secciones', 'periodos', 'stats'));
    }

    private function showSeccionAsistencias(Request $request)
    {
        $seccionId = $request->seccion_id;
        $seccion = Seccion::with(['curso', 'periodo', 'inscripciones.estudiante'])->findOrFail($seccionId);

        // Obtener todas las fechas de asistencia para esta sección
        $fechasAsistencia = Asistencia::whereHas('inscripcion', function($q) use ($seccionId) {
            $q->where('seccion_id', $seccionId);
        })
        ->selectRaw('DATE(fecha) as fecha')
        ->distinct()
        ->orderBy('fecha', 'desc')
        ->limit(20)
        ->pluck('fecha')
        ->map(function($fecha) {
            return \Carbon\Carbon::parse($fecha);
        });

        // Obtener estudiantes inscritos en la sección con sus asistencias
        $estudiantes = $seccion->inscripciones()
            ->with('estudiante')
            ->where('estado', 'inscrito')
            ->get();

        // Obtener todas las asistencias de esta sección para las fechas especificadas
        // Agrupamos por inscripcion_id y luego mapeamos cada colección a un array fecha => estado
        $fechasArray = $fechasAsistencia->map(function($fecha) {
            return $fecha->format('Y-m-d');
        })->toArray();

        $todasAsistencias = Asistencia::whereHas('inscripcion', function($q) use ($seccionId) {
            $q->where('seccion_id', $seccionId);
        })
        ->whereIn(DB::raw('DATE(fecha)'), $fechasArray)
        ->get()
        ->groupBy('inscripcion_id')
        ->map(function($asistenciasPorInscripcion) {
            return $asistenciasPorInscripcion->mapWithKeys(function($asistencia) {
                return [$asistencia->fecha->format('Y-m-d') => $asistencia->estado];
            });
        });

        // Asignar asistencias a cada estudiante
        $estudiantes = $estudiantes->map(function($inscripcion) use ($todasAsistencias) {
            $inscripcion->asistencias = $todasAsistencias->get($inscripcion->id, collect())->toArray();
            return $inscripcion;
        });

        // Filtros disponibles
        $secciones = Seccion::with('curso')->where('estado', 'activo')->get();
        $periodos = PeriodoAcademico::orderBy('fecha_inicio', 'desc')->get();

        // Estadísticas de la sección
        $stats = [
            'total' => Asistencia::whereHas('inscripcion', function($q) use ($seccionId) {
                $q->where('seccion_id', $seccionId);
            })->count(),
            'presente' => Asistencia::whereHas('inscripcion', function($q) use ($seccionId) {
                $q->where('seccion_id', $seccionId);
            })->where('estado', 'presente')->count(),
            'tardanza' => Asistencia::whereHas('inscripcion', function($q) use ($seccionId) {
                $q->where('seccion_id', $seccionId);
            })->where('estado', 'tardanza')->count(),
            'ausente' => Asistencia::whereHas('inscripcion', function($q) use ($seccionId) {
                $q->where('seccion_id', $seccionId);
            })->where('estado', 'ausente')->count(),
        ];

        return view('admin.asistencias.seccion', compact('seccion', 'estudiantes', 'fechasAsistencia', 'secciones', 'periodos', 'stats'));
    }

    /**
     * Mostrar formulario para registrar asistencia por sección
     */
    public function create(Request $request)
    {
        $secciones = Seccion::with(['curso', 'periodo'])
            ->where('estado', 'activo')
            ->get();

        $seccionSeleccionada = null;
        $inscripciones = collect();
        $fecha = $request->input('fecha', now()->format('Y-m-d'));

        if ($request->filled('seccion_id')) {
            $seccionSeleccionada = Seccion::with(['curso', 'periodo', 'inscripciones.estudiante'])
                ->findOrFail($request->seccion_id);

            // Obtener inscripciones activas
            $inscripciones = $seccionSeleccionada->inscripciones()
                ->with('estudiante')
                ->where('estado', 'inscrito')
                ->get();

            // Cargar asistencias existentes para la fecha seleccionada
            foreach ($inscripciones as $inscripcion) {
                $inscripcion->asistencia_existente = Asistencia::where('inscripcion_id', $inscripcion->id)
                    ->whereDate('fecha', $fecha)
                    ->first();
            }
        }

        return view('admin.asistencias.create', compact('secciones', 'seccionSeleccionada', 'inscripciones', 'fecha'));
    }

    /**
     * Guardar asistencias (individual o masiva)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'seccion_id' => 'required|exists:secciones,id',
            'fecha' => 'required|date',
            'asistencias' => 'required|array|min:1',
            'asistencias.*.inscripcion_id' => 'required|exists:inscripciones,id',
            'asistencias.*.estado' => 'required|in:presente,ausente,tardanza,justificado',
            'asistencias.*.observaciones' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();
        try {
            $registrados = 0;
            $actualizados = 0;

            foreach ($validated['asistencias'] as $asistenciaData) {
                // Verificar si ya existe asistencia para esta fecha
                $asistenciaExistente = Asistencia::where('inscripcion_id', $asistenciaData['inscripcion_id'])
                    ->whereDate('fecha', $validated['fecha'])
                    ->first();

                if ($asistenciaExistente) {
                    // Actualizar existente
                    $asistenciaExistente->update([
                        'estado' => $asistenciaData['estado'],
                        'observaciones' => $asistenciaData['observaciones'] ?? null,
                    ]);
                    $actualizados++;
                } else {
                    // Crear nueva
                    Asistencia::create([
                        'inscripcion_id' => $asistenciaData['inscripcion_id'],
                        'fecha' => $validated['fecha'],
                        'estado' => $asistenciaData['estado'],
                        'observaciones' => $asistenciaData['observaciones'] ?? null,
                    ]);
                    $registrados++;
                }
            }

            DB::commit();

            $mensaje = "Asistencias procesadas: {$registrados} registradas, {$actualizados} actualizadas";
            return redirect()
                ->route('admin.asistencias.create', ['seccion_id' => $validated['seccion_id'], 'fecha' => $validated['fecha']])
                ->with('success', $mensaje);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al guardar asistencias: ' . $e->getMessage());
            return back()
                ->withErrors(['error' => 'Error al guardar las asistencias: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Ver detalle de una asistencia
     */
    public function show(Asistencia $asistencia)
    {
        $asistencia->load(['inscripcion.estudiante', 'inscripcion.seccion.curso']);

        return view('admin.asistencias.show', compact('asistencia'));
    }

    /**
     * Editar una asistencia individual
     */
    public function edit(Asistencia $asistencia)
    {
        $asistencia->load(['inscripcion.estudiante', 'inscripcion.seccion.curso']);

        return view('admin.asistencias.edit', compact('asistencia'));
    }

    /**
     * Actualizar una asistencia
     */
    public function update(Request $request, Asistencia $asistencia)
    {
        $validated = $request->validate([
            'fecha' => 'required|date',
            'estado' => 'required|in:presente,ausente,tardanza,justificado',
            'observaciones' => 'nullable|string|max:500',
        ]);

        try {
            $asistencia->update($validated);

            return redirect()
                ->route('admin.asistencias.show', $asistencia)
                ->with('success', 'Asistencia actualizada exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al actualizar asistencia: ' . $e->getMessage());
            return back()
                ->withErrors(['error' => 'Error al actualizar: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Eliminar una asistencia
     */
    public function destroy(Asistencia $asistencia)
    {
        try {
            $asistencia->delete();

            return redirect()
                ->route('admin.asistencias.index')
                ->with('success', 'Asistencia eliminada exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al eliminar asistencia: ' . $e->getMessage());
            return back()
                ->withErrors(['error' => 'Error al eliminar: ' . $e->getMessage()]);
        }
    }

    /**
     * Reporte de asistencias por sección
     */
    public function reporte(Request $request)
    {
        $query = Asistencia::with(['inscripcion.estudiante', 'inscripcion.seccion']);

        $fechaInicio = $request->filled('fecha_inicio')
            ? \Carbon\Carbon::parse($request->input('fecha_inicio'))->startOfDay()
            : now()->startOfMonth();

        $fechaFin = $request->filled('fecha_fin')
            ? \Carbon\Carbon::parse($request->input('fecha_fin'))->endOfDay()
            : now()->endOfMonth();

        $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Obtener todas las asistencias
        $asistencias = $query->get();

        // Estadísticas generales
        $totalAsistencias = $asistencias->count();
        $presentes = $asistencias->where('estado', 'presente')->count();
        $tardanzas = $asistencias->where('estado', 'tardanza')->count();
        $ausentes = $asistencias->where('estado', 'ausente')->count();

        // Calcular porcentajes
        $porcentajePresentes = $totalAsistencias > 0 ? round(($presentes / $totalAsistencias) * 100, 2) : 0;
        $porcentajeTardanzas = $totalAsistencias > 0 ? round(($tardanzas / $totalAsistencias) * 100, 2) : 0;
        $porcentajeAusentes = $totalAsistencias > 0 ? round(($ausentes / $totalAsistencias) * 100, 2) : 0;

        // Reporte por fecha
        $reportePorFecha = $asistencias
            ->groupBy(function($asistencia) {
                return $asistencia->fecha->format('Y-m-d');
            })
            ->map(function($grupo, $fecha) {
                $presentes = $grupo->where('estado', 'presente')->count();
                $tardanzas = $grupo->where('estado', 'tardanza')->count();
                $ausentes = $grupo->where('estado', 'ausente')->count();
                $total = $grupo->count();
                
                $porcentaje = $total > 0 ? round((($presentes + $tardanzas) / $total) * 100, 2) : 0;

                return (object)[
                    'fecha' => \Carbon\Carbon::parse($fecha),
                    'presentes' => $presentes,
                    'tardanzas' => $tardanzas,
                    'ausentes' => $ausentes,
                    'total' => $total,
                    'porcentaje_asistencia' => $porcentaje
                ];
            })
            ->sortByDesc('fecha')
            ->values();

        return view('admin.asistencias.reporte', compact(
            'totalAsistencias',
            'presentes',
            'tardanzas',
            'ausentes',
            'porcentajePresentes',
            'porcentajeTardanzas',
            'porcentajeAusentes',
            'reportePorFecha'
        ));
    }
}