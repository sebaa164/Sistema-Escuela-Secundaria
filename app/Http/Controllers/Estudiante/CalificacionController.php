<?php

namespace App\Http\Controllers\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\Calificacion;
use App\Models\Inscripcion;
use App\Models\Evaluacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalificacionController extends Controller
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
     * Ver todas las calificaciones del estudiante
     */
    public function index(Request $request)
    {
        $estudiante = Auth::user();
        $inscripcionId = $request->get('inscripcion_id');
        $periodoId = $request->get('periodo_id');

        // Obtener inscripciones con evaluaciones y calificaciones
        $inscripciones = Inscripcion::with([
            'seccion.curso',
            'seccion.profesor',
            'seccion.periodo',
            'seccion.evaluaciones.tipoEvaluacion',
            'seccion.evaluaciones.calificaciones' => function($query) use ($estudiante) {
                $query->where('estudiante_id', $estudiante->id);
            }
        ])
        ->where('estudiante_id', $estudiante->id)
        ->where('estado', 'inscrito')
        ->get();

        // Calcular estadísticas
        $promedioGeneral = $inscripciones->whereNotNull('nota_final')->avg('nota_final') ?? 0;
        $materiasAprobadas = $inscripciones->where('nota_final', '>=', 70)->count();
        $materiasPendientes = $inscripciones->whereNull('nota_final')->count();
        $creditosAcumulados = $inscripciones->where('nota_final', '>=', 70)->sum(function($i) {
            return $i->seccion->curso->creditos ?? 0;
        });

        return view('estudiante.calificaciones.index', compact(
            'inscripciones',
            'promedioGeneral',
            'materiasAprobadas',
            'materiasPendientes',
            'creditosAcumulados'
        ));
    }

    /**
     * Ver calificaciones por materia específica
     */
    public function porMateria($inscripcionId)
    {
        $inscripcion = Inscripcion::with([
            'seccion.curso',
            'seccion.profesor',
            'seccion.periodo',
            'seccion.evaluaciones.tipoEvaluacion'
        ])
        ->where('estudiante_id', Auth::id())
        ->findOrFail($inscripcionId);

        $calificaciones = Calificacion::with('evaluacion.tipoEvaluacion')
            ->whereHas('evaluacion', function($query) use ($inscripcion) {
                $query->where('seccion_id', $inscripcion->seccion_id);
            })
            ->where('estudiante_id', Auth::id())
            ->get()
            ->keyBy('evaluacion_id');

        // Calcular estadísticas
        $evaluaciones = $inscripcion->seccion->evaluaciones;
        $estadisticas = $this->calcularEstadisticas($evaluaciones, $calificaciones);

        return view('estudiante.calificaciones.materia', compact('inscripcion', 'evaluaciones', 'calificaciones', 'estadisticas'));
    }

    /**
     * Ver detalles de una calificación específica
     */
    public function show($calificacionId)
    {
        $calificacion = Calificacion::with([
            'evaluacion.seccion.curso',
            'evaluacion.seccion.profesor',
            'evaluacion.tipoEvaluacion'
        ])
        ->where('estudiante_id', Auth::id())
        ->findOrFail($calificacionId);

        return view('estudiante.calificaciones.show', compact('calificacion'));
    }

    /**
     * Comparar rendimiento con el promedio de la clase
     */
    public function compararRendimiento($inscripcionId)
    {
        $inscripcion = Inscripcion::with('seccion.curso')
            ->where('estudiante_id', Auth::id())
            ->findOrFail($inscripcionId);

        $evaluaciones = Evaluacion::where('seccion_id', $inscripcion->seccion_id)
            ->with(['calificaciones', 'tipoEvaluacion'])
            ->get();

        $comparacion = $evaluaciones->map(function($evaluacion) {
            $calificacionEstudiante = $evaluacion->calificaciones
                ->where('estudiante_id', Auth::id())
                ->first();

            $notasClase = $evaluacion->calificaciones
                ->whereNotNull('nota')
                ->pluck('nota');

            return [
                'evaluacion' => $evaluacion->nombre,
                'tipo' => $evaluacion->tipoEvaluacion->nombre,
                'mi_nota' => $calificacionEstudiante ? $calificacionEstudiante->nota : null,
                'promedio_clase' => $notasClase->count() > 0 ? round($notasClase->avg(), 2) : null,
                'nota_mayor' => $notasClase->max(),
                'nota_menor' => $notasClase->min(),
                'mi_posicion' => $this->calcularPosicion($calificacionEstudiante, $notasClase),
                'total_estudiantes' => $notasClase->count()
            ];
        });

        return view('estudiante.calificaciones.comparar', compact('inscripcion', 'comparacion'));
    }

    /**
     * Reporte de progreso académico
     */
    public function progreso(Request $request)
    {
        $estudiante = Auth::user();
        $periodoId = $request->get('periodo_id');

        $inscripciones = Inscripcion::with([
            'seccion.curso',
            'seccion.periodo',
            'seccion.evaluaciones.calificaciones' => function($query) use ($estudiante) {
                $query->where('estudiante_id', $estudiante->id);
            }
        ])
        ->where('estudiante_id', $estudiante->id)
        ->when($periodoId, function($query) use ($periodoId) {
            return $query->whereHas('seccion', function($q) use ($periodoId) {
                $q->where('periodo_academico_id', $periodoId);
            });
        })
        ->where('estado', 'inscrito')
        ->get();

        $progreso = $inscripciones->map(function($inscripcion) {
            $evaluaciones = $inscripcion->seccion->evaluaciones;
            $totalPorcentaje = $evaluaciones->sum('porcentaje');
            $porcentajeCompletado = 0;
            $notaPonderada = 0;

            foreach ($evaluaciones as $evaluacion) {
                $calificacion = $evaluacion->calificaciones->first();
                if ($calificacion && $calificacion->nota !== null) {
                    $porcentajeCompletado += $evaluacion->porcentaje;
                    $notaPonderada += ($calificacion->nota * $evaluacion->porcentaje) / 100;
                }
            }

            return [
                'inscripcion' => $inscripcion,
                'total_evaluaciones' => $evaluaciones->count(),
                'evaluaciones_calificadas' => $evaluaciones->filter(function($eval) {
                    return $eval->calificaciones->first() && $eval->calificaciones->first()->nota !== null;
                })->count(),
                'porcentaje_completado' => $totalPorcentaje > 0 ? round(($porcentajeCompletado / $totalPorcentaje) * 100, 1) : 0,
                'nota_actual' => round($notaPonderada, 2),
                'nota_final' => $inscripcion->nota_final,
                'estado_aprobacion' => $this->determinarEstadoAprobacion($notaPonderada, $inscripcion->nota_final)
            ];
        });

        $periodos = \App\Models\PeriodoAcademico::orderBy('fecha_inicio', 'desc')->get();

        return view('estudiante.calificaciones.progreso', compact('progreso', 'periodos', 'periodoId'));
    }

    /**
     * Exportar calificaciones a Excel
     */
    public function exportar(Request $request, $inscripcionId)
    {
        $inscripcion = Inscripcion::with([
            'seccion.curso',
            'seccion.profesor',
            'estudiante'
        ])
        ->where('estudiante_id', Auth::id())
        ->findOrFail($inscripcionId);

        $calificaciones = Calificacion::with(['evaluacion.tipoEvaluacion'])
            ->whereHas('evaluacion', function($query) use ($inscripcion) {
                $query->where('seccion_id', $inscripcion->seccion_id);
            })
            ->where('estudiante_id', Auth::id())
            ->orderBy('created_at', 'asc')
            ->get();

        return $this->exportarExcel($inscripcion, $calificaciones);
    }

    /**
     * Calcular estadísticas de la materia
     */
    private function calcularEstadisticas($evaluaciones, $calificaciones)
    {
        $totalPorcentaje = $evaluaciones->sum('porcentaje');
        $porcentajeCalificado = 0;
        $notaPonderada = 0;
        $evaluacionesCalificadas = 0;

        foreach ($evaluaciones as $evaluacion) {
            $calificacion = $calificaciones->get($evaluacion->id);
            if ($calificacion && $calificacion->nota !== null) {
                $porcentajeCalificado += $evaluacion->porcentaje;
                $notaPonderada += ($calificacion->nota * $evaluacion->porcentaje) / 100;
                $evaluacionesCalificadas++;
            }
        }

        return [
            'total_evaluaciones' => $evaluaciones->count(),
            'evaluaciones_calificadas' => $evaluacionesCalificadas,
            'evaluaciones_pendientes' => $evaluaciones->count() - $evaluacionesCalificadas,
            'porcentaje_completado' => $totalPorcentaje > 0 ? round(($porcentajeCalificado / $totalPorcentaje) * 100, 1) : 0,
            'nota_actual' => round($notaPonderada, 2),
            'nota_necesaria' => $this->calcularNotaNecesaria($totalPorcentaje, $porcentajeCalificado, $notaPonderada)
        ];
    }

    /**
     * Calcular nota necesaria para aprobar
     */
    private function calcularNotaNecesaria($totalPorcentaje, $porcentajeCalificado, $notaPonderada)
    {
        $notaMinima = config('app.nota_minima_aprobacion', 70);
        $porcentajeRestante = $totalPorcentaje - $porcentajeCalificado;
        
        if ($porcentajeRestante <= 0) {
            return null;
        }

        $notaFaltante = $notaMinima - $notaPonderada;
        $notaNecesaria = ($notaFaltante * 100) / $porcentajeRestante;

        return $notaNecesaria > 0 ? round($notaNecesaria, 2) : 0;
    }

    /**
     * Calcular posición del estudiante en la clase
     */
    private function calcularPosicion($calificacionEstudiante, $notasClase)
    {
        if (!$calificacionEstudiante || !$calificacionEstudiante->nota) {
            return null;
        }

        $notasMayores = $notasClase->where('>', $calificacionEstudiante->nota)->count();
        return $notasMayores + 1;
    }

    /**
     * Determinar estado de aprobación
     */
    private function determinarEstadoAprobacion($notaActual, $notaFinal)
    {
        $notaMinima = config('app.nota_minima_aprobacion', 70);
        
        if ($notaFinal !== null) {
            return $notaFinal >= $notaMinima ? 'aprobado' : 'reprobado';
        }
        
        if ($notaActual >= $notaMinima) {
            return 'en_progreso_aprobando';
        } else {
            return 'en_progreso_riesgo';
        }
    }

    /**
     * Exportar a Excel
     */
    private function exportarExcel($inscripcion, $calificaciones)
    {
        // Calcular estadísticas
        $totalCalificaciones = $calificaciones->count();
        $calificadas = $calificaciones->where('nota', '!=', null)->count();
        $notaPromedio = $calificaciones->where('nota', '!=', null)->avg('nota');
        $notaPonderada = 0;
        
        foreach ($calificaciones as $calif) {
            if ($calif->nota !== null) {
                $notaPonderada += ($calif->nota * $calif->evaluacion->porcentaje) / 100;
            }
        }

        $html = '
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; }
                table { border-collapse: collapse; width: 100%; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #4CAF50; color: white; font-weight: bold; }
                .header { background-color: #f2f2f2; font-weight: bold; padding: 10px; margin-bottom: 20px; }
                .aprobado { background-color: #d4edda; }
                .reprobado { background-color: #f8d7da; }
                .pendiente { background-color: #fff3cd; }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>Reporte de Calificaciones</h2>
                <p><strong>Estudiante:</strong> ' . htmlspecialchars($inscripcion->estudiante->nombre_completo) . '</p>
                <p><strong>Curso:</strong> ' . htmlspecialchars($inscripcion->seccion->curso->nombre) . ' (' . htmlspecialchars($inscripcion->seccion->curso->codigo_curso) . ')</p>
                <p><strong>Profesor:</strong> ' . htmlspecialchars($inscripcion->seccion->profesor->nombre_completo) . '</p>
                <p><strong>Período:</strong> ' . htmlspecialchars($inscripcion->seccion->periodo->nombre) . '</p>
                <p><strong>Fecha de generación:</strong> ' . \Carbon\Carbon::now()->format('d/m/Y H:i') . '</p>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Evaluación</th>
                        <th>Tipo</th>
                        <th>Fecha</th>
                        <th>Nota</th>
                        <th>Nota Máxima</th>
                        <th>Porcentaje</th>
                        <th>Comentarios</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($calificaciones as $calificacion) {
            $nota = $calificacion->nota ?? 'Sin calificar';
            $rowClass = '';
            
            if ($calificacion->nota !== null) {
                $rowClass = $calificacion->nota >= 70 ? 'aprobado' : 'reprobado';
            } else {
                $rowClass = 'pendiente';
            }
            
            $html .= '
                    <tr class="' . $rowClass . '">
                        <td>' . htmlspecialchars($calificacion->evaluacion->nombre) . '</td>
                        <td>' . htmlspecialchars($calificacion->evaluacion->tipoEvaluacion->nombre) . '</td>
                        <td>' . \Carbon\Carbon::parse($calificacion->evaluacion->fecha_evaluacion)->format('d/m/Y') . '</td>
                        <td>' . ($calificacion->nota !== null ? number_format($calificacion->nota, 2) : 'Sin calificar') . '</td>
                        <td>' . number_format($calificacion->evaluacion->nota_maxima, 2) . '</td>
                        <td>' . number_format($calificacion->evaluacion->porcentaje, 1) . '%</td>
                        <td>' . htmlspecialchars($calificacion->comentarios ?? '-') . '</td>
                    </tr>';
        }

        $html .= '
                </tbody>
            </table>
            
            <div style="margin-top: 20px; padding: 10px; background-color: #f2f2f2;">
                <h3>Resumen</h3>
                <p><strong>Total de evaluaciones:</strong> ' . $totalCalificaciones . '</p>
                <p><strong>Evaluaciones calificadas:</strong> ' . $calificadas . '</p>
                <p><strong>Evaluaciones pendientes:</strong> ' . ($totalCalificaciones - $calificadas) . '</p>
                <p><strong>Nota promedio:</strong> ' . ($notaPromedio ? number_format($notaPromedio, 2) : 'N/A') . '</p>
                <p><strong>Nota ponderada actual:</strong> ' . number_format($notaPonderada, 2) . '</p>
                <p><strong>Estado:</strong> ' . ($notaPonderada >= 70 ? 'Aprobando' : 'Reprobando') . '</p>
            </div>
        </body>
        </html>';

        $filename = 'calificaciones_' . $inscripcion->seccion->curso->codigo_curso . '_' . date('Y-m-d_His') . '.xls';

        return response($html, 200)
            ->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}