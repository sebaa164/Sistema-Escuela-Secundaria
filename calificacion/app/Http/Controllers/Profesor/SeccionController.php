<?php

namespace App\Http\Controllers\Profesor;

use App\Http\Controllers\Controller;
use App\Models\Seccion;
use App\Models\Inscripcion;
use App\Models\PeriodoAcademico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SeccionController extends Controller
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
     * Listar secciones del profesor
     */
    public function index(Request $request)
    {
        $profesor = Auth::user();
        $periodoId = $request->get('periodo_id');
        
        $secciones = Seccion::with(['curso', 'periodo', 'inscripciones' => function($query) {
                $query->where('estado', 'inscrito');
            }])
            ->where('profesor_id', $profesor->id)
            ->when($periodoId, function($query) use ($periodoId) {
                return $query->where('periodo_academico_id', $periodoId);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $periodos = PeriodoAcademico::orderBy('fecha_inicio', 'desc')->get();

        return view('profesor.secciones.index', compact('secciones', 'periodos', 'periodoId'));
    }

    /**
     * Mostrar detalles de una sección específica
     */
    public function show($id)
    {
        $seccion = Seccion::with([
            'curso', 
            'periodo', 
            'inscripciones.estudiante',
            'evaluaciones' => function($query) {
                $query->orderBy('fecha_evaluacion', 'desc');
            }
        ])
        ->where('profesor_id', Auth::id())
        ->findOrFail($id);

        $estadisticas = [
            'total_estudiantes' => $seccion->inscripciones->where('estado', 'inscrito')->count(),
            'total_evaluaciones' => $seccion->evaluaciones->count(),
            'evaluaciones_activas' => $seccion->evaluaciones->where('estado', 'activa')->count(),
            'promedio_general' => $this->calcularPromedioGeneral($seccion)
        ];

        return view('profesor.secciones.show', compact('seccion', 'estadisticas'));
    }

    /**
     * Lista de estudiantes de una sección
     */
    public function estudiantes($id)
    {
        $seccion = Seccion::with([
            'curso', 
            'inscripciones' => function($query) {
                $query->with('estudiante')
                     ->where('estado', 'inscrito')
                     ->orderBy('created_at', 'asc');
            }
        ])
        ->where('profesor_id', Auth::id())
        ->findOrFail($id);

        $estudiantes = $seccion->inscripciones->map(function($inscripcion) {
            return [
                'id' => $inscripcion->estudiante->id,
                'nombre_completo' => $inscripcion->estudiante->nombre_completo,
                'email' => $inscripcion->estudiante->email,
                'fecha_inscripcion' => $inscripcion->fecha_inscripcion,
                'nota_final' => $inscripcion->nota_final,
                'estado_nota' => $inscripcion->estado_nota,
                'inscripcion_id' => $inscripcion->id
            ];
        });

        return view('profesor.secciones.estudiantes', compact('seccion', 'estudiantes'));
    }

    /**
     * Actualizar información básica de la sección
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'aula' => 'nullable|string|max:50',
            'horario' => 'nullable|array',
            'cupo_maximo' => 'integer|min:1|max:100'
        ]);

        $seccion = Seccion::where('profesor_id', Auth::id())->findOrFail($id);

        $seccion->update([
            'aula' => $request->aula,
            'horario' => $request->horario,
            'cupo_maximo' => $request->cupo_maximo
        ]);

        return redirect()->back()->with('success', 'Sección actualizada correctamente.');
    }

    /**
     * Exportar lista de estudiantes en PDF
     */
    public function exportarEstudiantesPDF($id)
    {
        $seccion = Seccion::with([
            'curso',
            'inscripciones.estudiante.tutor'
        ])
        ->where('profesor_id', Auth::id())
        ->findOrFail($id);

        $estudiantes = $seccion->inscripciones->where('estado', 'inscrito');

        $pdf = \PDF::loadView('profesor.secciones.pdf-estudiantes', compact('seccion', 'estudiantes'));
        return $pdf->download('estudiantes_' . $seccion->codigo_seccion . '.pdf');
    }

    /**
     * Calcular promedio general de la sección
     */
    private function calcularPromedioGeneral($seccion)
    {
        $inscripciones = $seccion->inscripciones->where('estado', 'inscrito');
        $notasFinales = $inscripciones->whereNotNull('nota_final')->pluck('nota_final');
        
        return $notasFinales->count() > 0 ? round($notasFinales->avg(), 2) : null;
    }

    /**
     * Buscar estudiantes para agregar a la sección
     */
    public function buscarEstudiantes(Request $request, $id)
    {
        $termino = $request->get('q');
        $seccion = Seccion::where('profesor_id', Auth::id())->findOrFail($id);
        
        // IDs de estudiantes ya inscritos
        $estudiantesInscritos = $seccion->inscripciones->pluck('estudiante_id');

        $estudiantes = \App\Models\Usuario::estudiantes()
            ->activos()
            ->where(function($query) use ($termino) {
                $query->where('nombre', 'like', "%{$termino}%")
                      ->orWhere('apellido', 'like', "%{$termino}%")
                      ->orWhere('email', 'like', "%{$termino}%");
            })
            ->whereNotIn('id', $estudiantesInscritos)
            ->limit(10)
            ->get(['id', 'nombre', 'apellido', 'email']);

        return response()->json($estudiantes);
    }

    /**
     * Agregar estudiante a la sección
     */
    public function agregarEstudiante(Request $request, $id)
    {
        $request->validate([
            'estudiante_id' => 'required|exists:usuarios,id'
        ]);

        $seccion = Seccion::where('profesor_id', Auth::id())->findOrFail($id);

        // Verificar cupo
        if ($seccion->estudiantes_inscritos >= $seccion->cupo_maximo) {
            return back()->with('error', 'La sección ha alcanzado su cupo máximo.');
        }

        // Verificar que no esté ya inscrito
        $existeInscripcion = Inscripcion::where('estudiante_id', $request->estudiante_id)
            ->where('seccion_id', $id)
            ->exists();

        if ($existeInscripcion) {
            return back()->with('error', 'El estudiante ya está inscrito en esta sección.');
        }

        Inscripcion::create([
            'estudiante_id' => $request->estudiante_id,
            'seccion_id' => $id,
            'estado' => 'inscrito'
        ]);

        return back()->with('success', 'Estudiante agregado correctamente.');
    }

    /**
     * Remover estudiante de la sección
     */
    public function removerEstudiante($seccionId, $estudianteId)
    {
        $seccion = Seccion::where('profesor_id', Auth::id())->findOrFail($seccionId);
        
        $inscripcion = Inscripcion::where('seccion_id', $seccionId)
            ->where('estudiante_id', $estudianteId)
            ->firstOrFail();

        $inscripcion->update(['estado' => 'retirado']);

        return back()->with('success', 'Estudiante removido de la sección.');
    }
}