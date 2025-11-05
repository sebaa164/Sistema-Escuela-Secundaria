<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ConfiguracionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Configuracion::query();

        // Filtro por búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('clave', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        // Filtro por tipo
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $configuraciones = $query->orderBy('clave')->paginate(15);

        return view('admin.configuraciones.index', compact('configuraciones'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.configuraciones.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'clave' => 'required|string|max:255|unique:configuraciones,clave',
            'valor' => 'required',
            'tipo' => 'required|in:string,number,boolean,json',
            'descripcion' => 'nullable|string|max:1000',
        ], [
            'clave.required' => 'La clave es obligatoria',
            'clave.unique' => 'Esta clave ya existe',
            'valor.required' => 'El valor es obligatorio',
            'tipo.required' => 'El tipo es obligatorio',
        ]);

        // Validar JSON si el tipo es json
        if ($validated['tipo'] === 'json') {
            $decoded = json_decode($validated['valor']);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['valor' => 'El valor JSON no es válido'])
                    ->withInput();
            }
        }

        Configuracion::create($validated);

        // Limpiar caché y recargar configuraciones
        actualizar_config_cache();
        aplicar_configuraciones_sistema();

        return redirect()->route('admin.configuraciones.index')
            ->with('success', 'Configuración creada exitosamente');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Configuracion $configuracion)
    {
        return view('admin.configuraciones.edit', compact('configuracion'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Configuracion $configuracion)
    {
        $validated = $request->validate([
            'clave' => 'required|string|max:255|unique:configuraciones,clave,' . $configuracion->id,
            'valor' => 'required',
            'tipo' => 'required|in:string,number,boolean,json',
            'descripcion' => 'nullable|string|max:1000',
        ], [
            'clave.required' => 'La clave es obligatoria',
            'clave.unique' => 'Esta clave ya existe',
            'valor.required' => 'El valor es obligatorio',
            'tipo.required' => 'El tipo es obligatorio',
        ]);

        // Validar JSON si el tipo es json
        if ($validated['tipo'] === 'json') {
            $decoded = json_decode($validated['valor']);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['valor' => 'El valor JSON no es válido'])
                    ->withInput();
            }
        }

        $configuracion->update($validated);

        // Limpiar caché y recargar configuraciones
        actualizar_config_cache();
        aplicar_configuraciones_sistema();

        return redirect()->route('admin.configuraciones.index')
            ->with('success', 'Configuración actualizada exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Configuracion $configuracion)
    {
        $configuracion->delete();

        // Limpiar caché
        Cache::forget('configuraciones');

        return redirect()->route('admin.configuraciones.index')
            ->with('success', 'Configuración eliminada exitosamente');
    }

    /**
     * Mostrar el formulario de configuración del sistema
     */
    public function sistema()
    {
        // Configuraciones comunes del sistema
        $configuracionesComunes = [
            'nota_minima_aprobacion' => $this->getConfig('nota_minima_aprobacion', 60),
            'max_estudiantes_seccion' => $this->getConfig('max_estudiantes_seccion', 30),
            'sistema_nombre' => $this->getConfig('sistema_nombre', 'Sistema de Gestión Académica'),
            'timezone' => $this->getConfig('timezone', 'America/Argentina/San_Juan'),
            'formato_fecha' => $this->getConfig('formato_fecha', 'Y-m-d'),
        ];

        return view('admin.configuraciones.sistema', compact('configuracionesComunes'));
    }

    /**
     * Actualizar configuraciones del sistema
     */
    public function actualizarSistema(Request $request)
    {
        try {
            // Log para debugging
            Log::info('Actualizando configuraciones del sistema', [
                'datos_recibidos' => $request->all()
            ]);

            $validated = $request->validate([
                'nota_minima_aprobacion' => 'required|numeric|min:0|max:100',
                'max_estudiantes_seccion' => 'required|integer|min:1|max:100',
                'sistema_nombre' => 'required|string|max:255',
                'timezone' => 'required|string',
                'formato_fecha' => 'nullable|string',
            ], [
                'nota_minima_aprobacion.required' => 'La nota mínima es obligatoria',
                'nota_minima_aprobacion.min' => 'La nota mínima debe ser al menos 0',
                'nota_minima_aprobacion.max' => 'La nota mínima no puede ser mayor a 100',
                'max_estudiantes_seccion.required' => 'El máximo de estudiantes es obligatorio',
                'max_estudiantes_seccion.min' => 'Debe haber al menos 1 estudiante',
                'sistema_nombre.required' => 'El nombre del sistema es obligatorio',
                'timezone.required' => 'La zona horaria es obligatoria',
            ]);

            // Iniciar transacción
            DB::beginTransaction();

            $actualizados = 0;
            
            // Actualizar cada configuración
            foreach ($validated as $clave => $valor) {
                $tipo = $this->determinarTipo($valor);
                
                $config = Configuracion::updateOrCreate(
                    ['clave' => $clave],
                    [
                        'valor' => (string) $valor,
                        'tipo' => $tipo,
                        'descripcion' => $this->getDescripcion($clave)
                    ]
                );

                if ($config->wasRecentlyCreated || $config->wasChanged()) {
                    $actualizados++;
                }

                Log::info("Configuración actualizada: {$clave}", [
                    'valor' => $valor,
                    'tipo' => $tipo,
                    'guardado' => $config->valor
                ]);
            }

            DB::commit();

            // Limpiar caché y recargar configuraciones
            actualizar_config_cache();
            aplicar_configuraciones_sistema();

            Log::info("Configuraciones actualizadas exitosamente. Total: {$actualizados}");

            return redirect()->route('admin.configuraciones.sistema')
                ->with('success', "Configuración del sistema actualizada exitosamente ({$actualizados} cambios)");

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Error de validación en configuraciones', [
                'errores' => $e->errors()
            ]);
            return back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar configuraciones del sistema', [
                'mensaje' => $e->getMessage(),
                'linea' => $e->getLine(),
                'archivo' => $e->getFile()
            ]);
            
            return back()
                ->with('error', 'Error al actualizar la configuración: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Obtener valor de configuración
     */
    private function getConfig($clave, $default = null)
    {
        try {
            $config = Configuracion::where('clave', $clave)->first();
            return $config ? $config->valor_tipificado : $default;
        } catch (\Exception $e) {
            Log::error("Error al obtener configuración: {$clave}", [
                'error' => $e->getMessage()
            ]);
            return $default;
        }
    }

    /**
     * Determinar el tipo de dato
     */
    private function determinarTipo($valor)
    {
        if (is_numeric($valor)) {
            return 'number';
        }
        if (is_bool($valor) || in_array(strtolower($valor), ['true', 'false', '1', '0'])) {
            return 'boolean';
        }
        if (is_array($valor) || is_object($valor)) {
            return 'json';
        }
        return 'string';
    }

    /**
     * Obtener descripción por clave
     */
    private function getDescripcion($clave)
    {
        $descripciones = [
            'nota_minima_aprobacion' => 'Nota mínima que debe obtener un estudiante para aprobar una materia',
            'max_estudiantes_seccion' => 'Cantidad máxima de estudiantes permitidos por sección',
            'sistema_nombre' => 'Nombre del sistema que aparece en títulos y encabezados',
            'timezone' => 'Zona horaria del sistema para fechas y horas',
            'formato_fecha' => 'Formato de visualización de fechas en el sistema',
        ];

        return $descripciones[$clave] ?? null;
    }
}