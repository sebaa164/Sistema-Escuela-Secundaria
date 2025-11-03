@extends('layouts.app')

@section('title', 'Gestión de Cursos')
@section('page-title', 'Cursos')

@section('content')
<div class="container-fluid">
    <!-- Header con Estadísticas -->
    <div class="stats-grid mb-4">
        <div class="stat-card">
            <div class="stat-icon bg-gradient-primary">
                <i class="fas fa-book"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $cursos->total() }}</h3>
                <p>Total Cursos</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $cursos->where('estado', 'activo')->count() }}</h3>
                <p>Cursos Activos</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-info">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $cursos->sum('secciones_count') }}</h3>
                <p>Total Secciones</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-warning">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $carreras->count() }}</h3>
                <p>Carreras</p>
            </div>
        </div>
    </div>

    <!-- Card Principal -->
    <div class="card-dark">
        <div class="card-header-dark">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-book me-2"></i>Listado de Cursos
                </h5>
                <a href="{{ route('admin.cursos.create') }}" class="btn-neon">
                    <i class="fas fa-plus me-2"></i>Nuevo Curso
                </a>
            </div>
        </div>

        <div class="card-body-dark">
            <!-- Filtros -->
            <form method="GET" action="{{ route('admin.cursos.index') }}" class="filter-form mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="input-group-dark">
                            <span class="input-icon">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" 
                            name="search" 
                            class="form-control-dark" 
                            placeholder="Buscar por nombre, código..."
                            value="{{ request('search') }}">
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <select name="carrera" class="form-select-dark">
                            <option value="">Todas las carreras</option>
                            @foreach($carreras as $carrera)
                                <option value="{{ $carrera }}" {{ request('carrera') == $carrera ? 'selected' : '' }}>
                                    {{ $carrera }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <select name="nivel" class="form-select-dark">
                            <option value="">Todos los niveles</option>
                            @foreach($niveles as $nivel)
                                <option value="{{ $nivel }}" {{ request('nivel') == $nivel ? 'selected' : '' }}>
                                    {{ $nivel }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select name="estado" class="form-select-dark">
                            <option value="">Todos los estados</option>
                            <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activos</option>
                            <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                        </select>
                    </div>
                    
                    <div class="col-md-1">
                        <button type="submit" class="btn-neon w-100">
                            <i class="fas fa-filter"></i>
                        </button>
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
                            <th>Carrera</th>
                            <th>Nivel</th>
                            <th>Horas/Sem</th>
                            <th>Secciones</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cursos as $curso)
                            <tr>
                                <td>
                                    <span class="badge-neon">{{ $curso->codigo_curso }}</span>
                                </td>
                                <td>
                                    <div class="course-info">
                                        <div class="course-icon">
                                           <i class="fas fa-book"></i>
                                        </div>
                                        <div>
                                            <strong>{{ $curso->nombre }}</strong>
                                            @if($curso->descripcion)
                                                <br><small class="text-muted">{{ Str::limit($curso->descripcion, 50) }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-info">
                                        <i class="fas fa-graduation-cap me-1"></i>{{ $curso->carrera }}
                                    </span>
                                </td>
                                <td>
                                    @if($curso->nivel)
                                        <span class="badge-success">{{ $curso->nivel }}</span>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="hours-badge">
                                        <i class="fas fa-clock me-1"></i>
                                        <strong>{{ $curso->horas_semanales }}</strong>
                                    </div>
                                </td>
                                <td>
                                    <div class="section-count">
                                        <i class="fas fa-layer-group me-1"></i>
                                        <strong>{{ $curso->secciones_count }}</strong>
                                    </div>
                                </td>
                                <td>
                                    @if($curso->estado === 'activo')
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
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.cursos.show', $curso) }}" 
                                           class="btn-action btn-action-info"
                                           title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.cursos.edit', $curso) }}" 
                                           class="btn-action btn-action-warning"
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn-action btn-action-danger"
                                                onclick="confirmarEliminacion({{ $curso->id }})"
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <form id="delete-form-{{ $curso->id }}" 
                                          action="{{ route('admin.cursos.destroy', $curso) }}" 
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
                                        <i class="fas fa-book fa-3x mb-3"></i>
                                        <h5>No se encontraron cursos</h5>
                                        <p>Intenta ajustar los filtros o crea un nuevo curso</p>
                                        <a href="{{ route('admin.cursos.create') }}" class="btn-neon mt-3">
                                            <i class="fas fa-plus me-2"></i>Crear Curso
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
                {{ $cursos->links() }}
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

    /* Button Neon */
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
        color: #e2e8f0;
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

    .course-info strong {
        color: #e2e8f0;
    }

    .course-info .text-muted {
        color: #94a3b8 !important;
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

    .badge-success {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 1px solid #10b981;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
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

    .hours-badge, .section-count {
        display: inline-flex;
        align-items: center;
        color: var(--neon-cyan);
        font-size: 1rem;
    }

    .hours-badge i {
        color: #f59e0b;
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
        if (confirm('¿Estás seguro de que deseas eliminar este curso? Esta acción no se puede deshacer.')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
@endpush
@endsection