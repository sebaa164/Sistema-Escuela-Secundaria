@extends('layouts.app')

@section('title', 'Detalle del Período')
@section('page-title', 'Período Académico')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->

    <div class="row">
        <!-- Columna Principal -->
        <div class="col-lg-8">
            <!-- Información del Período -->
            <div class="card-dark mb-4">
                <div class="card-header-dark">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-calendar-alt me-2"></i>{{ $periodo->nombre }}
                        </h5>
                        <div class="d-flex gap-2">
                            @if($periodo->es_vigente)
                                <span class="badge-vigente">
                                    <i class="fas fa-circle pulse"></i> Vigente
                                </span>
                            @endif

                            @if($periodo->estado === 'Activo')
                                <span class="status-badge status-active">
                                    <i class="fas fa-circle"></i> Activo
                                </span>
                            @elseif($periodo->estado === 'Finalizado')
                                <span class="status-badge status-finished">
                                    <i class="fas fa-circle"></i> Finalizado
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
                    <!-- Información de Fechas -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="info-card">
                                <div class="info-card-icon bg-gradient-success">
                                    <i class="fas fa-play"></i>
                                </div>
                                <div class="info-card-content">
                                    <p class="info-card-label">Fecha de Inicio</p>
                                    <h4 class="info-card-value">{{ $periodo->fecha_inicio->format('d/m/Y') }}</h4>
                                    <small class="text-muted">{{ $periodo->fecha_inicio->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-card">
                                <div class="info-card-icon bg-gradient-danger">
                                    <i class="fas fa-stop"></i>
                                </div>
                                <div class="info-card-content">
                                    <p class="info-card-label">Fecha de Finalización</p>
                                    <h4 class="info-card-value">{{ $periodo->fecha_fin->format('d/m/Y') }}</h4>
                                    <small class="text-muted">{{ $periodo->fecha_fin->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Duración -->
                    <div class="duration-display">
                        <div class="duration-icon">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                        <div class="duration-content">
                            <h6>Duración del Período</h6>
                            <p>
                                <strong>{{ $periodo->duracion_dias }}</strong> días
                                @php
                                    $meses = floor($periodo->duracion_dias / 30);
                                    $dias = $periodo->duracion_dias % 30;
                                @endphp
                                @if($meses > 0)
                                    (aprox. {{ $meses }} {{ $meses === 1 ? 'mes' : 'meses' }}
                                    @if($dias > 0)
                                        y {{ $dias }} {{ $dias === 1 ? 'día' : 'días' }}
                                    @endif
                                    )
                                @endif
                            </p>
                            @if($periodo->es_vigente)
                                <div class="remaining-time">
                                    <i class="fas fa-clock me-2"></i>
                                    Quedan <strong>{{ $periodo->dias_restantes }}</strong> días
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="action-buttons mt-4">
                        <a href="{{ route('admin.periodos.edit', $periodo) }}" class="btn-neon">
                            <i class="fas fa-edit me-2"></i>Editar Período
                        </a>
                        <a href="{{ route('admin.periodos.index') }}" class="btn-outline-neon">
                            <i class="fas fa-arrow-left me-2"></i>Volver
                        </a>
                    </div>
                </div>
            </div>

            <!-- Secciones del Período -->
            <div class="card-dark">
                <div class="card-header-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-layer-group me-2"></i>Secciones del Período
                    </h5>
                </div>

                <div class="card-body-dark">
                    @if($periodo->secciones->count() > 0)
                        <div class="table-responsive">
                            <table class="table-dark">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Curso</th>
                                        <th>Profesor</th>
                                        <th>Horario</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($periodo->secciones as $seccion)
                                        <tr>
                                            <td>
                                                <span class="badge-neon">{{ $seccion->codigo_seccion }}</span>
                                            </td>
                                            <td>
                                                <div class="course-info-mini">
                                                    <strong>{{ $seccion->curso->nombre_curso }}</strong>
                                                    <br><small class="text-muted">{{ $seccion->curso->codigo_curso }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                @if($seccion->profesor)
                                                    <div class="profesor-info">
                                                        <i class="fas fa-user-tie me-2"></i>
                                                        {{ $seccion->profesor->nombre }} {{ $seccion->profesor->apellido }}
                                                    </div>
                                                @else
                                                    <span class="text-muted">Sin asignar</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($seccion->horario)
                                                    <small class="text-muted">{{ $seccion->horario }}</small>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($seccion->estado === 'Activo')
                                                    <span class="status-badge-sm status-active">Activo</span>
                                                @else
                                                    <span class="status-badge-sm status-inactive">Inactivo</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state-mini">
                            <i class="fas fa-layer-group fa-3x mb-3"></i>
                            <h6>No hay secciones registradas</h6>
                            <p>Este período aún no tiene secciones asignadas</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Columna Lateral - Estadísticas -->
        <div class="col-lg-4">
            <!-- Estadísticas Rápidas -->
            <div class="card-dark mb-4">
                <div class="card-header-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Estadísticas
                    </h5>
                </div>

                <div class="card-body-dark">
                    <div class="stat-item-vertical">
                        <div class="stat-icon-vertical bg-gradient-primary">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div class="stat-content-vertical">
                            <h3>{{ $totalSecciones }}</h3>
                            <p>Total Secciones</p>
                        </div>
                    </div>

                    <div class="stat-item-vertical">
                        <div class="stat-icon-vertical bg-gradient-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-content-vertical">
                            <h3>{{ $seccionesActivas }}</h3>
                            <p>Secciones Activas</p>
                        </div>
                    </div>

                    <div class="stat-item-vertical">
                        <div class="stat-icon-vertical bg-gradient-info">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content-vertical">
                            <h3>{{ $totalInscripciones }}</h3>
                            <p>Total Inscripciones</p>
                        </div>
                    </div>

                    <div class="stat-item-vertical">
                        <div class="stat-icon-vertical bg-gradient-warning">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="stat-content-vertical">
                            <h3>{{ $periodo->secciones->pluck('curso_id')->unique()->count() }}</h3>
                            <p>Cursos Diferentes</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información Adicional -->
            <div class="card-dark">
                <div class="card-header-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Información
                    </h5>
                </div>

                <div class="card-body-dark">
                    <div class="info-list-vertical">

                        <div class="info-item-vertical">
                            <i class="fas fa-clock"></i>
                            <div>
                                <p class="label">Fecha de Creación</p>
                                <p class="value">{{ $periodo->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        <div class="info-item-vertical">
                            <i class="fas fa-sync"></i>
                            <div>
                                <p class="label">Última Actualización</p>
                                <p class="value">{{ $periodo->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        <div class="info-item-vertical">
                            <i class="fas fa-toggle-on"></i>
                            <div>
                                <p class="label">Estado</p>
                                <p class="value">{{ ucfirst($periodo->estado) }}</p>
                            </div>
                        </div>
                    </div>
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

    /* Info Cards */
    .info-card {
        background: rgba(0, 212, 255, 0.05);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 15px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.3s ease;
    }

    .info-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.2);
        border-color: var(--neon-cyan);
    }

    .info-card-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        flex-shrink: 0;
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .bg-gradient-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
    }

    .bg-gradient-info {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
    }

    .bg-gradient-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .info-card-content {
        flex: 1;
    }

    .info-card-label {
        color: #94a3b8;
        font-size: 0.875rem;
        margin: 0 0 0.5rem 0;
    }

    .info-card-value {
        color: var(--neon-cyan);
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.3);
    }

    /* Duration Display */
    .duration-display {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(6, 182, 212, 0.1) 100%);
        border: 2px solid rgba(16, 185, 129, 0.3);
        border-radius: 15px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .duration-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        background: linear-gradient(135deg, #10b981 0%, #06b6d4 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        flex-shrink: 0;
    }

    .duration-content h6 {
        color: var(--neon-cyan);
        font-weight: 600;
        margin: 0 0 0.5rem 0;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.3);
    }

    .duration-content p {
        color: #e2e8f0;
        margin: 0;
        font-size: 1.1rem;
    }

    .remaining-time {
        margin-top: 0.75rem;
        padding-top: 0.75rem;
        border-top: 1px solid rgba(16, 185, 129, 0.3);
        color: #10b981;
        font-size: 0.95rem;
    }

    /* Badges */
    .badge-vigente {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 1px solid #10b981;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .badge-vigente .pulse {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

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

    .status-finished {
        background: rgba(148, 163, 184, 0.2);
        color: #94a3b8;
        border: 1px solid #94a3b8;
    }

    .status-badge i {
        font-size: 0.5rem;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        padding-top: 1.5rem;
        border-top: 1px solid rgba(0, 212, 255, 0.2);
    }

    .btn-neon {
        background: linear-gradient(135deg, #84cc16 0%, #65a30d 100%);
        border: none;
        color: #0f172a;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        box-shadow: 0 0 20px rgba(132, 204, 22, 0.3);
        text-decoration: none;
        display: inline-block;
    }

    .btn-neon:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 30px rgba(132, 204, 22, 0.6);
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
    }

    .table-dark tbody td {
        color: #e2e8f0;
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

    .course-info-mini strong {
        color: #ffffff;
    }

    .profesor-info {
        color: #e2e8f0;
        font-size: 0.875rem;
    }

    .profesor-info i {
        color: var(--neon-cyan);
    }

    .status-badge-sm {
        padding: 0.25rem 0.5rem;
        border-radius: 15px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    /* Empty State */
    .empty-state-mini {
        text-align: center;
        padding: 3rem 1rem;
        color: #94a3b8;
    }

    .empty-state-mini i {
        color: rgba(0, 212, 255, 0.3);
    }

    .empty-state-mini h6 {
        color: #e2e8f0;
        margin-bottom: 0.5rem;
    }

    /* Stats Vertical */
    .stat-item-vertical {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        margin-bottom: 1rem;
        background: rgba(0, 212, 255, 0.05);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .stat-item-vertical:hover {
        background: rgba(0, 212, 255, 0.1);
        border-color: var(--neon-cyan);
        transform: translateX(5px);
    }

    .stat-icon-vertical {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
        flex-shrink: 0;
    }

    .stat-content-vertical h3 {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--neon-cyan);
        margin: 0;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .stat-content-vertical p {
        color: #94a3b8;
        margin: 0;
        font-size: 0.875rem;
    }

    /* Info List Vertical */
    .info-list-vertical {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .info-item-vertical {
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        padding: 1rem;
        background: rgba(0, 212, 255, 0.05);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
    }

    .info-item-vertical i {
        color: var(--neon-cyan);
        font-size: 1.25rem;
        margin-top: 0.25rem;
    }

    .info-item-vertical .label {
        color: #94a3b8;
        font-size: 0.875rem;
        margin: 0 0 0.25rem 0;
    }

    .info-item-vertical .value {
        color: #e2e8f0;
        font-weight: 600;
        margin: 0;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .action-buttons {
            flex-direction: column;
        }

        .btn-neon,
        .btn-outline-neon {
            width: 100%;
        }

        .duration-display,
        .info-card {
            flex-direction: column;
            text-align: center;
        }
    }

    .text-muted {
        color: #cbd5e1 !important;
    }

    small.text-muted {
        color: #94a3b8 !important;
    }
</style>
@endpush
@endsection