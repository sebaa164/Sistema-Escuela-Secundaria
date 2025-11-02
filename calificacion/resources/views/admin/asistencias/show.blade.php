@extends('layouts.app')

@section('title', 'Detalle de Asistencia')
@section('page-title', 'Detalle de Asistencia')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <div class="card-dark">
                <div class="card-header-dark">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <h5 class="mb-0">
                            <i class="fas fa-clipboard-check me-2"></i>Registro de Asistencia
                        </h5>
                        @php
                            $estadoConfig = [
                                'presente' => ['text' => 'Presente', 'class' => 'status-active', 'icon' => 'fa-check-circle'],
                                'tardanza' => ['text' => 'Tardanza', 'class' => 'status-warning', 'icon' => 'fa-clock'],
                                'ausente' => ['text' => 'Ausente', 'class' => 'status-inactive', 'icon' => 'fa-times-circle'],
                                'justificado' => ['text' => 'Justificado', 'class' => 'status-info', 'icon' => 'fa-file-alt'],
                            ][$asistencia->estado] ?? ['text' => 'Desconocido', 'class' => 'status-badge', 'icon' => 'fa-question'];
                        @endphp
                        <span class="status-badge {{ $estadoConfig['class'] }}">
                            <i class="fas {{ $estadoConfig['icon'] }}"></i> {{ $estadoConfig['text'] }}
                        </span>
                    </div>
                </div>

                <div class="card-body-dark">
                    <div class="row g-4">
                        {{-- Información del Estudiante --}}
                        <div class="col-12">
                            <div class="info-section">
                                <div class="info-header">
                                    <i class="fas fa-user-graduate me-2"></i>
                                    <h6>Datos del Estudiante</h6>
                                </div>
                                <div class="student-card">
                                    <div class="student-avatar bg-gradient-primary">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="student-details">
                                        <h4 class="mb-1">{{ $asistencia->inscripcion->estudiante->nombre_completo }}</h4>
                                        <p class="text-muted mb-2">
                                            <i class="fas fa-envelope me-2"></i>
                                            {{ $asistencia->inscripcion->estudiante->email }}
                                        </p>
                                        <span class="badge-neon">
                                            <i class="fas fa-id-card me-1"></i>
                                            {{ $asistencia->inscripcion->estudiante->codigo }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Información del Curso --}}
                        <div class="col-md-6">
                            <div class="info-section">
                                <div class="info-header">
                                    <i class="fas fa-book me-2"></i>
                                    <h6>Curso y Sección</h6>
                                </div>
                                <div class="info-content">
                                    <div class="detail-item">
                                        <span class="detail-label">
                                            <i class="fas fa-book-open me-1"></i>
                                            Curso:
                                        </span>
                                        <span class="detail-value">
                                            <strong>{{ $asistencia->inscripcion->seccion->curso->nombre }}</strong>
                                        </span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">
                                            <i class="fas fa-layer-group me-1"></i>
                                            Sección:
                                        </span>
                                        <span class="detail-value">
                                            <span class="badge-info">{{ $asistencia->inscripcion->seccion->codigo_seccion }}</span>
                                        </span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">
                                            <i class="fas fa-calendar-alt me-1"></i>
                                            Período:
                                        </span>
                                        <span class="detail-value">
                                            <span class="badge-success">{{ $asistencia->inscripcion->seccion->periodo->nombre }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Información de la Asistencia --}}
                        <div class="col-md-6">
                            <div class="info-section">
                                <div class="info-header">
                                    <i class="fas fa-calendar-check me-2"></i>
                                    <h6>Datos de Asistencia</h6>
                                </div>
                                <div class="info-content">
                                    <div class="detail-item">
                                        <span class="detail-label">
                                            <i class="fas fa-calendar-day me-1"></i>
                                            Fecha:
                                        </span>
                                        <span class="detail-value">
                                            <strong>{{ $asistencia->fecha->format('d/m/Y') }}</strong>
                                        </span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">
                                            <i class="fas fa-calendar-week me-1"></i>
                                            Día:
                                        </span>
                                        <span class="detail-value">
                                            {{ $asistencia->fecha->locale('es')->dayName }}
                                        </span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">
                                            <i class="fas fa-clock me-1"></i>
                                            Registrado:
                                        </span>
                                        <span class="detail-value text-muted">
                                            {{ $asistencia->created_at->format('d/m/Y H:i') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Observaciones --}}
                        @if($asistencia->observaciones)
                            <div class="col-12">
                                <div class="info-section">
                                    <div class="info-header">
                                        <i class="fas fa-sticky-note me-2"></i>
                                        <h6>Observaciones</h6>
                                    </div>
                                    <div class="observaciones-box">
                                        <i class="fas fa-quote-left quote-icon"></i>
                                        <p class="mb-0">{{ $asistencia->observaciones }}</p>
                                        <i class="fas fa-quote-right quote-icon-right"></i>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Botones de Acción --}}
            <div class="card-dark mt-4">
                <div class="card-body-dark">
                    <div class="action-buttons-large d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <a href="{{ route('admin.asistencias.index') }}" class="btn-outline-neon">
                            <i class="fas fa-arrow-left me-2"></i>Volver al Listado
                        </a>
                        <div class="d-flex gap-3 flex-wrap">
                            <a href="{{ route('admin.asistencias.edit', $asistencia) }}" class="btn-neon-lg">
                                <i class="fas fa-edit me-2"></i>Editar
                            </a>
                            <button type="button" class="btn-danger-neon" onclick="confirmarEliminacion()">
                                <i class="fas fa-trash me-2"></i>Eliminar
                            </button>
                        </div>
                    </div>

                    <form id="delete-form" 
                          action="{{ route('admin.asistencias.destroy', $asistencia) }}" 
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
    :root {
        --neon-cyan: #00d4ff;
        --neon-blue: #0ea5e9;
        --dark-bg: #0f172a;
        --dark-card: #1e293b;
        --text-color: #e2e8f0;
        --muted-text: #94a3b8;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
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
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .card-body-dark {
        padding: 2rem;
        color: var(--text-color);
    }

    .info-section {
        padding: 1.5rem;
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 12px;
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.05) 0%, rgba(14, 165, 233, 0.03) 100%);
        transition: all 0.3s ease;
    }

    .info-section:hover {
        border-color: rgba(0, 212, 255, 0.4);
        box-shadow: 0 5px 20px rgba(0, 212, 255, 0.15);
        transform: translateY(-2px);
    }

    .info-header {
        border-bottom: 2px solid var(--neon-cyan);
        padding-bottom: 0.75rem;
        margin-bottom: 1.5rem;
        color: var(--neon-cyan);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-header h6 {
        margin: 0;
        font-weight: 600;
        font-size: 1rem;
    }

    .student-card {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        padding: 1.5rem;
        background: rgba(30, 41, 59, 0.6);
        border-radius: 12px;
        border: 1px solid rgba(0, 212, 255, 0.15);
    }

    .student-avatar {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
        flex-shrink: 0;
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        box-shadow: 0 0 20px rgba(14, 165, 233, 0.5);
    }

    .student-details h4 {
        color: var(--text-color);
        font-size: 1.35rem;
        font-weight: 700;
    }

    .student-details p {
        font-size: 0.95rem;
    }

    .info-content {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .detail-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 1rem;
        background: rgba(30, 41, 59, 0.4);
        border-radius: 8px;
        border-left: 3px solid var(--neon-cyan);
        transition: all 0.3s ease;
    }

    .detail-item:hover {
        background: rgba(30, 41, 59, 0.6);
        border-left-color: var(--neon-blue);
        transform: translateX(5px);
    }

    .detail-label {
        font-weight: 600;
        color: var(--neon-blue);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .detail-value {
        color: var(--text-color);
        text-align: right;
        font-weight: 500;
    }

    .observaciones-box {
        position: relative;
        padding: 1.5rem 2rem;
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.6) 0%, rgba(15, 23, 42, 0.5) 100%);
        border-radius: 12px;
        border: 1px solid rgba(0, 212, 255, 0.2);
        color: var(--text-color);
        font-style: italic;
        line-height: 1.6;
    }

    .quote-icon {
        position: absolute;
        top: 10px;
        left: 10px;
        font-size: 1.5rem;
        color: rgba(0, 212, 255, 0.3);
    }

    .quote-icon-right {
        position: absolute;
        bottom: 10px;
        right: 10px;
        font-size: 1.5rem;
        color: rgba(0, 212, 255, 0.3);
    }

    .observaciones-box p {
        margin: 0;
        padding: 0 1rem;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .status-active {
        background: rgba(16, 185, 129, 0.2);
        color: var(--success-color);
        border: 1px solid #10b981;
    }

    .status-inactive {
        background: rgba(239, 68, 68, 0.2);
        color: var(--danger-color);
        border: 1px solid #ef4444;
    }

    .status-warning {
        background: rgba(245, 158, 11, 0.2);
        color: var(--warning-color);
        border: 1px solid #f59e0b;
    }

    .status-info {
        background: rgba(6, 182, 212, 0.2);
        color: #06b6d4;
        border: 1px solid #06b6d4;
    }

    .status-badge i {
        font-size: 0.75rem;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    .badge-neon {
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.2) 0%, rgba(14, 165, 233, 0.15) 100%);
        color: var(--neon-cyan);
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        border: 1px solid rgba(0, 212, 255, 0.3);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .badge-info {
        background: rgba(6, 182, 212, 0.2);
        color: #06b6d4;
        border: 1px solid #06b6d4;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        display: inline-block;
    }

    .badge-success {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 1px solid #10b981;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        display: inline-block;
    }

    .text-muted {
        color: var(--muted-text) !important;
    }

    .btn-neon-lg {
        background: linear-gradient(135deg, #0ea5e9 0%, #00d4ff 100%);
        border: none;
        color: var(--dark-bg);
        font-weight: 700;
        padding: 1rem 2rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
        text-decoration: none;
        display: inline-block;
    }

    .btn-neon-lg:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 30px rgba(0, 212, 255, 0.6);
        color: var(--dark-bg);
    }

    .btn-outline-neon {
        background: transparent;
        border: 2px solid var(--neon-cyan);
        color: var(--neon-cyan);
        font-weight: 600;
        padding: 1rem 2rem;
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
        padding: 1rem 2rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        box-shadow: 0 0 20px rgba(239, 68, 68, 0.3);
        cursor: pointer;
    }

    .btn-danger-neon:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 30px rgba(239, 68, 68, 0.6);
    }

    .action-buttons-large {
        padding-top: 1rem;
        border-top: 1px solid rgba(0, 212, 255, 0.1);
    }

    @media (max-width: 768px) {
        .card-body-dark {
            padding: 1.5rem;
        }

        .student-card {
            flex-direction: column;
            text-align: center;
        }

        .detail-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .detail-value {
            text-align: left;
        }

        .action-buttons-large {
            flex-direction: column;
        }

        .action-buttons-large > div {
            width: 100%;
        }

        .btn-neon-lg,
        .btn-outline-neon,
        .btn-danger-neon {
            width: 100%;
            text-align: center;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function confirmarEliminacion() {
        if (confirm('¿Estás seguro de eliminar este registro de asistencia? Esta acción no se puede deshacer.')) {
            document.getElementById('delete-form').submit();
        }
    }
</script>
@endpush
@endsection