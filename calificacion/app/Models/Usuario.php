<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'telefono',
        'direccion',
        'fecha_nacimiento',
        'tipo_usuario',
        'estado',
        'estado_estudiante',
        'tutor_id',
        'password',
        'email_verified_at',
        'remember_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'timestamp',
        'fecha_nacimiento' => 'date',
    ];

    // Relaciones
    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class, 'estudiante_id');
    }

    public function secciones()
    {
        return $this->hasMany(Seccion::class, 'profesor_id');
    }

    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class, 'estudiante_id');
    }

    public function asistencias()
    {
        return $this->hasManyThrough(Asistencia::class, Inscripcion::class, 'estudiante_id', 'inscripcion_id');
    }

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class, 'usuario_id');
    }

    public function tutor()
    {
        return $this->belongsTo(Tutor::class);
    }

    // Scopes
    public function scopeEstudiantes($query)
    {
        return $query->where('tipo_usuario', 'estudiante');
    }

    public function scopeProfesores($query)
    {
        return $query->where('tipo_usuario', 'profesor');
    }

    public function scopeAdministradores($query)
    {
        return $query->where('tipo_usuario', 'administrador');
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeRegulares($query)
    {
        return $query->where('estado_estudiante', 'regular');
    }

    public function scopeSuspendidos($query)
    {
        return $query->where('estado_estudiante', 'suspendido');
    }

    public function scopeLibres($query)
    {
        return $query->where('estado_estudiante', 'libre');
    }

    public function scopePreinscriptos($query)
    {
        return $query->where('estado_estudiante', 'preinscripto');
    }

    // Accessors
    public function getNombreCompletoAttribute()
    {
        return $this->nombre . ' ' . $this->apellido;
    }

    public function getEsEstudianteAttribute()
    {
        return $this->tipo_usuario === 'estudiante';
    }

    public function getEsProfesorAttribute()
    {
        return $this->tipo_usuario === 'profesor';
    }

    public function getEsAdministradorAttribute()
    {
        return $this->tipo_usuario === 'administrador';
    }
}