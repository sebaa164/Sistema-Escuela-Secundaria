@extends('layouts.app')

@section('title', 'Progreso Académico - Estudiante')
@section('page-title', 'Progreso Académico')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header-dark mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-chart-line me-3"></i>Progreso Académico
                </h1>
                <p class="header-subtitle mb-0">Monitorea tu avance en cada materia</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('estudiante.calificaciones.index') }}" class="btn-outline-neon">
                    <i class="fas fa-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Filtro por Período -->
    <div class="card-dark mb-4">
        <div class="card-body-dark">
            <form method="GET" action="{{ route('estudiante.calificaciones.progreso') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label-neon">Período Académico</label>
                    <select name="periodo_id" class="form-control-neon" onchange="this.form.submit()">
                        <option value="">Todos los períodos</option>
                        @foreach($periodos as $periodo)
                            <option value="{{ $periodo->id }}" {{ $periodoId == $periodo->id ? 'selected' : '' }}>
                                {{ $periodo->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Progreso por Materia -->
    <div class="row g-4">
        @forelse($progreso as $item)
            @php
                $inscripcion = $item['inscripcion'];
                $porcentajeCompletado = $item['porcentaje_completado'];
                $notaActual = $item['nota_actual'];
                $estadoAprobacion = $item['estado_aprobacion'];
                
                // Determinar color según estado
                $colorClass = '';
                $estadoTexto = '';
                $estadoIcon = '';
                
                switch($estadoAprobacion) {
                    case 'aprobado':
                        $colorClass = 'success';
                        $estadoTexto = 'Aprobado';
                        $estadoIcon = 'check-circle';
                        break;
                    case 'reprobado':
                        $colorClass = 'danger';
                        $estadoTexto = 'Reprobado';
                        $estadoIcon = 'times-circle';
                        break;
                    case 'en_progreso_aprobando':
                        $colorClass = 'info';
                        $estadoTexto = 'En Progreso - Aprobando';
                        $estadoIcon = 'arrow-up';
                        break;
                    case 'en_progreso_riesgo':
                        $colorClass = 'warning';
                        $estadoTexto = 'En Progreso - En Riesgo';
                        $estadoIcon = 'exclamation-triangle';
                        break;
                }
            @endphp
            
            <div class="col-lg-6">
                <div class="progress-card">
                    <div class="progress-card-header">
                        <div class="progress-card-icon bg-gradient-{{ $colorClass }}">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="progress-card-title">
                            <h5>{{ $inscripcion->seccion->curso->nombre }}</h5>
                            <div class="d-flex gap-2 align-items-center flex-wrap">
                                <span class="badge-neon-sm">{{ $inscripcion->seccion->curso->codigo_curso }}</span>
                                <span class="text-muted-neon">
                                    <i class="fas fa-user-tie"></i>
                                    {{ $inscripcion->seccion->profesor->nombre_completo }}
                                </span>
                            </div>
                        </div>
                        <div class="progress-card-status">
                            <span class="badge badge-{{ $colorClass }}">
                                <i class="fas fa-{{ $estadoIcon }} me-1"></i>
                                {{ $estadoTexto }}
                            </span>
                        </div>
                    </div>

                    <div class="progress-card-body">
                        <!-- Nota Actual -->
                        <div class="progress-stats-row">
                            <div class="progress-stat-item">
                                <div class="progress-stat-label">Nota Actual</div>
                                <div class="progress-stat-value grade-{{ $notaActual >= 70 ? 'pass' : 'fail' }}">
                                    {{ number_format($notaActual, 2) }}
                                </div>
                            </div>
                            
                            @if($item['nota_final'])
                                <div class="progress-stat-item">
                                    <div class="progress-stat-label">Nota Final</div>
                                    <div class="progress-stat-value grade-{{ $item['nota_final'] >= 70 ? 'pass' : 'fail' }}">
                                        {{ number_format($item['nota_final'], 2) }}
                                    </div>
                                </div>
                            @endif
                            
                            <div class="progress-stat-item">
                                <div class="progress-stat-label">Evaluaciones</div>
                                <div class="progress-stat-value">
                                    {{ $item['evaluaciones_calificadas'] }}/{{ $item['total_evaluaciones'] }}
                                </div>
                            </div>
                        </div>

                        <!-- Barra de Progreso -->
                        <div class="progress-section">
                            <div class="progress-label-row">
                                <span>Progreso de Evaluaciones</span>
                                <span class="progress-percentage">{{ number_format($porcentajeCompletado, 1) }}%</span>
                            </div>
                            <div class="progress-bar-wrapper">
                                <div class="progress-bar-fill bg-{{ $colorClass }}" style="width: {{ $porcentajeCompletado }}%"></div>
                            </div>
                        </div>

                        <!-- Acciones -->
                        <div class="progress-actions">
                            <a href="{{ route('estudiante.calificaciones.materia', $inscripcion->id) }}" class="btn-action-neon">
                                <i class="fas fa-eye me-2"></i>Ver Detalles
                            </a>
                            <a href="{{ route('estudiante.calificaciones.comparar', $inscripcion->id) }}" class="btn-action-neon">
                                <i class="fas fa-chart-bar me-2"></i>Comparar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="empty-state-compact">
                    <i class="fas fa-chart-line fa-3x mb-3"></i>
                    <h5>No hay datos de progreso</h5>
                    <p>No tienes materias inscritas en el período seleccionado</p>
                    <a href="{{ route('estudiante.inscripciones.disponibles') }}" class="btn-outline-neon mt-3">
                        <i class="fas fa-plus me-2"></i>Ver Materias Disponibles
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

    /* Progress Cards */
    .progress-card {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.95) 100%);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 15px;
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%;
    }

    .progress-card:hover {
        border-color: rgba(0, 212, 255, 0.5);
        box-shadow: 0 0 25px rgba(0, 212, 255, 0.2);
        transform: translateY(-5px);
    }

    .progress-card-header {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1.5rem;
        border-bottom: 1px solid rgba(0, 212, 255, 0.2);
        background: rgba(0, 212, 255, 0.05);
    }

    .progress-card-icon {
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
    .bg-gradient-danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
    .bg-gradient-info { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }
    .bg-gradient-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }

    .progress-card-title {
        flex: 1;
    }

    .progress-card-title h5 {
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

    .progress-card-status {
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
    }

    .badge-success {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 1px solid #10b981;
    }

    .badge-danger {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 1px solid #ef4444;
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

    .progress-card-body {
        padding: 1.5rem;
    }

    .progress-stats-row {
        display: flex;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    .progress-stat-item {
        flex: 1;
        min-width: 120px;
        text-align: center;
        padding: 1rem;
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
    }

    .progress-stat-label {
        color: #94a3b8;
        font-size: 0.75rem;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .progress-stat-value {
        color: var(--neon-cyan);
        font-size: 1.5rem;
        font-weight: 700;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .progress-stat-value.grade-pass {
        color: #10b981;
        text-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
    }

    .progress-stat-value.grade-fail {
        color: #ef4444;
        text-shadow: 0 0 10px rgba(239, 68, 68, 0.5);
    }

    .progress-section {
        margin-bottom: 1.5rem;
    }

    .progress-label-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
        color: #cbd5e1;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .progress-percentage {
        color: var(--neon-cyan);
        font-weight: 700;
    }

    .progress-bar-wrapper {
        height: 12px;
        background: rgba(100, 116, 139, 0.2);
        border-radius: 10px;
        overflow: hidden;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .progress-bar-fill {
        height: 100%;
        border-radius: 10px;
        transition: width 0.6s ease;
        position: relative;
        overflow: hidden;
    }

    .progress-bar-fill.bg-success {
        background: linear-gradient(90deg, #10b981 0%, #34d399 100%);
        box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
    }

    .progress-bar-fill.bg-danger {
        background: linear-gradient(90deg, #ef4444 0%, #f87171 100%);
        box-shadow: 0 0 10px rgba(239, 68, 68, 0.5);
    }

    .progress-bar-fill.bg-info {
        background: linear-gradient(90deg, #06b6d4 0%, #22d3ee 100%);
        box-shadow: 0 0 10px rgba(6, 182, 212, 0.5);
    }

    .progress-bar-fill.bg-warning {
        background: linear-gradient(90deg, #f59e0b 0%, #fbbf24 100%);
        box-shadow: 0 0 10px rgba(245, 158, 11, 0.5);
    }

    .progress-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .btn-action-neon {
        flex: 1;
        min-width: 150px;
        padding: 0.75rem 1rem;
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
        .progress-card-header {
            flex-wrap: wrap;
        }

        .progress-stats-row {
            flex-direction: column;
        }

        .progress-actions {
            flex-direction: column;
        }

        .btn-action-neon {
            width: 100%;
        }
    }
</style>
@endpush
@endsection
