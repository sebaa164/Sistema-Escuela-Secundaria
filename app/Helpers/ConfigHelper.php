<?php

use App\Models\Configuracion;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

if (!function_exists('config_sistema')) {
    /**
     * Obtener valor de configuración del sistema desde la base de datos
     *
     * @param string $clave Clave de configuración
     * @param mixed $default Valor por defecto si no existe
     * @return mixed
     */
    function config_sistema($clave, $default = null)
    {
        try {
            // Cachear las configuraciones por 1 hora
            $configuraciones = Cache::remember('configuraciones_sistema', 3600, function () {
                return Configuracion::all()->pluck('valor_tipificado', 'clave')->toArray();
            });

            return $configuraciones[$clave] ?? $default;
        } catch (\Exception $e) {
            Log::error("Error al obtener configuración: {$clave}", [
                'error' => $e->getMessage()
            ]);
            return $default;
        }
    }
}

if (!function_exists('actualizar_config_cache')) {
    /**
     * Actualizar caché de configuraciones
     *
     * @return void
     */
    function actualizar_config_cache()
    {
        Cache::forget('configuraciones_sistema');

        // Recargar configuraciones
        Cache::remember('configuraciones_sistema', 3600, function () {
            return Configuracion::all()->pluck('valor_tipificado', 'clave')->toArray();
        });
    }
}

if (!function_exists('aplicar_configuraciones_sistema')) {
    /**
     * Aplicar configuraciones del sistema al runtime de Laravel
     *
     * @return void
     */
    function aplicar_configuraciones_sistema()
    {
        try {
            // Aplicar timezone
            $timezone = config_sistema('timezone', config('app.timezone'));
            config(['app.timezone' => $timezone]);
            date_default_timezone_set($timezone);

            // Aplicar nombre del sistema
            $nombreSistema = config_sistema('sistema_nombre', config('app.name'));
            config(['app.name' => $nombreSistema]);

            // Aplicar formato de fecha
            $formatoFecha = config_sistema('formato_fecha', 'Y-m-d');
            config(['app.formato_fecha' => $formatoFecha]);

            // Aplicar nota mínima de aprobación
            $notaMinima = config_sistema('nota_minima_aprobacion', 70);
            config(['academico.nota_minima_aprobacion' => $notaMinima]);

            // Aplicar máximo de estudiantes por sección
            $maxEstudiantes = config_sistema('max_estudiantes_seccion', 30);
            config(['academico.max_estudiantes_seccion' => $maxEstudiantes]);

        } catch (\Exception $e) {
            Log::error('Error al aplicar configuraciones del sistema', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
