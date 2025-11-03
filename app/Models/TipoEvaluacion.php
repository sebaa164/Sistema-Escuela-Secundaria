<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoEvaluacion extends Model
{
    use HasFactory;

    protected $table = 'tipos_evaluacion';

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',  // Este campo SÍ existe en tu migración
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    // Relaciones
    public function evaluaciones()
    {
        return $this->hasMany(Evaluacion::class);
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorNombre($query, $nombre)
    {
        return $query->where('nombre', 'like', '%' . $nombre . '%');
    }

    // Accessors
    public function getEvaluacionesCountAttribute()
    {
        return $this->evaluaciones()->count();
    }
}