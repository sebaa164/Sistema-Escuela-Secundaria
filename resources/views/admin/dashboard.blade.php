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
        </div>
    </div>
</div>

@push('styles')
<style>
/* ============================================
   DASHBOARD MINIMALISTA - SISTEMA ACADÉMICO
   Diseño limpio y profesional
   ============================================ */

:root {
    /* Colores principales (coherentes con login) */
    --primary-color: #0ea5e9;
    --primary-dark: #0284c7;
    --primary-light: #38bdf8;
    --primary-hover: #0c8cd9;
    
    /* Colores neutros */
    --white: #ffffff;
    --gray-50: #f8fafc;
    --gray-100: #f1f5f9;
    --gray-200: #e2e8f0;
    --gray-300: #cbd5e1;
    --gray-400: #94a3b8;
    --gray-500: #64748b;
    --gray-600: #475569;
    --gray-700: #334155;
    --gray-800: #1e293b;
    --gray-900: #0f172a;
    
    /* Colores de estado */
    --success: #10b981;
    --success-bg: #d1fae5;
    --warning: #f59e0b;
    --warning-bg: #fef3c7;
    --danger: #ef4444;
    --danger-bg: #fee2e2;
    --info: #3b82f6;
    --info-bg: #dbeafe;
    
    /* Sombras */
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

/* ============================================
   PAGE HEADER - Limpio y moderno
   ============================================ */
.page-header-dark {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    border: none;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: var(--shadow-md);
    margin-bottom: 2rem;
}

.header-title {
    color: var(--white);
    font-size: 1.875rem;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    letter-spacing: -0.025em;
}

.header-title i {
    font-size: 1.75rem;
}

.header-subtitle {
    color: rgba(255, 255, 255, 0.9);
    margin: 0.5rem 0 0 0;
    font-size: 0.95rem;
    font-weight: 400;
}

/* ============================================
   STATS GRID - Tarjetas de estadísticas
   ============================================ */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1.25rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: 12px;
    padding: 1.5rem;
    transition: all 0.2s ease;
    box-shadow: var(--shadow-sm);
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    border-color: var(--primary-light);
}

.stat-icon {
    width: 56px;
    height: 56px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    align-self: flex-start;
    color: var(--white);
}

.bg-gradient-purple {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
}

.bg-gradient-pink {
    background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
}

.bg-gradient-cyan {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
}

.bg-gradient-green {
    background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
}

.stat-content {
    flex: 1;
}

.stat-content h3 {
    font-size: 2.25rem;
    font-weight: 700;
    color: var(--gray-900);
    margin: 0;
    line-height: 1;
}

.stat-content p {
    color: var(--gray-600);
    margin: 0.5rem 0 0;
    font-size: 0.95rem;
    font-weight: 500;
}

.stat-detail {
    color: var(--gray-500);
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    margin-top: 0.5rem;
}

.stat-detail i {
    font-size: 0.75rem;
}

.stat-footer {
    margin-top: auto;
    padding-top: 0.75rem;
}

/* ============================================
   CARDS - Minimalistas
   ============================================ */
.card-dark {
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    margin-bottom: 1.5rem;
}

.card-header-dark {
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
    padding: 1.25rem 1.5rem;
}

.card-header-dark h5,
.card-header-dark h6 {
    color: var(--gray-900);
    font-weight: 600;
    margin: 0;
    display: flex;
    align-items: center;
    font-size: 1.05rem;
}

.card-header-dark i {
    color: var(--primary-color);
    margin-right: 0.5rem;
}

.card-body-dark {
    padding: 1.5rem;
}

.card-footer-dark {
    background: var(--gray-50);
    border-top: 1px solid var(--gray-200);
    padding: 1rem 1.5rem;
}

/* ============================================
   BADGES - Limpios y legibles
   ============================================ */
