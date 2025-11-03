<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Rendimiento Academico</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 9pt;
            color: #E2E8F0;
            line-height: 1.4;
            background: #0F172A;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding: 25px 20px;
            background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%);
            border-radius: 8px;
            border: 2px solid #38BDF8;
            box-shadow: 0 4px 6px rgba(0, 245, 255, 0.1);
        }
        
        .header h1 {
            color: #00F5FF;
            font-size: 24pt;
            margin-bottom: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 0 0 20px rgba(0, 245, 255, 0.5);
        }
        
        .header p {
            color: #38BDF8;
            font-size: 10pt;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        
        .stat-box {
            display: table-cell;
            width: 25%;
            padding: 12px;
            text-align: center;
            border: 3px solid #334155;
            background: #1E293B;
        }
        
        .stat-box:not(:last-child) {
            border-right: none;
        }
        
        .stat-box h3 {
            font-size: 18pt;
            color: #14F195;
            margin-bottom: 4px;
            font-weight: bold;
        }
        
        .stat-box p {
            font-size: 8pt;
            color: #38BDF8;
            text-transform: uppercase;
            font-weight: 600;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        thead {
            background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%);
            color: #00F5FF;
        }
        
        thead th {
            padding: 10px 6px;
            text-align: left;
            font-size: 8.5pt;
            font-weight: bold;
            border: 2px solid #334155;
        }
        
        tbody td {
            padding: 8px 6px;
            border: 1px solid #334155;
            font-size: 8pt;
            background: #1E293B;
            color: #E2E8F0;
        }
        
        tbody tr:nth-child(even) td {
            background: #0F172A;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 7.5pt;
            font-weight: bold;
            text-align: center;
        }
        
        .badge-excelente {
            background: #14F195;
            color: #0F172A;
        }
        
        .badge-bueno {
            background: #00F5FF;
            color: #0F172A;
        }
        
        .badge-regular {
            background: #38BDF8;
            color: #0F172A;
        }
        
        .badge-critico {
            background: #64748B;
            color: #FFFFFF;
        }
        
        .text-center {
            text-align: center;
        }
        
        .progress-container {
            width: 100%;
            background: #0F172A;
            border-radius: 6px;
            padding: 2px;
        }
        
        .progress-bar {
            height: 10px;
            border-radius: 4px;
            text-align: center;
            font-size: 7pt;
            font-weight: bold;
            color: #0F172A;
            line-height: 10px;
        }
        
        .progress-excelente {
            background: #14F195;
        }
        
        .progress-bueno {
            background: #00F5FF;
        }
        
        .progress-regular {
            background: #38BDF8;
        }
        
        .progress-critico {
            background: #64748B;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8pt;
            color: #38BDF8;
            padding: 12px 0;
            border-top: 2px solid #38BDF8;
            background: #0F172A;
        }
        
        .page-number:after {
            content: counter(page);
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>REPORTE DE RENDIMIENTO ACADÉMICO</h1>
        <p>Generado el {{ date('d/m/Y H:i:s') }}</p>
    </div>

    <div class="stats-grid">
        <div class="stat-box">
            <h3>{{ $rendimientoPorMateria->count() }}</h3>
            <p>Materias</p>
        </div>
        <div class="stat-box">
            <h3>{{ $rendimientoPorMateria->sum('total_estudiantes') }}</h3>
            <p>Estudiantes</p>
        </div>
        <div class="stat-box">
            <h3>{{ number_format($rendimientoPorMateria->avg('porcentaje_aprobacion'), 1) }}%</h3>
            <p>% Aprobación</p>
        </div>
        <div class="stat-box">
            <h3>{{ number_format($rendimientoPorMateria->where('promedio', '!=', null)->avg('promedio'), 2) }}</h3>
            <p>Promedio</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 8%;">Código</th>
                <th style="width: 20%;">Materia</th>
                <th style="width: 12%;">Carrera</th>
                <th class="text-center" style="width: 6%;">Nivel</th>
                <th class="text-center" style="width: 6%;">Hrs</th>
                <th class="text-center" style="width: 8%;">Est.</th>
                <th class="text-center" style="width: 7%;">Aprob.</th>
                <th class="text-center" style="width: 7%;">Reprob.</th>
                <th class="text-center" style="width: 10%;">% Aprob.</th>
                <th class="text-center" style="width: 7%;">Prom.</th>
                <th class="text-center" style="width: 9%;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rendimientoPorMateria as $materia)
                @php
                    $porcentaje = $materia['porcentaje_aprobacion'];
                    $progressClass = $porcentaje >= 85 ? 'progress-excelente' : 
                                   ($porcentaje >= 70 ? 'progress-bueno' : 
                                   ($porcentaje >= 60 ? 'progress-regular' : 'progress-critico'));
                    
                    $badgeClass = $porcentaje >= 85 ? 'badge-excelente' : 
                                 ($porcentaje >= 70 ? 'badge-bueno' : 
                                 ($porcentaje >= 60 ? 'badge-regular' : 'badge-critico'));
                    
                    $estado = $porcentaje >= 85 ? 'Excelente' : 
                             ($porcentaje >= 70 ? 'Bueno' : 
                             ($porcentaje >= 60 ? 'Regular' : 'Crítico'));
                @endphp
                <tr>
                    <td><strong style="color: #00F5FF;">{{ $materia['curso']->codigo_curso }}</strong></td>
                    <td style="font-size: 7.5pt; font-weight: 500;">{{ $materia['curso']->nombre }}</td>
                    <td style="font-size: 7pt; color: #38BDF8;">{{ $materia['curso']->carrera }}</td>
                    <td class="text-center" style="font-weight: 600;">{{ $materia['curso']->nivel ?? 'N/A' }}</td>
                    <td class="text-center">{{ $materia['curso']->horas_semanales }}</td>
                    <td class="text-center"><strong>{{ $materia['total_estudiantes'] }}</strong></td>
                    <td class="text-center" style="color: #14F195; font-weight: 600;">{{ $materia['aprobados'] }}</td>
                    <td class="text-center" style="color: #F87171; font-weight: 600;">{{ $materia['reprobados'] }}</td>
                    <td class="text-center">
                        <div class="progress-container">
                            <div class="progress-bar {{ $progressClass }}" style="width: {{ $porcentaje }}%">
                                {{ number_format($porcentaje, 1) }}%
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <strong style="color: #00F5FF;">
                            {{ $materia['promedio'] ? number_format($materia['promedio'], 2) : 'N/A' }}
                        </strong>
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $badgeClass }}">{{ $estado }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center" style="color: #38BDF8; padding: 20px;">No hay datos disponibles</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Colegio Secundario Augusto Pulenta | Página <span class="page-number"></span>
    </div>
