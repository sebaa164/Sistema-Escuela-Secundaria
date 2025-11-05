@extends('layouts.app')

@section('title', 'Asistencias - Profesor')
@section('page-title', 'Asistencias')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header-dark mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-calendar-check me-3"></i>Asistencias
                </h1>
                <p class="header-subtitle mb-0">Gestiona la asistencia de tus secciones</p>
            </div>
            <a href="{{ route('profesor.dashboard') }}" class="btn-outline-neon">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="stats-grid mb-4">
        <div class="stat-card">
            <div class="stat-icon bg-gradient-primary">
                <i class="fas fa-chalkboard"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $secciones->count() }}</h3>
                <p>Secciones Disponibles</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-gradient-success">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $secciones->sum(function($s) { return $s->inscripciones->count(); }) }}</h3>
                <p>Estudiantes Totales</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-gradient-info">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stat-content">
                <h3>{{ \Carbon\Carbon::now()->format('d/m') }}</h3>
                <p>Fecha Actual</p>
            </div>
        </div>
    </div>

    <!-- Lista de Secciones -->
    <div class="card-dark">
        <div class="card-header-dark">
            <h5><i class="fas fa-list me-2"></i>Secciones Disponibles para Asistencia</h5>
        </div>
        <div class="card-body-dark">
            <div class="row g-3">
                @forelse($secciones as $seccion)
                    <div class="col-md-6 col-lg-4">
                        <div class="subject-detail-card">
                            <div class="subject-header">
                                <div class="subject-icon" style="background: {{ ['#0ea5e9', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444'][array_rand(['#0ea5e9', '#10b981', '#f59e0b', '#8b5cf6', '#ef4444'])] }}">
                                    <i class="fas fa-chalkboard"></i>
                                </div>
                                <div class="subject-info">
                                    <h6>{{ $seccion->curso->nombre }}</h6>
                                    <span class="subject-code">{{ $seccion->codigo_seccion }}</span>
                                </div>
                            </div>
                            <div class="subject-details">
                                <div class="detail-row">
                                    <i class="fas fa-users"></i>
                                    <span>{{ $seccion->inscripciones->count() }} estudiantes</span>
                                </div>
                                <div class="detail-row">
                                    <i class="fas fa-clock"></i>
                                    <span>{{ $seccion->curso->horas_semanales ?? 0 }} horas/semana</span>
                                </div>
                            </div>

                            <div class="subject-actions">
                                <a href="{{ route('profesor.asistencias.show', $seccion) }}"
                                   class="btn-neon-sm w-100">
                                    <i class="fas fa-calendar-check me-2"></i>Gestionar Asistencia
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fas fa-chalkboard fa-4x mb-4" style="color: rgba(0, 212, 255, 0.3);"></i>
                            <h4 style="color: #f1f5f9;">No tienes secciones asignadas</h4>
                            <p style="color: #94a3b8;">No hay secciones disponibles para gestionar asistencia</p>
                        </div>
                    </div>
                @endforelse
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
        transition: all 0.3s ease;
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.1);
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0 25px rgba(0, 212, 255, 0.3);
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

    .bg-gradient-primary { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); box-shadow: 0 0 20px rgba(14, 165, 233, 0.4); }
    .bg-gradient-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 0 20px rgba(16, 185, 129, 0.4); }
    .bg-gradient-info { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); box-shadow: 0 0 20px rgba(6, 182, 212, 0.4); }

    .stat-content h3 {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--neon-cyan);
        margin: 0;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .stat-content p { color: #f8f8f2; margin: 0.5rem 0; }

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

    .subject-actions {
        margin-top: 1rem;
    }

    .btn-neon, .btn-neon-sm {
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        color: #0f172a;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: all 0.3s ease;
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
    }

    .btn-neon-sm {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }

    .btn-neon:hover, .btn-neon-sm:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 30px rgba(0, 212, 255, 0.5);
        color: #0f172a;
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

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .subject-detail-card {
            margin-bottom: 1rem;
        }
    }
</style>
@endpush
@endsection
