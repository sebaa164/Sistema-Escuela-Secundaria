<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Asistencia extends Model
{
    use HasFactory;

    protected $fillable = [
        'inscripcion_id',
        'fecha',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    // Relaciones
    public function inscripcion()
    {
        return $this->belongsTo(Inscripcion::class);
    }

    public function estudiante()
    {
        return $this->hasOneThrough(
            Usuario::class,
            Inscripcion::class,
            'id',
            'id',
            'inscripcion_id',
            'estudiante_id'
        );
    }

    public function seccion()
    {
        return $this->hasOneThrough(
            Seccion::class,
            Inscripcion::class,
            'id',
            'id',
            'inscripcion_id',
            'seccion_id'
        );
    }

    // Scopes
    public function scopePresentes($query)
    {
        return $query->where('estado', 'presente');
    }

    public function scopeAusentes($query)
    {
        return $query->where('estado', 'ausente');
    }

    public function scopeTardanzas($query)
    {
        return $query->where('estado', 'tardanza');
    }

    public function scopeJustificadas($query)
    {
        return $query->where('estado', 'justificada');
    }

    public function scopeDeLaInscripcion($query, $inscripcionId)
    {
        return $query->where('inscripcion_id', $inscripcionId);
    }

    public function scopeDelMes($query, $mes, $año = null)
    {
        $año = $año ?: Carbon::now()->year;
        return $query->whereMonth('fecha', $mes)
                    ->whereYear('fecha', $año);
    }

    public function scopeDelRangoFecha($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha', [$fechaInicio, $fechaFin]);
    }

    public function scopeDeHoy($query)
    {
        return $query->whereDate('fecha', Carbon::today());
    }

    // Accessors
    public function getEsPresenteAttribute()
    {
        return $this->estado === 'presente';
    }

    public function getEsAusenteAttribute()
    {
        return $this->estado === 'ausente';
    }

    public function getEsTardanzaAttribute()
    {
        return $this->estado === 'tardanza';
    }

    public function getEsJustificadaAttribute()
    {
        return $this->estado === 'justificada';
    }

    public function getEstadoBadgeClassAttribute()
    {
        switch($this->estado) {
            case 'presente':
                return 'badge-success';
            case 'ausente':
                return 'badge-danger';
            case 'tardanza':
                return 'badge-warning';
            case 'justificada':
                return 'badge-info';
            default:
                return 'badge-secondary';
        }
    }

    public function getEstadoTextoAttribute()
    {
        switch($this->estado) {
            case 'presente':
                return 'Presente';
            case 'ausente':
                return 'Ausente';
            case 'tardanza':
                return 'Tardanza';
            case 'justificada':
                return 'Justificada';
            default:
                return 'No definido';
        }
    }

    public function getFechaFormateadaAttribute()
    {
        return $this->fecha->format('d/m/Y');
    }

    public function getDiaSemanaAttribute()
    {
        return $this->fecha->locale('es')->dayName;
    }
}