@extends('layouts.app')

@section('title', 'Gestión de Asistencias')
@section('page-title', 'Asistencias')

@section('content')
<div class="container-fluid">
    <!-- Estadísticas -->
    <div class="stats-grid mb-4">
        <div class="stat-card">
            <div class="stat-icon bg-gradient-primary">
                <i class="fas fa-clipboard-check"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['total'] }}</h3>
                <p>Total Registros</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-success">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['presente'] }}</h3>
                <p>Presentes</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['tardanza'] }}</h3>
                <p>Tardanzas</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-danger">
                <i class="fas fa-user-times"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['ausente'] }}</h3>
                <p>Ausentes</p>
            </div>
        </div>
    </div>

    <div class="card-dark">
        <div class="card-header-dark">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0">
                    <i class="fas fa-clipboard-check me-2"></i>Registro de Asistencias
                </h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.asistencias.reporte') }}" class="btn-neon btn-sm">
                        <i class="fas fa-chart-bar me-2"></i>Reportes
                    </a>
                    <a href="{{ route('admin.asistencias.create') }}" class="btn-neon btn-sm">
                        <i class="fas fa-plus me-2"></i>Tomar Asistencia
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body-dark">
            @if(session('success'))
                <div class="alert-success-dark mb-4">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Búsqueda Principal Mejorada -->
            <form method="GET" action="{{ route('admin.asistencias.index') }}" class="filter-form mb-4" id="filterForm">
                
                <!-- Búsqueda Principal -->
                <div class="row g-3 mb-3">
                    <div class="col-md-9">
                        <div class="search-box-main">
                            <div class="input-group-dark">
                                <span class="input-icon">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" 
                                    name="search" 
                                    id="mainSearch"
                                    class="form-control-dark" 
                                    placeholder="🔍 Buscar por estudiante, código o curso..."
                                    value="{{ request('search') }}"
                                    oninput="validarBusquedaAsistencias(this)"
                                    maxlength="100"
                                    autocomplete="off">
                                @if(request('search'))
                                <div id="search-error-asistencias" class="search-error-message" style="display: none;">
    <i class="fas fa-exclamation-circle me-1"></i>
    Búsqueda inválida. Use solo caracteres permitidos.
</div>
<div id="search-success-asistencias" class="search-success-message" style="display: none;">
    <i class="fas fa-check-circle me-1"></i>
    Búsqueda válida
