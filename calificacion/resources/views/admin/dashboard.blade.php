@extends('layouts.app')

@section('title', 'Dashboard - Administración')

@section('page-title', 'Panel de Administración')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header-dark mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="header-title">
                    <i class="fas fa-chart-line me-3"></i>Panel de Control
                </h1>
                <p class="header-subtitle">Colegio Secundario Augusto Pulenta</p>
            </div>
            <div class="col-md-4 text-end">
                @if($periodoActual)
                    <div class="badge-dracula-lg">
                        <i class="fas fa-calendar-check me-2"></i>{{ $periodoActual->nombre }}
                    </div>
                @else
                    <div class="badge-warning-lg">
                        <i class="fas fa-exclamation-triangle me-2"></i>Sin período activo
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Tarjetas de Estadísticas -->
    <div class="stats-grid mb-4">
        <!-- Estudiantes -->
        <div class="stat-card">
            <div class="stat-icon bg-gradient-purple">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $totalEstudiantes }}</h3>
                <p>Estudiantes</p>
                <small class="stat-detail">
                    <i class="fas fa-arrow-up me-1"></i>Activos
                </small>
            </div>
            <div class="stat-footer">
                <a href="{{ route('admin.usuarios.index') }}?tipo=estudiante" class="btn-dracula-sm">
                    <i class="fas fa-eye me-1"></i>Ver todos
                </a>
            </div>
        </div>

        <!-- Profesores -->
        <div class="stat-card">
            <div class="stat-icon bg-gradient-pink">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $totalProfesores }}</h3>
                <p>Profesores</p>
                <small class="stat-detail">
                    <i class="fas fa-check me-1"></i>Activos
                </small>
            </div>
            <div class="stat-footer">
                <a href="{{ route('admin.usuarios.profesores') }}" class="btn-dracula-sm">
                    <i class="fas fa-eye me-1"></i>Ver todos
                </a>
            </div>
        </div>

        <!-- Cursos/Materias -->
        <div class="stat-card">
            <div class="stat-icon bg-gradient-cyan">
                <i class="fas fa-book"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $totalCursos }}</h3>
                <p>Materias</p>
                <small class="stat-detail">
                    <i class="fas fa-check me-1"></i>Disponibles
                </small>
            </div>
            <div class="stat-footer">
                <a href="{{ route('admin.cursos.index') }}" class="btn-dracula-sm">
                    <i class="fas fa-eye me-1"></i>Ver todos
                </a>
            </div>
        </div>

        <!-- Secciones/Clases -->
        <div class="stat-card">
            <div class="stat-icon bg-gradient-green">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $seccionesActuales }}</h3>
                <p>Clases Activas</p>
                <small class="stat-detail">
                    <i class="fas fa-clock me-1"></i>Este período
                </small>
            </div>
            <div class="stat-footer">
                <a href="{{ route('admin.secciones.index') }}" class="btn-dracula-sm">
                    <i class="fas fa-eye me-1"></i>Ver todas
                </a>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <!-- Inscripciones del Período -->
        <div class="col-lg-8">
            <div class="card-dark">
                <div class="card-header-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-clipboard-list me-2"></i>Inscripciones Recientes
                    </h5>
                </div>
                <div class="card-body-dark">
                    @if($periodoActual)
                        <div class="alert-info-dark mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>{{ $inscripcionesActuales }}</strong> estudiantes inscritos en el período actual
                            <span class="badge-dracula ms-2">{{ $seccionesActuales }} clases</span>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table-dark">
                            <thead>
                                <tr>
                                    <th>Estudiante</th>
                                    <th>Materia</th>
                                    <th>Sección</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($ultimasInscripciones as $inscripcion)
                                    <tr>
                                        <td>
                                            <div class="user-info">
                                                <div class="avatar-circle">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                                <div>
                                                    <strong>{{ $inscripcion->estudiante->nombre_completo }}</strong>
                                                    <br><small class="text-muted-dark">{{ $inscripcion->estudiante->email }}</small>
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <strong>{{ $inscripcion->seccion->curso->nombre ?? 'N/A' }}</strong>
                                            <br><small class="text-muted-dark">{{ $inscripcion->seccion->curso->codigo_curso ?? 'N/A' }}</small>
                                        </td>
                                        <td>
                                            <span class="badge-secondary">{{ $inscripcion->seccion->codigo_seccion ?? 'N/A' }}</span>
                                        </td>
                                        <td>{{ $inscripcion->fecha_inscripcion ? $inscripcion->fecha_inscripcion->format('d/m/Y') : 'N/A' }}</td>
                                        <td>

                                            @if($inscripcion->estado === 'inscrito')
                                                <span class="status-badge status-active">
                                                    <i class="fas fa-circle"></i> Inscrito
                                                </span>
                                            @elseif($inscripcion->estado === 'completado')
                                                <span class="status-badge status-completed">
                                                    <i class="fas fa-circle"></i> Completado
                                                </span>
                                            @else
                                                <span class="status-badge status-warning">
                                                    <i class="fas fa-circle"></i> {{ ucfirst($inscripcion->estado) }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="empty-state">
                                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                                <h5>No hay inscripciones recientes</h5>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer-dark">
                    <a href="{{ route('admin.inscripciones.index') }}" class="btn-dracula">
                        <i class="fas fa-list me-1"></i>Ver todas las inscripciones
                    </a>
                </div>
            </div>
        </div>

        <!-- Estadísticas y Accesos Rápidos -->
        <div class="col-lg-4">
            <!-- Distribución por Carrera -->
            <div class="card-dark mb-3">
                <div class="card-header-dark">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Estudiantes por Carrera
                    </h6>
                </div>
                <div class="card-body-dark">
                    @if($estudiantesPorCarrera->count() > 0)
                        @foreach($estudiantesPorCarrera as $carrera)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="small text-light">{{ $carrera->carrera ?: 'Sin especificar' }}</span>
                                    <span class="small fw-bold dracula-text">{{ $carrera->total }}</span>
                                </div>
                                <div class="progress-dark">
                                    <div class="progress-bar-dracula" 
                                         style="width: {{ $inscripcionesActuales > 0 ? ($carrera->total / $inscripcionesActuales) * 100 : 0 }}%">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted-dark text-center mb-0">
                            <i class="fas fa-info-circle me-2"></i>Sin datos disponibles
                        </p>
                    @endif
                </div>
            </div>

            <!-- Secciones Populares -->
            <div class="card-dark">
                <div class="card-header-dark">
                    <h6 class="mb-0">
                        <i class="fas fa-fire me-2"></i>Clases más Solicitadas
                    </h6>
                </div>
                <div class="card-body-dark">
                    @forelse($seccionesPopulares as $seccion)
                        <div class="popular-item">
                            <div>
                                <strong class="d-block text-light">{{ $seccion->curso->nombre }}</strong>
                                <small class="text-muted-dark">
                                    Sección {{ $seccion->codigo_seccion }} - {{ $seccion->periodo->nombre ?? 'Sin período' }}
                                </small>
                            </div>
                            <div class="text-end">
                                <span class="badge-dracula">{{ $seccion->inscripciones_count }}</span>
                                <small class="d-block text-muted-dark">alumnos</small>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted-dark text-center mb-0">
                            <i class="fas fa-inbox me-2"></i>No hay datos
                        </p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Accesos Rápidos -->
    <div class="row g-3 mt-3">
        <div class="col-12">
            <div class="card-dark">
                <div class="card-header-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>Accesos Rápidos
                    </h5>
                </div>
                <div class="card-body-dark">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('admin.usuarios.create') }}" class="quick-access-btn quick-purple">
                                <i class="fas fa-user-plus fa-2x mb-2"></i>
                                <strong>Nuevo Usuario</strong>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.cursos.create') }}" class="quick-access-btn quick-pink">
                                <i class="fas fa-book-medical fa-2x mb-2"></i>
                                <strong>Nueva Materia</strong>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.secciones.create') }}" class="quick-access-btn quick-cyan">
                                <i class="fas fa-plus-circle fa-2x mb-2"></i>
                                <strong>Nueva Clase</strong>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.reportes.index') }}" class="quick-access-btn quick-green">
                                <i class="fas fa-chart-bar fa-2x mb-2"></i>
                                <strong>Ver Reportes</strong>
                            </a>
                        </div>
                    </div>
                </div>