</body>
</html>

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
                    $aprobProm = $rendimientoPorMateria->pluck('porcentaje_aprobacion')
                        ->filter(function($val) { return is_numeric($val); })
                        ->avg();
                    $aprobPromedio = is_numeric($aprobProm) ? (float) $aprobProm : 0;
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
                    $promGen = $rendimientoPorMateria->pluck('promedio')
                        ->filter(function($val) { return is_numeric($val) && $val !== null; })
                        ->avg();
                    $promedioGeneral = is_numeric($promGen) ? (float) $promGen : 0;
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
                                $porcentajeAprobacion = $materia['porcentaje_aprobacion'] ?? 0;
                                $porcentaje = is_numeric($porcentajeAprobacion) ? (float) $porcentajeAprobacion : 0; 
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
                                $porcentajeAprobacion = $materia['porcentaje_aprobacion'] ?? 0;
                                $porcentaje = is_numeric($porcentajeAprobacion) ? (float) $porcentajeAprobacion : 0; 
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
                                $porcentajeAprobacion = $materia['porcentaje_aprobacion'] ?? 0;
                                $porcentaje = is_numeric($porcentajeAprobacion) ? (float) $porcentajeAprobacion : 0;
                                
                                $promedioMateria = $materia['promedio'] ?? null;
                                $promedioVal = is_numeric($promedioMateria) ? (float) $promedioMateria : null;
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
    /* ... (Aquí va todo el CSS que ya tenías, lo mantengo igual) ... */
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