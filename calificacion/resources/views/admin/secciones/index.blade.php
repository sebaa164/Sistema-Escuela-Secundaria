@extends('layouts.app')

@section('title', 'Gestión de Secciones')
@section('page-title', 'Secciones')

@section('content')
<div class="container-fluid">
    <!-- Header con Estadísticas -->
    <div class="stats-grid mb-4">
        <div class="stat-card">
            <div class="stat-icon bg-gradient-primary">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $secciones->total() }}</h3>
                <p>Total Secciones</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $secciones->where('estado', 'activo')->count() }}</h3>
                <p>Secciones Activas</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-info">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $secciones->sum('estudiantes_inscritos') }}</h3>
                <p>Estudiantes Inscritos</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-warning">
                <i class="fas fa-chair"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $secciones->sum('cupos_disponibles') }}</h3>
                <p>Cupos Disponibles</p>
            </div>
        </div>
    </div>

    <!-- Card Principal -->
    <div class="card-dark">
        <div class="card-header-dark">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Listado de Secciones
                </h5>
                <a href="{{ route('admin.secciones.create') }}" class="btn-neon">
                    <i class="fas fa-plus me-2"></i>Nueva Sección
                </a>
            </div>
        </div>

        <div class="card-body-dark">
            <!-- Alertas -->
            @if(session('success'))
                <div class="alert-success-dark mb-4">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert-danger-dark mb-4">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                </div>
            @endif

            <!-- Filtros -->
            <form method="GET" action="{{ route('admin.secciones.index') }}" class="filter-form mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="input-group-dark">
                            <span class="input-icon">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" 
                                   name="search" 
                                   class="form-control-dark" 
                                   placeholder="Buscar sección..."
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <select name="curso_id" class="form-select-dark">
                            <option value="">Todos los cursos</option>
                            @foreach($cursos as $curso)
                                <option value="{{ $curso->id }}" {{ request('curso_id') == $curso->id ? 'selected' : '' }}>
                                    {{ $curso->codigo_curso }} - {{ $curso->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <select name="profesor_id" class="form-select-dark">
                            <option value="">Todos los profesores</option>
                            @foreach($profesores as $profesor)
                                <option value="{{ $profesor->id }}" {{ request('profesor_id') == $profesor->id ? 'selected' : '' }}>
                                    {{ $profesor->nombre }} {{ $profesor->apellido }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select name="estado" class="form-select-dark">
                            <option value="">Todos los estados</option>
                            <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activos</option>
                            <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                            <option value="finalizado" {{ request('estado') == 'finalizado' ? 'selected' : '' }}>Finalizados</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select name="modalidad" class="form-select-dark">
                            <option value="">Todas las modalidades</option>
                            <option value="presencial" {{ request('modalidad') == 'presencial' ? 'selected' : '' }}>Presencial</option>
                            <option value="virtual" {{ request('modalidad') == 'virtual' ? 'selected' : '' }}>Virtual</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12 d-flex gap-2 justify-content-end">
                        <button type="submit" class="btn-neon">
                            <i class="fas fa-filter me-2"></i>Filtrar
                        </button>
                        <a href="{{ route('admin.secciones.index') }}" class="btn-outline-neon">
                            <i class="fas fa-redo me-2"></i>Limpiar
                        </a>
                    </div>
                </div>
            </form>

            <!-- Tabla -->
            <div class="table-responsive">
                <table class="table-dark">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Curso</th>
                            <th>Profesor</th>
                            <th>Período</th>
                            <th>Cupos</th>
                            <th>Modalidad</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($secciones as $seccion)
                            <tr>
                                <td>
                                    <span class="badge-neon">{{ $seccion->codigo_seccion }}</span>
                                </td>
                                <td>
                                    <div class="course-info">
                                        <div class="course-icon">
                                            <i class="fas fa-book"></i>
                                        </div>
                                        <div>
                                            <strong>{{ $seccion->curso->nombre ?? 'N/A' }}</strong>
                                            <br><small class="text-muted">{{ $seccion->curso->codigo_curso ?? '' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="profesor-info">
                                        <i class="fas fa-user-tie me-2"></i>
                                        {{ $seccion->profesor->nombre ?? 'Sin' }} {{ $seccion->profesor->apellido ?? 'asignar' }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-info">
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ $seccion->periodo->nombre ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $inscritos = $seccion->estudiantes_inscritos;
                                        $porcentaje = ($inscritos / $seccion->cupo_maximo) * 100;
                                    @endphp
                                    <div class="cupo-container">
                                        <div class="cupo-text">
                                            <strong>{{ $inscritos }}</strong> / {{ $seccion->cupo_maximo }}
                                        </div>
                                        <div class="cupo-progress">
                                            <div class="cupo-bar {{ $porcentaje >= 100 ? 'full' : ($porcentaje >= 80 ? 'warning' : '') }}" 
                                                 style="width: {{ min($porcentaje, 100) }}%"></div>
                                        </div>
                                        <small class="cupo-percent">{{ number_format($porcentaje, 0) }}%</small>
                                    </div>
                                </td>
                                <td>
                                    @if($seccion->modalidad == 'presencial')
                                        <span class="badge-modalidad presencial">
                                            <i class="fas fa-school"></i> Presencial
                                        </span>
                                    @elseif($seccion->modalidad == 'virtual')
                                        <span class="badge-modalidad virtual">
                                            <i class="fas fa-laptop-house"></i> Virtual
                                        </span>
                                    @else
                                        <span class="badge-modalidad">
                                            <i class="fas fa-question"></i> {{ ucfirst($seccion->modalidad) }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($seccion->estado === 'activo')
                                        <span class="status-badge status-active">
                                            <i class="fas fa-circle"></i> Activo
                                        </span>
                                    @elseif($seccion->estado === 'inactivo')
                                        <span class="status-badge status-inactive">
                                            <i class="fas fa-circle"></i> Inactivo
                                        </span>
                                    @else
                                        <span class="status-badge status-finished">
                                            <i class="fas fa-circle"></i> Finalizado
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.secciones.show', $seccion) }}" 
                                           class="btn-action btn-action-info"
                                           title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.horarios.index', $seccion) }}" 
                                           class="btn-action btn-action-primary"
                                           title="Gestionar horarios">
                                            <i class="fas fa-clock"></i>
                                        </a>
                                        <a href="{{ route('admin.secciones.edit', $seccion) }}" 
                                           class="btn-action btn-action-warning"
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn-action btn-action-danger"
                                                onclick="confirmarEliminacion({{ $seccion->id }})"
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <form id="delete-form-{{ $seccion->id }}" 
                                          action="{{ route('admin.secciones.destroy', $seccion) }}" 
                                          method="POST" 
                                          style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-chalkboard-teacher fa-3x mb-3"></i>
                                        <h5>No se encontraron secciones</h5>
                                        <p>Intenta ajustar los filtros o crea una nueva sección</p>
                                        <a href="{{ route('admin.secciones.create') }}" class="btn-neon mt-3">
                                            <i class="fas fa-plus me-2"></i>Crear Sección
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
                {{ $secciones->links() }}
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Stats Grid */
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
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.1);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0 30px rgba(0, 212, 255, 0.3);
        border-color: var(--neon-cyan);
    }

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

    .bg-gradient-primary {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .bg-gradient-info {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
    }

    .bg-gradient-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .stat-content h3 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--neon-cyan);
        margin: 0;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .stat-content p {
        color: #94a3b8;
        margin: 0;
        font-size: 0.875rem;
    }

    /* Card Dark */
    .card-dark {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.95) 100%);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 0 30px rgba(0, 212, 255, 0.2);
    }

    .card-header-dark {
        background: linear-gradient(135deg, rgba(14, 165, 233, 0.2) 0%, rgba(0, 212, 255, 0.1) 100%);
        border-bottom: 1px solid rgba(0, 212, 255, 0.3);
        padding: 1.5rem;
    }

    .card-header-dark h5 {
        color: var(--neon-cyan);
        font-weight: 600;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .card-body-dark {
        padding: 2rem;
    }

    /* Alerts */
    .alert-success-dark, .alert-danger-dark {
        padding: 1rem 1.5rem;
        border-radius: 10px;
        font-weight: 500;
    }

    .alert-success-dark {
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid #10b981;
        color: #6ee7b7;
    }

    .alert-danger-dark {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid #ef4444;
        color: #fca5a5;
    }

    /* Form Controls Dark */
    .input-group-dark {
        position: relative;
    }

    .input-icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--neon-cyan);
        z-index: 10;
    }

    .form-control-dark {
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 10px;
        color: #e2e8f0;
        padding: 0.75rem 1rem 0.75rem 2.5rem;
        width: 100%;
        transition: all 0.3s ease;
    }

    .form-control-dark:focus {
        background: rgba(15, 23, 42, 0.95);
        border-color: var(--neon-cyan);
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
        outline: none;
        color: white;
    }

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

    /* Buttons */
    .btn-neon {
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        border: none;
        color: #0f172a;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
        text-decoration: none;
        display: inline-block;
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
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .btn-outline-neon:hover {
        background: rgba(0, 212, 255, 0.1);
        transform: translateY(-2px);
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
        color: var(--neon-cyan);
    }

    /* Table Dark */
    .table-dark {
        width: 100%;
        color: #e2e8f0;
    }

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

    .table-dark tbody tr {
        border-bottom: 1px solid rgba(0, 212, 255, 0.1);
        transition: all 0.3s ease;
    }

    .table-dark tbody tr:hover {
        background: rgba(0, 212, 255, 0.05);
        transform: scale(1.01);
    }

    .table-dark tbody td {
        padding: 1rem;
        vertical-align: middle;
    }

    /* Course Info */
    .course-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .course-icon {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: #0f172a;
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.5);
    }

    .profesor-info {
        color: #94a3b8;
    }

    .profesor-info i {
        color: var(--neon-cyan);
    }

    /* Badges */
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

    /* Cupos */
    .cupo-container {
        min-width: 120px;
    }

    .cupo-text {
        color: #e2e8f0;
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }

    .cupo-progress {
        height: 6px;
        background: rgba(14, 165, 233, 0.2);
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 0.25rem;
    }

    .cupo-bar {
        height: 100%;
        background: linear-gradient(135deg, var(--neon-cyan) 0%, var(--neon-blue) 100%);
        transition: width 0.3s ease;
    }

    .cupo-bar.warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .cupo-bar.full {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }

    .cupo-percent {
        color: #94a3b8;
        font-size: 0.75rem;
    }

    /* Modalidad Badges */
    .badge-modalidad {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        border: 1px solid;
    }

    .badge-modalidad.presencial {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border-color: #10b981;
    }

    .badge-modalidad.virtual {
        background: rgba(139, 92, 246, 0.2);
        color: #8b5cf6;
        border-color: #8b5cf6;
    }

    /* Status Badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-active {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 1px solid #10b981;
    }

    .status-inactive {
        background: rgba(100, 116, 139, 0.2);
        color: #64748b;
        border: 1px solid #64748b;
    }

    .status-finished {
        background: rgba(99, 102, 241, 0.2);
        color: #6366f1;
        border: 1px solid #6366f1;
    }

    .status-badge i {
        font-size: 0.5rem;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }

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

    .btn-action-info {
        border-color: #06b6d4;
        color: #06b6d4;
    }

    .btn-action-info:hover {
        background: #06b6d4;
        color: white;
        box-shadow: 0 0 15px rgba(6, 182, 212, 0.5);
        transform: translateY(-2px);
    }

    .btn-action-primary {
        border-color: var(--neon-cyan);
        color: var(--neon-cyan);
    }

    .btn-action-primary:hover {
        background: var(--neon-cyan);
        color: #0f172a;
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.5);
        transform: translateY(-2px);
    }

    .btn-action-warning {
        border-color: #84cc16;
        color: #54e23bff;
    }

    .btn-action-warning:hover {
        background: #84cc16;
        color: white;
        box-shadow: 0 0 15px rgba(132, 204, 22, 0.5);
        transform: translateY(-2px);
    }

    .btn-action-danger {
        border-color: #ef4444;
        color: #ef4444;
    }

    .btn-action-danger:hover {
        background: #ef4444;
        color: white;
        box-shadow: 0 0 15px rgba(239, 68, 68, 0.5);
        transform: translateY(-2px);
    }

    /* Empty State */
    .empty-state {
        color: #94a3b8;
        padding: 3rem;
    }

    .empty-state i {
        color: rgba(0, 212, 255, 0.3);
    }

    .empty-state h5 {
        color: #e2e8f0;
        margin-bottom: 0.5rem;
    }
</style>
@endpush

@push('scripts')
<script>
    function confirmarEliminacion(id) {
        if (confirm('¿Estás seguro de que deseas eliminar esta sección? Esta acción no se puede deshacer.')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
@endpush
@endsection