</div>
                                    <button type="button" class="clear-btn" onclick="clearMainSearch()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @endif
                            </div>
                            <small class="search-hint">
                                <i class="fas fa-info-circle me-1"></i>
                                Ejemplos: "Juan Pérez", "EST-001", "Matemáticas"
                            </small>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn-neon flex-grow-1">
                                <i class="fas fa-search me-2"></i>Buscar
                            </button>
                            <button type="button" class="btn-outline-neon" onclick="toggleAdvancedFilters()" id="toggleFiltersBtn">
                                <i class="fas fa-sliders-h"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filtros Avanzados (Colapsable) -->
                <div id="advancedFilters" style="display: {{ request()->hasAny(['seccion_id', 'periodo_id', 'estado', 'fecha']) ? 'block' : 'none' }};">
                    <div class="advanced-filters-container">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="section-divider">
                                    <i class="fas fa-filter me-2"></i>
                                    <span>Filtros Avanzados</span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <select name="seccion_id" class="form-select-dark">
                                    <option value="">📚 Todas las Secciones</option>
                                    @foreach($secciones as $seccion)
                                        <option value="{{ $seccion->id }}" {{ request('seccion_id') == $seccion->id ? 'selected' : '' }}>
                                            {{ $seccion->curso->nombre }} - {{ $seccion->codigo_seccion }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <select name="periodo_id" class="form-select-dark">
                                    <option value="">📅 Todos los Períodos</option>
                                    @foreach($periodos as $periodo)
                                        <option value="{{ $periodo->id }}" {{ request('periodo_id') == $periodo->id ? 'selected' : '' }}>
                                            {{ $periodo->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <select name="estado" class="form-select-dark">
                                    <option value="">📊 Todos los estados</option>
                                    <option value="presente" {{ request('estado') == 'presente' ? 'selected' : '' }}>✅ Presente</option>
                                    <option value="tardanza" {{ request('estado') == 'tardanza' ? 'selected' : '' }}>⏰ Tardanza</option>
                                    <option value="ausente" {{ request('estado') == 'ausente' ? 'selected' : '' }}>❌ Ausente</option>
                                    <option value="justificado" {{ request('estado') == 'justificado' ? 'selected' : '' }}>📝 Justificado</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <input type="date" 
                                       name="fecha" 
                                       class="form-control-dark"
                                       value="{{ request('fecha') }}">
                            </div>

                            <div class="col-12">
                                <div class="d-flex gap-2 justify-content-end">
                                    <button type="button" class="btn-outline-neon" onclick="clearAllFilters()">
                                        <i class="fas fa-eraser me-2"></i>Limpiar Todo
                                    </button>
                                    <button type="submit" class="btn-neon">
                                        <i class="fas fa-check me-2"></i>Aplicar Filtros
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Badges de Filtros Activos -->
                @if(request()->hasAny(['search', 'seccion_id', 'periodo_id', 'estado', 'fecha']))
                    <div class="active-filters mt-3">
                        <span class="filter-label">
                            <i class="fas fa-filter me-2"></i>Filtros aplicados:
                        </span>
                        
                        @if(request('search'))
                            <span class="filter-badge">
                                🔍 "{{ Str::limit(request('search'), 30) }}"
                                <button type="button" onclick="removeFilter('search')" class="remove-filter">
                                    <i class="fas fa-times"></i>
                                </button>
                            </span>
                        @endif

                        @if(request('seccion_id'))
                            @php
                                $seccion = $secciones->firstWhere('id', request('seccion_id'));
                            @endphp
                            <span class="filter-badge">
                                📚 {{ $seccion->curso->nombre ?? 'Sección' }}
                                <button type="button" onclick="removeFilter('seccion_id')" class="remove-filter">
                                    <i class="fas fa-times"></i>
                                </button>
                            </span>
                        @endif

                        @if(request('periodo_id'))
                            @php
                                $periodo = $periodos->firstWhere('id', request('periodo_id'));
                            @endphp
                            <span class="filter-badge">
                                📅 {{ $periodo->nombre ?? 'Período' }}
                                <button type="button" onclick="removeFilter('periodo_id')" class="remove-filter">
                                    <i class="fas fa-times"></i>
                                </button>
                            </span>
                        @endif

                        @if(request('estado'))
                            <span class="filter-badge">
                                @if(request('estado') == 'presente') ✅
                                @elseif(request('estado') == 'tardanza') ⏰
                                @elseif(request('estado') == 'ausente') ❌
                                @else 📝 @endif
                                {{ ucfirst(request('estado')) }}
                                <button type="button" onclick="removeFilter('estado')" class="remove-filter">
                                    <i class="fas fa-times"></i>
                                </button>
                            </span>
                        @endif

                        @if(request('fecha'))
                            <span class="filter-badge">
                                📅 {{ \Carbon\Carbon::parse(request('fecha'))->format('d/m/Y') }}
                                <button type="button" onclick="removeFilter('fecha')" class="remove-filter">
                                    <i class="fas fa-times"></i>
                                </button>
                            </span>
                        @endif

                        <button type="button" class="clear-all-btn" onclick="clearAllFilters()">
                            <i class="fas fa-times-circle me-1"></i>Limpiar todos
                        </button>
                    </div>
                @endif
            </form>

            <!-- Contador de resultados -->
            @if(request()->hasAny(['search', 'seccion_id', 'periodo_id', 'estado', 'fecha']))
                <div class="results-counter mb-3">
                    <i class="fas fa-list-ol me-2"></i>
                    Se encontraron <strong>{{ $asistencias->total() }}</strong> registros de asistencia
                </div>
            @endif

            <!-- Tabla -->
            <div class="table-responsive">
                <table class="table-dark">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Estudiante</th>
                            <th>Curso/Sección</th>
                            <th>Estado</th>
                            <th>Observaciones</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($asistencias as $asistencia)
                            <tr>
                                <td>
                                    <div class="d-flex flex-column">
                                        <strong>{{ $asistencia->fecha->format('d/m/Y') }}</strong>
                                        <small class="text-muted">{{ $asistencia->fecha->locale('es')->dayName }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="course-info">
                                        <div class="course-icon bg-gradient-info">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div>
                                            <strong>{{ $asistencia->inscripcion->estudiante->nombre_completo }}</strong>
                                            <br><small class="text-muted">{{ $asistencia->inscripcion->estudiante->codigo }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <strong>{{ $asistencia->inscripcion->seccion->curso->nombre }}</strong>
                                    <br><span class="badge-info">{{ $asistencia->inscripcion->seccion->codigo_seccion }}</span>
                                </td>
                                <td>
                                    @php
                                        $estadoConfig = [
                                            'presente' => ['text' => 'Presente', 'class' => 'status-badge status-active', 'icon' => 'fa-check-circle'],
                                            'tardanza' => ['text' => 'Tardanza', 'class' => 'status-badge status-warning', 'icon' => 'fa-clock'],
                                            'ausente' => ['text' => 'Ausente', 'class' => 'status-badge status-inactive', 'icon' => 'fa-times-circle'],
                                            'justificado' => ['text' => 'Justificado', 'class' => 'status-badge status-info', 'icon' => 'fa-file-alt'],
                                        ][$asistencia->estado] ?? ['text' => 'Desconocido', 'class' => 'status-badge', 'icon' => 'fa-question'];
                                    @endphp
                                    <span class="{{ $estadoConfig['class'] }}">
                                        <i class="fas {{ $estadoConfig['icon'] }}"></i> {{ $estadoConfig['text'] }}
                                    </span>
                                </td>
                                <td>
                                    @if($asistencia->observaciones)
                                        <span class="text-muted" title="{{ $asistencia->observaciones }}">
                                            {{ Str::limit($asistencia->observaciones, 30) }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.asistencias.show', $asistencia) }}" 
                                           class="btn-action btn-action-info"
                                           title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.asistencias.edit', $asistencia) }}" 
                                           class="btn-action btn-action-warning"
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn-action btn-action-danger"
                                                onclick="confirmarEliminacion({{ $asistencia->id }})"
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <form id="delete-form-{{ $asistencia->id }}" 
                                          action="{{ route('admin.asistencias.destroy', $asistencia) }}" 
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
                                        <i class="fas fa-clipboard-check fa-3x mb-3"></i>
                                        <h5>No se encontraron registros de asistencia</h5>
                                        <p>{{ request()->hasAny(['search', 'seccion_id', 'estado', 'fecha']) ? 'Intenta ajustar los filtros de búsqueda' : 'Comienza tomando asistencia' }}</p>
                                        @if(!request()->hasAny(['search', 'seccion_id', 'estado', 'fecha']))
                                            <a href="{{ route('admin.asistencias.create') }}" class="btn-neon mt-3">
                                                <i class="fas fa-plus me-2"></i>Tomar Asistencia
                                            </a>
                                        @else
                                            <button type="button" class="btn-neon mt-3" onclick="clearAllFilters()">
                                                <i class="fas fa-eraser me-2"></i>Limpiar Filtros
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($asistencias->hasPages())
                <div class="pagination-dark mt-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="pagination-info">
                            Mostrando {{ $asistencias->firstItem() }} - {{ $asistencias->lastItem() }} de {{ $asistencias->total() }} resultados
                        </div>
                        <div class="pagination-links">
                            @if ($asistencias->onFirstPage())
                                <span class="page-link disabled">
                                    <i class="fas fa-chevron-left"></i>
                                </span>
                            @else
                                <a href="{{ $asistencias->previousPageUrl() }}" class="page-link">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            @endif

                            @foreach ($asistencias->getUrlRange(1, $asistencias->lastPage()) as $page => $url)
                                @if ($page == $asistencias->currentPage())
                                    <span class="page-link active">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                                @endif
                            @endforeach

                            @if ($asistencias->hasMorePages())
                                <a href="{{ $asistencias->nextPageUrl() }}" class="page-link">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            @else
                                <span class="page-link disabled">
                                    <i class="fas fa-chevron-right"></i>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    :root {
        --neon-cyan: #00d4ff;
        --neon-blue: #0ea5e9;
        --dark-bg: #0f172a;
        --dark-card: #1e293b;
        --text-color: #e2e8f0;
        --muted-text: #94a3b8;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
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
    .bg-gradient-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .bg-gradient-danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
    .bg-gradient-info { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }

    .stat-content h3 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--neon-cyan);
        margin: 0;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .stat-content p {
        color: var(--muted-text);
        margin: 0;
        font-size: 0.875rem;
    }

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
        color: var(--text-color);
    }

    .card-header-dark h5 {
        color: var(--neon-cyan);
        font-weight: 600;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .card-body-dark {
        padding: 2rem;
        color: var(--text-color);
    }

    /* Búsqueda Principal Mejorada */
    .search-box-main {
        position: relative;
    }

    .input-group-dark {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-icon {
        position: absolute;
        left: 15px;
        color: var(--neon-cyan);
        z-index: 10;
        font-size: 1.1rem;
    }

    .form-control-dark {
        background: rgba(15, 23, 42, 0.8);
        border: 2px solid rgba(0, 212, 255, 0.3);
        border-radius: 12px;
        color: #e2e8f0;
        padding: 1rem 3rem 1rem 3rem;
        width: 100%;
        transition: all 0.3s ease;
        font-size: 1rem;
    }

    .form-control-dark:focus {
        background: rgba(15, 23, 42, 0.95);
        border-color: var(--neon-cyan);
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.4);
        outline: none;
        color: white;
    }

    .form-control-dark::placeholder {
        color: var(--muted-text);
        opacity: 0.7;
    }

    .search-hint {
        display: block;
        color: var(--muted-text);
        font-size: 0.75rem;
        margin-top: 0.5rem;
        padding-left: 0.5rem;
    }

    .clear-btn {
        position: absolute;
        right: 1rem;
        background: rgba(239, 68, 68, 0.2);
        border: 1px solid var(--danger-color);
        color: var(--danger-color);
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        z-index: 10;
    }

    .clear-btn:hover {
        background: var(--danger-color);
        color: white;
        transform: rotate(90deg);
    }

    /* Filtros Avanzados */
    .advanced-filters-container {
        background: rgba(0, 212, 255, 0.05);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
        padding: 1.5rem;
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .section-divider {
        color: var(--neon-cyan);
        font-weight: 600;
        font-size: 0.95rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid rgba(0, 212, 255, 0.2);
        margin-bottom: 0.5rem;
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

    .form-select-dark option {
        background-color: var(--dark-card);
        color: var(--text-color);
    }

    /* Filtros Activos */
    .active-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        align-items: center;
        padding: 1rem;
        background: rgba(0, 212, 255, 0.05);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
    }

    .filter-label {
        color: var(--neon-cyan);
        font-weight: 600;
        font-size: 0.9rem;
    }

    .filter-badge {
        background: linear-gradient(135deg, rgba(14, 165, 233, 0.2) 0%, rgba(0, 212, 255, 0.2) 100%);
        border: 1px solid var(--neon-cyan);
        color: var(--text-color);
        padding: 0.5rem 1rem;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.85rem;
    }

    .remove-filter {
        background: rgba(239, 68, 68, 0.2);
        border: none;
        color: var(--danger-color);
        width: 18px;
        height: 18px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        padding: 0;
        font-size: 0.7rem;
    }

    .remove-filter:hover {
        background: var(--danger-color);
        color: white;
        transform: rotate(90deg);
    }

    .clear-all-btn {
        background: transparent;
        border: 1px solid var(--danger-color);
        color: var(--danger-color);
        padding: 0.5rem 1rem;
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .clear-all-btn:hover {
        background: var(--danger-color);
        color: white;
    }

    .results-counter {
        color: var(--neon-cyan);
        font-size: 0.95rem;
        padding: 0.75rem 1rem;
        background: rgba(0, 212, 255, 0.05);
        border-left: 3px solid var(--neon-cyan);
        border-radius: 5px;
    }

    .alert-success-dark {
        background-color: rgba(16, 185, 129, 0.2);
        border: 1px solid #10b981;
        color: #10b981;
        padding: 1rem;
        border-radius: 8px;
    }

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

    .btn-neon.btn-sm {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
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

    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

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
        color: #e2e8f0;
    }

    .course-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .course-info strong {
        color: #e2e8f0;
    }

    .course-info .text-muted {
        color: #94a3b8 !important;
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
        flex-shrink: 0;
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

    .status-active {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 1px solid #10b981;
    }

    .status-inactive {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 1px solid #ef4444;
    }

    .status-warning {
        background: rgba(245, 158, 11, 0.2);
        color: #f59e0b;
        border: 1px solid #f59e0b;
    }

    .status-info {
        background: rgba(6, 182, 212, 0.2);
        color: #06b6d4;
        border: 1px solid #06b6d4;
    }

    .status-badge i {
        font-size: 0.5rem;
        animation: statusPulse 2s infinite;
    }

    @keyframes statusPulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
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

    .text-muted {
        color: var(--muted-text) !important;
    }

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

    .empty-state {
        color: #94a3b8;
        padding: 3rem;
        text-align: center;
    }

    .empty-state i {
        color: rgba(0, 212, 255, 0.3);
    }

    .empty-state h5 {
        color: #e2e8f0;
        margin-bottom: 0.5rem;
    }

    .pagination-dark {
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
        padding: 1rem 1.5rem;
    }

    .pagination-info {
        color: var(--muted-text);
        font-size: 0.875rem;
    }

    .pagination-links {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .page-link {
        min-width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(0, 212, 255, 0.3);
        background: rgba(15, 23, 42, 0.8);
        color: var(--neon-cyan);
        border-radius: 8px;
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 0.875rem;
    }

    .page-link:hover:not(.disabled):not(.active) {
        background: rgba(0, 212, 255, 0.2);
        border-color: var(--neon-cyan);
        transform: translateY(-2px);
        box-shadow: 0 0 10px rgba(0, 212, 255, 0.3);
    }

    .page-link.active {
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        color: #0f172a;
        border-color: var(--neon-cyan);
        font-weight: 600;
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.5);
    }

    .page-link.disabled {
        opacity: 0.3;
        cursor: not-allowed;
        border-color: rgba(0, 212, 255, 0.1);
        color: var(--muted-text);
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
        
        .action-buttons {
            flex-direction: row;
            flex-wrap: wrap;
        }
        
        .table-responsive {
            overflow-x: auto;
        }

        .pagination-dark .d-flex {
            flex-direction: column;
            gap: 1rem;
        }

        .pagination-info {
            text-align: center;
        }

        .pagination-links {
            justify-content: center;
            flex-wrap: wrap;
        }

        .card-body-dark {
            padding: 1.5rem;
        }
    }
    /* VALIDACIÓN DE BÚSQUEDA */
    .search-error-message {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.95) 0%, rgba(220, 38, 38, 0.95) 100%);
        color: white;
        padding: 0.75rem 1rem;
        border-radius: 0 0 10px 10px;
        font-size: 0.85rem;
        font-weight: 500;
        z-index: 100;
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.5);
        animation: slideDownError 0.3s ease;
        margin-top: 2px;
    }

    .search-success-message {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.95) 0%, rgba(5, 150, 105, 0.95) 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0 0 10px 10px;
        font-size: 0.8rem;
        font-weight: 500;
        z-index: 100;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.5);
        animation: slideDownSuccess 0.3s ease;
        margin-top: 2px;
    }

    @keyframes slideDownError {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideDownSuccess {
        from {
            opacity: 0;
            transform: translateY(-5px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .form-control-dark.error {
        border-color: #ef4444 !important;
        box-shadow: 0 0 20px rgba(239, 68, 68, 0.4) !important;
        animation: shake 0.5s;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }

    .form-control-dark.success {
        border-color: #10b981 !important;
        box-shadow: 0 0 20px rgba(16, 185, 129, 0.3) !important;
    }
</style>
@endpush

@push('scripts')
<script>
    function confirmarEliminacion(id) {
        if (confirm('¿Estás seguro de eliminar este registro de asistencia?')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }

    // VALIDACIÓN DE BÚSQUEDA DE ASISTENCIAS
    function validarBusquedaAsistencias(input) {
        const valor = input.value.trim();
        const errorDiv = document.getElementById('search-error-asistencias');
        const successDiv = document.getElementById('search-success-asistencias');
        const btnBuscar = document.getElementById('btnBuscar');
        
        // ✅ VALIDACIÓN PERMISIVA: rechaza solo caracteres peligrosos
        // Bloquea: < > ; ' " = \ (pueden ser inyección SQL/XSS)
        // Permite: letras, números, espacios, @, #, -, _, ., /, etc.
        const caracteresProhibidos = /[<>;'"=\\]/;
        
        if (valor === '') {
            // Campo vacío - estado neutral
            input.classList.remove('error', 'success');
            if (errorDiv) errorDiv.style.display = 'none';
            if (successDiv) successDiv.style.display = 'none';
            if (btnBuscar) btnBuscar.disabled = false;
            return;
        }
        
        if (caracteresProhibidos.test(valor)) {
            // Contiene caracteres peligrosos
            input.classList.add('error');
            input.classList.remove('success');
            if (errorDiv) {
                errorDiv.style.display = 'block';
                errorDiv.innerHTML = '<i class="fas fa-exclamation-circle me-1"></i>No se permiten los caracteres: < > ; \' " = \\';
            }
            if (successDiv) successDiv.style.display = 'none';
            if (btnBuscar) btnBuscar.disabled = true;
        } else {
            // Texto válido
            input.classList.remove('error');
            input.classList.add('success');
            if (errorDiv) errorDiv.style.display = 'none';
            if (successDiv) successDiv.style.display = 'block';
            if (btnBuscar) btnBuscar.disabled = false;
            
            // Auto-ocultar mensaje de éxito después de 2 segundos
            setTimeout(() => {
                if (successDiv) successDiv.style.display = 'none';
                input.classList.remove('success');
            }, 2000);
        }
    }

    // Validar al enviar el formulario
    document.getElementById('filterForm').addEventListener('submit', function(e) {
        const input = document.getElementById('mainSearch');
        const valor = input.value.trim();
        const caracteresProhibidos = /[<>;'"=\\]/;
        
        if (valor !== '' && caracteresProhibidos.test(valor)) {
            e.preventDefault();
            input.focus();
            validarBusquedaAsistencias(input);
        }
    });

    function toggleAdvancedFilters() {
        const filters = document.getElementById('advancedFilters');
        const toggleBtn = document.getElementById('toggleFiltersBtn');
        
        if (filters.style.display === 'none' || filters.style.display === '') {
            filters.style.display = 'block';
            toggleBtn.innerHTML = '<i class="fas fa-times"></i>';
        } else {
            filters.style.display = 'none';
            toggleBtn.innerHTML = '<i class="fas fa-sliders-h"></i>';
        }
    }

    function clearMainSearch() {
        document.getElementById('mainSearch').value = '';
        document.getElementById('filterForm').submit();
    }

    function removeFilter(filterName) {
        const form = document.getElementById('filterForm');
        const input = form.querySelector(`[name="${filterName}"]`);
        if (input) {
            input.value = '';
        }
        form.submit();
    }

    function clearAllFilters() {
        window.location.href = '{{ route('admin.asistencias.index') }}';
    }

    window.addEventListener('DOMContentLoaded', function() {
        const hasAdvancedFilters = {{ request()->hasAny(['seccion_id', 'periodo_id', 'estado', 'fecha']) ? 'true' : 'false' }};
        if (hasAdvancedFilters) {
            const filters = document.getElementById('advancedFilters');
            const toggleBtn = document.getElementById('toggleFiltersBtn');
            filters.style.display = 'block';
            toggleBtn.innerHTML = '<i class="fas fa-times"></i>';
        }
    });
</script>
@endpush
@endsection