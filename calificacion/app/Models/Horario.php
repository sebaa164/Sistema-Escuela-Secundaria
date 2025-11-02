<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Horario extends Model
{
    use HasFactory;

    protected $table = 'horarios';

    protected $fillable = [
        'seccion_id',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
        'aula',
    ];

    protected $casts = [
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i',
    ];

    /**
     * Relación con Sección
     */
    public function seccion()
    {
        return $this->belongsTo(Seccion::class);
    }

    /**
     * Accessor para obtener hora de inicio formateada
     */
    public function getHoraInicioFormateadaAttribute()
    {
        return Carbon::parse($this->hora_inicio)->format('H:i');
    }

    /**
     * Accessor para obtener hora de fin formateada
     */
    public function getHoraFinFormateadaAttribute()
    {
        return Carbon::parse($this->hora_fin)->format('H:i');
    }

    /**
     * Scope para filtrar por día
     */
    public function scopePorDia($query, $dia)
    {
        return $query->where('dia_semana', $dia);
    }

    /**
     * Scope para ordenar por día de la semana (Lunes primero)
     */
    public function scopeOrdenadoPorDia($query)
    {
        return $query->orderByRaw(
            "FIELD(dia_semana, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo')"
        );
    }

    /**
     * Scope para ordenar por hora de inicio
     */
    public function scopeOrdenadoPorHora($query)
    {
        return $query->orderBy('hora_inicio');
    }
}