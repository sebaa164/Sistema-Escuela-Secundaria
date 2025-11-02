@extends('layouts.app')

@section('title', 'Reporte de Calificaciones')
@section('page-title', 'Reporte de Calificaciones')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->

    <!-- Estadísticas Generales -->
    <div class="stats-grid mb-4">
        <div class="stat-card">
            <div class="stat-icon bg-gradient-primary">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $estadisticas['total_calificaciones'] }}</h3>
                <p>Total Calificaciones</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-success">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
                <h3>{{ number_format($estadisticas['promedio_general'], 2) }}</h3>
                <p>Promedio General</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-info">
                <i class="fas fa-check-double"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $estadisticas['aprobados'] }}</h3>
                <p>Aprobados</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-danger">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $estadisticas['reprobados'] }}</h3>
                <p>Reprobados</p>
            </div>
        </div>
    </div>

    <!-- Distribución de Notas -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card-dark">
                <div class="card-header-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Distribución de Notas
                    </h5>
                </div>
                <div class="card-body-dark">
                    <div class="distribution-chart">
                        @foreach($estadisticas['distribucion_notas'] as $rango => $cantidad)
                            @php
                                $porcentaje = $estadisticas['total_calificaciones'] > 0 
                                    ? ($cantidad / $estadisticas['total_calificaciones']) * 100 
                                    : 0;
                            @endphp
                            <div class="distribution-item">
                                <div class="distribution-label">
                                    <span class="range-badge">{{ $rango }}</span>
                                    <span class="count-badge">{{ $cantidad }} estudiantes</span>
                                </div>
                                <div class="distribution-bar">
                                    <div class="distribution-fill {{ $loop->index < 2 ? 'fill-success' : ($loop->index == 2 ? 'fill-warning' : 'fill-danger') }}" 
                                         style="width: {{ $porcentaje }}%">
                                        <span class="percentage-label">{{ number_format($porcentaje, 1) }}%</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card-dark">
                <div class="card-header-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Estadísticas Detalladas
                    </h5>
                </div>
                <div class="card-body-dark">
                    <div class="stats-detail-grid">
                        <div class="stat-detail-item">
                            <div class="stat-detail-icon bg-success">
                                <i class="fas fa-arrow-up"></i>
                            </div>
                            <div class="stat-detail-content">
                                <span class="stat-detail-label">Nota Máxima</span>
                                <span class="stat-detail-value text-success">{{ number_format($estadisticas['nota_maxima'], 2) }}</span>
                            </div>
                        </div>

                        <div class="stat-detail-item">
                            <div class="stat-detail-icon bg-danger">
                                <i class="fas fa-arrow-down"></i>
                            </div>
                            <div class="stat-detail-content">
                                <span class="stat-detail-label">Nota Mínima</span>
                                <span class="stat-detail-value text-danger">{{ number_format($estadisticas['nota_minima'], 2) }}</span>
                            </div>
                        </div>

                        <div class="stat-detail-item">
                            <div class="stat-detail-icon bg-info">
                                <i class="fas fa-percent"></i>
                            </div>
                            <div class="stat-detail-content">
                                <span class="stat-detail-label">Tasa de Aprobación</span>
                                <span class="stat-detail-value text-info">
                                    {{ $estadisticas['total_calificaciones'] > 0 ? number_format(($estadisticas['aprobados'] / $estadisticas['total_calificaciones']) * 100, 2) : 0 }}%
                                </span>
                            </div>
                        </div>

                        <div class="stat-detail-item">
                            <div class="stat-detail-icon bg-warning">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="stat-detail-content">
                                <span class="stat-detail-label">Desempeño</span>
                                <span class="stat-detail-value text-warning">
                                    @if($estadisticas['promedio_general'] >= 85)
                                        Excelente
                                    @elseif($estadisticas['promedio_general'] >= 70)
                                        Bueno
                                    @elseif($estadisticas['promedio_general'] >= 60)
                                        Regular
                                    @else
                                        Bajo
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Principal -->
    <div class="card-dark">
        <div class="card-header-dark">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h5 class="mb-0">
                    <i class="fas fa-clipboard-check me-2"></i>Detalle de Calificaciones
                </h5>
                <div class="d-flex gap-2">
                    <button class="btn-neon-sm" onclick="imprimirReporte()">
                        <i class="fas fa-file-pdf me-2"></i>IMPRIMIR PDF
                    </button>
                    <button class="btn-neon-sm" onclick="exportarExcel()">
                        <i class="fas fa-file-excel me-2"></i>Exportar
                    </button>
                </div>
            </div>
        </div>

        <div class="card-body-dark">
            <!-- Filtros -->
            <form method="GET" action="{{ route('admin.reportes.calificaciones') }}" class="filter-form mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label-dark">Sección</label>
                        <select name="seccion_id" class="form-select-dark">
                            <option value="">Todas las secciones</option>
                            @foreach($secciones as $seccion)
                                <option value="{{ $seccion->id }}" {{ request('seccion_id') == $seccion->id ? 'selected' : '' }}>
                                    {{ $seccion->curso->nombre }} - {{ $seccion->codigo_seccion }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label-dark">Período</label>
                        <select name="periodo_id" class="form-select-dark">
                            <option value="">Todos los períodos</option>
                            @foreach($periodos as $periodo)
                                <option value="{{ $periodo->id }}" {{ request('periodo_id') == $periodo->id ? 'selected' : '' }}>
                                    {{ $periodo->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label-dark">Carrera</label>
                        <select name="carrera" class="form-select-dark">
                            <option value="">Todas las carreras</option>
                            @foreach($carreras as $carrera)
                                <option value="{{ $carrera }}" {{ request('carrera') == $carrera ? 'selected' : '' }}>
                                    {{ $carrera }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label-dark">&nbsp;</label>
                        <button type="submit" class="btn-neon w-100">
                            <i class="fas fa-filter me-2"></i>Filtrar
                        </button>
                    </div>
                </div>
            </form>

            <!-- Tabla de Calificaciones -->
            <div class="table-responsive">
                <table class="table-dark" id="tablaCalificaciones">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Curso</th>
                            <th>Evaluación</th>
                            <th>Nota</th>
                            <th>Porcentaje</th>
                            <th>Puntos</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($calificaciones as $calificacion)
                            <tr>
                                <td>
                                    <div class="student-info">
                                        <div class="student-avatar">
                                            {{ substr($calificacion->estudiante->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <strong>{{ $calificacion->estudiante->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $calificacion->estudiante->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="course-info-compact">
                                        <span class="badge-course">{{ $calificacion->evaluacion->seccion->curso->codigo_curso }}</span>
                                        <small class="d-block text-muted mt-1">{{ $calificacion->evaluacion->seccion->codigo_seccion }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="evaluation-info">
                                        <strong class="text-white">{{ $calificacion->evaluacion->nombre }}</strong>
                                        <br>
                                        <span class="badge-type">
                                            @switch($calificacion->evaluacion->tipo)
                                                @case('examen')
                                                    <i class="fas fa-file-alt"></i> Examen
                                                    @break
                                                @case('tarea')
                                                    <i class="fas fa-tasks"></i> Tarea
                                                    @break
                                                @case('proyecto')
                                                    <i class="fas fa-project-diagram"></i> Proyecto
                                                    @break
                                                @case('parcial')
                                                    <i class="fas fa-clipboard-list"></i> Parcial
                                                    @break
                                                @default
                                                    <i class="fas fa-book"></i> {{ ucfirst($calificacion->evaluacion->tipo) }}
                                            @endswitch
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="grade-display-lg {{ $calificacion->esta_aprobada ? 'grade-pass' : 'grade-fail' }}">
                                        {{ number_format($calificacion->nota, 2) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="percentage-display">
                                        <i class="fas fa-percentage text-cyan"></i>
                                        <strong>{{ number_format($calificacion->evaluacion->porcentaje, 0) }}%</strong>
                                    </div>
                                </td>
                                <td>
                                    <span class="points-badge">
                                        <i class="fas fa-star text-warning"></i>
                                        {{ number_format($calificacion->nota * ($calificacion->evaluacion->porcentaje / 100), 2) }} pts
                                    </span>
                                </td>
                                <td>
                                    @if($calificacion->esta_aprobada)
                                        <span class="status-badge status-approved">
                                            <i class="fas fa-check-circle"></i> Aprobado
                                        </span>
                                    @else
                                        <span class="status-badge status-failed">
                                            <i class="fas fa-times-circle"></i> Reprobado
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ $calificacion->created_at->format('d/m/Y') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-clipboard fa-3x mb-3"></i>
                                        <h5>No se encontraron calificaciones</h5>
                                        <p>Intenta ajustar los filtros de búsqueda</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
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
    .bg-gradient-info { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }
    .bg-gradient-danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }

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

    .card-body-dark { padding: 2rem; }

    /* Distribution Chart */
    .distribution-chart {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .distribution-item {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .distribution-label {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .range-badge {
        background: rgba(0, 212, 255, 0.1);
        border: 1px solid rgba(0, 212, 255, 0.3);
        color: var(--neon-cyan);
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .count-badge {
        color: #94a3b8;
        font-size: 0.85rem;
    }

    .distribution-bar {
        height: 30px;
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
        overflow: hidden;
        position: relative;
    }

    .distribution-fill {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding-right: 1rem;
        transition: width 0.6s ease;
        position: relative;
    }

    .fill-success {
        background: linear-gradient(90deg, #10b981 0%, #059669 100%);
    }

    .fill-warning {
        background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%);
    }

    .fill-danger {
        background: linear-gradient(90deg, #ef4444 0%, #dc2626 100%);
    }

    .percentage-label {
        color: white;
        font-weight: 600;
        font-size: 0.85rem;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    }

    /* Stats Detail Grid */
    .stats-detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .stat-detail-item {
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 12px;
        padding: 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.3s ease;
    }

    .stat-detail-item:hover {
        border-color: var(--neon-cyan);
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.2);
    }

    .stat-detail-icon {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
    }

    .bg-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .bg-danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
    .bg-info { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }
    .bg-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }

    .stat-detail-content {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .stat-detail-label {
        color: #94a3b8;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-detail-value {
        font-size: 1.25rem;
        font-weight: 700;
    }

    .text-success { color: #10b981; }
    .text-danger { color: #ef4444; }
    .text-info { color: #06b6d4; }
    .text-warning { color: #f59e0b; }

    /* Form Controls */
    .form-label-dark {
        color: #e2e8f0;
        font-weight: 500;
        margin-bottom: 0.5rem;
        display: block;
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

    .btn-neon-sm {
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        border: none;
        color: #0f172a;
        font-weight: 600;
        padding: 0.6rem 1.2rem;
        border-radius: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
        font-size: 0.9rem;
        cursor: pointer;
    }

    .btn-neon-sm:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 25px rgba(0, 212, 255, 0.6);
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
    }

    .btn-neon:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 30px rgba(0, 212, 255, 0.6);
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
        font-weight: 700;
        color: #0f172a;
        font-size: 0.9rem;
        text-transform: uppercase;
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.5);
    }

    .badge-course {
        background: rgba(6, 182, 212, 0.2);
        color: #06b6d4;
        border: 1px solid #06b6d4;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-block;
    }

    .badge-type {
        background: rgba(245, 158, 11, 0.2);
        color: #f59e0b;
        border: 1px solid #f59e0b;
        padding: 0.25rem 0.6rem;
        border-radius: 15px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }

    .grade-display-lg {
        display: inline-block;
        padding: 0.6rem 1.2rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1.3rem;
        text-align: center;
        min-width: 80px;
    }

    .grade-pass {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 2px solid #10b981;
        box-shadow: 0 0 15px rgba(16, 185, 129, 0.3);
    }

    .grade-fail {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 2px solid #ef4444;
        box-shadow: 0 0 15px rgba(239, 68, 68, 0.3);
    }

    .percentage-display {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #e2e8f0;
        font-size: 1rem;
    }

    .text-cyan { color: var(--neon-cyan); }

    .points-badge {
        background: rgba(245, 158, 11, 0.1);
        border: 1px solid rgba(245, 158, 11, 0.3);
        color: #e2e8f0;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.4rem 0.9rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-approved {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 1px solid #10b981;
    }

    .status-failed {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 1px solid #ef4444;
    }

    .text-muted { color: #94a3b8; }

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

    @media print {
        .breadcrumb-dark,
        .card-header-dark,
        .filter-form,
        .btn-neon-sm {
            display: none !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function imprimirReporte() {
        const params = new URLSearchParams(window.location.search);
        params.set('tipo', 'calificaciones');
        window.open('{{ route("admin.reportes.exportar-pdf") }}?' + params.toString(), '_blank');
    }

    function exportarExcel() {
        const params = new URLSearchParams(window.location.search);
        params.set('tipo', 'calificaciones');
        window.location.href = '{{ route("admin.reportes.exportar-excel") }}?' + params.toString();
    }
</script>
@endpush
@endsection