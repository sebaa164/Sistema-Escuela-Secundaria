<?php

namespace App\Http\Controllers\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\Inscripcion;
use App\Models\Evaluacion;
use App\Models\Calificacion;
use App\Models\Asistencia;
use App\Models\PeriodoAcademico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (Auth::user()->tipo_usuario !== 'estudiante') {
                abort(403, 'Acceso denegado.');
            }
            return $next($request);
        });
    }

    /**
     * Dashboard principal del estudiante
     */
    public function index()
    {
        $estudiante = Auth::user();
        $periodoActual = PeriodoAcademico::vigente()->first();

        // Estadísticas generales
        $stats = $this->getEstadisticasGenerales($estudiante, $periodoActual);

        // Inscripciones actuales
        $inscripcionesActuales = $this->getInscripcionesActuales($estudiante, $periodoActual);

        // Evaluaciones próximas
        $evaluacionesProximas = $this->getEvaluacionesProximas($estudiante);

        // Calificaciones recientes
        $calificacionesRecientes = $this->getCalificacionesRecientes($estudiante);

        // Horario de clases de hoy
        $horarioHoy = $this->getHorarioHoy($estudiante);

        return view('estudiante.dashboard.index', compact(
            'estudiante',
            'stats',
            'inscripcionesActuales',
            'evaluacionesProximas',
            'calificacionesRecientes',
            'horarioHoy',
            'periodoActual'
        ));
    }

    /**
     * Estadísticas generales del estudiante
     */
    private function getEstadisticasGenerales($estudiante, $periodoActual)
    {
        $inscripciones = Inscripcion::where('estudiante_id', $estudiante->id)
            ->when($periodoActual, function($query) use ($periodoActual) {
                return $query->whereHas('seccion', function($q) use ($periodoActual) {
                    $q->where('periodo_academico_id', $periodoActual->id);
                });
            })
            ->where('estado', 'inscrito')
            ->get();

        $creditos = $inscripciones->sum(function($inscripcion) {
            return $inscripcion->seccion->curso->creditos;
        });

        $promedioGeneral = $inscripciones->whereNotNull('nota_final')->avg('nota_final');

        return [
            'total_materias' => $inscripciones->count(),
            'total_creditos' => $creditos,
            'promedio_general' => $promedioGeneral ? round($promedioGeneral, 2) : null,
            'evaluaciones_pendientes' => $this->contarEvaluacionesPendientes($estudiante)
        ];
    }

    /**
     * Inscripciones actuales del estudiante
     */
    private function getInscripcionesActuales($estudiante, $periodoActual)
    {
        return Inscripcion::with(['seccion.curso', 'seccion.profesor', 'seccion.periodo'])
            ->where('estudiante_id', $estudiante->id)
            ->when($periodoActual, function($query) use ($periodoActual) {
                return $query->whereHas('seccion', function($q) use ($periodoActual) {
                    $q->where('periodo_academico_id', $periodoActual->id);
                });
            })
            ->where('estado', 'inscrito')
            ->get();
    }

    /**
     * Evaluaciones próximas (próximos 7 días)
     */
    private function getEvaluacionesProximas($estudiante)
    {
        $fechaLimite = Carbon::now()->addDays(7);

        return Evaluacion::with(['seccion.curso', 'tipoEvaluacion', 'calificaciones' => function($query) use ($estudiante) {
                $query->where('estudiante_id', $estudiante->id);
            }])
            ->whereHas('seccion.inscripciones', function($query) use ($estudiante) {
                $query->where('estudiante_id', $estudiante->id)
                      ->where('estado', 'inscrito');
            })
            ->where('fecha_limite', '<=', $fechaLimite)
            ->where('fecha_limite', '>=', Carbon::now())
            ->whereIn('estado', ['programada', 'activa'])
            ->orderBy('fecha_limite', 'asc')
            ->get()
            ->map(function($evaluacion) {
                $calificacion = $evaluacion->calificaciones->first();
                
                return [
                    'id' => $evaluacion->id,
                    'nombre' => $evaluacion->nombre,
                    'tipo' => $evaluacion->tipoEvaluacion->nombre,
                    'curso' => $evaluacion->seccion->curso->nombre,
                    'fecha_evaluacion' => $evaluacion->fecha_evaluacion,
                    'fecha_limite' => $evaluacion->fecha_limite,
                    'dias_restantes' => $evaluacion->dias_para_vencimiento,
                    'nota_maxima' => $evaluacion->nota_maxima,
                    'porcentaje' => $evaluacion->porcentaje,
                    'estado_calificacion' => $calificacion ? $calificacion->estado : 'pendiente'
                ];
            });
    }

    /**
     * Calificaciones recientes (últimos 14 días)
     */
    private function getCalificacionesRecientes($estudiante)
    {
        $fechaInicio = Carbon::now()->subDays(14);

        return Calificacion::with(['evaluacion.seccion.curso', 'evaluacion.tipoEvaluacion'])
            ->where('estudiante_id', $estudiante->id)
            ->whereNotNull('nota')
            ->where('fecha_calificacion', '>=', $fechaInicio)
            ->orderBy('fecha_calificacion', 'desc')
            ->limit(5)
            ->get()
            ->map(function($calificacion) {
                return [
                    'id' => $calificacion->id,
                    'evaluacion' => $calificacion->evaluacion->nombre,
                    'tipo' => $calificacion->evaluacion->tipoEvaluacion->nombre,
                    'curso' => $calificacion->evaluacion->seccion->curso->nombre,
                    'nota' => $calificacion->nota,
                    'nota_maxima' => $calificacion->evaluacion->nota_maxima,
                    'porcentaje_obtenido' => $calificacion->porcentaje_obtenido,
                    'esta_aprobada' => $calificacion->esta_aprobada,
                    'fecha_calificacion' => $calificacion->fecha_calificacion,
                    'comentarios' => $calificacion->comentarios
                ];
            });
    }

    /**
     * Horario de clases de hoy
     */
    private function getHorarioHoy($estudiante)
    {
        $hoy = Carbon::now()->locale('es')->dayName;

        return Inscripcion::with(['seccion.curso', 'seccion.profesor'])
            ->where('estudiante_id', $estudiante->id)
            ->where('estado', 'inscrito')
            ->get()
            ->filter(function($inscripcion) use ($hoy) {
                $horario = $inscripcion->seccion->horario;
                return $horario && isset($horario[strtolower($hoy)]);
            })
            ->map(function($inscripcion) use ($hoy) {
                $horario = $inscripcion->seccion->horario;
                
                return [
                    'curso' => $inscripcion->seccion->curso->nombre,
                    'codigo_curso' => $inscripcion->seccion->curso->codigo_curso,
                    'profesor' => $inscripcion->seccion->profesor->nombre_completo,
                    'aula' => $inscripcion->seccion->aula,
                    'hora' => $horario[strtolower($hoy)] ?? 'No definida',
                    'seccion_id' => $inscripcion->seccion->id
                ];
            })
            ->sortBy('hora')
            ->values();
    }

    /**
     * Contar evaluaciones pendientes
     */
    private function contarEvaluacionesPendientes($estudiante)
    {
        return Calificacion::whereHas('evaluacion.seccion.inscripciones', function($query) use ($estudiante) {
                $query->where('estudiante_id', $estudiante->id)
                      ->where('estado', 'inscrito');
            })
            ->where('estudiante_id', $estudiante->id)
            ->where('estado', 'pendiente')
            ->count();
    }

    /**
     * Obtener datos para gráficos
     */
    public function getDatosGraficos(Request $request)
    {
        $estudiante = Auth::user();
        $periodoId = $request->get('periodo_id');
        
        if (!$periodoId) {
            $periodoActual = PeriodoAcademico::vigente()->first();
            $periodoId = $periodoActual ? $periodoActual->id : null;
        }

        return response()->json([
            'progreso_notas' => $this->getProgresoNotas($estudiante, $periodoId),
            'distribucion_calificaciones' => $this->getDistribucionCalificaciones($estudiante, $periodoId),
            'asistencia_mensual' => $this->getAsistenciaMensual($estudiante, $periodoId)
        ]);
    }

    /**
     * Progreso de notas por materia
     */
    private function getProgresoNotas($estudiante, $periodoId)
    {
        $inscripciones = Inscripcion::with(['seccion.curso', 'seccion.evaluaciones.calificaciones' => function($query) use ($estudiante) {
                $query->where('estudiante_id', $estudiante->id);
            }])
            ->where('estudiante_id', $estudiante->id)
            ->when($periodoId, function($query) use ($periodoId) {
                return $query->whereHas('seccion', function($q) use ($periodoId) {
                    $q->where('periodo_academico_id', $periodoId);
                });
            })
            ->where('estado', 'inscrito')
            ->get();

        return $inscripciones->map(function($inscripcion) {
            $evaluaciones = $inscripcion->seccion->evaluaciones;
            $totalPorcentaje = $evaluaciones->sum('porcentaje');
            $porcentajeCalificado = 0;
            $notaPonderada = 0;

            foreach ($evaluaciones as $evaluacion) {
                $calificacion = $evaluacion->calificaciones->first();
                if ($calificacion && $calificacion->nota !== null) {
                    $porcentajeCalificado += $evaluacion->porcentaje;
                    $notaPonderada += ($calificacion->nota * $evaluacion->porcentaje) / 100;
                }
            }

            return [
                'curso' => $inscripcion->seccion->curso->nombre,
                'porcentaje_completado' => $totalPorcentaje > 0 ? round(($porcentajeCalificado / $totalPorcentaje) * 100, 1) : 0,
                'nota_actual' => round($notaPonderada, 2),
                'nota_final' => $inscripcion->nota_final
            ];
        });
    }

    /**
     * Distribución de calificaciones
     */
    private function getDistribucionCalificaciones($estudiante, $periodoId)
    {
        $calificaciones = Calificacion::whereHas('evaluacion.seccion', function($query) use ($periodoId) {
                if ($periodoId) {
                    $query->where('periodo_academico_id', $periodoId);
                }
            })
            ->where('estudiante_id', $estudiante->id)
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
    private function getAsistenciaMensual($estudiante, $periodoId)
    {
        return Asistencia::whereHas('inscripcion', function($query) use ($estudiante, $periodoId) {
                $query->where('estudiante_id', $estudiante->id);
                if ($periodoId) {
                    $query->whereHas('seccion', function($q) use ($periodoId) {
                        $q->where('periodo_academico_id', $periodoId);
                    });
                }
            })
            ->selectRaw('MONTH(fecha) as mes, estado, COUNT(*) as total')
            ->groupBy('mes', 'estado')
            ->orderBy('mes')
            ->get()
            ->groupBy('mes')
            ->map(function($grupo, $mes) {
                $total = $grupo->sum('total');
                $presentes = $grupo->where('estado', 'presente')->sum('total');
                $tardanzas = $grupo->where('estado', 'tardanza')->sum('total');
                
                return [
                    'mes' => $mes,
                    'total' => $total,
                    'presentes' => $presentes + $tardanzas,
                    'ausentes' => $grupo->where('estado', 'ausente')->sum('total'),
                    'porcentaje' => $total > 0 ? round((($presentes + $tardanzas) / $total) * 100, 1) : 0
                ];
            })
            ->values();
    }
}