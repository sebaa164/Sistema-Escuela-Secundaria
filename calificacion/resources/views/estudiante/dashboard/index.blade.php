@extends('layouts.app')

@section('title', 'Dashboard - Estudiante')
@section('page-title', 'Panel de Estudiante')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header-dark mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="header-title">
                    <i class="fas fa-user-graduate me-3"></i>Panel de Control - Estudiante
                </h1>
                <p class="header-subtitle">Bienvenido, {{ $estudiante->nombre_completo }}</p>
            </div>
            <div class="col-md-4 text-end">
                @if($periodoActual)
                    <div class="badge-dracula-lg">
                        <i class="fas fa-calendar-check me-2"></i>{{ $periodoActual->nombre }}
                    </div>
                @else
                    <div class="badge-warning-lg">
                        <i class="fas fa-exclamation-triangle me-2"></i>Sin período activo
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Tarjetas de Estadísticas -->
    <div class="stats-grid mb-4">
        <!-- Materias -->
        <div class="stat-card">
            <div class="stat-icon bg-gradient-purple">
                <i class="fas fa-book"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['total_materias'] }}</h3>
                <p>Materias Inscritas</p>
                <small class="stat-detail">
                    <i class="fas fa-check me-1"></i>Activas
                </small>
            </div>
            <div class="stat-footer">
                <a href="{{ route('estudiante.inscripciones.index') }}" class="btn-dracula-sm">
                    <i class="fas fa-eye me-1"></i>Ver todas
                </a>
            </div>
        </div>

        <!-- Créditos -->
        <div class="stat-card">
            <div class="stat-icon bg-gradient-pink">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['total_creditos'] }}</h3>
                <p>Créditos Totales</p>
                <small class="stat-detail">
                    <i class="fas fa-award me-1"></i>Este período
                </small>
            </div>
        </div>

        <!-- Promedio -->
        <div class="stat-card">
            <div class="stat-icon bg-gradient-cyan">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['promedio_general'] ?? 'N/A' }}</h3>
                <p>Promedio General</p>
                <small class="stat-detail">
                    <i class="fas fa-star me-1"></i>Acumulado
                </small>
            </div>
            <div class="stat-footer">
                <a href="{{ route('estudiante.calificaciones.index') }}" class="btn-dracula-sm">
                    <i class="fas fa-eye me-1"></i>Ver notas
                </a>
            </div>
        </div>

        <!-- Evaluaciones Pendientes -->
        <div class="stat-card">
            <div class="stat-icon bg-gradient-green">
                <i class="fas fa-tasks"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['evaluaciones_pendientes'] }}</h3>
                <p>Evaluaciones Pendientes</p>
                <small class="stat-detail">
                    <i class="fas fa-exclamation-circle me-1"></i>Por realizar
                </small>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- Materias Inscritas -->
        <div class="col-lg-8">
            <div class="card-dark">
                <div class="card-header-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-book me-2"></i>Mis Materias Actuales
                    </h5>
                </div>
                <div class="card-body-dark">
                    <div class="table-responsive">
                        <table class="table-dark">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Materia</th>
                                    <th>Profesor</th>
                                    <th>Créditos</th>
                                    <th>Nota</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($inscripcionesActuales as $inscripcion)
                                    <tr>
                                        <td><span class="badge-neon">{{ $inscripcion->seccion->curso->codigo_curso }}</span></td>
                                        <td>
                                            <strong>{{ $inscripcion->seccion->curso->nombre }}</strong>
                                            <br><small class="text-muted-dark">Sección {{ $inscripcion->seccion->codigo_seccion }}</small>
                                        </td>
                                        <td>
                                            <small>{{ $inscripcion->seccion->profesor->nombre_completo }}</small>
                                        </td>
                                        <td>{{ $inscripcion->seccion->curso->creditos }}</td>
                                        <td>
                                            @if($inscripcion->nota_final)
                                                <span class="badge-success">{{ number_format($inscripcion->nota_final, 2) }}</span>
                                            @else
                                                <span class="text-muted-dark">Pendiente</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('estudiante.inscripciones.show', $inscripcion->id) }}" 
                                                class="btn-action btn-action-info"
                                                title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="empty-state">
                                                <i class="fas fa-book fa-3x mb-3"></i>
                                                <h5>No tienes materias inscritas</h5>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Evaluaciones Próximas -->
            <div class="card-dark mt-3">
                <div class="card-header-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>Evaluaciones Próximas
                    </h5>
                </div>
                <div class="card-body-dark">
                    @forelse($evaluacionesProximas as $evaluacion)
                        <div class="popular-item">
                            <div>
                                <strong class="d-block text-light">{{ $evaluacion['nombre'] }}</strong>
                                <small class="text-muted-dark">
                                    {{ $evaluacion['curso'] }} - {{ $evaluacion['tipo'] }}
                                </small>
                            </div>
                            <div class="text-end">
                                <span class="badge-dracula">{{ $evaluacion['dias_restantes'] }} días</span>
                                <small class="d-block text-muted-dark">{{ $evaluacion['fecha_evaluacion']->format('d/m/Y') }}</small>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted-dark text-center mb-0">
                            <i class="fas fa-check-circle me-2"></i>No hay evaluaciones próximas
                        </p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar Derecho -->
        <div class="col-lg-4">
            <!-- Calificaciones Recientes -->
            <div class="card-dark mb-3">
                <div class="card-header-dark">
                    <h6 class="mb-0">
                        <i class="fas fa-star me-2"></i>Calificaciones Recientes
                    </h6>
                </div>
                <div class="card-body-dark">
                    @forelse($calificacionesRecientes as $calificacion)
                        <div class="mb-3 pb-3 border-bottom border-secondary">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="small text-light">{{ $calificacion['evaluacion'] }}</span>
                                <span class="badge-neon">{{ $calificacion['nota'] }}</span>
                            </div>
                            <small class="text-muted-dark d-block">{{ $calificacion['curso'] }}</small>
                            <small class="text-muted-dark">{{ $calificacion['fecha_calificacion']->format('d/m/Y') }}</small>
                        </div>
                    @empty
                        <p class="text-muted-dark text-center mb-0">
                            <i class="fas fa-inbox me-2"></i>Sin calificaciones recientes
                        </p>
                    @endforelse
                </div>
            </div>

            <!-- Horario de Hoy -->
            <div class="card-dark">
                <div class="card-header-dark">
                    <h6 class="mb-0">
                        <i class="fas fa-clock me-2"></i>Horario de Hoy
                    </h6>
                </div>
                <div class="card-body-dark">
                    @forelse($horarioHoy as $clase)
                        <div class="popular-item">
                            <div>
                                <strong class="d-block text-light">{{ $clase['curso'] }}</strong>
                                <small class="text-muted-dark">{{ $clase['profesor'] }}</small>
                            </div>
                            <div class="text-end">
                                <span class="badge-info">{{ $clase['aula'] }}</span>
                                <small class="d-block text-muted-dark">{{ $clase['hora'] }}</small>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted-dark text-center mb-0">
                            <i class="fas fa-coffee me-2"></i>Sin clases hoy
                        </p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    :root {
        --dracula-bg: #1a1c2e;
        --dracula-cyan: #00ffff;
        --dracula-cyan-light: #5dfdff;
        --dracula-green: #50fa7b;
        --dracula-purple: #bd93f9;
        --dracula-pink: #ff79c6;
        --neon-blue: #00d4ff;
        --neon-cyan: #00ffff;
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

    .header-subtitle { color: var(--dracula-cyan-light); margin: 0.5rem 0 0 0; }

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
        transition: all 0.3s ease;
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.1);
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 0 25px rgba(0, 212, 255, 0.3); }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: #0f172a;
    }

    .bg-gradient-purple { background: linear-gradient(135deg, #bd93f9 0%, #7c3aed 100%); box-shadow: 0 0 20px rgba(189, 147, 249, 0.4); }
    .bg-gradient-pink { background: linear-gradient(135deg, #ff79c6 0%, #ec4899 100%); box-shadow: 0 0 20px rgba(255, 121, 198, 0.4); }
    .bg-gradient-cyan { background: linear-gradient(135deg, #00ffff 0%, #00d4ff 100%); box-shadow: 0 0 20px rgba(0, 255, 255, 0.4); }
    .bg-gradient-green { background: linear-gradient(135deg, #50fa7b 0%, #00ff88 100%); box-shadow: 0 0 20px rgba(80, 250, 123, 0.4); }

    .stat-content h3 {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--neon-cyan);
        margin: 0;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .stat-content p { color: #f8f8f2; margin: 0.5rem 0; }
    .stat-detail { color: #6272a4; font-size: 0.875rem; }

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

    .badge-dracula-lg, .badge-dracula {
        background: linear-gradient(135deg, var(--neon-cyan) 0%, var(--electric-blue) 100%);
        color: #0f172a;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.4);
        display: inline-block;
    }

    .badge-dracula-lg { font-size: 1rem; padding: 0.75rem 1.5rem; }
    .badge-warning-lg {
        background: linear-gradient(135deg, #ffb86c 0%, #f1fa8c 100%);
        color: #1a1c2e;
        padding: 0.75rem 1.5rem;
        border-radius: 20px;
        font-weight: 600;
        display: inline-block;
    }

    .btn-dracula-sm {
        background: linear-gradient(135deg, var(--neon-cyan) 0%, var(--electric-blue) 100%);
        border: none;
        color: #0f172a;
        font-weight: 600;
        padding: 0.5rem 1rem;
        border-radius: 10px;
        font-size: 0.875rem;
        width: 100%;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
    }

    .btn-dracula-sm:hover { transform: translateY(-2px); box-shadow: 0 0 30px rgba(0, 212, 255, 0.6); color: #0f172a; }

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

    .badge-neon {
        background: linear-gradient(135deg, var(--neon-cyan) 0%, var(--electric-blue) 100%);
        color: #0f172a;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.75rem;
        box-shadow: 0 0 15px rgba(0, 255, 255, 0.4);
    }

    .badge-success {
        background: rgba(16, 185, 129, 0.25);
        color: #10b981;
        border: 2px solid #10b981;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .badge-info {
        background: rgba(6, 182, 212, 0.2);
        color: #06b6d4;
        border: 1px solid #06b6d4;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .text-muted-dark { color: #6272a4; }

    .btn-action {
        width: 35px;
        height: 35px;
        border-radius: 8px;
        border: 1px solid;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        cursor: pointer;
        background: transparent;
    }

    .btn-action-info { border-color: #06b6d4; color: #06b6d4; }
    .btn-action-info:hover { background: #06b6d4; color: white; box-shadow: 0 0 15px rgba(6, 182, 212, 0.5); transform: scale(1.1); }

    .empty-state { color: #94a3b8; padding: 3rem; }
    .empty-state i { color: rgba(0, 212, 255, 0.3); }
    .empty-state h5 { color: #e2e8f0; margin-top: 1rem; }

    .popular-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 0;
        border-bottom: 1px solid rgba(0, 212, 255, 0.1);
    }

    .popular-item:last-child { border-bottom: none; }
</style>
@endpush
@endsection
