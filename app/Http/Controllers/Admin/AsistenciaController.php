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
        $query = Asistencia::with(['inscripcion.estudiante', 'inscripcion.seccion.curso']);

        // Filtro por sección
        if ($request->filled('seccion_id')) {
            $query->whereHas('inscripcion', function($q) use ($request) {
                $q->where('seccion_id', $request->seccion_id);
            });
        }

        // Filtro por período
        if ($request->filled('periodo_id')) {
            $query->whereHas('inscripcion.seccion', function($q) use ($request) {
                $q->where('periodo_academico_id', $request->periodo_id);
            });
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por fecha
        if ($request->filled('fecha')) {
            $query->whereDate('fecha', $request->fecha);
        }
        
        // BÚSQUEDA 
if ($request->filled('search')) {
    $search = $request->search;
    
    $query->whereHas('inscripcion', function($inscripcion) use ($search) {
        // Buscar en estudiante (nombre, apellido, email)
        $inscripcion->whereHas('estudiante', function($estudiante) use ($search) {
            $estudiante->where('nombre', 'like', "%{$search}%")
                ->orWhere('apellido', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhereRaw("CONCAT(nombre, ' ', apellido) LIKE ?", ["%{$search}%"]);
        })
        // Buscar en sección (código de sección)
        ->orWhereHas('seccion', function($seccion) use ($search) {
            $seccion->where('codigo_seccion', 'like', "%{$search}%");
        })
        // Buscar en curso (nombre y código_curso)
        ->orWhereHas('seccion.curso', function($curso) use ($search) {
            $curso->where('nombre', 'like', "%{$search}%")
                  ->orWhere('codigo_curso', 'like', "%{$search}%");
        });
    });
}

        $asistencias = $query->orderBy('fecha', 'desc')
                             ->orderBy('created_at', 'desc')
                             ->paginate(20)
                             ->withQueryString();

        // Datos para filtros
        $secciones = Seccion::with('curso')->where('estado', 'activo')->get();
        $periodos = PeriodoAcademico::orderBy('fecha_inicio', 'desc')->get();

        // Estadísticas
        $stats = [
            'total' => Asistencia::count(),
            'presente' => Asistencia::where('estado', 'presente')->count(),
            'tardanza' => Asistencia::where('estado', 'tardanza')->count(),
            'ausente' => Asistencia::where('estado', 'ausente')->count(),
        ];

        return view('admin.asistencias.index', compact('asistencias', 'secciones', 'periodos', 'stats'));
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