@extends('layouts.app')

@section('title', 'Reporte de Estudiantes')
@section('page-title', 'Reporte de Estudiantes')

@section('content')
<div class="container-fluid">
    <!-- Estadísticas Generales -->
    <div class="stats-grid mb-4">
        <div class="stat-card">
            <div class="stat-icon bg-gradient-primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $reporteEstudiantes->count() }}</h3>
                <p>Total Estudiantes</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-success">
                <i class="fas fa-book-open"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $reporteEstudiantes->sum('total_materias') }}</h3>
                <p>Total Inscripciones</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-info">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $reporteEstudiantes->sum('materias_aprobadas') }}</h3>
                <p>Materias Aprobadas</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-warning">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
                <h3>{{ number_format($reporteEstudiantes->avg('promedio_general'), 2) }}</h3>
                <p>Promedio General</p>
            </div>
        </div>
    </div>

    <!-- Card Principal -->
    <div class="card-dark">
        <div class="card-header-dark">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('admin.reportes.index') }}" class="btn-back" title="Volver a Reportes">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h5 class="mb-0">
                        <i class="fas fa-user-graduate me-2"></i>Reporte Detallado de Estudiantes
                    </h5>
                </div>
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
            <form method="GET" action="{{ route('admin.reportes.estudiantes') }}" class="filter-form mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
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

                    <div class="col-md-4">
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
                    
                    <div class="col-md-4">
                        <label class="form-label-dark">&nbsp;</label>
                        <button type="submit" class="btn-neon w-100">
                            <i class="fas fa-filter me-2"></i>Filtrar
                        </button>
                    </div>
                </div>
            </form>

            <!-- Tabla de Estudiantes -->
            <div class="table-responsive">
                <table class="table-dark" id="tablaEstudiantes">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Email</th>
                            <th>Total Materias</th>
                            <th>Aprobadas</th>
                            <th>Reprobadas</th>
                            <th>Horas Semanales</th>
                            <th>Promedio</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reporteEstudiantes as $reporte)
                            <tr>
                                <td>
                                    <div class="student-info">
                                        <div class="student-avatar">
                                            {{ substr($reporte['estudiante']->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <strong class="student-name">{{ $reporte['estudiante']->name }}</strong>
                                            @if($reporte['estudiante']->carrera)
                                                <br>
                                                <small class="student-career">
                                                    <i class="fas fa-graduation-cap"></i> {{ $reporte['estudiante']->carrera }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="email-text">{{ $reporte['estudiante']->email }}</span>
                                </td>
                                <td>
                                    <span class="badge-info">
                                        <i class="fas fa-book me-1"></i>{{ $reporte['total_materias'] }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-success">
                                        <i class="fas fa-check-circle me-1"></i>{{ $reporte['materias_aprobadas'] }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-danger">
                                        <i class="fas fa-times-circle me-1"></i>{{ $reporte['materias_reprobadas'] }}
                                    </span>
                                </td>
                                <td>
                                    <div class="hours-display">
                                        <i class="fas fa-clock text-warning"></i>
                                        <strong class="hours-value">{{ $reporte['horas_semanales_aprobadas'] }}</strong>
                                        <span class="hours-label">hrs</span>
                                    </div>
                                </td>
                                <td>
                                    @if($reporte['promedio_general'])
                                        <div class="grade-display {{ $reporte['promedio_general'] >= 70 ? 'grade-pass' : 'grade-fail' }}">
                                            {{ number_format($reporte['promedio_general'], 2) }}
                                        </div>
                                    @else
                                        <span class="no-data">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $porcentajeAprobacion = $reporte['total_materias'] > 0 
                                            ? ($reporte['materias_aprobadas'] / $reporte['total_materias']) * 100 
                                            : 0;
                                    @endphp
                                    @if($porcentajeAprobacion >= 80)
                                        <span class="status-badge status-excellent">
                                            <i class="fas fa-star"></i> Excelente
                                        </span>
                                    @elseif($porcentajeAprobacion >= 60)
                                        <span class="status-badge status-good">
                                            <i class="fas fa-check"></i> Bueno
                                        </span>
                                    @elseif($porcentajeAprobacion >= 40)
                                        <span class="status-badge status-regular">
                                            <i class="fas fa-minus"></i> Regular
                                        </span>
                                    @else
                                        <span class="status-badge status-critical">
                                            <i class="fas fa-exclamation"></i> Crítico
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-user-slash fa-3x mb-3"></i>
                                        <h5>No se encontraron estudiantes</h5>
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

    /* Botón de Volver */
    .btn-back {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        background: rgba(15, 23, 42, 0.8);
        border: 2px solid rgba(0, 212, 255, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--neon-cyan);
        font-size: 1.2rem;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .btn-back:hover {
        background: rgba(0, 212, 255, 0.1);
        border-color: var(--neon-cyan);
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
        color: var(--neon-cyan);
        transform: translateX(-3px);
    }

    /* Form Controls */
    .form-label-dark {
        color: #e2e8f0;
        font-weight: 500;
        margin-bottom: 0.5rem;
        display: block;
        font-size: 0.9rem;
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

    /* Buttons */
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

    /* Student Info */
    .student-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .student-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #0f172a;
        font-size: 1rem;
        text-transform: uppercase;
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.5);
        flex-shrink: 0;
    }

    .student-name {
        color: #FFFFFF !important;
        font-size: 1rem;
        font-weight: 600;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
    }

    .student-career {
        color: #94a3b8 !important;
        font-size: 0.85rem;
    }

    .email-text {
        color: #06b6d4;
        font-weight: 500;
    }

    /* Badges */
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

    /* Hours Display */
    .hours-display {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: rgba(245, 158, 11, 0.1);
        border: 1px solid rgba(245, 158, 11, 0.3);
        border-radius: 10px;
        width: fit-content;
    }

    .hours-display i {
        font-size: 1.1rem;
    }

    .hours-value {
        color: #FFFFFF;
        font-size: 1.2rem;
        font-weight: 700;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
    }

    .hours-label {
        color: #94a3b8;
        font-size: 0.85rem;
        font-weight: 600;
    }

    /* Grade Display */
    .grade-display {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 10px;
        font-weight: 700;
        font-size: 1.1rem;
    }

    .grade-pass {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 2px solid #10b981;
    }

    .grade-fail {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 2px solid #ef4444;
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
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
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
        .btn-neon-sm,
        .btn-back {
            display: none !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function imprimirReporte() {
        const params = new URLSearchParams(window.location.search);
        params.set('tipo', 'estudiantes');
        window.open('{{ route("admin.reportes.exportar-pdf") }}?' + params.toString(), '_blank');
    }

    function exportarExcel() {
        const params = new URLSearchParams(window.location.search);
        params.set('tipo', 'estudiantes');
        window.location.href = '{{ route("admin.reportes.exportar-excel") }}?' + params.toString();
    }
</script>
@endpush
@endsection