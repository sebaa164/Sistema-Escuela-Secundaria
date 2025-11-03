@extends('layouts.app')

@section('title', 'Rendimiento Académico')
@section('page-title', 'Rendimiento Académico')

@section('content')
<div class="container-fluid">
    <!-- Estadísticas Generales -->
    <div class="stats-grid mb-4">
        <div class="stat-card">
            <div class="stat-icon bg-gradient-primary">
                <i class="fas fa-book"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $rendimientoPorMateria->count() }}</h3>
                <p>Total Materias</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-success">
                <i class="fas fa-check-double"></i>
            </div>
            <div class="stat-content">
                @php
                    // Filtrar solo valores numéricos válidos
                    $porcentajesValidos = $rendimientoPorMateria
                        ->pluck('porcentaje_aprobacion')
                        ->filter(function($val) { 
                            return $val !== null && is_numeric($val); 
                        });
                    
                    $aprobPromedio = $porcentajesValidos->isNotEmpty() 
                        ? (float) $porcentajesValidos->avg() 
                        : 0;
                @endphp
                <h3>{{ number_format($aprobPromedio, 1) }}%</h3>
                <p>Aprobación Promedio</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-info">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $rendimientoPorMateria->sum('total_estudiantes') }}</h3>
                <p>Total Estudiantes</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-warning">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
                @php
                    // Filtrar solo promedios numéricos válidos
                    $promediosValidos = $rendimientoPorMateria
                        ->pluck('promedio')
                        ->filter(function($val) { 
                            return $val !== null && is_numeric($val) && $val > 0; 
                        });
                    
                    $promedioGeneral = $promediosValidos->isNotEmpty() 
                        ? (float) $promediosValidos->avg() 
                        : 0;
                @endphp
                <h3>{{ number_format($promedioGeneral, 2) }}</h3>
                <p>Promedio General</p>
            </div>
        </div>
    </div>

    <!-- Mejores y Peores Materias -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card-dark">
                <div class="card-header-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-trophy me-2 text-warning"></i>Top 5 - Mejor Rendimiento
                    </h5>
                </div>
                <div class="card-body-dark">
                    <div class="ranking-list">
                        @foreach($rendimientoPorMateria->sortByDesc('porcentaje_aprobacion')->take(5) as $index => $materia)
                            @php 
                                // Asegurar que sea numérico
                                $porcentaje = isset($materia['porcentaje_aprobacion']) && is_numeric($materia['porcentaje_aprobacion'])
                                    ? (float) $materia['porcentaje_aprobacion']
                                    : 0;
                            @endphp
                            <div class="ranking-item">
                                <div class="ranking-position position-{{ $index + 1 }}">
                                    {{ $index + 1 }}
                                </div>
                                <div class="ranking-info">
                                    <h6 class="ranking-title">{{ $materia['curso']->nombre }}</h6>
                                    <span class="ranking-code">{{ $materia['curso']->codigo_curso }}</span>
                                    <span class="ranking-hours">
                                        <i class="fas fa-clock"></i> {{ $materia['curso']->horas_semanales }} hrs/sem
                                    </span>
                                </div>
                                <div class="ranking-stats">
                                    <div class="ranking-percentage success">
                                        {{ number_format($porcentaje, 1) }}%
                                    </div>
                                    <small class="ranking-detail">
                                        {{ $materia['aprobados'] }}/{{ $materia['total_estudiantes'] }}
                                    </small>
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
                        <i class="fas fa-exclamation-triangle me-2 text-danger"></i>Top 5 - Requieren Atención
                    </h5>
                </div>
                <div class="card-body-dark">
                    <div class="ranking-list">
                        @foreach($rendimientoPorMateria->sortBy('porcentaje_aprobacion')->take(5) as $index => $materia)
                            @php 
                                // Asegurar que sea numérico
                                $porcentaje = isset($materia['porcentaje_aprobacion']) && is_numeric($materia['porcentaje_aprobacion'])
                                    ? (float) $materia['porcentaje_aprobacion']
                                    : 0;
                            @endphp
                            <div class="ranking-item">
                                <div class="ranking-position position-danger">
                                    <i class="fas fa-exclamation"></i>
                                </div>
                                <div class="ranking-info">
                                    <h6 class="ranking-title">{{ $materia['curso']->nombre }}</h6>
                                    <span class="ranking-code">{{ $materia['curso']->codigo_curso }}</span>
                                    <span class="ranking-hours">
                                        <i class="fas fa-clock"></i> {{ $materia['curso']->horas_semanales }} hrs/sem
                                    </span>
                                </div>
                                <div class="ranking-stats">
                                    <div class="ranking-percentage danger">
                                        {{ number_format($porcentaje, 1) }}%
                                    </div>
                                    <small class="ranking-detail">
                                        {{ $materia['aprobados'] }}/{{ $materia['total_estudiantes'] }}
                                    </small>
                                </div>
                            </div>
                        @endforeach
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
                    <i class="fas fa-chart-bar me-2"></i>Detalle por Materia
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
            <form method="GET" action="{{ route('admin.reportes.rendimiento') }}" class="filter-form mb-4">
                <div class="row g-3">
                    <div class="col-md-5">
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

                    <div class="col-md-5">
                        <label class="form-label-dark">Período Académico</label>
                        <select name="periodo_id" class="form-select-dark">
                            <option value="">Todos los períodos</option>
                            @foreach($periodos as $periodo)
                                <option value="{{ $periodo->id }}" {{ request('periodo_id') == $periodo->id ? 'selected' : '' }}>
                                    {{ $periodo->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label-dark">&nbsp;</label>
                        <button type="submit" class="btn-neon w-100">
                            <i class="fas fa-filter me-2"></i>Filtrar
                        </button>
                    </div>
                </div>
            </form>

            <!-- Tabla de Rendimiento -->
            <div class="table-responsive">
                <table class="table-dark" id="tablaRendimiento">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Materia</th>
                            <th>Carrera</th>
                            <th>Estudiantes</th>
                            <th>Aprobados</th>
                            <th>Reprobados</th>
                            <th>% Aprobación</th>
                            <th>Promedio</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rendimientoPorMateria as $materia)
                            @php
                                // Asegurar valores numéricos con valores por defecto seguros
                                $porcentaje = isset($materia['porcentaje_aprobacion']) && is_numeric($materia['porcentaje_aprobacion'])
                                    ? (float) $materia['porcentaje_aprobacion']
                                    : 0;
                                
                                $promedioVal = isset($materia['promedio']) && is_numeric($materia['promedio']) && $materia['promedio'] > 0
                                    ? (float) $materia['promedio']
                                    : null;
                            @endphp
                            <tr>
                                <td>
                                    <span class="badge-neon">{{ $materia['curso']->codigo_curso }}</span>
                                </td>
                                <td>
                                    <div class="course-info-detail">
                                        <strong class="course-name">{{ $materia['curso']->nombre }}</strong>
                                        <div class="course-meta">
                                            <span class="meta-item">
                                                <i class="fas fa-clock text-warning"></i>
                                                {{ $materia['curso']->horas_semanales }} hrs/sem
                                            </span>
                                            @if($materia['curso']->nivel)
                                                <span class="meta-item">
                                                    <i class="fas fa-layer-group text-info"></i>
                                                    {{ $materia['curso']->nivel }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-career">
                                        <i class="fas fa-graduation-cap me-1"></i>
                                        {{ $materia['curso']->carrera }}
                                    </span>
                                </td>
                                <td>
                                    <div class="student-count">
                                        <i class="fas fa-users text-cyan"></i>
                                        <strong class="count-value">{{ $materia['total_estudiantes'] }}</strong>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-success-lg">
                                        <i class="fas fa-check-circle me-1"></i>{{ $materia['aprobados'] }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-danger-lg">
                                        <i class="fas fa-times-circle me-1"></i>{{ $materia['reprobados'] }}
                                    </span>
                                </td>
                                <td>
                                    <div class="approval-rate">
                                        <div class="approval-bar-container">
                                            <div class="approval-bar {{ $porcentaje >= 80 ? 'bar-excellent' : ($porcentaje >= 70 ? 'bar-good' : ($porcentaje >= 60 ? 'bar-regular' : 'bar-poor')) }}" 
                                                 style="width: {{ $porcentaje }}%">
                                            </div>
                                        </div>
                                        <span class="approval-text">{{ number_format($porcentaje, 1) }}%</span>
                                    </div>
                                </td>
                                <td>
                                    @if($promedioVal !== null)
                                        <div class="grade-display {{ $promedioVal >= 85 ? 'grade-excellent' : ($promedioVal >= 70 ? 'grade-good' : 'grade-low') }}">
                                            {{ number_format($promedioVal, 2) }}
                                        </div>
                                    @else
                                        <span class="no-data">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($porcentaje >= 85)
                                        <span class="status-badge status-excellent">
                                            <i class="fas fa-star"></i> Excelente
                                        </span>
                                    @elseif($porcentaje >= 70)
                                        <span class="status-badge status-good">
                                            <i class="fas fa-thumbs-up"></i> Bueno
                                        </span>
                                    @elseif($porcentaje >= 60)
                                        <span class="status-badge status-regular">
                                            <i class="fas fa-minus-circle"></i> Regular
                                        </span>
                                    @else
                                        <span class="status-badge status-critical">
                                            <i class="fas fa-exclamation-triangle"></i> Crítico
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-chart-bar fa-3x mb-3"></i>
                                        <h5>No se encontraron datos de rendimiento</h5>
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

    .bg-gradient-primary { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); }
    .bg-gradient-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .bg-gradient-info { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }
    .bg-gradient-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }

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

    /* Ranking List - MEJORADO */
    .ranking-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .ranking-item {
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 12px;
        padding: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.3s ease;
    }

    .ranking-item:hover {
        border-color: var(--neon-cyan);
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.2);
        transform: translateX(5px);
    }

    .ranking-position {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.25rem;
        color: white;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
        flex-shrink: 0;
    }

    .position-1 { background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); color: #0f172a; }
    .position-2 { background: linear-gradient(135deg, #C0C0C0 0%, #A8A8A8 100%); color: #0f172a; }
    .position-3 { background: linear-gradient(135deg, #CD7F32 0%, #B87333 100%); color: white; }
    .position-4, .position-5 { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }
    .position-danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }

    .ranking-info {
        flex: 1;
    }

    .ranking-title {
        color: #FFFFFF;
        font-weight: 600;
        margin: 0 0 0.5rem 0;
        font-size: 0.95rem;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
    }

    .ranking-code {
        background: rgba(0, 212, 255, 0.1);
        border: 1px solid rgba(0, 212, 255, 0.3);
        color: var(--neon-cyan);
        padding: 0.15rem 0.5rem;
        border-radius: 10px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-right: 0.5rem;
    }

    .ranking-hours {
        color: #94a3b8;
        font-size: 0.8rem;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    .ranking-hours i {
        color: #f59e0b;
    }

    .ranking-stats {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 0.25rem;
    }

    .ranking-percentage {
        font-size: 1.5rem;
        font-weight: 700;
        padding: 0.25rem 0.75rem;
        border-radius: 8px;
    }

    .ranking-percentage.success {
        color: #10b981;
        background: rgba(16, 185, 129, 0.1);
    }

    .ranking-percentage.danger {
        color: #ef4444;
        background: rgba(239, 68, 68, 0.1);
    }

    .ranking-detail {
        color: #94a3b8;
    }

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
        white-space: nowrap;
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
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
        box-shadow: 0 0 10px rgba(0, 212, 255, 0.3);
        display: inline-block;
    }

    /* Course Info Detail - MEJORADO */
    .course-info-detail {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .course-name {
        color: #FFFFFF;
        font-weight: 600;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
    }

    .course-meta {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .meta-item {
        color: #94a3b8;
        font-size: 0.8rem;
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .badge-career {
        background: rgba(6, 182, 212, 0.2);
        color: #06b6d4;
        border: 1px solid #06b6d4;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        white-space: nowrap;
    }

    .student-count {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 1.1rem;
    }

    .count-value {
        color: #FFFFFF;
        font-weight: 700;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
    }

    .text-cyan { 
        color: var(--neon-cyan); 
    }

    .badge-success-lg {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 1px solid #10b981;
        padding: 0.4rem 0.9rem;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
    }

    .badge-danger-lg {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 1px solid #ef4444;
        padding: 0.4rem 0.9rem;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
    }

    /* Approval Rate */
    .approval-rate {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        min-width: 120px;
    }

    .approval-bar-container {
        height: 25px;
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 12px;
        overflow: hidden;
        position: relative;
    }

    .approval-bar {
        height: 100%;
        transition: width 0.6s ease;
        border-radius: 12px;
    }

    .bar-excellent {
        background: linear-gradient(90deg, #10b981 0%, #059669 100%);
        box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
    }

    .bar-good {
        background: linear-gradient(90deg, #06b6d4 0%, #0891b2 100%);
        box-shadow: 0 0 10px rgba(6, 182, 212, 0.5);
    }

    .bar-regular {
        background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%);
        box-shadow: 0 0 10px rgba(245, 158, 11, 0.5);
    }

    .bar-poor {
        background: linear-gradient(90deg, #ef4444 0%, #dc2626 100%);
        box-shadow: 0 0 10px rgba(239, 68, 68, 0.5);
    }

    .approval-text {
        color: var(--neon-cyan);
        font-weight: 700;
        font-size: 0.95rem;
        text-align: center;
        display: block;
    }

    /* Grade Display */
    .grade-display {
        display: inline-block;
        padding: 0.6rem 1rem;
        border-radius: 10px;
        font-weight: 700;
        font-size: 1.2rem;
        text-align: center;
        min-width: 70px;
    }

    .grade-excellent {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 2px solid #10b981;
        box-shadow: 0 0 15px rgba(16, 185, 129, 0.3);
    }

    .grade-good {
        background: rgba(6, 182, 212, 0.2);
        color: #06b6d4;
        border: 2px solid #06b6d4;
        box-shadow: 0 0 15px rgba(6, 182, 212, 0.3);
    }

    .grade-low {
        background: rgba(245, 158, 11, 0.2);
        color: #f59e0b;
        border: 2px solid #f59e0b;
        box-shadow: 0 0 15px rgba(245, 158, 11, 0.3);
    }

    .no-data {
        color: #64748b;
        font-style: italic;
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
        white-space: nowrap;
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

    .status-critical {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 1px solid #ef4444;
    }

    .text-warning { color: #f59e0b; }
    .text-info { color: #06b6d4; }

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

    @media print {
        .card-header-dark,
        .filter-form,
        .btn-neon-sm {
            display: none !important;
        }
    }

    @media (max-width: 1200px) {
        .table-dark {
            font-size: 0.85rem;
        }

        .table-dark thead th,
        .table-dark tbody td {
            padding: 0.75rem 0.5rem;
        }

        .ranking-item {
            flex-wrap: wrap;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function imprimirReporte() {
        const params = new URLSearchParams(window.location.search);
        params.set('tipo', 'rendimiento');
        window.open('{{ route("admin.reportes.exportar-pdf") }}?' + params.toString(), '_blank');
    }

    function exportarExcel() {
        const params = new URLSearchParams(window.location.search);
        params.set('tipo', 'rendimiento');
        window.location.href = '{{ route("admin.reportes.exportar-excel") }}?' + params.toString();
    }
</script>
@endpush
@endsection