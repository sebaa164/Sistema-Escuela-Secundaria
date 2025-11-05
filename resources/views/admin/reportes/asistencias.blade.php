@extends('layouts.app')

@section('title', 'Reporte de Asistencias')
@section('page-title', 'Reporte de Asistencias')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->

    <!-- Estadísticas Generales -->
    <div class="stats-grid mb-4">
        <div class="stat-card">
            <div class="stat-icon bg-gradient-primary">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $estadisticas['total_registros'] }}</h3>
                <p>Total Registros</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $estadisticas['presentes'] }}</h3>
                <p>Presentes</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-danger">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $estadisticas['ausentes'] }}</h3>
                <p>Ausentes</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-info">
                <i class="fas fa-percent"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $estadisticas['porcentaje_asistencia'] }}%</h3>
                <p>Asistencia Promedio</p>
            </div>
        </div>
    </div>

    <!-- Gráfico de Distribución -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card-dark">
                <div class="card-header-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Distribución de Asistencia
                    </h5>
                </div>
                <div class="card-body-dark">
                    <div class="attendance-chart">
                        @php
                            $total = $estadisticas['total_registros'];
                            $items = [
                                ['label' => 'Presentes', 'count' => $estadisticas['presentes'], 'class' => 'chart-success', 'icon' => 'check-circle'],
                                ['label' => 'Ausentes', 'count' => $estadisticas['ausentes'], 'class' => 'chart-danger', 'icon' => 'times-circle'],
                                ['label' => 'Tardanzas', 'count' => $estadisticas['tardanzas'], 'class' => 'chart-warning', 'icon' => 'clock'],
                                ['label' => 'Justificadas', 'count' => $estadisticas['justificadas'], 'class' => 'chart-info', 'icon' => 'file-alt']
                            ];
                        @endphp
                        
                        @foreach($items as $item)
                            @php
                                $porcentaje = $total > 0 ? ($item['count'] / $total) * 100 : 0;
                            @endphp
                            <div class="chart-item">
                                <div class="chart-label">
                                    <span class="chart-icon {{ $item['class'] }}">
                                        <i class="fas fa-{{ $item['icon'] }}"></i>
                                    </span>
                                    <span class="chart-text">{{ $item['label'] }}</span>
                                    <span class="chart-count">{{ $item['count'] }}</span>
                                </div>
                                <div class="chart-bar">
                                    <div class="chart-fill {{ $item['class'] }}" style="width: {{ $porcentaje }}%">
                                        <span class="chart-percentage">{{ number_format($porcentaje, 1) }}%</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card-dark">
                <div class="card-header-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Resumen
                    </h5>
                </div>
                <div class="card-body-dark">
                    <div class="summary-list">
                        <div class="summary-item">
                            <div class="summary-icon bg-success">
                                <i class="fas fa-thumbs-up"></i>
                            </div>
                            <div class="summary-content">
                                <span class="summary-label">Mejor Asistencia</span>
                                <span class="summary-value text-success">
                                    @if($reportePorEstudiante->isNotEmpty())
                                        {{ number_format($reportePorEstudiante->max('porcentaje'), 1) }}%
                                    @else
                                        N/A
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="summary-item">
                            <div class="summary-icon bg-danger">
                                <i class="fas fa-thumbs-down"></i>
                            </div>
                            <div class="summary-content">
                                <span class="summary-label">Menor Asistencia</span>
                                <span class="summary-value text-danger">
                                    @if($reportePorEstudiante->isNotEmpty())
                                        {{ number_format($reportePorEstudiante->min('porcentaje'), 1) }}%
                                    @else
                                        N/A
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="summary-item">
                            <div class="summary-icon bg-warning">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="summary-content">
                                <span class="summary-label">Total Tardanzas</span>
                                <span class="summary-value text-warning">{{ $estadisticas['tardanzas'] }}</span>
                            </div>
                        </div>

                        <div class="summary-item">
                            <div class="summary-icon bg-info">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="summary-content">
                                <span class="summary-label">Justificadas</span>
                                <span class="summary-value text-info">{{ $estadisticas['justificadas'] }}</span>
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
                    <i class="fas fa-list-alt me-2"></i>Reporte por Estudiante
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
            <form method="GET" action="{{ route('admin.reportes.asistencias') }}" class="filter-form mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
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
                        <label class="form-label-dark">Fecha Inicio</label>
                        <input type="date" 
                               name="fecha_inicio" 
                               class="form-control-dark" 
                               value="{{ request('fecha_inicio', $fechaInicio) }}">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label-dark">Fecha Fin</label>
                        <input type="date" 
                               name="fecha_fin" 
                               class="form-control-dark" 
                               value="{{ request('fecha_fin', $fechaFin) }}">
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label-dark">&nbsp;</label>
                        <button type="submit" class="btn-neon w-100">
                            <i class="fas fa-filter me-2"></i>Filtrar
                        </button>
                    </div>
                </div>
            </form>

            <!-- Tabla de Asistencias por Estudiante -->
            <div class="table-responsive">
                <table class="table-dark" id="tablaAsistencias">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Total Días</th>
                            <th>Presentes</th>
                            <th>Ausentes</th>
                            <th>Tardanzas</th>
                            <th>Justificadas</th>
                            <th>Porcentaje</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reportePorEstudiante as $reporte)
                            <tr>
                                <td>
                                    <div class="student-info">
                                        <div class="student-avatar">
                                            {{ substr($reporte['estudiante']->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <strong>{{ $reporte['estudiante']->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $reporte['estudiante']->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-info">
                                        <i class="fas fa-calendar me-1"></i>{{ $reporte['total_dias'] }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-success">
                                        <i class="fas fa-check-circle me-1"></i>{{ $reporte['presentes'] }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-danger">
                                        <i class="fas fa-times-circle me-1"></i>{{ $reporte['ausentes'] }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-warning">
                                        <i class="fas fa-clock me-1"></i>{{ $reporte['tardanzas'] }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-secondary">
                                        <i class="fas fa-file-alt me-1"></i>{{ $reporte['justificadas'] }}
                                    </span>
                                </td>
                                <td>
                                    <div class="progress-circle" data-percentage="{{ $reporte['porcentaje'] }}">
                                        <svg width="60" height="60">
                                            <circle cx="30" cy="30" r="25" class="progress-bg"></circle>
                                            <circle cx="30" cy="30" r="25" class="progress-bar {{ $reporte['porcentaje'] >= 80 ? 'progress-success' : ($reporte['porcentaje'] >= 60 ? 'progress-warning' : 'progress-danger') }}" 
                                                    style="stroke-dasharray: {{ $reporte['porcentaje'] * 1.57 }} 157"></circle>
                                            <text x="30" y="35" class="progress-text">{{ number_format($reporte['porcentaje'], 0) }}%</text>
                                        </svg>
                                    </div>
                                </td>
                                <td>
                                    @if($reporte['porcentaje'] >= 90)
                                        <span class="status-badge status-excellent">
                                            <i class="fas fa-star"></i> Excelente
                                        </span>
                                    @elseif($reporte['porcentaje'] >= 80)
                                        <span class="status-badge status-good">
                                            <i class="fas fa-check"></i> Bueno
                                        </span>
                                    @elseif($reporte['porcentaje'] >= 70)
                                        <span class="status-badge status-regular">
                                            <i class="fas fa-minus"></i> Regular
                                        </span>
                                    @elseif($reporte['porcentaje'] >= 60)
                                        <span class="status-badge status-warning">
                                            <i class="fas fa-exclamation"></i> Deficiente
                                        </span>
                                    @else
                                        <span class="status-badge status-critical">
                                            <i class="fas fa-ban"></i> Crítico
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                        <h5>No se encontraron registros de asistencia</h5>
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

    /* Attendance Chart */
    .attendance-chart {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .chart-item {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .chart-label {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .chart-icon {
        width: 35px;
        height: 35px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
    }

    .chart-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .chart-danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
    .chart-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .chart-info { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }

    .chart-text {
        color: #e2e8f0;
        font-weight: 600;
        flex: 1;
    }

    .chart-count {
        color: var(--neon-cyan);
        font-weight: 700;
        font-size: 1.1rem;
    }

    .chart-bar {
        height: 35px;
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
        overflow: hidden;
        position: relative;
    }

    .chart-fill {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding-right: 1rem;
        transition: width 0.6s ease;
    }

    .chart-percentage {
        color: white;
        font-weight: 700;
        font-size: 0.9rem;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.5);
    }

    /* Summary List */
    .summary-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .summary-item {
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 12px;
        padding: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.3s ease;
    }

    .summary-item:hover {
        border-color: var(--neon-cyan);
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.2);
    }

    .summary-icon {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
    }

    .summary-content {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        flex: 1;
    }

    .summary-label {
        color: #94a3b8;
        font-size: 0.85rem;
    }

    .summary-value {
        font-size: 1.5rem;
        font-weight: 700;
    }

    .text-success { color: #10b981; }
    .text-danger { color: #ef4444; }
    .text-warning { color: #f59e0b; }
    .text-info { color: #06b6d4; }

    /* Form Controls */
    .form-label-dark {
        color: #e2e8f0;
        font-weight: 500;
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-select-dark, .form-control-dark {
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 10px;
        color: #e2e8f0;
        padding: 0.75rem 1rem;
        width: 100%;
        transition: all 0.3s ease;
    }

    .form-select-dark:focus, .form-control-dark:focus {
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

    .badge-info {
        background: rgba(6, 182, 212, 0.2);
        color: #06b6d4;
        border: 1px solid #06b6d4;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
    }

    .badge-success {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 1px solid #10b981;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
    }

    .badge-danger {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 1px solid #ef4444;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
    }

    .badge-warning {
        background: rgba(245, 158, 11, 0.2);
        color: #f59e0b;
        border: 1px solid #f59e0b;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
    }

    .badge-secondary {
        background: rgba(100, 116, 139, 0.2);
        color: #94a3b8;
        border: 1px solid #64748b;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
    }

    /* Progress Circle */
    .progress-circle {
        display: flex;
        justify-content: center;
    }

    .progress-bg {
        fill: none;
        stroke: rgba(15, 23, 42, 0.8);
        stroke-width: 4;
    }

    .progress-bar {
        fill: none;
        stroke-width: 4;
        stroke-linecap: round;
        transform: rotate(-90deg);
        transform-origin: 50% 50%;
        transition: stroke-dasharray 0.6s ease;
    }

    .progress-success { stroke: #10b981; }
    .progress-warning { stroke: #f59e0b; }
    .progress-danger { stroke: #ef4444; }

    .progress-text {
        font-size: 10px;
        font-weight: 700;
        fill: #e2e8f0;
        text-anchor: middle;
    }

    /* Status Badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.4rem 0.9rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-excellent {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 1px solid #10b981;
    }

    .status-good {
        background: rgba(6, 182, 212, 0.2);
        color: #06b6d4;
        border: 1px solid #06b6d4;
    }

    .status-regular {
        background: rgba(245, 158, 11, 0.2);
        color: #f59e0b;
        border: 1px solid #f59e0b;
    }

    .status-warning {
        background: rgba(249, 115, 22, 0.2);
        color: #f97316;
        border: 1px solid #f97316;
    }

    .status-critical {
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
        params.set('tipo', 'asistencias');
        window.open('{{ route("admin.reportes.exportar-pdf") }}?' + params.toString(), '_blank');
    }

    function exportarExcel() {
        const params = new URLSearchParams(window.location.search);
        params.set('tipo', 'asistencias');
        window.location.href = '{{ route("admin.reportes.exportar-excel") }}?' + params.toString();
    }
</script>
@endpush
@endsection