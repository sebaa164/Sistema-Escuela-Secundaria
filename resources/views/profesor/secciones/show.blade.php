@extends('layouts.app')

@section('title', 'Detalle de Sección')
@section('page-title', 'Detalle de Sección')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header-dark mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-chalkboard me-3"></i>{{ $seccion->curso->nombre }}
                </h1>
                <p class="header-subtitle mb-0">
                    Sección {{ $seccion->codigo_seccion }} - {{ $seccion->periodo->nombre }}
                </p>
            </div>
            <a href="{{ route('profesor.secciones.index') }}" class="btn-outline-neon">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="stats-grid mb-4">
        <div class="stat-card">
            <div class="stat-icon bg-gradient-primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $estadisticas['total_estudiantes'] }}</h3>
                <p>Estudiantes Inscritos</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-info">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $estadisticas['total_evaluaciones'] }}</h3>
                <p>Evaluaciones</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $estadisticas['evaluaciones_activas'] }}</h3>
                <p>Activas</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-warning">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $estadisticas['promedio_general'] ?? 'N/A' }}</h3>
                <p>Promedio General</p>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- Información de la Sección -->
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
                        <span class="info-value">{{ $seccion->curso->nombre }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-code me-2"></i>Código:</span>
                        <span class="info-value">{{ $seccion->codigo_seccion }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-calendar me-2"></i>Período:</span>
                        <span class="info-value">{{ $seccion->periodo->nombre }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-door-open me-2"></i>Aula:</span>
                        <span class="info-value">{{ $seccion->aula ?? 'No asignada' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-users me-2"></i>Cupo:</span>
                        <span class="info-value">{{ $estadisticas['total_estudiantes'] }} / {{ $seccion->cupo_maximo }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-clock me-2"></i>Horario:</span>
                        <span class="info-value">{{ $seccion->horario ?? 'No definido' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label"><i class="fas fa-toggle-on me-2"></i>Estado:</span>
                        <span class="badge-success">{{ ucfirst($seccion->estado) }}</span>
                    </div>
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="card-dark mt-3">
                <div class="card-header-dark">
                    <h6 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>Acciones Rápidas
                    </h6>
                </div>
                <div class="card-body-dark">
                    <a href="{{ route('profesor.secciones.estudiantes', $seccion) }}" class="btn-action-full btn-primary mb-2">
                        <i class="fas fa-users me-2"></i>Ver Estudiantes
                    </a>
                    <a href="{{ route('profesor.evaluaciones.index', ['seccion_id' => $seccion->id]) }}" class="btn-action-full btn-info mb-2">
                        <i class="fas fa-file-alt me-2"></i>Ver Evaluaciones
                    </a>
                    <a href="{{ route('profesor.asistencias.index', ['seccion_id' => $seccion->id]) }}" class="btn-action-full btn-success">
                        <i class="fas fa-clipboard-check me-2"></i>Tomar Asistencia
                    </a>
                </div>
            </div>
        </div>

        <!-- Evaluaciones -->
        <div class="col-lg-8">
            <div class="card-dark">
                <div class="card-header-dark">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-file-alt me-2"></i>Evaluaciones
                        </h5>
                        <a href="{{ route('profesor.evaluaciones.create', ['seccion_id' => $seccion->id]) }}" class="btn-neon-sm">
                            <i class="fas fa-plus me-2"></i>Nueva Evaluación
                        </a>
                    </div>
                </div>
                <div class="card-body-dark">
                    <div class="table-responsive">
                        <table class="table-dark">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Tipo</th>
                                    <th>Fecha</th>
                                    <th>Peso</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($seccion->evaluaciones as $evaluacion)
                                    <tr>
                                        <td><strong>{{ $evaluacion->nombre }}</strong></td>
                                        <td><span class="badge-info">{{ $evaluacion->tipoEvaluacion->nombre ?? 'N/A' }}</span></td>
                                        <td>{{ $evaluacion->fecha_evaluacion->format('d/m/Y') }}</td>
                                        <td>{{ $evaluacion->porcentaje }}%</td>
                                        <td>
                                            @if($evaluacion->estado === 'activa')
                                                <span class="status-badge status-active">Activa</span>
                                            @else
                                                <span class="status-badge status-inactive">{{ ucfirst($evaluacion->estado) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="{{ route('profesor.evaluaciones.show', $evaluacion) }}" 
                                                   class="btn-action btn-action-info"
                                                   title="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('profesor.calificaciones.edit', $evaluacion) }}" 
                                                   class="btn-action btn-action-warning"
                                                   title="Calificar">
                                                    <i class="fas fa-star"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="fas fa-file-alt fa-2x mb-2"></i>
                                                <p>No hay evaluaciones creadas</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Estudiantes Recientes -->
            <div class="card-dark mt-3">
                <div class="card-header-dark">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-users me-2"></i>Estudiantes ({{ $estadisticas['total_estudiantes'] }})
                        </h5>
                        <a href="{{ route('profesor.secciones.estudiantes', $seccion) }}" class="btn-outline-neon-sm">
                            Ver todos
                        </a>
                    </div>
                </div>
                <div class="card-body-dark">
                    <div class="row g-3">
                        @forelse($seccion->inscripciones->where('estado', 'inscrito')->take(6) as $inscripcion)
                            <div class="col-md-6">
                                <div class="student-card">
                                    <div class="student-avatar">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="student-info">
                                        <strong>{{ $inscripcion->estudiante->nombre_completo }}</strong>
                                        <small class="d-block text-muted">{{ $inscripcion->estudiante->email }}</small>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-muted text-center mb-0">No hay estudiantes inscritos</p>
                            </div>
                        @endforelse
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
    .bg-gradient-info { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }
    .bg-gradient-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .bg-gradient-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }

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

    .btn-action-full {
        display: block;
        width: 100%;
        padding: 0.75rem;
        border-radius: 10px;
        text-align: center;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        border: 1px solid;
    }

    .btn-primary {
        background: rgba(14, 165, 233, 0.2);
        border-color: #0ea5e9;
        color: #0ea5e9;
    }

    .btn-primary:hover { background: #0ea5e9; color: white; }

    .btn-info {
        background: rgba(6, 182, 212, 0.2);
        border-color: #06b6d4;
        color: #06b6d4;
    }

    .btn-info:hover { background: #06b6d4; color: white; }

    .btn-success {
        background: rgba(16, 185, 129, 0.2);
        border-color: #10b981;
        color: #10b981;
    }

    .btn-success:hover { background: #10b981; color: white; }

    .btn-neon-sm {
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        border: none;
        color: #0f172a;
        font-weight: 600;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        text-decoration: none;
        font-size: 0.875rem;
        transition: all 0.3s ease;
    }

    .btn-neon-sm:hover { transform: translateY(-2px); box-shadow: 0 0 20px rgba(0, 212, 255, 0.5); color: #0f172a; }

    .btn-outline-neon, .btn-outline-neon-sm {
        background: transparent;
        border: 2px solid var(--neon-cyan);
        color: var(--neon-cyan);
        font-weight: 600;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        text-decoration: none;
        font-size: 0.875rem;
        transition: all 0.3s ease;
    }

    .btn-outline-neon { padding: 0.75rem 1.5rem; font-size: 1rem; }
    .btn-outline-neon:hover, .btn-outline-neon-sm:hover {
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

    .badge-info, .badge-success {
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-info { background: rgba(6, 182, 212, 0.2); color: #06b6d4; border: 1px solid #06b6d4; }
    .badge-success { background: rgba(16, 185, 129, 0.2); color: #10b981; border: 1px solid #10b981; }

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
    .status-inactive { background: rgba(100, 116, 139, 0.2); color: #64748b; border: 1px solid #64748b; }

    .action-buttons { display: flex; gap: 0.5rem; }

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
    .btn-action-info:hover { background: #06b6d4; color: white; box-shadow: 0 0 15px rgba(6, 182, 212, 0.5); }

    .btn-action-warning { border-color: #f59e0b; color: #f59e0b; }
    .btn-action-warning:hover { background: #f59e0b; color: white; box-shadow: 0 0 15px rgba(245, 158, 11, 0.5); }

    .student-card {
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
        padding: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.3s ease;
    }

    .student-card:hover { background: rgba(0, 212, 255, 0.05); border-color: rgba(0, 212, 255, 0.4); }

    .student-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0f172a;
        font-size: 1.25rem;
    }

    .student-info strong { color: #e2e8f0; display: block; }
    .student-info small { color: #94a3b8; }

    .empty-state { color: #94a3b8; padding: 2rem; text-align: center; }
    .empty-state i { color: rgba(0, 212, 255, 0.3); }
</style>
@endpush
@endsection
