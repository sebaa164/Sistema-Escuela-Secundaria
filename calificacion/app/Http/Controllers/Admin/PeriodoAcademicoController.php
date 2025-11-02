<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeriodoAcademico;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PeriodoAcademicoController extends Controller
{
    /**
     * Mostrar listado de períodos académicos
     */
    public function index(Request $request)
    {
        $query = PeriodoAcademico::withCount('secciones');

        // Filtro por búsqueda
        if ($request->filled('search')) {
            $query->where('nombre', 'like', '%' . $request->search . '%');
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $periodos = $query->orderBy('fecha_inicio', 'desc')->paginate(10);

        return view('admin.periodos.index', compact('periodos'));
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        return view('admin.periodos.create');
    }

    /**
     * Almacenar nuevo período académico
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
        ], [
            'nombre.required' => 'El nombre del período es obligatorio.',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_fin.required' => 'La fecha de finalización es obligatoria.',
            'fecha_fin.after' => 'La fecha de finalización debe ser posterior a la fecha de inicio.',
        ]);

        // Generar código único automáticamente
        $validated['codigo'] = $this->generarCodigoPeriodo();
        
        // ✅ AGREGADO: Generar ciclo escolar automáticamente
        $validated['ciclo_escolar'] = $this->generarCicloEscolar($validated['fecha_inicio']);
        
        // ✅ AGREGADO: Generar año académico (año de la fecha de inicio)
        $validated['año_academico'] = \Carbon\Carbon::parse($validated['fecha_inicio'])->year;
        
        $validated['estado'] = 'Activo';  // ✅ Con mayúscula

        PeriodoAcademico::create($validated);

        return redirect()->route('admin.periodos.index')
            ->with('success', 'Período académico creado exitosamente.');
    }

    /**
     * Mostrar detalles de un período
     */
    public function show(PeriodoAcademico $periodo)
    {
        $periodo->load(['secciones.curso', 'secciones.profesor']);
        
        $totalSecciones = $periodo->secciones->count();
        $seccionesActivas = $periodo->secciones->where('estado', 'activo')->count();
        $totalInscripciones = $periodo->secciones->sum(function($seccion) {
            return $seccion->inscripciones()->count();
        });

        return view('admin.periodos.show', compact('periodo', 'totalSecciones', 'seccionesActivas', 'totalInscripciones'));
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit(PeriodoAcademico $periodo)
    {
        $periodo->loadCount('secciones');
        return view('admin.periodos.edit', compact('periodo'));
    }

    /**
     * Actualizar período académico
     */
    public function update(Request $request, PeriodoAcademico $periodo)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'estado' => 'required|in:Activo,Inactivo,Finalizado',  // ✅ Con mayúsculas
        ], [
            'nombre.required' => 'El nombre del período es obligatorio.',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_fin.required' => 'La fecha de finalización es obligatoria.',
            'fecha_fin.after' => 'La fecha de finalización debe ser posterior a la fecha de inicio.',
            'estado.required' => 'El estado es obligatorio.',
        ]);

        $periodo->update($validated);

        return redirect()->route('admin.periodos.index')
            ->with('success', 'Período académico actualizado exitosamente.');
    }

    /**
     * Eliminar período académico
     */
    public function destroy(PeriodoAcademico $periodo)
    {
        // Verificar que no tenga secciones asociadas
        if ($periodo->secciones()->exists()) {
            return redirect()->route('admin.periodos.index')
                ->with('error', 'No se puede eliminar un período que tiene secciones asociadas.');
        }

        $periodo->delete();

        return redirect()->route('admin.periodos.index')
            ->with('success', 'Período académico eliminado exitosamente.');
    }

    /**
     * Cambiar estado del período
     */
    public function cambiarEstado(PeriodoAcademico $periodo)
    {
        $nuevoEstado = $periodo->estado === 'Activo' ? 'Inactivo' : 'Activo';  // ✅ Con mayúsculas
        $periodo->update(['estado' => $nuevoEstado]);

        return redirect()->back()
            ->with('success', 'Estado del período actualizado exitosamente.');
    }

    /**
     * Obtener período vigente
     */
    public function vigente()
    {
        $periodoVigente = PeriodoAcademico::where('estado', 'activo')
            ->where('fecha_inicio', '<=', now())
            ->where('fecha_fin', '>=', now())
            ->first();

        if (!$periodoVigente) {
            return response()->json([
                'success' => false,
                'message' => 'No hay período académico vigente'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'periodo' => $periodoVigente
        ]);
    }

    /**
     * Generar código único para el período
     */
    private function generarCodigoPeriodo()
    {
        $year = date('Y');
        $lastPeriodo = PeriodoAcademico::where('codigo', 'like', "PA-{$year}-%")
            ->orderBy('codigo', 'desc')
            ->first();

        if ($lastPeriodo) {
            // Extraer el número del último código y aumentarlo
            $lastNumber = (int) substr($lastPeriodo->codigo, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            // Primer período del año
            $newNumber = '001';
        }

        return "PA-{$year}-{$newNumber}";
    }

    /**
     * ✅ NUEVO MÉTODO: Generar ciclo escolar basado en la fecha de inicio
     */
    private function generarCicloEscolar($fechaInicio)
    {
        $fecha = \Carbon\Carbon::parse($fechaInicio);
        $year = $fecha->year;
        
        // Si es después de julio (mes 7), es el ciclo que va al año siguiente
        // Ejemplo: fecha en agosto 2025 → ciclo 2025-2026
        if ($fecha->month >= 7) {
            return $year . '-' . ($year + 1);
        }
        
        // Si es antes de julio, es el ciclo que empezó el año anterior
        // Ejemplo: fecha en marzo 2025 → ciclo 2024-2025
        return ($year - 1) . '-' . $year;
    }
}