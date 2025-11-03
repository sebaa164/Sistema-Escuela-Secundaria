@extends('layouts.app')

@section('title', 'Detalle de Materia')
@section('page-title', 'Detalle de Materia')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header-dark mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-book me-3"></i>{{ $inscripcion->seccion->curso->nombre }}
                </h1>
                <p class="header-subtitle mb-0">
                    {{ $inscripcion->seccion->codigo_seccion }} - {{ $inscripcion->seccion->periodo->nombre }}
                </p>
            </div>
            <a href="{{ route('estudiante.inscripciones.index') }}" class="btn-outline-neon">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Información de la Materia -->
        <div class="col-lg-4">
            <div class="card-dark">
                <div class="card-header-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Información
                    </h5>
                </div>
                <div class="card-body-dark">
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-book me-2"></i>Curso:</span>
                        <span class="info-value">{{ $inscripcion->seccion->curso->nombre }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-code me-2"></i>Código:</span>
                        <span class="info-value">{{ $inscripcion->seccion->curso->codigo_curso }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-tag me-2"></i>Sección:</span>
                        <span class="info-value">{{ $inscripcion->seccion->codigo_seccion }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-user-tie me-2"></i>Profesor:</span>
                        <span class="info-value">{{ $inscripcion->seccion->profesor->nombre_completo }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-graduation-cap me-2"></i>Créditos:</span>
                        <span class="info-value">{{ $inscripcion->seccion->curso->creditos }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-door-open me-2"></i>Aula:</span>
                        <span class="info-value">{{ $inscripcion->seccion->aula ?? 'No asignada' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-clock me-2"></i>Horario:</span>
                        <span class="info-value">{{ $inscripcion->seccion->horario ?? 'No definido' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-calendar me-2"></i>Período:</span>
                        <span class="info-value">{{ $inscripcion->seccion->periodo->nombre }}</span>
                    </div>
                </div>
            </div>

            <!-- Calificación Final -->
            <div class="card-dark mt-3">
                <div class="card-header-dark">
                    <h6 class="mb-0">
                        <i class="fas fa-star me-2"></i>Calificación Final
                    </h6>
                </div>
                <div class="card-body-dark text-center">
                    @if($inscripcion->nota_final)
                        <div class="final-grade-display grade-{{ $inscripcion->nota_final >= 70 ? 'pass' : 'fail' }}">
                            {{ number_format($inscripcion->nota_final, 2) }}
                        </div>
                        <p class="grade-status mt-3">
                            @if($inscripcion->nota_final >= 70)
                                <span class="badge-success-lg">
                                    <i class="fas fa-check-circle me-2"></i>Aprobado
                                </span>
                            @else
                                <span class="badge-danger-lg">
                                    <i class="fas fa-times-circle me-2"></i>Reprobado
                                </span>
                            @endif
                        </p>
                    @else
                        <div class="final-grade-display grade-pending">
                            --
                        </div>
                        <p class="text-muted mt-3">Calificación pendiente</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Evaluaciones -->
        <div class="col-lg-8">
            <div class="card-dark">
                <div class="card-header-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i>Evaluaciones
                    </h5>
                </div>
                <div class="card-body-dark">
                    <div class="table-responsive">
                        <table class="table-dark">
                            <thead>
                                <tr>
                                    <th>Evaluación</th>
                                    <th>Tipo</th>
                                    <th>Fecha</th>
                                    <th>Porcentaje</th>
                                    <th>Nota</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($inscripcion->seccion->evaluaciones as $evaluacion)
                                    @php
                                        $calificacion = $calificaciones->get($evaluacion->id);
                                    @endphp
                                    <tr>
                                        <td>
                                            <strong>{{ $evaluacion->nombre }}</strong>
                                            @if($evaluacion->descripcion)
                                                <br><small class="text-muted">{{ Str::limit($evaluacion->descripcion, 40) }}</small>
                                            @endif
                                        </td>
                                        <td><span class="badge-info">{{ $evaluacion->tipoEvaluacion->nombre ?? 'N/A' }}</span></td>
                                        <td>
                                            <i class="fas fa-calendar me-1"></i>{{ $evaluacion->fecha_evaluacion->format('d/m/Y') }}
                                        </td>
                                        <td><span class="weight-badge">{{ $evaluacion->porcentaje }}%</span></td>
                                        <td>
                                            @if($calificacion && $calificacion->nota !== null)
                                                <span class="grade-badge-sm grade-{{ $calificacion->nota >= 70 ? 'pass' : 'fail' }}">
                                                    {{ number_format($calificacion->nota, 2) }}
                                                </span>
                                            @else
                                                <span class="badge-pending">Pendiente</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($evaluacion->estado === 'activa')
                                                <span class="status-badge status-active">
                                                    <i class="fas fa-circle"></i> Activa
                                                </span>
                                            @elseif($evaluacion->estado === 'finalizada')
                                                <span class="status-badge status-finalizada">
                                                    <i class="fas fa-circle"></i> Finalizada
                                                </span>
                                            @else
                                                <span class="status-badge status-programada">
                                                    <i class="fas fa-circle"></i> Programada
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="fas fa-file-alt fa-2x mb-2"></i>
                                                <p>No hay evaluaciones registradas</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
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

    .header-subtitle { color: #94a3b8; }

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
    .info-label { color: #94a3b8; font-size: 0.875rem; }
    .info-value { color: #e2e8f0; font-weight: 500; }

    .final-grade-display {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0 auto;
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

    .grade-pending {
        background: rgba(100, 116, 139, 0.2);
        color: #94a3b8;
        border: 4px solid #64748b;
    }

    .badge-success-lg, .badge-danger-lg {
        padding: 0.75rem 1.5rem;
        border-radius: 25px;
        font-weight: 600;
        font-size: 1rem;
        display: inline-block;
    }

    .badge-success-lg {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 2px solid #10b981;
    }

    .badge-danger-lg {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 2px solid #ef4444;
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

    .table-dark { width: 100%; color: #e2e8f0; }

    .table-dark thead th {
        background: linear-gradient(135deg, rgba(14, 165, 233, 0.3) 0%, rgba(0, 212, 255, 0.2) 100%);
        color: var(--neon-cyan);
        text-transform: uppercase;
        font-size: 0.875rem;
        font-weight: 600;
        padding: 1rem;
        border: none;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .table-dark tbody tr { border-bottom: 1px solid rgba(0, 212, 255, 0.1); transition: all 0.3s ease; }
    .table-dark tbody tr:hover { background: rgba(0, 212, 255, 0.05); }
    .table-dark tbody td { padding: 1rem; vertical-align: middle; }

    .badge-info {
        background: rgba(6, 182, 212, 0.2);
        color: #06b6d4;
        border: 1px solid #06b6d4;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .weight-badge {
        background: rgba(245, 158, 11, 0.2);
        color: #f59e0b;
        border: 1px solid #f59e0b;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .grade-badge-sm {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 700;
        font-size: 0.875rem;
    }

    .badge-pending {
        background: rgba(100, 116, 139, 0.2);
        color: #94a3b8;
        border: 1px solid #64748b;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

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
    .status-finalizada { background: rgba(100, 116, 139, 0.2); color: #64748b; border: 1px solid #64748b; }
    .status-programada { background: rgba(245, 158, 11, 0.2); color: #f59e0b; border: 1px solid #f59e0b; }
    .status-badge i { font-size: 0.5rem; animation: pulse 2s infinite; }

    @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }

    .empty-state { color: #94a3b8; padding: 2rem; text-align: center; }
    .empty-state i { color: rgba(0, 212, 255, 0.3); }
</style>
@endpush
@endsection
