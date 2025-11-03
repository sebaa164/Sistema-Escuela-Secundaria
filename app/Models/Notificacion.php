<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Notificacion extends Model
{
    use HasFactory;

    protected $table = 'notificaciones';

    protected $fillable = [
        'usuario_id',
        'titulo',
        'mensaje',
        'tipo',
        'leida',
        'fecha_envio',
    ];

    protected $casts = [
        'leida' => 'boolean',
        'fecha_envio' => 'timestamp',
    ];

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id'); // Cambiado a User::class y agregada la clave foránea explícita
    }

    // Scopes
    public function scopeDelUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    public function scopeNoLeidas($query)
    {
        return $query->where('leida', false);
    }

    public function scopeLeidas($query)
    {
        return $query->where('leida', true);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeRecientes($query, $dias = 7)
    {
        return $query->where('fecha_envio', '>=', Carbon::now()->subDays($dias));
    }

    public function scopeOrdenadaPorFecha($query, $direccion = 'desc')
    {
        return $query->orderBy('fecha_envio', $direccion);
    }

    // Accessors
    public function getTipoBadgeClassAttribute()
    {
        switch($this->tipo) {
            case 'info':
                return 'badge-info';
            case 'warning':
                return 'badge-warning';
            case 'success':
                return 'badge-success';
            case 'error':
                return 'badge-danger';
            default:
                return 'badge-secondary';
        }
    }

    public function getTipoIconoAttribute()
    {
        switch($this->tipo) {
            case 'info':
                return 'fas fa-info-circle';
            case 'warning':
                return 'fas fa-exclamation-triangle';
            case 'success':
                return 'fas fa-check-circle';
            case 'error':
                return 'fas fa-times-circle';
            default:
                return 'fas fa-bell';
        }
    }

    public function getFechaEnvioFormateadaAttribute()
    {
        return $this->fecha_envio->diffForHumans();
    }

    public function getFechaEnvioCompletaAttribute()
    {
        return $this->fecha_envio->format('d/m/Y H:i');
    }

    public function getEsRecienteAttribute()
    {
        return $this->fecha_envio->gt(Carbon::now()->subHours(24));
    }

    // Métodos de instancia
    public function marcarComoLeida()
    {
        $this->update(['leida' => true]);
    }

    public function marcarComoNoLeida()
    {
        $this->update(['leida' => false]);
    }

    // Métodos estáticos para crear notificaciones
    public static function enviarAUsuario($usuarioId, $titulo, $mensaje, $tipo = 'info')
    {
        return static::create([
            'usuario_id' => $usuarioId,
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'tipo' => $tipo,
            'fecha_envio' => Carbon::now(),
        ]);
    }

    public static function enviarATodos($titulo, $mensaje, $tipo = 'info')
    {
        $usuarios = User::where('activo', true)->pluck('id'); // Cambiado a User y ajustado el scope
        $notificaciones = [];
        
        foreach ($usuarios as $usuarioId) {
            $notificaciones[] = [
                'usuario_id' => $usuarioId,
                'titulo' => $titulo,
                'mensaje' => $mensaje,
                'tipo' => $tipo,
                'fecha_envio' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        
        return static::insert($notificaciones);
    }

    public static function enviarAEstudiantes($titulo, $mensaje, $tipo = 'info')
    {
        // Ajustado para usar User y asumiendo que hay un campo 'rol' o similar
        $estudiantes = User::where('rol', 'estudiante')
                          ->where('activo', true)
                          ->pluck('id');
        $notificaciones = [];
        
        foreach ($estudiantes as $estudianteId) {
            $notificaciones[] = [
                'usuario_id' => $estudianteId,
                'titulo' => $titulo,
                'mensaje' => $mensaje,
                'tipo' => $tipo,
                'fecha_envio' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        
        return static::insert($notificaciones);
    }

    public static function enviarAProfesores($titulo, $mensaje, $tipo = 'info')
    {
        // Ajustado para usar User y asumiendo que hay un campo 'rol' o similar
        $profesores = User::where('rol', 'profesor')
                         ->where('activo', true)
                         ->pluck('id');
        $notificaciones = [];
        
        foreach ($profesores as $profesorId) {
            $notificaciones[] = [
                'usuario_id' => $profesorId,
                'titulo' => $titulo,
                'mensaje' => $mensaje,
                'tipo' => $tipo,
                'fecha_envio' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        
        return static::insert($notificaciones);
    }
}