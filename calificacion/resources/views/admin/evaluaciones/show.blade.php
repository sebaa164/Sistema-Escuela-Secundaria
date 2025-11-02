@extends('layouts.app')

@section('title', 'Detalle de Evaluación')
@section('page-title', 'Detalle de Evaluación')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Card Principal -->
            <div class="card-dark mb-4">
                <div class="card-header-dark">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <h5 class="mb-1">
                                <i class="fas fa-clipboard-list me-2"></i>{{ $evaluacion->nombre }}
                            </h5>
                            <p class="mb-0 text-muted">
                                @if($evaluacion->seccion)
                                    {{ $evaluacion->seccion->curso->codigo_curso }} - {{ $evaluacion->seccion->curso->nombre }} | Sección: {{ $evaluacion->seccion->nombre }}
                                @else
                                    Sin sección asignada
                                @endif
                            </p>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            @if($evaluacion->estado === 'programada')
                                <span class="status-badge status-pending">
                                    <i class="fas fa-clock"></i> Programada
                                </span>
                            @elseif($evaluacion->estado === 'activa')
                                <span class="status-badge status-active">
                                    <i class="fas fa-play-circle"></i> En Curso
                                </span>
                            @elseif($evaluacion->estado === 'finalizada')
                                <span class="status-badge status-graded">
                                    <i class="fas fa-check-circle"></i> Finalizada
                                </span>
                            @else
                                <span class="status-badge status-danger">
                                    <i class="fas fa-times-circle"></i> Cancelada
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-body-dark">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="info-section">
                                <div class="info-header">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <h6>Información Básica</h6>
                                </div>
                                <div class="info-content">
                                    <div class="detail-item">
                                        <span class="detail-label">
                                            <i class="fas fa-heading me-2"></i>Nombre:
                                        </span>
                                        <span class="detail-value">{{ $evaluacion->nombre }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">
                                            <i class="fas fa-tag me-2"></i>Tipo:
                                        </span>
                                        <span class="detail-value">{{ optional($evaluacion->tipoEvaluacion)->nombre ?? 'Sin tipo' }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">
                                            <i class="fas fa-calendar-alt me-2"></i>Fecha:
                                        </span>
                                        <span class="detail-value">{{ $evaluacion->fecha_evaluacion->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">
                                            <i class="fas fa-trophy me-2"></i>Nota Máxima:
                                        </span>
                                        <span class="detail-value">{{ number_format($evaluacion->nota_maxima, 2) }}</span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">
                                            <i class="fas fa-percentage me-2"></i>Peso:
                                        </span>
                                        <span class="detail-value">{{ $evaluacion->porcentaje !== null ? ($evaluacion->porcentaje . '%') : 'Sin peso' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-section">
                                <div class="info-header">
                                    <i class="fas fa-book me-2"></i>
                                    <h6>Información del Curso</h6>
                                </div>
                                <div class="info-content">
                                    @if($evaluacion->seccion)
                                        <div class="detail-item">
                                            <span class="detail-label">
                                                <i class="fas fa-book me-2"></i>Curso:
                                            </span>
                                            <span class="detail-value">{{ $evaluacion->seccion->curso->codigo_curso }} - {{ $evaluacion->seccion->curso->nombre }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">
                                                <i class="fas fa-layer-group me-2"></i>Sección:
                                            </span>
                                            <span class="detail-value">{{ $evaluacion->seccion->nombre }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">
                                                <i class="fas fa-chalkboard-teacher me-2"></i>Profesor:
                                            </span>
                                            <span class="detail-value">{{ $evaluacion->seccion->profesor->nombre_completo }}</span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">
                                                <i class="fas fa-calendar me-2"></i>Período:
                                            </span>
                                            <span class="detail-value">{{ $evaluacion->seccion->periodo->nombre }}</span>
                                        </div>
                                    @else
                                        <p class="mb-0 text-muted">Sin sección asignada</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($evaluacion->descripcion)
                        <div class="col-12">
                            <div class="info-section">
                                <div class="info-header">
                                    <i class="fas fa-align-left me-2"></i>
                                    <h6>Descripción</h6>
                                </div>
                                <div class="info-content">
                                    <p class="mb-0">{{ $evaluacion->descripcion }}</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="col-12">
                            <div class="info-section">
                                <div class="info-header">
                                    <i class="fas fa-chart-bar me-2"></i>
                                    <h6>Estadísticas de Calificaciones</h6>
                                </div>
                                <div class="info-content">
                                    <div class="row g-3">
                                        <div class="col-6 col-md-3">
                                            <div class="stat-box">
                                                <i class="fas fa-users"></i>
                                                <h4>{{ $estadisticas['total_estudiantes'] }}</h4>
                                                <p>Total Estudiantes</p>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="stat-box">
                                                <i class="fas fa-check-circle"></i>
                                                <h4>{{ $estadisticas['calificados'] }}</h4>
                                                <p>Calificados</p>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="stat-box">
                                                <i class="fas fa-clock"></i>
                                                <h4>{{ $estadisticas['pendientes'] }}</h4>
                                                <p>Pendientes</p>
                                            </div>
                                        </div>
                                        <div class="col-6 col-md-3">
                                            <div class="stat-box">
                                                <i class="fas fa-star"></i>
                                                <h4>{{ $estadisticas['promedio'] ? number_format($estadisticas['promedio'], 1) : 'N/A' }}</h4>
                                                <p>Promedio</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Calificaciones de Estudiantes -->
            @if($evaluacion->calificaciones->count() > 0)
            <div class="card-dark mb-4">
                <div class="card-header-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>Calificaciones Registradas
                    </h5>
                </div>
                <div class="card-body-dark">
                    <div class="table-responsive">
                        <table class="table-dark">
                            <thead>
                                <tr>
                                    <th>Estudiante</th>
                                    <th>Nota</th>
                                    <th>Estado</th>
                                    <th>Fecha Registro</th>
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($evaluacion->calificaciones as $calificacion)
                                <tr>
                                    <td>
                                        <div class="user-info">
                                            <div class="user-avatar">
                                                {{ substr($calificacion->estudiante->nombre, 0, 1) }}{{ substr($calificacion->estudiante->apellido, 0, 1) }}
                                            </div>
                                            <div>
                                                <strong>{{ $calificacion->estudiante->nombre }} {{ $calificacion->estudiante->apellido }}</strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="nota-badge">
                                            {{ number_format($calificacion->nota, 1) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($calificacion->nota >= 70)
                                            <span class="status-badge status-active">
                                                <i class="fas fa-check-circle"></i> Aprobado
                                            </span>
                                        @else
                                            <span class="status-badge status-danger">
                                                <i class="fas fa-times-circle"></i> Reprobado
                                            </span>
                                        @endif
                                    </td>
                                    <td>{{ $calificacion->created_at->format('d/m/Y') }}</td>
                                    <td>{{ $calificacion->observaciones ?: 'Sin observaciones' }}</td>
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
                        <a href="{{ route('admin.calificaciones.evaluacion', $evaluacion) }}" class="btn-neon-lg">
                            <i class="fas fa-edit me-2"></i>Gestionar Calificaciones
                        </a>
                        <a href="{{ route('admin.evaluaciones.edit', ['evaluacione' => $evaluacion->id]) }}" class="btn-outline-neon">
                            <i class="fas fa-pen me-2"></i>Editar Evaluación
                        </a>
                        <a href="{{ route('admin.evaluaciones.index') }}" class="btn-outline-neon">
                            <i class="fas fa-arrow-left me-2"></i>Volver al Listado
                        </a>
                        @if($evaluacion->calificaciones->count() === 0)
                            <button type="button" 
                                    class="btn-danger-neon"
                                    onclick="confirmarEliminacion()">
                                <i class="fas fa-trash me-2"></i>Eliminar Evaluación
                            </button>
                        @endif
                    </div>

                    @if($evaluacion->calificaciones->count() === 0)
                        <form id="delete-form" 
                              action="{{ route('admin.evaluaciones.destroy', ['evaluacione' => $evaluacion->id]) }}" 
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
    :root {
        --neon-cyan: #00d4ff;
        --neon-blue: #0ea5e9;
        --dark-bg: #0f172a;
        --dark-card: #1e293b;
        --text-color: #e2e8f0;
        --muted-text: #94a3b8;
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
        color: var(--muted-text) !important;
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

    .status-pending {
        background: rgba(245, 158, 11, 0.2);
        color: #f59e0b;
        border: 1px solid #f59e0b;
    }

    .status-active {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 1px solid #10b981;
    }

    .status-graded {
        background: rgba(6, 182, 212, 0.2);
        color: #06b6d4;
        border: 1px solid #06b6d4;
    }

    .status-danger {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 1px solid #ef4444;
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
        color: var(--text-color);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.875rem;
    }

    .info-content {
        padding: 1.25rem;
        color: var(--text-color);
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
        color: var(--muted-text);
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        min-width: 150px;
    }

    .detail-label i {
        color: var(--neon-cyan);
    }

    .detail-value {
        color: var(--text-color);
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
        color: var(--muted-text);
        margin: 0;
        font-size: 0.875rem;
    }

    /* Table */
    .table-dark {
        width: 100%;
        color: var(--text-color);
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
        gap: 0.75rem;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--neon-cyan) 0%, var(--neon-blue) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.875rem;
        color: #0f172a;
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.5);
        flex-shrink: 0;
    }

    .nota-badge {
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        color: #0f172a;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 700;
        font-size: 1rem;
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
        display: inline-block;
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

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
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

        .stat-box {
            margin-bottom: 1rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function confirmarEliminacion() {
        if (confirm('¿Estás seguro de que deseas eliminar esta evaluación? Esta acción no se puede deshacer.')) {
            document.getElementById('delete-form').submit();
        }
    }
</script>
@endpush
@endsection