<?php

namespace App\Http\Controllers;

use App\Models\Seccion;
use App\Models\Curso;
use App\Models\PeriodoAcademico;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SeccionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Seccion::with(['curso', 'periodo', 'profesor']);

        // Filtros
        if ($request->filled('curso_id')) {
            $query->where('curso_id', $request->curso_id);
        }

        if ($request->filled('periodo_id')) {
            $query->where('periodo_id', $request->periodo_id);
        }

        if ($request->filled('profesor_id')) {
            $query->where('profesor_id', $request->profesor_id);
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Si es profesor, solo mostrar sus secciones
        if (Auth::user()->es_profesor) {
            $query->delProfesor(Auth::id());
        }

        $secciones = $query->orderBy('created_at', 'desc')->paginate(15);

        $cursos = Curso::activos()->orderBy('nombre')->get();
        $periodos = PeriodoAcademico::orderBy('fecha_inicio', 'desc')->get();
        $profesores = Usuario::profesores()->activos()->orderBy('nombre')->get();

        return view('secciones.index', compact('secciones', 'cursos', 'periodos', 'profesores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $cursos = Curso::activos()->orderBy('nombre')->get();
        $periodos = PeriodoAcademico::activos()->orderBy('fecha_inicio', 'desc')->get();
        $profesores = Usuario::profesores()->activos()->orderBy('nombre')->get();

        return view('secciones.create', compact('cursos', 'periodos', 'profesores'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'curso_id' => 'required|exists:cursos,id',
            'periodo_id' => 'required|exists:periodos_academicos,id',
            'profesor_id' => 'required|exists:usuarios,id',
            'codigo_seccion' => 'required|string|max:10',
            'cupo_maximo' => 'required|integer|min:1|max:100',
            'horario' => 'required|array',
            'aula' => 'required|string|max:50',
        ]);

        // Verificar que no exista otra sección con el mismo código en el mismo periodo
        $existeSeccion = Seccion::where('codigo_seccion', $request->codigo_seccion)
            ->where('periodo_id', $request->periodo_id)
            ->where('curso_id', $request->curso_id)
            ->exists();

        if ($existeSeccion) {
            return back()->withErrors([
                'codigo_seccion' => 'Ya existe una sección con este código para este curso en el período seleccionado.'
            ])->withInput();
        }

        try {
            DB::beginTransaction();

            $seccion = Seccion::create([
                'curso_id' => $request->curso_id,
                'periodo_id' => $request->periodo_id,
                'profesor_id' => $request->profesor_id,
                'codigo_seccion' => $request->codigo_seccion,
                'cupo_maximo' => $request->cupo_maximo,
                'horario' => $request->horario,
                'aula' => $request->aula,
                'estado' => 'activo',
            ]);

            DB::commit();

            return redirect()->route('secciones.show', $seccion)
                           ->with('success', 'Sección creada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al crear la sección: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Seccion $seccion)
    {
        $seccion->load(['curso', 'periodo', 'profesor', 'inscripciones.estudiante', 'evaluaciones']);

        $estadisticas = [
            'total_estudiantes' => $seccion->estudiantes_inscritos,
            'cupos_disponibles' => $seccion->cupos_disponibles,
            'total_evaluaciones' => $seccion->evaluaciones->count(),
            'evaluaciones_activas' => $seccion->evaluaciones()->activas()->count(),
        ];

        return view('secciones.show', compact('seccion', 'estadisticas'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Seccion $seccion)
    {
        // Solo administradores o el profesor asignado pueden editar
        if (!Auth::user()->es_administrador && $seccion->profesor_id !== Auth::id()) {
            abort(403, 'No tienes permisos para editar esta sección.');
        }

        $cursos = Curso::activos()->orderBy('nombre')->get();
        $periodos = PeriodoAcademico::orderBy('fecha_inicio', 'desc')->get();
        $profesores = Usuario::profesores()->activos()->orderBy('nombre')->get();

        return view('secciones.edit', compact('seccion', 'cursos', 'periodos', 'profesores'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Seccion $seccion)
    {
        // Solo administradores o el profesor asignado pueden editar
        if (!Auth::user()->es_administrador && $seccion->profesor_id !== Auth::id()) {
            abort(403, 'No tienes permisos para editar esta sección.');
        }

        $request->validate([
            'curso_id' => 'required|exists:cursos,id',
            'periodo_id' => 'required|exists:periodos_academicos,id',
            'profesor_id' => 'required|exists:usuarios,id',
            'codigo_seccion' => 'required|string|max:10',
            'cupo_maximo' => 'required|integer|min:1|max:100',
            'horario' => 'required|array',
            'aula' => 'required|string|max:50',
            'estado' => 'required|in:activo,inactivo',
        ]);

        // Verificar código único (excluyendo la sección actual)
        $existeSeccion = Seccion::where('codigo_seccion', $request->codigo_seccion)
            ->where('periodo_id', $request->periodo_id)
            ->where('curso_id', $request->curso_id)
            ->where('id', '!=', $seccion->id)
            ->exists();

        if ($existeSeccion) {
            return back()->withErrors([
                'codigo_seccion' => 'Ya existe una sección con este código para este curso en el período seleccionado.'
            ])->withInput();
        }

        try {
            DB::beginTransaction();

            $seccion->update([
                'curso_id' => $request->curso_id,
                'periodo_id' => $request->periodo_id,
                'profesor_id' => $request->profesor_id,
                'codigo_seccion' => $request->codigo_seccion,
                'cupo_maximo' => $request->cupo_maximo,
                'horario' => $request->horario,
                'aula' => $request->aula,
                'estado' => $request->estado,
            ]);

            DB::commit();

            return redirect()->route('secciones.show', $seccion)
                           ->with('success', 'Sección actualizada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar la sección: ' . $e->getMessage()])
                        ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Seccion $seccion)
    {
        // Solo administradores pueden eliminar
        if (!Auth::user()->es_administrador) {
            abort(403, 'No tienes permisos para eliminar secciones.');
        }

        // Verificar si tiene estudiantes inscritos
        if ($seccion->estudiantes_inscritos > 0) {
            return back()->withErrors(['error' => 'No se puede eliminar una sección que tiene estudiantes inscritos.']);
        }

        try {
            DB::beginTransaction();

            // Eliminar evaluaciones relacionadas
            $seccion->evaluaciones()->delete();
            
            // Eliminar la sección
            $seccion->delete();

            DB::commit();

            return redirect()->route('secciones.index')
                           ->with('success', 'Sección eliminada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al eliminar la sección: ' . $e->getMessage()]);
        }
    }

    /**
     * Mostrar estudiantes de la sección
     */
    public function estudiantes(Seccion $seccion)
    {
        $estudiantes = $seccion->inscripciones()
            ->with('estudiante')
            ->inscritos()
            ->orderBy('created_at')
            ->paginate(20);

        return view('secciones.estudiantes', compact('seccion', 'estudiantes'));
    }

    /**
     * Cambiar estado de la sección
     */
    public function cambiarEstado(Request $request, Seccion $seccion)
    {
        if (!Auth::user()->es_administrador) {
            abort(403);
        }

        $request->validate([
            'estado' => 'required|in:activo,inactivo',
        ]);

        $seccion->update(['estado' => $request->estado]);

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado correctamente.',
            'nuevo_estado' => $seccion->estado
        ]);
    }
}