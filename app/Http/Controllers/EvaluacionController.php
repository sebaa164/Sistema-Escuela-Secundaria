<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evaluacion;
use App\Models\Seccion;
use App\Models\TipoEvaluacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EvaluacionController extends Controller
{
    /**
     * Mostrar listado de evaluaciones
     */
    public function index(Request $request)
    {
        $query = Evaluacion::with([
            'seccion.curso',
            'seccion.profesor',
            'seccion.periodo',
            'tipoEvaluacion'
        ]);

        // Filtros
        if ($request->filled('seccion_id')) {
            $query->where('seccion_id', $request->seccion_id);
        }

        if ($request->filled('tipo_evaluacion_id')) {
            $query->where('tipo_evaluacion_id', $request->tipo_evaluacion_id);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $evaluaciones = $query->orderBy('fecha_evaluacion', 'desc')->paginate(15);

        // Para filtros
        $secciones = Seccion::with(['curso', 'periodo'])
            ->whereHas('curso')
            ->orderBy('created_at', 'desc')
            ->get();

        $tiposEvaluacion = TipoEvaluacion::where('activo', true)->orderBy('nombre')->get();

        $stats = [
            'total' => Evaluacion::count(),
            'programadas' => Evaluacion::where('estado', 'programada')->count(),
            'activas' => Evaluacion::where('estado', 'activa')->count(),
            'finalizadas' => Evaluacion::where('estado', 'finalizada')->count(),
        ];

        return view('admin.evaluaciones.index', compact('evaluaciones', 'secciones', 'tiposEvaluacion', 'stats'));
    }

    /**
     * Mostrar formulario de crear evaluación
     */
    public function create()
    {
        // Obtener TODAS las secciones con sus relaciones
        $secciones = Seccion::with(['curso', 'profesor', 'periodo'])
            ->whereHas('curso')
            ->whereHas('periodo', function($q) {
                $q->where('estado', 'activo')
                  ->orWhere('fecha_fin', '>=', now()->subMonths(3));
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Obtener tipos de evaluación activos
        $tiposEvaluacion = TipoEvaluacion::where('activo', true)
            ->orderBy('nombre')
            ->get();

        // Si no hay tipos de evaluación, crear los básicos
        if ($tiposEvaluacion->isEmpty()) {
            $this->crearTiposEvaluacionBasicos();
            $tiposEvaluacion = TipoEvaluacion::where('activo', true)
                ->orderBy('nombre')
                ->get();
        }

        return view('admin.evaluaciones.create', compact('secciones', 'tiposEvaluacion'));
    }

    /**
     * Guardar nueva evaluación
     */
    public function store(Request $request)
    {
        $request->validate([
            'seccion_id' => 'required|exists:secciones,id',
            'tipo_evaluacion_id' => 'required|exists:tipos_evaluacion,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_evaluacion' => 'required|date',
            'porcentaje' => 'required|numeric|min:0.01|max:100',
            'nota_maxima' => 'nullable|numeric|min:0',
            'estado' => 'required|in:programada,activa,finalizada,cancelada'
        ], [
            'seccion_id.required' => 'Debe seleccionar una sección',
            'seccion_id.exists' => 'La sección seleccionada no existe',
            'tipo_evaluacion_id.required' => 'Debe seleccionar un tipo de evaluación',
            'nombre.required' => 'El nombre de la evaluación es obligatorio',
            'fecha_evaluacion.required' => 'La fecha de evaluación es obligatoria',
            'porcentaje.required' => 'El porcentaje es obligatorio',
            'porcentaje.min' => 'El porcentaje mínimo es 0.01%',
            'porcentaje.max' => 'El porcentaje máximo es 100%',
        ]);

        try {
            DB::beginTransaction();

            // Verificar que el porcentaje total no exceda 100%
            $seccion = Seccion::find($request->seccion_id);
            $porcentajeActual = $seccion->evaluaciones()->sum('porcentaje');
            
            if (($porcentajeActual + $request->porcentaje) > 100) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'porcentaje' => "⚠️ El porcentaje total de evaluaciones no puede exceder 100%. Actualmente hay {$porcentajeActual}% asignado. Solo puede agregar " . (100 - $porcentajeActual) . "% más."
                    ]);
            }

            $evaluacion = Evaluacion::create([
                'seccion_id' => $request->seccion_id,
                'tipo_evaluacion_id' => $request->tipo_evaluacion_id,
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'fecha_evaluacion' => $request->fecha_evaluacion,
                'porcentaje' => $request->porcentaje,
                'nota_maxima' => $request->nota_maxima ?? 100,
                'estado' => $request->estado,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.evaluaciones.index')
                ->with('success', '✅ Evaluación creada exitosamente. Ahora puede calificar a los estudiantes en esta evaluación.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear evaluación: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->withErrors(['error' => '❌ Error al crear la evaluación: ' . $e->getMessage()]);
        }
    }

    /**
     * Mostrar detalle de evaluación
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

        $seccion = $evaluacion->seccion;
        $totalInscritos = $seccion ? $seccion->inscripciones()->count() : 0;
        $totalCalificados = $evaluacion->calificaciones()->count();
        $promedio = $evaluacion->calificaciones()->avg('nota');
        $aprobados = $evaluacion->calificaciones()->where('nota', '>=', 70)->count();

        $estadisticas = [
            'total_estudiantes' => $totalInscritos,
            'calificados' => $totalCalificados,
            'pendientes' => max($totalInscritos - $totalCalificados, 0),
            'promedio' => $promedio,
            'aprobados' => $aprobados,
        ];

        return view('admin.evaluaciones.show', compact('evaluacion', 'estadisticas'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(Evaluacion $evaluacion)
    {
        $secciones = Seccion::with(['curso', 'profesor', 'periodo'])
            ->whereHas('curso')
            ->orderBy('created_at', 'desc')
            ->get();

        $tiposEvaluacion = TipoEvaluacion::where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('admin.evaluaciones.edit', compact('evaluacion', 'secciones', 'tiposEvaluacion'));
    }

    /**
     * Actualizar evaluación
     */
    public function update(Request $request, Evaluacion $evaluacion)
    {
        $request->validate([
            'tipo_evaluacion_id' => 'required|exists:tipos_evaluacion,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'fecha_evaluacion' => 'required|date',
            'porcentaje' => 'required|numeric|min:0.01|max:100',
            'nota_maxima' => 'nullable|numeric|min:0',
            'estado' => 'required|in:programada,activa,finalizada,cancelada'
        ]);

        try {
            DB::beginTransaction();

            // Verificar porcentaje total (excluyendo la evaluación actual)
            $porcentajeActual = $evaluacion->seccion->evaluaciones()
                ->where('id', '!=', $evaluacion->id)
                ->sum('porcentaje');
            
            if (($porcentajeActual + $request->porcentaje) > 100) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'porcentaje' => "⚠️ El porcentaje total no puede exceder 100%. Actualmente hay {$porcentajeActual}% asignado (sin incluir esta evaluación)."
                    ]);
            }

            $evaluacion->update([
                'tipo_evaluacion_id' => $request->tipo_evaluacion_id,
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'fecha_evaluacion' => $request->fecha_evaluacion,
                'porcentaje' => $request->porcentaje,
                'nota_maxima' => $request->nota_maxima ?? 100,
                'estado' => $request->estado,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.evaluaciones.show', $evaluacion)
                ->with('success', '✅ Evaluación actualizada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar evaluación: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->withErrors(['error' => '❌ Error al actualizar la evaluación: ' . $e->getMessage()]);
        }
    }

    /**
     * Eliminar evaluación
     */
    public function destroy(Evaluacion $evaluacion)
    {
        try {
            // Verificar si tiene calificaciones
            $tieneCalificaciones = $evaluacion->calificaciones()->exists();
            
            if ($tieneCalificaciones) {
                return back()->withErrors([
                    'error' => '⚠️ No se puede eliminar esta evaluación porque ya tiene calificaciones registradas. Primero debe eliminar las calificaciones.'
                ]);
            }

            DB::beginTransaction();
            
            $evaluacion->delete();
            
            DB::commit();

            return redirect()
                ->route('admin.evaluaciones.index')
                ->with('success', '✅ Evaluación eliminada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al eliminar evaluación: ' . $e->getMessage());
            
            return back()->withErrors([
                'error' => '❌ Error al eliminar la evaluación: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Crear tipos de evaluación básicos si no existen
     */
    private function crearTiposEvaluacionBasicos()
    {
        $tipos = [
            ['nombre' => 'Examen Parcial', 'descripcion' => 'Evaluación escrita del periodo', 'activo' => true],
            ['nombre' => 'Examen Final', 'descripcion' => 'Evaluación final del curso', 'activo' => true],
            ['nombre' => 'Tarea', 'descripcion' => 'Trabajo asignado para casa', 'activo' => true],
            ['nombre' => 'Proyecto', 'descripcion' => 'Proyecto de investigación o desarrollo', 'activo' => true],
            ['nombre' => 'Quiz', 'descripcion' => 'Evaluación rápida de conocimientos', 'activo' => true],
            ['nombre' => 'Participación', 'descripcion' => 'Evaluación de participación en clase', 'activo' => true],
            ['nombre' => 'Trabajo en Equipo', 'descripcion' => 'Evaluación de trabajo colaborativo', 'activo' => true],
            ['nombre' => 'Exposición', 'descripcion' => 'Presentación oral de tema', 'activo' => true],
        ];

        foreach ($tipos as $tipo) {
            TipoEvaluacion::firstOrCreate(
                ['nombre' => $tipo['nombre']],
                $tipo
            );
        }
    }

    /**
     * Ver evaluaciones de una sección específica
     */
    public function porSeccion($seccionId)
    {
        $seccion = Seccion::with(['curso', 'profesor', 'periodo'])
            ->findOrFail($seccionId);

        $evaluaciones = Evaluacion::with('tipoEvaluacion')
            ->where('seccion_id', $seccionId)
            ->orderBy('fecha_evaluacion', 'desc')
            ->get();

        $porcentajeTotal = $evaluaciones->sum('porcentaje');
        $porcentajeRestante = 100 - $porcentajeTotal;

        return view('admin.evaluaciones.por-seccion', compact('seccion', 'evaluaciones', 'porcentajeTotal', 'porcentajeRestante'));
    }
}