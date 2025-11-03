<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calificacion extends Model
{
    use HasFactory;

    protected $table = 'calificaciones';

    protected $fillable = [
        'evaluacion_id',
        'estudiante_id',
        'nota',
        'comentarios',
        'fecha_calificacion',
        'estado',
        'intentos',
        'tiempo_empleado',
    ];

    protected $casts = [
        'nota' => 'decimal:2',
        'fecha_calificacion' => 'datetime',
        'intentos' => 'integer',
        'tiempo_empleado' => 'integer',
    ];

    // Relaciones
    public function evaluacion()
    {
        return $this->belongsTo(Evaluacion::class);
    }

    public function estudiante()
    {
        return $this->belongsTo(Usuario::class, 'estudiante_id');
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeCalificadas($query)
    {
        return $query->where('estado', 'calificada');
    }

    public function scopeRevisadas($query)
    {
        return $query->where('estado', 'revisada');
    }

    public function scopeDelEstudiante($query, $estudianteId)
    {
        return $query->where('estudiante_id', $estudianteId);
    }

    public function scopeDeLaEvaluacion($query, $evaluacionId)
    {
        return $query->where('evaluacion_id', $evaluacionId);
    }

    public function scopeAprobadas($query)
    {
        $notaMinima = config('app.nota_minima_aprobacion', 70);
        return $query->where('nota', '>=', $notaMinima);
    }

    public function scopeReprobadas($query)
    {
        $notaMinima = config('app.nota_minima_aprobacion', 70);
        return $query->where('nota', '<', $notaMinima);
    }

    // Accessors
    public function getEstaAprobadaAttribute()
    {
        if ($this->nota === null) return null;

        $notaMinima = config('app.nota_minima_aprobacion', 70);
        $notaNormalizada = $this->nota_sobre_cien;
        return $notaNormalizada !== null ? $notaNormalizada >= $notaMinima : null;
    }

    public function getEstadoNotaAttribute()
    {
        if ($this->nota === null) return 'Sin calificar';
        
        if ($this->esta_aprobada) {
            return 'Aprobado';
        } else {
            return 'Reprobado';
        }
    }

    public function getNotaFormateadaAttribute()
    {
        if ($this->nota === null) return 'Sin calificar';
        return number_format($this->nota, 2);
    }

    public function getPorcentajeObtenidoAttribute()
    {
        if ($this->nota === null || $this->evaluacion->nota_maxima == 0) return null;
        
        return ($this->nota / $this->evaluacion->nota_maxima) * 100;
    }

    public function getNotaSobreCienAttribute()
    {
        if ($this->nota === null) return null;
        
        if ($this->evaluacion->nota_maxima == 100) {
            return $this->nota;
        }
        
        return ($this->nota / $this->evaluacion->nota_maxima) * 100;
    }

    public function getTiempoEmpleadoFormateadoAttribute()
    {
        if (!$this->tiempo_empleado) return 'No registrado';
        
        $horas = floor($this->tiempo_empleado / 60);
        $minutos = $this->tiempo_empleado % 60;
        
        if ($horas > 0) {
            return $horas . 'h ' . $minutos . 'm';
        }
        
        return $minutos . ' minutos';
    }

    public function getEstadoBadgeClassAttribute()
    {
        switch($this->estado) {
            case 'pendiente':
                return 'badge-warning';
            case 'calificada':
                return 'badge-success';
            case 'revisada':
                return 'badge-info';
            default:
                return 'badge-secondary';
        }
    }
}