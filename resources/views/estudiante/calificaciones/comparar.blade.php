@extends('layouts.app')

@section('title', 'Comparar Rendimiento - Estudiante')
@section('page-title', 'Comparar Rendimiento')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header-dark mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-chart-bar me-3"></i>Comparar Rendimiento
                </h1>
                <p class="header-subtitle mb-0">
                    {{ $inscripcion->seccion->curso->nombre }} - {{ $inscripcion->seccion->curso->codigo_curso }}
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('estudiante.calificaciones.materia', $inscripcion->id) }}" class="btn-outline-neon">
                    <i class="fas fa-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Información del Curso -->
    <div class="card-dark mb-4">
        <div class="card-body-dark">
            <div class="course-info-grid">
                <div class="info-item">
                    <i class="fas fa-user-tie"></i>
                    <div>
                        <small>Profesor</small>
                        <strong>{{ $inscripcion->seccion->profesor->nombre_completo }}</strong>
                    </div>
                </div>
                <div class="info-item">
                    <i class="fas fa-calendar"></i>
                    <div>
                        <small>Período</small>
                        <strong>{{ $inscripcion->seccion->periodo->nombre }}</strong>
                    </div>
                </div>
                <div class="info-item">
                    <i class="fas fa-users"></i>
                    <div>
                        <small>Sección</small>
                        <strong>{{ $inscripcion->seccion->codigo_seccion }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Comparación de Evaluaciones -->
    <div class="row g-4">
        @forelse($comparacion as $item)
            @php
                $miNota = $item['mi_nota'];
                $promedioClase = $item['promedio_clase'];
                $notaMayor = $item['nota_mayor'];
                $notaMenor = $item['nota_menor'];
                $miPosicion = $item['mi_posicion'];
                $totalEstudiantes = $item['total_estudiantes'];
                
                // Determinar rendimiento relativo
                $rendimiento = '';
                $rendimientoColor = '';
                $rendimientoIcon = '';
                
                if ($miNota !== null && $promedioClase !== null) {
                    $diferencia = $miNota - $promedioClase;
                    if ($diferencia >= 10) {
                        $rendimiento = 'Muy Superior';
                        $rendimientoColor = 'success';
                        $rendimientoIcon = 'arrow-up';
                    } elseif ($diferencia >= 5) {
                        $rendimiento = 'Superior';
                        $rendimientoColor = 'info';
                        $rendimientoIcon = 'arrow-up';
                    } elseif ($diferencia >= -5) {
                        $rendimiento = 'Promedio';
                        $rendimientoColor = 'warning';
                        $rendimientoIcon = 'minus';
                    } else {
                        $rendimiento = 'Inferior';
                        $rendimientoColor = 'danger';
                        $rendimientoIcon = 'arrow-down';
                    }
                }
            @endphp
            
            <div class="col-lg-6">
                <div class="comparison-card">
                    <div class="comparison-header">
                        <div class="comparison-title">
                            <h5>{{ $item['evaluacion'] }}</h5>
                            <span class="badge-type">{{ $item['tipo'] }}</span>
                        </div>
                        @if($miNota !== null && $promedioClase !== null)
                            <span class="badge badge-{{ $rendimientoColor }}">
                                <i class="fas fa-{{ $rendimientoIcon }} me-1"></i>
                                {{ $rendimiento }}
                            </span>
                        @endif
                    </div>

                    <div class="comparison-body">
                        @if($miNota !== null)
                            <!-- Gráfico de Comparación -->
                            <div class="comparison-chart">
                                <div class="chart-bar">
                                    <div class="chart-segment my-grade" 
                                         style="width: {{ $notaMayor > 0 ? ($miNota / $notaMayor) * 100 : 0 }}%"
                                         title="Mi Nota: {{ number_format($miNota, 1) }}">
                                        <span class="chart-label">Yo</span>
                                        <span class="chart-value">{{ number_format($miNota, 1) }}</span>
                                    </div>
                                </div>
                                
                                @if($promedioClase !== null)
                                    <div class="chart-bar">
                                        <div class="chart-segment class-average" 
                                             style="width: {{ $notaMayor > 0 ? ($promedioClase / $notaMayor) * 100 : 0 }}%"
                                             title="Promedio: {{ number_format($promedioClase, 2) }}">
                                            <span class="chart-label">Promedio</span>
                                            <span class="chart-value">{{ number_format($promedioClase, 2) }}</span>
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="chart-bar">
                                    <div class="chart-segment highest-grade" 
                                         style="width: 100%"
                                         title="Nota Mayor: {{ number_format($notaMayor, 1) }}">
                                        <span class="chart-label">Mayor</span>
                                        <span class="chart-value">{{ number_format($notaMayor, 1) }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Estadísticas -->
                            <div class="comparison-stats">
                                <div class="stat-box">
                                    <i class="fas fa-trophy"></i>
                                    <div>
                                        <small>Mi Posición</small>
                                        <strong>{{ $miPosicion ?? 'N/A' }}/{{ $totalEstudiantes }}</strong>
                                    </div>
                                </div>
                                
                                @if($promedioClase !== null)
                                    <div class="stat-box">
                                        <i class="fas fa-chart-line"></i>
                                        <div>
                                            <small>Diferencia</small>
                                            <strong class="{{ $miNota >= $promedioClase ? 'text-success' : 'text-danger' }}">
                                                {{ $miNota >= $promedioClase ? '+' : '' }}{{ number_format($miNota - $promedioClase, 2) }}
                                            </strong>
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="stat-box">
                                    <i class="fas fa-arrow-down"></i>
                                    <div>
                                        <small>Nota Menor</small>
                                        <strong>{{ number_format($notaMenor, 1) }}</strong>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="no-grade-message">
                                <i class="fas fa-clock fa-2x mb-2"></i>
                                <p>Aún no tienes calificación para esta evaluación</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="empty-state-compact">
                    <i class="fas fa-chart-bar fa-3x mb-3"></i>
                    <h5>No hay evaluaciones para comparar</h5>
                    <p>Esta materia aún no tiene evaluaciones registradas</p>
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

    .course-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: rgba(0, 212, 255, 0.05);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
    }

    .info-item i {
        font-size: 1.5rem;
        color: var(--neon-cyan);
    }

    .info-item small {
        display: block;
        color: #94a3b8;
        font-size: 0.75rem;
        margin-bottom: 0.25rem;
    }

    .info-item strong {
        color: #f1f5f9;
        font-size: 0.875rem;
    }

    /* Comparison Cards */
    .comparison-card {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.95) 100%);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 15px;
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%;
    }

    .comparison-card:hover {
        border-color: rgba(0, 212, 255, 0.5);
        box-shadow: 0 0 25px rgba(0, 212, 255, 0.2);
        transform: translateY(-5px);
    }

    .comparison-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        padding: 1.5rem;
        border-bottom: 1px solid rgba(0, 212, 255, 0.2);
        background: rgba(0, 212, 255, 0.05);
    }

    .comparison-title {
        flex: 1;
    }

    .comparison-title h5 {
        color: #f1f5f9;
        font-weight: 600;
        margin: 0 0 0.5rem 0;
        font-size: 1.1rem;
    }

    .badge-type {
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        color: #0f172a;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.75rem;
        box-shadow: 0 0 8px rgba(0, 212, 255, 0.3);
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

    .comparison-body {
        padding: 1.5rem;
    }

    /* Comparison Chart */
    .comparison-chart {
        margin-bottom: 1.5rem;
    }

    .chart-bar {
        margin-bottom: 1rem;
    }

    .chart-segment {
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 1rem;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .chart-segment::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(90deg, transparent 0%, rgba(255, 255, 255, 0.1) 50%, transparent 100%);
        animation: shimmer 2s infinite;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    .chart-segment.my-grade {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        box-shadow: 0 0 15px rgba(14, 165, 233, 0.5);
    }

    .chart-segment.class-average {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        box-shadow: 0 0 15px rgba(245, 158, 11, 0.5);
    }

    .chart-segment.highest-grade {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        box-shadow: 0 0 15px rgba(16, 185, 129, 0.5);
    }

    .chart-label {
        color: white;
        font-weight: 600;
        font-size: 0.875rem;
        z-index: 1;
    }

    .chart-value {
        color: white;
        font-weight: 700;
        font-size: 1.1rem;
        z-index: 1;
    }

    /* Comparison Stats */
    .comparison-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
    }

    .stat-box {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .stat-box:hover {
        background: rgba(0, 212, 255, 0.05);
        border-color: rgba(0, 212, 255, 0.4);
    }

    .stat-box i {
        font-size: 1.5rem;
        color: var(--neon-cyan);
    }

    .stat-box small {
        display: block;
        color: #94a3b8;
        font-size: 0.75rem;
        margin-bottom: 0.25rem;
    }

    .stat-box strong {
        color: #f1f5f9;
        font-size: 1rem;
        font-weight: 700;
    }

    .text-success {
        color: #10b981 !important;
    }

    .text-danger {
        color: #ef4444 !important;
    }

    .no-grade-message {
        text-align: center;
        padding: 3rem 1rem;
        color: #94a3b8;
    }

    .no-grade-message i {
        color: rgba(0, 212, 255, 0.3);
    }

    .no-grade-message p {
        margin: 0;
        color: #cbd5e1;
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
        .comparison-header {
            flex-direction: column;
        }

        .comparison-stats {
            grid-template-columns: 1fr;
        }

        .chart-segment {
            height: 40px;
            padding: 0 0.75rem;
        }

        .chart-label, .chart-value {
            font-size: 0.75rem;
        }
    }
</style>
@endpush
@endsection
