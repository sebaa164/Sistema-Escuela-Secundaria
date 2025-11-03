<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inscripcion;
use App\Models\Seccion;
use App\Models\Usuario;
use App\Models\PeriodoAcademico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InscripcionController extends Controller
{
    /**
     * Display a listing of inscriptions
     */
    public function index(Request $request)
    {
        $query = Inscripcion::with(['estudiante'])
            ->orderBy('fecha_inscripcion', 'desc');

        // BÚSQUEDA MEJORADA - busca en múltiples campos simultáneamente
if ($request->filled('search')) {
    $searchTerm = '%' . $request->search . '%';
    
    $query->where(function($q) use ($searchTerm) {
        // Buscar en el estudiante (nombre, apellido, email)
        $q->whereHas('estudiante', function($estudiante) use ($searchTerm) {
            $estudiante->where('nombre', 'like', $searchTerm)
                ->orWhere('apellido', 'like', $searchTerm)
                ->orWhere('email', 'like', $searchTerm)
                ->orWhereRaw("CONCAT(nombre, ' ', apellido) LIKE ?", [$searchTerm]);
        })
        // Buscar en sección (código de sección)
        ->orWhereHas('seccion', function($seccion) use ($searchTerm) {
            $seccion->where('codigo_seccion', 'like', $searchTerm);
        })
        // Buscar en curso (nombre y código_curso)
        ->orWhereHas('seccion.curso', function($curso) use ($searchTerm) {
            $curso->where('nombre', 'like', $searchTerm)
                  ->orWhere('codigo_curso', 'like', $searchTerm);
        })
        // Buscar por ID de inscripción (#123)
        ->orWhere('inscripciones.id', 'like', $searchTerm);
    });
}

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por período - verificar que seccion_id no sea null
        if ($request->filled('periodo_id')) {
            $query->whereNotNull('seccion_id')
                ->whereHas('seccion', function($q) use ($request) {
                    $q->where('periodo_academico_id', $request->periodo_id);
                });
        }

        // Ordenamiento
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'oldest':
                    $query->orderBy('fecha_inscripcion', 'asc');
                    break;
                case 'student_name':
                    $query->join('usuarios', 'inscripciones.estudiante_id', '=', 'usuarios.id')
                        ->orderBy('usuarios.apellido')
                        ->orderBy('usuarios.nombre')
                        ->select('inscripciones.*');
                    break;
                case 'recent':
                default:
                    $query->orderBy('fecha_inscripcion', 'desc');
                    break;
            }
        }

        // Obtener inscripciones con paginación
        $inscripciones = $query->paginate(15)->withQueryString();

        // Cargar secciones de forma segura después de la paginación
        $inscripciones->getCollection()->transform(function ($inscripcion) {
            if ($inscripcion->seccion_id) {
                try {
                    $seccion = Seccion::with(['curso', 'periodo'])->find($inscripcion->seccion_id);
                    if ($seccion) {
                        $inscripcion->setRelation('seccion', $seccion);
                    }
                } catch (\Exception $e) {
                    // Si falla, la relación quedará como null
                    Log::warning("No se pudo cargar sección {$inscripcion->seccion_id} para inscripción {$inscripcion->id}");
                }
            }
            return $inscripcion;
        });
        
        $periodos = PeriodoAcademico::orderBy('fecha_inicio', 'desc')->get();

        // Estadísticas
        $stats = [
            'total' => Inscripcion::count(),
            'inscritos' => Inscripcion::where('estado', 'inscrito')->count(),
            'completados' => Inscripcion::where('estado', 'completado')->count(),
            'retirados' => Inscripcion::where('estado', 'retirado')->count(),
        ];

        return view('admin.inscripciones.index', compact('inscripciones', 'periodos', 'stats'));
    }

    /**
     * Show the form for creating a new inscription
     */
    public function create()
    {
        $estudiantes = Usuario::where('tipo_usuario', 'estudiante')
            ->where('estado', 'activo')
            ->orderBy('apellido')
            ->orderBy('nombre')
            ->get();

        $periodoActual = PeriodoAcademico::where('estado', 'activo')->first();
        
        $secciones = Seccion::with(['curso', 'profesor', 'periodo'])
            ->where('estado', 'activo')
            ->when($periodoActual, function($q) use ($periodoActual) {
                return $q->where('periodo_academico_id', $periodoActual->id);
            })
            ->get();

        if ($secciones->isEmpty()) {
            session()->flash('warning', 'No hay secciones activas disponibles en el período actual.');
        }

        return view('admin.inscripciones.create', compact('estudiantes', 'secciones'));
    }

    /**
     * Store a newly created inscription
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'estudiante_id' => 'required|exists:usuarios,id',
            'seccion_id' => 'required|exists:secciones,id',
            'fecha_inscripcion' => 'nullable|date',
            'estado' => 'nullable|in:inscrito,retirado,completado'
        ], [
            'estudiante_id.required' => 'Debe seleccionar un estudiante',
            'estudiante_id.exists' => 'El estudiante seleccionado no existe',
            'seccion_id.required' => 'Debe seleccionar una sección',
            'seccion_id.exists' => 'La sección seleccionada no existe'
        ]);

        // Verificar que sea un estudiante
        $estudiante = Usuario::findOrFail($validated['estudiante_id']);
        if ($estudiante->tipo_usuario !== 'estudiante') {
            return back()
                ->withErrors(['estudiante_id' => 'El usuario seleccionado no es un estudiante'])
                ->withInput();
        }

        // Verificar cupo disponible
        $seccion = Seccion::findOrFail($validated['seccion_id']);
        $inscritosActuales = Inscripcion::where('seccion_id', $seccion->id)
            ->where('estado', 'inscrito')
            ->count();

        if ($inscritosActuales >= $seccion->cupo_maximo) {
            return back()
                ->withErrors(['seccion_id' => 'La sección ha alcanzado su cupo máximo'])
                ->withInput();
        }

        // Verificar inscripción duplicada
        $inscripcionExistente = Inscripcion::where('estudiante_id', $validated['estudiante_id'])
            ->where('seccion_id', $validated['seccion_id'])
            ->first();

        if ($inscripcionExistente) {
            return back()
                ->withErrors(['seccion_id' => 'El estudiante ya está inscrito en esta sección'])
                ->withInput();
        }

        try {
            $inscripcion = Inscripcion::create([
                'estudiante_id' => $validated['estudiante_id'],
                'seccion_id' => $validated['seccion_id'],
                'fecha_inscripcion' => $validated['fecha_inscripcion'] ?? now(),
                'estado' => $validated['estado'] ?? 'inscrito'
            ]);

            return redirect()
                ->route('admin.inscripciones.show', $inscripcion)
                ->with('success', 'Inscripción creada exitosamente');
        } catch (\Exception $e) {
            Log::error('Error al crear inscripción: ' . $e->getMessage());
            return back()
                ->withErrors(['error' => 'Error al crear la inscripción: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(Inscripcion $inscripcion)
{
    try {
        // ✅ Cargar estudiante siempre
        $inscripcion->load('estudiante');
        
        // ✅ Intentar cargar sección con todas sus relaciones
        $tienSeccionValida = false;
        
        if ($inscripcion->seccion_id) {
            try {
                $seccion = Seccion::with([
                    'curso',
                    'profesor',
                    'periodo',
                    'horarios',
                    'evaluaciones' => function($query) use ($inscripcion) {
                        $query->with(['calificaciones' => function($q) use ($inscripcion) {
                            $q->where('estudiante_id', $inscripcion->estudiante_id)
                              ->where('estado', 'calificada');
                        }]);
                    }
                ])->find($inscripcion->seccion_id);
                
                if ($seccion) {
                    $inscripcion->setRelation('seccion', $seccion);
                    $tienSeccionValida = true;
                }
            } catch (\Exception $e) {
                Log::warning("Error cargando sección {$inscripcion->seccion_id}: " . $e->getMessage());
            }
        }
        
        // ✅ IMPORTANTE: Cargar asistencias ANTES de calcular estadísticas
        $inscripcion->load('asistencias');
        
        // ✅ Calcular estadísticas usando los métodos del modelo
        $estadisticas = $inscripcion->obtenerEstadisticas();
        
        // 🔍 Debug temporal - puedes eliminarlo después
        Log::info('Estadísticas de inscripción:', [
            'inscripcion_id' => $inscripcion->id,
            'tiene_seccion' => $tienSeccionValida,
            'total_asistencias' => $inscripcion->asistencias->count(),
            'fecha_retiro' => $inscripcion->fecha_retiro,
            'estadisticas' => $estadisticas
        ]);
        
        return view('admin.inscripciones.show', compact(
            'inscripcion',
            'tienSeccionValida',
            'estadisticas'
        ));

    } catch (\Exception $e) {
        Log::error("Error en show inscripción {$inscripcion->id}: " . $e->getMessage());
        Log::error($e->getTraceAsString());
        
        return redirect()
            ->route('admin.inscripciones.index')
            ->withErrors(['error' => 'Error al cargar la inscripción: ' . $e->getMessage()]);
    }
}

    /**
     * Show the form for editing the specified inscription
     */
    public function edit(Inscripcion $inscripcion)
    {
        // Cargar estudiante siempre primero
        $inscripcion->load('estudiante');
    
        // Verificar que el estudiante existe
        if (!$inscripcion->estudiante) {
            return redirect()
                ->route('admin.inscripciones.index')
                ->withErrors(['error' => 'Esta inscripción no tiene un estudiante válido asociado']);
        }

        // Intentar cargar sección si existe
        if ($inscripcion->seccion_id) {
            try {
                $seccion = Seccion::with('curso')->find($inscripcion->seccion_id);
                if ($seccion) {
                    $inscripcion->setRelation('seccion', $seccion);
                }
            } catch (\Exception $e) {
                // Sección no existe, continuamos sin ella
                Log::warning("Sección {$inscripcion->seccion_id} no encontrada para inscripción {$inscripcion->id}");
            } 
        }

        // Cargar todas las secciones activas disponibles
        $secciones = Seccion::with(['curso', 'profesor', 'periodo'])
            ->where('estado', 'activo')
            ->get();

        return view('admin.inscripciones.edit', compact('inscripcion', 'secciones'));
    }

    /**
     * Update the specified inscription
     */
    public function update(Request $request, Inscripcion $inscripcion)
{
    // Cargar estudiante primero
    $inscripcion->load('estudiante');

    // Verificar que el estudiante existe
    if (!$inscripcion->estudiante) {
        return redirect()
            ->route('admin.inscripciones.index')
            ->withErrors(['error' => 'Esta inscripción no tiene un estudiante válido asociado']);
    }

    // Validación
    $validated = $request->validate([
        'seccion_id' => 'required|exists:secciones,id',
        'estado' => 'required|in:inscrito,retirado,completado',
        'nota_final' => 'nullable|numeric|min:0|max:100',
        'fecha_retiro' => 'nullable|date',
    ]);

    // Si está cambiando de sección, verificar que no exista duplicado
    if ($inscripcion->seccion_id != $validated['seccion_id']) {
        $duplicado = Inscripcion::where('estudiante_id', $inscripcion->estudiante_id)
            ->where('seccion_id', $validated['seccion_id'])
            ->where('id', '!=', $inscripcion->id)
            ->first();

        if ($duplicado) {
            return back()
                ->withErrors(['seccion_id' => 'El estudiante ya tiene otra inscripción en esta sección'])
                ->withInput();
        }

        // Verificar cupo disponible en la nueva sección
        $nuevaSeccion = Seccion::findOrFail($validated['seccion_id']);
        $inscritosActuales = Inscripcion::where('seccion_id', $nuevaSeccion->id)
            ->where('estado', 'inscrito')
            ->where('id', '!=', $inscripcion->id)
            ->count();

        if ($inscritosActuales >= $nuevaSeccion->cupo_maximo) {
            return back()
                ->withErrors(['seccion_id' => 'La sección no tiene cupos disponibles'])
                ->withInput();
        }
    }

    // 🔥 CORRECCIÓN: Manejo correcto de fecha_retiro
    if ($validated['estado'] === 'retirado') {
        // Si el estado es retirado y no hay fecha, usar la fecha actual
        if (empty($validated['fecha_retiro'])) {
            $validated['fecha_retiro'] = now();
        }
    } else {
        // Si NO es retirado, limpiar la fecha de retiro
        $validated['fecha_retiro'] = null;
    }

    try {
        $inscripcion->update($validated);

        return redirect()
            ->route('admin.inscripciones.show', $inscripcion)
            ->with('success', 'Inscripción actualizada exitosamente');
    } catch (\Exception $e) {
        Log::error('Error al actualizar inscripción: ' . $e->getMessage());
        return back()
            ->withErrors(['error' => 'Error al actualizar: ' . $e->getMessage()])
            ->withInput();
    }
}

    /**
     * Remove the specified inscription
     */
    public function destroy(Inscripcion $inscripcion)
    {
        try {
            $estudianteNombre = $inscripcion->estudiante->nombre_completo ?? 'Estudiante';
            
            $cursoNombre = 'Curso no disponible';
            if ($inscripcion->seccion_id) {
                $seccion = Seccion::find($inscripcion->seccion_id);
                if ($seccion && $seccion->curso) {
                    $cursoNombre = $seccion->curso->nombre;
                }
            }
            
            $inscripcion->delete();

            return redirect()
                ->route('admin.inscripciones.index')
                ->with('success', "Inscripción de {$estudianteNombre} en {$cursoNombre} eliminada exitosamente");
        } catch (\Exception $e) {
            Log::error('Error al eliminar inscripción: ' . $e->getMessage());
            return back()
                ->withErrors(['error' => 'Error al eliminar la inscripción: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for mass inscription
     */
    public function createMasiva()
    {
        $estudiantes = Usuario::where('tipo_usuario', 'estudiante')
            ->where('estado', 'activo')
            ->orderBy('apellido')
            ->orderBy('nombre')
            ->get();

        $periodoActual = PeriodoAcademico::where('estado', 'activo')->first();
        
        $secciones = Seccion::with(['curso', 'profesor', 'periodo'])
            ->where('estado', 'activo')
            ->when($periodoActual, function($q) use ($periodoActual) {
                return $q->where('periodo_academico_id', $periodoActual->id);
            })
            ->get();

        if ($secciones->isEmpty()) {
            session()->flash('warning', 'No hay secciones activas disponibles en el período actual.');
        }

        return view('admin.inscripciones.masiva', compact('estudiantes', 'secciones'));
    }

    /**
     * Store mass inscriptions
     */
    public function storeMasiva(Request $request)
    {
        $validated = $request->validate([
            'seccion_id' => 'required|exists:secciones,id',
            'estudiantes' => 'required|array|min:1',
            'estudiantes.*' => 'exists:usuarios,id'
        ], [
            'seccion_id.required' => 'Debe seleccionar una sección',
            'estudiantes.required' => 'Debe seleccionar al menos un estudiante',
            'estudiantes.min' => 'Debe seleccionar al menos un estudiante'
        ]);

        $seccion = Seccion::findOrFail($validated['seccion_id']);
        $inscritosActuales = Inscripcion::where('seccion_id', $seccion->id)
            ->where('estado', 'inscrito')
            ->count();

        $cupoDisponible = $seccion->cupo_maximo - $inscritosActuales;
        
        if (count($validated['estudiantes']) > $cupoDisponible) {
            return back()
                ->withErrors(['estudiantes' => "Solo hay {$cupoDisponible} cupos disponibles en esta sección"])
                ->withInput();
        }

        $inscritosExitosos = 0;
        $errores = [];

        DB::beginTransaction();
        try {
            foreach ($validated['estudiantes'] as $estudianteId) {
                $existe = Inscripcion::where('estudiante_id', $estudianteId)
                    ->where('seccion_id', $seccion->id)
                    ->exists();

                if (!$existe) {
                    Inscripcion::create([
                        'estudiante_id' => $estudianteId,
                        'seccion_id' => $seccion->id,
                        'fecha_inscripcion' => now(),
                        'estado' => 'inscrito'
                    ]);
                    $inscritosExitosos++;
                } else {
                    $estudiante = Usuario::find($estudianteId);
                    $errores[] = "{$estudiante->nombre_completo} ya estaba inscrito";
                }
            }

            DB::commit();

            $mensaje = "{$inscritosExitosos} estudiantes inscritos exitosamente";
            if (count($errores) > 0) {
                $mensaje .= ". Advertencias: " . implode(', ', $errores);
            }

            return redirect()
                ->route('admin.inscripciones.index')
                ->with('success', $mensaje);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en inscripción masiva: ' . $e->getMessage());
            return back()
                ->withErrors(['error' => 'Error en la inscripción masiva: ' . $e->getMessage()])
                ->withInput();
        }
    }
}