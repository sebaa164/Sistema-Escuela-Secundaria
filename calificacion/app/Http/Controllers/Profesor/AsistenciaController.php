<?php

namespace App\Http\Controllers\Profesor;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Models\Seccion;
use App\Models\Inscripcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AsistenciaController extends Controller
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
     * Lista todas las secciones del profesor para gestionar asistencia
     */
    public function listarSecciones()
    {
        $secciones = Seccion::with(['curso', 'inscripciones'])
            ->where('profesor_id', Auth::id())
            ->whereHas('inscripciones', function($query) {
                $query->where('estado', 'inscrito');
            })
            ->orderBy('curso_id')
            ->get();

        return view('profesor.asistencias.index', compact('secciones'));
    }

    /**
     * Lista de asistencia por sección
     */
    public function index(Request $request, $seccionId)
    {
        $seccion = Seccion::with(['curso', 'inscripciones.estudiante'])
            ->where('profesor_id', Auth::id())
            ->findOrFail($seccionId);

        $fecha = $request->get('fecha', Carbon::today()->format('Y-m-d'));
        $mes = $request->get('mes', Carbon::now()->month);
        $año = $request->get('año', Carbon::now()->year);

        // Obtener inscripciones activas
        $inscripciones = $seccion->inscripciones()
            ->with('estudiante')
            ->where('estado', 'inscrito')
            ->orderBy('estudiante_id')
            ->get();

        // Si es vista por fecha específica
        if ($request->has('fecha')) {
            $asistencias = $this->obtenerAsistenciasPorFecha($inscripciones, $fecha);
            return view('profesor.asistencias.fecha', compact('seccion', 'inscripciones', 'asistencias', 'fecha'));
        }

        // Vista mensual
        $asistenciasMensuales = $this->obtenerAsistenciasMensuales($inscripciones, $mes, $año);
        
        return view('profesor.asistencias.index', compact('seccion', 'asistenciasMensuales', 'mes', 'año'));
    }

    /**
     * Registrar asistencia para una fecha
     */
    public function registrar(Request $request, $seccionId)
    {
        $request->validate([
            'fecha' => 'required|date',
            'asistencias' => 'required|array',
            'asistencias.*.inscripcion_id' => 'required|exists:inscripciones,id',
            'asistencias.*.estado' => 'required|in:presente,ausente,tardanza,justificada',
            'asistencias.*.observaciones' => 'nullable|string|max:500'
        ]);

        $seccion = Seccion::where('profesor_id', Auth::id())->findOrFail($seccionId);

        DB::beginTransaction();
        try {
            foreach ($request->asistencias as $datos) {
                // Verificar que la inscripción pertenece a esta sección
                $inscripcion = Inscripcion::where('id', $datos['inscripcion_id'])
                    ->where('seccion_id', $seccionId)
                    ->firstOrFail();

                Asistencia::updateOrCreate(
                    [
                        'inscripcion_id' => $datos['inscripcion_id'],
                        'fecha' => $request->fecha
                    ],
                    [
                        'estado' => $datos['estado'],
                        'observaciones' => $datos['observaciones'] ?? null
                    ]
                );
            }

            DB::commit();
            return redirect()->back()->with('success', 'Asistencia registrada correctamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error al registrar la asistencia.');
        }
    }

    /**
     * Formulario para tomar asistencia
     */
    public function tomarAsistencia($seccionId, $fecha = null)
    {
        $fecha = $fecha ?? Carbon::today()->format('Y-m-d');
        
        $seccion = Seccion::with(['curso', 'inscripciones.estudiante'])
            ->where('profesor_id', Auth::id())
            ->findOrFail($seccionId);

        $inscripciones = $seccion->inscripciones()
            ->with('estudiante')
            ->where('estado', 'inscrito')
            ->orderBy('estudiante_id')
            ->get();

        // Obtener asistencias existentes para esta fecha
        $asistenciasExistentes = Asistencia::where('fecha', $fecha)
            ->whereIn('inscripcion_id', $inscripciones->pluck('id'))
            ->get()
            ->keyBy('inscripcion_id');

        return view('profesor.asistencias.tomar', compact('seccion', 'inscripciones', 'fecha', 'asistenciasExistentes'));
    }

    /**
     * Marcar todos como presentes
     */
    public function marcarTodosPresentes(Request $request, $seccionId)
    {
        $request->validate([
            'fecha' => 'required|date'
        ]);

        $seccion = Seccion::where('profesor_id', Auth::id())->findOrFail($seccionId);
        $inscripciones = $seccion->inscripciones()->where('estado', 'inscrito')->get();

        DB::beginTransaction();
        try {
            foreach ($inscripciones as $inscripcion) {
                Asistencia::updateOrCreate(
                    [
                        'inscripcion_id' => $inscripcion->id,
                        'fecha' => $request->fecha
                    ],
                    [
                        'estado' => 'presente'
                    ]
                );
            }

            DB::commit();
            return redirect()->back()->with('success', 'Todos los estudiantes marcados como presentes.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error al marcar asistencias.');
        }
    }

    /**
     * Reporte de asistencia por estudiante
     */
    public function reporteEstudiante($seccionId, $estudianteId)
    {
        $seccion = Seccion::where('profesor_id', Auth::id())->findOrFail($seccionId);
        
        $inscripcion = Inscripcion::where('seccion_id', $seccionId)
            ->where('estudiante_id', $estudianteId)
            ->with('estudiante')
            ->firstOrFail();

        $asistencias = Asistencia::where('inscripcion_id', $inscripcion->id)
            ->orderBy('fecha', 'desc')
            ->paginate(15);

        $estadisticas = [
            'total_clases' => Asistencia::where('inscripcion_id', $inscripcion->id)->count(),
            'presentes' => Asistencia::where('inscripcion_id', $inscripcion->id)->where('estado', 'presente')->count(),
            'ausentes' => Asistencia::where('inscripcion_id', $inscripcion->id)->where('estado', 'ausente')->count(),
            'tardanzas' => Asistencia::where('inscripcion_id', $inscripcion->id)->where('estado', 'tardanza')->count(),
            'justificadas' => Asistencia::where('inscripcion_id', $inscripcion->id)->where('estado', 'justificada')->count()
        ];

        $estadisticas['porcentaje_asistencia'] = $estadisticas['total_clases'] > 0 
            ? round((($estadisticas['presentes'] + $estadisticas['tardanzas']) / $estadisticas['total_clases']) * 100, 1)
            : 0;

        return view('profesor.asistencias.reporte-estudiante', compact('seccion', 'inscripcion', 'asistencias', 'estadisticas'));
    }

    /**
     * Reporte general de la sección
     */
    public function reporteSeccion($seccionId, Request $request)
    {
        $seccion = Seccion::where('profesor_id', Auth::id())->findOrFail($seccionId);
        
        $fechaInicio = $request->get('fecha_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $inscripciones = $seccion->inscripciones()
            ->with(['estudiante', 'asistencias' => function($query) use ($fechaInicio, $fechaFin) {
                $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
            }])
            ->where('estado', 'inscrito')
            ->get();

        $reporte = $inscripciones->map(function($inscripcion) {
            $asistencias = $inscripcion->asistencias;
            $total = $asistencias->count();
            $presentes = $asistencias->where('estado', 'presente')->count();
            $tardanzas = $asistencias->where('estado', 'tardanza')->count();
            
            return [
                'estudiante' => $inscripcion->estudiante,
                'total_clases' => $total,
                'presentes' => $presentes,
                'ausentes' => $asistencias->where('estado', 'ausente')->count(),
                'tardanzas' => $tardanzas,
                'justificadas' => $asistencias->where('estado', 'justificada')->count(),
                'porcentaje_asistencia' => $total > 0 ? round((($presentes + $tardanzas) / $total) * 100, 1) : 0
            ];
        });

        return view('profesor.asistencias.reporte-seccion', compact('seccion', 'reporte', 'fechaInicio', 'fechaFin'));
    }

    /**
     * Exportar reporte de asistencias
     */
    public function exportar($seccionId, Request $request)
    {
        $seccion = Seccion::with('curso')->where('profesor_id', Auth::id())->findOrFail($seccionId);
        
        $fechaInicio = $request->get('fecha_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $asistencias = DB::table('asistencias')
            ->join('inscripciones', 'asistencias.inscripcion_id', '=', 'inscripciones.id')
            ->join('usuarios', 'inscripciones.estudiante_id', '=', 'usuarios.id')
            ->where('inscripciones.seccion_id', $seccionId)
            ->whereBetween('asistencias.fecha', [$fechaInicio, $fechaFin])
            ->select('usuarios.nombre', 'usuarios.apellido', 'usuarios.email', 'asistencias.fecha', 'asistencias.estado', 'asistencias.observaciones')
            ->orderBy('usuarios.apellido')
            ->orderBy('asistencias.fecha')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="asistencias_' . $seccion->codigo_seccion . '.csv"'
        ];

        $callback = function() use ($asistencias) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Nombre', 'Apellido', 'Email', 'Fecha', 'Estado', 'Observaciones']);

            foreach ($asistencias as $asistencia) {
                fputcsv($file, [
                    $asistencia->nombre,
                    $asistencia->apellido,
                    $asistencia->email,
                    $asistencia->fecha,
                    $asistencia->estado,
                    $asistencia->observaciones
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Justificar ausencia
     */
    public function justificarAusencia(Request $request, $asistenciaId)
    {
        $request->validate([
            'observaciones' => 'required|string|max:500'
        ]);

        $asistencia = Asistencia::whereHas('inscripcion.seccion', function($query) {
                $query->where('profesor_id', Auth::id());
            })
            ->findOrFail($asistenciaId);

        $asistencia->update([
            'estado' => 'justificada',
            'observaciones' => $request->observaciones
        ]);

        return response()->json(['success' => true, 'message' => 'Ausencia justificada correctamente.']);
    }

    /**
     * Obtener asistencias por fecha específica
     */
    private function obtenerAsistenciasPorFecha($inscripciones, $fecha)
    {
        $asistencias = Asistencia::where('fecha', $fecha)
            ->whereIn('inscripcion_id', $inscripciones->pluck('id'))
            ->get()
            ->keyBy('inscripcion_id');

        return $inscripciones->map(function($inscripcion) use ($asistencias) {
            $asistencia = $asistencias->get($inscripcion->id);
            
            return [
                'inscripcion' => $inscripcion,
                'estudiante' => $inscripcion->estudiante,
                'asistencia' => $asistencia,
                'estado' => $asistencia ? $asistencia->estado : null,
                'observaciones' => $asistencia ? $asistencia->observaciones : null
            ];
        });
    }

    /**
     * Obtener asistencias mensuales
     */
    private function obtenerAsistenciasMensuales($inscripciones, $mes, $año)
    {
        return $inscripciones->map(function($inscripcion) use ($mes, $año) {
            $asistencias = $inscripcion->asistencias()
                ->whereMonth('fecha', $mes)
                ->whereYear('fecha', $año)
                ->get();

            $total = $asistencias->count();
            $presentes = $asistencias->where('estado', 'presente')->count();
            $tardanzas = $asistencias->where('estado', 'tardanza')->count();

            return [
                'estudiante' => $inscripcion->estudiante,
                'total_clases' => $total,
                'presentes' => $presentes,
                'ausentes' => $asistencias->where('estado', 'ausente')->count(),
                'tardanzas' => $tardanzas,
                'justificadas' => $asistencias->where('estado', 'justificada')->count(),
                'porcentaje_asistencia' => $total > 0 ? round((($presentes + $tardanzas) / $total) * 100, 1) : 0
            ];
        });
    }
}