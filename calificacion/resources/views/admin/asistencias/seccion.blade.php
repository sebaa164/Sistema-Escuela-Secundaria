@extends('layouts.app')

@section('title', 'Asistencias - ' . $seccion->curso->nombre)
@section('page-title', $seccion->curso->nombre . ' - ' . $seccion->codigo_seccion)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header-dark mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-clipboard-check me-3"></i>Asistencias - {{ $seccion->curso->nombre }}
                </h1>
                <p class="header-subtitle mb-0">
                    Sección: {{ $seccion->codigo_seccion }} | Período: {{ $seccion->periodo->nombre ?? 'Sin período' }}
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.asistencias.index') }}" class="btn-outline-neon">
                    <i class="fas fa-arrow-left me-2"></i>Volver a Materias
                </a>
                <a href="{{ route('admin.asistencias.create', ['seccion_id' => $seccion->id]) }}" class="btn-neon">
                    <i class="fas fa-plus me-2"></i>Tomar Asistencia
                </a>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="stats-grid mb-4">
        <div class="stat-card">
            <div class="stat-icon bg-gradient-primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $estudiantes->count() }}</h3>
                <p>Estudiantes</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-gradient-success">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['presente'] }}</h3>
                <p>Presentes</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-gradient-warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['tardanza'] }}</h3>
                <p>Tardanzas</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-gradient-danger">
                <i class="fas fa-user-times"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['ausente'] }}</h3>
                <p>Ausentes</p>
            </div>
        </div>
    </div>

    <div class="card-dark">
        <div class="card-header-dark">
            <h5 class="mb-0">
                <i class="fas fa-calendar-check me-2"></i>Registro de Asistencias por Fecha
            </h5>
        </div>

        <div class="card-body-dark">
            @if(session('success'))
                <div class="alert-success-dark mb-4">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if($fechasAsistencia->count() > 0)
                <div class="table-responsive">
                    <table class="table-dark table-attendance">
                        <thead>
                            <tr>
                                <th class="student-column">Estudiante</th>
                                @foreach($fechasAsistencia as $fecha)
                                    <th class="date-column">
                                        <div class="date-header">
                                            <strong>{{ $fecha->format('d/m') }}</strong>
                                            <small class="text-muted">{{ $fecha->locale('es')->dayName }}</small>
                                        </div>
                                    </th>
                                @endforeach
                                <th class="stats-column">Estadísticas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($estudiantes as $inscripcion)
                                <tr>
                                    <td class="student-cell">
                                        <div class="student-info">
                                            <div class="student-avatar">
                                                {{ substr($inscripcion->estudiante->nombre, 0, 1) }}{{ substr($inscripcion->estudiante->apellido, 0, 1) }}
                                            </div>
                                            <div class="student-details">
                                                <strong>{{ $inscripcion->estudiante->nombre_completo }}</strong>
                                                <br><small class="text-muted">{{ $inscripcion->estudiante->codigo }}</small>
                                            </div>
                                        </div>
                                    </td>

                                    @foreach($fechasAsistencia as $fecha)
                                        @php
                                            $estado = $inscripcion->asistencias[$fecha->format('Y-m-d')] ?? null;
                                            $estadoConfig = [
                                                'presente' => ['icon' => 'fa-check', 'color' => '#10b981', 'bg' => 'rgba(16, 185, 129, 0.2)', 'text' => 'P'],
                                                'tardanza' => ['icon' => 'fa-clock', 'color' => '#f59e0b', 'bg' => 'rgba(245, 158, 11, 0.2)', 'text' => 'T'],
                                                'ausente' => ['icon' => 'fa-times', 'color' => '#ef4444', 'bg' => 'rgba(239, 68, 68, 0.2)', 'text' => 'A'],
                                                'justificado' => ['icon' => 'fa-file-alt', 'color' => '#06b6d4', 'bg' => 'rgba(6, 182, 212, 0.2)', 'text' => 'J'],
                                            ];
                                        @endphp
                                        <td class="attendance-cell">
                                            @if($estado)
                                                <div class="attendance-check {{ $estado }}" style="background: {{ $estadoConfig[$estado]['bg'] }}; color: {{ $estadoConfig[$estado]['color'] }};">
                                                    <i class="fas {{ $estadoConfig[$estado]['icon'] }}"></i>
                                                    <span class="check-text">{{ $estadoConfig[$estado]['text'] }}</span>
                                                </div>
                                            @else
                                                <div class="attendance-empty">
                                                    <i class="fas fa-minus"></i>
                                                </div>
                                            @endif
                                        </td>
                                    @endforeach

                                    <td class="stats-cell">
                                        @php
                                            $totalAsistencias = count(array_filter($inscripcion->asistencias));
                                            $presentes = count(array_filter($inscripcion->asistencias, fn($estado) => $estado === 'presente'));
                                            $tardanzas = count(array_filter($inscripcion->asistencias, fn($estado) => $estado === 'tardanza'));
                                            $porcentaje = $totalAsistencias > 0 ? round((($presentes + $tardanzas) / $totalAsistencias) * 100, 1) : 0;
                                        @endphp
                                        <div class="attendance-stats">
                                            <div class="stat-number">{{ $porcentaje }}%</div>
                                            <small class="text-muted">{{ $presentes + $tardanzas }}/{{ $totalAsistencias }}</small>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Leyenda -->
                <div class="attendance-legend mt-4">
                    <h6 class="legend-title"><i class="fas fa-info-circle me-2"></i>Leyenda</h6>
                    <div class="legend-items">
                        <div class="legend-item">
                            <div class="legend-check presente">
                                <i class="fas fa-check"></i>
                                <span>Presente</span>
                            </div>
                        </div>
                        <div class="legend-item">
                            <div class="legend-check tardanza">
                                <i class="fas fa-clock"></i>
                                <span>Tardanza</span>
                            </div>
                        </div>
                        <div class="legend-item">
                            <div class="legend-check ausente">
                                <i class="fas fa-times"></i>
                                <span>Ausente</span>
                            </div>
                        </div>
                        <div class="legend-item">
                            <div class="legend-check justificado">
                                <i class="fas fa-file-alt"></i>
                                <span>Justificado</span>
                            </div>
                        </div>
                        <div class="legend-item">
                            <div class="legend-empty">
                                <i class="fas fa-minus"></i>
                                <span>Sin registro</span>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-clipboard-check fa-3x mb-3"></i>
                    <h5>No hay fechas de asistencia registradas</h5>
                    <p>Comienza tomando asistencia para esta materia</p>
                    <a href="{{ route('admin.asistencias.create', ['seccion_id' => $seccion->id]) }}" class="btn-neon mt-3">
                        <i class="fas fa-plus me-2"></i>Tomar Primera Asistencia
                    </a>
                </div>
            @endif
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

    /* Page Header */
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
        color: #94a3b8;
        font-size: 0.95rem;
    }

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
        margin: 0;
    }

    .card-body-dark {
        padding: 2rem;
        color: var(--text-color);
    }

    .alert-success-dark {
        background-color: rgba(16, 185, 129, 0.2);
        border: 1px solid #10b981;
        color: #10b981;
        padding: 1rem;
        border-radius: 8px;
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

    /* Attendance Table */
    .table-attendance {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .table-attendance thead th {
        background: linear-gradient(135deg, rgba(14, 165, 233, 0.3) 0%, rgba(0, 212, 255, 0.2) 100%);
        color: var(--neon-cyan);
        text-transform: uppercase;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 1rem;
        border: none;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .table-attendance tbody tr {
        border-bottom: 1px solid rgba(0, 212, 255, 0.1);
        transition: all 0.3s ease;
    }

    .table-attendance tbody tr:hover {
        background: rgba(0, 212, 255, 0.05);
    }

    .table-attendance tbody td {
        padding: 0.75rem;
        vertical-align: middle;
    }

    .student-column {
        min-width: 250px;
        position: sticky;
        left: 0;
        background: rgba(15, 23, 42, 0.95);
        z-index: 5;
    }

    .date-column {
        min-width: 80px;
        text-align: center;
    }

    .stats-column {
        min-width: 100px;
        text-align: center;
    }

    .date-header {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.25rem;
    }

    .student-cell {
        position: sticky;
        left: 0;
        background: rgba(15, 23, 42, 0.95);
        z-index: 5;
    }

    .student-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .student-avatar {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--neon-cyan) 0%, var(--electric-blue) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.75rem;
        color: #ffffff; /* texto claro en avatar */
        box-shadow: 0 0 10px rgba(0, 212, 255, 0.3);
        flex-shrink: 0;
    }

    .student-details {
        min-width: 0;
        flex: 1;
    }

    .student-details strong {
        display: block;
        color: #e2e8f0;
        font-size: 0.85rem;
        line-height: 1.3;
        word-break: break-word;
    }

    .attendance-cell {
        text-align: center;
        padding: 0.5rem;
        color: var(--text-color); /* forzar texto claro en celdas */
    }

    .attendance-check {
        width: 35px;
        height: 35px;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: 600;
        margin: 0 auto;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        color: #ffffff; /* texto claro dentro del check */
        transition: all 0.3s ease;
    }

    .attendance-check:hover {
        transform: scale(1.1);
    }

    .attendance-check i {
        font-size: 0.6rem;
        margin-bottom: 0.1rem;
    }

    .check-text {
        font-size: 0.5rem;
        line-height: 1;
    }

    .attendance-empty {
        width: 35px;
        height: 35px;
        border-radius: 8px;
        background: rgba(45, 49, 72, 0.3);
        border: 1px solid rgba(0, 212, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #e2e8f0; /* texto claro para indicar sin registro */
        font-size: 0.8rem;
        margin: 0 auto;
    }

    .stats-cell {
        text-align: center;
    }

    .attendance-stats {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.25rem;
    }

    .stat-number {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--neon-cyan);
        text-shadow: 0 0 8px rgba(0, 212, 255, 0.4);
    }

    /* Legend */
    .attendance-legend {
        background: rgba(0, 212, 255, 0.05);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
        padding: 1.5rem;
    }

    .legend-title {
        color: var(--neon-cyan);
        font-weight: 600;
        margin-bottom: 1rem;
        text-shadow: 0 0 8px rgba(0, 212, 255, 0.4);
    }

    .legend-items {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        justify-content: center;
    }

    .legend-item {
        display: flex;
        align-items: center;
    }

    .legend-check {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        color: white;
    }

    .legend-check.presente {
        background: rgba(16, 185, 129, 0.2);
        border: 1px solid #10b981;
        color: #10b981;
    }

    .legend-check.tardanza {
        background: rgba(245, 158, 11, 0.2);
        border: 1px solid #f59e0b;
        color: #f59e0b;
    }

    .legend-check.ausente {
        background: rgba(239, 68, 68, 0.2);
        border: 1px solid #ef4444;
        color: #ef4444;
    }

    .legend-check.justificado {
        background: rgba(6, 182, 212, 0.2);
        border: 1px solid #06b6d4;
        color: #06b6d4;
    }

    .legend-empty {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: rgba(45, 49, 72, 0.3);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 8px;
        font-size: 0.85rem;
        color: #e2e8f0; /* texto claro en leyenda vacía */
    }

    /* Forzar texto claro en toda la tabla de asistencias */
    .table-attendance,
    .table-attendance td,
    .table-attendance th {
        color: var(--text-color) !important;
    }

    .empty-state {
        color: #94a3b8;
        padding: 3rem 1rem;
        text-align: center;
    }

    .empty-state i {
        color: rgba(0, 212, 255, 0.3);
    }

    .empty-state h5 {
        color: #e2e8f0;
        margin-bottom: 0.5rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .stat-content h3 {
            font-size: 1.5rem;
        }

        .card-body-dark {
            padding: 1.5rem;
        }

        .table-attendance {
            font-size: 0.8rem;
        }

        .date-column {
            min-width: 60px;
        }

        .student-column {
            min-width: 200px;
        }

        .attendance-check,
        .attendance-empty {
            width: 28px;
            height: 28px;
            font-size: 0.6rem;
        }

        .student-avatar {
            width: 28px;
            height: 28px;
            font-size: 0.6rem;
        }

        .legend-items {
            flex-direction: column;
            align-items: center;
        }

        .legend-item {
            width: 100%;
            justify-content: center;
        }
    }

    /* Scrollbar */
    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: rgba(26, 28, 46, 0.5);
        border-radius: 10px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, var(--neon-cyan) 0%, var(--electric-blue) 100%);
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 255, 255, 0.5);
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        box-shadow: 0 0 20px rgba(0, 255, 255, 0.8);
    }
</style>
@endpush
@endsection
