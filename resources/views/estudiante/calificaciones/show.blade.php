@extends('layouts.app')

@section('title', 'Detalle de Calificación')
@section('page-title', 'Detalle de Calificación')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header-dark mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-file-alt me-3"></i>Detalle de Calificación
                </h1>
                <p class="header-subtitle mb-0">{{ $calificacion->evaluacion->nombre }}</p>
            </div>
            <a href="{{ route('estudiante.calificaciones.materia', $calificacion->evaluacion->seccion->inscripciones->where('estudiante_id', auth()->id())->first()->id ?? '#') }}" class="btn-outline-neon">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Información de la Evaluación -->
        <div class="col-lg-4">
            <div class="card-dark">
                <div class="card-header-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Información de la Evaluación
                    </h5>
                </div>
                <div class="card-body-dark">
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-file-alt me-2"></i>Evaluación:</span>
                        <span class="info-value">{{ $calificacion->evaluacion->nombre }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-tag me-2"></i>Tipo:</span>
                        <span class="info-value">{{ $calificacion->evaluacion->tipoEvaluacion->nombre ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-book me-2"></i>Curso:</span>
                        <span class="info-value">{{ $calificacion->evaluacion->seccion->curso->nombre }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-code me-2"></i>Código:</span>
                        <span class="info-value">{{ $calificacion->evaluacion->seccion->curso->codigo_curso }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-user-tie me-2"></i>Profesor:</span>
                        <span class="info-value">{{ $calificacion->evaluacion->seccion->profesor->nombre_completo }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-calendar me-2"></i>Fecha Evaluación:</span>
                        <span class="info-value">{{ $calificacion->evaluacion->fecha_evaluacion->format('d/m/Y') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-percent me-2"></i>Porcentaje:</span>
                        <span class="weight-badge">{{ $calificacion->evaluacion->porcentaje }}%</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-trophy me-2"></i>Nota Máxima:</span>
                        <span class="info-value">{{ $calificacion->evaluacion->nota_maxima }}</span>
                    </div>
                </div>
            </div>

            <!-- Descripción de la Evaluación -->
            @if($calificacion->evaluacion->descripcion)
            <div class="card-dark mt-3">
                <div class="card-header-dark">
                    <h6 class="mb-0">
                        <i class="fas fa-align-left me-2"></i>Descripción
                    </h6>
                </div>
                <div class="card-body-dark">
                    <p class="info-value mb-0">{{ $calificacion->evaluacion->descripcion }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Calificación Obtenida -->
        <div class="col-lg-8">
            <div class="card-dark">
                <div class="card-header-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-star me-2"></i>Tu Calificación
                    </h5>
                </div>
                <div class="card-body-dark">
                    <div class="row g-4">
                        <!-- Nota Obtenida -->
                        <div class="col-md-4">
                            <div class="grade-display-card">
                                <div class="grade-circle grade-{{ $calificacion->nota >= 70 ? 'pass' : 'fail' }}">
                                    @if($calificacion->nota !== null)
                                        {{ number_format($calificacion->nota, 2) }}
                                    @else
                                        --
                                    @endif
                                </div>
                                <p class="grade-label">Nota Obtenida</p>
                            </div>
                        </div>

                        <!-- Puntos Obtenidos -->
                        <div class="col-md-4">
                            <div class="grade-display-card">
                                <div class="grade-circle grade-aporte">
                                    @if($calificacion->nota !== null)
                                        {{ number_format(($calificacion->nota * $calificacion->evaluacion->porcentaje) / 100, 2) }}
                                    @else
                                        --
                                    @endif
                                </div>
                                <p class="grade-label">Puntos Obtenidos</p>
                                <small class="grade-sublabel">(Nota × Porcentaje)</small>
                            </div>
                        </div>

                        <!-- Porcentaje Obtenido -->
                        <div class="col-md-4">
                            <div class="grade-display-card">
                                <div class="grade-circle grade-percentage">
                                    @if($calificacion->nota !== null)
                                        {{ number_format(($calificacion->nota / $calificacion->evaluacion->nota_maxima) * 100, 1) }}%
                                    @else
                                        --%
                                    @endif
                                </div>
                                <p class="grade-label">Porcentaje Logrado</p>
                            </div>
                        </div>
                    </div>

                    <!-- Estado y Fechas -->
                    <div class="info-grid mt-4">
                        <div class="info-box">
                            <div class="info-box-icon bg-info">
                                <i class="fas fa-hourglass-half"></i>
                            </div>
                            <div>
                                <small class="info-box-label">Estado</small>
                                <p class="info-box-value">
                                    @if($calificacion->estado === 'calificada')
                                        <span class="status-badge status-finalizada">
                                            <i class="fas fa-check-circle"></i> Calificada
                                        </span>
                                    @elseif($calificacion->estado === 'revisada')
                                        <span class="status-badge status-active">
                                            <i class="fas fa-eye"></i> Revisada
                                        </span>
                                    @else
                                        <span class="status-badge status-programada">
                                            <i class="fas fa-clock"></i> Pendiente
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="info-box">
                            <div class="info-box-icon bg-success">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div>
                                <small class="info-box-label">Fecha Calificación</small>
                                <p class="info-box-value">
                                    {{ $calificacion->fecha_calificacion ? $calificacion->fecha_calificacion->format('d/m/Y H:i') : 'Pendiente' }}
                                </p>
                            </div>
                        </div>

                        @if($calificacion->fecha_revision)
                        <div class="info-box">
                            <div class="info-box-icon bg-warning">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div>
                                <small class="info-box-label">Fecha Revisión</small>
                                <p class="info-box-value">
                                    {{ $calificacion->fecha_revision->format('d/m/Y H:i') }}
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Comentarios del Profesor -->
                    @if($calificacion->comentarios)
                    <div class="comentarios-section mt-4">
                        <h6 class="comentarios-title">
                            <i class="fas fa-comment-dots me-2"></i>Comentarios del Profesor
                        </h6>
                        <div class="comentarios-box">
                            <p class="mb-0">{{ $calificacion->comentarios }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Retroalimentación -->
                    @if($calificacion->retroalimentacion)
                    <div class="comentarios-section mt-3">
                        <h6 class="comentarios-title">
                            <i class="fas fa-lightbulb me-2"></i>Retroalimentación
                        </h6>
                        <div class="comentarios-box retroalimentacion">
                            <p class="mb-0">{{ $calificacion->retroalimentacion }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Estadísticas Comparativas -->
            <div class="card-dark mt-3">
                <div class="card-header-dark">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Análisis de Desempeño
                    </h6>
                </div>
                <div class="card-body-dark">
                    <div class="analysis-grid">
                        <div class="analysis-item">
                            <div class="analysis-icon bg-gradient-success">
                                <i class="fas fa-bullseye"></i>
                            </div>
                            <div>
                                <h4 class="analysis-value">
                                    @if($calificacion->nota >= 70)
                                        <span class="text-success">Aprobado</span>
                                    @else
                                        <span class="text-danger">No Aprobado</span>
                                    @endif
                                </h4>
                                <p class="analysis-label">Estado de Aprobación</p>
                            </div>
                        </div>

                        <div class="analysis-item">
                            <div class="analysis-icon bg-gradient-info">
                                <i class="fas fa-percentage"></i>
                            </div>
                            <div>
                                <h4 class="analysis-value">{{ $calificacion->evaluacion->porcentaje }}%</h4>
                                <p class="analysis-label">Valor en Nota Final</p>
                            </div>
                        </div>

                        <div class="analysis-item">
                            <div class="analysis-icon bg-gradient-warning">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <div>
                                <h4 class="analysis-value">
                                    @if($calificacion->nota !== null)
                                        {{ number_format(($calificacion->nota / $calificacion->evaluacion->nota_maxima) * 100, 0) }}%
                                    @else
                                        --
                                    @endif
                                </h4>
                                <p class="analysis-label">Rendimiento</p>
                            </div>
                        </div>
                    </div>
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

    .card-header-dark h5, .card-header-dark h6 {
        color: var(--neon-cyan);
        font-weight: 600;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
        margin: 0;
    }

    .card-body-dark { padding: 2rem; }

    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(0, 212, 255, 0.1);
    }

    .info-item:last-child { border-bottom: none; }
    .info-label { color: #cbd5e1; font-size: 0.875rem; font-weight: 500; }
    .info-value { color: #f1f5f9; font-weight: 600; text-align: right; }

    .weight-badge {
        background: rgba(245, 158, 11, 0.3);
        color: #fbbf24;
        border: 1px solid #fbbf24;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .btn-outline-neon {
        background: transparent;
        border: 2px solid var(--neon-cyan);
        color: var(--neon-cyan);
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .btn-outline-neon:hover {
        background: rgba(0, 212, 255, 0.1);
        transform: translateY(-2px);
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
        color: var(--neon-cyan);
    }

    .grade-display-card { text-align: center; }

    .grade-circle {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: 700;
        margin: 0 auto 1rem;
    }

    .grade-pass {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 4px solid #10b981;
        box-shadow: 0 0 30px rgba(16, 185, 129, 0.5);
    }

    .grade-fail {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 4px solid #ef4444;
        box-shadow: 0 0 30px rgba(239, 68, 68, 0.5);
    }

    .grade-aporte {
        background: rgba(139, 92, 246, 0.2);
        color: #a78bfa;
        border: 4px solid #a78bfa;
    }

    .grade-sublabel {
        display: block;
        color: #64748b;
        font-size: 0.75rem;
        margin-top: 0.25rem;
        font-style: italic;
        box-shadow: 0 0 30px rgba(139, 92, 246, 0.5);
    }

    .grade-percentage {
        background: rgba(6, 182, 212, 0.2);
        color: #22d3ee;
        border: 4px solid #22d3ee;
        box-shadow: 0 0 30px rgba(6, 182, 212, 0.5);
    }

    .grade-label { color: #cbd5e1; font-size: 0.875rem; font-weight: 600; }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .info-box {
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
        padding: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .info-box-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
    }

    .bg-info { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }
    .bg-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .bg-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }

    .info-box-label { color: #94a3b8; font-size: 0.75rem; display: block; margin-bottom: 0.25rem; }
    .info-box-value { color: #f1f5f9; font-weight: 600; margin: 0; font-size: 0.875rem; }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-active { background: rgba(16, 185, 129, 0.2); color: #10b981; border: 1px solid #10b981; }
    .status-finalizada { background: rgba(100, 116, 139, 0.2); color: #cbd5e1; border: 1px solid #cbd5e1; }
    .status-programada { background: rgba(245, 158, 11, 0.2); color: #fbbf24; border: 1px solid #fbbf24; }

    .comentarios-section { 
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
        padding: 1.5rem;
    }

    .comentarios-title {
        color: var(--neon-cyan);
        font-size: 0.875rem;
        margin-bottom: 1rem;
        text-transform: uppercase;
        font-weight: 600;
    }

    .comentarios-box {
        background: rgba(30, 41, 59, 0.5);
        border-left: 4px solid #06b6d4;
        border-radius: 5px;
        padding: 1rem;
    }

    .comentarios-box.retroalimentacion {
        border-left-color: #a78bfa;
    }

    .comentarios-box p {
        color: #f1f5f9;
        line-height: 1.6;
    }

    .analysis-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
    }

    .analysis-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
        padding: 1.25rem;
    }

    .analysis-icon {
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

    .bg-gradient-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .bg-gradient-info { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }
    .bg-gradient-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }

    .analysis-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--neon-cyan);
        margin: 0;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.3);
    }

    .text-success { color: #10b981 !important; }
    .text-danger { color: #ef4444 !important; }

    .analysis-label {
        color: #cbd5e1;
        font-size: 0.875rem;
        margin: 0.25rem 0 0 0;
    }
</style>
@endpush
@endsection