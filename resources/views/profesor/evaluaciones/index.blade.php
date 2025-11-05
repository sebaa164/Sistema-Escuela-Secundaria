@extends('layouts.app')

@section('title', 'Evaluaciones - Profesor')
@section('page-title', 'Evaluaciones')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header-dark mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-file-alt me-3"></i>Mis Evaluaciones
                </h1>
                <p class="header-subtitle mb-0">Gestiona las evaluaciones de tus secciones</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('profesor.evaluaciones.create') }}" class="btn-neon">
                    <i class="fas fa-plus me-2"></i>Nueva Evaluación
                </a>
                <a href="{{ route('profesor.dashboard') }}" class="btn-outline-neon">
                    <i class="fas fa-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="stats-grid mb-4">
        <div class="stat-card">
            <div class="stat-icon bg-gradient-primary">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $evaluaciones->total() }}</h3>
                <p>Total Evaluaciones</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $evaluaciones->where('estado', 'activa')->count() }}</h3>
                <p>Activas</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $evaluaciones->where('estado', 'programada')->count() }}</h3>
                <p>Programadas</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-info">
                <i class="fas fa-check-double"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $evaluaciones->where('estado', 'finalizada')->count() }}</h3>
                <p>Finalizadas</p>
            </div>
        </div>
    </div>

    <!-- Filtros y Tabla -->
    <div class="card-dark">
        <div class="card-header-dark">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Listado de Evaluaciones
            </h5>
        </div>
        <div class="card-body-dark">
            <!-- Filtros -->
            <form method="GET" class="filter-form mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <select name="seccion_id" class="form-select-dark">
                            <option value="">Todas las secciones</option>
                            @foreach($secciones as $seccion)
                                <option value="{{ $seccion->id }}" {{ request('seccion_id') == $seccion->id ? 'selected' : '' }}>
                                    {{ $seccion->curso->nombre }} - {{ $seccion->codigo_seccion }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="estado" class="form-select-dark">
                            <option value="">Todos los estados</option>
                            <option value="programada" {{ request('estado') == 'programada' ? 'selected' : '' }}>Programada</option>
                            <option value="activa" {{ request('estado') == 'activa' ? 'selected' : '' }}>Activa</option>
                            <option value="finalizada" {{ request('estado') == 'finalizada' ? 'selected' : '' }}>Finalizada</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="tipo_evaluacion_id" class="form-select-dark">
                            <option value="">Todos los tipos</option>
                            @foreach($tiposEvaluacion as $tipo)
                                <option value="{{ $tipo->id }}" {{ request('tipo_evaluacion_id') == $tipo->id ? 'selected' : '' }}>
                                    {{ $tipo->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn-filter w-100">
                            <i class="fas fa-filter me-2"></i>Filtrar
                        </button>
                    </div>
                </div>
            </form>

            <!-- Tabla -->
            <div class="table-responsive">
                <table class="table-dark">
                    <thead>
                        <tr>
                            <th>Evaluación</th>
                            <th>Sección</th>
                            <th>Tipo</th>
                            <th>Fecha</th>
                            <th>Peso</th>
                            <th>Estado</th>
                            <th>Calificadas</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($evaluaciones as $evaluacion)
                            <tr>
                                <td>
                                    <strong class="text-light">{{ $evaluacion->nombre }}</strong>
                                    <br><small class="text-muted">{{ Str::limit($evaluacion->descripcion, 40) }}</small>
                                </td>
                                <td>
                                    <span class="badge-neon">{{ $evaluacion->seccion->codigo_seccion }}</span>
                                    <br><small class="text-muted">{{ $evaluacion->seccion->curso->nombre }}</small>
                                </td>
                                <td><span class="badge-info">{{ $evaluacion->tipoEvaluacion->nombre ?? 'N/A' }}</span></td>
                                <td>
                                    <i class="fas fa-calendar me-1"></i>{{ $evaluacion->fecha_evaluacion->format('d/m/Y') }}
                                    <br><small class="text-muted">{{ $evaluacion->fecha_evaluacion->format('H:i') }}</small>
                                </td>
                                <td><span class="weight-badge">{{ $evaluacion->porcentaje }}%</span></td>
                                <td>
                                    @if($evaluacion->estado === 'activa')
                                        <span class="status-badge status-active">
                                            <i class="fas fa-circle"></i> Activa
                                        </span>
                                    @elseif($evaluacion->estado === 'programada')
                                        <span class="status-badge status-programada">
                                            <i class="fas fa-circle"></i> Programada
                                        </span>
                                    @else
                                        <span class="status-badge status-finalizada">
                                            <i class="fas fa-circle"></i> Finalizada
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="progress-container">
                                        <div class="progress-bar-custom">
                                            <div class="progress-fill" style="width: {{ $evaluacion->porcentaje_calificado }}%"></div>
                                        </div>
                                        <small class="text-muted">{{ $evaluacion->calificaciones_count }}/{{ $evaluacion->seccion->inscripciones_count }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('profesor.evaluaciones.show', $evaluacion) }}" 
                                           class="btn-action btn-action-info"
                                           title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('profesor.evaluaciones.edit', $evaluacion) }}" 
                                           class="btn-action btn-action-warning"
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('profesor.calificaciones.edit', $evaluacion) }}" 
                                           class="btn-action btn-action-success"
                                           title="Calificar">
                                            <i class="fas fa-star"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-file-alt fa-3x mb-3"></i>
                                        <h5>No hay evaluaciones</h5>
                                        <p>Crea tu primera evaluación para comenzar</p>
                                        <a href="{{ route('profesor.evaluaciones.create') }}" class="btn-neon-sm mt-3">
                                            <i class="fas fa-plus me-2"></i>Nueva Evaluación
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="mt-4">
                {{ $evaluaciones->links() }}
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

    .btn-neon {
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        border: none;
        color: #0f172a;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
    }

    .btn-neon:hover { transform: translateY(-2px); box-shadow: 0 0 30px rgba(0, 212, 255, 0.6); color: #0f172a; }

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

    .form-select-dark {
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 10px;
        color: #e2e8f0;
        padding: 0.75rem 1rem;
        width: 100%;
        transition: all 0.3s ease;
    }

    .form-select-dark:focus {
        background: rgba(15, 23, 42, 0.95);
        border-color: var(--neon-cyan);
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
        outline: none;
    }

    .btn-filter {
        background: rgba(0, 212, 255, 0.2);
        border: 1px solid rgba(0, 212, 255, 0.3);
        color: var(--neon-cyan);
        font-weight: 600;
        padding: 0.75rem 1rem;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .btn-filter:hover { background: rgba(0, 212, 255, 0.3); border-color: var(--neon-cyan); }

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
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        color: #0f172a;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.75rem;
        box-shadow: 0 0 10px rgba(0, 212, 255, 0.3);
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

    .weight-badge {
        background: rgba(245, 158, 11, 0.2);
        color: #f59e0b;
        border: 1px solid #f59e0b;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
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
    .status-programada { background: rgba(245, 158, 11, 0.2); color: #f59e0b; border: 1px solid #f59e0b; }
    .status-finalizada { background: rgba(100, 116, 139, 0.2); color: #64748b; border: 1px solid #64748b; }
    .status-badge i { font-size: 0.5rem; animation: pulse 2s infinite; }

    @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }

    .progress-container { min-width: 120px; }

    .progress-bar-custom {
        width: 100%;
        height: 8px;
        background: rgba(15, 23, 42, 0.8);
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 0.25rem;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        border-radius: 10px;
        transition: width 0.3s ease;
        box-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

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

    .btn-action-success { border-color: #10b981; color: #10b981; }
    .btn-action-success:hover { background: #10b981; color: white; box-shadow: 0 0 15px rgba(16, 185, 129, 0.5); }

    .empty-state { color: #94a3b8; padding: 3rem; text-align: center; }
    .empty-state i { color: rgba(0, 212, 255, 0.3); }
    .empty-state h5 { color: #e2e8f0; margin: 1rem 0; }

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
        display: inline-block;
    }

    .btn-neon-sm:hover { transform: translateY(-2px); box-shadow: 0 0 20px rgba(0, 212, 255, 0.5); color: #0f172a; }
</style>
@endpush
@endsection
