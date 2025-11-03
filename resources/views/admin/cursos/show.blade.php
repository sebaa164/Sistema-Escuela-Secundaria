@extends('layouts.app')

@section('title', 'Detalle del Curso')
@section('page-title', 'Detalle del Curso')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Breadcrumb -->

            <!-- Card Principal -->
            <div class="card-dark mb-4">
                <div class="card-header-dark">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h5 class="mb-1">
                                <i class="fas fa-book me-2"></i>{{ $curso->nombre }}
                            </h5>
                            <p class="mb-0 text-muted">Código: {{ $curso->codigo_curso }}</p>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            @if($curso->estado === 'activo')
                                <span class="status-badge status-active">
                                    <i class="fas fa-circle"></i> Activo
                                </span>
                            @else
                                <span class="status-badge status-inactive">
                                    <i class="fas fa-circle"></i> Inactivo
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-body-dark">
                    <div class="row g-4">
                        <!-- Información Básica -->
                        <div class="col-md-6">
                            <div class="info-section">
                                <div class="info-header">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <h6>Información Básica</h6>
                                </div>
                                <div class="info-content">
                                    <div class="detail-item">
                                        <span class="detail-label">
                                            <i class="fas fa-barcode me-2"></i>Código:
                                        </span>
                                        <span class="detail-value">{{ $curso->codigo_curso }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">
                                            <i class="fas fa-book me-2"></i>Nombre:
                                        </span>
                                        <span class="detail-value">{{ $curso->nombre }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">
                                            <i class="fas fa-align-left me-2"></i>Descripción:
                                        </span>
                                        <span class="detail-value">{{ $curso->descripcion ?: 'Sin descripción' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Detalles Académicos -->
                        <div class="col-md-6">
                            <div class="info-section">
                                <div class="info-header">
                                    <i class="fas fa-graduation-cap me-2"></i>
                                    <h6>Detalles Académicos</h6>
                                </div>
                                <div class="info-content">
                                    <div class="detail-item">
                                        <span class="detail-label">
                                            <i class="fas fa-university me-2"></i>Carrera:
                                        </span>
                                        <span class="detail-value">{{ $curso->carrera }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">
                                            <i class="fas fa-layer-group me-2"></i>Nivel:
                                        </span>
                                        <span class="detail-value">{{ $curso->nivel ?: 'No especificado' }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">
                                            <i class="fas fa-clock me-2"></i>Horas Semanales:
                                        </span>
                                        <span class="detail-value">{{ $curso->horas_semanales }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Requisitos -->
                        @if($curso->requisitos)
                        <div class="col-12">
                            <div class="info-section">
                                <div class="info-header">
                                    <i class="fas fa-list-ul me-2"></i>
                                    <h6>Requisitos</h6>
                                </div>
                                <div class="info-content">
                                    <p class="mb-0">{{ $curso->requisitos }}</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Estadísticas -->
                        <div class="col-12">
                            <div class="info-section">
                                <div class="info-header">
                                    <i class="fas fa-chart-bar me-2"></i>
                                    <h6>Estadísticas</h6>
                                </div>
                                <div class="info-content">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="stat-box">
                                                <i class="fas fa-layer-group"></i>
                                                <h4>{{ $totalSecciones }}</h4>
                                                <p>Total Secciones</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="stat-box">
                                                <i class="fas fa-check-circle"></i>
                                                <h4>{{ $seccionesActivas }}</h4>
                                                <p>Secciones Activas</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="stat-box">
                                                <i class="fas fa-user-graduate"></i>
                                                <h4>{{ $totalEstudiantes }}</h4>
                                                <p>Estudiantes Inscritos</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="stat-box">
                                                <i class="fas fa-calendar"></i>
                                                <h4>{{ $curso->created_at->format('Y') }}</h4>
                                                <p>Año de Creación</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Secciones del Curso -->
            @if($curso->secciones->count() > 0)
            <div class="card-dark mb-4">
                <div class="card-header-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>Secciones del Curso
                    </h5>
                </div>
                <div class="card-body-dark">
                    <div class="table-responsive">
                        <table class="table-dark">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Período</th>
                                    <th>Profesor</th>
                                    <th>Horario</th>
                                    <th>Inscritos</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($curso->secciones as $seccion)
                                <tr>
                                    <td><span class="badge-neon">{{ $seccion->codigo_seccion }}</span></td>
                                    <td>{{ $seccion->periodo->nombre ?? 'N/A' }}</td>
                                    <td>{{ $seccion->profesor->nombre_completo ?? 'Sin asignar' }}</td>
                                    <td>
                                        @if($seccion->horarios && $seccion->horarios->count() > 0)
                                            {{ $seccion->horarios->first()->dia_semana }}
                                        @else
                                            <span class="text-muted">Sin horario</span>
                                        @endif
                                    </td>
                                    <td>{{ $seccion->inscripciones->where('estado', 'inscrito')->count() }}/{{ $seccion->cupo_maximo }}</td>
                                    <td>
                                        @if($seccion->estado === 'activo')
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
                                        <a href="{{ route('admin.secciones.show', $seccion) }}" 
                                           class="btn-action btn-action-info"
                                           title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Botones de Acción -->
            <div class="card-dark">
                <div class="card-body-dark">
                    <div class="action-buttons-large">
                        <a href="{{ route('admin.cursos.edit', $curso) }}" class="btn-neon-lg">
                            <i class="fas fa-edit me-2"></i>Editar Curso
                        </a>
                        <a href="{{ route('admin.cursos.index') }}" class="btn-outline-neon">
                            <i class="fas fa-arrow-left me-2"></i>Volver al Listado
                        </a>
                        @if($curso->secciones->count() === 0)
                            <button type="button" 
                                    class="btn-danger-neon"
                                    onclick="confirmarEliminacion()">
                                <i class="fas fa-trash me-2"></i>Eliminar Curso
                            </button>
                        @endif
                    </div>

                    @if($curso->secciones->count() === 0)
                        <form id="delete-form" 
                              action="{{ route('admin.cursos.destroy', $curso) }}" 
                              method="POST" 
                              style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endif
                </div>
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

    .breadcrumb-dark .breadcrumb-item {
        color: #94a3b8;
    }

    .breadcrumb-dark .breadcrumb-item a {
        color: var(--neon-cyan);
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .breadcrumb-dark .breadcrumb-item a:hover {
        color: var(--neon-blue);
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .breadcrumb-dark .breadcrumb-item.active {
        color: #e2e8f0;
    }

    .breadcrumb-dark .breadcrumb-item + .breadcrumb-item::before {
        content: "›";
        color: rgba(0, 212, 255, 0.5);
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

    .text-muted {
        color: #94a3b8 !important;
    }

    /* Status Badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
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
        font-size: 0.75rem;
        animation: pulse 2s infinite;
    }

    /* Info Sections */
    .info-section {
        background: rgba(0, 212, 255, 0.03);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
        overflow: hidden;
        height: 100%;
    }

    .info-header {
        display: flex;
        align-items: center;
        background: rgba(0, 212, 255, 0.1);
        padding: 1rem 1.25rem;
        border-bottom: 1px solid rgba(0, 212, 255, 0.2);
    }

    .info-header i {
        color: var(--neon-cyan);
        font-size: 1.25rem;
    }

    .info-header h6 {
        margin: 0;
        color: #e2e8f0;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.875rem;
    }

    .info-content {
        padding: 1.25rem;
        color: #e2e8f0;
    }

    /* Detail Items */
    .detail-item {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(0, 212, 255, 0.1);
    }

    .detail-item:last-child {
        border-bottom: none;
    }

    .detail-label {
        color: #94a3b8;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        min-width: 150px;
    }

    .detail-label i {
        color: var(--neon-cyan);
    }

    .detail-value {
        color: #e2e8f0;
        font-weight: 500;
        text-align: right;
        flex: 1;
    }

    /* Stat Box */
    .stat-box {
        background: rgba(0, 212, 255, 0.05);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.3s ease;
    }

    .stat-box:hover {
        background: rgba(0, 212, 255, 0.1);
        border-color: var(--neon-cyan);
        transform: translateY(-5px);
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
    }

    .stat-box i {
        font-size: 2rem;
        color: var(--neon-cyan);
        margin-bottom: 0.75rem;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .stat-box h4 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--neon-cyan);
        margin: 0.5rem 0;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .stat-box p {
        color: #94a3b8;
        margin: 0;
        font-size: 0.875rem;
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

    .badge-neon {
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        color: #0f172a;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.75rem;
        box-shadow: 0 0 10px rgba(0, 212, 255, 0.3);
    }

    /* Action Buttons */
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

    /* Action Buttons Large */
    .action-buttons-large {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn-neon-lg {
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        border: none;
        color: #0f172a;
        font-weight: 600;
        padding: 1rem 2.5rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
        font-size: 1rem;
        text-decoration: none;
        display: inline-block;
    }

    .btn-neon-lg:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 30px rgba(0, 212, 255, 0.6);
        color: #0f172a;
    }

    .btn-outline-neon {
        background: transparent;
        border: 2px solid var(--neon-cyan);
        color: var(--neon-cyan);
        font-weight: 600;
        padding: 1rem 2.5rem;
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

    .btn-danger-neon {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        border: none;
        color: white;
        font-weight: 600;
        padding: 1rem 2.5rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        box-shadow: 0 0 20px rgba(239, 68, 68, 0.3);
        font-size: 1rem;
        cursor: pointer;
    }

    .btn-danger-neon:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 30px rgba(239, 68, 68, 0.6);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .action-buttons-large {
            flex-direction: column;
        }

        .btn-neon-lg,
        .btn-outline-neon,
        .btn-danger-neon {
            width: 100%;
        }

        .detail-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .detail-value {
            text-align: left;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function confirmarEliminacion() {
        if (confirm('¿Estás seguro de que deseas eliminar este curso? Esta acción no se puede deshacer.')) {
            document.getElementById('delete-form').submit();
        }
    }
</script>
@endpush
@endsection