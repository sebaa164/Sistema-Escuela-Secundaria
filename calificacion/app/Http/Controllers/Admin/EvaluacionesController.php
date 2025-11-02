<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evaluacion;
use App\Models\Seccion;
use App\Models\TipoEvaluacion;
use App\Models\Calificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EvaluacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Evaluacion::with([
            'seccion.curso',
            'seccion.periodo',
            'seccion.profesor',
            'tipoEvaluacion'
        ])->orderBy('fecha_evaluacion', 'desc');

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por sección
        if ($request->filled('seccion_id')) {
            $query->where('seccion_id', $request->seccion_id);
        }

        // Filtro por tipo de evaluación
        if ($request->filled('tipo_evaluacion_id')) {
            $query->where('tipo_evaluacion_id', $request->tipo_evaluacion_id);
        }

        // Filtro por fecha
        if ($request->filled('fecha_desde')) {
            $query->where('fecha_evaluacion', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_evaluacion', '<=', $request->fecha_hasta);
        }

        $evaluaciones = $query->paginate(15);

        // Estadísticas
        $stats = [
            'total' => Evaluacion::count(),
            'programadas' => Evaluacion::where('estado', 'programada')->count(),
            'activas' => Evaluacion::where('estado', 'activa')->count(),
            'finalizadas' => Evaluacion::where('estado', 'finalizada')->count(),
            'canceladas' => Evaluacion::where('estado', 'cancelada')->count()
        ];

        // Datos para filtros
        $secciones = Seccion::with(['curso', 'periodo'])
            ->where('estado', 'activo')
            ->orderBy('id', 'desc')
            ->get();

        $tiposEvaluacion = TipoEvaluacion::orderBy('nombre')->get();

        return view('admin.evaluaciones.index', compact(
            'evaluaciones',
            'stats',
            'secciones',
            'tiposEvaluacion'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $seccionId = $request->get('seccion_id');
        
        $secciones = Seccion::with(['curso', 'profesor', 'periodo'])
            ->where('estado', 'activo')
            ->orderBy('id', 'desc')
            ->get();

        $tiposEvaluacion = TipoEvaluacion::orderBy('nombre')->get();

        $seccionSeleccionada = $seccionId 
            ? Seccion::with(['curso', 'periodo'])->find($seccionId)
            : null;

        return view('admin.evaluaciones.create', compact(
            'secciones',
            'tiposEvaluacion',
            'seccionSeleccionada'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'seccion_id' => 'required|exists:secciones,id',
            'tipo_evaluacion_id' => 'required|exists:tipos_evaluacion,id',
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'fecha_evaluacion' => 'required|date',
            'fecha_limite' => 'nullable|date|after_or_equal:fecha_evaluacion',
            'nota_maxima' => 'required|numeric|min:0|max:100',
            'porcentaje' => 'required|numeric|min:0|max:100',
            'estado' => 'nullable|in:programada,activa,finalizada,cancelada',
            'instrucciones' => 'nullable|string'
        ], [
            'seccion_id.required' => 'Debe seleccionar una sección',
            'tipo_evaluacion_id.required' => 'Debe seleccionar un tipo de evaluación',
            'nombre.required' => 'El nombre de la evaluación es obligatorio',
            'fecha_evaluacion.required' => 'La fecha de evaluación es obligatoria',
            'fecha_limite.after_or_equal' => 'La fecha límite debe ser igual o posterior a la fecha de evaluación',
            'nota_maxima.required' => 'La nota máxima es obligatoria',
            'nota_maxima.min' => 'La nota máxima no puede ser menor a 0',
            'nota_maxima.max' => 'La nota máxima no puede ser mayor a 100',
            'porcentaje.required' => 'El porcentaje es obligatorio',
            'porcentaje.min' => 'El porcentaje no puede ser menor a 0',
            'porcentaje.max' => 'El porcentaje no puede ser mayor a 100'
        ]);

        // Verificar que el porcentaje total no exceda 100%
        $seccion = Seccion::findOrFail($validated['seccion_id']);
        $porcentajeActual = Evaluacion::where('seccion_id', $seccion->id)
            ->whereIn('estado', ['programada', 'activa', 'finalizada'])
            ->sum('porcentaje');

        if (($porcentajeActual + $validated['porcentaje']) > 100) {
            return back()->withErrors([
                'porcentaje' => "El porcentaje total de evaluaciones excedería el 100%. Porcentaje disponible: " . (100 - $porcentajeActual) . "%"
            ])->withInput();
        }

        try {
            $evaluacion = Evaluacion::create([
                'seccion_id' => $validated['seccion_id'],
                'tipo_evaluacion_id' => $validated['tipo_evaluacion_id'],
                'nombre' => $validated['nombre'],
                'descripcion' => $validated['descripcion'],
                'fecha_evaluacion' => $validated['fecha_evaluacion'],
                'fecha_limite' => $validated['fecha_limite'],
                'nota_maxima' => $validated['nota_maxima'],
                'porcentaje' => $validated['porcentaje'],
                'estado' => $validated['estado'] ?? 'programada',
                'instrucciones' => $validated['instrucciones']
            ]);

            return redirect()->route('admin.evaluaciones.index')
                ->with('success', 'Evaluación creada exitosamente');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al crear la evaluación: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Evaluacion $evaluacion)
    {
        $evaluacion->load([
            'seccion.curso',
            'seccion.profesor',
            'seccion.periodo',
            'tipoEvaluacion',
            'calificaciones.estudiante'
        ]);

        // Estadísticas de la evaluación
        $stats = [
            'total_estudiantes' => $evaluacion->seccion->inscripciones()
                ->where('estado', 'inscrito')
                ->count(),
            'calificadas' => $evaluacion->calificaciones()
                ->where('estado', 'calificada')
                ->count(),
            'pendientes' => $evaluacion->calificaciones()
                ->where('estado', 'pendiente')
                ->count(),
            'promedio' => round($evaluacion->calificaciones()
                ->where('estado', 'calificada')
                ->avg('nota'), 2),
            'nota_maxima_obtenida' => $evaluacion->calificaciones()
                ->where('estado', 'calificada')
                ->max('nota'),
            'nota_minima_obtenida' => $evaluacion->calificaciones()
                ->where('estado', 'calificada')
                ->min('nota')
        ];

        // Distribución de notas
        $distribucion = [
            'excelente' => $evaluacion->calificaciones()
                ->where('estado', 'calificada')
                ->where('nota', '>=', 90)
                ->count(),
            'muy_bueno' => $evaluacion->calificaciones()
                ->where('estado', 'calificada')
                ->whereBetween('nota', [80, 89.99])
                ->count(),
            'bueno' => $evaluacion->calificaciones()
                ->where('estado', 'calificada')
                ->whereBetween('nota', [70, 79.99])
                ->count(),
            'regular' => $evaluacion->calificaciones()
                ->where('estado', 'calificada')
                ->whereBetween('nota', [60, 69.99])
                ->count(),
            'insuficiente' => $evaluacion->calificaciones()
                ->where('estado', 'calificada')
                ->where('nota', '<', 60)
                ->count()
        ];

        return view('admin.evaluaciones.show', compact('evaluacion', 'stats', 'distribucion'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Evaluacion $evaluacion)
    {
        $evaluacion->load(['seccion.curso', 'tipoEvaluacion']);

        $secciones = Seccion::with(['curso', 'profesor', 'periodo'])
            ->where('estado', 'activo')
            ->orWhere('id', $evaluacion->seccion_id)
            ->orderBy('id', 'desc')
            ->get();

        $tiposEvaluacion = TipoEvaluacion::orderBy('nombre')->get();

        return view('admin.evaluaciones.edit', compact(
            'evaluacion',
            'secciones',
            'tiposEvaluacion'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Evaluacion $evaluacion)
    {
        $validated = $request->validate([
            'seccion_id' => 'required|exists:secciones,id',
            'tipo_evaluacion_id' => 'required|exists:tipos_evaluacion,id',
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'fecha_evaluacion' => 'required|date',
            'fecha_limite' => 'nullable|date|after_or_equal:fecha_evaluacion',
            'nota_maxima' => 'required|numeric|min:0|max:100',
            'porcentaje' => 'required|numeric|min:0|max:100',
            'estado' => 'required|in:programada,activa,finalizada,cancelada',
            'instrucciones' => 'nullable|string'
        ], [
            'seccion_id.required' => 'Debe seleccionar una sección',
            'tipo_evaluacion_id.required' => 'Debe seleccionar un tipo de evaluación',
            'nombre.required' => 'El nombre es obligatorio',
            'fecha_evaluacion.required' => 'La fecha de evaluación es obligatoria',
            'nota_maxima.required' => 'La nota máxima es obligatoria',
            'porcentaje.required' => 'El porcentaje es obligatorio',
            'estado.required' => 'El estado es obligatorio'
        ]);

        // Verificar porcentaje total (excluyendo esta evaluación)
        $porcentajeActual = Evaluacion::where('seccion_id', $validated['seccion_id'])
            ->where('id', '!=', $evaluacion->id)
            ->whereIn('estado', ['programada', 'activa', 'finalizada'])
            ->sum('porcentaje');

        if (($porcentajeActual + $validated['porcentaje']) > 100) {
            return back()->withErrors([
                'porcentaje' => "El porcentaje total excedería el 100%. Disponible: " . (100 - $porcentajeActual) . "%"
            ])->withInput();
        }

        try {
            $evaluacion->update([
                'seccion_id' => $validated['seccion_id'],
                'tipo_evaluacion_id' => $validated['tipo_evaluacion_id'],
                'nombre' => $validated['nombre'],
                'descripcion' => $validated['descripcion'],
                'fecha_evaluacion' => $validated['fecha_evaluacion'],
                'fecha_limite' => $validated['fecha_limite'],
                'nota_maxima' => $validated['nota_maxima'],
                'porcentaje' => $validated['porcentaje'],
                'estado' => $validated['estado'],
                'instrucciones' => $validated['instrucciones']
            ]);

            return redirect()->route('admin.evaluaciones.index')
                ->with('success', 'Evaluación actualizada exitosamente');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al actualizar: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Evaluacion $evaluacion)
    {
        // Verificar si tiene calificaciones
        $tieneCalificaciones = $evaluacion->calificaciones()->exists();

        if ($tieneCalificaciones) {
            return back()->withErrors([
                'error' => 'No se puede eliminar la evaluación porque tiene calificaciones registradas. Considere cancelarla en su lugar.'
            ]);
        }

        try {
            $nombreEvaluacion = $evaluacion->nombre;
            $evaluacion->delete();

            return redirect()->route('admin.evaluaciones.index')
                ->with('success', "Evaluación '{$nombreEvaluacion}' eliminada exitosamente");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al eliminar: ' . $e->getMessage()]);
        }
    }

    /**
     * Cambiar estado de la evaluación
     */
    public function cambiarEstado(Request $request, Evaluacion $evaluacion)
    {
        $validated = $request->validate([
            'estado' => 'required|in:programada,activa,finalizada,cancelada'
        ]);

        try {
            $estadoAnterior = $evaluacion->estado;
            $evaluacion->update(['estado' => $validated['estado']]);

            // Si se finaliza, crear calificaciones pendientes para estudiantes sin nota
            if ($validated['estado'] === 'finalizada') {
                $this->crearCalificacionesPendientes($evaluacion);
            }

            return back()->with('success', "Estado cambiado de '{$estadoAnterior}' a '{$validated['estado']}'");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al cambiar estado: ' . $e->getMessage()]);
        }
    }

    /**
     * Crear calificaciones pendientes para estudiantes que no tienen nota
     */
    private function crearCalificacionesPendientes(Evaluacion $evaluacion)
    {
        $inscripciones = $evaluacion->seccion->inscripciones()
            ->where('estado', 'inscrito')
            ->get();

        foreach ($inscripciones as $inscripcion) {
            // Verificar si ya tiene calificación
            $tieneCalificacion = Calificacion::where('evaluacion_id', $evaluacion->id)
                ->where('estudiante_id', $inscripcion->estudiante_id)
                ->exists();

            if (!$tieneCalificacion) {
                Calificacion::create([
                    'evaluacion_id' => $evaluacion->id,
                    'estudiante_id' => $inscripcion->estudiante_id,
                    'nota' => null,
                    'estado' => 'pendiente'
                ]);
            }
        }
    }

    /**
     * Duplicar evaluación
     */
    public function duplicar(Evaluacion $evaluacion)
    {
        try {
            $nuevaEvaluacion = $evaluacion->replicate();
            $nuevaEvaluacion->nombre = $evaluacion->nombre . ' (Copia)';
            $nuevaEvaluacion->estado = 'programada';
            $nuevaEvaluacion->fecha_evaluacion = null;
            $nuevaEvaluacion->fecha_limite = null;
            $nuevaEvaluacion->save();

            return redirect()->route('admin.evaluaciones.edit', $nuevaEvaluacion)
                ->with('success', 'Evaluación duplicada exitosamente. Actualice las fechas.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al duplicar: ' . $e->getMessage()]);
        }
    }
}