<?php

namespace App\Http\Controllers\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Models\Inscripcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AsistenciaController extends Controller
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
     * Ver resumen general de asistencias
     */
    public function index(Request $request)
    {
        $estudiante = Auth::user();
        $inscripcionId = $request->get('inscripcion_id');
        $mes = $request->get('mes', Carbon::now()->month);
        $año = $request->get('año', Carbon::now()->year);

        // Obtener inscripciones activas
        $inscripciones = Inscripcion::with(['seccion.curso', 'seccion.periodo'])
            ->where('estudiante_id', $estudiante->id)
            ->where('estado', 'inscrito')
            ->get();

        // Calcular estadísticas por materia
        $estadisticasPorMateria = $inscripciones->map(function($inscripcion) use ($mes, $año) {
            $asistencias = Asistencia::where('inscripcion_id', $inscripcion->id)
                ->whereMonth('fecha', $mes)
                ->whereYear('fecha', $año)
                ->get();

            $total = $asistencias->count();
            $presentes = $asistencias->where('estado', 'presente')->count();
            $tardanzas = $asistencias->where('estado', 'tardanza')->count();

            return [
                'inscripcion' => $inscripcion,
                'total_clases' => $total,
                'presentes' => $presentes,
                'ausentes' => $asistencias->where('estado', 'ausente')->count(),
                'tardanzas' => $tardanzas,
                'justificadas' => $asistencias->where('estado', 'justificada')->count(),
                'porcentaje_asistencia' => $total > 0 ? round((($presentes + $tardanzas) / $total) * 100, 1) : 0
            ];
        });

        return view('estudiante.asistencias.index', compact('estadisticasPorMateria', 'inscripciones', 'inscripcionId', 'mes', 'año'));
    }

    /**
     * Ver asistencias por materia específica
     */
    public function porMateria($inscripcionId, Request $request)
    {
        $inscripcion = Inscripcion::with(['seccion.curso', 'seccion.profesor'])
            ->where('estudiante_id', Auth::id())
            ->findOrFail($inscripcionId);

        $mes = $request->get('mes', Carbon::now()->month);
        $año = $request->get('año', Carbon::now()->year);

        $asistencias = Asistencia::where('inscripcion_id', $inscripcionId)
            ->when($mes && $año, function($query) use ($mes, $año) {
                return $query->whereMonth('fecha', $mes)->whereYear('fecha', $año);
            })
            ->orderBy('fecha', 'desc')
            ->paginate(20);

        // Calcular estadísticas del período
        $estadisticas = $this->calcularEstadisticas($inscripcionId, $mes, $año);

        return view('estudiante.asistencias.materia', compact('inscripcion', 'asistencias', 'estadisticas', 'mes', 'año'));
    }

    /**
     * Ver calendario de asistencias
     */
    public function calendario($inscripcionId, Request $request)
    {
        $inscripcion = Inscripcion::with('seccion.curso')
            ->where('estudiante_id', Auth::id())
            ->findOrFail($inscripcionId);

        $mes = $request->get('mes', Carbon::now()->month);
        $año = $request->get('año', Carbon::now()->year);

        $asistencias = Asistencia::where('inscripcion_id', $inscripcionId)
            ->whereMonth('fecha', $mes)
            ->whereYear('fecha', $año)
            ->get()
            ->keyBy(function($item) {
                return $item->fecha->format('Y-m-d');
            });

        return view('estudiante.asistencias.calendario', compact('inscripcion', 'asistencias', 'mes', 'año'));
    }

    /**
     * Reporte detallado de asistencias
     */
    public function reporte(Request $request)
    {
        $estudiante = Auth::user();
        $fechaInicio = $request->get('fecha_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $inscripciones = Inscripcion::with([
            'seccion.curso',
            'asistencias' => function($query) use ($fechaInicio, $fechaFin) {
                $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
            }
        ])
        ->where('estudiante_id', $estudiante->id)
        ->where('estado', 'inscrito')
        ->get();

        $reporte = $inscripciones->map(function($inscripcion) {
            $asistencias = $inscripcion->asistencias;
            $total = $asistencias->count();
            $presentes = $asistencias->where('estado', 'presente')->count();
            $tardanzas = $asistencias->where('estado', 'tardanza')->count();

            return [
                'curso' => $inscripcion->seccion->curso->nombre,
                'codigo_curso' => $inscripcion->seccion->curso->codigo_curso,
                'total_clases' => $total,
                'presentes' => $presentes,
                'ausentes' => $asistencias->where('estado', 'ausente')->count(),
                'tardanzas' => $tardanzas,
                'justificadas' => $asistencias->where('estado', 'justificada')->count(),
                'porcentaje_asistencia' => $total > 0 ? round((($presentes + $tardanzas) / $total) * 100, 1) : 0,
                'estado_asistencia' => $this->determinarEstadoAsistencia($total, $presentes, $tardanzas)
            ];
        });

        return view('estudiante.asistencias.reporte', compact('reporte', 'fechaInicio', 'fechaFin'));
    }

    /**
     * Ver detalles de una asistencia específica
     */
    public function show($asistenciaId)
    {
        $asistencia = Asistencia::with([
            'inscripcion.seccion.curso',
            'inscripcion.seccion.profesor'
        ])
        ->whereHas('inscripcion', function($query) {
            $query->where('estudiante_id', Auth::id());
        })
        ->findOrFail($asistenciaId);

        return view('estudiante.asistencias.show', compact('asistencia'));
    }

    /**
     * Exportar reporte de asistencias a Excel
     */
    public function exportar(Request $request, $inscripcionId = null)
    {
        $estudiante = Auth::user();
        $fechaInicio = $request->get('fecha_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $query = Asistencia::with(['inscripcion.seccion.curso', 'inscripcion.seccion.profesor'])
            ->whereHas('inscripcion', function($q) use ($estudiante) {
                $q->where('estudiante_id', $estudiante->id);
            })
            ->whereBetween('fecha', [$fechaInicio, $fechaFin]);

        if ($inscripcionId) {
            $query->where('inscripcion_id', $inscripcionId);
        }

        $asistencias = $query->orderBy('fecha', 'desc')->get();

        // Crear contenido HTML para Excel
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
                .presente { background-color: #d4edda; }
                .ausente { background-color: #f8d7da; }
                .tardanza { background-color: #fff3cd; }
                .justificada { background-color: #d1ecf1; }
            </style>
        </head>
        <body>
            <div class="header">
                <h2>Reporte de Asistencias</h2>
                <p><strong>Estudiante:</strong> ' . htmlspecialchars($estudiante->nombre_completo) . '</p>
                <p><strong>Período:</strong> ' . Carbon::parse($fechaInicio)->format('d/m/Y') . ' - ' . Carbon::parse($fechaFin)->format('d/m/Y') . '</p>
                <p><strong>Fecha de generación:</strong> ' . Carbon::now()->format('d/m/Y H:i') . '</p>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Curso</th>
                        <th>Código</th>
                        <th>Profesor</th>
                        <th>Estado</th>
                        <th>Observaciones</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($asistencias as $asistencia) {
            $estadoClass = $asistencia->estado;
            $estadoTexto = ucfirst($asistencia->estado);
            
            $html .= '
                    <tr class="' . $estadoClass . '">
                        <td>' . $asistencia->fecha->format('d/m/Y') . '</td>
                        <td>' . htmlspecialchars($asistencia->inscripcion->seccion->curso->nombre) . '</td>
                        <td>' . htmlspecialchars($asistencia->inscripcion->seccion->curso->codigo_curso) . '</td>
                        <td>' . htmlspecialchars($asistencia->inscripcion->seccion->profesor->nombre_completo) . '</td>
                        <td>' . $estadoTexto . '</td>
                        <td>' . htmlspecialchars($asistencia->observaciones ?? '-') . '</td>
                    </tr>';
        }

        $html .= '
                </tbody>
            </table>
            
            <div style="margin-top: 20px; padding: 10px; background-color: #f2f2f2;">
                <h3>Resumen</h3>
                <p><strong>Total de registros:</strong> ' . $asistencias->count() . '</p>
                <p><strong>Presentes:</strong> ' . $asistencias->where('estado', 'presente')->count() . '</p>
                <p><strong>Ausentes:</strong> ' . $asistencias->where('estado', 'ausente')->count() . '</p>
                <p><strong>Tardanzas:</strong> ' . $asistencias->where('estado', 'tardanza')->count() . '</p>
                <p><strong>Justificadas:</strong> ' . $asistencias->where('estado', 'justificada')->count() . '</p>
            </div>
        </body>
        </html>';

        $filename = 'asistencias_' . $estudiante->id . '_' . date('Y-m-d_His') . '.xls';

        return response($html, 200)
            ->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Obtener datos para gráficos de asistencia
     */
    public function getDatosGraficos($inscripcionId, Request $request)
    {
        $meses = $request->get('meses', 6); // Últimos 6 meses por defecto

        $asistenciasMensuales = Asistencia::where('inscripcion_id', $inscripcionId)
            ->where('fecha', '>=', Carbon::now()->subMonths($meses))
            ->selectRaw('MONTH(fecha) as mes, YEAR(fecha) as año, estado, COUNT(*) as total')
            ->groupBy('mes', 'año', 'estado')
            ->orderBy('año')
            ->orderBy('mes')
            ->get()
            ->groupBy(function($item) {
                return $item->año . '-' . str_pad($item->mes, 2, '0', STR_PAD_LEFT);
            });

        $datos = [];
        for ($i = $meses - 1; $i >= 0; $i--) {
            $fecha = Carbon::now()->subMonths($i);
            $clave = $fecha->format('Y-m');
            $grupo = $asistenciasMensuales->get($clave, collect());
            
            $total = $grupo->sum('total');
            $presentes = $grupo->where('estado', 'presente')->sum('total');
            $tardanzas = $grupo->where('estado', 'tardanza')->sum('total');
            
            $datos[] = [
                'mes' => $fecha->locale('es')->monthName,
                'total' => $total,
                'presentes' => $presentes + $tardanzas,
                'ausentes' => $grupo->where('estado', 'ausente')->sum('total'),
                'porcentaje' => $total > 0 ? round((($presentes + $tardanzas) / $total) * 100, 1) : 0
            ];
        }

        return response()->json($datos);
    }

    /**
     * Calcular estadísticas de asistencia
     */
    private function calcularEstadisticas($inscripcionId, $mes = null, $año = null)
    {
        $query = Asistencia::where('inscripcion_id', $inscripcionId);
        
        if ($mes && $año) {
            $query->whereMonth('fecha', $mes)->whereYear('fecha', $año);
        }

        $asistencias = $query->get();
        $total = $asistencias->count();
        $presentes = $asistencias->where('estado', 'presente')->count();
        $tardanzas = $asistencias->where('estado', 'tardanza')->count();
        $ausentes = $asistencias->where('estado', 'ausente')->count();
        $justificadas = $asistencias->where('estado', 'justificada')->count();

        return [
            'total_clases' => $total,
            'presentes' => $presentes,
            'tardanzas' => $tardanzas,
            'ausentes' => $ausentes,
            'justificadas' => $justificadas,
            'porcentaje_asistencia' => $total > 0 ? round((($presentes + $tardanzas) / $total) * 100, 1) : 0,
            'porcentaje_ausencias' => $total > 0 ? round(($ausentes / $total) * 100, 1) : 0,
            'estado_general' => $this->determinarEstadoAsistencia($total, $presentes, $tardanzas)
        ];
    }

    /**
     * Determinar estado general de asistencia
     */
    private function determinarEstadoAsistencia($total, $presentes, $tardanzas)
    {
        if ($total == 0) {
            return 'sin_datos';
        }

        $porcentaje = (($presentes + $tardanzas) / $total) * 100;

        if ($porcentaje >= 90) {
            return 'excelente';
        } elseif ($porcentaje >= 80) {
            return 'buena';
        } elseif ($porcentaje >= 70) {
            return 'regular';
        } else {
            return 'deficiente';
        }
    }
}