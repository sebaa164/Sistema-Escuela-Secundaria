@extends('layouts.app')

@section('title', 'Mis Calificaciones - Estudiante')
@section('page-title', 'Mis Calificaciones')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header-dark mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-star me-3"></i>Mis Calificaciones
                </h1>
                <p class="header-subtitle mb-0">Revisa tu desempeño académico completo</p>
            </div>
            <a href="{{ route('estudiante.dashboard') }}" class="btn-outline-neon">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <!-- Estadísticas Mejoradas -->
    <div class="stats-grid-enhanced mb-4">
        <div class="stat-card-enhanced">
            <div class="stat-icon-enhanced bg-gradient-primary">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content-enhanced">
                <h3>{{ number_format($promedioGeneral, 2) }}</h3>
                <p>Promedio General</p>
                <div class="stat-progress">
                    <div class="progress-bar-custom" style="width: {{ $promedioGeneral }}%"></div>
                </div>
                <small class="stat-subtext">
                    @if($promedioGeneral >= 90)
                        Excelente rendimiento
                    @elseif($promedioGeneral >= 80)
                        Muy buen desempeño
                    @elseif($promedioGeneral >= 70)
                        Buen desempeño
                    @else
                        Necesitas mejorar
                    @endif
                </small>
            </div>
        </div>
        
        <div class="stat-card-enhanced">
            <div class="stat-icon-enhanced bg-gradient-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content-enhanced">
                <h3>{{ $materiasAprobadas }}</h3>
                <p>Materias Aprobadas</p>
                <div class="stat-progress">
                    <div class="progress-bar-custom bg-success" style="width: {{ $inscripciones->count() > 0 ? ($materiasAprobadas / $inscripciones->count()) * 100 : 0 }}%"></div>
                </div>
                <small class="stat-subtext">{{ $inscripciones->count() - $materiasAprobadas }} reprobadas o pendientes</small>
            </div>
        </div>
        
        <div class="stat-card-enhanced">
            <div class="stat-icon-enhanced bg-gradient-warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content-enhanced">
                <h3>{{ $materiasPendientes }}</h3>
                <p>Pendientes</p>
                <div class="stat-progress">
                    <div class="progress-bar-custom bg-warning" style="width: {{ $inscripciones->count() > 0 ? ($materiasPendientes / $inscripciones->count()) * 100 : 0 }}%"></div>
                </div>
                <small class="stat-subtext">Por calificar</small>
            </div>
        </div>
        
        <div class="stat-card-enhanced">
            <div class="stat-icon-enhanced bg-gradient-info">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="stat-content-enhanced">
                <h3>{{ $creditosAcumulados }}</h3>
                <p>Créditos Aprobados</p>
                <div class="stat-progress">
                    <div class="progress-bar-custom bg-info" style="width: {{ min(($creditosAcumulados / 120) * 100, 100) }}%"></div>
                </div>
                <small class="stat-subtext">De 120 créditos totales</small>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Columna Principal: Calificaciones -->
        <div class="col-lg-8">
            <div class="card-dark">
                <div class="card-header-dark">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>Calificaciones por Materia
                        </h5>
                        <div class="filter-buttons">
                            <button class="filter-btn active" data-filter="all">
                                <i class="fas fa-border-all me-1"></i>Todas
                            </button>
                            <button class="filter-btn" data-filter="aprobadas">
                                <i class="fas fa-check me-1"></i>Aprobadas
                            </button>
                            <button class="filter-btn" data-filter="pendientes">
                                <i class="fas fa-clock me-1"></i>Pendientes
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body-dark">
                    @forelse($inscripciones as $inscripcion)
                        @php
                            $isAprobada = $inscripcion->nota_final && $inscripcion->nota_final >= 70;
                            $isPendiente = !$inscripcion->nota_final;
                            $filterClass = $isAprobada ? 'aprobadas' : ($isPendiente ? 'pendientes' : 'reprobadas');
                        @endphp
                        <div class="grade-item-enhanced {{ $filterClass }}" data-filter="{{ $filterClass }}">
                            <div class="grade-item-main">
                                <div class="grade-icon-wrapper">
                                    <div class="grade-icon-circle">
                                        <i class="fas fa-book"></i>
                                    </div>
                                </div>
                                <div class="grade-info-section">
                                    <h6>{{ $inscripcion->seccion->curso->nombre }}</h6>
                                    <div class="grade-meta-row">
                                        <span class="badge-neon-sm">{{ $inscripcion->seccion->curso->codigo_curso }}</span>
                                        <span class="grade-meta-text">
                                            <i class="fas fa-user-tie"></i>
                                            {{ $inscripcion->seccion->profesor->nombre_completo }}
                                        </span>
                                    </div>
                                </div>
                                <div class="grade-display-section">
                                    @if($inscripcion->nota_final)
                                        <div class="grade-number grade-{{ $inscripcion->nota_final >= 70 ? 'pass' : 'fail' }}">
                                            {{ number_format($inscripcion->nota_final, 1) }}
                                        </div>
                                        <span class="grade-status-text">Nota Final</span>
                                    @else
                                        <div class="grade-number grade-pending">
                                            --
                                        </div>
                                        <span class="grade-status-text">Pendiente</span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Barra de progreso de evaluaciones -->
                            <div class="evaluations-progress">
                                @php
                                    $totalEval = $inscripcion->seccion->evaluaciones->count();
                                    $evalCalificadas = $inscripcion->seccion->evaluaciones->filter(function($eval) use ($inscripcion) {
                                        return $eval->calificaciones->where('estudiante_id', $inscripcion->estudiante_id)->whereNotNull('nota')->count() > 0;
                                    })->count();
                                    $progreso = $totalEval > 0 ? ($evalCalificadas / $totalEval) * 100 : 0;
                                @endphp
                                <div class="progress-info-row">
                                    <small>Evaluaciones: {{ $evalCalificadas }}/{{ $totalEval }}</small>
                                    <small>{{ number_format($progreso, 0) }}% completado</small>
                                </div>
                                <div class="mini-progress-bar">
                                    <div class="mini-progress-fill" style="width: {{ $progreso }}%"></div>
                                </div>
                            </div>

                            <!-- Listado mini de evaluaciones -->
                            <div class="mini-evaluations-list">
                                @foreach($inscripcion->seccion->evaluaciones->take(3) as $evaluacion)
                                    @php
                                        $calif = $evaluacion->calificaciones->where('estudiante_id', $inscripcion->estudiante_id)->first();
                                    @endphp
                                    <div class="mini-eval-item">
                                        <span class="mini-eval-name">{{ Str::limit($evaluacion->nombre, 25) }}</span>
                                        <span class="mini-eval-type">{{ $evaluacion->porcentaje }}%</span>
                                        @if($calif && $calif->nota !== null)
                                            <span class="mini-eval-grade grade-{{ $calif->nota >= 70 ? 'pass' : 'fail' }}">
                                                {{ number_format($calif->nota, 1) }}
                                            </span>
                                        @else
                                            <span class="mini-eval-grade grade-pending">--</span>
                                        @endif
                                    </div>
                                @endforeach
                                
                                @if($inscripcion->seccion->evaluaciones->count() > 3)
                                    <div class="mini-eval-more">
                                        <i class="fas fa-plus-circle"></i>
                                        {{ $inscripcion->seccion->evaluaciones->count() - 3 }} evaluaciones más
                                    </div>
                                @endif
                            </div>

                            <div class="grade-item-footer">
                                <a href="{{ route('estudiante.calificaciones.materia', $inscripcion->id) }}" class="btn-detail">
                                    <i class="fas fa-chart-bar me-2"></i>Ver Detalles Completos
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state-compact">
                            <i class="fas fa-star fa-3x mb-3"></i>
                            <h5>No hay calificaciones disponibles</h5>
                            <p>Aún no tienes calificaciones registradas</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar: Análisis y Estadísticas -->
        <div class="col-lg-4">
            <!-- Gráfico de Distribución -->
            <div class="card-dark mb-3">
                <div class="card-header-dark">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Distribución de Notas
                    </h6>
                </div>
                <div class="card-body-dark">
                    @php
                        $notasCalificadas = $inscripciones->whereNotNull('nota_final')->pluck('nota_final');
                        $excelente = $notasCalificadas->where('>=', 90)->count();
                        $bueno = $notasCalificadas->whereBetween(80, [80, 89])->count();
                        $regular = $notasCalificadas->whereBetween(70, [70, 79])->count();
                        $deficiente = $notasCalificadas->where('<', 70)->count();
                        $total = $notasCalificadas->count();
                    @endphp
                    
                    <div class="distribution-chart">
                        @if($total > 0)
                            <div class="distribution-bar">
                                @if($excelente > 0)
                                    <div class="distribution-segment bg-excellent" 
                                         style="width: {{ ($excelente / $total) * 100 }}%"
                                         title="Excelente: {{ $excelente }}">
                                    </div>
                                @endif
                                @if($bueno > 0)
                                    <div class="distribution-segment bg-good" 
                                         style="width: {{ ($bueno / $total) * 100 }}%"
                                         title="Bueno: {{ $bueno }}">
                                    </div>
                                @endif
                                @if($regular > 0)
                                    <div class="distribution-segment bg-regular" 
                                         style="width: {{ ($regular / $total) * 100 }}%"
                                         title="Regular: {{ $regular }}">
                                    </div>
                                @endif
                                @if($deficiente > 0)
                                    <div class="distribution-segment bg-poor" 
                                         style="width: {{ ($deficiente / $total) * 100 }}%"
                                         title="Deficiente: {{ $deficiente }}">
                                    </div>
                                @endif
                            </div>
                            
                            <div class="distribution-legend">
                                <div class="legend-item">
                                    <span class="legend-color bg-excellent"></span>
                                    <span>Excelente (90-100)</span>
                                    <strong>{{ $excelente }}</strong>
                                </div>
                                <div class="legend-item">
                                    <span class="legend-color bg-good"></span>
                                    <span>Bueno (80-89)</span>
                                    <strong>{{ $bueno }}</strong>
                                </div>
                                <div class="legend-item">
                                    <span class="legend-color bg-regular"></span>
                                    <span>Regular (70-79)</span>
                                    <strong>{{ $regular }}</strong>
                                </div>
                                <div class="legend-item">
                                    <span class="legend-color bg-poor"></span>
                                    <span>Deficiente (<70)</span>
                                    <strong>{{ $deficiente }}</strong>
                                </div>
                            </div>
                        @else
                            <p class="text-center text-muted mb-0">Sin datos para mostrar</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Mejores y Peores Materias -->
            <div class="card-dark mb-3">
                <div class="card-header-dark">
                    <h6 class="mb-0">
                        <i class="fas fa-trophy me-2"></i>Destacadas
                    </h6>
                </div>
                <div class="card-body-dark">
                    @php
                        $mejoresMaterias = $inscripciones->whereNotNull('nota_final')->sortByDesc('nota_final')->take(3);
                    @endphp
                    
                    <h6 class="section-subtitle">
                        <i class="fas fa-arrow-up me-2"></i>Mejores Notas
                    </h6>
                    @foreach($mejoresMaterias as $index => $materia)
                        <div class="ranked-item">
                            <div class="rank-badge rank-{{ $index + 1 }}">{{ $index + 1 }}</div>
                            <div class="ranked-info">
                                <strong>{{ Str::limit($materia->seccion->curso->nombre, 25) }}</strong>
                                <small>{{ $materia->seccion->curso->codigo_curso }}</small>
                            </div>
                            <div class="ranked-grade grade-pass">
                                {{ number_format($materia->nota_final, 1) }}
                            </div>
                        </div>
                    @endforeach
                    
                    @php
                        $materiasRiesgo = $inscripciones->whereNotNull('nota_final')->where('nota_final', '<', 70)->sortBy('nota_final')->take(2);
                    @endphp
                    
                    @if($materiasRiesgo->count() > 0)
                        <h6 class="section-subtitle mt-4">
                            <i class="fas fa-exclamation-triangle me-2"></i>Requieren Atención
                        </h6>
                        @foreach($materiasRiesgo as $materia)
                            <div class="ranked-item alert-item">
                                <i class="fas fa-exclamation-circle"></i>
                                <div class="ranked-info">
                                    <strong>{{ Str::limit($materia->seccion->curso->nombre, 25) }}</strong>
                                    <small>{{ $materia->seccion->curso->codigo_curso }}</small>
                                </div>
                                <div class="ranked-grade grade-fail">
                                    {{ number_format($materia->nota_final, 1) }}
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="card-dark">
                <div class="card-header-dark">
                    <h6 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>Acciones Rápidas
                    </h6>
                </div>
                <div class="card-body-dark">
                    <a href="{{ route('estudiante.inscripciones.index') }}" class="quick-action-btn">
                        <div class="quick-action-icon bg-purple">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="quick-action-text">
                            <strong>Mis Materias</strong>
                            <small>Ver todas las inscripciones</small>
                        </div>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    
                    <a href="{{ route('estudiante.asistencias.index') }}" class="quick-action-btn">
                        <div class="quick-action-icon bg-green">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="quick-action-text">
                            <strong>Asistencias</strong>
                            <small>Revisar mi asistencia</small>
                        </div>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    
                    <a href="{{ route('estudiante.inscripciones.historial') }}" class="quick-action-btn">
                        <div class="quick-action-icon bg-cyan">
                            <i class="fas fa-history"></i>
                        </div>
                        <div class="quick-action-text">
                            <strong>Historial</strong>
                            <small>Ver historial completo</small>
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

    /* Reutilizamos los mismos estilos base del index de inscripciones */
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

    /* Items de Calificación Mejorados */
    .grade-item-enhanced {
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 15px;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .grade-item-enhanced:hover {
        border-color: rgba(0, 212, 255, 0.5);
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.2);
        transform: translateY(-3px);
    }

    .grade-item-main {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        padding: 1.5rem;
        border-bottom: 1px solid rgba(0, 212, 255, 0.1);
    }

    .grade-icon-circle {
        width: 60px;
        height: 60px;
        border-radius: 15px;
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: #0f172a;
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.4);
    }

    .grade-info-section {
        flex: 1;
    }

    .grade-info-section h6 {
        color: #f1f5f9;
        margin: 0 0 0.75rem 0;
        font-weight: 600;
        font-size: 1.1rem;
    }

    .grade-meta-row {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .grade-meta-text {
        color: #cbd5e1;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .grade-meta-text i {
        color: #94a3b8;
    }

    .grade-display-section {
        text-align: center;
    }

    .grade-number {
        width: 70px;
        height: 70px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .grade-number.grade-pass {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 3px solid #10b981;
        box-shadow: 0 0 15px rgba(16, 185, 129, 0.3);
    }

    .grade-number.grade-fail {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 3px solid #ef4444;
        box-shadow: 0 0 15px rgba(239, 68,68, 0.3);
    }

    .grade-number.grade-pending {
        background: rgba(148, 163, 184, 0.2);
        color: #cbd5e1;
        border: 3px solid #94a3b8;
    }

    .grade-status-text {
        color: #94a3b8;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    /* Progreso de Evaluaciones */
    .evaluations-progress {
        padding: 1rem 1.5rem;
        background: rgba(15, 23, 42, 0.3);
        border-bottom: 1px solid rgba(0, 212, 255, 0.1);
    }

    .progress-info-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }

    .progress-info-row small {
        color: #cbd5e1;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .mini-progress-bar {
        height: 6px;
        background: rgba(100, 116, 139, 0.2);
        border-radius: 10px;
        overflow: hidden;
    }

    .mini-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        border-radius: 10px;
        transition: width 0.6s ease;
        box-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    /* Lista Mini de Evaluaciones */
    .mini-evaluations-list {
        padding: 1rem 1.5rem;
    }

    .mini-eval-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.75rem;
        background: rgba(30, 41, 59, 0.3);
        border-radius: 8px;
        margin-bottom: 0.5rem;
        transition: all 0.2s ease;
    }

    .mini-eval-item:hover {
        background: rgba(0, 212, 255, 0.05);
    }

    .mini-eval-name {
        flex: 1;
        color: #f1f5f9;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .mini-eval-type {
        color: #fbbf24;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.25rem 0.5rem;
        background: rgba(245, 158, 11, 0.2);
        border-radius: 8px;
    }

    .mini-eval-grade {
        min-width: 45px;
        text-align: center;
        padding: 0.4rem 0.6rem;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.875rem;
    }

    .mini-eval-grade.grade-pass {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 1px solid #10b981;
    }

    .mini-eval-grade.grade-fail {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 1px solid #ef4444;
    }

    .mini-eval-grade.grade-pending {
        background: rgba(148, 163, 184, 0.2);
        color: #cbd5e1;
        border: 1px solid #94a3b8;
    }

    .mini-eval-more {
        text-align: center;
        padding: 0.75rem;
        color: var(--neon-cyan);
        font-size: 0.875rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .grade-item-footer {
        padding: 1rem 1.5rem;
        background: rgba(15, 23, 42, 0.5);
    }

    .btn-detail {
        display: block;
        width: 100%;
        padding: 0.875rem;
        border-radius: 10px;
        text-align: center;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        background: rgba(0, 212, 255, 0.1);
        border: 1px solid rgba(0, 212, 255, 0.3);
        color: var(--neon-cyan);
    }

    .btn-detail:hover {
        background: rgba(0, 212, 255, 0.2);
        border-color: var(--neon-cyan);
        transform: translateY(-2px);
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
        color: var(--neon-cyan);
    }

    /* Distribución de Notas */
    .distribution-chart {
        margin-top: 1rem;
    }

    .distribution-bar {
        display: flex;
        height: 40px;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 1.5rem;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
    }

    .distribution-segment {
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .distribution-segment:hover {
        opacity: 0.8;
    }

    .bg-excellent { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .bg-good { background: linear-gradient(135deg, #22d3ee 0%, #06b6d4 100%); }
    .bg-regular { background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%); }
    .bg-poor { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }

    .distribution-legend {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem;
        background: rgba(15, 23, 42, 0.3);
        border-radius: 8px;
    }

    .legend-color {
        width: 20px;
        height: 20px;
        border-radius: 6px;
        flex-shrink: 0;
    }

    .legend-item span:nth-child(2) {
        flex: 1;
        color: #cbd5e1;
        font-size: 0.875rem;
    }

    .legend-item strong {
        color: var(--neon-cyan);
        font-weight: 700;
    }

    /* Materias Destacadas */
    .section-subtitle {
        color: var(--neon-cyan);
        font-size: 0.875rem;
        text-transform: uppercase;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid rgba(0, 212, 255, 0.2);
        font-weight: 600;
    }

    .ranked-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
        margin-bottom: 0.75rem;
        transition: all 0.3s ease;
    }

    .ranked-item:hover {
        background: rgba(0, 212, 255, 0.05);
        border-color: rgba(0, 212, 255, 0.4);
        transform: translateX(5px);
    }

    .rank-badge {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .rank-1 {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: #0f172a;
        box-shadow: 0 0 15px rgba(251, 191, 36, 0.5);
    }

    .rank-2 {
        background: linear-gradient(135deg, #cbd5e1 0%, #94a3b8 100%);
        color: #0f172a;
        box-shadow: 0 0 15px rgba(203, 213, 225, 0.5);
    }

    .rank-3 {
        background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
        color: white;
        box-shadow: 0 0 15px rgba(217, 119, 6, 0.5);
    }

    .ranked-info {
        flex: 1;
    }

    .ranked-info strong {
        display: block;
        color: #f1f5f9;
        font-weight: 600;
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }

    .ranked-info small {
        color: #94a3b8;
        font-size: 0.75rem;
    }

    .ranked-grade {
        min-width: 50px;
        text-align: center;
        padding: 0.5rem 0.75rem;
        border-radius: 10px;
        font-weight: 700;
        font-size: 1rem;
    }

    .alert-item {
        border-color: rgba(239, 68, 68, 0.3);
        background: rgba(239, 68, 68, 0.05);
    }

    .alert-item i {
        color: #ef4444;
        font-size: 1.25rem;
    }

    /* Estadísticas Mejoradas - Reutilizamos del index de inscripciones */
    .stats-grid-enhanced {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
    }

    .stat-card-enhanced {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(30, 41, 59, 0.9) 100%);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 15px;
        padding: 1.5rem;
        display: flex;
        gap: 1rem;
        transition: all 0.3s ease;
    }

    .stat-card-enhanced:hover { transform: translateY(-5px); box-shadow: 0 0 30px rgba(0, 212, 255, 0.3); }

    .stat-icon-enhanced {
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

    .bg-gradient-primary { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); }
    .bg-gradient-info { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }
    .bg-gradient-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .bg-gradient-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }

    .stat-content-enhanced {
        flex: 1;
    }

    .stat-content-enhanced h3 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--neon-cyan);
        margin: 0 0 0.25rem 0;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .stat-content-enhanced p {
        color: #f1f5f9;
        margin: 0 0 0.75rem 0;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .stat-progress {
        height: 6px;
        background: rgba(100, 116, 139, 0.2);
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 0.5rem;
    }

    .progress-bar-custom {
        height: 100%;
        background: linear-gradient(90deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        border-radius: 10px;
        transition: width 0.6s ease;
        box-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .progress-bar-custom.bg-info {
        background: linear-gradient(90deg, #06b6d4 0%, #22d3ee 100%);
    }

    .progress-bar-custom.bg-success {
        background: linear-gradient(90deg, #10b981 0%, #34d399 100%);
    }

    .progress-bar-custom.bg-warning {
        background: linear-gradient(90deg, #f59e0b 0%, #fbbf24 100%);
    }

    .stat-subtext {
        color: #94a3b8;
        font-size: 0.75rem;
    }

    /* Cards y Headers */
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

    .card-header-dark h5, .card-header-dark h6 {
        color: var(--neon-cyan);
        font-weight: 600;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
        margin: 0;
    }

    .card-body-dark { padding: 1.5rem; }

    /* Botones */
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

    /* Filtros */
    .filter-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .filter-btn {
        background: rgba(0, 212, 255, 0.1);
        border: 1px solid rgba(0, 212, 255, 0.3);
        color: #cbd5e1;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .filter-btn:hover, .filter-btn.active {
        background: rgba(0, 212, 255, 0.2);
        border-color: var(--neon-cyan);
        color: var(--neon-cyan);
    }

    /* Badges */
    .badge-neon-sm {
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        color: #0f172a;
        padding: 0.25rem 0.6rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.7rem;
        box-shadow: 0 0 8px rgba(0, 212, 255, 0.3);
    }

    /* Acciones Rápidas */
    .quick-action-btn {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 12px;
        margin-bottom: 0.75rem;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .quick-action-btn:hover {
        background: rgba(0, 212, 255, 0.1);
        border-color: rgba(0, 212, 255, 0.5);
        transform: translateX(5px);
    }

    .quick-action-icon {
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

    .bg-purple { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
    .bg-cyan { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }
    .bg-green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }

    .quick-action-text {
        flex: 1;
    }

    .quick-action-text strong {
        display: block;
        color: #f1f5f9;
        font-weight: 600;
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }

    .quick-action-text small {
        color: #94a3b8;
        font-size: 0.75rem;
    }

    .quick-action-btn > i:last-child {
        color: #94a3b8;
        font-size: 0.875rem;
    }

    /* Empty State */
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

    /* Responsive */
    @media (max-width: 768px) {
        .stats-grid-enhanced {
            grid-template-columns: 1fr;
        }

        .filter-buttons {
            flex-wrap: wrap;
        }

        .grade-item-main {
            flex-wrap: wrap;
        }

        .mini-eval-item {
            flex-wrap: wrap;
            gap: 0.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Filtrado de calificaciones
    document.addEventListener('DOMContentLoaded', function() {
        const filterBtns = document.querySelectorAll('.filter-btn');
        const gradeItems = document.querySelectorAll('.grade-item-enhanced');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const filter = this.dataset.filter;
                
                // Actualizar botones activos
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                // Filtrar items
                gradeItems.forEach(item => {
                    if (filter === 'all') {
                        item.style.display = 'block';
                    } else {
                        if (item.dataset.filter === filter) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    }
                });
            });
        });
    });
</script>
@endpush
@endsection