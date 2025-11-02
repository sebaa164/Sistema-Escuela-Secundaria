@extends('layouts.app')

@section('title', 'Gestión de Inscripciones')
@section('page-title', 'Inscripciones')

@section('content')
<div class="container-fluid">
    <div class="stats-grid mb-4">
        <div class="stat-card">
            <div class="stat-icon bg-gradient-primary">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['total'] }}</h3>
                <p>Total Inscripciones</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-success">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['inscritos'] }}</h3>
                <p>Inscritos Activos</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-info">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['completados'] }}</h3>
                <p>Completados</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-warning">
                <i class="fas fa-ban"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['retirados'] }}</h3>
                <p>Retirados/Cancelados</p>
            </div>
        </div>
    </div>

    <div class="card-dark">
        <div class="card-header-dark">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0">
                    <i class="fas fa-clipboard-list me-2"></i>Listado de Inscripciones
                </h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.inscripciones.create-masiva') }}" class="btn-neon btn-sm">
                        <i class="fas fa-users me-2"></i>Inscripción Masiva
                    </a>
                    <a href="{{ route('admin.inscripciones.create') }}" class="btn-neon btn-sm">
                        <i class="fas fa-plus me-2"></i>Nueva Inscripción
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

            <!-- BÚSQUEDA MEJORADA CON VALIDACIÓN -->
            <form method="GET" action="{{ route('admin.inscripciones.index') }}" class="filter-form mb-4" id="filterForm">
                
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
                                       placeholder="🔍 Buscar por estudiante, curso, código de sección o ID..."
                                       value="{{ request('search') }}"
                                       oninput="validarBusquedaInscripciones(this)"
                                       maxlength="100"
                                       autocomplete="off">
                                @if(request('search'))
                                    <button type="button" class="clear-btn" onclick="clearMainSearch()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @endif
                            </div>
                            <div id="search-error-inscripciones" class="search-error-message" style="display: none;">
                                <i class="fas fa-exclamation-circle me-1"></i>
                                Búsqueda inválida. Use solo letras, números, espacios y guiones.
                            </div>
                            <div id="search-success-inscripciones" class="search-success-message" style="display: none;">
                                <i class="fas fa-check-circle me-1"></i>
                                Búsqueda válida
                            </div>
                            <small class="search-hint">
                                <i class="fas fa-info-circle me-1"></i>
                                Ejemplos: "Juan Pérez", "Matemáticas", "MAT-101-A", "#123"
                            </small>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn-neon flex-grow-1" id="btnBuscarInscripciones">
                                <i class="fas fa-search me-2"></i>Buscar
                            </button>
                            <button type="button" class="btn-outline-neon" onclick="toggleAdvancedFilters()" id="toggleFiltersBtn">
                                <i class="fas fa-sliders-h"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filtros Avanzados (Colapsable) -->
                <div id="advancedFilters" style="display: {{ request()->hasAny(['periodo_id', 'estado', 'sort']) ? 'block' : 'none' }};">
                    <div class="advanced-filters-container">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="section-divider">
                                    <i class="fas fa-filter me-2"></i>
                                    <span>Filtros Avanzados</span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <select name="periodo_id" class="form-select-dark">
                                    <option value="">📅 Todos los Períodos</option>
                                    @foreach($periodos as $periodo)
                                        <option value="{{ $periodo->id }}" {{ request('periodo_id') == $periodo->id ? 'selected' : '' }}>
                                            {{ $periodo->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <select name="estado" class="form-select-dark">
                                    <option value="">📊 Todos los estados</option>
                                    <option value="inscrito" {{ request('estado') == 'inscrito' ? 'selected' : '' }}>✅ Inscritos</option>
                                    <option value="retirado" {{ request('estado') == 'retirado' ? 'selected' : '' }}>❌ Retirados</option>
                                    <option value="completado" {{ request('estado') == 'completado' ? 'selected' : '' }}>🎓 Completados</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <select name="sort" class="form-select-dark">
                                    <option value="recent" {{ request('sort') == 'recent' ? 'selected' : '' }}>🕐 Más Recientes</option>
                                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>🕑 Más Antiguos</option>
                                    <option value="student_name" {{ request('sort') == 'student_name' ? 'selected' : '' }}>👤 Por Estudiante (A-Z)</option>
                                    <option value="course_name" {{ request('sort') == 'course_name' ? 'selected' : '' }}>📚 Por Curso (A-Z)</option>
                                </select>
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
                @if(request()->hasAny(['search', 'periodo_id', 'estado', 'sort']))
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
                                @if(request('estado') == 'inscrito') ✅
                                @elseif(request('estado') == 'retirado') ❌
                                @else 🎓 @endif
                                {{ ucfirst(request('estado')) }}
                                <button type="button" onclick="removeFilter('estado')" class="remove-filter">
                                    <i class="fas fa-times"></i>
                                </button>
                            </span>
                        @endif

                        @if(request('sort') && request('sort') != 'recent')
                            @php
                                $sortLabels = [
                                    'oldest' => '🕑 Más Antiguos',
                                    'student_name' => '👤 Por Estudiante',
                                    'course_name' => '📚 Por Curso'
                                ];
                            @endphp
                            <span class="filter-badge">
                                {{ $sortLabels[request('sort')] ?? 'Ordenar' }}
                                <button type="button" onclick="removeFilter('sort')" class="remove-filter">
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
            @if(request()->hasAny(['search', 'periodo_id', 'estado']))
                <div class="results-counter mb-3">
                    <i class="fas fa-list-ol me-2"></i>
                    Se encontraron <strong>{{ $inscripciones->total() }}</strong> inscripciones
                </div>
            @endif

            <div class="table-responsive">
                <table class="table-dark">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Estudiante</th>
                            <th>Curso/Sección</th>
                            <th>Período</th>
                            <th>Nota Final</th>
                            <th>Estado</th>
                            <th>Fecha Insc.</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
    @forelse($inscripciones as $inscripcion)
        <tr>
            <td>
                <span class="badge-neon">#{{ $inscripcion->id }}</span>
            </td>
            <td>
                <div class="course-info">
                    <div class="course-icon bg-gradient-info">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        @if($inscripcion->estudiante)
                            <strong>{{ $inscripcion->estudiante->nombre_completo }}</strong>
                            <br><small class="text-muted">{{ $inscripcion->estudiante->email }}</small>
                        @else
                            <strong class="text-danger">Estudiante no disponible</strong>
                        @endif
                    </div>
                </div>
            </td>
            <td>
                @if($inscripcion->seccion && $inscripcion->seccion->curso)
                    <strong>{{ $inscripcion->seccion->curso->nombre }}</strong>
                    <br><span class="badge-info">{{ $inscripcion->seccion->codigo_seccion }}</span>
                @else
                    <span class="badge-danger">
                        <i class="fas fa-exclamation-triangle me-1"></i>Sección no disponible
                    </span>
                @endif
            </td>
            <td>
                @if($inscripcion->seccion && $inscripcion->seccion->periodo)
                    <span class="badge-success">{{ $inscripcion->seccion->periodo->nombre_corto }}</span>
                @else
                    <span class="text-muted">N/A</span>
                @endif
            </td>
            <td>
                @if($inscripcion->nota_final !== null)
                    <span class="nota-badge {{ $inscripcion->esta_aprobado ? 'aprobado' : 'reprobado' }}">
                        {{ number_format($inscripcion->nota_final, 2) }}
                    </span>
                @else
                    <span class="text-muted">N/A</span>
                @endif
            </td>
            <td>
                @php
                    $estadoClase = [
                        'inscrito' => 'status-active',
                        'completado' => 'status-info',
                        'retirado' => 'status-inactive',
                    ][$inscripcion->estado] ?? 'status-inactive';
                @endphp
                <span class="status-badge {{ $estadoClase }}">
                    <i class="fas fa-circle"></i> {{ ucfirst($inscripcion->estado) }}
                </span>
            </td>
            <td>
                {{ $inscripcion->fecha_inscripcion ? $inscripcion->fecha_inscripcion->format('d/m/Y') : 'N/A' }}
            </td>
            <td>
    <div class="action-buttons">
        <a href="{{ route('admin.inscripciones.show', $inscripcion) }}" 
           class="btn-action btn-action-info"
           title="Ver detalles">
            <i class="fas fa-eye"></i>
        </a>
        <a href="{{ route('admin.inscripciones.edit', $inscripcion) }}" 
           class="btn-action btn-action-warning"
           title="Editar">
            <i class="fas fa-edit"></i>
        </a>
        <button type="button" 
                class="btn-action btn-action-danger"
                onclick="confirmarEliminacion({{ $inscripcion->id }})"
                title="Eliminar">
            <i class="fas fa-trash"></i>
        </button>
    </div>
    
    <form id="delete-form-{{ $inscripcion->id }}" 
          action="{{ route('admin.inscripciones.destroy', $inscripcion) }}" 
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
                    <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                    <h5>No se encontraron inscripciones</h5>
                    <p>{{ request()->hasAny(['search', 'periodo_id', 'estado']) ? 'Intenta ajustar los filtros de búsqueda' : 'Crea una nueva inscripción para comenzar' }}</p>
                    @if(!request()->hasAny(['search', 'periodo_id', 'estado']))
                        <a href="{{ route('admin.inscripciones.create') }}" class="btn-neon mt-3">
                            <i class="fas fa-plus me-2"></i>Crear Inscripción
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

            <!-- Paginación personalizada -->
            @if($inscripciones->hasPages())
            <div class="pagination-dark mt-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="pagination-info">
                        Mostrando {{ $inscripciones->firstItem() }} - {{ $inscripciones->lastItem() }} de {{ $inscripciones->total() }} resultados
                    </div>
                    <div class="pagination-links">
                        @if ($inscripciones->onFirstPage())
                            <span class="page-link disabled">
                                <i class="fas fa-chevron-left"></i>
                            </span>
                        @else
                            <a href="{{ $inscripciones->previousPageUrl() }}" class="page-link">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        @endif

                        @foreach ($inscripciones->getUrlRange(1, $inscripciones->lastPage()) as $page => $url)
                            @if ($page == $inscripciones->currentPage())
                                <span class="page-link active">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if ($inscripciones->hasMorePages())
                            <a href="{{ $inscripciones->nextPageUrl() }}" class="page-link">
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
/* Estilos completos para Inscripciones */
.badge-danger {
    background: rgba(239, 68, 68, 0.2);
    color: #ef4444;
    border: 1px solid #ef4444;
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
}

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
    color: var(--muted-text);
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
    background: rgba(0, 212, 255, 0.1);
    border-left: 3px solid var(--neon-cyan);
    padding: 0.75rem 1rem;
    border-radius: 5px;
    color: var(--text-color);
    font-size: 0.9rem;
}

.results-counter strong {
    color: var(--neon-cyan);
}

/* Alertas */
.alert-success-dark {
    background-color: rgba(16, 185, 129, 0.2);
    border: 1px solid #10b981;
    color: #10b981;
    padding: 1rem;
    border-radius: 8px;
}

/* Botones Neon */
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

.btn-neon:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 0 30px rgba(0, 212, 255, 0.6);
    color: #0f172a;
}