</div>

@push('styles')
<style>
    /* Dracula Theme Colors + Neon Blues - Original Style */
    :root {
        --dracula-bg: #1a1c2e;
        --dracula-current: #2d3148;
        --dracula-selection: #44475a;
        --dracula-foreground: #f8f8f2;
        --dracula-comment: #6272a4;
        --dracula-cyan: #00ffff;
        --dracula-cyan-light: #5dfdff;
        --dracula-green: #50fa7b;
        --dracula-orange: #ffb86c;
        --dracula-pink: #ff79c6;
        --dracula-purple: #bd93f9;
        --dracula-red: #ff5555;
        --dracula-yellow: #f1fa8c;
        --neon-blue: #00d4ff;
        --neon-cyan: #00ffff;
        --electric-blue: #0ea5e9;
        --deep-blue: #0284c7;
    }

    /* Page Header Dark - Original */
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
        color: var(--dracula-cyan-light);
        margin: 0.5rem 0 0 0;
        text-shadow: 0 0 5px rgba(93, 253, 255, 0.3);
    }

    /* Stats Grid - Original */
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
        transition: all 0.3s ease;
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.1);
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0 25px rgba(0, 212, 255, 0.3);
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
        color: var(--dracula-bg);
        align-self: flex-start;
    }

    .bg-gradient-purple {
        background: linear-gradient(135deg, #bd93f9 0%, #7c3aed 100%);
        box-shadow: 0 0 20px rgba(189, 147, 249, 0.4);
    }

    .bg-gradient-pink {
        background: linear-gradient(135deg, #ff79c6 0%, #ec4899 100%);
        box-shadow: 0 0 20px rgba(255, 121, 198, 0.4);
    }

    .bg-gradient-cyan {
        background: linear-gradient(135deg, #00ffff 0%, #00d4ff 100%);
        box-shadow: 0 0 20px rgba(0, 255, 255, 0.4);
    }

    .bg-gradient-green {
        background: linear-gradient(135deg, #50fa7b 0%, #00ff88 100%);
        box-shadow: 0 0 20px rgba(80, 250, 123, 0.4);
    }

    .stat-content {
        flex: 1;
    }

    .stat-content h3 {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--neon-cyan);
        margin: 0;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .stat-content p {
        color: var(--dracula-foreground);
        margin: 0.5rem 0;
        font-size: 1rem;
        font-weight: 500;
    }

    .stat-detail {
        color: var(--dracula-comment);
        font-size: 0.875rem;
    }

    .stat-footer {
        margin-top: auto;
    }

    /* Card Dark - Original */
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

    .card-header-dark h5,
    .card-header-dark h6 {
        color: var(--neon-cyan);
        font-weight: 600;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
        margin: 0;
    }

    .card-body-dark {
        padding: 2rem;
    }

    .card-footer-dark {
        background: rgba(26, 28, 46, 0.8);
        border-top: 1px solid rgba(0, 212, 255, 0.2);
        padding: 1rem 1.5rem;
    }

    /* Badges */
    .badge-dracula-lg,
    .badge-dracula {
        background: linear-gradient(135deg, var(--neon-cyan) 0%, var(--electric-blue) 100%);
        color: #0f172a;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.875rem;
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.4);
        display: inline-block;
    }

    .badge-dracula-lg {
        font-size: 1rem;
        padding: 0.75rem 1.5rem;
    }

    .badge-warning-lg {
        background: linear-gradient(135deg, var(--dracula-orange) 0%, var(--dracula-yellow) 100%);
        color: var(--dracula-bg);
        padding: 0.75rem 1.5rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 1rem;
        box-shadow: 0 0 10px rgba(255, 184, 108, 0.3);
        display: inline-block;
    }

    .badge-secondary {
        background: rgba(45, 49, 72, 0.6);
        color: var(--dracula-cyan-light);
        border: 1px solid var(--electric-blue);
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        box-shadow: 0 0 10px rgba(14, 165, 233, 0.2);
    }

    /* Buttons */
    .btn-dracula,
    .btn-dracula-sm {
        background: linear-gradient(135deg, var(--neon-cyan) 0%, var(--electric-blue) 100%);
        border: none;
        color: #0f172a;
        font-weight: 600;
        border-radius: 10px;
        transition: all 0.3s ease;
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
        text-decoration: none;
        display: inline-block;
    }

    .btn-dracula {
        padding: 0.75rem 1.5rem;
    }

    .btn-dracula-sm {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        width: 100%;
        text-align: center;
    }

    .btn-dracula:hover,
    .btn-dracula-sm:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 30px rgba(0, 212, 255, 0.6);
        color: #0f172a;
    }

    /* Table Dark */
    .table-dark {
        width: 100%;
        color: var(--dracula-foreground);
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
        gap: 1rem;
    }

    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--neon-cyan) 0%, var(--electric-blue) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0f172a;
        font-size: 1rem;
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.4);
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
        background: rgba(80, 250, 123, 0.25);
        color: #00ff88;
        border: 1px solid var(--dracula-green);
        box-shadow: 0 0 10px rgba(80, 250, 123, 0.3);
    }

    .status-completed {
        background: rgba(0, 255, 255, 0.25);
        color: var(--neon-cyan);
        border: 1px solid var(--neon-cyan);
        box-shadow: 0 0 10px rgba(0, 255, 255, 0.3);
    }

    .status-warning {
        background: rgba(255, 184, 108, 0.25);
        color: #ffb86c;
        border: 1px solid var(--dracula-orange);
        box-shadow: 0 0 10px rgba(255, 184, 108, 0.3);
    }

    .status-badge i {
        font-size: 0.5rem;
        animation: pulse 2s infinite;
    }

    /* Alert Info Dark */
    .alert-info-dark {
        background: rgba(0, 212, 255, 0.1);
        border: 1px solid var(--neon-cyan);
        border-radius: 10px;
        padding: 1rem;
        color: var(--dracula-foreground);
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.2);
    }

    /* Progress Dark */
    .progress-dark {
        height: 8px;
        background: rgba(26, 28, 46, 0.9);
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid rgba(0, 212, 255, 0.2);
    }

    .progress-bar-dracula {
        height: 100%;
        background: linear-gradient(135deg, var(--neon-cyan) 0%, var(--electric-blue) 100%);
        box-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
        transition: width 0.6s ease;
    }

    /* Popular Item */
    .popular-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 0;
        border-bottom: 1px solid rgba(0, 212, 255, 0.1);
    }

    .popular-item:last-child {
        border-bottom: none;
    }

    /* Quick Access Buttons - Original */
    .quick-access-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
        background: rgba(15, 23, 42, 0.8);
        border: 2px solid;
        border-radius: 15px;
        text-decoration: none;
        transition: all 0.3s ease;
        height: 100%;
        min-height: 150px;
    }

    .quick-purple {
        border-color: var(--dracula-purple);
        color: var(--dracula-purple);
    }

    .quick-purple:hover {
        background: rgba(189, 147, 249, 0.15);
        border-color: var(--dracula-purple);
        transform: translateY(-5px);
        box-shadow: 0 0 30px rgba(189, 147, 249, 0.4);
        color: var(--dracula-purple);
        text-decoration: none;
    }

    .quick-pink {
        border-color: var(--dracula-pink);
        color: var(--dracula-pink);
    }

    .quick-pink:hover {
        background: rgba(255, 121, 198, 0.15);
        border-color: var(--dracula-pink);
        transform: translateY(-5px);
        box-shadow: 0 0 30px rgba(255, 121, 198, 0.4);
        color: var(--dracula-pink);
        text-decoration: none;
    }

    .quick-cyan {
        border-color: var(--neon-cyan);
        color: var(--neon-cyan);
    }

    .quick-cyan:hover {
        background: rgba(0, 255, 255, 0.15);
        border-color: var(--neon-cyan);
        transform: translateY(-5px);
        box-shadow: 0 0 30px rgba(0, 255, 255, 0.5);
        color: var(--neon-cyan);
        text-decoration: none;
    }

    .quick-green {
        border-color: var(--dracula-green);
        color: var(--dracula-green);
    }

    .quick-green:hover {
        background: rgba(80, 250, 123, 0.15);
        border-color: var(--dracula-green);
        transform: translateY(-5px);
        box-shadow: 0 0 30px rgba(80, 250, 123, 0.4);
        color: var(--dracula-green);
        text-decoration: none;
    }

    .quick-access-btn i {
        text-shadow: 0 0 15px currentColor;
        margin-bottom: 0.5rem;
    }

    .quick-access-btn strong {
        font-size: 1rem;
        text-align: center;
        line-height: 1.4;
    }

    /* Text Utilities */
    .text-muted-dark {
        color: var(--dracula-comment) !important;
    }

    .text-light {
        color: var(--dracula-foreground) !important;
    }

    .dracula-text {
        color: var(--neon-cyan);
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    /* Empty State */
    .empty-state {
        color: var(--dracula-comment);
        padding: 3rem;
    }

    .empty-state i {
        color: rgba(0, 212, 255, 0.3);
        text-shadow: 0 0 15px rgba(0, 212, 255, 0.2);
    }

    .empty-state h5 {
        color: var(--dracula-foreground);
        margin-top: 1rem;
    }

    /* Animations */
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .header-title {
            font-size: 1.5rem;
        }

        .stat-content h3 {
            font-size: 2rem;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .quick-access-btn {
            padding: 1.5rem 1rem;
            min-height: 120px;
        }

        .quick-access-btn i {
            font-size: 1.5rem !important;
        }

        .page-header-dark {
            padding: 1.5rem;
        }

        .card-body-dark {
            padding: 1rem;
        }
    }

    /* Scrollbar Styling */
    ::-webkit-scrollbar {
        width: 10px;
        height: 10px;
    }

    ::-webkit-scrollbar-track {
        background: var(--dracula-bg);
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, var(--neon-cyan) 0%, var(--electric-blue) 100%);
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 255, 255, 0.3);
    }

    ::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, var(--electric-blue) 0%, var(--neon-cyan) 100%);
        box-shadow: 0 0 15px rgba(0, 255, 255, 0.5);
    }
</style>
@endpush
@endsection