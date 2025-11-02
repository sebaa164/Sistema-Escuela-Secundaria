<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seccion;
use App\Models\Curso;
use App\Models\Usuario;
use App\Models\PeriodoAcademico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SeccionController extends Controller
{
    public function index(Request $request)
    {
        $query = Seccion::with(['curso', 'profesor', 'periodo']);
        
        if ($request->filled('curso_id')) {
            $query->where('curso_id', $request->curso_id);
        }
        
        if ($request->filled('profesor_id')) {
            $query->where('profesor_id', $request->profesor_id);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('modalidad')) {
            $query->where('modalidad', $request->modalidad);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('codigo_seccion', 'like', "%{$search}%")
                  ->orWhereHas('curso', function($subq) use ($search) {
                      $subq->where('nombre', 'like', "%{$search}%")
                           ->orWhere('codigo_curso', 'like', "%{$search}%");
                  });
            });
        }
        
        $secciones = $query->withCount('inscripciones')->paginate(15);
        
        // Calcular estadísticas y cupos disponibles para cada sección
        foreach ($secciones as $seccion) {
            $seccion->estudiantes_inscritos = $seccion->inscripciones_count;
            $seccion->cupos_disponibles = $seccion->cupo_maximo - $seccion->estudiantes_inscritos;
        }
        
        // Obtener cursos y profesores para los filtros
        $cursos = Curso::where('estado', 'activo')
                      ->orderBy('nombre')
                      ->get(['id', 'nombre', 'codigo_curso']);
        $profesores = Usuario::where('tipo_usuario', 'profesor')
                            ->where('estado', 'activo')
                            ->orderBy('nombre')
                            ->get(['id', 'nombre', 'apellido']);
        
        return view('admin.secciones.index', compact('secciones', 'cursos', 'profesores'));
    }
    
    public function create()
    {
        $cursos = Curso::where('estado', 'activo')
                      ->orderBy('nombre')
                      ->get(['id', 'nombre', 'codigo_curso']);
        $profesores = Usuario::where('tipo_usuario', 'profesor')
                            ->where('estado', 'activo')
                            ->orderBy('nombre')
                            ->get(['id', 'nombre', 'apellido']);
        $periodos = PeriodoAcademico::where('estado', 'activo')
                                   ->orderBy('fecha_inicio', 'desc')
                                   ->get(['id', 'nombre']);
        
        return view('admin.secciones.create', compact('cursos', 'profesores', 'periodos'));
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codigo_seccion' => 'required|string|max:100|unique:secciones',
            'curso_id' => 'required|exists:cursos,id',
            'profesor_id' => 'required|exists:usuarios,id',
            'periodo_id' => 'required|exists:periodos_academicos,id',
            'cupo_maximo' => 'required|integer|min:1|max:100',
            'horario' => 'nullable|string|max:1000',
            'modalidad' => 'required|in:presencial,virtual',  // ✅ Solo presencial y virtual
        ], [
            'codigo_seccion.required' => 'El código de sección es obligatorio.',
            'codigo_seccion.max' => 'El código de sección no puede exceder 100 caracteres.',
            'codigo_seccion.unique' => 'Este código de sección ya existe.',
            'curso_id.required' => 'Debe seleccionar un curso.',
            'curso_id.exists' => 'El curso seleccionado no existe.',
            'profesor_id.required' => 'Debe seleccionar un profesor.',
            'profesor_id.exists' => 'El profesor seleccionado no existe.',
            'periodo_id.required' => 'Debe seleccionar un período académico.',
            'periodo_id.exists' => 'El período académico seleccionado no existe.',
            'cupo_maximo.required' => 'El cupo máximo es obligatorio.',
            'cupo_maximo.integer' => 'El cupo máximo debe ser un número entero.',
            'cupo_maximo.min' => 'El cupo máximo debe ser al menos 1.',
            'cupo_maximo.max' => 'El cupo máximo no puede ser mayor a 100.',
            'horario.max' => 'El horario no puede exceder 1000 caracteres.',
            'modalidad.required' => 'Debe seleccionar una modalidad.',
            'modalidad.in' => 'La modalidad debe ser presencial o virtual.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            Seccion::create([
                'codigo_seccion' => strtoupper(trim($request->codigo_seccion)),
                'curso_id' => $request->curso_id,
                'profesor_id' => $request->profesor_id,
                'periodo_academico_id' => $request->periodo_id,
                'cupo_maximo' => $request->cupo_maximo,
                'horario' => $request->horario,
                'modalidad' => $request->modalidad,
                'estado' => 'activo',
            ]);

            return redirect()->route('admin.secciones.index')
                            ->with('success', 'Sección creada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error al crear la sección: ' . $e->getMessage());
        }
    }
    
    public function show($id)
    {
        $seccion = Seccion::with(['curso', 'profesor', 'periodo', 'inscripciones.estudiante'])
                         ->findOrFail($id);
        
        // Calcular estadísticas
        $seccion->estudiantes_inscritos = $seccion->inscripciones()->where('estado', 'inscrito')->count();
        $seccion->cupos_disponibles = $seccion->cupo_maximo - $seccion->estudiantes_inscritos;
        
        return view('admin.secciones.show', compact('seccion'));
    }
    
    public function edit($id)
    {
        $seccion = Seccion::findOrFail($id);
        $cursos = Curso::where('estado', 'activo')
                      ->orderBy('nombre')
                      ->get(['id', 'nombre', 'codigo_curso']);
        $profesores = Usuario::where('tipo_usuario', 'profesor')
                            ->where('estado', 'activo')
                            ->orderBy('nombre')
                            ->get(['id', 'nombre', 'apellido']);
        $periodos = PeriodoAcademico::orderBy('fecha_inicio', 'desc')
                                   ->get(['id', 'nombre']);
        
        return view('admin.secciones.edit', compact('seccion', 'cursos', 'profesores', 'periodos'));
    }
    
    public function update(Request $request, $id)
    {
        $seccion = Seccion::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'codigo_seccion' => 'required|string|max:100|unique:secciones,codigo_seccion,'.$seccion->id,
            'curso_id' => 'required|exists:cursos,id',
            'profesor_id' => 'required|exists:usuarios,id',
            'cupo_maximo' => 'required|integer|min:1',
            'horario' => 'nullable|string|max:1000',
            'modalidad' => 'required|in:presencial,virtual',  // ✅ Solo presencial y virtual
            'estado' => 'required|in:activo,inactivo,finalizado',
        ], [
            'codigo_seccion.required' => 'El código de sección es obligatorio.',
            'codigo_seccion.max' => 'El código de sección no puede exceder 100 caracteres.',
            'codigo_seccion.unique' => 'Este código de sección ya existe.',
            'curso_id.required' => 'Debe seleccionar un curso.',
            'curso_id.exists' => 'El curso seleccionado no existe.',
            'profesor_id.required' => 'Debe seleccionar un profesor.',
            'profesor_id.exists' => 'El profesor seleccionado no existe.',
            'cupo_maximo.required' => 'El cupo máximo es obligatorio.',
            'cupo_maximo.integer' => 'El cupo máximo debe ser un número entero.',
            'cupo_maximo.min' => 'El cupo máximo debe ser al menos 1.',
            'horario.max' => 'El horario no puede exceder 1000 caracteres.',
            'modalidad.required' => 'Debe seleccionar una modalidad.',
            'modalidad.in' => 'La modalidad debe ser presencial o virtual.',
            'estado.required' => 'Debe seleccionar un estado.',
            'estado.in' => 'El estado seleccionado no es válido.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Validar que el cupo máximo no sea menor que los estudiantes inscritos
        $estudiantesInscritos = $seccion->inscripciones()->where('estado', 'inscrito')->count();
        if ($request->cupo_maximo < $estudiantesInscritos) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', "No puede reducir el cupo a {$request->cupo_maximo} porque hay {$estudiantesInscritos} estudiantes inscritos.");
        }

        try {
            $seccion->update([
                'codigo_seccion' => strtoupper(trim($request->codigo_seccion)),
                'curso_id' => $request->curso_id,
                'profesor_id' => $request->profesor_id,
                'cupo_maximo' => $request->cupo_maximo,
                'horario' => $request->horario,
                'modalidad' => $request->modalidad,
                'estado' => $request->estado,
            ]);

            return redirect()->route('admin.secciones.index')
                            ->with('success', 'Sección actualizada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error al actualizar la sección: ' . $e->getMessage());
        }
    }
    
    public function destroy($id)
    {
        try {
            $seccion = Seccion::findOrFail($id);
            
            // Verificar si hay estudiantes inscritos
            $estudiantesInscritos = $seccion->inscripciones()
                                           ->where('estado', 'inscrito')
                                           ->count();
            
            if ($estudiantesInscritos > 0) {
                return redirect()->back()
                               ->with('error', "No se puede eliminar la sección porque tiene {$estudiantesInscritos} estudiantes inscritos.");
            }
            
            $seccion->delete();
            
            return redirect()->route('admin.secciones.index')
                            ->with('success', 'Sección eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Error al eliminar la sección: ' . $e->getMessage());
        }
    }
}