@extends('layouts.app')

@section('title', 'Calificaciones - ' . $inscripcion->seccion->curso->nombre)
@section('page-title', 'Calificaciones')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header-dark mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-star me-3"></i>{{ $inscripcion->seccion->curso->nombre }}
                </h1>
                <p class="header-subtitle mb-0">Calificaciones detalladas</p>
            </div>
            <a href="{{ route('estudiante.calificaciones.index') }}" class="btn-outline-neon">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="stats-grid mb-4">
        <div class="stat-card">
            <div class="stat-icon bg-gradient-primary">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $estadisticas['total_evaluaciones'] }}</h3>
                <p>Total Evaluaciones</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $estadisticas['evaluaciones_calificadas'] }}</h3>
                <p>Calificadas</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-warning">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
                <h3>{{ number_format($estadisticas['nota_actual'], 2) }}</h3>
                <p>Nota Actual</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-info">
                <i class="fas fa-percent"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $estadisticas['porcentaje_completado'] }}%</h3>
                <p>Completado</p>
            </div>
        </div>
    </div>

    <!-- Tabla de Evaluaciones -->
    <div class="card-dark">
        <div class="card-header-dark">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Evaluaciones y Calificaciones
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
                            <th>Puntos</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($evaluaciones as $evaluacion)
                            @php
                                $calificacion = $calificaciones->get($evaluacion->id);
                                $notaPonderada = ($calificacion && $calificacion->nota) ? ($calificacion->nota * $evaluacion->porcentaje / 100) : 0;
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $evaluacion->nombre }}</strong>
                                    @if($evaluacion->descripcion)
                                        <br><small class="text-muted">{{ Str::limit($evaluacion->descripcion, 50) }}</small>
                                    @endif
                                </td>
                                <td><span class="badge-info">{{ $evaluacion->tipoEvaluacion->nombre ?? 'N/A' }}</span></td>
                                <td>
                                    <i class="fas fa-calendar me-1"></i>{{ $evaluacion->fecha_evaluacion->format('d/m/Y') }}
                                </td>
                                <td><span class="weight-badge">{{ $evaluacion->porcentaje }}%</span></td>
                                <td>
                                    @if($calificacion && $calificacion->nota !== null)
                                        <span class="grade-badge grade-{{ $calificacion->nota >= 70 ? 'pass' : 'fail' }}">
                                            {{ number_format($calificacion->nota, 2) }}
                                        </span>
                                    @else
                                        <span class="badge-pending">Pendiente</span>
                                    @endif
                                </td>
                                <td>
                                    @if($calificacion && $calificacion->nota !== null)
                                        <strong class="text-cyan">{{ number_format($notaPonderada, 2) }}</strong>
                                    @else
                                        <span class="text-muted">--</span>
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
                                <td colspan="7" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-file-alt fa-3x mb-3"></i>
                                        <h5>No hay evaluaciones</h5>
                                        <p>Aún no se han creado evaluaciones para esta materia</p>
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

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    .stat-card {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(30, 41, 59, 0.9) 100%);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 15px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.3s ease;
    }

    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 0 30px rgba(0, 212, 255, 0.3); }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }

    .bg-gradient-primary { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); }
    .bg-gradient-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .bg-gradient-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .bg-gradient-info { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }

    .stat-content h3 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--neon-cyan);
        margin: 0;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .stat-content p { color: #94a3b8; margin: 0; font-size: 0.875rem; }

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

    .card-header-dark h5 { color: var(--neon-cyan); font-weight: 600; text-shadow: 0 0 10px rgba(0, 212, 255, 0.5); margin: 0; }
    .card-body-dark { padding: 2rem; }

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

    .grade-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 700;
        font-size: 0.875rem;
    }

    .grade-pass {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 2px solid #10b981;
    }

    .grade-fail {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 2px solid #ef4444;
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

    .text-cyan { color: var(--neon-cyan); }

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

    .empty-state { color: #94a3b8; padding: 3rem; text-align: center; }
    .empty-state i { color: rgba(0, 212, 255, 0.3); }
    .empty-state h5 { color: #e2e8f0; margin: 1rem 0; }

    /* ... todos tus estilos existentes ... */

    /* 🎨 MEJORAS DE VISIBILIDAD */
    .text-muted {
        color: #cbd5e1 !important; /* Gris más claro y visible */
    }

    .table-dark tbody td {
        color: #f1f5f9; /* Texto más blanco en las celdas */
    }

    .table-dark tbody td strong {
        color: #ffffff; /* Títulos completamente blancos */
    }

    .table-dark tbody td small {
        color: #cbd5e1; /* Texto pequeño más visible */
    }

    /* Mejorar contraste de los badges */
    .badge-info {
        background: rgba(6, 182, 212, 0.3); /* Fondo más opaco */
        color: #22d3ee; /* Color más brillante */
        border: 1px solid #22d3ee;
    }

    .weight-badge {
        background: rgba(245, 158, 11, 0.3); /* Fondo más opaco */
        color: #fbbf24; /* Color más brillante */
        border: 1px solid #fbbf24;
    }

    .badge-pending {
        background: rgba(148, 163, 184, 0.3); /* Fondo más opaco */
        color: #cbd5e1; /* Color más claro */
        border: 1px solid #cbd5e1;
    }

    /* Mejorar visibilidad del texto cyan */
    .text-cyan {
        color: #22d3ee !important; /* Cyan más brillante */
        text-shadow: 0 0 10px rgba(34, 211, 238, 0.5);
    }

    /* Íconos del calendario más visibles */
    .fas.fa-calendar {
        color: #94a3b8; /* Gris más claro para íconos */
    }
</style>
@endpush
@endsection
