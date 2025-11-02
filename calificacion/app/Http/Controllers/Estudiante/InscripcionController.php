<?php

namespace App\Http\Controllers\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\Inscripcion;
use App\Models\Seccion;
use App\Models\Curso;
use App\Models\PeriodoAcademico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InscripcionController extends Controller
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
     * Mostrar inscripciones actuales del estudiante
     */
    public function index(Request $request)
    {
        $estudiante = Auth::user();
        $periodoId = $request->get('periodo_id');

        $inscripciones = Inscripcion::with(['seccion.curso', 'seccion.profesor', 'seccion.periodo'])
            ->where('estudiante_id', $estudiante->id)
            ->when($periodoId, function($query) use ($periodoId) {
                return $query->whereHas('seccion', function($q) use ($periodoId) {
                    $q->where('periodo_academico_id', $periodoId);
                });
            })
            ->where('estado', 'inscrito')
            ->orderBy('created_at', 'desc')
            ->get();

        $periodoActual = PeriodoAcademico::vigente()->first();

        $periodos = PeriodoAcademico::orderBy('fecha_inicio', 'desc')->get();

        return view('estudiante.inscripciones.index', compact('inscripciones', 'periodos', 'periodoId', 'periodoActual'));
    }

    /**
     * Mostrar cursos disponibles para inscripción
     */
    public function disponibles(Request $request)
    {
        $estudiante = Auth::user();
        $periodoActual = PeriodoAcademico::vigente()->first();
        
        if (!$periodoActual) {
            return redirect()->back()->with('error', 'No hay período académico activo para inscripciones.');
        }

        $carrera = $request->get('carrera');
        $nivel = $request->get('nivel');

        // Obtener cursos ya inscritos por el estudiante en este período
        $cursosInscritos = Inscripcion::where('estudiante_id', $estudiante->id)
            ->whereHas('seccion', function($query) use ($periodoActual) {
                $query->where('periodo_academico_id', $periodoActual->id);
            })
            ->whereIn('estado', ['inscrito', 'completado'])
            ->with('seccion.curso')
            ->get()
            ->pluck('seccion.curso.id');

        // Obtener secciones disponibles
        $secciones = Seccion::with(['curso', 'profesor'])
            ->where('periodo_academico_id', $periodoActual->id)
            ->whereNotIn('curso_id', $cursosInscritos)
            ->whereHas('curso', function($query) use ($carrera, $nivel) {
                $query->where('estado', 'activo');
                if ($carrera) {
                    $query->where('carrera', $carrera);
                }
                if ($nivel) {
                    $query->where('nivel', $nivel);
                }
            })
            ->activas()
            ->get()
            ->filter(function($seccion) {
                return $seccion->cupos_disponibles > 0;
            });

        $carreras = Curso::distinct()->pluck('carrera')->filter();
        $niveles = Curso::distinct()->pluck('nivel')->filter();

        return view('estudiante.inscripciones.disponibles', compact('secciones', 'carreras', 'niveles', 'carrera', 'nivel'));
    }

    /**
     * Inscribirse a una sección
     */
    public function inscribirse(Request $request, $seccionId)
    {
        $request->validate([
            'confirmacion' => 'required|boolean'
        ]);

        $estudiante = Auth::user();
        $seccion = Seccion::with('curso')->findOrFail($seccionId);

        // Verificar que la sección esté activa
        if ($seccion->estado !== 'activo') {
            return redirect()->back()->with('error', 'Esta sección no está disponible para inscripción.');
        }

        // Verificar cupos disponibles
        if ($seccion->cupos_disponibles <= 0) {
            return redirect()->back()->with('error', 'No hay cupos disponibles en esta sección.');
        }

        // Verificar que no esté ya inscrito en este curso
        $inscripcionExistente = Inscripcion::where('estudiante_id', $estudiante->id)
            ->whereHas('seccion', function($query) use ($seccion) {
                $query->where('curso_id', $seccion->curso_id)
                      ->where('periodo_academico_id', $seccion->periodo_academico_id);
            })
            ->whereIn('estado', ['inscrito', 'completado'])
            ->exists();

        if ($inscripcionExistente) {
            return redirect()->back()->with('error', 'Ya estás inscrito en este curso para este período.');
        }

        DB::beginTransaction();
        try {
            Inscripcion::create([
                'estudiante_id' => $estudiante->id,
                'seccion_id' => $seccionId,
                'estado' => 'inscrito'
            ]);

            DB::commit();
            return redirect()->route('estudiante.inscripciones.index')
                ->with('success', 'Te has inscrito correctamente en ' . $seccion->curso->nombre);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Error al procesar la inscripción.');
        }
    }

    /**
     * Retirarse de una sección
     */
    public function retirarse(Request $request, $inscripcionId)
    {
        $request->validate([
            'motivo' => 'nullable|string|max:500'
        ]);

        $inscripcion = Inscripcion::where('id', $inscripcionId)
            ->where('estudiante_id', Auth::id())
            ->where('estado', 'inscrito')
            ->firstOrFail();

        $inscripcion->update([
            'estado' => 'retirado'
            // Podrías agregar un campo 'motivo_retiro' en la tabla si quieres guardarlo
        ]);

        return redirect()->back()->with('success', 'Te has retirado de la materia correctamente.');
    }

    /**
     * Ver detalles de una inscripción
     */
    public function show($inscripcionId)
    {
        $inscripcion = Inscripcion::with([
            'seccion.curso',
            'seccion.profesor',
            'seccion.periodo',
            'seccion.evaluaciones' => function($query) {
                $query->orderBy('fecha_evaluacion', 'asc');
            }
        ])
        ->where('estudiante_id', Auth::id())
        ->findOrFail($inscripcionId);

        // Obtener calificaciones del estudiante para esta sección
        $calificaciones = \App\Models\Calificacion::whereHas('evaluacion', function($query) use ($inscripcion) {
                $query->where('seccion_id', $inscripcion->seccion_id);
            })
            ->where('estudiante_id', Auth::id())
            ->with(['evaluacion.tipoEvaluacion'])
            ->get()
            ->keyBy('evaluacion_id');

        return view('estudiante.inscripciones.show', compact('inscripcion', 'calificaciones'));
    }

    /**
     * Historial académico
     */
    public function historial()
    {
        $estudiante = Auth::user();

        $inscripciones = Inscripcion::with(['seccion.curso', 'seccion.periodo'])
            ->where('estudiante_id', $estudiante->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('seccion.periodo.nombre');

        $resumen = [
            'total_materias' => $inscripciones->flatten()->count(),
            'materias_aprobadas' => $inscripciones->flatten()->where('esta_aprobado', true)->count(),
            'materias_reprobadas' => $inscripciones->flatten()->where('esta_aprobado', false)->count(),
            'creditos_aprobados' => $inscripciones->flatten()
                ->where('esta_aprobado', true)
                ->sum(function($ins) { return $ins->seccion->curso->creditos; }),
            'promedio_general' => $inscripciones->flatten()
                ->whereNotNull('nota_final')
                ->avg('nota_final')
        ];

        return view('estudiante.inscripciones.historial', compact('inscripciones', 'resumen'));
    }

    /**
     * Buscar secciones disponibles (AJAX)
     */
    public function buscarSecciones(Request $request)
    {
        $termino = $request->get('q');
        $periodoActual = PeriodoAcademico::vigente()->first();
        
        if (!$periodoActual) {
            return response()->json([]);
        }

        $estudiante = Auth::user();
        
        // Cursos ya inscritos
        $cursosInscritos = Inscripcion::where('estudiante_id', $estudiante->id)
            ->whereHas('seccion', function($query) use ($periodoActual) {
                $query->where('periodo_academico_id', $periodoActual->id);
            })
            ->whereIn('estado', ['inscrito', 'completado'])
            ->with('seccion.curso')
            ->get()
            ->pluck('seccion.curso.id');

        $secciones = Seccion::with(['curso', 'profesor'])
            ->where('periodo_academico_id', $periodoActual->id)
            ->whereNotIn('curso_id', $cursosInscritos)
            ->whereHas('curso', function($query) use ($termino) {
                $query->where('estado', 'activo')
                      ->where(function($q) use ($termino) {
                          $q->where('nombre', 'like', "%{$termino}%")
                            ->orWhere('codigo_curso', 'like', "%{$termino}%");
                      });
            })
            ->activas()
            ->limit(10)
            ->get()
            ->filter(function($seccion) {
                return $seccion->cupos_disponibles > 0;
            })
            ->map(function($seccion) {
                return [
                    'id' => $seccion->id,
                    'curso_nombre' => $seccion->curso->nombre,
                    'codigo_curso' => $seccion->curso->codigo_curso,
                    'seccion' => $seccion->codigo_seccion,
                    'profesor' => $seccion->profesor->nombre_completo,
                    'creditos' => $seccion->curso->creditos,
                    'cupos_disponibles' => $seccion->cupos_disponibles,
                    'horario' => $seccion->horario_formateado
                ];
            });

        return response()->json($secciones);
    }
}