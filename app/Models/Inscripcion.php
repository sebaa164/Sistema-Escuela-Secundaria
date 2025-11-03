<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Inscripcion extends Model
{
    use HasFactory;

    protected $table = 'inscripciones';

    protected $fillable = [
        'estudiante_id',
        'seccion_id',
        'fecha_inscripcion',
        'fecha_retiro',
        'estado',
        'nota_final',
    ];

    protected $casts = [
        'fecha_inscripcion' => 'datetime',
        'fecha_retiro' => 'datetime',
        'nota_final' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ✅ Campos calculados disponibles bajo demanda (no automáticamente)
    // Se usará $inscripcion->esta_aprobado cuando lo necesite
    // protected $appends = [];
    
    // ==========================================
    // RELACIONES
    // ==========================================
    
    public function estudiante()
    {
        return $this->belongsTo(Usuario::class, 'estudiante_id');
    }

    public function seccion()
    {
        return $this->belongsTo(Seccion::class);
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class);
    }

    /**
     * ✅ RELACIÓN CORREGIDA: Obtener calificaciones del estudiante en esta sección
     */
    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class, 'estudiante_id', 'estudiante_id')
            ->whereHas('evaluacion', function($query) {
                $query->where('seccion_id', $this->seccion_id);
            });
    }

    // ==========================================
    // SCOPES
    // ==========================================
    
    public function scopeInscritos($query)
    {
        return $query->where('estado', 'inscrito');
    }

    public function scopeCompletados($query)
    {
        return $query->where('estado', 'completado');
    }

    public function scopeRetirados($query)
    {
        return $query->where('estado', 'retirado');
    }

    public function scopeDelEstudiante($query, $estudianteId)
    {
        return $query->where('estudiante_id', $estudianteId);
    }

    public function scopeDeLaSeccion($query, $seccionId)
    {
        return $query->where('seccion_id', $seccionId);
    }

    public function scopeConRelacionesValidas($query)
    {
        return $query->whereHas('estudiante')
            ->whereHas('seccion', function($q) {
                $q->whereHas('curso');
            });
    }

    // ==========================================
    // ACCESSORS (Solo los básicos en appends)
    // ==========================================
    
    /**
     * Verifica si el estudiante está aprobado
     */
    public function getEstaAprobadoAttribute()
    {
        if (!$this->nota_final) return null;
        
        $notaMinima = config('app.nota_minima_aprobacion', 70);
        return $this->nota_final >= $notaMinima;
    }

    /**
     * Obtiene el estado de la nota (Aprobado/Reprobado/Sin calificar)
     */
    public function getEstadoNotaAttribute()
    {
        if (!$this->nota_final) return 'Sin calificar';
        
        return $this->esta_aprobado ? 'Aprobado' : 'Reprobado';
    }

    // ==========================================
    // MÉTODOS PÚBLICOS (No en appends para evitar N+1)
    // ==========================================

    /**
     * Calcula el promedio ponderado de evaluaciones
     * ⚠️ Requiere que seccion.evaluaciones.calificaciones estén cargadas
     */
    public function calcularPromedioEvaluaciones()
    {
        if (!$this->seccion_id || !$this->seccion) {
            return null;
        }

        try {
            $evaluaciones = $this->seccion->evaluaciones ?? collect();
            
            if ($evaluaciones->isEmpty()) {
                return null;
            }

            $totalPorcentaje = 0;
            $notaPonderada = 0;

            foreach ($evaluaciones as $evaluacion) {
                // Buscar calificación del estudiante
                $calificacion = Calificacion::where('evaluacion_id', $evaluacion->id)
                    ->where('estudiante_id', $this->estudiante_id)
                    ->where('estado', 'calificada')
                    ->first();
                
                if ($calificacion && $calificacion->nota !== null) {
                    $notaPonderada += ($calificacion->nota * $evaluacion->porcentaje) / 100;
                    $totalPorcentaje += $evaluacion->porcentaje;
                }
            }

            if ($totalPorcentaje == 0) {
                return null;
            }

            return round($notaPonderada, 2);
        } catch (\Exception $e) {
            Log::error("Error calculando promedio para inscripción {$this->id}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Calcula estadísticas de asistencia
     * ⚠️ Requiere que asistencias estén cargadas
     */
    public function calcularEstadisticasAsistencia()
    {
        try {
            $asistencias = $this->asistencias ?? collect();
            
            $total = $asistencias->count();
            
            if ($total == 0) {
                return [
                    'total' => 0,
                    'presente' => 0,
                    'tardanza' => 0,
                    'ausente' => 0,
                    'asistencias_validas' => 0,
                    'porcentaje' => 0
                ];
            }

            $presente = $asistencias->where('estado', 'presente')->count();
            $tardanza = $asistencias->where('estado', 'tardanza')->count();
            $ausente = $asistencias->where('estado', 'ausente')->count();
            $validas = $presente + $tardanza;
            $porcentaje = round(($validas / $total) * 100, 2);

            return [
                'total' => $total,
                'presente' => $presente,
                'tardanza' => $tardanza,
                'ausente' => $ausente,
                'asistencias_validas' => $validas,
                'porcentaje' => $porcentaje
            ];
        } catch (\Exception $e) {
            Log::error("Error calculando asistencias para inscripción {$this->id}: " . $e->getMessage());
            return [
                'total' => 0,
                'presente' => 0,
                'tardanza' => 0,
                'ausente' => 0,
                'asistencias_validas' => 0,
                'porcentaje' => 0
            ];
        }
    }

    /**
     * Obtiene todas las estadísticas de la inscripción
     */
    public function obtenerEstadisticas()
    {
        $promedioEvaluaciones = $this->calcularPromedioEvaluaciones();
        $asistencias = $this->calcularEstadisticasAsistencia();

        return [
            'promedio_evaluaciones' => $promedioEvaluaciones,
            'nota_final' => $this->nota_final,
            'esta_aprobado' => $this->esta_aprobado,
            'estado_nota' => $this->estado_nota,
            'asistencias' => $asistencias,
        ];
    }

    /**
     * Actualiza la nota final basándose en el promedio de evaluaciones
     */
    public function actualizarNotaFinal()
    {
        $promedio = $this->calcularPromedioEvaluaciones();
        
        if ($promedio !== null) {
            $this->nota_final = $promedio;
            $this->save();
            return true;
        }
        
        return false;
    }

    /**
     * Completa la inscripción si cumple las condiciones
     */
    public function completarSiCumpleCondiciones()
    {
        if (!$this->seccion_id || !$this->seccion) {
            return false;
        }

        $periodo = $this->seccion->periodo;
        if (!$periodo || $periodo->estado !== 'completado') {
            return false;
        }

        $evaluaciones = $this->seccion->evaluaciones;
        if ($evaluaciones->isEmpty()) {
            return false;
        }

        $totalEvaluaciones = $evaluaciones->count();
        $evaluacionesCalificadas = 0;

        foreach ($evaluaciones as $evaluacion) {
            $calificacion = Calificacion::where('evaluacion_id', $evaluacion->id)
                ->where('estudiante_id', $this->estudiante_id)
                ->where('estado', 'calificada')
                ->first();
                
            if ($calificacion) {
                $evaluacionesCalificadas++;
            }
        }

        if ($evaluacionesCalificadas >= $totalEvaluaciones) {
            $this->estado = 'completado';
            $this->actualizarNotaFinal();
            $this->save();
            return true;
        }

        return false;
    }

    /**
     * Verificar si tiene relaciones válidas
     */
    public function tieneRelacionesValidas(): bool
    {
        try {
            return $this->estudiante !== null 
                && $this->seccion !== null 
                && $this->seccion->curso !== null;
        } catch (\Exception $e) {
            return false;
        }
    }
}