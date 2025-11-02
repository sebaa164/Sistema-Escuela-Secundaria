@extends('layouts.app')

@section('title', 'Reporte de Asistencias')
@section('page-title', 'Reporte de Asistencias')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header-dark mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-chart-bar me-3"></i>
                    Reporte General de Asistencias
                </h1>
                <p class="header-subtitle mb-0">Estadísticas y análisis detallado</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.asistencias.index') }}" class="btn-outline-neon">
                    <i class="fas fa-arrow-left me-2"></i>Volver
                </a>
                <button onclick="window.print()" class="btn-neon">
                    <i class="fas fa-print me-2"></i>Imprimir
                </button>
            </div>
        </div>
    </div>

    <!-- Estadísticas Generales -->
    <div class="stats-grid mb-4">
        <div class="stat-card">
            <div class="stat-icon bg-gradient-primary">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $totalAsistencias ?? 0 }}</h3>
                <p>Total Registros</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $presentes ?? 0 }}</h3>
                <p>Presentes</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $tardanzas ?? 0 }}</h3>
                <p>Tardanzas</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-danger">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $ausentes ?? 0 }}</h3>
                <p>Ausentes</p>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card-dark mb-4">
        <div class="card-header-dark">
            <h5 class="mb-0">
                <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
            </h5>
        </div>
        <div class="card-body-dark">
            <form method="GET" action="{{ route('admin.asistencias.reporte') }}" class="filter-form">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="fecha_inicio" class="form-label-dark">
                            <i class="fas fa-calendar me-2"></i>Fecha Inicio
                        </label>
                        <input type="date" 
                               name="fecha_inicio" 
                               id="fecha_inicio"
                               class="form-control-dark" 
                               value="{{ request('fecha_inicio') }}">
                    </div>

                    <div class="col-md-3">
                        <label for="fecha_fin" class="form-label-dark">
                            <i class="fas fa-calendar me-2"></i>Fecha Fin
                        </label>
                        <input type="date" 
                               name="fecha_fin" 
                               id="fecha_fin"
                               class="form-control-dark" 
                               value="{{ request('fecha_fin') }}">
                    </div>

                    <div class="col-md-3">
                        <label for="estado" class="form-label-dark">
                            <i class="fas fa-list me-2"></i>Estado
                        </label>
                        <select name="estado" id="estado" class="form-select-dark">
                            <option value="">Todos</option>
                            <option value="presente" {{ request('estado') == 'presente' ? 'selected' : '' }}>Presente</option>
                            <option value="tardanza" {{ request('estado') == 'tardanza' ? 'selected' : '' }}>Tardanza</option>
                            <option value="ausente" {{ request('estado') == 'ausente' ? 'selected' : '' }}>Ausente</option>
                            <option value="justificado" {{ request('estado') == 'justificado' ? 'selected' : '' }}>Justificado</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label-dark">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn-neon flex-grow-1">
                                <i class="fas fa-search me-2"></i>Buscar
                            </button>
                            @if(request()->hasAny(['fecha_inicio', 'fecha_fin', 'estado']))
                                <a href="{{ route('admin.asistencias.reporte') }}" class="btn-outline-neon">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Gráfico de Porcentajes -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card-dark">
                <div class="card-header-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>Distribución de Asistencias
                    </h5>
                </div>
                <div class="card-body-dark">
                    <canvas id="asistenciasChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card-dark">
                <div class="card-header-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-percentage me-2"></i>Porcentajes
                    </h5>
                </div>
                <div class="card-body-dark">
                    <div class="progress-item mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-success"><i class="fas fa-check-circle me-2"></i>Presente</span>
                            <strong class="text-success">{{ $porcentajePresentes ?? 0 }}%</strong>
                        </div>
                        <div class="progress-bar-custom">
                            <div class="progress-fill bg-success" style="width: {{ $porcentajePresentes ?? 0 }}%"></div>
                        </div>
                    </div>

                    <div class="progress-item mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-warning"><i class="fas fa-clock me-2"></i>Tardanza</span>
                            <strong class="text-warning">{{ $porcentajeTardanzas ?? 0 }}%</strong>
                        </div>
                        <div class="progress-bar-custom">
                            <div class="progress-fill bg-warning" style="width: {{ $porcentajeTardanzas ?? 0 }}%"></div>
                        </div>
                    </div>

                    <div class="progress-item">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-danger"><i class="fas fa-times-circle me-2"></i>Ausente</span>
                            <strong class="text-danger">{{ $porcentajeAusentes ?? 0 }}%</strong>
                        </div>
                        <div class="progress-bar-custom">
                            <div class="progress-fill bg-danger" style="width: {{ $porcentajeAusentes ?? 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Detalle -->
    <div class="card-dark">
        <div class="card-header-dark">
            <h5 class="mb-0">
                <i class="fas fa-table me-2"></i>Detalle por Fecha
            </h5>
        </div>
        <div class="card-body-dark">
            <div class="table-responsive">
                <table class="table-dark">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Presentes</th>
                            <th>Tardanzas</th>
                            <th>Ausentes</th>
                            <th>Total</th>
                            <th>% Asistencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reportePorFecha ?? [] as $reporte)
                            <tr>
                                <td>{{ $reporte->fecha->format('d/m/Y') }}</td>
                                <td><span class="badge-success">{{ $reporte->presentes }}</span></td>
                                <td><span class="badge-warning">{{ $reporte->tardanzas }}</span></td>
                                <td><span class="badge-danger">{{ $reporte->ausentes }}</span></td>
                                <td><strong>{{ $reporte->total }}</strong></td>
                                <td>
                                    <span class="badge-info">{{ $reporte->porcentaje_asistencia }}%</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                        <h5>No hay datos para mostrar</h5>
                                        <p>Ajusta los filtros para ver los resultados</p>
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
    :root {
        --neon-cyan: #00d4ff;
        --neon-blue: #0ea5e9;
        --dark-bg: #0f172a;
        --dark-card: #1e293b;
        --text-color: #e2e8f0;
        --muted-text: #94a3b8;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
    }

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
        color: var(--muted-text);
        margin-top: 0.5rem;
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
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0 30px rgba(0, 212, 255, 0.3);
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
    .bg-gradient-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .bg-gradient-danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }

    .stat-content h3 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--neon-cyan);
        margin: 0;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .stat-content p {
        color: var(--muted-text);
        margin: 0;
        font-size: 0.875rem;
    }

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

    .card-header-dark h5 {
        color: var(--neon-cyan);
        font-weight: 600;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .card-body-dark {
        padding: 2rem;
    }

    .form-label-dark {
        color: var(--neon-cyan);
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-control-dark, .form-select-dark {
        background-color: var(--dark-card);
        border: 1px solid rgba(0, 212, 255, 0.3);
        color: var(--text-color);
        padding: 0.75rem 1rem;
        border-radius: 8px;
        transition: all 0.3s ease;
        width: 100%;
    }

    .form-control-dark:focus, .form-select-dark:focus {
        background-color: var(--dark-bg);
        border-color: var(--neon-cyan);
        box-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
        outline: none;
        color: var(--text-color);
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
        text-decoration: none;
        display: inline-block;
    }

    .btn-neon:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 30px rgba(0, 212, 255, 0.6);
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

    .progress-bar-custom {
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(0, 212, 255, 0.3);
        height: 25px;
        border-radius: 10px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        transition: width 0.6s ease;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    }

    .progress-fill.bg-success { background: linear-gradient(90deg, #10b981 0%, #059669 100%); }
    .progress-fill.bg-warning { background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%); }
    .progress-fill.bg-danger { background: linear-gradient(90deg, #ef4444 0%, #dc2626 100%); }

    .table-dark {
        width: 100%;
        color: var(--text-color);
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

    .badge-success {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 1px solid #10b981;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-warning {
        background: rgba(245, 158, 11, 0.2);
        color: #f59e0b;
        border: 1px solid #f59e0b;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-danger {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 1px solid #ef4444;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-info {
        background: rgba(6, 182, 212, 0.2);
        color: #06b6d4;
        border: 1px solid #06b6d4;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .empty-state {
        color: var(--muted-text);
        text-align: center;
    }

    .empty-state i {
        color: rgba(0, 212, 255, 0.3);
    }

    .empty-state h5 {
        color: var(--text-color);
        margin: 1rem 0 0.5rem;
    }

    @media print {
        .btn-neon, .btn-outline-neon {
            display: none !important;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('asistenciasChart');
        
        if (ctx) {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Presentes', 'Tardanzas', 'Ausentes'],
                    datasets: [{
                        data: [
                            {{ $presentes ?? 0 }},
                            {{ $tardanzas ?? 0 }},
                            {{ $ausentes ?? 0 }}
                        ],
                        backgroundColor: [
                            'rgba(16, 185, 129, 0.8)',
                            'rgba(245, 158, 11, 0.8)',
                            'rgba(239, 68, 68, 0.8)'
                        ],
                        borderColor: [
                            '#10b981',
                            '#f59e0b',
                            '#ef4444'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#e2e8f0',
                                font: {
                                    size: 14
                                }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection