<?php

namespace App\Http\Controllers\Profesor;

use App\Http\Controllers\Controller;
use App\Models\Seccion;
use App\Models\Evaluacion;
use App\Models\Calificacion;
use App\Models\Asistencia;
use App\Models\PeriodoAcademico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (Auth::user()->tipo_usuario !== 'profesor') {
                abort(403, 'Acceso denegado. Solo profesores pueden acceder a esta sección.');
            }
            return $next($request);
        });
    }

    /**
     * Mostrar el dashboard principal del profesor
     */
    public function index()
    {
        $profesor = Auth::user();
        $periodoActual = PeriodoAcademico::vigente()->first();

        // Estadísticas generales
        $stats = $this->getEstadisticasGenerales($profesor, $periodoActual);

        // Secciones actuales del profesor
        $seccionesActuales = $this->getSeccionesActuales($profesor, $periodoActual);

        // Evaluaciones próximas a vencer
        $evaluacionesProximas = $this->getEvaluacionesProximas($profesor);

        // Calificaciones pendientes
        $calificacionesPendientes = $this->getCalificacionesPendientes($profesor);

        // Asistencias de hoy
        $asistenciasHoy = $this->getAsistenciasHoy($profesor);

        // Resumen de evaluaciones recientes
        $evaluacionesRecientes = $this->getEvaluacionesRecientes($profesor);

        return view('profesor.dashboard.index', compact(
            'profesor',
            'stats',
            'seccionesActuales',
            'evaluacionesProximas',
            'calificacionesPendientes',
            'asistenciasHoy',
            'evaluacionesRecientes',
            'periodoActual'
        ));
    }

    /**
     * Obtener estadísticas generales del profesor
     */
    private function getEstadisticasGenerales($profesor, $periodoActual)
    {
        $seccionesIds = Seccion::where('profesor_id', $profesor->id)
            ->when($periodoActual, function($query) use ($periodoActual) {
                return $query->where('periodo_academico_id', $periodoActual->id);
            })
            ->activas()
            ->pluck('id');

        return [
            'total_secciones' => $seccionesIds->count(),
            'total_estudiantes' => \App\Models\Inscripcion::whereIn('seccion_id', $seccionesIds)
            ->where('estado', 'inscrito')
            ->count(),
            'evaluaciones_activas' => Evaluacion::whereIn('seccion_id', $seccionesIds)
                ->where('estado', 'activa')
                ->count(),
            'calificaciones_pendientes' => Calificacion::whereHas('evaluacion', function($query) use ($seccionesIds) {
                    $query->whereIn('seccion_id', $seccionesIds);
                })
                ->where('estado', 'pendiente')
                ->count()
        ];
    }

    /**
     * Obtener secciones actuales del profesor
     */
    private function getSeccionesActuales($profesor, $periodoActual)
    {
        return Seccion::with(['curso', 'periodo', 'inscripciones' => function($query) {
                $query->where('estado', 'inscrito');
            }])
            ->where('profesor_id', $profesor->id)
            ->when($periodoActual, function($query) use ($periodoActual) {
                return $query->where('periodo_academico_id', $periodoActual->id);
            })
            ->activas()
            ->get()
            ->map(function($seccion) {
                return [
                    'id' => $seccion->id,
                    'nombre_completo' => $seccion->nombre_completo,
                    'curso' => $seccion->curso->nombre,
                    'codigo_seccion' => $seccion->codigo_seccion,
                    'estudiantes_inscritos' => $seccion->estudiantes_inscritos,
                    'cupo_maximo' => $seccion->cupo_maximo,
                    'aula' => $seccion->aula,
                    'horario_formateado' => $seccion->horario_formateado
                ];
            });
    }

    /**
     * Obtener evaluaciones próximas a vencer (próximos 7 días)
     */
    private function getEvaluacionesProximas($profesor)
    {
        $fechaLimite = Carbon::now()->addDays(7);

        return Evaluacion::with(['seccion.curso', 'tipoEvaluacion'])
            ->whereHas('seccion', function($query) use ($profesor) {
                $query->where('profesor_id', $profesor->id);
            })
            ->where('fecha_limite', '<=', $fechaLimite)
            ->where('fecha_limite', '>=', Carbon::now())
            ->whereIn('estado', ['programada', 'activa'])
            ->orderBy('fecha_limite', 'asc')
            ->limit(5)
            ->get()
            ->map(function($evaluacion) {
                return [
                    'id' => $evaluacion->id,
                    'nombre' => $evaluacion->nombre,
                    'tipo' => $evaluacion->tipoEvaluacion->nombre,
                    'curso' => $evaluacion->seccion->curso->nombre,
                    'seccion' => $evaluacion->seccion->codigo_seccion,
                    'fecha_limite' => $evaluacion->fecha_limite,
                    'dias_restantes' => $evaluacion->dias_para_vencimiento,
                    'estado' => $evaluacion->estado
                ];
            });
    }

    /**
     * Obtener calificaciones pendientes
     */
    private function getCalificacionesPendientes($profesor)
    {
        return Calificacion::with(['evaluacion.seccion.curso', 'evaluacion.tipoEvaluacion', 'estudiante'])
            ->whereHas('evaluacion.seccion', function($query) use ($profesor) {
                $query->where('profesor_id', $profesor->id);
            })
            ->where('estado', 'pendiente')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($calificacion) {
                return [
                    'id' => $calificacion->id,
                    'estudiante' => $calificacion->estudiante->nombre_completo,
                    'evaluacion' => $calificacion->evaluacion->nombre,
                    'tipo' => $calificacion->evaluacion->tipoEvaluacion->nombre,
                    'curso' => $calificacion->evaluacion->seccion->curso->nombre,
                    'seccion' => $calificacion->evaluacion->seccion->codigo_seccion,
                    'fecha_evaluacion' => $calificacion->evaluacion->fecha_evaluacion,
                    'intentos' => $calificacion->intentos
                ];
            });
    }

    /**
     * Obtener asistencias de hoy
     */
    private function getAsistenciasHoy($profesor)
    {
        $hoy = Carbon::today();

        return Asistencia::with(['inscripcion.seccion.curso', 'inscripcion.estudiante'])
            ->whereHas('inscripcion.seccion', function($query) use ($profesor) {
                $query->where('profesor_id', $profesor->id);
            })
            ->whereDate('fecha', $hoy)
            ->get()
            ->groupBy('inscripcion.seccion.nombre_completo')
            ->map(function($asistencias, $seccion) {
                $total = $asistencias->count();
                $presentes = $asistencias->where('estado', 'presente')->count();
                $ausentes = $asistencias->where('estado', 'ausente')->count();
                $tardanzas = $asistencias->where('estado', 'tardanza')->count();

                return [
                    'seccion' => $seccion,
                    'total_estudiantes' => $total,
                    'presentes' => $presentes,
                    'ausentes' => $ausentes,
                    'tardanzas' => $tardanzas,
                    'porcentaje_asistencia' => $total > 0 ? round(($presentes / $total) * 100, 1) : 0
                ];
            });
    }

    /**
     * Obtener evaluaciones recientes (últimos 7 días)
     */
    private function getEvaluacionesRecientes($profesor)
    {
        $fechaInicio = Carbon::now()->subDays(7);

        return Evaluacion::with(['seccion.curso', 'tipoEvaluacion', 'calificaciones'])
            ->whereHas('seccion', function($query) use ($profesor) {
                $query->where('profesor_id', $profesor->id);
            })
            ->where('fecha_evaluacion', '>=', $fechaInicio)
            ->where('estado', 'finalizada')
            ->orderBy('fecha_evaluacion', 'desc')
            ->limit(5)
            ->get()
            ->map(function($evaluacion) {
                $totalCalificaciones = $evaluacion->calificaciones->count();
                $calificadas = $evaluacion->calificaciones->whereIn('estado', ['calificada', 'revisada'])->count();
                
                return [
                    'id' => $evaluacion->id,
                    'nombre' => $evaluacion->nombre,
                    'tipo' => $evaluacion->tipoEvaluacion->nombre,
                    'curso' => $evaluacion->seccion->curso->nombre,
                    'seccion' => $evaluacion->seccion->codigo_seccion,
                    'fecha_evaluacion' => $evaluacion->fecha_evaluacion,
                    'total_estudiantes' => $totalCalificaciones,
                    'calificadas' => $calificadas,
                    'pendientes' => $totalCalificaciones - $calificadas,
                    'promedio_notas' => $evaluacion->promedio_notas ? round($evaluacion->promedio_notas, 2) : null
                ];
            });
    }

    /**
     * Obtener datos para gráficos y reportes
     */
    public function getDatosGraficos(Request $request)
    {
        $profesor = Auth::user();
        $periodoId = $request->get('periodo_id');
        
        // Si no se especifica período, usar el actual
        if (!$periodoId) {
            $periodoActual = PeriodoAcademico::vigente()->first();
            $periodoId = $periodoActual ? $periodoActual->id : null;
        }

        $datos = [
            'distribucion_notas' => $this->getDistribucionNotas($profesor, $periodoId),
            'asistencia_mensual' => $this->getAsistenciaMensual($profesor, $periodoId),
            'progreso_evaluaciones' => $this->getProgresoEvaluaciones($profesor, $periodoId)
        ];

        return response()->json($datos);
    }

    /**
     * Distribución de notas por rango
     */
    private function getDistribucionNotas($profesor, $periodoId)
    {
        $seccionesIds = Seccion::where('profesor_id', $profesor->id)
            ->when($periodoId, function($query) use ($periodoId) {
                return $query->where('periodo_academico_id', $periodoId);
            })
            ->pluck('id');

        $calificaciones = Calificacion::whereHas('evaluacion', function($query) use ($seccionesIds) {
                $query->whereIn('seccion_id', $seccionesIds);
            })
            ->whereNotNull('nota')
            ->pluck('nota');

        return [
            'excelente' => $calificaciones->where('>=', 90)->count(),
            'muy_bueno' => $calificaciones->whereBetween(80, [80, 89])->count(),
            'bueno' => $calificaciones->whereBetween(70, [70, 79])->count(),
            'regular' => $calificaciones->whereBetween(60, [60, 69])->count(),
            'deficiente' => $calificaciones->where('<', 60)->count()
        ];
    }

    /**
     * Asistencia mensual
     */
    private function getAsistenciaMensual($profesor, $periodoId)
    {
        $seccionesIds = Seccion::where('profesor_id', $profesor->id)
            ->when($periodoId, function($query) use ($periodoId) {
                return $query->where('periodo_academico_id', $periodoId);
            })
            ->pluck('id');

        return Asistencia::whereHas('inscripcion', function($query) use ($seccionesIds) {
                $query->whereIn('seccion_id', $seccionesIds);
            })
            ->selectRaw('MONTH(fecha) as mes, estado, COUNT(*) as total')
            ->groupBy('mes', 'estado')
            ->orderBy('mes')
            ->get()
            ->groupBy('mes')
            ->map(function($grupo, $mes) {
                $total = $grupo->sum('total');
                $presentes = $grupo->where('estado', 'presente')->sum('total');
                
                return [
                    'mes' => $mes,
                    'total' => $total,
                    'presentes' => $presentes,
                    'porcentaje' => $total > 0 ? round(($presentes / $total) * 100, 1) : 0
                ];
            })
            ->values();
    }

    /**
     * Progreso de evaluaciones
     */
    private function getProgresoEvaluaciones($profesor, $periodoId)
    {
        $seccionesIds = Seccion::where('profesor_id', $profesor->id)
            ->when($periodoId, function($query) use ($periodoId) {
                return $query->where('periodo_academico_id', $periodoId);
            })
            ->pluck('id');

        return Evaluacion::whereIn('seccion_id', $seccionesIds)
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->get()
            ->pluck('total', 'estado');
    }
}