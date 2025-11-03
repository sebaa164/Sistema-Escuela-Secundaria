<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    use HasFactory;

    protected $table = 'configuraciones';

    protected $fillable = [
        'clave',
        'valor',
        'descripcion',
        'tipo',
    ];

    /**
     * Accessor para obtener el valor tipificado
     */
    public function getValorTipificadoAttribute()
    {
        switch ($this->tipo) {
            case 'number':
                return is_numeric($this->valor) ? (float) $this->valor : $this->valor;
            
            case 'boolean':
                return in_array(strtolower($this->valor), ['true', '1', 'yes', 'on']) ? true : false;
            
            case 'json':
                $decoded = json_decode($this->valor, true);
                return json_last_error() === JSON_ERROR_NONE ? $decoded : $this->valor;
            
            case 'string':
            default:
                return $this->valor;
        }
    }

    /**
     * Helper estático para obtener configuración
     */
    public static function getConfig($clave, $default = null)
    {
        $config = static::where('clave', $clave)->first();
        return $config ? $config->valor_tipificado : $default;
    }

    /**
     * Helper estático para establecer configuración
     */
    public static function setConfig($clave, $valor, $tipo = 'string', $descripcion = null)
    {
        return static::updateOrCreate(
            ['clave' => $clave],
            [
                'valor' => $valor,
                'tipo' => $tipo,
                'descripcion' => $descripcion
            ]
        );
    }
}