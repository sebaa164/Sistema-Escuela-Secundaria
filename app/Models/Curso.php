<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo_curso',
        'nombre',
        'descripcion',
        'horas_semanales', 
        'nivel',
        'carrera',
        'requisitos',   
        'estado',
    ];

    protected $casts = [
        'horas_semanales' => 'integer',
    ];

    // Relaciones
    public function secciones()
    {
        return $this->hasMany(Seccion::class);
    }

    public function inscripciones()
    {
        return $this->hasManyThrough(Inscripcion::class, Seccion::class);
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopePorCarrera($query, $carrera)
    {
        return $query->where('carrera', $carrera);
    }

    public function scopePorNivel($query, $nivel)
    {
        return $query->where('nivel', $nivel);
    }

    // Accessors
    public function getCodigoNombreAttribute()
    {
        return $this->codigo_curso . ' - ' . $this->nombre;
    }

    public function getEstudiantesInscritosCountAttribute()
    {
        return $this->inscripciones()->where('estado', 'inscrito')->count();
    }
}