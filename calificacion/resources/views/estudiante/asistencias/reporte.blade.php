@extends('layouts.app')

@section('title', 'Reporte de Asistencias - Estudiante')
@section('page-title', 'Reporte de Asistencias')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header-dark mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-file-alt me-3"></i>Reporte de Asistencias
                </h1>
                <p class="header-subtitle mb-0">Resumen detallado de tu asistencia</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('estudiante.asistencias.exportar', ['fecha_inicio' => $fechaInicio, 'fecha_fin' => $fechaFin]) }}" 
                   class="btn-action-neon">
                    <i class="fas fa-file-excel me-2"></i>Exportar a Excel
                </a>
                <a href="{{ route('estudiante.asistencias.index') }}" class="btn-outline-neon">
                    <i class="fas fa-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Filtros de Fecha -->
    <div class="card-dark mb-4">
        <div class="card-body-dark">
            <form method="GET" action="{{ route('estudiante.asistencias.reporte') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label-neon">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control-neon" 
                           value="{{ $fechaInicio }}" max="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label-neon">Fecha Fin</label>
                    <input type="date" name="fecha_fin" class="form-control-neon" 
                           value="{{ $fechaFin }}" max="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn-action-neon w-100">
                        <i class="fas fa-search me-2"></i>Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reporte por Materia -->
    <div class="row g-4">
        @forelse($reporte as $item)
            @php
                $porcentaje = $item['porcentaje_asistencia'];
                $estado = $item['estado_asistencia'];
                
                // Determinar color según porcentaje
                $colorClass = '';
                $estadoTexto = '';
                $estadoIcon = '';
                
                switch($estado) {
                    case 'excelente':
                        $colorClass = 'success';
                        $estadoTexto = 'Excelente';
                        $estadoIcon = 'star';
                        break;
                    case 'buena':
                        $colorClass = 'info';
                        $estadoTexto = 'Buena';
                        $estadoIcon = 'thumbs-up';
                        break;
                    case 'regular':
                        $colorClass = 'warning';
                        $estadoTexto = 'Regular';
                        $estadoIcon = 'exclamation-triangle';
                        break;
                    case 'deficiente':
                        $colorClass = 'danger';
                        $estadoTexto = 'Deficiente';
                        $estadoIcon = 'exclamation-circle';
                        break;
                    default:
                        $colorClass = 'secondary';
                        $estadoTexto = 'Sin Datos';
                        $estadoIcon = 'minus-circle';
                }
            @endphp
            
            <div class="col-lg-6">
                <div class="report-card">
                    <div class="report-header">
                        <div class="report-icon bg-gradient-{{ $colorClass }}">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="report-title">
                            <h5>{{ $item['curso'] }}</h5>
                            <span class="badge-code">{{ $item['codigo_curso'] }}</span>
                        </div>
                        <div class="report-status">
                            <span class="badge badge-{{ $colorClass }}">
                                <i class="fas fa-{{ $estadoIcon }} me-1"></i>
                                {{ $estadoTexto }}
                            </span>
                        </div>
                    </div>

                    <div class="report-body">
                        <!-- Porcentaje Principal -->
                        <div class="percentage-display">
                            <div class="percentage-circle {{ $colorClass }}">
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

                        <!-- Estadísticas Detalladas -->
                        <div class="stats-grid">
                            <div class="stat-item">
                                <div class="stat-icon bg-primary">
                                    <i class="fas fa-calendar-day"></i>
                                </div>
                                <div class="stat-content">
                                    <span class="stat-value">{{ $item['total_clases'] }}</span>
                                    <span class="stat-label">Total Clases</span>
                                </div>
                            </div>

                            <div class="stat-item">
                                <div class="stat-icon bg-success">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div class="stat-content">
                                    <span class="stat-value">{{ $item['presentes'] }}</span>
                                    <span class="stat-label">Presentes</span>
                                </div>
                            </div>

                            <div class="stat-item">
                                <div class="stat-icon bg-warning">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="stat-content">
                                    <span class="stat-value">{{ $item['tardanzas'] }}</span>
                                    <span class="stat-label">Tardanzas</span>
                                </div>
                            </div>

                            <div class="stat-item">
                                <div class="stat-icon bg-danger">
                                    <i class="fas fa-times"></i>
                                </div>
                                <div class="stat-content">
                                    <span class="stat-value">{{ $item['ausentes'] }}</span>
                                    <span class="stat-label">Ausentes</span>
                                </div>
                            </div>

                            <div class="stat-item">
                                <div class="stat-icon bg-info">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div class="stat-content">
                                    <span class="stat-value">{{ $item['justificadas'] }}</span>
                                    <span class="stat-label">Justificadas</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="empty-state-compact">
                    <i class="fas fa-file-alt fa-3x mb-3"></i>
                    <h5>No hay datos de asistencia</h5>
                    <p>No se encontraron registros de asistencia en el período seleccionado</p>
                    <a href="{{ route('estudiante.asistencias.index') }}" class="btn-outline-neon mt-3">
                        <i class="fas fa-arrow-left me-2"></i>Volver al Inicio
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

    /* Report Cards */
    .report-card {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.95) 100%);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 15px;
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%;
    }

    .report-card:hover {
        border-color: rgba(0, 212, 255, 0.5);
        box-shadow: 0 0 25px rgba(0, 212, 255, 0.2);
        transform: translateY(-5px);
    }

    .report-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.5rem;
        border-bottom: 1px solid rgba(0, 212, 255, 0.2);
        background: rgba(0, 212, 255, 0.05);
    }

    .report-icon {
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
    .bg-gradient-secondary { background: linear-gradient(135deg, #64748b 0%, #475569 100%); }

    .report-title {
        flex: 1;
    }

    .report-title h5 {
        color: #f1f5f9;
        font-weight: 600;
        margin: 0 0 0.5rem 0;
        font-size: 1.1rem;
    }

    .badge-code {
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        color: #0f172a;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.75rem;
        box-shadow: 0 0 8px rgba(0, 212, 255, 0.3);
    }

    .report-status {
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

    .badge-secondary {
        background: rgba(100, 116, 139, 0.2);
        color: #94a3b8;
        border: 1px solid #94a3b8;
    }

    .report-body {
        padding: 1.5rem;
    }

    /* Percentage Circle */
    .percentage-display {
        display: flex;
        justify-content: center;
        margin-bottom: 1.5rem;
    }

    .percentage-circle {
        position: relative;
        width: 150px;
        height: 150px;
    }

    .percentage-circle svg {
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

    .percentage-circle.success .circle-progress {
        stroke: #10b981;
        filter: drop-shadow(0 0 10px rgba(16, 185, 129, 0.5));
    }

    .percentage-circle.info .circle-progress {
        stroke: #06b6d4;
        filter: drop-shadow(0 0 10px rgba(6, 182, 212, 0.5));
    }

    .percentage-circle.warning .circle-progress {
        stroke: #f59e0b;
        filter: drop-shadow(0 0 10px rgba(245, 158, 11, 0.5));
    }

    .percentage-circle.danger .circle-progress {
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
        font-size: 2rem;
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

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 0.75rem;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .stat-item:hover {
        background: rgba(0, 212, 255, 0.05);
        border-color: rgba(0, 212, 255, 0.4);
    }

    .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        flex-shrink: 0;
    }

    .stat-icon.bg-primary { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); }
    .stat-icon.bg-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .stat-icon.bg-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .stat-icon.bg-danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
    .stat-icon.bg-info { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }

    .stat-content {
        display: flex;
        flex-direction: column;
    }

    .stat-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: #f1f5f9;
    }

    .stat-label {
        font-size: 0.75rem;
        color: #94a3b8;
        text-transform: uppercase;
        font-weight: 600;
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
        .report-header {
            flex-wrap: wrap;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .percentage-circle {
            width: 120px;
            height: 120px;
        }

        .percentage-number {
            font-size: 1.5rem;
        }
    }
</style>
@endpush
@endsection
