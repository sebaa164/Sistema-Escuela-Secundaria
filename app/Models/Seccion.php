<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seccion extends Model
{
    use HasFactory;

    protected $table = 'secciones';

    protected $fillable = [
        'curso_id',
        'periodo_academico_id',  // ✅ Nombre correcto
        'profesor_id',
        'codigo_seccion',
        'cupo_maximo',
        'horario',
        'aula',
        'modalidad',
        'estado',
    ];

    protected $casts = [
        'horario' => 'array',
        'cupo_maximo' => 'integer',
    ];

    // Relaciones
    public function curso()
    {
        return $this->belongsTo(Curso::class);
    }

    // ✅ Especificar clave foránea correcta
    public function periodo()
    {
        return $this->belongsTo(PeriodoAcademico::class, 'periodo_academico_id');
    }

    public function profesor()
    {
        return $this->belongsTo(Usuario::class, 'profesor_id');
    }

    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class);
    }

    public function evaluaciones()
    {
        return $this->hasMany(Evaluacion::class);
    }

    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }

    public function estudiantes()
    {
        return $this->hasManyThrough(Usuario::class, Inscripcion::class, 'seccion_id', 'id', 'id', 'estudiante_id');
    }

    // Scopes
    public function scopeActivas($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeDelProfesor($query, $profesorId)
    {
        return $query->where('profesor_id', $profesorId);
    }

    public function scopeDelPeriodo($query, $periodoId)
    {
        return $query->where('periodo_academico_id', $periodoId);  // ✅ Nombre correcto
    }

    // Accessors
    public function getNombreCompletoAttribute()
    {
        $curso = $this->curso ? $this->curso->codigo_curso : 'N/A';
        $periodo = $this->periodo ? $this->periodo->nombre : 'N/A';
        return $curso . ' - ' . $this->codigo_seccion . ' (' . $periodo . ')';
    }

    public function getEstudiantesInscritosAttribute()
    {
        return $this->inscripciones()->where('estado', 'inscrito')->count();
    }

    public function getCuposDisponiblesAttribute()
    {
        return $this->cupo_maximo - $this->estudiantes_inscritos;
    }

    public function getEstaLlenaAttribute()
    {
        return $this->estudiantes_inscritos >= $this->cupo_maximo;
    }

    public function getHorarioFormateadoAttribute()
    {
        if (!$this->horario) return 'No definido';
        
        $horarioTexto = '';
        foreach ($this->horario as $dia => $horas) {
            $horarioTexto .= ucfirst($dia) . ': ' . $horas . ' | ';
        }
        
        return rtrim($horarioTexto, ' | ');
    }
}