.badge-dracula-lg,
.badge-dracula {
    background: var(--primary-color);
    color: var(--white);
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.875rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.badge-dracula-lg {
    font-size: 0.95rem;
    padding: 0.65rem 1.25rem;
}

.badge-warning-lg {
    background: var(--warning);
    color: var(--white);
    padding: 0.65rem 1.25rem;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.95rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.badge-secondary {
    background: var(--gray-100);
    color: var(--gray-700);
    border: 1px solid var(--gray-300);
    padding: 0.35rem 0.75rem;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 600;
}

/* ============================================
   BUTTONS - Minimalistas
   ============================================ */
.btn-dracula,
.btn-dracula-sm {
    background: var(--primary-color);
    border: none;
    color: var(--white);
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-dracula {
    padding: 0.65rem 1.25rem;
    font-size: 0.9rem;
}

.btn-dracula-sm {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    width: 100%;
}

.btn-dracula:hover,
.btn-dracula-sm:hover {
    background: var(--primary-hover);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
    color: var(--white);
}

/* ============================================
   TABLES - Limpias y profesionales
   ============================================ */
.table-dark {
    width: 100%;
    color: var(--gray-700);
    background: var(--white);
}

.table-dark thead th {
    background: var(--gray-50);
    color: var(--gray-700);
    text-transform: uppercase;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 0.875rem 1rem;
    border-bottom: 2px solid var(--gray-200);
    letter-spacing: 0.05em;
}

.table-dark tbody tr {
    border-bottom: 1px solid var(--gray-200);
    transition: background-color 0.15s ease;
}

.table-dark tbody tr:hover {
    background: var(--gray-50);
}

.table-dark tbody td {
    padding: 1rem;
    vertical-align: middle;
    font-size: 0.9rem;
}

.table-dark tbody tr:last-child {
    border-bottom: none;
}

/* ============================================
   USER INFO
   ============================================ */
.user-info {
    display: flex;
    align-items: center;
    gap: 0.875rem;
}

.avatar-circle {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    font-size: 1rem;
    flex-shrink: 0;
}

.user-info strong {
    color: var(--gray-900);
    font-size: 0.9rem;
}

.user-info small {
    color: var(--gray-500);
    font-size: 0.8rem;
}

/* ============================================
   STATUS BADGES
   ============================================ */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.35rem 0.75rem;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 600;
    border: 1px solid;
}

.status-active {
    background: var(--success-bg);
    color: #065f46;
    border-color: var(--success);
}

.status-completed {
    background: var(--info-bg);
    color: #1e40af;
    border-color: var(--info);
}

.status-warning {
    background: var(--warning-bg);
    color: #92400e;
    border-color: var(--warning);
}

.status-badge i {
    font-size: 0.5rem;
}

/* ============================================
   ALERTS
   ============================================ */
.alert-info-dark {
    background: var(--info-bg);
    border: 1px solid var(--info);
    border-radius: 8px;
    padding: 1rem 1.25rem;
    color: #1e40af;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.alert-info-dark i {
    color: var(--info);
}

.alert-info-dark strong {
    font-weight: 600;
}

/* ============================================
   PROGRESS BARS
   ============================================ */
.progress-dark {
    height: 8px;
    background: var(--gray-200);
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar-dracula {
    height: 100%;
    background: linear-gradient(90deg, var(--primary-color) 0%, var(--primary-light) 100%);
    transition: width 0.6s ease;
}

/* ============================================
   POPULAR ITEMS
   ============================================ */
.popular-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 0;
    border-bottom: 1px solid var(--gray-200);
}

.popular-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.popular-item strong {
    color: var(--gray-900);
    font-size: 0.9rem;
    display: block;
    margin-bottom: 0.25rem;
}

.popular-item small {
    color: var(--gray-500);
    font-size: 0.8rem;
}

/* ============================================
   QUICK ACCESS BUTTONS
   ============================================ */
.quick-access-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 2rem 1rem;
    background: var(--white);
    border: 2px solid;
    border-radius: 12px;
    text-decoration: none;
    transition: all 0.2s ease;
    height: 100%;
    min-height: 160px;
    box-shadow: var(--shadow-sm);
}

.quick-purple {
    border-color: #8b5cf6;
    color: #7c3aed;
}

.quick-purple:hover {
    background: #f5f3ff;
    border-color: #7c3aed;
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    color: #7c3aed;
}

.quick-pink {
    border-color: #ec4899;
    color: #db2777;
}

.quick-pink:hover {
    background: #fdf2f8;
    border-color: #db2777;
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    color: #db2777;
}

.quick-cyan {
    border-color: var(--primary-color);
    color: var(--primary-dark);
}

.quick-cyan:hover {
    background: #f0f9ff;
    border-color: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    color: var(--primary-dark);
}

.quick-green {
    border-color: var(--success);
    color: #059669;
}

.quick-green:hover {
    background: #d1fae5;
    border-color: #059669;
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    color: #059669;
}

.quick-access-btn i {
    margin-bottom: 0.75rem;
}

.quick-access-btn strong {
    font-size: 1rem;
    text-align: center;
    line-height: 1.4;
}

/* ============================================
   TEXT UTILITIES
   ============================================ */
.text-muted-dark {
    color: var(--gray-500) !important;
}

.text-light {
    color: var(--gray-700) !important;
}

.dracula-text {
    color: var(--primary-color);
    font-weight: 600;
}

/* ============================================
   EMPTY STATE
   ============================================ */
.empty-state {
    color: var(--gray-500);
    padding: 3rem 1rem;
    text-align: center;
}

.empty-state i {
    color: var(--gray-300);
    margin-bottom: 1rem;
}

.empty-state h5 {
    color: var(--gray-700);
    margin-top: 1rem;
    font-size: 1.1rem;
}

/* ============================================
   SCROLLBAR - Minimalista
   ============================================ */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: var(--gray-100);
}

