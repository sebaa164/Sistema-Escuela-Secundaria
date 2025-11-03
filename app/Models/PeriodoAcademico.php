<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PeriodoAcademico extends Model
{
    use HasFactory;

    protected $table = 'periodos_academicos';

    protected $fillable = [
        'codigo',
        'ciclo_escolar',
        'año_academico',
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'estado',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    // ✅ CORREGIDO: Especificar la clave foránea correcta
    public function secciones()
    {
        return $this->hasMany(Seccion::class, 'periodo_academico_id');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeFinalizados($query)
    {
        return $query->where('estado', 'finalizado');
    }

    public function scopeVigente($query)
    {
        $hoy = Carbon::now()->toDateString();
        return $query->where('fecha_inicio', '<=', $hoy)
                    ->where('fecha_fin', '>=', $hoy)
                    ->where('estado', 'activo');
    }

    // Accessors
    public function getEsVigenteAttribute()
    {
        $hoy = Carbon::now();
        return $this->fecha_inicio <= $hoy && $this->fecha_fin >= $hoy && $this->estado === 'activo';
    }

    public function getDuracionDiasAttribute()
    {
        return $this->fecha_inicio->diffInDays($this->fecha_fin);
    }

    public function getDiasRestantesAttribute()
    {
        if ($this->fecha_fin < Carbon::now()) {
            return 0;
        }
        return Carbon::now()->diffInDays($this->fecha_fin);
    }

    // Nombre corto para mostrar en tablas
    public function getNombreCortoAttribute()
    {
        $nombre = $this->nombre;
        
        // Extraer año si existe
        preg_match('/(\d{4})/', $nombre, $year);
        $anio = $year[0] ?? '';
        
        // Detectar trimestres
        if (str_contains(strtolower($nombre), 'trimestre')) {
            if (str_contains(strtolower($nombre), 'primer')) return $anio . ' T1';
            if (str_contains(strtolower($nombre), 'segundo')) return $anio . ' T2';
            if (str_contains(strtolower($nombre), 'tercer')) return $anio . ' T3';
            if (str_contains(strtolower($nombre), 'cuarto')) return $anio . ' T4';
        }
        
        // Detectar semestres
        if (str_contains(strtolower($nombre), 'semestre')) {
            if (str_contains(strtolower($nombre), 'primer')) return $anio . ' S1';
            if (str_contains(strtolower($nombre), 'segundo')) return $anio . ' S2';
        }
        
        // Si no coincide con ningún patrón, devolver nombre truncado
        return Str::limit($nombre, 15, '');
    }
}