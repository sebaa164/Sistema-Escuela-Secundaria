@extends('layouts.app')

@section('title', 'Mis Materias - Estudiante')
@section('page-title', 'Mis Materias')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header-dark mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-book me-3"></i>Mis Materias Inscritas
                </h1>
                <p class="header-subtitle mb-0">Período: {{ $periodoActual->nombre ?? 'Sin período activo' }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('estudiante.inscripciones.disponibles') }}" class="btn-neon">
                    <i class="fas fa-plus me-2"></i>Ver Disponibles
                </a>
                <a href="{{ route('estudiante.dashboard') }}" class="btn-outline-neon">
                    <i class="fas fa-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Estadísticas Mejoradas -->
    <div class="stats-grid-enhanced mb-4">
        <div class="stat-card-enhanced">
            <div class="stat-icon-enhanced bg-gradient-primary">
                <i class="fas fa-book"></i>
            </div>
            <div class="stat-content-enhanced">
                <h3>{{ $inscripciones->count() }}</h3>
                <p>Materias Inscritas</p>
                <div class="stat-progress">
                    <div class="progress-bar-custom" style="width: {{ min(($inscripciones->count() / 8) * 100, 100) }}%"></div>
                </div>
                <small class="stat-subtext">Carga académica actual</small>
            </div>
        </div>
        
        <div class="stat-card-enhanced">
            <div class="stat-icon-enhanced bg-gradient-info">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="stat-content-enhanced">
                <h3>{{ $inscripciones->sum(function($i) { return $i->seccion->curso->creditos; }) }}</h3>
                <p>Créditos Totales</p>
                <div class="stat-progress">
                    <div class="progress-bar-custom bg-info" style="width: {{ min(($inscripciones->sum(function($i) { return $i->seccion->curso->creditos; }) / 20) * 100, 100) }}%"></div>
                </div>
                <small class="stat-subtext">De 20 créditos máximos</small>
            </div>
        </div>
        
        <div class="stat-card-enhanced">
            <div class="stat-icon-enhanced bg-gradient-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content-enhanced">
                <h3>{{ $inscripciones->whereNotNull('nota_final')->count() }}</h3>
                <p>Materias Calificadas</p>
                <div class="stat-progress">
                    <div class="progress-bar-custom bg-success" style="width: {{ $inscripciones->count() > 0 ? ($inscripciones->whereNotNull('nota_final')->count() / $inscripciones->count()) * 100 : 0 }}%"></div>
                </div>
                <small class="stat-subtext">{{ $inscripciones->whereNull('nota_final')->count() }} pendientes</small>
            </div>
        </div>
        
        <div class="stat-card-enhanced">
            <div class="stat-icon-enhanced bg-gradient-warning">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content-enhanced">
                <h3>{{ $inscripciones->whereNotNull('nota_final')->avg('nota_final') ? number_format($inscripciones->whereNotNull('nota_final')->avg('nota_final'), 2) : 'N/A' }}</h3>
                <p>Promedio General</p>
                <div class="stat-progress">
                    @if($inscripciones->whereNotNull('nota_final')->avg('nota_final'))
                        <div class="progress-bar-custom bg-warning" style="width: {{ ($inscripciones->whereNotNull('nota_final')->avg('nota_final') / 100) * 100 }}%"></div>
                    @endif
                </div>
                <small class="stat-subtext">Rendimiento académico</small>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Columna Principal: Materias -->
        <div class="col-lg-8">
            <div class="card-dark">
                <div class="card-header-dark">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>Listado de Materias
                        </h5>
                        <div class="filter-buttons">
                            <button class="filter-btn active" data-filter="all">
                                <i class="fas fa-border-all me-1"></i>Todas
                            </button>
                            <button class="filter-btn" data-filter="calificadas">
                                <i class="fas fa-check me-1"></i>Calificadas
                            </button>
                            <button class="filter-btn" data-filter="pendientes">
                                <i class="fas fa-clock me-1"></i>Pendientes
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body-dark">
                    @forelse($inscripciones as $inscripcion)
                        <div class="course-item {{ $inscripcion->nota_final ? 'calificadas' : 'pendientes' }}" data-filter="{{ $inscripcion->nota_final ? 'calificadas' : 'pendientes' }}">
                            <div class="course-item-header">
                                <div class="course-icon-wrapper">
                                    <div class="course-icon-mini">
                                        <i class="fas fa-book"></i>
                                    </div>
                                </div>
                                <div class="course-info-wrapper">
                                    <h6>{{ $inscripcion->seccion->curso->nombre }}</h6>
                                    <div class="course-meta-tags">
                                        <span class="badge-neon-sm">{{ $inscripcion->seccion->curso->codigo_curso }}</span>
                                        <span class="badge-outline-sm">Sección {{ $inscripcion->seccion->codigo_seccion }}</span>
                                        <span class="badge-info-sm">{{ $inscripcion->seccion->curso->creditos }} créditos</span>
                                    </div>
                                </div>
                                <div class="course-grade-display">
                                    @if($inscripcion->nota_final)
                                        <div class="mini-grade-circle grade-{{ $inscripcion->nota_final >= 70 ? 'pass' : 'fail' }}">
                                            {{ number_format($inscripcion->nota_final, 0) }}
                                        </div>
                                    @else
                                        <div class="mini-grade-circle grade-pending">
                                            --
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="course-item-body">
                                <div class="course-detail-row">
                                    <i class="fas fa-user-tie"></i>
                                    <span>{{ $inscripcion->seccion->profesor->nombre_completo }}</span>
                                </div>
                                <div class="course-detail-row">
                                    <i class="fas fa-door-open"></i>
                                    <span>{{ $inscripcion->seccion->aula ?? 'No asignada' }}</span>
                                </div>
                                <div class="course-detail-row">
                                    <i class="fas fa-clock"></i>
                                    <span>{{ $inscripcion->seccion->horario ?? 'No definido' }}</span>
                                </div>
                            </div>
                            <div class="course-item-footer">
                                <a href="{{ route('estudiante.inscripciones.show', $inscripcion->id) }}" class="btn-mini">
                                    <i class="fas fa-eye"></i> Ver
                                </a>
                                <a href="{{ route('estudiante.calificaciones.materia', $inscripcion->id) }}" class="btn-mini btn-mini-primary">
                                    <i class="fas fa-star"></i> Calificaciones
                                </a>
                                <a href="{{ route('estudiante.asistencias.materia', $inscripcion->id) }}" class="btn-mini btn-mini-success">
                                    <i class="fas fa-calendar-check"></i> Asistencias
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state-compact">
                            <i class="fas fa-book-open fa-3x mb-3"></i>
                            <h5>No tienes materias inscritas</h5>
                            <p>Aún no te has inscrito en ninguna materia para este período</p>
                            <a href="{{ route('estudiante.inscripciones.disponibles') }}" class="btn-neon mt-3">
                                <i class="fas fa-plus me-2"></i>Ver Materias Disponibles
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar: Resumen e Información -->
        <div class="col-lg-4">
            <!-- Progreso del Período -->
            <div class="card-dark mb-3">
                <div class="card-header-dark">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Progreso del Período
                    </h6>
                </div>
                <div class="card-body-dark">
                    @php
                        $totalMaterias = $inscripciones->count();
                        $materiasCalificadas = $inscripciones->whereNotNull('nota_final')->count();
                        $progreso = $totalMaterias > 0 ? ($materiasCalificadas / $totalMaterias) * 100 : 0;
                    @endphp
                    
                    <div class="circular-progress">
                        <svg viewBox="0 0 100 100">
                            <circle cx="50" cy="50" r="45" class="progress-bg"></circle>
                            <circle cx="50" cy="50" r="45" class="progress-fill" 
                                    style="stroke-dashoffset: {{ 283 - (283 * $progreso / 100) }}"></circle>
                            <text x="50" y="50" class="progress-text">{{ number_format($progreso, 0) }}%</text>
                        </svg>
                    </div>
                    
                    <div class="progress-details">
                        <div class="progress-detail-item">
                            <span class="dot dot-success"></span>
                            <span>{{ $materiasCalificadas }} Calificadas</span>
                        </div>
                        <div class="progress-detail-item">
                            <span class="dot dot-warning"></span>
                            <span>{{ $totalMaterias - $materiasCalificadas }} Pendientes</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Distribución por Créditos -->
            <div class="card-dark mb-3">
                <div class="card-header-dark">
                    <h6 class="mb-0">
                        <i class="fas fa-graduation-cap me-2"></i>Distribución por Créditos
                    </h6>
                </div>
                <div class="card-body-dark">
                    @php
                        $creditosAgrupados = $inscripciones->groupBy(function($i) {
                            return $i->seccion->curso->creditos;
                        })->map(function($group) {
                            return $group->count();
                        });
                    @endphp
                    
                    @foreach($creditosAgrupados as $creditos => $cantidad)
                        <div class="credit-bar-item">
                            <div class="credit-bar-label">
                                <span>{{ $creditos }} créditos</span>
                                <strong>{{ $cantidad }} {{ $cantidad == 1 ? 'materia' : 'materias' }}</strong>
                            </div>
                            <div class="credit-bar">
                                <div class="credit-bar-fill" style="width: {{ ($cantidad / $inscripciones->count()) * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach
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
                    <a href="{{ route('estudiante.inscripciones.historial') }}" class="quick-action-btn">
                        <div class="quick-action-icon bg-purple">
                            <i class="fas fa-history"></i>
                        </div>
                        <div class="quick-action-text">
                            <strong>Historial Académico</strong>
                            <small>Ver todas tus materias</small>
                        </div>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    
                    <a href="{{ route('estudiante.calificaciones.index') }}" class="quick-action-btn">
                        <div class="quick-action-icon bg-cyan">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="quick-action-text">
                            <strong>Todas las Calificaciones</strong>
                            <small>Revisar todas tus notas</small>
                        </div>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                    
                    <a href="{{ route('estudiante.asistencias.index') }}" class="quick-action-btn">
                        <div class="quick-action-icon bg-green">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="quick-action-text">
                            <strong>Registro de Asistencias</strong>
                            <small>Ver tu asistencia</small>
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
        --electric-blue: #0ea5e9;
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

    /* Estadísticas Mejoradas */
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

    /* Botones */
    .btn-neon {
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        border: none;
        color: #0f172a;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: all 0.3s ease;
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
    }

    .btn-neon:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 30px rgba(0, 212, 255, 0.6);
        color: #0f172a;
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

    /* Cards */
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

    /* Items de Curso */
    .course-item {
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 12px;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    .course-item:hover {
        border-color: rgba(0, 212, 255, 0.5);
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.2);
        transform: translateX(5px);
    }

    .course-item-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        border-bottom: 1px solid rgba(0, 212, 255, 0.1);
    }

    .course-icon-mini {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: #0f172a;
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.4);
    }

    .course-info-wrapper {
        flex: 1;
    }

    .course-info-wrapper h6 {
        color: #f1f5f9;
        margin: 0 0 0.5rem 0;
        font-weight: 600;
        font-size: 1rem;
    }

    .course-meta-tags {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
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

    .badge-outline-sm {
        background: transparent;
        border: 1px solid rgba(0, 212, 255, 0.5);
        color: var(--neon-cyan);
        padding: 0.25rem 0.6rem;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 500;
    }

    .badge-info-sm {
        background: rgba(6, 182, 212, 0.2);
        color: #22d3ee;
        border: 1px solid #22d3ee;
        padding: 0.25rem 0.6rem;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .mini-grade-circle {
        width: 55px;
        height: 55px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        font-weight: 700;
    }

    .mini-grade-circle.grade-pass {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 3px solid #10b981;
    }

    .mini-grade-circle.grade-fail {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 3px solid #ef4444;
    }

    .mini-grade-circle.grade-pending {
        background: rgba(148, 163, 184, 0.2);
        color: #cbd5e1;
        border: 3px solid #94a3b8;
    }

    .course-item-body {
        padding: 1rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .course-detail-row {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: #cbd5e1;
        font-size: 0.875rem;
    }

    .course-detail-row i {
        color: #94a3b8;
        width: 16px;
    }

    .course-item-footer {
        padding: 1rem;
        background: rgba(15, 23, 42, 0.5);
        border-top: 1px solid rgba(0, 212, 255, 0.1);
        display: flex;
        gap: 0.5rem;
    }

    .btn-mini {
        flex: 1;
        padding: 0.6rem;
        border-radius: 8px;
        text-align: center;
        text-decoration: none;
        font-size: 0.8rem;
        font-weight: 600;
        transition: all 0.3s ease;
        background: rgba(0, 212, 255, 0.1);
        border: 1px solid rgba(0, 212, 255, 0.3);
        color: var(--neon-cyan);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-mini:hover {
        background: rgba(0, 212, 255, 0.2);
        border-color: var(--neon-cyan);
        transform: translateY(-2px);
        color: var(--neon-cyan);
    }

    .btn-mini-primary {
        background: rgba(139, 92, 246, 0.1);
        border-color: rgba(139, 92, 246, 0.3);
        color: #a78bfa;
    }

    .btn-mini-primary:hover {
        background: rgba(139, 92, 246, 0.2);
        border-color: #a78bfa;
        color: #a78bfa;
    }

    .btn-mini-success {
        background: rgba(16, 185, 129, 0.1);
        border-color: rgba(16, 185, 129, 0.3);
        color: #10b981;
    }

    .btn-mini-success:hover {
        background: rgba(16, 185, 129, 0.2);
        border-color: #10b981;
        color: #10b981;
    }

    /* Progreso Circular */
    .circular-progress {
        width: 150px;
        height: 150px;
        margin: 0 auto 1.5rem;
    }

    .circular-progress svg {
        transform: rotate(-90deg);
    }

    .progress-bg {
        fill: none;
        stroke: rgba(100, 116, 139, 0.2);
        stroke-width: 8;
    }

    .progress-fill {
        fill: none;
        stroke: url(#gradient);
        stroke-width: 8;
        stroke-linecap: round;
        stroke-dasharray: 283;
        transition: stroke-dashoffset 1s ease;
    }

    .progress-text {
        fill: var(--neon-cyan);
        font-size: 20px;
        font-weight: 700;
        text-anchor: middle;
        dominant-baseline: middle;
        transform: rotate(90deg);
        transform-origin: center;
    }

    .progress-details {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .progress-detail-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: #cbd5e1;
        font-size: 0.875rem;
    }

    .dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }

    .dot-success { background: #10b981; box-shadow: 0 0 10px rgba(16, 185, 129, 0.5); }
    .dot-warning { background: #f59e0b; box-shadow: 0 0 10px rgba(245, 158, 11, 0.5); }

    /* Distribución por Créditos */
    .credit-bar-item {
        margin-bottom: 1rem;
    }

    .credit-bar-label {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }

    .credit-bar-label span {
        color: #cbd5e1;
    }

    .credit-bar-label strong {
        color: var(--neon-cyan);
        font-weight: 600;
    }

    .credit-bar {
        height: 8px;
        background: rgba(100, 116, 139, 0.2);
        border-radius: 10px;
        overflow: hidden;
    }

    .credit-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        border-radius: 10px;
        transition: width 0.6s ease;
        box-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
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

        .course-item-header {
            flex-wrap: wrap;
        }

        .course-item-footer {
            flex-direction: column;
        }

        .btn-mini {
            width: 100%;
        }
    }
</style>

<svg width="0" height="0">
    <defs>
        <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="0%">
            <stop offset="0%" style="stop-color:#00d4ff;stop-opacity:1" />
            <stop offset="100%" style="stop-color:#00ffff;stop-opacity:1" />
        </linearGradient>
    </defs>
</svg>
@endpush

@push('scripts')
<script>
    // Filtrado de materias
    document.addEventListener('DOMContentLoaded', function() {
        const filterBtns = document.querySelectorAll('.filter-btn');
        const courseItems = document.querySelectorAll('.course-item');

        filterBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const filter = this.dataset.filter;
                
                // Actualizar botones activos
                filterBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                // Filtrar items
                courseItems.forEach(item => {
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