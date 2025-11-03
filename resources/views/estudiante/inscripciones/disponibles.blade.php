@extends('layouts.app')

@section('title', 'Secciones Disponibles - Estudiante')
@section('page-title', 'Secciones Disponibles')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header-dark mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-plus-circle me-3"></i>Secciones Disponibles
                </h1>
                <p class="header-subtitle mb-0">Inscríbete en las materias que desees cursar</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('estudiante.inscripciones.historial') }}" class="btn-action-neon">
                    <i class="fas fa-history me-2"></i>Ver Historial
                </a>
                <a href="{{ route('estudiante.inscripciones.index') }}" class="btn-outline-neon">
                    <i class="fas fa-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card-dark mb-4">
        <div class="card-body-dark">
            <form method="GET" action="{{ route('estudiante.inscripciones.disponibles') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label-neon">Carrera</label>
                    <select name="carrera" class="form-control-neon" onchange="this.form.submit()">
                        <option value="">Todas las carreras</option>
                        @foreach($carreras as $carrera)
                            <option value="{{ $carrera }}" {{ request('carrera') == $carrera ? 'selected' : '' }}>
                                {{ $carrera }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label-neon">Nivel</label>
                    <select name="nivel" class="form-control-neon" onchange="this.form.submit()">
                        <option value="">Todos los niveles</option>
                        @foreach($niveles as $nivel)
                            <option value="{{ $nivel }}" {{ request('nivel') == $nivel ? 'selected' : '' }}>
                                Nivel {{ $nivel }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <a href="{{ route('estudiante.inscripciones.disponibles') }}" class="btn-action-neon w-100">
                        <i class="fas fa-redo me-2"></i>Limpiar Filtros
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Secciones -->
    <div class="row g-4">
        @forelse($secciones as $seccion)
            @php
                $cuposDisponibles = $seccion->cupos_disponibles;
                $porcentajeCupos = $seccion->cupo_maximo > 0 ? ($cuposDisponibles / $seccion->cupo_maximo) * 100 : 0;
                
                $estadoCupos = '';
                if ($porcentajeCupos > 50) {
                    $estadoCupos = 'success';
                } elseif ($porcentajeCupos > 20) {
                    $estadoCupos = 'warning';
                } else {
                    $estadoCupos = 'danger';
                }
            @endphp
            
            <div class="col-lg-6">
                <div class="section-card">
                    <div class="section-header">
                        <div class="section-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="section-title">
                            <h5>{{ $seccion->curso->nombre }}</h5>
                            <div class="d-flex gap-2 align-items-center flex-wrap">
                                <span class="badge-neon-sm">{{ $seccion->curso->codigo_curso }}</span>
                                <span class="text-muted-neon">
                                    <i class="fas fa-layer-group"></i>
                                    Nivel {{ $seccion->curso->nivel }}
                                </span>
                            </div>
                        </div>
                        <div class="section-status">
                            <span class="badge badge-{{ $estadoCupos }}">
                                <i class="fas fa-users me-1"></i>
                                {{ $cuposDisponibles }} cupos
                            </span>
                        </div>
                    </div>

                    <div class="section-body">
                        <!-- Información del Curso -->
                        <div class="info-grid-sm">
                            <div class="info-item-sm">
                                <i class="fas fa-user-tie text-primary"></i>
                                <div>
                                    <small>Profesor</small>
                                    <strong>{{ $seccion->profesor->nombre_completo }}</strong>
                                </div>
                            </div>

                            <div class="info-item-sm">
                                <i class="fas fa-code text-info"></i>
                                <div>
                                    <small>Sección</small>
                                    <strong>{{ $seccion->codigo_seccion }}</strong>
                                </div>
                            </div>

                            <div class="info-item-sm">
                                <i class="fas fa-graduation-cap text-success"></i>
                                <div>
                                    <small>Créditos</small>
                                    <strong>{{ $seccion->curso->creditos }}</strong>
                                </div>
                            </div>

                            <div class="info-item-sm">
                                <i class="fas fa-clock text-warning"></i>
                                <div>
                                    <small>Horas</small>
                                    <strong>{{ $seccion->curso->horas_semanales }}h/sem</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Barra de Cupos -->
                        <div class="cupos-section">
                            <div class="cupos-label">
                                <span>Cupos Disponibles</span>
                                <span class="cupos-text">{{ $cuposDisponibles }}/{{ $seccion->cupo_maximo }}</span>
                            </div>
                            <div class="cupos-bar">
                                <div class="cupos-fill bg-{{ $estadoCupos }}" style="width: {{ $porcentajeCupos }}%"></div>
                            </div>
                        </div>

                        <!-- Botón de Inscripción -->
                        <form method="POST" action="{{ route('estudiante.inscripciones.inscribirse', $seccion->id) }}" class="mt-3">
                            @csrf
                            <button type="submit" class="btn-inscribir" {{ $cuposDisponibles == 0 ? 'disabled' : '' }}>
                                @if($cuposDisponibles > 0)
                                    <i class="fas fa-check-circle me-2"></i>Inscribirse
                                @else
                                    <i class="fas fa-times-circle me-2"></i>Sin Cupos
                                @endif
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="empty-state-compact">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <h5>No hay secciones disponibles</h5>
                    <p>No se encontraron secciones disponibles con los filtros seleccionados</p>
                    <a href="{{ route('estudiante.inscripciones.disponibles') }}" class="btn-outline-neon mt-3">
                        <i class="fas fa-redo me-2"></i>Limpiar Filtros
                    </a>
                </div>
            </div>
        @endforelse
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

    .card-body-dark { padding: 1.5rem; }

    .form-label-neon {
        color: var(--neon-cyan);
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-control-neon {
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(0, 212, 255, 0.3);
        color: #f1f5f9;
        padding: 0.75rem 1rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        width: 100%;
    }

    .form-control-neon:focus {
        background: rgba(15, 23, 42, 0.7);
        border-color: var(--neon-cyan);
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
        color: #f1f5f9;
        outline: none;
    }

    .form-control-neon option {
        background: #1e293b;
        color: #f1f5f9;
    }

    /* Section Cards */
    .section-card {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.95) 100%);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 15px;
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%;
    }

    .section-card:hover {
        border-color: rgba(0, 212, 255, 0.5);
        box-shadow: 0 0 25px rgba(0, 212, 255, 0.2);
        transform: translateY(-5px);
    }

    .section-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.5rem;
        border-bottom: 1px solid rgba(0, 212, 255, 0.2);
        background: rgba(0, 212, 255, 0.05);
    }

    .section-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        flex-shrink: 0;
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        box-shadow: 0 0 20px rgba(14, 165, 233, 0.5);
    }

    .section-title {
        flex: 1;
    }

    .section-title h5 {
        color: #f1f5f9;
        font-weight: 600;
        margin: 0 0 0.5rem 0;
        font-size: 1.1rem;
    }

    .text-muted-neon {
        color: #94a3b8;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
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

    .section-status {
        flex-shrink: 0;
    }

    .badge {
        padding: 0.5rem 1rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.75rem;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        white-space: nowrap;
    }

    .badge-success {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 1px solid #10b981;
    }

    .badge-warning {
        background: rgba(245, 158, 11, 0.2);
        color: #f59e0b;
        border: 1px solid #f59e0b;
    }

    .badge-danger {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 1px solid #ef4444;
    }

    .section-body {
        padding: 1.5rem;
    }

    /* Info Grid Small */
    .info-grid-sm {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .info-item-sm {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 8px;
    }

    .info-item-sm i {
        font-size: 1.25rem;
    }

    .info-item-sm small {
        display: block;
        color: #94a3b8;
        font-size: 0.7rem;
        text-transform: uppercase;
        font-weight: 600;
    }

    .info-item-sm strong {
        display: block;
        color: #f1f5f9;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .text-primary { color: #0ea5e9; }
    .text-info { color: #06b6d4; }
    .text-success { color: #10b981; }
    .text-warning { color: #f59e0b; }

    /* Cupos Section */
    .cupos-section {
        margin-bottom: 1rem;
    }

    .cupos-label {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
        color: #cbd5e1;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .cupos-text {
        color: var(--neon-cyan);
        font-weight: 700;
    }

    .cupos-bar {
        height: 10px;
        background: rgba(100, 116, 139, 0.2);
        border-radius: 10px;
        overflow: hidden;
    }

    .cupos-fill {
        height: 100%;
        border-radius: 10px;
        transition: width 0.6s ease;
    }

    .cupos-fill.bg-success {
        background: linear-gradient(90deg, #10b981 0%, #34d399 100%);
        box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
    }

    .cupos-fill.bg-warning {
        background: linear-gradient(90deg, #f59e0b 0%, #fbbf24 100%);
        box-shadow: 0 0 10px rgba(245, 158, 11, 0.5);
    }

    .cupos-fill.bg-danger {
        background: linear-gradient(90deg, #ef4444 0%, #f87171 100%);
        box-shadow: 0 0 10px rgba(239, 68, 68, 0.5);
    }

    /* Botón Inscribir */
    .btn-inscribir {
        width: 100%;
        padding: 1rem;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border: none;
        color: white;
        border-radius: 10px;
        font-weight: 700;
        font-size: 1rem;
        text-transform: uppercase;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 0 20px rgba(16, 185, 129, 0.3);
    }

    .btn-inscribir:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 0 30px rgba(16, 185, 129, 0.5);
    }

    .btn-inscribir:disabled {
        background: linear-gradient(135deg, #64748b 0%, #475569 100%);
        cursor: not-allowed;
        opacity: 0.6;
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
        .section-header {
            flex-wrap: wrap;
        }

        .info-grid-sm {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush
@endsection
