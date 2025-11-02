@extends('layouts.app')

@section('title', 'Configuraciones del Sistema')
@section('page-title', 'Configuraciones')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->

    <!-- Mensajes -->
    @if(session('success'))
        <div class="alert-success-neon mb-4">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Info Banner -->
    <div class="info-banner mb-4">
        <div class="info-icon">
            <i class="fas fa-info-circle"></i>
        </div>
        <div class="info-content">
            <h5>¿Qué son las configuraciones?</h5>
            <p>Las configuraciones son <strong>parámetros clave del sistema</strong> que controlan su comportamiento. Cada configuración tiene:</p>
            <ul>
                <li><strong>Clave:</strong> Nombre único del parámetro (ej: nota_minima_aprobacion)</li>
                <li><strong>Valor:</strong> El dato almacenado (ej: 70, "San Juan", true)</li>
                <li><strong>Tipo:</strong> El formato del dato (Texto, Número, Booleano, JSON)</li>
                <li><strong>Descripción:</strong> Para qué sirve este parámetro</li>
            </ul>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions-grid mb-4">
        <a href="{{ route('admin.configuraciones.sistema') }}" class="action-card action-card-primary">
            <div class="action-icon bg-gradient-primary">
                <i class="fas fa-cogs"></i>
            </div>
            <div class="action-content">
                <h5>Configuración del Sistema</h5>
                <p><strong>Recomendado:</strong> Usa esta opción para cambiar nota mínima, zona horaria, etc.</p>
            </div>
            <div class="action-arrow">
                <i class="fas fa-arrow-right"></i>
            </div>
        </a>

        <a href="{{ route('admin.configuraciones.create') }}" class="action-card">
            <div class="action-icon bg-gradient-success">
                <i class="fas fa-plus-circle"></i>
            </div>
            <div class="action-content">
                <h5>Nueva Configuración Personalizada</h5>
                <p><strong>Avanzado:</strong> Crea un nuevo parámetro personalizado del sistema</p>
            </div>
            <div class="action-arrow">
                <i class="fas fa-arrow-right"></i>
            </div>
        </a>
    </div>

    <!-- Estadísticas -->
    <div class="stats-grid mb-4">
        <div class="stat-card">
            <div class="stat-icon bg-gradient-info">
                <i class="fas fa-sliders-h"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $configuraciones->total() }}</h3>
                <p>Total Configuraciones</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-success">
                <i class="fas fa-text-width"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $configuraciones->where('tipo', 'string')->count() }}</h3>
                <p>Tipo Texto</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-warning">
                <i class="fas fa-hashtag"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $configuraciones->where('tipo', 'number')->count() }}</h3>
                <p>Tipo Número</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-danger">
                <i class="fas fa-toggle-on"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $configuraciones->where('tipo', 'boolean')->count() }}</h3>
                <p>Tipo Booleano</p>
            </div>
        </div>
    </div>

    <!-- Card Principal -->
    <div class="card-dark">
        <div class="card-header-dark">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h5 class="mb-1">
                        <i class="fas fa-list me-2"></i>Todas las Configuraciones
                    </h5>
                    <small class="text-muted">Vista detallada de todos los parámetros del sistema</small>
                </div>
                <a href="{{ route('admin.configuraciones.create') }}" class="btn-neon">
                    <i class="fas fa-plus me-2"></i>Nueva Configuración
                </a>
            </div>
        </div>

        <div class="card-body-dark">
            <!-- Filtros -->
            <form method="GET" action="{{ route('admin.configuraciones.index') }}" class="filter-form mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="input-group-dark">
                            <span class="input-icon">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" 
                                   name="search" 
                                   class="form-control-dark" 
                                   placeholder="Buscar por clave o descripción..."
                                   value="{{ request('search') }}">
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <select name="tipo" class="form-select-dark">
                            <option value="">Todos los tipos</option>
                            <option value="string" {{ request('tipo') == 'string' ? 'selected' : '' }}>Texto</option>
                            <option value="number" {{ request('tipo') == 'number' ? 'selected' : '' }}>Número</option>
                            <option value="boolean" {{ request('tipo') == 'boolean' ? 'selected' : '' }}>Booleano</option>
                            <option value="json" {{ request('tipo') == 'json' ? 'selected' : '' }}>JSON</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <button type="submit" class="btn-neon w-100">
                            <i class="fas fa-filter me-2"></i>Filtrar
                        </button>
                    </div>

                    @if(request()->hasAny(['search', 'tipo']))
                        <div class="col-md-3">
                            <a href="{{ route('admin.configuraciones.index') }}" class="btn-outline-neon w-100">
                                <i class="fas fa-times me-2"></i>Limpiar
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
                            <th>
                                <i class="fas fa-key me-2"></i>Clave
                                <small class="d-block text-muted">Nombre del parámetro</small>
                            </th>
                            <th>
                                <i class="fas fa-edit me-2"></i>Valor Actual
                                <small class="d-block text-muted">Dato almacenado</small>
                            </th>
                            <th>
                                <i class="fas fa-tag me-2"></i>Tipo
                                <small class="d-block text-muted">Formato del dato</small>
                            </th>
                            <th>
                                <i class="fas fa-align-left me-2"></i>Descripción
                                <small class="d-block text-muted">¿Para qué sirve?</small>
                            </th>
                            <th>
                                <i class="fas fa-clock me-2"></i>Última Modificación
                            </th>
                            <th>
                                <i class="fas fa-tools me-2"></i>Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($configuraciones as $config)
                            <tr>
                                <td>
                                    <div class="config-key">
                                        <i class="fas fa-key text-cyan"></i>
                                        <strong>{{ $config->clave }}</strong>
                                    </div>
                                </td>
                                <td>
                                    <div class="config-value">
                                        @if($config->tipo === 'boolean')
                                            <span class="badge-{{ $config->valor_tipificado ? 'success' : 'danger' }}">
                                                <i class="fas fa-{{ $config->valor_tipificado ? 'check' : 'times' }}-circle me-1"></i>
                                                {{ $config->valor_tipificado ? 'True' : 'False' }}
                                            </span>
                                        @elseif($config->tipo === 'json')
                                            <code class="json-preview" title="{{ $config->valor }}">
                                                {{ Str::limit($config->valor, 50) }}
                                            </code>
                                        @else
                                            <span class="value-text">{{ Str::limit($config->valor, 60) }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @switch($config->tipo)
                                        @case('string')
                                            <span class="type-badge badge-info">
                                                <i class="fas fa-text-width me-1"></i>Texto
                                            </span>
                                            @break
                                        @case('number')
                                            <span class="type-badge badge-warning">
                                                <i class="fas fa-hashtag me-1"></i>Número
                                            </span>
                                            @break
                                        @case('boolean')
                                            <span class="type-badge badge-danger">
                                                <i class="fas fa-toggle-on me-1"></i>Booleano
                                            </span>
                                            @break
                                        @case('json')
                                            <span class="type-badge badge-primary">
                                                <i class="fas fa-code me-1"></i>JSON
                                            </span>
                                            @break
                                    @endswitch
                                </td>
                                <td>
                                    @if($config->descripcion)
                                        <span class="text-muted">{{ Str::limit($config->descripcion, 80) }}</span>
                                    @else
                                        <span class="text-muted-light">Sin descripción</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $config->updated_at->format('d/m/Y H:i') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.configuraciones.edit', $config) }}" 
                                           class="btn-action btn-action-warning"
                                           title="Editar esta configuración">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn-action btn-action-danger"
                                                onclick="confirmarEliminacion({{ $config->id }})"
                                                title="Eliminar configuración">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <form id="delete-form-{{ $config->id }}" 
                                          action="{{ route('admin.configuraciones.destroy', $config) }}" 
                                          method="POST" 
                                          style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-cog fa-3x mb-3"></i>
                                        <h5>No se encontraron configuraciones</h5>
                                        <p>Comienza creando una nueva configuración o ajusta los filtros de búsqueda</p>
                                        <a href="{{ route('admin.configuraciones.create') }}" class="btn-neon mt-3">
                                            <i class="fas fa-plus me-2"></i>Crear Configuración
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
                {{ $configuraciones->links() }}
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Alerts */
    .alert-success-neon {
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid #10b981;
        border-radius: 12px;
        padding: 1rem 1.5rem;
        color: #10b981;
        font-weight: 500;
        box-shadow: 0 0 20px rgba(16, 185, 129, 0.2);
        animation: slideInDown 0.4s ease;
    }

    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Info Banner */
    .info-banner {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(14, 165, 233, 0.05) 100%);
        border: 1px solid rgba(59, 130, 246, 0.3);
        border-radius: 15px;
        padding: 1.5rem;
        display: flex;
        gap: 1.5rem;
        align-items: flex-start;
    }

    .info-icon {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #3b82f6 0%, #0ea5e9 100%);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        color: white;
        flex-shrink: 0;
        box-shadow: 0 0 20px rgba(59, 130, 246, 0.4);
    }

    .info-content h5 {
        color: var(--neon-cyan);
        margin-bottom: 0.75rem;
        font-weight: 600;
    }

    .info-content p {
        color: #94a3b8;
        margin-bottom: 0.5rem;
        line-height: 1.6;
    }

    .info-content ul {
        list-style: none;
        padding-left: 0;
        margin: 0;
        color: #94a3b8;
    }

    .info-content ul li {
        padding: 0.5rem 0;
        border-bottom: 1px solid rgba(0, 212, 255, 0.1);
    }

    .info-content ul li:last-child {
        border-bottom: none;
    }

    .info-content strong {
        color: var(--neon-cyan);
    }

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

    /* Quick Actions Grid */
    .quick-actions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 1.5rem;
    }

    .action-card {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.95) 100%);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 15px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.5rem;
        transition: all 0.3s ease;
        text-decoration: none;
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.1);
    }

    .action-card-primary {
        border: 2px solid rgba(59, 130, 246, 0.5);
        box-shadow: 0 0 30px rgba(59, 130, 246, 0.2);
    }

    .action-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0 40px rgba(0, 212, 255, 0.3);
        border-color: var(--neon-cyan);
    }

    .action-icon {
        width: 70px;
        height: 70px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
        flex-shrink: 0;
    }

    .bg-gradient-primary { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); }
    .bg-gradient-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .bg-gradient-info { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }
    .bg-gradient-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .bg-gradient-danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }

    .action-content {
        flex: 1;
    }

    .action-content h5 {
        color: var(--neon-cyan);
        font-weight: 600;
        margin: 0 0 0.5rem 0;
        font-size: 1.1rem;
    }

    .action-content p {
        color: #94a3b8;
        margin: 0;
        font-size: 0.9rem;
    }

    .action-arrow {
        color: var(--neon-cyan);
        font-size: 1.5rem;
        opacity: 0.5;
        transition: all 0.3s ease;
    }

    .action-card:hover .action-arrow {
        opacity: 1;
        transform: translateX(5px);
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

    .card-header-dark .text-muted {
        color: #94a3b8;
        font-size: 0.875rem;
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

    .table-dark thead th small {
        display: block;
        font-size: 0.75rem;
        color: #64748b;
        font-weight: 400;
        text-transform: none;
        margin-top: 0.25rem;
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
        color: #f1f5f9;
    }

    .config-key {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-family: 'Courier New', monospace;
    }

    .config-key strong {
        color: #ffffff;
        font-weight: 600;
    }

    .text-cyan { color: var(--neon-cyan); }

    .config-value {
        font-family: 'Courier New', monospace;
    }

    .value-text {
        color: #ffffff;
        font-weight: 500;
    }

    .json-preview {
        background: rgba(0, 212, 255, 0.1);
        border: 1px solid rgba(0, 212, 255, 0.3);
        padding: 0.25rem 0.5rem;
        border-radius: 5px;
        color: #06b6d4;
        font-size: 0.85rem;
        cursor: help;
    }

    .type-badge {
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
    }

    .badge-info {
        background: rgba(6, 182, 212, 0.2);
        color: #06b6d4;
        border: 1px solid #06b6d4;
    }

    .badge-warning {
        background: rgba(245, 158, 11, 0.2);
        color: #f59e0b;
        border: 1px solid #f59e0b;
    }

    .badge-danger {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 1px solid #ef4444;
    }

    .badge-primary {
        background: rgba(14, 165, 233, 0.2);
        color: #0ea5e9;
        border: 1px solid #0ea5e9;
    }

    .badge-success {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 1px solid #10b981;
    }

    .text-muted { 
        color: #cbd5e1 !important;
        font-weight: 400;
    }
    .text-muted-light { 
        color: #94a3b8; 
        font-style: italic; 
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

    .btn-action-warning {
        border-color: #10b981;
        color: #10b981;
    }

    .btn-action-warning:hover {
        background: #10b981;
        color: white;
        box-shadow: 0 0 15px rgba(16, 185, 129, 0.5);
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
        if (confirm('¿Estás seguro de que deseas eliminar esta configuración? Esta acción no se puede deshacer.')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
@endpush
@endsection