@extends('layouts.app')

@section('title', 'Gestión de Calificaciones')
@section('page-title', 'Calificaciones')

@section('content')
<div class="container-fluid">
    <!-- Mensajes de éxito/info/warning -->
    @if(session('success'))
        <div class="alert alert-success-neon mb-4 animate-slide-down">
            <i class="fas fa-check-circle me-2"></i>
            <span>{!! session('success') !!}</span>
            <button type="button" class="btn-close-neon" onclick="this.parentElement.style.display='none'">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info-neon mb-4 animate-slide-down">
            <i class="fas fa-info-circle me-2"></i>
            <span>{!! session('info') !!}</span>
            <button type="button" class="btn-close-neon" onclick="this.parentElement.style.display='none'">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning-neon mb-4 animate-slide-down">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <span>{!! session('warning') !!}</span>
            <button type="button" class="btn-close-neon" onclick="this.parentElement.style.display='none'">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger-neon mb-4 animate-slide-down">
            <i class="fas fa-exclamation-circle me-2"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close-neon" onclick="this.parentElement.style.display='none'">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- Header con Estadísticas -->
    <div class="stats-grid mb-4">
        <div class="stat-card">
            <div class="stat-icon bg-gradient-primary">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['total'] }}</h3>
                <p>Total Calificaciones</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['calificadas'] }}</h3>
                <p>Calificadas</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['pendientes'] }}</h3>
                <p>Pendientes</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-info">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['promedio_general'] }}</h3>
                <p>Promedio General</p>
            </div>
        </div>
    </div>

    <!-- Card Principal -->
    <div class="card-dark">
        <div class="card-header-dark">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h5 class="mb-0">
                    <i class="fas fa-clipboard-list me-2"></i>Listado de Calificaciones
                </h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.calificaciones.masiva') }}" class="btn-outline-neon">
                        <i class="fas fa-users me-2"></i>Calificación Masiva
                    </a>
                    <a href="{{ route('admin.calificaciones.reporte') }}" class="btn-outline-neon">
                        <i class="fas fa-chart-bar me-2"></i>Reportes
                    </a>
                    <a href="{{ route('admin.calificaciones.create') }}" class="btn-neon">
                        <i class="fas fa-plus me-2"></i>Nueva Calificación
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body-dark">
            <!-- Filtros CON VALIDACIÓN -->
            <form method="GET" action="{{ route('admin.calificaciones.index') }}" class="filter-form mb-4" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="input-group-dark">
                            <span class="input-icon">
                                <i class="fas fa-user"></i>
                            </span>
                            <input type="text" 
                                   name="estudiante" 
                                   id="searchEstudiante"
                                   class="form-control-dark" 
                                   placeholder="Buscar estudiante..."
                                   value="{{ request('estudiante') }}"
                                   oninput="validarBusqueda(this)"
                                   maxlength="50">
                            <div id="search-error" class="search-error-message" style="display: none;">
                                <i class="fas fa-exclamation-circle me-1"></i>
                                Solo se permiten letras. Por favor, ingrese un nombre válido.
                            </div>
                            <div id="search-success" class="search-success-message" style="display: none;">
                                <i class="fas fa-check-circle me-1"></i>
                                Búsqueda válida
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <select name="evaluacion_id" class="form-select-dark">
                            <option value="">Todas las evaluaciones</option>
                            @foreach($evaluaciones as $evaluacion)
                                <option value="{{ $evaluacion->id }}" {{ request('evaluacion_id') == $evaluacion->id ? 'selected' : '' }}>
                                    {{ $evaluacion->seccion->curso->nombre }} - {{ $evaluacion->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <select name="estado" class="form-select-dark">
                            <option value="">Todos los estados</option>
                            <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendientes</option>
                            <option value="calificada" {{ request('estado') == 'calificada' ? 'selected' : '' }}>Calificadas</option>
                            <option value="revisada" {{ request('estado') == 'revisada' ? 'selected' : '' }}>Revisadas</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <input type="number" 
                               name="nota_min" 
                               class="form-control-dark" 
                               placeholder="Nota mínima"
                               value="{{ request('nota_min') }}"
                               min="0"
                               max="100"
                               step="0.01">
                    </div>

                    <div class="col-md-2">
                        <input type="number" 
                               name="nota_max" 
                               class="form-control-dark" 
                               placeholder="Nota máxima"
                               value="{{ request('nota_max') }}"
                               min="0"
                               max="100"
                               step="0.01">
                    </div>
                    
                    <div class="col-md-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn-neon" id="btnBuscar">
                                <i class="fas fa-filter me-2"></i>Filtrar
                            </button>
                            <a href="{{ route('admin.calificaciones.index') }}" class="btn-outline-neon">
                                <i class="fas fa-times me-2"></i>Limpiar
                            </a>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Tabla -->
            <div class="table-responsive">
                <table class="table-dark">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Evaluación</th>
                            <th>Curso</th>
                            <th>Nota</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($calificaciones as $calificacion)
                            <tr>
                                <td>
                                    <div class="student-info">
                                        <div class="student-avatar">
                                            <i class="fas fa-user-graduate"></i>
                                        </div>
                                        <div>
                                            <strong>{{ $calificacion->estudiante->nombre_completo }}</strong>
                                            <br><small class="text-muted">{{ $calificacion->estudiante->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="evaluation-info">
                                        <strong>{{ $calificacion->evaluacion->nombre }}</strong>
                                        <br><small class="text-muted">{{ $calificacion->evaluacion->tipoEvaluacion->nombre ?? 'N/A' }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-info">
                                        <i class="fas fa-book me-1"></i>
                                        {{ $calificacion->evaluacion->seccion->curso->nombre }}
                                    </span>
                                </td>
                                <td>
                                    <div class="grade-display">
                                        <span class="grade-number {{ $calificacion->esta_aprobada ? 'grade-pass' : 'grade-fail' }}">
                                            {{ $calificacion->nota_formateada }}
                                        </span>
                                        <small class="grade-status">
                                            {{ $calificacion->estado_nota }}
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    @if($calificacion->estado === 'pendiente')
                                        <span class="status-badge status-pending">
                                            <i class="fas fa-clock"></i> Pendiente
                                        </span>
                                    @elseif($calificacion->estado === 'calificada')
                                        <span class="status-badge status-graded">
                                            <i class="fas fa-check"></i> Calificada
                                        </span>
                                    @else
                                        <span class="status-badge status-reviewed">
                                            <i class="fas fa-check-double"></i> Revisada
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="date-info">
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ $calificacion->fecha_calificacion->format('d/m/Y') }}
                                        <br><small class="text-muted">{{ $calificacion->fecha_calificacion->format('H:i') }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.calificaciones.show', $calificacion) }}" 
                                           class="btn-action btn-action-info"
                                           title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.calificaciones.edit', $calificacion) }}" 
                                           class="btn-action btn-action-warning"
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn-action btn-action-danger"
                                                onclick="confirmarEliminacion({{ $calificacion->id }})"
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <form id="delete-form-{{ $calificacion->id }}" 
                                          action="{{ route('admin.calificaciones.destroy', $calificacion) }}" 
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
                                        <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                                        <h5>No se encontraron calificaciones</h5>
                                        <p>Intenta ajustar los filtros o crea una nueva calificación</p>
                                        <a href="{{ route('admin.calificaciones.create') }}" class="btn-neon mt-3">
                                            <i class="fas fa-plus me-2"></i>Nueva Calificación
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($calificaciones->hasPages())
            <div class="pagination-dark mt-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="pagination-info">
                        Mostrando {{ $calificaciones->firstItem() }} - {{ $calificaciones->lastItem() }} de {{ $calificaciones->total() }} resultados
                    </div>
                    <div class="pagination-links">
                        @if ($calificaciones->onFirstPage())
                            <span class="page-link disabled">
                                <i class="fas fa-chevron-left"></i>
                            </span>
                        @else
                            <a href="{{ $calificaciones->previousPageUrl() }}" class="page-link">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        @endif

                        @foreach ($calificaciones->getUrlRange(1, $calificaciones->lastPage()) as $page => $url)
                            @if ($page == $calificaciones->currentPage())
                                <span class="page-link active">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if ($calificaciones->hasMorePages())
                            <a href="{{ $calificaciones->nextPageUrl() }}" class="page-link">
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
    /* Alertas Neon */
    .alert {
        position: relative;
        padding: 1rem 3rem 1rem 1.5rem;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 500;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    }

    .alert-success-neon {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.2) 0%, rgba(16, 185, 129, 0.1) 100%);
        border: 2px solid #10b981;
        color: #10b981;
    }

    .alert-info-neon {
        background: linear-gradient(135deg, rgba(6, 182, 212, 0.2) 0%, rgba(6, 182, 212, 0.1) 100%);
        border: 2px solid #06b6d4;
        color: #06b6d4;
    }

    .alert-warning-neon {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.2) 0%, rgba(245, 158, 11, 0.1) 100%);
        border: 2px solid #f59e0b;
        color: #f59e0b;
    }

    .alert-danger-neon {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.2) 0%, rgba(239, 68, 68, 0.1) 100%);
        border: 2px solid #ef4444;
        color: #ef4444;
    }

    .btn-close-neon {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        background: transparent;
        border: none;
        color: inherit;
        font-size: 1.2rem;
        cursor: pointer;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .btn-close-neon:hover {
        background: rgba(255, 255, 255, 0.1);
        transform: translateY(-50%) scale(1.1);
    }

    .alert ul {
        list-style: none;
        padding-left: 0;
    }

    .alert ul li {
        padding: 0.25rem 0;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-slide-down {
        animation: slideDown 0.4s ease-out;
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

    /* Student Info */
    .student-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .student-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: #0f172a;
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.5);
    }

    .evaluation-info strong {
        color: #e2e8f0;
    }

    /* Grade Display */
    .grade-display {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .grade-number {
        font-size: 1.5rem;
        font-weight: 700;
        padding: 0.5rem 1rem;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
    }

    .grade-pass {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 2px solid #10b981;
    }

    .grade-fail {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 2px solid #ef4444;
    }

    .grade-status {
        margin-top: 0.25rem;
        color: #94a3b8;
        font-size: 0.75rem;
    }

    /* Badges */
    .badge-info {
        background: rgba(6, 182, 212, 0.2);
        color: #06b6d4;
        border: 1px solid #06b6d4;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
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

    .status-pending {
        background: rgba(245, 158, 11, 0.2);
        color: #f59e0b;
        border: 1px solid #f59e0b;
    }

    .status-graded {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 1px solid #10b981;
    }

    .status-reviewed {
        background: rgba(6, 182, 212, 0.2);
        color: #06b6d4;
        border: 1px solid #06b6d4;
    }

    .status-badge i {
        font-size: 0.75rem;
    }

    /* Date Info */
    .date-info {
        color: #94a3b8;
        font-size: 0.875rem;
    }

    .date-info i {
        color: var(--neon-cyan);
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

    .text-muted {
        color: #94a3b8 !important;
    }

    /* Paginación Mejorada - Igual que inscripciones */
    .pagination-dark {
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
        padding: 1rem 1.5rem;
    }

    .pagination-info {
        color: #94a3b8;
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
        color: #94a3b8;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .card-header-dark {
            flex-direction: column;
            align-items: flex-start !important;
        }

        .card-header-dark .d-flex {
            flex-direction: column;
            width: 100%;
        }

        .btn-neon, .btn-outline-neon {
            width: 100%;
            justify-content: center;
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
    }
</style>
@endpush

@push('scripts')
<script>
    function confirmarEliminacion(id) {
        if (confirm('¿Estás seguro de que deseas eliminar esta calificación? Esta acción no se puede deshacer y afectará la nota final del estudiante.')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }

    // VALIDACIÓN DE BÚSQUEDA DE ESTUDIANTE
    function validarBusqueda(input) {
        const valor = input.value.trim();
        const errorDiv = document.getElementById('search-error');
        const successDiv = document.getElementById('search-success');
        const btnBuscar = document.getElementById('btnBuscar');
        
        // Solo letras, espacios, acentos y ñ
        const regex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]*$/;
        
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
        const input = document.getElementById('searchEstudiante');
        const valor = input.value.trim();
        const regex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]*$/;
        
        if (valor !== '' && !regex.test(valor)) {
            e.preventDefault();
            input.focus();
            validarBusqueda(input);
        }
    });

    // Auto-ocultar alertas después de 5 segundos
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 500);
            }, 5000); // 5 segundos
        });
    });
</script>
@endpush
@endsection