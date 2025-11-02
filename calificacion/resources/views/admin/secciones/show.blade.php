@extends('layouts.app')

@section('title', 'Ver Sección')
@section('page-title', 'Detalles de Sección')

@section('content')
<div class="container-fluid">
    <!-- Header con Info Principal -->
    <div class="page-header-show mb-4">
        <div class="header-content">
            <div class="header-left">
                <div class="section-icon">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div>
                    <h2>{{ $seccion->codigo_seccion }}</h2>
                    <p class="mb-0">{{ $seccion->curso->nombre ?? 'N/A' }}</p>
                </div>
            </div>
            <div class="header-right">
                <a href="{{ route('admin.secciones.edit', $seccion) }}" class="btn-neon me-2">
                    <i class="fas fa-edit me-2"></i>Editar
                </a>
                <a href="{{ route('admin.secciones.index') }}" class="btn-outline-neon">
                    <i class="fas fa-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Columna Izquierda - Información -->
        <div class="col-lg-8">
            <!-- Información General -->
            <div class="card-dark mb-4">
                <div class="card-header-dark">
                    <i class="fas fa-info-circle me-2"></i>Información General
                </div>
                <div class="card-body-dark">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="info-box">
                                <div class="info-label">
                                    <i class="fas fa-barcode me-2"></i>Código de Sección
                                </div>
                                <div class="info-value">{{ $seccion->codigo_seccion }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-box">
                                <div class="info-label">
                                    <i class="fas fa-toggle-on me-2"></i>Estado
                                </div>
                                <div class="info-value">
                                    @if($seccion->estado === 'activo')
                                        <span class="status-badge status-active">
                                            <i class="fas fa-circle"></i> Activo
                                        </span>
                                    @elseif($seccion->estado === 'inactivo')
                                        <span class="status-badge status-inactive">
                                            <i class="fas fa-circle"></i> Inactivo
                                        </span>
                                    @else
                                        <span class="status-badge status-finished">
                                            <i class="fas fa-circle"></i> Finalizado
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="info-box">
                                <div class="info-label">
                                    <i class="fas fa-book me-2"></i>Curso
                                </div>
                                <div class="info-value">
                                    <strong>{{ $seccion->curso->codigo_curso ?? 'N/A' }}</strong> - {{ $seccion->curso->nombre ?? 'N/A' }}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-box">
                                <div class="info-label">
                                    <i class="fas fa-user-tie me-2"></i>Profesor
                                </div>
                                <div class="info-value">{{ $seccion->profesor->nombre_completo ?? 'Sin asignar' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-box">
                                <div class="info-label">
                                    <i class="fas fa-calendar-alt me-2"></i>Período Académico
                                </div>
                                <div class="info-value">{{ $seccion->periodo->nombre ?? 'N/A' }}</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-box">
                                <div class="info-label">
                                    <i class="fas fa-graduation-cap me-2"></i>Modalidad
                                </div>
                                <div class="info-value">
                                    @if($seccion->modalidad == 'presencial')
                                        <span class="badge-modalidad presencial">
                                            <i class="fas fa-school"></i> Presencial
                                        </span>
                                    @elseif($seccion->modalidad == 'virtual')
                                        <span class="badge-modalidad virtual">
                                            <i class="fas fa-laptop-house"></i> Virtual
                                        </span>
                                    @else
                                        <span class="text-muted">No especificado</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-box">
                                <div class="info-label">
                                    <i class="fas fa-users me-2"></i>Capacidad
                                </div>
                                <div class="info-value">{{ $seccion->cupo_maximo }} estudiantes</div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="info-box">
                                <div class="info-label">
                                    <i class="fas fa-clock me-2"></i>Horario
                                </div>
                                <div class="info-value">{{ $seccion->horario ?? 'No especificado' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estudiantes Inscritos -->
            <div class="card-dark">
                <div class="card-header-dark">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>
                            <i class="fas fa-users me-2"></i>Estudiantes Inscritos
                        </span>
                        <span class="badge-neon">
                            {{ $seccion->inscripciones->count() }} / {{ $seccion->cupo_maximo }}
                        </span>
                    </div>
                </div>
                <div class="card-body-dark">
                    @if($seccion->inscripciones->count() > 0)
                        <div class="table-responsive">
                            <table class="table-dark">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Estudiante</th>
                                        <th>Email</th>
                                        <th>Fecha Inscripción</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($seccion->inscripciones as $inscripcion)
                                        <tr>
                                            <td><strong>#{{ $loop->iteration }}</strong></td>
                                            <td>
                                                <div class="student-info">
                                                    <div class="student-avatar">
                                                        <i class="fas fa-user-graduate"></i>
                                                    </div>
                                                    <div>
                                                        <strong>{{ $inscripcion->estudiante->nombre_completo ?? 'N/A' }}</strong>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $inscripcion->estudiante->email ?? 'N/A' }}</td>
                                            <td>
                                                <i class="fas fa-calendar me-2 text-info"></i>
                                                {{ $inscripcion->fecha_inscripcion->format('d/m/Y') }}
                                            </td>
                                            <td>
                                                @if($inscripcion->estado == 'inscrito')
                                                    <span class="status-badge status-active">
                                                        <i class="fas fa-circle"></i> Inscrito
                                                    </span>
                                                @else
                                                    <span class="status-badge status-inactive">
                                                        <i class="fas fa-circle"></i> {{ ucfirst($inscripcion->estado) }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-user-slash fa-3x mb-3"></i>
                            <h5>No hay estudiantes inscritos</h5>
                            <p>Esta sección aún no tiene estudiantes registrados</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Columna Derecha - Estadísticas -->
        <div class="col-lg-4">
            <!-- Estadísticas -->
            <div class="card-dark mb-4">
                <div class="card-header-dark">
                    <i class="fas fa-chart-pie me-2"></i>Estadísticas
                </div>
                <div class="card-body-dark">
                    <div class="stat-item mb-4">
                        <div class="stat-circle">
                            <svg viewBox="0 0 200 200">
                                <defs>
                                    <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                        <stop offset="0%" style="stop-color:#00d4ff;stop-opacity:1" />
                                        <stop offset="100%" style="stop-color:#00ffff;stop-opacity:1" />
                                    </linearGradient>
                                </defs>
                                <circle cx="100" cy="100" r="90" class="circle-bg"/>
                                <circle cx="100" cy="100" r="90" class="circle-progress"
                                        style="stroke-dashoffset: {{ 565.48 - (565.48 * ($seccion->estudiantes_inscritos / $seccion->cupo_maximo)) }}"/>
                            </svg>
                            <div class="stat-circle-text">
                                <span class="stat-number">{{ number_format(($seccion->estudiantes_inscritos / $seccion->cupo_maximo) * 100, 0) }}%</span>
                                <span class="stat-label">Ocupación</span>
                            </div>
                        </div>
                    </div>

                    <div class="stats-list">
                        <div class="stat-list-item">
                            <div class="stat-icon-small bg-gradient-success">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <div class="stat-details">
                                <div class="stat-value-small">{{ $seccion->estudiantes_inscritos }}</div>
                                <div class="stat-label-small">Estudiantes Inscritos</div>
                            </div>
                        </div>

                        <div class="stat-list-item">
                            <div class="stat-icon-small bg-gradient-info">
                                <i class="fas fa-chair"></i>
                            </div>
                            <div class="stat-details">
                                <div class="stat-value-small">{{ $seccion->cupos_disponibles }}</div>
                                <div class="stat-label-small">Cupos Disponibles</div>
                            </div>
                        </div>

                        <div class="stat-list-item">
                            <div class="stat-icon-small bg-gradient-primary">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-details">
                                <div class="stat-value-small">{{ $seccion->cupo_maximo }}</div>
                                <div class="stat-label-small">Cupo Máximo</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalles Adicionales -->
            <div class="card-dark mb-4">
                <div class="card-header-dark">
                    <i class="fas fa-calendar-check me-2"></i>Fechas
                </div>
                <div class="card-body-dark">
                    <div class="timeline-item">
                        <div class="timeline-icon">
                            <i class="fas fa-plus-circle"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-label">Creado</div>
                            <div class="timeline-value">{{ $seccion->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-icon">
                            <i class="fas fa-sync"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-label">Última Actualización</div>
                            <div class="timeline-value">{{ $seccion->updated_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="card-dark">
                <div class="card-header-dark">
                    <i class="fas fa-cog me-2"></i>Acciones
                </div>
                <div class="card-body-dark">
                    <div class="d-grid gap-3">
                        <a href="{{ route('admin.secciones.edit', $seccion) }}" class="btn-neon-full">
                            <i class="fas fa-edit me-2"></i>Editar Sección
                        </a>

                        <button type="button" 
                                class="btn-outline-neon-full"
                                onclick="window.print()">
                            <i class="fas fa-print me-2"></i>Imprimir Detalles
                        </button>

                        <form action="{{ route('admin.secciones.destroy', $seccion) }}" 
                              method="POST" 
                              id="delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="button" 
                                    class="btn-danger-full"
                                    onclick="confirmarEliminacion()">
                                <i class="fas fa-trash me-2"></i>Eliminar Sección
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Variables */
    :root {
        --neon-cyan: #00ffff;
        --neon-blue: #00d4ff;
        --electric-blue: #0ea5e9;
        --deep-blue: #0284c7;
    }

    /* Page Header Show */
    .page-header-show {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.95) 100%);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 0 30px rgba(0, 212, 255, 0.2);
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .section-icon {
        width: 80px;
        height: 80px;
        border-radius: 15px;
        background: linear-gradient(135deg, #84cc16 0%, #65a30d 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        color: #0f172a;
        box-shadow: 0 0 30px rgba(0, 212, 255, 0.5);
    }

    .header-left h2 {
        color: var(--neon-cyan);
        font-weight: 700;
        margin: 0;
        text-shadow: 0 0 15px rgba(0, 212, 255, 0.5);
        font-size: 2rem;
    }

    .header-left p {
        color: #94a3b8;
        font-size: 1.1rem;
    }

    .header-right {
        display: flex;
        gap: 0.5rem;
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
        color: var(--neon-cyan);
        font-weight: 600;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .card-body-dark {
        padding: 2rem;
    }

    /* Info Boxes */
    .info-box {
        background: rgba(0, 212, 255, 0.05);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
        padding: 1.25rem;
        transition: all 0.3s ease;
    }

    .info-box:hover {
        background: rgba(0, 212, 255, 0.1);
        border-color: rgba(0, 212, 255, 0.4);
        transform: translateY(-2px);
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.2);
    }

    .info-label {
        color: var(--neon-cyan);
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        color: #e2e8f0;
        font-size: 1.1rem;
    }

    /* Badges */
    .badge-neon {
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        color: #0f172a;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.875rem;
        box-shadow: 0 0 10px rgba(0, 212, 255, 0.3);
    }

    .badge-modalidad {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        border: 1px solid;
    }

    .badge-modalidad.presencial {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border-color: #10b981;
    }

    .badge-modalidad.virtual {
        background: rgba(139, 92, 246, 0.2);
        color: #8b5cf6;
        border-color: #8b5cf6;
    }

    .badge-modalidad.hibrida {
        background: rgba(245, 158, 11, 0.2);
        color: #f59e0b;
        border-color: #f59e0b;
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

    .status-finished {
        background: rgba(99, 102, 241, 0.2);
        color: #6366f1;
        border: 1px solid #6366f1;
    }

    .status-badge i {
        font-size: 0.5rem;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    /* Stat Circle */
    .stat-circle {
        position: relative;
        width: 200px;
        height: 200px;
        margin: 0 auto;
    }

    .stat-circle svg {
        width: 100%;
        height: 100%;
        transform: rotate(-90deg);
    }

    .circle-bg {
        fill: none;
        stroke: rgba(0, 212, 255, 0.1);
        stroke-width: 12;
    }

    .circle-progress {
        fill: none;
        stroke: url(#gradient);
        stroke-width: 12;
        stroke-linecap: round;
        stroke-dasharray: 565.48;
        transition: stroke-dashoffset 1s ease;
    }

    .stat-circle-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-align: center;
    }

    .stat-number {
        display: block;
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--neon-cyan);
        text-shadow: 0 0 15px rgba(0, 212, 255, 0.5);
    }

    .stat-label {
        display: block;
        font-size: 0.875rem;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* Stats List */
    .stats-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .stat-list-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        background: rgba(0, 212, 255, 0.05);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
        padding: 1rem;
        transition: all 0.3s ease;
    }

    .stat-list-item:hover {
        background: rgba(0, 212, 255, 0.1);
        transform: translateX(5px);
    }

    .stat-icon-small {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
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

    .stat-value-small {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--neon-cyan);
    }

    .stat-label-small {
        font-size: 0.875rem;
        color: #94a3b8;
    }

    /* Timeline */
    .timeline-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 0;
        border-bottom: 1px solid rgba(0, 212, 255, 0.1);
    }

    .timeline-item:last-child {
        border-bottom: none;
    }

    .timeline-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0f172a;
        font-size: 1rem;
    }

    .timeline-label {
        color: #94a3b8;
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }

    .timeline-value {
        color: #e2e8f0;
        font-weight: 600;
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

    .student-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .student-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0f172a;
        font-size: 1rem;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        color: #94a3b8;
    }

    .empty-state i {
        color: rgba(0, 212, 255, 0.3);
    }

    .empty-state h5 {
        color: #e2e8f0;
        margin: 1rem 0 0.5rem 0;
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

    .btn-neon-full {
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        border: none;
        color: #0f172a;
        font-weight: 600;
        padding: 1rem 1.5rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
        text-decoration: none;
        display: block;
        text-align: center;
        width: 100%;
    }

    .btn-neon-full:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 30px rgba(0, 212, 255, 0.6);
        color: #0f172a;
    }

    .btn-outline-neon-full {
        background: transparent;
        border: 2px solid var(--neon-cyan);
        color: var(--neon-cyan);
        font-weight: 600;
        padding: 1rem 1.5rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        text-decoration: none;
        display: block;
        text-align: center;
        width: 100%;
        cursor: pointer;
    }

    .btn-outline-neon-full:hover {
        background: rgba(0, 212, 255, 0.1);
        transform: translateY(-2px);
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
        color: var(--neon-cyan);
    }

    .btn-danger-full {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        border: none;
        color: white;
        font-weight: 600;
        padding: 1rem 1.5rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        box-shadow: 0 0 20px rgba(239, 68, 68, 0.3);
        width: 100%;
        cursor: pointer;
    }

    .btn-danger-full:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 30px rgba(239, 68, 68, 0.6);
    }

    /* Responsive */
    @media (max-width: 992px) {
        .header-content {
            flex-direction: column;
            align-items: flex-start;
        }

        .header-right {
            width: 100%;
            justify-content: flex-start;
        }
    }

    @media (max-width: 768px) {
        .page-header-show {
            padding: 1.5rem;
        }

        .section-icon {
            width: 60px;
            height: 60px;
            font-size: 2rem;
        }

        .header-left h2 {
            font-size: 1.5rem;
        }

        .header-left {
            gap: 1rem;
        }

        .card-body-dark {
            padding: 1rem;
        }

        .stat-circle {
            width: 150px;
            height: 150px;
        }

        .stat-number {
            font-size: 2rem;
        }

        .btn-neon,
        .btn-outline-neon {
            width: 100%;
            margin: 0 !important;
        }

        .header-right {
            flex-direction: column;
            gap: 0.5rem;
        }
    }

    /* Print Styles */
    @media print {
        .btn-neon,
        .btn-outline-neon,
        .btn-neon-full,
        .btn-outline-neon-full,
        .btn-danger-full {
            display: none;
        }

        .page-header-show,
        .card-dark {
            box-shadow: none;
            border: 1px solid #ddd;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function confirmarEliminacion() {
        if (confirm('¿Estás seguro de que deseas eliminar esta sección? Esta acción no se puede deshacer.\n\nSe eliminarán también todas las inscripciones asociadas.')) {
            document.getElementById('delete-form').submit();
        }
    }

    // Animación del círculo de progreso al cargar
    document.addEventListener('DOMContentLoaded', function() {
        const circle = document.querySelector('.circle-progress');
        if (circle) {
            circle.style.strokeDashoffset = '565.48';
            setTimeout(() => {
                circle.style.strokeDashoffset = circle.style.strokeDashoffset;
            }, 100);
        }
    });
</script>
@endpush
@endsection