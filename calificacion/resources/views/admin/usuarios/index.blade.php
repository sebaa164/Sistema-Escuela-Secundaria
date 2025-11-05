@extends('layouts.app')

@section('title', 'Gestión de Usuarios')
@section('page-title', 'Usuarios')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header-dark mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-users-cog me-3"></i>Gestión de Usuarios
                </h1>
                <p class="header-subtitle mb-0">Administra estudiantes, profesores y administradores desde un solo lugar</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.dashboard') }}" class="btn-outline-neon">
                    <i class="fas fa-arrow-left me-2"></i>Volver al Panel
                </a>
                <a href="{{ route('admin.usuarios.create') }}" class="btn-neon">
                    <i class="fas fa-plus me-2"></i>Nuevo Usuario
                </a>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="stats-grid mb-4">
        <div class="stat-card">
            <div class="stat-icon bg-gradient-primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $usuarios->total() }}</h3>
                <p>Total Usuarios</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-success">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $usuarios->where('tipo_usuario', 'estudiante')->count() }}</h3>
                <p>Estudiantes</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-info">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $usuarios->where('tipo_usuario', 'profesor')->count() }}</h3>
                <p>Profesores</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-warning">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $usuarios->where('tipo_usuario', 'administrador')->count() }}</h3>
                <p>Administradores</p>
            </div>
        </div>
    </div>

    <!-- Card Principal -->
    <div class="card-dark">
        <div class="card-header-dark">
            <h5 class="mb-0 d-flex align-items-center gap-2">
                <i class="fas fa-users"></i>
                Listado de Usuarios
            </h5>
        </div>

        <div class="card-body-dark">
            <!-- Filtros -->
            <form method="GET" action="{{ route('admin.usuarios.index') }}" class="filter-form mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="input-group-dark">
                            <span class="input-icon">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" 
                                   name="search" 
                                   class="form-control-dark with-icon" 
                                   placeholder="Buscar por nombre, email..."
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <select name="tipo" class="form-select-dark">
                            <option value="">Todos los tipos</option>
                            <option value="estudiante" {{ request('tipo') == 'estudiante' ? 'selected' : '' }}>Estudiantes</option>
                            <option value="profesor" {{ request('tipo') == 'profesor' ? 'selected' : '' }}>Profesores</option>
                            <option value="administrador" {{ request('tipo') == 'administrador' ? 'selected' : '' }}>Administradores</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <select name="estado" class="form-select-dark">
                            <option value="">Todos los estados</option>
                            <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activos</option>
                            <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                            <option value="suspendido" {{ request('estado') == 'suspendido' ? 'selected' : '' }}>Suspendidos</option>
                            <option value="regular" {{ request('estado') == 'regular' ? 'selected' : '' }}>Estudiantes Regulares</option>
                            <option value="libre" {{ request('estado') == 'libre' ? 'selected' : '' }}>Estudiantes Libres</option>
                            <option value="preinscripto" {{ request('estado') == 'preinscripto' ? 'selected' : '' }}>Preinscriptos</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <button type="submit" class="btn-neon w-100">
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
                            <th style="width: 80px;">ID</th>
                            <th style="min-width: 200px;">Usuario</th>
                            <th style="min-width: 200px;">Email</th>
                            <th style="width: 150px;">Tipo</th>
                            <th style="width: 130px;">Estado</th>
                            <th style="width: 120px;">Fecha Registro</th>
                            <th style="width: 180px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($usuarios as $usuario)
                            <tr>
                                <td><span class="badge-neon">{{ $usuario->id }}</span></td>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            {{ substr($usuario->nombre, 0, 1) }}{{ substr($usuario->apellido, 0, 1) }}
                                        </div>
                                        <div class="user-details">
                                            <strong class="user-name">{{ $usuario->nombre }} {{ $usuario->apellido }}</strong>
                                            @if($usuario->telefono)
                                                <small class="text-muted-dark d-block">{{ $usuario->telefono }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td><span class="text-wrap">{{ $usuario->email }}</span></td>
                                <td>
                                    @if($usuario->tipo_usuario === 'estudiante')
                                        <span class="badge-success">
                                            <i class="fas fa-user-graduate me-1"></i>Estudiante
                                        </span>
                                    @elseif($usuario->tipo_usuario === 'profesor')
                                        <span class="badge-info">
                                            <i class="fas fa-chalkboard-teacher me-1"></i>Profesor
                                        </span>
                                    @else
                                        <span class="badge-warning">
                                            <i class="fas fa-user-shield me-1"></i>Admin
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($usuario->tipo_usuario === 'estudiante')
                                        @if($usuario->estado_estudiante === 'regular')
                                            <span class="status-badge status-active">
                                                <i class="fas fa-circle"></i> Regular
                                            </span>
                                        @elseif($usuario->estado_estudiante === 'suspendido')
                                            <span class="status-badge status-suspended">
                                                <i class="fas fa-circle"></i> Suspendido
                                            </span>
                                        @elseif($usuario->estado_estudiante === 'libre')
                                            <span class="status-badge status-inactive">
                                                <i class="fas fa-circle"></i> Libre
                                            </span>
                                        @elseif($usuario->estado_estudiante === 'preinscripto')
                                            <span class="status-badge status-pending">
                                                <i class="fas fa-circle"></i> Preinscripto
                                            </span>
                                        @else
                                            <span class="status-badge status-inactive">
                                                <i class="fas fa-circle"></i> Sin estado
                                            </span>
                                        @endif
                                    @else
                                        @if($usuario->estado === 'activo')
                                            <span class="status-badge status-active">
                                                <i class="fas fa-circle"></i> Activo
                                            </span>
                                        @elseif($usuario->estado === 'inactivo')
                                            <span class="status-badge status-inactive">
                                                <i class="fas fa-circle"></i> Inactivo
                                            </span>
                                        @else
                                            <span class="status-badge status-suspended">
                                                <i class="fas fa-circle"></i> Suspendido
                                            </span>
                                        @endif
                                    @endif
                                </td>
                                <td>{{ $usuario->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.usuarios.show', $usuario) }}" 
                                           class="btn-action btn-action-info"
                                           title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.usuarios.edit', $usuario) }}" 
                                           class="btn-action btn-action-warning"
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn-action btn-action-danger"
                                                onclick="confirmarEliminacion({{ $usuario->id }})"
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <form id="delete-form-{{ $usuario->id }}" 
                                          action="{{ route('admin.usuarios.destroy', $usuario) }}" 
                                          method="POST" 
                                          style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-users fa-3x mb-3"></i>
                                        <h5>No se encontraron usuarios</h5>
                                        <p>Intenta ajustar los filtros o crea un nuevo usuario</p>
                                        <a href="{{ route('admin.usuarios.create') }}" class="btn-neon mt-3">
                                            <i class="fas fa-plus me-2"></i>Crear Usuario
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($usuarios->hasPages())
                <div class="pagination-wrapper mt-4">
                    {{ $usuarios->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Variables CSS Neon */
    :root {
        --neon-cyan: #00ffff;
        --neon-blue: #00d4ff;
        --electric-blue: #0ea5e9;
        --deep-blue: #0284c7;
    }

    /* Page Header */
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

    .header-subtitle {
        color: #94a3b8;
        font-size: 0.95rem;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    .stat-card {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.95) 100%);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 15px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.15);
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
        color: #0f172a;
        flex-shrink: 0;
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        box-shadow: 0 0 30px rgba(14, 165, 233, 0.6);
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        box-shadow: 0 0 30px rgba(16, 185, 129, 0.6);
    }

    .bg-gradient-info {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
        box-shadow: 0 0 30px rgba(6, 182, 212, 0.6);
    }

    .bg-gradient-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        box-shadow: 0 0 30px rgba(245, 158, 11, 0.6);
    }

    .stat-content {
        flex: 1;
        min-width: 0;
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
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Card Dark */
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

    .card-header-dark h5 {
        color: var(--neon-cyan);
        font-weight: 600;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
        margin: 0;
    }

    .card-body-dark {
        padding: 2rem;
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
        pointer-events: none;
    }

    .form-control-dark {
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 10px;
        color: #e2e8f0;
        padding: 0.75rem 1rem;
        width: 100%;
        transition: all 0.3s ease;
    }

    .form-control-dark.with-icon {
        padding-left: 2.75rem;
    }

    .form-control-dark:focus {
        background: rgba(15, 23, 42, 0.95);
        border-color: var(--neon-cyan);
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
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
        cursor: pointer;
    }

    .form-select-dark:focus {
        background: rgba(15, 23, 42, 0.95);
        border-color: var(--neon-cyan);
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
        outline: none;
    }

    .form-select-dark option {
        background: #1a1c2e;
        color: #e2e8f0;
    }

    /* Button Neon */
    .btn-neon {
        background: linear-gradient(135deg, var(--neon-cyan) 0%, var(--electric-blue) 100%);
        border: none;
        color: #0f172a;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
        text-decoration: none;
        display: inline-block;
        white-space: nowrap;
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
        white-space: nowrap;
    }

    .btn-outline-neon:hover {
        background: rgba(0, 212, 255, 0.1);
        transform: translateY(-2px);
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
        color: var(--neon-cyan);
        text-decoration: none;
    }

    /* Table Dark */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table-dark {
        width: 100%;
        color: #e2e8f0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-dark thead th {
        background: linear-gradient(135deg, rgba(14, 165, 233, 0.3) 0%, rgba(0, 212, 255, 0.2) 100%);
        color: var(--neon-cyan);
        text-transform: uppercase;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 1rem;
        border: none;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 10;
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
        gap: 0.75rem;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--neon-cyan) 0%, var(--electric-blue) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.875rem;
        color: #0f172a;
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.5);
        flex-shrink: 0;
    }

    .user-details {
        min-width: 0;
        flex: 1;
    }

    .user-name {
        display: block;
        color: #e2e8f0;
        font-size: 0.9rem;
        line-height: 1.4;
        word-break: break-word;
    }

    .text-muted-dark {
        color: #94a3b8;
        font-size: 0.75rem;
    }

    .text-wrap {
        word-break: break-all;
        display: block;
    }

    /* Badges */
    .badge-neon {
        background: linear-gradient(135deg, var(--neon-cyan) 0%, var(--electric-blue) 100%);
        color: #0f172a;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.75rem;
        box-shadow: 0 0 15px rgba(0, 255, 255, 0.4);
        display: inline-block;
        white-space: nowrap;
    }

    .badge-success {
        background: rgba(16, 185, 129, 0.25);
        color: #10b981;
        border: 2px solid #10b981;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        display: inline-block;
        white-space: nowrap;
        box-shadow: 0 0 15px rgba(16, 185, 129, 0.3);
    }

    .badge-info {
        background: rgba(6, 182, 212, 0.25);
        color: #06b6d4;
        border: 2px solid #06b6d4;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        display: inline-block;
        white-space: nowrap;
        box-shadow: 0 0 15px rgba(6, 182, 212, 0.3);
    }

    .badge-warning {
        background: rgba(245, 158, 11, 0.25);
        color: #f59e0b;
        border: 2px solid #f59e0b;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        display: inline-block;
        white-space: nowrap;
        box-shadow: 0 0 15px rgba(245, 158, 11, 0.3);
    }

    /* Status Badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .status-active {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 1px solid #10b981;
    }

    .status-inactive {
        background: rgba(100, 116, 139, 0.2);
        color: #94a3b8;
        border: 1px solid #64748b;
    }

    .status-suspended {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 1px solid #ef4444;
    }

    .status-pending {
        background: rgba(245, 158, 11, 0.2);
        color: #f59e0b;
        border: 1px solid #f59e0b;
    }

    .status-badge i {
        font-size: 0.5rem;
        animation: pulse 2s infinite;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        justify-content: flex-start;
        align-items: center;
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
        flex-shrink: 0;
    }

    .btn-action-info {
        border-color: #06b6d4;
        color: #06b6d4;
    }

    .btn-action-info:hover {
        background: #06b6d4;
        color: white;
        box-shadow: 0 0 15px rgba(6, 182, 212, 0.5);
        transform: scale(1.1);
    }

    .btn-action-warning {
        border-color: #84cc16;
        color: #84cc16;
    }

    .btn-action-warning:hover {
        background: #84cc16;
        color: #0f172a;
        box-shadow: 0 0 15px rgba(132, 204, 22, 0.5);
        transform: scale(1.1);
    }

    .btn-action-danger {
        border-color: #ef4444;
        color: #ef4444;
    }

    .btn-action-danger:hover {
        background: #ef4444;
        color: white;
        box-shadow: 0 0 15px rgba(239, 68, 68, 0.5);
        transform: scale(1.1);
    }

    /* Empty State */
    .empty-state {
        color: #94a3b8;
        padding: 3rem 1rem;
    }

    .empty-state i {
        color: rgba(0, 212, 255, 0.3);
    }

    .empty-state h5 {
        color: #e2e8f0;
        margin: 1rem 0 0.5rem 0;
    }

    /* Pagination */
    .pagination-wrapper {
        display: flex;
        justify-content: center;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid rgba(0, 212, 255, 0.2);
    }

    .pagination {
        display: flex;
        list-style: none;
        margin: 0;
        padding: 0;
        gap: 0.5rem;
    }

    .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
        height: 40px;
        padding: 0.5rem 0.75rem;
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 8px;
        color: #e2e8f0;
        text-decoration: none;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .page-link:hover {
        background: rgba(0, 212, 255, 0.1);
        border-color: var(--neon-cyan);
        color: var(--neon-cyan);
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
        transform: translateY(-1px);
    }

    .page-item.active .page-link {
        background: linear-gradient(135deg, var(--neon-cyan) 0%, var(--electric-blue) 100%);
        border-color: var(--neon-cyan);
        color: #0f172a;
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.5);
        font-weight: 600;
    }

    .page-item.disabled .page-link {
        background: rgba(45, 49, 72, 0.5);
        border-color: rgba(0, 212, 255, 0.2);
        color: #64748b;
        cursor: not-allowed;
        opacity: 0.6;
    }

    /* Pagination Links - Fallback Styles */
    .pagination-wrapper a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
        height: 40px;
        padding: 0.5rem 0.75rem;
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 8px;
        color: #e2e8f0;
        text-decoration: none;
        transition: all 0.3s ease;
        font-weight: 500;
        margin: 0 0.25rem;
    }

    .pagination-wrapper a:hover {
        background: rgba(0, 212, 255, 0.1);
        border-color: var(--neon-cyan);
        color: var(--neon-cyan);
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
        transform: translateY(-1px);
    }

    .pagination-wrapper .active a {
        background: linear-gradient(135deg, var(--neon-cyan) 0%, var(--electric-blue) 100%);
        border-color: var(--neon-cyan);
        color: #0f172a;
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.5);
        font-weight: 600;
    }

    .pagination-wrapper .disabled a {
        background: rgba(45, 49, 72, 0.5);
        border-color: rgba(0, 212, 255, 0.2);
        color: #64748b;
        cursor: not-allowed;
        opacity: 0.6;
    }
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .stat-content h3 {
            font-size: 1.5rem;
        }

        .card-body-dark {
            padding: 1rem;
        }

        .table-dark thead th,
        .table-dark tbody td {
            padding: 0.75rem 0.5rem;
            font-size: 0.8rem;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            font-size: 0.75rem;
        }

        .action-buttons {
            flex-direction: column;
        }
    }

    /* Scrollbar Personalizado */
    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: rgba(26, 28, 46, 0.5);
        border-radius: 10px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, var(--neon-cyan) 0%, var(--electric-blue) 100%);
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 255, 255, 0.5);
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        box-shadow: 0 0 20px rgba(0, 255, 255, 0.8);
    }
</style>
@endpush

@push('scripts')
<script>
    function confirmarEliminacion(id) {
        if (confirm('¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }

    // Limpiar filtros
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('input[name="search"]');
        if (searchInput && searchInput.value === '') {
            searchInput.placeholder = 'Buscar por nombre, email...';
        }
    });
</script>
@endpush
@endsection