.btn-neon:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
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

/* Tablas Oscuras */
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
    background: rgba(100, 116, 139, 0.2);
    color: #64748b;
    border: 1px solid #64748b;
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

.badge-success {
    background: rgba(16, 185, 129, 0.2);
    color: #10b981;
    border: 1px solid #10b981;
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.nota-badge {
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    font-weight: 700;
    min-width: 65px;
    text-align: center;
    display: inline-block;
    transition: all 0.3s ease;
}

.nota-badge.aprobado {
    background: #10b981;
    color: #0f172a;
    box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
}

.nota-badge.reprobado {
    background: #ef4444;
    color: white;
    box-shadow: 0 0 10px rgba(239, 68, 68, 0.5);
}

/* Action Buttons - Diseño Estandarizado */
.action-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: nowrap;
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
    text-decoration: none;
    flex-shrink: 0;
}

.btn-action i {
    font-size: 0.9rem;
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

/* Responsive */
@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
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
    
    .btn-action {
        width: 32px;
        height: 32px;
    }
    
    .btn-action i {
        font-size: 0.85rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
    function confirmarEliminacion(id) {
        if (confirm('¿Estás seguro de que deseas eliminar esta inscripción? Esta acción es irreversible y afectará a las calificaciones y asistencias.')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }

    // VALIDACIÓN DE BÚSQUEDA DE INSCRIPCIONES
    function validarBusquedaInscripciones(input) {
        const valor = input.value.trim();
        const errorDiv = document.getElementById('search-error-inscripciones');
        const successDiv = document.getElementById('search-success-inscripciones');
        const btnBuscar = document.getElementById('btnBuscarInscripciones');
        
        // Permite letras, números, espacios, guiones, puntos y caracteres especiales comunes
        // Ideal para buscar: "Juan Pérez", "MAT-101-A", "#123", "Matemáticas I"
        const regex = /^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-#\.]*$/;
        
        if (valor === '') {
            // Campo vacío - estado neutral
            input.classList.remove('error', 'success');
            errorDiv.style.display = 'none';
            successDiv.style.display = 'none';
            btnBuscar.disabled = false;
            return;
        }
        
        if (!regex.test(valor)) {
            // Texto inválido
            input.classList.add('error');
            input.classList.remove('success');
            errorDiv.style.display = 'block';
            successDiv.style.display = 'none';
            btnBuscar.disabled = true;
        } else {
            // Texto válido
            input.classList.remove('error');
            input.classList.add('success');
            errorDiv.style.display = 'none';
            successDiv.style.display = 'block';
            btnBuscar.disabled = false;
            
            // Auto-ocultar mensaje de éxito después de 2 segundos
            setTimeout(() => {
                successDiv.style.display = 'none';
            }, 2000);
        }
    }

    // Validar al enviar el formulario
    document.getElementById('filterForm').addEventListener('submit', function(e) {
        const input = document.getElementById('mainSearch');
        const valor = input.value.trim();
        const regex = /^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-#\.]*$/;
        
        if (valor !== '' && !regex.test(valor)) {
            e.preventDefault();
            input.focus();
            validarBusquedaInscripciones(input);
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
        window.location.href = '{{ route('admin.inscripciones.index') }}';
    }

    window.addEventListener('DOMContentLoaded', function() {
        const hasAdvancedFilters = {{ request()->hasAny(['periodo_id', 'estado', 'sort']) ? 'true' : 'false' }};
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