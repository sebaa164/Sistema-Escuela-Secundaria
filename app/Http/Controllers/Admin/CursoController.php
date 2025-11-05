<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Curso;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CursoController extends Controller
{
    public function index(Request $request)
    {
        $query = Curso::query();
        
        // Filtros
        if ($request->filled('carrera')) {
            $query->where('carrera', $request->carrera);
        }
        
        if ($request->filled('nivel')) {
            $query->where('nivel', $request->nivel);
        }
        
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('codigo_curso', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }
        
        $cursos = $query->withCount('secciones')
                       ->orderBy('codigo_curso')
                       ->paginate(15);
        
        // Para los filtros
        $carreras = Curso::distinct()->pluck('carrera')->filter()->sort();
        $niveles = Curso::distinct()->pluck('nivel')->filter()->sort();
        
        return view('admin.cursos.index', compact('cursos', 'carreras', 'niveles'));
    }
    
    public function create()
    {
        return view('admin.cursos.create');
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codigo_curso' => [
                'required',
                'string',
                'max:20',
                'unique:cursos',
                'regex:/^[A-Z0-9\-\.\_]+$/i'
            ],
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'horas_semanales' => 'required|integer|min:1|max:40',
            'carrera' => 'required|string|max:255',
            'nivel' => 'nullable|string|max:50',
            'requisitos' => 'nullable|string|max:500',
        ], [
            'codigo_curso.required' => 'El código del curso es obligatorio',
            'codigo_curso.unique' => 'Este código de curso ya existe',
            'codigo_curso.regex' => 'El código puede contener letras, números, guiones, puntos y guiones bajos (Ej: HIST-2, MAT-101, FIS.1)',
            'nombre.required' => 'El nombre del curso es obligatorio',
            'carrera.required' => 'La carrera es obligatoria',
            'horas_semanales.required' => 'Las horas semanales son obligatorias',
            'horas_semanales.min' => 'Las horas semanales deben ser al menos 1',
            'horas_semanales.max' => 'Las horas semanales no pueden ser más de 40',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        Curso::create([
            'codigo_curso' => strtoupper($request->codigo_curso),
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'horas_semanales' => $request->horas_semanales,
            'carrera' => $request->carrera,
            'nivel' => $request->nivel,
            'requisitos' => $request->requisitos,
            'estado' => 'activo',
        ]);

        return redirect()->route('admin.cursos.index')
                        ->with('success', 'Curso creado exitosamente.');
    }
    
    public function show($id)
    {
        $curso = Curso::with(['secciones.periodo', 'secciones.profesor'])->findOrFail($id);
        
        // Estadísticas del curso
        $totalSecciones = $curso->secciones->count();
        $seccionesActivas = $curso->secciones->where('estado', 'activo')->count();
        $totalEstudiantes = $curso->secciones->sum(function($seccion) {
            return $seccion->inscripciones()->where('estado', 'inscrito')->count();
        });
        
        return view('admin.cursos.show', compact('curso', 'totalSecciones', 'seccionesActivas', 'totalEstudiantes'));
    }
    
    public function edit($id)
    {
        $curso = Curso::findOrFail($id);
        return view('admin.cursos.edit', compact('curso'));
    }
    
    public function update(Request $request, $id)
    {
        $curso = Curso::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'codigo_curso' => [
                'required',
                'string',
                'max:20',
                'unique:cursos,codigo_curso,'.$curso->id,
                'regex:/^[A-Z0-9\-\.\_]+$/i'
            ],
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'horas_semanales' => 'required|integer|min:1|max:40',
            'carrera' => 'required|string|max:255',
            'nivel' => 'nullable|string|max:50',
            'requisitos' => 'nullable|string|max:500',
            'estado' => 'required|in:activo,inactivo',
        ], [
            'codigo_curso.required' => 'El código del curso es obligatorio',
            'codigo_curso.unique' => 'Este código de curso ya existe',
            'codigo_curso.regex' => 'El código puede contener letras, números, guiones, puntos y guiones bajos (Ej: HIST-2, MAT-101, FIS.1)',
            'nombre.required' => 'El nombre del curso es obligatorio',
            'carrera.required' => 'La carrera es obligatoria',
            'estado.required' => 'El estado es obligatorio',
            'horas_semanales.required' => 'Las horas semanales son obligatorias',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        $curso->update([
            'codigo_curso' => strtoupper($request->codigo_curso),
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'horas_semanales' => $request->horas_semanales,
            'carrera' => $request->carrera,
            'nivel' => $request->nivel,
            'requisitos' => $request->requisitos,
            'estado' => $request->estado,
        ]);

        return redirect()->route('admin.cursos.index')
                        ->with('success', 'Curso actualizado exitosamente.');
    }
    
    public function destroy($id)
    {
        $curso = Curso::findOrFail($id);
        
        // Verificar si tiene secciones asociadas
        if ($curso->secciones()->exists()) {
            return redirect()->back()
                           ->with('error', 'No se puede eliminar el curso porque tiene secciones asociadas.');
        }
        
        $curso->delete();
        
        return redirect()->route('admin.cursos.index')
                        ->with('success', 'Curso eliminado exitosamente.');
    }
}