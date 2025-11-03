<?php

namespace App\Http\Controllers\Profesor;

use App\Http\Controllers\Controller;
use App\Models\Evaluacion;
use App\Models\Seccion;
use App\Models\TipoEvaluacion;
use App\Models\Calificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EvaluacionController extends Controller
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
     * Listar evaluaciones del profesor
     */
    public function index(Request $request)
    {
        $seccionId = $request->get('seccion_id');
        $estado = $request->get('estado');

        $evaluaciones = Evaluacion::with(['seccion.curso', 'tipoEvaluacion'])
            ->whereHas('seccion', function($query) {
                $query->where('profesor_id', Auth::id());
            })
            ->when($seccionId, function($query) use ($seccionId) {
                return $query->where('seccion_id', $seccionId);
            })
            ->when($estado, function($query) use ($estado) {
                return $query->where('estado', $estado);
            })
            ->orderBy('fecha_evaluacion', 'desc')
            ->paginate(15);

        $secciones = Seccion::where('profesor_id', Auth::id())
            ->with('curso')
            ->activas()
            ->get();

        $tiposEvaluacion = TipoEvaluacion::all();

        return view('profesor.evaluaciones.index', compact('evaluaciones', 'secciones', 'tiposEvaluacion', 'seccionId', 'estado'));
    }

    /**
     * Mostrar formulario para crear evaluación
     */
    public function create(Request $request)
    {
        $seccionId = $request->get('seccion_id');
        
        $secciones = Seccion::where('profesor_id', Auth::id())
            ->with('curso')
            ->activas()
            ->get();

        $tiposEvaluacion = TipoEvaluacion::all();

        return view('profesor.evaluaciones.create', compact('secciones', 'tiposEvaluacion', 'seccionId'));
    }

    /**
     * Crear nueva evaluación
     */
    public function store(Request $request)
    {
        $request->validate([
            'seccion_id' => 'required|exists:secciones,id',
            'tipo_evaluacion_id' => 'required|exists:tipos_evaluacion,id',
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'fecha_evaluacion' => 'required|date',
            'fecha_limite' => 'nullable|date|after_or_equal:fecha_evaluacion',
            'nota_maxima' => 'required|numeric|min:1|max:999.99',
            'porcentaje' => 'required|numeric|min:0.01|max:100',
            'instrucciones' => 'nullable|string'
        ]);

        // Verificar que la sección pertenezca al profesor
        $seccion = Seccion::where('id', $request->seccion_id)
            ->where('profesor_id', Auth::id())
            ->firstOrFail();

        DB::beginTransaction();
        try {
            // Crear la evaluación
            $evaluacion = Evaluacion::create($request->all());

            // Crear calificaciones para todos los estudiantes inscritos
            $inscripciones = $seccion->inscripciones()->where('estado', 'inscrito')->get();
            
            foreach ($inscripciones as $inscripcion) {
                Calificacion::create([
                    'evaluacion_id' => $evaluacion->id,
                    'estudiante_id' => $inscripcion->estudiante_id,
                    'estado' => 'pendiente'
                ]);
            }

            DB::commit();
            return redirect()->route('profesor.evaluaciones.show', $evaluacion->id)
                ->with('success', 'Evaluación creada correctamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error al crear la evaluación.');
        }
    }

    /**
     * Mostrar detalles de la evaluación
     */
    public function show($id)
    {
        $evaluacion = Evaluacion::with([
            'seccion.curso',
            'tipoEvaluacion',
            'calificaciones.estudiante' => function($query) {
                $query->orderBy('apellido', 'asc');
            }
        ])
        ->whereHas('seccion', function($query) {
            $query->where('profesor_id', Auth::id());
        })
        ->findOrFail($id);

        $estadisticas = [
            'total_estudiantes' => $evaluacion->calificaciones->count(),
            'calificadas' => $evaluacion->calificaciones->whereIn('estado', ['calificada', 'revisada'])->count(),
            'pendientes' => $evaluacion->calificaciones->where('estado', 'pendiente')->count(),
            'promedio' => $evaluacion->calificaciones->whereNotNull('nota')->avg('nota'),
            'nota_mayor' => $evaluacion->calificaciones->whereNotNull('nota')->max('nota'),
            'nota_menor' => $evaluacion->calificaciones->whereNotNull('nota')->min('nota')
        ];

        return view('profesor.evaluaciones.show', compact('evaluacion', 'estadisticas'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $evaluacion = Evaluacion::whereHas('seccion', function($query) {
                $query->where('profesor_id', Auth::id());
            })
            ->findOrFail($id);

        // No permitir editar si ya está finalizada
        if ($evaluacion->estado === 'finalizada') {
            return redirect()->back()->with('error', 'No se puede editar una evaluación finalizada.');
        }

        $tiposEvaluacion = TipoEvaluacion::all();

        return view('profesor.evaluaciones.edit', compact('evaluacion', 'tiposEvaluacion'));
    }

    /**
     * Actualizar evaluación
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'tipo_evaluacion_id' => 'required|exists:tipos_evaluacion,id',
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'fecha_evaluacion' => 'required|date',
            'fecha_limite' => 'nullable|date|after_or_equal:fecha_evaluacion',
            'nota_maxima' => 'required|numeric|min:1|max:999.99',
            'porcentaje' => 'required|numeric|min:0.01|max:100',
            'instrucciones' => 'nullable|string'
        ]);

        $evaluacion = Evaluacion::whereHas('seccion', function($query) {
                $query->where('profesor_id', Auth::id());
            })
            ->findOrFail($id);

        if ($evaluacion->estado === 'finalizada') {
            return redirect()->back()->with('error', 'No se puede editar una evaluación finalizada.');
        }

        $evaluacion->update($request->all());

        return redirect()->route('profesor.evaluaciones.show', $evaluacion->id)
            ->with('success', 'Evaluación actualizada correctamente.');
    }

    /**
     * Cambiar estado de la evaluación
     */
    public function cambiarEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:programada,activa,finalizada,cancelada'
        ]);

        $evaluacion = Evaluacion::whereHas('seccion', function($query) {
                $query->where('profesor_id', Auth::id());
            })
            ->findOrFail($id);

        $evaluacion->update(['estado' => $request->estado]);

        switch($request->estado) {
            case 'activa':
                $mensaje = 'Evaluación activada correctamente.';
                break;
            case 'finalizada':
                $mensaje = 'Evaluación finalizada correctamente.';
                break;
            case 'cancelada':
                $mensaje = 'Evaluación cancelada.';
                break;
            default:
                $mensaje = 'Estado actualizado correctamente.';
        }

        return redirect()->back()->with('success', $mensaje);
    }

    /**
     * Duplicar evaluación
     */
    public function duplicar($id)
    {
        $evaluacionOriginal = Evaluacion::whereHas('seccion', function($query) {
                $query->where('profesor_id', Auth::id());
            })
            ->findOrFail($id);

        $nuevaEvaluacion = $evaluacionOriginal->replicate([
            'fecha_evaluacion',
            'fecha_limite'
        ]);
        
        $nuevaEvaluacion->nombre = $evaluacionOriginal->nombre . ' (Copia)';
        $nuevaEvaluacion->estado = 'programada';
        $nuevaEvaluacion->save();

        // Crear calificaciones para estudiantes actuales
        $inscripciones = $evaluacionOriginal->seccion->inscripciones()
            ->where('estado', 'inscrito')
            ->get();
            
        foreach ($inscripciones as $inscripcion) {
            Calificacion::create([
                'evaluacion_id' => $nuevaEvaluacion->id,
                'estudiante_id' => $inscripcion->estudiante_id,
                'estado' => 'pendiente'
            ]);
        }

        return redirect()->route('profesor.evaluaciones.edit', $nuevaEvaluacion->id)
            ->with('success', 'Evaluación duplicada. Modifica las fechas y detalles necesarios.');
    }

    /**
     * Eliminar evaluación
     */
    public function destroy($id)
    {
        $evaluacion = Evaluacion::whereHas('seccion', function($query) {
                $query->where('profesor_id', Auth::id());
            })
            ->findOrFail($id);

        if ($evaluacion->estado === 'finalizada') {
            return redirect()->back()->with('error', 'No se puede eliminar una evaluación finalizada.');
        }

        // Verificar si tiene calificaciones registradas
        $tieneCalificaciones = $evaluacion->calificaciones()
            ->whereNotNull('nota')
            ->exists();

        if ($tieneCalificaciones) {
            return redirect()->back()->with('error', 'No se puede eliminar una evaluación que ya tiene calificaciones registradas.');
        }

        DB::beginTransaction();
        try {
            // Eliminar calificaciones pendientes
            $evaluacion->calificaciones()->delete();
            
            // Eliminar evaluación
            $evaluacion->delete();

            DB::commit();
            return redirect()->route('profesor.evaluaciones.index')
                ->with('success', 'Evaluación eliminada correctamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error al eliminar la evaluación.');
        }
    }

    /**
     * Obtener estadísticas para gráficos
     */
    public function estadisticas($id)
    {
        $evaluacion = Evaluacion::with('calificaciones')
            ->whereHas('seccion', function($query) {
                $query->where('profesor_id', Auth::id());
            })
            ->findOrFail($id);

        $notas = $evaluacion->calificaciones->whereNotNull('nota')->pluck('nota');

        $distribucion = [
            'excelente' => $notas->where('>=', 90)->count(),
            'muy_bueno' => $notas->whereBetween(80, [80, 89])->count(),
            'bueno' => $notas->whereBetween(70, [70, 79])->count(),
            'regular' => $notas->whereBetween(60, [60, 69])->count(),
            'deficiente' => $notas->where('<', 60)->count()
        ];

        return response()->json([
            'distribucion' => $distribucion,
            'promedio' => $notas->count() > 0 ? round($notas->avg(), 2) : 0,
            'total_calificadas' => $notas->count(),
            'total_estudiantes' => $evaluacion->calificaciones->count()
        ]);
    }
}