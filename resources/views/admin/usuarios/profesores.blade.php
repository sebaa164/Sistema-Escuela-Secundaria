@extends('layouts.app')

@section('title', 'Gestión de Profesores')
@section('page-title', 'Profesores')

@section('content')
<div class="container-fluid">
    <!-- Header con Estadísticas -->
    <div class="stats-grid mb-4">
        <div class="stat-card">
            <div class="stat-icon bg-gradient-primary">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $profesores->total() }}</h3>
                <p>Total Profesores</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $profesores->where('estado', 'activo')->count() }}</h3>
                <p>Activos</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-warning">
                <i class="fas fa-pause-circle"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $profesores->where('estado', 'inactivo')->count() }}</h3>
                <p>Inactivos</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-info">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $totalSecciones ?? 0 }}</h3>
                <p>Secciones Asignadas</p>
            </div>
        </div>
    </div>

    <!-- Card Principal -->
    <div class="card-dark">
        <div class="card-header-dark">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h5 class="mb-0">
                    <i class="fas fa-chalkboard-teacher me-2"></i>Listado de Profesores
                </h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.usuarios.index') }}" class="btn-outline-neon">
                        <i class="fas fa-arrow-left me-2"></i>Volver
                    </a>
                    <a href="{{ route('admin.usuarios.create') }}?tipo=profesor" class="btn-neon">
                        <i class="fas fa-plus me-2"></i>Nuevo Profesor
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body-dark">
            <!-- Filtros -->
            <form method="GET" action="{{ route('admin.usuarios.profesores') }}" class="filter-form mb-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="input-group-dark">
                            <span class="input-icon">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" 
                                   name="search" 
                                   class="form-control-dark" 
                                   placeholder="Buscar por nombre, apellido o email..."
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <select name="estado" class="form-select-dark">
                            <option value="">Todos los estados</option>
                            <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activos</option>
                            <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <button type="submit" class="btn-neon w-100">
                            <i class="fas fa-filter me-2"></i>Filtrar
                        </button>
                    </div>

                    @if(request()->hasAny(['search', 'estado']))
                        <div class="col-md-1">
                            <a href="{{ route('admin.usuarios.profesores') }}" class="btn-outline-neon w-100">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </form>

            <!-- Tabla -->
            <div class="table-responsive">
                <table class="table-dark">
                    <thead>
                        <tr>
                            <th>Profesor</th>
                            <th>Email</th>
                            <th>Secciones</th>
                            <th>Estado</th>
                            <th>Fecha de Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($profesores as $profesor)
                            <tr>
                                <td>
                                    <div class="user-info">
                                        <div class="avatar-circle">
                                            <i class="fas fa-chalkboard-teacher"></i>
                                        </div>
                                        <div>
                                            <strong>{{ $profesor->nombre_completo }}</strong>
                                            <br><small class="text-muted">ID: {{ $profesor->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-light">{{ $profesor->email }}</span>
                                </td>
                                <td>
                                    <div class="section-count">
                                        <i class="fas fa-layer-group me-1"></i>
                                        <strong>{{ $profesor->secciones_count ?? 0 }}</strong>
                                        <small class="text-muted ms-1">asignadas</small>
                                    </div>
                                </td>
                                <td>
                                    @if($profesor->estado === 'activo')
                                        <span class="status-badge status-active">
                                            <i class="fas fa-circle"></i> Activo
                                        </span>
                                    @else
                                        <span class="status-badge status-inactive">
                                            <i class="fas fa-circle"></i> Inactivo
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ $profesor->created_at->format('d/m/Y') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.usuarios.show', $profesor) }}" 
                                           class="btn-action btn-action-info"
                                           title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.usuarios.edit', $profesor) }}" 
                                           class="btn-action btn-action-warning"
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($profesor->estado === 'activo')
                                            <button type="button" 
                                                    class="btn-action btn-action-success"
                                                    onclick="cambiarEstado({{ $profesor->id }}, '{{ $profesor->estado }}')"
                                                    title="Profesor Activo - Click para desactivar">
                                                <i class="fas fa-user-check"></i>
                                            </button>
                                        @else
                                            <button type="button" 
                                                    class="btn-action btn-action-danger"
                                                    onclick="cambiarEstado({{ $profesor->id }}, '{{ $profesor->estado }}')"
                                                    title="Profesor Inactivo - Click para activar">
                                                <i class="fas fa-user-slash"></i>
                                            </button>
                                        @endif
                                    </div>
                                    
                                    <form id="estado-form-{{ $profesor->id }}" 
                                          action="{{ route('admin.usuarios.cambiar-estado', $profesor) }}" 
                                          method="POST" 
                                          style="display: none;">
                                        @csrf
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-chalkboard-teacher fa-3x mb-3"></i>
                                        <h5>No se encontraron profesores</h5>
                                        <p>Intenta ajustar los filtros o crea un nuevo profesor</p>
                                        <a href="{{ route('admin.usuarios.create') }}?tipo=profesor" class="btn-neon mt-3">
                                            <i class="fas fa-plus me-2"></i>Crear Profesor
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
                {{ $profesores->links() }}
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Breadcrumb Dark */
    .breadcrumb-dark {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(30, 41, 59, 0.9) 100%);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 10px;
        padding: 1rem 1.5rem;
        margin: 0;
    }

    .breadcrumb-dark .breadcrumb-item { color: #94a3b8; }
    .breadcrumb-dark .breadcrumb-item a {
        color: var(--neon-cyan);
        text-decoration: none;
        transition: all 0.3s ease;
    }
    .breadcrumb-dark .breadcrumb-item a:hover {
        color: var(--neon-blue);
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }
    .breadcrumb-dark .breadcrumb-item.active { color: #e2e8f0; }
    .breadcrumb-dark .breadcrumb-item + .breadcrumb-item::before {
        content: "›";
        color: rgba(0, 212, 255, 0.5);
    }

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

    .bg-gradient-primary { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); }
    .bg-gradient-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .bg-gradient-info { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }
    .bg-gradient-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }

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

    .card-body-dark { padding: 2rem; }

    /* Form Controls */
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
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
        color: var(--neon-cyan);
    }

    /* Table */
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
    }

    .table-dark tbody td {
        padding: 1rem;
        vertical-align: middle;
    }

    /* User Info */
    .user-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .avatar-circle {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0f172a;
        font-size: 1.25rem;
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.5);
    }

    .section-count {
        display: inline-flex;
        align-items: center;
        color: var(--neon-cyan);
        font-size: 1rem;
    }

    .section-count i {
        color: #06b6d4;
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

    .status-badge i {
        font-size: 0.5rem;
        animation: pulse 2s infinite;
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
        transform: scale(1.05);
    }

    .btn-action-warning {
        border-color: #f59e0b;
        color: #f59e0b;
    }

    .btn-action-warning:hover {
        background: #f59e0b;
        color: white;
        box-shadow: 0 0 15px rgba(245, 158, 11, 0.5);
        transform: scale(1.05);
    }

    .btn-action-danger {
        border-color: #ef4444;
        color: #ef4444;
    }

    .btn-action-danger:hover {
        background: #ef4444;
        color: white;
        box-shadow: 0 0 15px rgba(239, 68, 68, 0.5);
        transform: scale(1.05);
    }

    .btn-action-success {
        border-color: #10b981;
        color: #10b981;
    }

    .btn-action-success:hover {
        background: #10b981;
        color: white;
        box-shadow: 0 0 15px rgba(16, 185, 129, 0.5);
        transform: scale(1.05);
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

    .text-muted {
        color: #94a3b8 !important;
    }

    .text-light {
        color: #e2e8f0 !important;
    }

    /* Animations */
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .card-body-dark {
            padding: 1rem;
        }
        
        .table-dark thead th,
        .table-dark tbody td {
            padding: 0.5rem;
            font-size: 0.875rem;
        }
        
        .action-buttons {
            flex-wrap: wrap;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function cambiarEstado(id, estadoActual) {
        const nuevoEstado = estadoActual === 'activo' ? 'inactivo' : 'activo';
        const accion = nuevoEstado === 'activo' ? 'activar' : 'desactivar';
        const mensaje = `¿Estás seguro de que deseas ${accion} este profesor?`;
        
        if (confirm(mensaje)) {
            document.getElementById('estado-form-' + id).submit();
        }
    }
</script>
@endpush
@endsection