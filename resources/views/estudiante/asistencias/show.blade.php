@extends('layouts.app')

@section('title', 'Detalle de Asistencia')
@section('page-title', 'Detalle de Asistencia')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header-dark mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-calendar-check me-3"></i>Detalle de Asistencia
                </h1>
                <p class="header-subtitle mb-0">{{ $asistencia->fecha->format('d/m/Y') }}</p>
            </div>
            <a href="{{ route('estudiante.asistencias.materia', $asistencia->inscripcion_id) }}" class="btn-outline-neon">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Información Principal -->
        <div class="col-lg-8">
            <div class="card-dark mb-4">
                <div class="card-header-dark">
                    <h5><i class="fas fa-info-circle me-2"></i>Información de la Asistencia</h5>
                </div>
                <div class="card-body-dark">
                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="detail-label">
                                <i class="fas fa-calendar me-2"></i>Fecha
                            </div>
                            <div class="detail-value">
                                {{ $asistencia->fecha->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">
                                <i class="fas fa-book me-2"></i>Materia
                            </div>
                            <div class="detail-value">
                                {{ $asistencia->inscripcion->seccion->curso->nombre }}
                                <span class="badge-code-sm">{{ $asistencia->inscripcion->seccion->curso->codigo_curso }}</span>
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">
                                <i class="fas fa-user-tie me-2"></i>Profesor
                            </div>
                            <div class="detail-value">
                                {{ $asistencia->inscripcion->seccion->profesor->nombre_completo }}
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">
                                <i class="fas fa-users me-2"></i>Sección
                            </div>
                            <div class="detail-value">
                                {{ $asistencia->inscripcion->seccion->codigo_seccion }}
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">
                                <i class="fas fa-clock me-2"></i>Hora de Registro
                            </div>
                            <div class="detail-value">
                                {{ $asistencia->created_at->format('H:i:s') }}
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">
                                <i class="fas fa-calendar-alt me-2"></i>Período
                            </div>
                            <div class="detail-value">
                                {{ $asistencia->inscripcion->seccion->periodo->nombre }}
                            </div>
                        </div>
                    </div>

                    @if($asistencia->observaciones)
                        <div class="observations-section">
                            <h6><i class="fas fa-comment-dots me-2"></i>Observaciones</h6>
                            <div class="observations-content">
                                {{ $asistencia->observaciones }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Estado y Estadísticas -->
        <div class="col-lg-4">
            <!-- Estado de Asistencia -->
            <div class="card-dark mb-4">
                <div class="card-header-dark">
                    <h5><i class="fas fa-check-circle me-2"></i>Estado</h5>
                </div>
                <div class="card-body-dark text-center">
                    <div class="status-display {{ $asistencia->estado }}">
                        <div class="status-icon">
                            @switch($asistencia->estado)
                                @case('presente')
                                    <i class="fas fa-check"></i>
                                    @break
                                @case('tardanza')
                                    <i class="fas fa-clock"></i>
                                    @break
                                @case('ausente')
                                    <i class="fas fa-times"></i>
                                    @break
                                @case('justificada')
                                    <i class="fas fa-file-alt"></i>
                                    @break
                            @endswitch
                        </div>
                        <h3 class="status-text">
                            @switch($asistencia->estado)
                                @case('presente')
                                    Presente
                                    @break
                                @case('tardanza')
                                    Tardanza
                                    @break
                                @case('ausente')
                                    Ausente
                                    @break
                                @case('justificada')
                                    Justificada
                                    @break
                            @endswitch
                        </h3>
                    </div>
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="card-dark">
                <div class="card-header-dark">
                    <h5><i class="fas fa-bolt me-2"></i>Acciones</h5>
                </div>
                <div class="card-body-dark">
                    <a href="{{ route('estudiante.asistencias.calendario', $asistencia->inscripcion_id) }}" 
                       class="action-link">
                        <div class="action-icon bg-primary">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="action-text">
                            <strong>Ver Calendario</strong>
                            <small>Calendario mensual</small>
                        </div>
                        <i class="fas fa-chevron-right"></i>
                    </a>

                    <a href="{{ route('estudiante.asistencias.materia', $asistencia->inscripcion_id) }}" 
                       class="action-link">
                        <div class="action-icon bg-info">
                            <i class="fas fa-list"></i>
                        </div>
                        <div class="action-text">
                            <strong>Lista Completa</strong>
                            <small>Todas las asistencias</small>
                        </div>
                        <i class="fas fa-chevron-right"></i>
                    </a>

                    <a href="{{ route('estudiante.asistencias.index') }}" 
                       class="action-link">
                        <div class="action-icon bg-success">
                            <i class="fas fa-home"></i>
                        </div>
                        <div class="action-text">
                            <strong>Mis Asistencias</strong>
                            <small>Vista general</small>
                        </div>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    :root {
        --neon-cyan: #00ffff;
        --neon-blue: #00d4ff;
    }

    .page-header-dark {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.95) 100%);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.15);
    }

    .header-title {
        color: var(--neon-cyan);
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .header-subtitle { color: #cbd5e1; font-weight: 500; }

    .card-dark {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.95) 100%);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.15);
    }

    .card-header-dark {
        background: linear-gradient(135deg, rgba(14, 165, 233, 0.2) 0%, rgba(0, 212, 255, 0.1) 100%);
        border-bottom: 1px solid rgba(0, 212, 255, 0.3);
        padding: 1.5rem;
    }

    .card-header-dark h5 {
        color: var(--neon-cyan);
        font-weight: 600;
        margin: 0;
    }

    .card-body-dark { padding: 1.5rem; }

    /* Detail Grid */
    .detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .detail-item {
        padding: 1.25rem;
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .detail-item:hover {
        background: rgba(0, 212, 255, 0.05);
        border-color: rgba(0, 212, 255, 0.4);
        transform: translateY(-2px);
    }

    .detail-label {
        color: #94a3b8;
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
    }

    .detail-value {
        color: #f1f5f9;
        font-size: 1rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .badge-code-sm {
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        color: #0f172a;
        padding: 0.2rem 0.5rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.7rem;
        box-shadow: 0 0 8px rgba(0, 212, 255, 0.3);
    }

    /* Observations */
    .observations-section {
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid rgba(0, 212, 255, 0.2);
    }

    .observations-section h6 {
        color: var(--neon-cyan);
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .observations-content {
        padding: 1rem;
        background: rgba(0, 212, 255, 0.05);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
        color: #cbd5e1;
        line-height: 1.6;
        font-style: italic;
    }

    /* Status Display */
    .status-display {
        padding: 2rem;
    }

    .status-icon {
        width: 100px;
        height: 100px;
        margin: 0 auto 1.5rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        color: white;
        position: relative;
    }

    .status-icon::before {
        content: '';
        position: absolute;
        inset: -10px;
        border-radius: 50%;
        padding: 10px;
        background: inherit;
        opacity: 0.3;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 0.3; }
        50% { transform: scale(1.1); opacity: 0.1; }
    }

    .status-display.presente .status-icon {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        box-shadow: 0 0 30px rgba(16, 185, 129, 0.5);
    }

    .status-display.tardanza .status-icon {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        box-shadow: 0 0 30px rgba(245, 158, 11, 0.5);
    }

    .status-display.ausente .status-icon {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        box-shadow: 0 0 30px rgba(239, 68, 68, 0.5);
    }

    .status-display.justificada .status-icon {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
        box-shadow: 0 0 30px rgba(6, 182, 212, 0.5);
    }

    .status-text {
        color: var(--neon-cyan);
        font-size: 1.75rem;
        font-weight: 700;
        text-transform: uppercase;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
        margin: 0;
    }

    /* Action Links */
    .action-link {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
        margin-bottom: 0.75rem;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .action-link:hover {
        background: rgba(0, 212, 255, 0.1);
        border-color: rgba(0, 212, 255, 0.5);
        transform: translateX(5px);
    }

    .action-icon {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
        flex-shrink: 0;
    }

    .action-icon.bg-primary { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); }
    .action-icon.bg-info { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }
    .action-icon.bg-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }

    .action-text {
        flex: 1;
    }

    .action-text strong {
        display: block;
        color: #f1f5f9;
        font-weight: 600;
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }

    .action-text small {
        color: #94a3b8;
        font-size: 0.75rem;
    }

    .action-link > i:last-child {
        color: #94a3b8;
        font-size: 0.875rem;
    }

    .btn-outline-neon {
        background: transparent;
        border: 2px solid var(--neon-cyan);
        color: var(--neon-cyan);
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: all 0.3s ease;
    }

    .btn-outline-neon:hover {
        background: rgba(0, 212, 255, 0.1);
        transform: translateY(-2px);
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
        color: var(--neon-cyan);
    }

    @media (max-width: 768px) {
        .detail-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush
@endsection
