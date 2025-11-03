@extends('layouts.app')

@section('title', 'Calificación con Problemas')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            {{-- Alerta de problemas --}}
            <div class="alert alert-danger-neon mb-4">
                <h4><i class="fas fa-exclamation-triangle me-2"></i>Esta calificación tiene datos incompletos</h4>
                <p class="mb-0">No se puede mostrar completamente porque faltan datos relacionados.</p>
            </div>

            {{-- Card Principal --}}
            <div class="card-dark">
                <div class="card-header-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-clipboard-check me-2"></i>Calificación ID: {{ $calificacion->id }}
                    </h5>
                </div>

                <div class="card-body-dark">
                    
                    {{-- Datos que SÍ existen --}}
                    <div class="section-header mb-4">
                        <i class="fas fa-check-circle me-2"></i>
                        <h6>Datos Disponibles</h6>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-box">
                                <label>Nota:</label>
                                <p>{{ $calificacion->nota_formateada ?? 'Sin calificar' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <label>Estado:</label>
                                <p>{{ ucfirst($calificacion->estado) }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <label>Fecha de calificación:</label>
                                <p>{{ $calificacion->fecha_calificacion ? $calificacion->fecha_calificacion->format('d/m/Y H:i') : 'No registrada' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-box">
                                <label>ID Estudiante:</label>
                                <p>{{ $calificacion->estudiante_id }}</p>
                            </div>
                        </div>
                    </div>

                    @if($calificacion->comentarios)
                        <div class="comments-box mb-4">
                            <label>Comentarios:</label>
                            <p>{{ $calificacion->comentarios }}</p>
                        </div>
                    @endif

                    {{-- Problemas detectados --}}
                    <div class="section-header mb-4">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <h6>Problemas Detectados</h6>
                    </div>

                    <div class="alert alert-warning-dark mb-4">
                        <ul class="mb-0">
                            @foreach($problemas as $problema)
                                <li>
                                    <strong>{{ ucfirst($problema['tipo']) }}:</strong> 
                                    {{ $problema['mensaje'] }}
                                    <br><small>ID faltante: {{ $problema['id_faltante'] }}</small>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Opciones de acción --}}
                    <div class="section-header mb-4">
                        <i class="fas fa-tools me-2"></i>
                        <h6>¿Qué deseas hacer?</h6>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.calificaciones.index') }}" class="btn-outline-neon">
                            <i class="fas fa-arrow-left me-2"></i>Volver al Listado
                        </a>
                        
                        <button type="button" 
                                class="btn-danger-neon"
                                onclick="confirmarEliminacion({{ $calificacion->id }})">
                            <i class="fas fa-trash me-2"></i>Eliminar Calificación
                        </button>
                    </div>

                    <form id="delete-form" 
                          action="{{ route('admin.calificaciones.destroy', $calificacion) }}" 
                          method="POST" 
                          style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .alert-danger-neon {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.2) 0%, rgba(239, 68, 68, 0.1) 100%);
        border: 2px solid #ef4444;
        color: #ef4444;
        border-radius: 12px;
        padding: 1.5rem;
    }

    .alert-warning-dark {
        background: rgba(245, 158, 11, 0.1);
        border: 1px solid rgba(245, 158, 11, 0.3);
        border-radius: 10px;
        color: #f59e0b;
        padding: 1rem;
    }

    .alert-warning-dark ul {
        padding-left: 1.5rem;
    }

    .info-box {
        background: rgba(0, 212, 255, 0.05);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .info-box label {
        color: #94a3b8;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
        display: block;
    }

    .info-box p {
        color: #e2e8f0;
        font-weight: 600;
        margin: 0;
    }

    .comments-box {
        background: rgba(0, 212, 255, 0.05);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
        padding: 1rem;
    }

    .comments-box label {
        color: #94a3b8;
        font-weight: 500;
        margin-bottom: 0.5rem;
        display: block;
    }

    .comments-box p {
        color: #e2e8f0;
        margin: 0;
    }

    .section-header {
        display: flex;
        align-items: center;
        color: var(--neon-cyan);
        padding-bottom: 0.75rem;
        border-bottom: 2px solid rgba(0, 212, 255, 0.3);
    }

    .section-header i {
        font-size: 1.25rem;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .section-header h6 {
        margin: 0;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.875rem;
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: center;
        padding-top: 2rem;
        border-top: 1px solid rgba(0, 212, 255, 0.2);
        flex-wrap: wrap;
    }

    .btn-outline-neon {
        background: transparent;
        border: 2px solid var(--neon-cyan);
        color: var(--neon-cyan);
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .btn-outline-neon:hover {
        background: rgba(0, 212, 255, 0.1);
        transform: translateY(-2px);
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
        color: var(--neon-cyan);
    }

    .btn-danger-neon {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        border: none;
        color: white;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        box-shadow: 0 0 20px rgba(239, 68, 68, 0.3);
        cursor: pointer;
    }

    .btn-danger-neon:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 30px rgba(239, 68, 68, 0.6);
    }

    .card-dark {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.95) 100%);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 0 30px rgba(0, 212, 255, 0.2);
    }

    .card-header-dark {
        background: linear-gradient(135deg, rgba(14, 165, 233, 0.2) 0%, rgba(0, 212, 255, 0.1) 100%);
        border-bottom: 1px solid rgba(0, 212, 255, 0.3);
        padding: 1.5rem;
    }

    .card-header-dark h5 {
        color: var(--neon-cyan);
        font-weight: 600;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .card-body-dark {
        padding: 2rem;
    }
</style>
@endpush

@push('scripts')
<script>
    function confirmarEliminacion(id) {
        if (confirm('⚠️ ¿Estás SEGURO de eliminar esta calificación?\n\nEsta acción NO se puede deshacer y se perderán todos los datos asociados.\n\n¿Deseas continuar?')) {
            document.getElementById('delete-form').submit();
        }
    }
</script>
@endpush
@endsection