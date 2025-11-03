<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TipoEvaluacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TipoEvaluacionController extends Controller
{
    public function index(Request $request)
    {
        $query = TipoEvaluacion::query();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }
        
        $tiposEvaluacion = $query->withCount('evaluaciones')
                                ->orderBy('nombre')
                                ->paginate(15);
        
        return view('admin.tipos-evaluacion.index', compact('tiposEvaluacion'));
    }
    
    public function create()
    {
        return view('admin.tipos-evaluacion.create');
    }
    
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255|unique:tipos_evaluacion',
            'descripcion' => 'nullable|string|max:1000',
            'porcentaje_default' => 'required|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        TipoEvaluacion::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'porcentaje_default' => $request->porcentaje_default,
        ]);

        return redirect()->route('admin.tipos-evaluacion.index')
                        ->with('success', 'Tipo de evaluación creado exitosamente.');
    }
    
    public function show(TipoEvaluacion $tipoEvaluacion)
    {
        $tipoEvaluacion->load(['evaluaciones.seccion.curso']);
        
        return view('admin.tipos-evaluacion.show', compact('tipoEvaluacion'));
    }
    
    public function edit(TipoEvaluacion $tipoEvaluacion)
    {
        return view('admin.tipos-evaluacion.edit', compact('tipoEvaluacion'));
    }
    
    public function update(Request $request, TipoEvaluacion $tipoEvaluacion)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255|unique:tipos_evaluacion,nombre,'.$tipoEvaluacion->id,
            'descripcion' => 'nullable|string|max:1000',
            'porcentaje_default' => 'required|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }

        $tipoEvaluacion->update([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'porcentaje_default' => $request->porcentaje_default,
        ]);

        return redirect()->route('admin.tipos-evaluacion.index')
                        ->with('success', 'Tipo de evaluación actualizado exitosamente.');
    }
    
    public function destroy(TipoEvaluacion $tipoEvaluacion)
    {
        // Verificar si tiene evaluaciones asociadas
        if ($tipoEvaluacion->evaluaciones()->exists()) {
            return redirect()->back()
                           ->with('error', 'No se puede eliminar el tipo de evaluación porque tiene evaluaciones asociadas.');
        }
        
        $tipoEvaluacion->delete();
        
        return redirect()->route('admin.tipos-evaluacion.index')
                        ->with('success', 'Tipo de evaluación eliminado exitosamente.');
    }
}