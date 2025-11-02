@extends('layouts.app')

@section('title', 'Mi Horario - Estudiante')
@section('page-title', 'Mi Horario')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header-dark mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-calendar-week me-3"></i>Mi Horario de Clases
                </h1>
                <p class="header-subtitle mb-0">Horario semanal de tus materias</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('estudiante.horario.pdf') }}" class="btn-action-neon" target="_blank">
                    <i class="fas fa-download me-2"></i>Descargar PDF
                </a>
                <a href="{{ route('estudiante.dashboard') }}" class="btn-outline-neon">
                    <i class="fas fa-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Resumen de Materias -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="summary-card-sm">
                <div class="summary-icon-sm bg-primary">
                    <i class="fas fa-book"></i>
                </div>
                <div class="summary-content-sm">
                    <h4>{{ $inscripciones->count() }}</h4>
                    <p>Materias Inscritas</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-card-sm">
                <div class="summary-icon-sm bg-success">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="summary-content-sm">
                    @php
                        $totalHoras = 0;
                        foreach($inscripciones as $ins) {
                            $totalHoras += $ins->seccion->curso->horas_semanales ?? 0;
                        }
                    @endphp
                    <h4>{{ $totalHoras }}</h4>
                    <p>Horas Semanales</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="summary-card-sm">
                <div class="summary-icon-sm bg-info">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="summary-content-sm">
                    @php
                        $totalCreditos = $inscripciones->sum(function($ins) {
                            return $ins->seccion->curso->creditos ?? 0;
                        });
                    @endphp
                    <h4>{{ $totalCreditos }}</h4>
                    <p>Créditos Totales</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Horario Semanal -->
    <div class="card-dark">
        <div class="card-header-dark">
            <h5><i class="fas fa-calendar-alt me-2"></i>Horario Semanal</h5>
        </div>
        <div class="card-body-dark p-0">
            <div class="schedule-container">
                <div class="schedule-grid">
                    <!-- Header con días de la semana -->
                    <div class="schedule-header">
                        <div class="schedule-time-header">Hora</div>
                        @foreach(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'] as $dia)
                            <div class="schedule-day-header">
                                <i class="fas fa-calendar-day me-2"></i>{{ $dia }}
                            </div>
                        @endforeach
                    </div>

                    <!-- Cuerpo del horario -->
                    <div class="schedule-body">
                        @forelse($horasDisponibles as $hora)
                            <div class="schedule-row">
                                <div class="schedule-time-cell">{{ substr($hora, 0, 5) }}</div>
                                
                                @foreach(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'] as $dia)
                                    <div class="schedule-cell">
                                        @foreach($horarioSemanal[$dia] ?? [] as $clase)
                                            @if($clase['hora_inicio'] == $hora)
                                                <div class="schedule-class" style="border-left-color: {{ $clase['color'] }}">
                                                    <div class="class-header">
                                                        <span class="class-code">{{ $clase['codigo'] }}</span>
                                                        <span class="class-time">{{ substr($clase['hora_inicio'], 0, 5) }} - {{ substr($clase['hora_fin'], 0, 5) }}</span>
                                                    </div>
                                                    <div class="class-name">{{ $clase['curso'] }}</div>
                                                    <div class="class-details">
                                                        <div class="class-detail-item">
                                                            <i class="fas fa-user-tie"></i>
                                                            <span>{{ $clase['profesor'] }}</span>
                                                        </div>
                                                        <div class="class-detail-item">
                                                            <i class="fas fa-door-open"></i>
                                                            <span>{{ $clase['aula'] }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        @empty
                            <div class="empty-schedule">
                                <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                <h5>No hay horarios registrados</h5>
                                <p>Aún no se han asignado horarios a tus materias</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Materias -->
    <div class="card-dark mt-4">
        <div class="card-header-dark">
            <h5><i class="fas fa-list me-2"></i>Detalle de Materias</h5>
        </div>
        <div class="card-body-dark">
            <div class="row g-3">
                @foreach($inscripciones as $inscripcion)
                    <div class="col-md-6">
                        <div class="subject-detail-card">
                            <div class="subject-header">
                                <div class="subject-icon" style="background: {{ $coloresInscripciones[$inscripcion->seccion->curso->codigo_curso] ?? '#0ea5e9' }}">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div class="subject-info">
                                    <h6>{{ $inscripcion->seccion->curso->nombre }}</h6>
                                    <span class="subject-code">{{ $inscripcion->seccion->curso->codigo_curso }}</span>
                                </div>
                            </div>
                            <div class="subject-details">
                                <div class="detail-row">
                                    <i class="fas fa-user-tie"></i>
                                    <span>{{ $inscripcion->seccion->profesor->nombre_completo }}</span>
                                </div>
                                <div class="detail-row">
                                    <i class="fas fa-users"></i>
                                    <span>Sección {{ $inscripcion->seccion->codigo_seccion }}</span>
                                </div>
                                <div class="detail-row">
                                    <i class="fas fa-clock"></i>
                                    <span>{{ $inscripcion->seccion->curso->horas_semanales ?? 0 }} horas/semana</span>
                                </div>
                                <div class="detail-row">
                                    <i class="fas fa-graduation-cap"></i>
                                    <span>{{ $inscripcion->seccion->curso->creditos ?? 0 }} créditos</span>
                                </div>
                            </div>
                            
                            @if($inscripcion->seccion->horarios->count() > 0)
                                <div class="subject-schedule">
                                    <strong>Horarios:</strong>
                                    @foreach($inscripcion->seccion->horarios as $horario)
                                        <div class="schedule-item">
                                            <span class="schedule-day">{{ $horario->dia_semana }}</span>
                                            <span class="schedule-time">{{ substr($horario->hora_inicio, 0, 5) }} - {{ substr($horario->hora_fin, 0, 5) }}</span>
                                            <span class="schedule-room">{{ $horario->aula ?? 'Por asignar' }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
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
    .summary-card-sm {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.95) 100%);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 12px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.3s ease;
    }

    .summary-card-sm:hover {
        border-color: rgba(0, 212, 255, 0.5);
        transform: translateY(-3px);
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.2);
    }

    .summary-icon-sm {
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

    .summary-icon-sm.bg-primary { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); box-shadow: 0 0 15px rgba(14, 165, 233, 0.5); }
    .summary-icon-sm.bg-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 0 15px rgba(16, 185, 129, 0.5); }
    .summary-icon-sm.bg-info { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); box-shadow: 0 0 15px rgba(6, 182, 212, 0.5); }

    .summary-content-sm h4 {
        color: var(--neon-cyan);
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .summary-content-sm p {
        color: #cbd5e1;
        margin: 0;
        font-size: 0.875rem;
        font-weight: 500;
    }

    /* Schedule Grid */
    .schedule-container {
        overflow-x: auto;
        padding: 1rem;
    }

    .schedule-grid {
        min-width: 1200px;
    }

    .schedule-header {
        display: grid;
        grid-template-columns: 100px repeat(6, 1fr);
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .schedule-time-header,
    .schedule-day-header {
        background: rgba(0, 212, 255, 0.1);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 8px;
        padding: 1rem;
        text-align: center;
        color: var(--neon-cyan);
        font-weight: 600;
        font-size: 0.875rem;
    }

    .schedule-body {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .schedule-row {
        display: grid;
        grid-template-columns: 100px repeat(6, 1fr);
        gap: 0.5rem;
        min-height: 120px;
    }

    .schedule-time-cell {
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 8px;
        padding: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #cbd5e1;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .schedule-cell {
        background: rgba(15, 23, 42, 0.3);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 8px;
        padding: 0.5rem;
        position: relative;
        min-height: 120px;
    }

    .schedule-class {
        background: rgba(15, 23, 42, 0.9);
        border: 1px solid rgba(0, 212, 255, 0.4);
        border-left: 4px solid;
        border-radius: 8px;
        padding: 0.75rem;
        height: 100%;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }

    .schedule-class:hover {
        background: rgba(0, 212, 255, 0.1);
        transform: scale(1.02);
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
    }

    .class-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.25rem;
    }

    .class-code {
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        color: #0f172a;
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.7rem;
        box-shadow: 0 0 8px rgba(0, 212, 255, 0.3);
    }

    .class-time {
        color: #94a3b8;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .class-name {
        color: #f1f5f9;
        font-weight: 600;
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
        line-height: 1.3;
    }

    .class-details {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .class-detail-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #cbd5e1;
        font-size: 0.75rem;
    }

    .class-detail-item i {
        color: var(--neon-cyan);
        font-size: 0.7rem;
        width: 12px;
    }

    /* Subject Detail Cards */
    .subject-detail-card {
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 12px;
        padding: 1.25rem;
        transition: all 0.3s ease;
    }

    .subject-detail-card:hover {
        border-color: rgba(0, 212, 255, 0.5);
        transform: translateY(-3px);
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.2);
    }

    .subject-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid rgba(0, 212, 255, 0.2);
    }

    .subject-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
        flex-shrink: 0;
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
    }

    .subject-info h6 {
        color: #f1f5f9;
        font-weight: 600;
        margin: 0 0 0.25rem 0;
        font-size: 1rem;
    }

    .subject-code {
        background: rgba(0, 212, 255, 0.2);
        color: var(--neon-cyan);
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.75rem;
    }

    .subject-details {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .detail-row {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: #cbd5e1;
        font-size: 0.875rem;
    }

    .detail-row i {
        color: var(--neon-cyan);
        width: 16px;
    }

    .subject-schedule {
        background: rgba(0, 212, 255, 0.05);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 8px;
        padding: 0.75rem;
        margin-top: 0.75rem;
    }

    .subject-schedule strong {
        color: var(--neon-cyan);
        font-size: 0.875rem;
        display: block;
        margin-bottom: 0.5rem;
    }

    .schedule-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem;
        background: rgba(15, 23, 42, 0.5);
        border-radius: 6px;
        margin-bottom: 0.5rem;
        font-size: 0.8rem;
    }

    .schedule-item:last-child {
        margin-bottom: 0;
    }

    .schedule-day {
        color: var(--neon-cyan);
        font-weight: 600;
        min-width: 80px;
    }

    .schedule-time {
        color: #f1f5f9;
        font-weight: 600;
    }

    .schedule-room {
        color: #94a3b8;
        margin-left: auto;
    }

    .btn-action-neon {
        padding: 0.75rem 1.5rem;
        background: rgba(0, 212, 255, 0.1);
        border: 1px solid rgba(0, 212, 255, 0.3);
        color: var(--neon-cyan);
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
    }

    .btn-action-neon:hover {
        background: rgba(0, 212, 255, 0.2);
        border-color: var(--neon-cyan);
        transform: translateY(-2px);
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
        color: var(--neon-cyan);
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

    .empty-schedule {
        text-align: center;
        padding: 4rem 2rem;
        color: #94a3b8;
        grid-column: 1 / -1;
    }

    .empty-schedule i {
        color: rgba(0, 212, 255, 0.3);
    }

    .empty-schedule h5 {
        color: #f1f5f9;
        margin: 1rem 0 0.5rem 0;
    }

    @media (max-width: 768px) {
        .schedule-container {
            padding: 0.5rem;
        }

        .schedule-grid {
            min-width: 800px;
        }
    }
</style>
@endpush
@endsection
