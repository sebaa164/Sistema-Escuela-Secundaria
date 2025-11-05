<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Evaluacion extends Model
{
    use HasFactory;

    protected $table = 'evaluaciones';

    protected $fillable = [
        'seccion_id',
        'tipo_evaluacion_id',
        'nombre',
        'descripcion',
        'fecha_evaluacion',
        'fecha_limite',
        'nota_maxima',
        'porcentaje',
        'estado',
        'instrucciones',
    ];

    protected $casts = [
        'fecha_evaluacion' => 'date',
        'fecha_limite' => 'datetime',
        'nota_maxima' => 'decimal:2',
        'porcentaje' => 'decimal:2',
    ];

    // Relaciones
    public function seccion()
    {
        return $this->belongsTo(Seccion::class);
    }

    public function tipoEvaluacion()
    {
        return $this->belongsTo(TipoEvaluacion::class);
    }

    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class);
    }

    // Scopes
    public function scopeProgramadas($query)
    {
        return $query->where('estado', 'programada');
    }

    public function scopeActivas($query)
    {
        return $query->where('estado', 'activa');
    }

    public function scopeFinalizadas($query)
    {
        return $query->where('estado', 'finalizada');
    }

    public function scopeDeSeccion($query, $seccionId)
    {
        return $query->where('seccion_id', $seccionId);
    }

    public function scopeVencidas($query)
    {
        return $query->where('fecha_limite', '<', Carbon::now());
    }

    public function scopePendientes($query)
    {
        return $query->where('fecha_limite', '>=', Carbon::now())
                    ->whereIn('estado', ['programada', 'activa']);
    }

    // Accessors
    public function getEstaVencidaAttribute()
    {
        return $this->fecha_limite && Carbon::now()->gt($this->fecha_limite);
    }

    public function getDiasParaVencimientoAttribute()
    {
        if (!$this->fecha_limite) return null;
        
        $diasRestantes = Carbon::now()->diffInDays($this->fecha_limite, false);
        return $diasRestantes > 0 ? $diasRestantes : 0;
    }

    public function getCalificacionesCountAttribute()
    {
        return $this->calificaciones()->count();
    }

    public function getCalificacionesPendientesCountAttribute()
    {
        return $this->calificaciones()->where('estado', 'pendiente')->count();
    }

    public function getCalificacionesCalificadasCountAttribute()
    {
        return $this->calificaciones()->whereIn('estado', ['calificada', 'revisada'])->count();
    }

    public function getPromedioNotasAttribute()
    {
        return $this->calificaciones()
                   ->whereNotNull('nota')
                   ->avg('nota');
    }

    public function getNombreCompletoAttribute()
    {
        return $this->tipoEvaluacion->nombre . ': ' . $this->nombre;
    }

    public function getEstadoBadgeClassAttribute()
    {
        switch($this->estado) {
            case 'programada':
                return 'badge-secondary';
            case 'activa':
                return 'badge-primary';
            case 'finalizada':
                return 'badge-success';
            case 'cancelada':
                return 'badge-danger';
            default:
                return 'badge-light';
        }
    }

    public function getPorcentajeFormateadoAttribute()
    {
        return $this->porcentaje . '%';
    }
}