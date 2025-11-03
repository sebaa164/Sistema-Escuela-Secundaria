@extends('layouts.app')

@section('title', 'Mis Asistencias - Estudiante')
@section('page-title', 'Mis Asistencias')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header-dark mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-calendar-check me-3"></i>Mis Asistencias
                </h1>
                <p class="header-subtitle mb-0">Revisa tu asistencia en todas tus materias</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('estudiante.asistencias.reporte') }}" class="btn-action-neon">
                    <i class="fas fa-file-alt me-2"></i>Ver Reporte
                </a>
                <a href="{{ route('estudiante.dashboard') }}" class="btn-outline-neon">
                    <i class="fas fa-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card-dark mb-4">
        <div class="card-body-dark">
            <form method="GET" action="{{ route('estudiante.asistencias.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label-neon">Materia</label>
                    <select name="inscripcion_id" class="form-control-neon" onchange="this.form.submit()">
                        <option value="">Todas las materias</option>
                        @foreach($inscripciones as $insc)
                            <option value="{{ $insc->id }}" {{ $inscripcionId == $insc->id ? 'selected' : '' }}>
                                {{ $insc->seccion->curso->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label-neon">Mes</label>
                    <select name="mes" class="form-control-neon" onchange="this.form.submit()">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $mes == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create(null, $m, 1)->locale('es')->monthName }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label-neon">Año</label>
                    <select name="año" class="form-control-neon" onchange="this.form.submit()">
                        @for($y = date('Y'); $y >= date('Y') - 3; $y--)
                            <option value="{{ $y }}" {{ $año == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Resumen por Materia -->
    <div class="row g-4">
        @forelse($estadisticasPorMateria as $stats)
            @php
                $inscripcion = $stats['inscripcion'];
                $porcentaje = $stats['porcentaje_asistencia'];
                
                // Determinar estado
                $estadoClass = '';
                $estadoTexto = '';
                $estadoIcon = '';
                
                if ($porcentaje >= 90) {
                    $estadoClass = 'success';
                    $estadoTexto = 'Excelente';
                    $estadoIcon = 'star';
                } elseif ($porcentaje >= 80) {
                    $estadoClass = 'info';
                    $estadoTexto = 'Buena';
                    $estadoIcon = 'thumbs-up';
                } elseif ($porcentaje >= 70) {
                    $estadoClass = 'warning';
                    $estadoTexto = 'Regular';
                    $estadoIcon = 'exclamation-triangle';
                } else {
                    $estadoClass = 'danger';
                    $estadoTexto = 'Deficiente';
                    $estadoIcon = 'exclamation-circle';
                }
            @endphp
            
            <div class="col-lg-6">
                <div class="attendance-card">
                    <div class="attendance-header">
                        <div class="attendance-icon bg-gradient-{{ $estadoClass }}">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="attendance-title">
                            <h5>{{ $inscripcion->seccion->curso->nombre }}</h5>
                            <div class="d-flex gap-2 align-items-center flex-wrap">
                                <span class="badge-neon-sm">{{ $inscripcion->seccion->curso->codigo_curso }}</span>
                                <span class="text-muted-neon">
                                    <i class="fas fa-user-tie"></i>
                                    {{ $inscripcion->seccion->profesor->nombre_completo }}
                                </span>
                            </div>
                        </div>
                        <div class="attendance-badge">
                            <span class="badge badge-{{ $estadoClass }}">
                                <i class="fas fa-{{ $estadoIcon }} me-1"></i>
                                {{ $estadoTexto }}
                            </span>
                        </div>
                    </div>

                    <div class="attendance-body">
                        <!-- Porcentaje Principal -->
                        <div class="percentage-main">
                            <div class="percentage-circle-lg {{ $estadoClass }}">
                                <svg viewBox="0 0 100 100">
                                    <circle class="circle-bg" cx="50" cy="50" r="45"></circle>
                                    <circle class="circle-progress" cx="50" cy="50" r="45" 
                                            style="stroke-dashoffset: {{ 283 - (283 * $porcentaje / 100) }}"></circle>
                                </svg>
                                <div class="percentage-text">
                                    <span class="percentage-number">{{ number_format($porcentaje, 1) }}%</span>
                                    <span class="percentage-label">Asistencia</span>
                                </div>
                            </div>
                        </div>

                        <!-- Estadísticas -->
                        <div class="stats-row">
                            <div class="stat-mini">
                                <i class="fas fa-calendar-day text-primary"></i>
                                <div>
                                    <strong>{{ $stats['total_clases'] }}</strong>
                                    <small>Total</small>
                                </div>
                            </div>
                            <div class="stat-mini">
                                <i class="fas fa-check text-success"></i>
                                <div>
                                    <strong>{{ $stats['presentes'] }}</strong>
                                    <small>Presentes</small>
                                </div>
                            </div>
                            <div class="stat-mini">
                                <i class="fas fa-clock text-warning"></i>
                                <div>
                                    <strong>{{ $stats['tardanzas'] }}</strong>
                                    <small>Tardanzas</small>
                                </div>
                            </div>
                            <div class="stat-mini">
                                <i class="fas fa-times text-danger"></i>
                                <div>
                                    <strong>{{ $stats['ausentes'] }}</strong>
                                    <small>Ausentes</small>
                                </div>
                            </div>
                        </div>

                        <!-- Acciones -->
                        <div class="attendance-actions">
                            <a href="{{ route('estudiante.asistencias.materia', $inscripcion->id) }}" 
                               class="btn-action-attendance">
                                <i class="fas fa-list me-2"></i>Ver Detalle
                            </a>
                            <a href="{{ route('estudiante.asistencias.calendario', $inscripcion->id) }}" 
                               class="btn-action-attendance">
                                <i class="fas fa-calendar-alt me-2"></i>Calendario
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="empty-state-compact">
                    <i class="fas fa-calendar-check fa-3x mb-3"></i>
                    <h5>No hay registros de asistencia</h5>
                    <p>Aún no tienes asistencias registradas para el período seleccionado</p>
                    <a href="{{ route('estudiante.inscripciones.index') }}" class="btn-outline-neon mt-3">
                        <i class="fas fa-book me-2"></i>Ver Mis Materias
                    </a>
                </div>
            </div>
        @endforelse
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

    .card-body-dark { padding: 1.5rem; }

    .form-label-neon {
        color: var(--neon-cyan);
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-control-neon {
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(0, 212, 255, 0.3);
        color: #f1f5f9;
        padding: 0.75rem 1rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        width: 100%;
    }

    .form-control-neon:focus {
        background: rgba(15, 23, 42, 0.7);
        border-color: var(--neon-cyan);
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
        color: #f1f5f9;
        outline: none;
    }

    .form-control-neon option {
        background: #1e293b;
        color: #f1f5f9;
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

    /* Attendance Cards */
    .attendance-card {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.95) 100%);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 15px;
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%;
    }

    .attendance-card:hover {
        border-color: rgba(0, 212, 255, 0.5);
        box-shadow: 0 0 25px rgba(0, 212, 255, 0.2);
        transform: translateY(-5px);
    }

    .attendance-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.5rem;
        border-bottom: 1px solid rgba(0, 212, 255, 0.2);
        background: rgba(0, 212, 255, 0.05);
    }

    .attendance-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        flex-shrink: 0;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
    }

    .bg-gradient-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .bg-gradient-info { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }
    .bg-gradient-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .bg-gradient-danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }

    .attendance-title {
        flex: 1;
    }

    .attendance-title h5 {
        color: #f1f5f9;
        font-weight: 600;
        margin: 0 0 0.5rem 0;
        font-size: 1.1rem;
    }

    .text-muted-neon {
        color: #94a3b8;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
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

    .attendance-badge {
        flex-shrink: 0;
    }

    .badge {
        padding: 0.5rem 1rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.75rem;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        white-space: nowrap;
    }

    .badge-success {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 1px solid #10b981;
    }

    .badge-info {
        background: rgba(6, 182, 212, 0.2);
        color: #06b6d4;
        border: 1px solid #06b6d4;
    }

    .badge-warning {
        background: rgba(245, 158, 11, 0.2);
        color: #f59e0b;
        border: 1px solid #f59e0b;
    }

    .badge-danger {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 1px solid #ef4444;
    }

    .attendance-body {
        padding: 1.5rem;
    }

    /* Percentage Circle */
    .percentage-main {
        display: flex;
        justify-content: center;
        margin-bottom: 1.5rem;
    }

    .percentage-circle-lg {
        position: relative;
        width: 140px;
        height: 140px;
    }

    .percentage-circle-lg svg {
        transform: rotate(-90deg);
        width: 100%;
        height: 100%;
    }

    .circle-bg {
        fill: none;
        stroke: rgba(100, 116, 139, 0.2);
        stroke-width: 8;
    }

    .circle-progress {
        fill: none;
        stroke-width: 8;
        stroke-linecap: round;
        stroke-dasharray: 283;
        transition: stroke-dashoffset 1s ease;
    }

    .percentage-circle-lg.success .circle-progress {
        stroke: #10b981;
        filter: drop-shadow(0 0 10px rgba(16, 185, 129, 0.5));
    }

    .percentage-circle-lg.info .circle-progress {
        stroke: #06b6d4;
        filter: drop-shadow(0 0 10px rgba(6, 182, 212, 0.5));
    }

    .percentage-circle-lg.warning .circle-progress {
        stroke: #f59e0b;
        filter: drop-shadow(0 0 10px rgba(245, 158, 11, 0.5));
    }

    .percentage-circle-lg.danger .circle-progress {
        stroke: #ef4444;
        filter: drop-shadow(0 0 10px rgba(239, 68, 68, 0.5));
    }

    .percentage-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
    }

    .percentage-number {
        display: block;
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--neon-cyan);
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .percentage-label {
        display: block;
        font-size: 0.75rem;
        color: #94a3b8;
        text-transform: uppercase;
        font-weight: 600;
    }

    /* Stats Row */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .stat-mini {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem;
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .stat-mini:hover {
        background: rgba(0, 212, 255, 0.05);
        border-color: rgba(0, 212, 255, 0.4);
    }

    .stat-mini i {
        font-size: 1.25rem;
    }

    .stat-mini strong {
        display: block;
        color: #f1f5f9;
        font-size: 1.1rem;
        font-weight: 700;
    }

    .stat-mini small {
        display: block;
        color: #94a3b8;
        font-size: 0.7rem;
        text-transform: uppercase;
        font-weight: 600;
    }

    .text-primary { color: #0ea5e9; }
    .text-success { color: #10b981; }
    .text-warning { color: #f59e0b; }
    .text-danger { color: #ef4444; }

    /* Actions */
    .attendance-actions {
        display: flex;
        gap: 0.75rem;
    }

    .btn-action-attendance {
        flex: 1;
        padding: 0.875rem 1rem;
        background: rgba(0, 212, 255, 0.1);
        border: 1px solid rgba(0, 212, 255, 0.3);
        color: var(--neon-cyan);
        border-radius: 10px;
        text-decoration: none;
        text-align: center;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-action-attendance:hover {
        background: rgba(0, 212, 255, 0.2);
        border-color: var(--neon-cyan);
        transform: translateY(-2px);
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
        color: var(--neon-cyan);
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
        .attendance-header {
            flex-wrap: wrap;
        }

        .stats-row {
            grid-template-columns: repeat(2, 1fr);
        }

        .attendance-actions {
            flex-direction: column;
        }

        .btn-action-attendance {
            width: 100%;
        }
    }
</style>
@endpush
@endsection
