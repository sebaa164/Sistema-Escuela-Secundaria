@extends('layouts.app')

@section('title', 'Reportes')
@section('page-title', 'Centro de Reportes')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header mb-4">
        <h2 class="page-title">
            <i class="fas fa-chart-line me-3"></i>Centro de Reportes Académicos
        </h2>
        <p class="page-subtitle">Visualiza y genera reportes detallados del sistema</p>
    </div>

    <!-- Grid de Reportes -->
    <div class="reports-grid">
        <!-- Reporte de Estudiantes -->
        <div class="report-card">
            <div class="report-icon bg-gradient-primary">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="report-content">
                <h4>Reporte de Estudiantes</h4>
                <p>Información académica completa de estudiantes, promedios y créditos aprobados</p>
                <div class="report-stats">
                    <span class="stat-item">
                        <i class="fas fa-users"></i>
                        {{ \App\Models\Usuario::estudiantes()->count() }} Estudiantes
                    </span>
                </div>
            </div>
            <div class="report-actions">
                <a href="{{ route('admin.reportes.estudiantes') }}" class="btn-neon-sm">
                    <i class="fas fa-eye me-2"></i>Ver Reporte
                </a>
            </div>
        </div>

        <!-- Reporte de Calificaciones -->
        <div class="report-card">
            <div class="report-icon bg-gradient-success">
                <i class="fas fa-clipboard-check"></i>
            </div>
            <div class="report-content">
                <h4>Reporte de Calificaciones</h4>
                <p>Estadísticas de calificaciones, promedios y distribución de notas por materia</p>
                <div class="report-stats">
                    <span class="stat-item">
                        <i class="fas fa-star"></i>
                        {{ \App\Models\Calificacion::whereNotNull('nota')->count() }} Calificaciones
                    </span>
                </div>
            </div>
            <div class="report-actions">
                <a href="{{ route('admin.reportes.calificaciones') }}" class="btn-neon-sm">
                    <i class="fas fa-eye me-2"></i>Ver Reporte
                </a>
            </div>
        </div>

        <!-- Reporte de Asistencias -->
        <div class="report-card">
            <div class="report-icon bg-gradient-info">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="report-content">
                <h4>Reporte de Asistencias</h4>
                <p>Control de asistencia, porcentajes y registros por estudiante y sección</p>
                <div class="report-stats">
                    <span class="stat-item">
                        <i class="fas fa-check-circle"></i>
                        {{ \App\Models\Asistencia::count() }} Registros
                    </span>
                </div>
            </div>
            <div class="report-actions">
                <a href="{{ route('admin.reportes.asistencias') }}" class="btn-neon-sm">
                    <i class="fas fa-eye me-2"></i>Ver Reporte
                </a>
            </div>
        </div>

        <!-- Reporte de Rendimiento Académico -->
        <div class="report-card">
            <div class="report-icon bg-gradient-warning">
                <i class="fas fa-chart-bar"></i>
            </div>
            <div class="report-content">
                <h4>Rendimiento Académico</h4>
                <p>Análisis de aprobación y reprobación por materia, promedios generales</p>
                <div class="report-stats">
                    <span class="stat-item">
                        <i class="fas fa-graduation-cap"></i>
                        {{ \App\Models\Curso::count() }} Cursos
                    </span>
                </div>
            </div>
            <div class="report-actions">
                <a href="{{ route('admin.reportes.rendimiento') }}" class="btn-neon-sm">
                    <i class="fas fa-eye me-2"></i>Ver Reporte
                </a>
            </div>
        </div>
    </div>

    <!-- Accesos Rápidos -->
    <div class="quick-actions-section mt-5">
        <div class="section-header-custom mb-4">
            <i class="fas fa-bolt me-2"></i>
            <h5>Accesos Rápidos</h5>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="quick-action-card">
                    <div class="quick-icon">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <div class="quick-content">
                        <h6>Exportar Reportes</h6>
                        <p>Genera reportes en PDF o Excel</p>
                    </div>
                    <button class="btn-outline-neon-sm" data-bs-toggle="modal" data-bs-target="#exportModal">
                        <i class="fas fa-download"></i>
                    </button>
                </div>
            </div>

            <div class="col-md-6">
                <div class="quick-action-card">
                    <div class="quick-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="quick-content">
                        <h6>Período Actual</h6>
                        <p>
                            @php
                                $periodoActual = \App\Models\PeriodoAcademico::vigente()->first();
                            @endphp
                            @if($periodoActual)
                                {{ $periodoActual->nombre }}
                            @else
                                No hay período activo
                            @endif
                        </p>
                    </div>
                    <a href="{{ route('admin.periodos.index') }}" class="btn-outline-neon-sm">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Exportación -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-dark">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-download me-2"></i>Exportar Reportes
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="GET" id="exportForm">
                    <div class="mb-3">
                        <label class="form-label-dark">Tipo de Reporte</label>
                        <select name="tipo" class="form-select-dark" required>
                            <option value="">Seleccionar...</option>
                            <option value="estudiantes">Estudiantes</option>
                            <option value="calificaciones">Calificaciones</option>
                            <option value="asistencias">Asistencias</option>
                            <option value="rendimiento">Rendimiento Académico</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label-dark">Formato</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="formato" id="pdf" value="pdf" checked>
                            <label class="btn btn-outline-neon-radio" for="pdf">
                                <i class="fas fa-file-pdf me-2"></i>PDF
                            </label>
                            
                            <input type="radio" class="btn-check" name="formato" id="excel" value="excel">
                            <label class="btn btn-outline-neon-radio" for="excel">
                                <i class="fas fa-file-excel me-2"></i>Excel
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-outline-neon" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <button type="button" class="btn-neon" onclick="exportarReporte()">
                    <i class="fas fa-download me-2"></i>Exportar
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.95) 100%);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 0 30px rgba(0, 212, 255, 0.2);
    }

    .page-title {
        color: var(--neon-cyan);
        font-weight: 700;
        font-size: 2rem;
        margin: 0;
        text-shadow: 0 0 20px rgba(0, 212, 255, 0.5);
        display: flex;
        align-items: center;
    }

    .page-subtitle {
        color: #94a3b8;
        margin: 0.5rem 0 0 0;
        font-size: 1.1rem;
    }

    .reports-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
    }

    .report-card {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.95) 100%);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 15px;
        padding: 1.5rem;
        transition: all 0.3s ease;
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.1);
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .report-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0 40px rgba(0, 212, 255, 0.3);
        border-color: var(--neon-cyan);
    }

    .report-icon {
        width: 70px;
        height: 70px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
    }

    .bg-gradient-primary { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); }
    .bg-gradient-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .bg-gradient-info { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }
    .bg-gradient-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }

    .report-content { flex: 1; }

    .report-content h4 {
        color: var(--neon-cyan);
        font-weight: 600;
        margin-bottom: 0.5rem;
        font-size: 1.25rem;
    }

    .report-content p {
        color: #94a3b8;
        margin-bottom: 1rem;
        font-size: 0.9rem;
        line-height: 1.5;
    }

    .report-stats {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .stat-item {
        background: rgba(0, 212, 255, 0.1);
        border: 1px solid rgba(0, 212, 255, 0.3);
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-size: 0.85rem;
        color: #e2e8f0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .stat-item i { color: var(--neon-cyan); }

    .btn-neon-sm {
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        border: none;
        color: #0f172a;
        font-weight: 600;
        padding: 0.6rem 1.2rem;
        border-radius: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        font-size: 0.9rem;
    }

    .btn-neon-sm:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 25px rgba(0, 212, 255, 0.6);
        color: #0f172a;
    }

    .btn-outline-neon-sm {
        background: transparent;
        border: 2px solid var(--neon-cyan);
        color: var(--neon-cyan);
        font-weight: 600;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }

    .btn-outline-neon-sm:hover {
        background: rgba(0, 212, 255, 0.1);
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
        color: var(--neon-cyan);
    }

    .section-header-custom {
        display: flex;
        align-items: center;
        color: var(--neon-cyan);
        font-size: 1.25rem;
        font-weight: 600;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .quick-actions-section {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.95) 100%);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 0 30px rgba(0, 212, 255, 0.2);
    }

    .quick-action-card {
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 12px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.3s ease;
    }

    .quick-action-card:hover {
        border-color: var(--neon-cyan);
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.2);
    }

    .quick-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: #0f172a;
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.5);
    }

    .quick-content { flex: 1; }

    .quick-content h6 {
        color: #e2e8f0;
        font-weight: 600;
        margin: 0 0 0.25rem 0;
    }

    .quick-content p {
        color: #94a3b8;
        margin: 0;
        font-size: 0.9rem;
    }

    .modal-dark {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.98) 0%, rgba(30, 41, 59, 0.98) 100%);
        border: 1px solid rgba(0, 212, 255, 0.3);
        color: #e2e8f0;
    }

    .modal-dark .modal-header {
        border-bottom: 1px solid rgba(0, 212, 255, 0.2);
    }

    .modal-dark .modal-title {
        color: var(--neon-cyan);
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .modal-dark .modal-footer {
        border-top: 1px solid rgba(0, 212, 255, 0.2);
    }

    .form-label-dark {
        color: #e2e8f0;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .form-select-dark {
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 10px;
        color: #e2e8f0;
        padding: 0.75rem 1rem;
        width: 100%;
    }

    .form-select-dark:focus {
        background: rgba(15, 23, 42, 0.95);
        border-color: var(--neon-cyan);
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
        outline: none;
    }

    .btn-outline-neon-radio {
        background: transparent;
        border: 2px solid rgba(0, 212, 255, 0.3);
        color: #94a3b8;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .btn-check:checked + .btn-outline-neon-radio {
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        border-color: var(--neon-cyan);
        color: #0f172a;
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
    }

    .btn-neon {
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        border: none;
        color: #0f172a;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .btn-neon:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 30px rgba(0, 212, 255, 0.6);
    }

    .btn-outline-neon {
        background: transparent;
        border: 2px solid var(--neon-cyan);
        color: var(--neon-cyan);
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
    }

    .btn-outline-neon:hover {
        background: rgba(0, 212, 255, 0.1);
        color: var(--neon-cyan);
    }
</style>
@endpush

@push('scripts')
<script>
    function exportarReporte() {
        const form = document.getElementById('exportForm');
        const tipo = form.querySelector('[name="tipo"]').value;
        const formato = form.querySelector('[name="formato"]:checked').value;
        
        if (!tipo) {
            alert('Por favor selecciona un tipo de reporte');
            return;
        }
        
        const url = formato === 'pdf' 
            ? `{{ route('admin.reportes.exportar-pdf') }}?tipo=${tipo}`
            : `{{ route('admin.reportes.exportar-excel') }}?tipo=${tipo}`;
        
        window.location.href = url;
        
        setTimeout(() => {
            bootstrap.Modal.getInstance(document.getElementById('exportModal')).hide();
        }, 500);
    }
</script>
@endpush
@endsection