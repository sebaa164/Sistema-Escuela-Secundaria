@extends('layouts.app')

@section('title', 'Asistencias - ' . $inscripcion->seccion->curso->nombre)
@section('page-title', 'Asistencias por Materia')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header-dark mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-calendar-check me-3"></i>{{ $inscripcion->seccion->curso->nombre }}
                </h1>
                <p class="header-subtitle mb-0">
                    {{ $inscripcion->seccion->curso->codigo_curso }} - Asistencias Detalladas
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('estudiante.asistencias.calendario', $inscripcion->id) }}" class="btn-action-neon">
                    <i class="fas fa-calendar-alt me-2"></i>Ver Calendario
                </a>
                <a href="{{ route('estudiante.asistencias.index') }}" class="btn-outline-neon">
                    <i class="fas fa-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Información del Curso -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="info-card">
                <div class="info-icon bg-primary">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="info-content">
                    <small>Profesor</small>
                    <strong>{{ $inscripcion->seccion->profesor->nombre_completo }}</strong>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-card">
                <div class="info-icon bg-info">
                    <i class="fas fa-calendar"></i>
                </div>
                <div class="info-content">
                    <small>Período</small>
                    <strong>{{ $inscripcion->seccion->periodo->nombre }}</strong>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-card">
                <div class="info-icon bg-success">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="info-content">
                    <small>Asistencia</small>
                    <strong>{{ number_format($estadisticas['porcentaje_asistencia'], 1) }}%</strong>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-card">
                <div class="info-icon bg-warning">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="info-content">
                    <small>Total Clases</small>
                    <strong>{{ $estadisticas['total_clases'] }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas Detalladas -->
    <div class="card-dark mb-4">
        <div class="card-header-dark">
            <h5><i class="fas fa-chart-pie me-2"></i>Resumen de Asistencia</h5>
        </div>
        <div class="card-body-dark">
            <div class="stats-grid-detailed">
                <div class="stat-detail presente">
                    <div class="stat-detail-icon">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="stat-detail-content">
                        <h3>{{ $estadisticas['presentes'] }}</h3>
                        <p>Presentes</p>
                        <div class="stat-bar">
                            <div class="stat-bar-fill bg-success" style="width: {{ $estadisticas['total_clases'] > 0 ? ($estadisticas['presentes'] / $estadisticas['total_clases']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="stat-detail tardanza">
                    <div class="stat-detail-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-detail-content">
                        <h3>{{ $estadisticas['tardanzas'] }}</h3>
                        <p>Tardanzas</p>
                        <div class="stat-bar">
                            <div class="stat-bar-fill bg-warning" style="width: {{ $estadisticas['total_clases'] > 0 ? ($estadisticas['tardanzas'] / $estadisticas['total_clases']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="stat-detail ausente">
                    <div class="stat-detail-icon">
                        <i class="fas fa-times"></i>
                    </div>
                    <div class="stat-detail-content">
                        <h3>{{ $estadisticas['ausentes'] }}</h3>
                        <p>Ausentes</p>
                        <div class="stat-bar">
                            <div class="stat-bar-fill bg-danger" style="width: {{ $estadisticas['total_clases'] > 0 ? ($estadisticas['ausentes'] / $estadisticas['total_clases']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="stat-detail justificada">
                    <div class="stat-detail-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stat-detail-content">
                        <h3>{{ $estadisticas['justificadas'] }}</h3>
                        <p>Justificadas</p>
                        <div class="stat-bar">
                            <div class="stat-bar-fill bg-info" style="width: {{ $estadisticas['total_clases'] > 0 ? ($estadisticas['justificadas'] / $estadisticas['total_clases']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Asistencias -->
    <div class="card-dark">
        <div class="card-header-dark">
            <div class="d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-list me-2"></i>Registro de Asistencias</h5>
                <span class="badge-neon-sm">{{ $asistencias->count() }} registros</span>
            </div>
        </div>
        <div class="card-body-dark">
            @forelse($asistencias as $asistencia)
                <div class="attendance-item">
                    <div class="attendance-item-date">
                        <div class="date-badge">
                            <span class="day">{{ $asistencia->fecha->format('d') }}</span>
                            <span class="month">{{ $asistencia->fecha->locale('es')->monthName }}</span>
                            <span class="year">{{ $asistencia->fecha->format('Y') }}</span>
                        </div>
                    </div>
                    
                    <div class="attendance-item-content">
                        <div class="attendance-item-header">
                            <h6>{{ $asistencia->fecha->locale('es')->dayName }}</h6>
                            <span class="badge-status {{ $asistencia->estado }}">
                                @switch($asistencia->estado)
                                    @case('presente')
                                        <i class="fas fa-check me-1"></i>Presente
                                        @break
                                    @case('tardanza')
                                        <i class="fas fa-clock me-1"></i>Tardanza
                                        @break
                                    @case('ausente')
                                        <i class="fas fa-times me-1"></i>Ausente
                                        @break
                                    @case('justificada')
                                        <i class="fas fa-file-alt me-1"></i>Justificada
                                        @break
                                @endswitch
                            </span>
                        </div>
                        
                        @if($asistencia->observaciones)
                            <div class="attendance-item-obs">
                                <i class="fas fa-comment-dots me-2"></i>
                                <span>{{ $asistencia->observaciones }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="attendance-item-time">
                        <small>{{ $asistencia->created_at->format('H:i') }}</small>
                    </div>
                </div>
            @empty
                <div class="empty-state-compact">
                    <i class="fas fa-calendar-times fa-3x mb-3"></i>
                    <h5>No hay registros de asistencia</h5>
                    <p>Aún no se han registrado asistencias para esta materia</p>
                </div>
            @endforelse
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

    /* Info Cards */
    .info-card {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.95) 100%);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 12px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.3s ease;
    }

    .info-card:hover {
        border-color: rgba(0, 212, 255, 0.5);
        transform: translateY(-3px);
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.2);
    }

    .info-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
        flex-shrink: 0;
    }

    .info-icon.bg-primary { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); }
    .info-icon.bg-info { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }
    .info-icon.bg-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .info-icon.bg-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }

    .info-content small {
        display: block;
        color: #94a3b8;
        font-size: 0.75rem;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .info-content strong {
        color: #f1f5f9;
        font-size: 1.25rem;
        font-weight: 700;
    }

    /* Stats Grid Detailed */
    .stats-grid-detailed {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
    }

    .stat-detail {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.5rem;
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .stat-detail:hover {
        background: rgba(0, 212, 255, 0.05);
        border-color: rgba(0, 212, 255, 0.4);
        transform: scale(1.02);
    }

    .stat-detail-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        flex-shrink: 0;
    }

    .stat-detail.presente .stat-detail-icon {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        box-shadow: 0 0 15px rgba(16, 185, 129, 0.5);
    }

    .stat-detail.tardanza .stat-detail-icon {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        box-shadow: 0 0 15px rgba(245, 158, 11, 0.5);
    }

    .stat-detail.ausente .stat-detail-icon {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        box-shadow: 0 0 15px rgba(239, 68, 68, 0.5);
    }

    .stat-detail.justificada .stat-detail-icon {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
        box-shadow: 0 0 15px rgba(6, 182, 212, 0.5);
    }

    .stat-detail-content {
        flex: 1;
    }

    .stat-detail-content h3 {
        color: var(--neon-cyan);
        font-size: 2rem;
        font-weight: 700;
        margin: 0 0 0.25rem 0;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .stat-detail-content p {
        color: #cbd5e1;
        margin: 0 0 0.5rem 0;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .stat-bar {
        height: 6px;
        background: rgba(100, 116, 139, 0.2);
        border-radius: 10px;
        overflow: hidden;
    }

    .stat-bar-fill {
        height: 100%;
        border-radius: 10px;
        transition: width 0.6s ease;
    }

    .stat-bar-fill.bg-success {
        background: linear-gradient(90deg, #10b981 0%, #34d399 100%);
        box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
    }

    .stat-bar-fill.bg-warning {
        background: linear-gradient(90deg, #f59e0b 0%, #fbbf24 100%);
        box-shadow: 0 0 10px rgba(245, 158, 11, 0.5);
    }

    .stat-bar-fill.bg-danger {
        background: linear-gradient(90deg, #ef4444 0%, #f87171 100%);
        box-shadow: 0 0 10px rgba(239, 68, 68, 0.5);
    }

    .stat-bar-fill.bg-info {
        background: linear-gradient(90deg, #06b6d4 0%, #22d3ee 100%);
        box-shadow: 0 0 10px rgba(6, 182, 212, 0.5);
    }

    /* Attendance Items */
    .attendance-item {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        padding: 1.25rem;
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 12px;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    .attendance-item:hover {
        background: rgba(0, 212, 255, 0.05);
        border-color: rgba(0, 212, 255, 0.4);
        transform: translateX(5px);
    }

    .attendance-item-date {
        flex-shrink: 0;
    }

    .date-badge {
        width: 70px;
        padding: 0.75rem;
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.2) 0%, rgba(14, 165, 233, 0.2) 100%);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 10px;
        text-align: center;
    }

    .date-badge .day {
        display: block;
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--neon-cyan);
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .date-badge .month {
        display: block;
        font-size: 0.75rem;
        color: #cbd5e1;
        text-transform: uppercase;
        font-weight: 600;
    }

    .date-badge .year {
        display: block;
        font-size: 0.7rem;
        color: #94a3b8;
    }

    .attendance-item-content {
        flex: 1;
    }

    .attendance-item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .attendance-item-header h6 {
        color: #f1f5f9;
        font-weight: 600;
        margin: 0;
        text-transform: capitalize;
    }

    .badge-status {
        padding: 0.375rem 0.75rem;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
    }

    .badge-status.presente {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 1px solid #10b981;
    }

    .badge-status.tardanza {
        background: rgba(245, 158, 11, 0.2);
        color: #f59e0b;
        border: 1px solid #f59e0b;
    }

    .badge-status.ausente {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 1px solid #ef4444;
    }

    .badge-status.justificada {
        background: rgba(6, 182, 212, 0.2);
        color: #06b6d4;
        border: 1px solid #06b6d4;
    }

    .attendance-item-obs {
        display: flex;
        align-items: center;
        color: #94a3b8;
        font-size: 0.875rem;
        font-style: italic;
    }

    .attendance-item-time {
        flex-shrink: 0;
        text-align: right;
    }

    .attendance-item-time small {
        color: #64748b;
        font-size: 0.75rem;
    }

    .btn-action-neon {
        padding: 0.75rem 1.5rem;
        background: rgba(0, 212, 255, 0.1);
        border: 1px solid rgba(0, 212, 255, 0.3);
        color: var(--neon-cyan);
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
    }

    .btn-action-neon:hover {
        background: rgba(0, 212, 255, 0.2);
        border-color: var(--neon-cyan);
        transform: translateY(-2px);
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
        color: var(--neon-cyan);
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

    .badge-neon-sm {
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        color: #0f172a;
        padding: 0.25rem 0.6rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.7rem;
        box-shadow: 0 0 8px rgba(0, 212, 255, 0.3);
    }

    .empty-state-compact {
        text-align: center;
        padding: 4rem 2rem;
        color: #94a3b8;
    }

    .empty-state-compact i {
        color: rgba(0, 212, 255, 0.3);
    }

    .empty-state-compact h5 {
        color: #f1f5f9;
        margin: 1rem 0 0.5rem 0;
    }

    .empty-state-compact p {
        color: #cbd5e1;
    }

    @media (max-width: 768px) {
        .stats-grid-detailed {
            grid-template-columns: 1fr;
        }

        .attendance-item {
            flex-direction: column;
            align-items: flex-start;
        }

        .attendance-item-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
    }
</style>
@endpush
@endsection
