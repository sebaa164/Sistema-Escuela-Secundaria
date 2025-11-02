@extends('layouts.app')

@section('title', 'Historial Académico - Estudiante')
@section('page-title', 'Historial Académico')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header-dark mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-history me-3"></i>Historial Académico
                </h1>
                <p class="header-subtitle mb-0">Tu trayectoria académica completa</p>
            </div>
            <a href="{{ route('estudiante.inscripciones.index') }}" class="btn-outline-neon">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <!-- Resumen General -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-icon bg-primary">
                    <i class="fas fa-book"></i>
                </div>
                <div class="summary-content">
                    <h3>{{ $resumen['total_materias'] }}</h3>
                    <p>Total Materias</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-icon bg-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="summary-content">
                    <h3>{{ $resumen['materias_aprobadas'] }}</h3>
                    <p>Aprobadas</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-icon bg-warning">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="summary-content">
                    <h3>{{ $resumen['creditos_aprobados'] }}</h3>
                    <p>Créditos</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="summary-card">
                <div class="summary-icon bg-info">
                    <i class="fas fa-star"></i>
                </div>
                <div class="summary-content">
                    <h3>{{ number_format($resumen['promedio_general'], 2) }}</h3>
                    <p>Promedio General</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Historial por Período -->
    @foreach($inscripciones as $periodo => $materiasDelPeriodo)
        <div class="card-dark mb-4">
            <div class="card-header-dark">
                <div class="d-flex justify-content-between align-items-center">
                    <h5><i class="fas fa-calendar me-2"></i>{{ $periodo }}</h5>
                    <span class="badge-neon-sm">{{ $materiasDelPeriodo->count() }} materias</span>
                </div>
            </div>
            <div class="card-body-dark">
                <div class="table-responsive">
                    <table class="table-history">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Materia</th>
                                <th>Sección</th>
                                <th>Profesor</th>
                                <th>Créditos</th>
                                <th>Nota Final</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($materiasDelPeriodo as $inscripcion)
                                @php
                                    $estadoClass = '';
                                    $estadoTexto = '';
                                    $estadoIcon = '';
                                    
                                    if ($inscripcion->nota_final !== null) {
                                        if ($inscripcion->esta_aprobado) {
                                            $estadoClass = 'success';
                                            $estadoTexto = 'Aprobado';
                                            $estadoIcon = 'check-circle';
                                        } else {
                                            $estadoClass = 'danger';
                                            $estadoTexto = 'Reprobado';
                                            $estadoIcon = 'times-circle';
                                        }
                                    } else {
                                        switch($inscripcion->estado) {
                                            case 'inscrito':
                                                $estadoClass = 'info';
                                                $estadoTexto = 'En Curso';
                                                $estadoIcon = 'clock';
                                                break;
                                            case 'retirado':
                                                $estadoClass = 'warning';
                                                $estadoTexto = 'Retirado';
                                                $estadoIcon = 'ban';
                                                break;
                                            default:
                                                $estadoClass = 'secondary';
                                                $estadoTexto = ucfirst($inscripcion->estado);
                                                $estadoIcon = 'circle';
                                        }
                                    }
                                @endphp
                                
                                <tr class="history-row">
                                    <td>
                                        <span class="code-badge">{{ $inscripcion->seccion->curso->codigo_curso }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $inscripcion->seccion->curso->nombre }}</strong>
                                    </td>
                                    <td>{{ $inscripcion->seccion->codigo_seccion }}</td>
                                    <td>{{ $inscripcion->seccion->profesor->nombre_completo }}</td>
                                    <td>
                                        <span class="credits-badge">
                                            <i class="fas fa-graduation-cap me-1"></i>
                                            {{ $inscripcion->seccion->curso->creditos }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($inscripcion->nota_final !== null)
                                            <span class="grade-badge {{ $inscripcion->esta_aprobado ? 'pass' : 'fail' }}">
                                                {{ number_format($inscripcion->nota_final, 2) }}
                                            </span>
                                        @else
                                            <span class="grade-badge pending">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="status-badge {{ $estadoClass }}">
                                            <i class="fas fa-{{ $estadoIcon }} me-1"></i>
                                            {{ $estadoTexto }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach

    @if($inscripciones->isEmpty())
        <div class="empty-state-compact">
            <i class="fas fa-history fa-3x mb-3"></i>
            <h5>No hay historial académico</h5>
            <p>Aún no tienes materias cursadas</p>
            <a href="{{ route('estudiante.inscripciones.disponibles') }}" class="btn-outline-neon mt-3">
                <i class="fas fa-plus me-2"></i>Ver Materias Disponibles
            </a>
        </div>
    @endif
</div>

@push('styles')
<style>
    :root {
        --neon-cyan: #00ffff;
        --neon-blue: #00d4ff;
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

    .header-subtitle { color: #cbd5e1; font-weight: 500; }

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
        margin: 0;
    }

    .card-body-dark { padding: 1.5rem; }

    /* Summary Cards */
    .summary-card {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.95) 100%);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 12px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.5rem;
        transition: all 0.3s ease;
    }

    .summary-card:hover {
        border-color: rgba(0, 212, 255, 0.5);
        transform: translateY(-5px);
        box-shadow: 0 0 25px rgba(0, 212, 255, 0.2);
    }

    .summary-icon {
        width: 70px;
        height: 70px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: white;
        flex-shrink: 0;
    }

    .summary-icon.bg-primary { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); box-shadow: 0 0 20px rgba(14, 165, 233, 0.5); }
    .summary-icon.bg-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 0 20px rgba(16, 185, 129, 0.5); }
    .summary-icon.bg-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); box-shadow: 0 0 20px rgba(245, 158, 11, 0.5); }
    .summary-icon.bg-info { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); box-shadow: 0 0 20px rgba(6, 182, 212, 0.5); }

    .summary-content h3 {
        color: var(--neon-cyan);
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .summary-content p {
        color: #cbd5e1;
        margin: 0;
        font-size: 0.875rem;
        font-weight: 500;
    }

    /* Table History */
    .table-responsive {
        overflow-x: auto;
    }

    .table-history {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 0.5rem;
    }

    .table-history thead th {
        color: var(--neon-cyan);
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: uppercase;
        padding: 1rem;
        background: rgba(0, 212, 255, 0.1);
        border: none;
        text-align: left;
    }

    .table-history thead th:first-child {
        border-radius: 10px 0 0 10px;
    }

    .table-history thead th:last-child {
        border-radius: 0 10px 10px 0;
    }

    .history-row {
        transition: all 0.3s ease;
    }

    .history-row:hover {
        transform: translateX(5px);
    }

    .history-row td {
        padding: 1rem;
        background: rgba(15, 23, 42, 0.5);
        border-top: 1px solid rgba(0, 212, 255, 0.2);
        border-bottom: 1px solid rgba(0, 212, 255, 0.2);
        color: #f1f5f9;
    }

    .history-row td:first-child {
        border-left: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px 0 0 10px;
    }

    .history-row td:last-child {
        border-right: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 0 10px 10px 0;
    }

    .code-badge {
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        color: #0f172a;
        padding: 0.25rem 0.75rem;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.75rem;
        box-shadow: 0 0 8px rgba(0, 212, 255, 0.3);
    }

    .credits-badge {
        display: inline-flex;
        align-items: center;
        color: #f59e0b;
        font-weight: 600;
    }

    .grade-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 700;
        font-size: 1rem;
    }

    .grade-badge.pass {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 1px solid #10b981;
    }

    .grade-badge.fail {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 1px solid #ef4444;
    }

    .grade-badge.pending {
        background: rgba(100, 116, 139, 0.2);
        color: #94a3b8;
        border: 1px solid #94a3b8;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.75rem;
    }

    .status-badge.success {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 1px solid #10b981;
    }

    .status-badge.danger {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 1px solid #ef4444;
    }

    .status-badge.info {
        background: rgba(6, 182, 212, 0.2);
        color: #06b6d4;
        border: 1px solid #06b6d4;
    }

    .status-badge.warning {
        background: rgba(245, 158, 11, 0.2);
        color: #f59e0b;
        border: 1px solid #f59e0b;
    }

    .status-badge.secondary {
        background: rgba(100, 116, 139, 0.2);
        color: #94a3b8;
        border: 1px solid #94a3b8;
    }

    .badge-neon-sm {
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        color: #0f172a;
        padding: 0.25rem 0.6rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.7rem;
        box-shadow: 0 0 8px rgba(0, 212, 255, 0.3);
    }

    .btn-outline-neon {
        background: transparent;
        border: 2px solid var(--neon-cyan);
        color: var(--neon-cyan);
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: all 0.3s ease;
    }

    .btn-outline-neon:hover {
        background: rgba(0, 212, 255, 0.1);
        transform: translateY(-2px);
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
        color: var(--neon-cyan);
    }

    .empty-state-compact {
        text-align: center;
        padding: 4rem 2rem;
        color: #94a3b8;
    }

    .empty-state-compact i {
        color: rgba(0, 212, 255, 0.3);
    }

    .empty-state-compact h5 {
        color: #f1f5f9;
        margin: 1rem 0 0.5rem 0;
    }

    .empty-state-compact p {
        color: #cbd5e1;
    }

    @media (max-width: 768px) {
        .summary-card {
            flex-direction: column;
            text-align: center;
        }

        .table-history {
            font-size: 0.875rem;
        }

        .table-history thead th,
        .history-row td {
            padding: 0.75rem 0.5rem;
        }
    }
</style>
@endpush
@endsection
