@extends('layouts.app')

@section('title', 'Reporte de Calificaciones')
@section('page-title', 'Reporte de Calificaciones por Sección')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <!-- Card de Selección de Sección -->
            <div class="card-dark mb-4">
                <div class="card-header-dark">
    <div class="d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-filter me-2"></i>Filtrar por Sección
        </h5>
        <a href="{{ route('admin.calificaciones.index') }}" class="btn-outline-neon-sm">
            <i class="fas fa-arrow-left me-2"></i>Volver
        </a>
    </div>
</div>
                <div class="card-body-dark">
                    <form method="GET" action="{{ route('admin.calificaciones.reporte') }}">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-10">
                                <label class="form-label-dark">
                                    <i class="fas fa-chalkboard me-2"></i>Asignación de Materias 
                                </label>
                                <select name="seccion_id" 
                                        class="form-select-dark" 
                                        required
                                        onchange="this.form.submit()">
                                    <option value="">-- Seleccione una Materia --</option>
                                    @foreach($secciones as $sec)
                                        <option value="{{ $sec->id }}" 
                                                {{ request('seccion_id') == $sec->id ? 'selected' : '' }}>
                                            {{ $sec->curso->codigo_curso }} - {{ $sec->curso->nombre }}
                                            | Sección: {{ $sec->nombre }}
                                            | Período: {{ $sec->periodo->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn-generar-reporte w-100">
                                    <i class="fas fa-sync-alt me-2"></i>Autogenerar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @if($seccion)
                <!-- Información de la Sección -->
                <div class="card-dark mb-4">
                    <div class="card-header-dark">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>Información de la Sección
                            </h5>
                            <button class="btn-outline-neon" onclick="window.print()">
                                <i class="fas fa-print me-2"></i>Imprimir
                            </button>
                        </div>
                    </div>
                    <div class="card-body-dark">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="info-card">
                                    <div class="info-icon bg-gradient-primary">
                                        <i class="fas fa-book"></i>
                                    </div>
                                    <div class="info-content">
                                        <label>Curso</label>
                                        <p>{{ $seccion->curso->nombre }}</p>
                                        <small>{{ $seccion->curso->codigo_curso }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-card">
                                    <div class="info-icon bg-gradient-success">
                                        <i class="fas fa-chalkboard-teacher"></i>
                                    </div>
                                    <div class="info-content">
                                        <label>Profesor</label>
                                        <p>{{ $seccion->profesor->nombre_completo }}</p>
                                        <small>{{ $seccion->profesor->email }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-card">
                                    <div class="info-icon bg-gradient-info">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div class="info-content">
                                        <label>Período</label>
                                        <p>{{ $seccion->periodo->nombre }}</p>
                                        <small>{{ $seccion->periodo->fecha_inicio->format('d/m/Y') }} - {{ $seccion->periodo->fecha_fin->format('d/m/Y') }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-card">
                                    <div class="info-icon bg-gradient-warning">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="info-content">
                                        <label>Estudiantes</label>
                                        <p>{{ $estadisticas['total_estudiantes'] }}</p>
                                        <small>Inscritos activos</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @php
                    $notaMinima = config('app.nota_minima_aprobacion', 60);
                @endphp

                <!-- Estadísticas Generales -->
                <div class="stats-grid mb-4">
                    <div class="stat-card">
                        <div class="stat-icon bg-gradient-primary">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="stat-content">
                            <h3>{{ $estadisticas['promedio_seccion'] ?? 'N/A' }}</h3>
                            <p>Promedio General</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon bg-gradient-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <h3>{{ $estadisticas['aprobados'] }}</h3>
                            <p>Aprobados (≥{{ $notaMinima }})</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon bg-gradient-danger">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="stat-content">
                            <h3>{{ $estadisticas['reprobados'] }}</h3>
                            <p>Reprobados (<{{ $notaMinima }})</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon bg-gradient-info">
                            <i class="fas fa-percentage"></i>
                        </div>
                        <div class="stat-content">
                            <h3>{{ $estadisticas['total_estudiantes'] > 0 ? round(($estadisticas['aprobados'] / $estadisticas['total_estudiantes']) * 100, 1) : 0 }}%</h3>
                            <p>Tasa de Aprobación</p>
                        </div>
                    </div>
                </div>

                <!-- Tabla de Calificaciones -->
                <div class="card-dark mb-4">
                    <div class="card-header-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-table me-2"></i>Calificaciones Detalladas
                        </h5>
                    </div>
                    <div class="card-body-dark">
                        <div class="table-responsive">
                            <table class="table-dark">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Estudiante</th>
                                        @foreach($seccion->evaluaciones as $evaluacion)
                                            <th class="text-center">
                                                {{ $evaluacion->nombre }}
                                                <br><small class="text-muted">({{ $evaluacion->porcentaje }}%)</small>
                                            </th>
                                        @endforeach
                                        <th class="text-center">Nota Final</th>
                                        <th class="text-center">Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($estadisticas['inscripciones'] as $index => $inscripcion)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <div class="student-info-compact">
                                                    <div class="student-avatar-small">
                                                        <i class="fas fa-user-graduate"></i>
                                                    </div>
                                                    <div>
                                                        <strong>{{ $inscripcion->estudiante->nombre_completo }}</strong>
                                                        <br><small class="text-muted">{{ $inscripcion->estudiante->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            @foreach($seccion->evaluaciones as $evaluacion)
                                                @php
                                                    $calificacion = $inscripcion->calificaciones->where('evaluacion_id', $evaluacion->id)->first();
                                                @endphp
                                                <td class="text-center">
                                                    @if($calificacion && $calificacion->nota !== null)
                                                        <span class="grade-badge-small {{ $calificacion->nota >= $notaMinima ? 'grade-pass' : 'grade-fail' }}">
                                                            {{ number_format($calificacion->nota, 2) }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            @endforeach
                                            <td class="text-center">
                                                @if($inscripcion->nota_final)
                                                    <span class="grade-badge {{ $inscripcion->nota_final >= $notaMinima ? 'grade-pass' : 'grade-fail' }}">
                                                        {{ number_format($inscripcion->nota_final, 2) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">Sin calcular</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($inscripcion->nota_final)
                                                    @if($inscripcion->nota_final >= $notaMinima)
                                                        <span class="status-badge status-approved">
                                                            <i class="fas fa-check-circle"></i> Aprobado
                                                        </span>
                                                    @else
                                                        <span class="status-badge status-failed">
                                                            <i class="fas fa-times-circle"></i> Reprobado
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="status-badge status-pending">
                                                        <i class="fas fa-clock"></i> Pendiente
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Gráfico de Distribución -->
                <div class="card-dark">
                    <div class="card-header-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-pie me-2"></i>Distribución de Calificaciones
                        </h5>
                    </div>
                    <div class="card-body-dark">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="distribution-chart">
                                    <canvas id="gradeDistributionChart"></canvas>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="distribution-stats">
                                    <h6 class="text-neon mb-3">Rangos de Calificación</h6>
                                    @php
                                        $rangos = [
                                            'Excelente (90-100)' => $estadisticas['inscripciones']->filter(function($i) { return $i->nota_final >= 90; })->count(),
                                            'Muy Bueno (80-89)' => $estadisticas['inscripciones']->filter(function($i) { return $i->nota_final >= 80 && $i->nota_final < 90; })->count(),
                                            'Bueno (70-79)' => $estadisticas['inscripciones']->filter(function($i) { return $i->nota_final >= 70 && $i->nota_final < 80; })->count(),
                                            'Regular ('.$notaMinima.'-69)' => $estadisticas['inscripciones']->filter(function($i) use ($notaMinima) { return $i->nota_final >= $notaMinima && $i->nota_final < 70; })->count(),
                                            'Insuficiente (<'.$notaMinima.')' => $estadisticas['inscripciones']->filter(function($i) use ($notaMinima) { return $i->nota_final > 0 && $i->nota_final < $notaMinima; })->count(),
                                        ];
                                    @endphp
                                    
                                    @foreach($rangos as $nombre => $cantidad)
                                        <div class="range-item">
                                            <div class="range-info">
                                                <span class="range-name">{{ $nombre }}</span>
                                                <span class="range-count">{{ $cantidad }} estudiantes</span>
                                            </div>
                                            <div class="range-bar">
                                                <div class="range-fill" style="width: {{ $estadisticas['total_estudiantes'] > 0 ? ($cantidad / $estadisticas['total_estudiantes']) * 100 : 0 }}%"></div>
                                            </div>
                                            <span class="range-percentage">
                                                {{ $estadisticas['total_estudiantes'] > 0 ? round(($cantidad / $estadisticas['total_estudiantes']) * 100, 1) : 0 }}%
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="card-dark">
                    <div class="card-body-dark">
                        <div class="empty-state">
                            <i class="fas fa-chart-bar fa-3x mb-3"></i>
                            <h5>Selecciona una Sección</h5>
                            <p>Elige una sección del menú desplegable para generar el reporte de calificaciones detallado</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
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

    .form-label-dark {
        color: #e2e8f0;
        font-weight: 500;
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-label-dark i {
        color: var(--neon-cyan);
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

    .info-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.5rem;
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 12px;
        height: 100%;
        transition: all 0.3s ease;
    }

    .info-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.2);
        border-color: var(--neon-cyan);
    }

    .info-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        flex-shrink: 0;
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .bg-gradient-info {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
    }

    .bg-gradient-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .bg-gradient-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }

    .info-content label {
        color: #64748b;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 0.25rem;
        display: block;
    }

    .info-content p {
        color: #e2e8f0;
        font-weight: 600;
        font-size: 1.1rem;
        margin: 0;
    }

    .info-content small {
        color: #94a3b8;
        font-size: 0.875rem;
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

    .student-info-compact {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .student-avatar-small {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        color: #0f172a;
        box-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
        flex-shrink: 0;
    }

    .grade-badge, .grade-badge-small {
        padding: 0.35rem 0.75rem;
        border-radius: 8px;
        font-weight: 700;
        display: inline-block;
    }

    .grade-badge {
        font-size: 1rem;
    }

    .grade-badge-small {
        font-size: 0.875rem;
        padding: 0.25rem 0.5rem;
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

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
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

    .status-pending {
        background: rgba(245, 158, 11, 0.2);
        color: #f59e0b;
        border: 1px solid #f59e0b;
    }

    .text-muted {
        color: #94a3b8 !important;
    }

    .distribution-chart {
        padding: 2rem;
    }

    .distribution-stats {
        padding: 1rem;
    }

    .text-neon {
        color: var(--neon-cyan);
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .range-item {
        margin-bottom: 1.5rem;
    }

    .range-info {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }

    .range-name {
        color: #e2e8f0;
        font-weight: 500;
    }

    .range-count {
        color: #94a3b8;
        font-size: 0.875rem;
    }

    .range-bar {
        height: 8px;
        background: rgba(0, 212, 255, 0.1);
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 0.5rem;
    }

    .range-fill {
        height: 100%;
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        border-radius: 10px;
        transition: width 0.3s ease;
    }

    .range-percentage {
        color: var(--neon-cyan);
        font-weight: 600;
        font-size: 0.875rem;
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

    /* Botón Autogenerar - Color Cyan Original ✨ */
    .btn-generar-reporte {
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        border: none;
        color: #0f172a;
        font-weight: 700;
        font-size: 0.95rem;
        padding: 0.85rem 1rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.4);
        text-decoration: none;
        display: inline-block;
        position: relative;
        overflow: hidden;
        cursor: pointer;
        text-transform: none;
        white-space: nowrap;
    }

    .btn-generar-reporte::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s ease;
    }

    .btn-generar-reporte:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 35px rgba(0, 212, 255, 0.6);
        background: linear-gradient(135deg, #0ea5e9 0%, #00d4ff 100%);
    }

    .btn-generar-reporte:hover::before {
        left: 100%;
    }

    .btn-generar-reporte:active {
        transform: translateY(-1px);
    }

    .btn-generar-reporte i {
        font-size: 1rem;
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

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #94a3b8;
    }

    .empty-state i {
        color: rgba(0, 212, 255, 0.3);
    }

    .empty-state h5 {
        color: #e2e8f0;
        margin-bottom: 0.5rem;
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .info-card {
            flex-direction: column;
            text-align: center;
        }

        .student-info-compact {
            flex-direction: column;
            text-align: center;
        }
    }
    /* Botón pequeño para header */
.btn-outline-neon-sm {
    background: transparent;
    border: 2px solid var(--neon-cyan);
    color: var(--neon-cyan);
    font-weight: 600;
    padding: 0.5rem 1rem;
    border-radius: 10px;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    font-size: 0.875rem;
    white-space: nowrap;
}

.btn-outline-neon-sm:hover {
    background: rgba(0, 212, 255, 0.1);
    transform: translateY(-2px);
    box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
    color: var(--neon-cyan);
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    @if($seccion)
    const ctx = document.getElementById('gradeDistributionChart');
    
    if (ctx) {
        const aprobados = {{ $estadisticas['aprobados'] }};
        const reprobados = {{ $estadisticas['reprobados'] }};
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Aprobados (≥{{ $notaMinima }})', 'Reprobados (<{{ $notaMinima }})'],
                datasets: [{
                    data: [aprobados, reprobados],
                    backgroundColor: [
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(239, 68, 68, 0.8)'
                    ],
                    borderColor: [
                        'rgba(16, 185, 129, 1)',
                        'rgba(239, 68, 68, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#e2e8f0',
                            font: {
                                size: 14
                            },
                            padding: 20
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.95)',
                        titleColor: '#00d4ff',
                        bodyColor: '#e2e8f0',
                        borderColor: '#00d4ff',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                const total = aprobados + reprobados;
                                const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }
    @endif
</script>
@endpush

@endsection