::-webkit-scrollbar-thumb {
    background: var(--gray-300);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: var(--gray-400);
}

/* ============================================
   RESPONSIVE DESIGN
   ============================================ */
@media (max-width: 768px) {
    .page-header-dark {
        padding: 1.5rem;
    }
    
    .header-title {
        font-size: 1.5rem;
    }
    
    .header-title i {
        font-size: 1.25rem;
    }

    .stat-content h3 {
        font-size: 2rem;
    }

    .stats-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .stat-card {
        padding: 1.25rem;
    }

    .quick-access-btn {
        padding: 1.5rem 1rem;
        min-height: 140px;
    }

    .quick-access-btn i {
        font-size: 1.75rem !important;
    }
    
    .quick-access-btn strong {
        font-size: 0.9rem;
    }

    .card-body-dark {
        padding: 1rem;
    }
    
    .table-dark {
        font-size: 0.85rem;
    }
    
    .table-dark thead th,
    .table-dark tbody td {
        padding: 0.75rem 0.5rem;
    }
    
    .user-info {
        gap: 0.625rem;
    }
    
    .avatar-circle {
        width: 36px;
        height: 36px;
        font-size: 0.875rem;
    }
}

@media (max-width: 576px) {
    .header-title {
        font-size: 1.25rem;
    }
    
    .badge-dracula-lg,
    .badge-warning-lg {
        font-size: 0.85rem;
        padding: 0.5rem 1rem;
    }
    
    .stat-content h3 {
        font-size: 1.75rem;
    }
    
    .card-header-dark h5,
    .card-header-dark h6 {
        font-size: 0.95rem;
    }
}

/* ============================================
   ANIMACIONES SUTILES
   ============================================ */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.stat-card,
.card-dark,
.quick-access-btn {
    animation: fadeIn 0.3s ease-out;
}

/* ============================================
   MEJORAS DE ACCESIBILIDAD
   ============================================ */
.btn-dracula:focus,
.btn-dracula-sm:focus,
.quick-access-btn:focus {
    outline: 2px solid var(--primary-color);
    outline-offset: 2px;
}

.nav-link:focus {
    outline: 2px solid var(--primary-color);
    outline-offset: -2px;
}

/* ============================================
   PRINT STYLES
   ============================================ */
@media print {
    .sidebar,
    .header,
    .sidebar-overlay,
    .btn-dracula,
    .btn-dracula-sm,
    .quick-access-btn {
        display: none !important;
    }
    
    .main-content {
        margin: 0 !important;
        padding: 0 !important;
    }
    
    .card-dark,
    .stat-card {
        page-break-inside: avoid;
        box-shadow: none;
        border: 1px solid #000;
    }
}
</style>
@endpush
@endsection