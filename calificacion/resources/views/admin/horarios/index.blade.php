@extends('layouts.app')

@section('title', 'Gestión de Horarios')
@section('page-title', 'Horarios de Sección')

@section('content')
<div class="container-fluid">
    <!-- Header con información de la sección -->
    <div class="page-header-dark mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-clock me-3"></i>Horarios de Sección
                </h1>
                <p class="header-subtitle mb-0">
                    {{ $seccion->curso->codigo_curso }} - {{ $seccion->curso->nombre }} 
                    (Sección {{ $seccion->codigo_seccion }})
                </p>
                <small class="text-muted">
                    Profesor: {{ $seccion->profesor->nombre_completo }}
                </small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.horarios.create', $seccion->id) }}" class="btn-action-neon">
                    <i class="fas fa-plus me-2"></i>Agregar Horario
                </a>
                <a href="{{ route('admin.secciones.index') }}" class="btn-outline-neon">
                    <i class="fas fa-arrow-left me-2"></i>Volver a Secciones
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Grid de Horarios por Día -->
    <div class="card-dark">
        <div class="card-header-dark">
            <h5><i class="fas fa-calendar-week me-2"></i>Horario Semanal</h5>
        </div>
        <div class="card-body-dark">
            <div class="schedule-grid-admin">
                @foreach(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'] as $dia)
                    <div class="day-column">
                        <div class="day-header">
                            <h6>{{ $dia }}</h6>
                        </div>
                        <div class="day-content">
                            @if(count($horariosPorDia[$dia]) > 0)
                                @foreach($horariosPorDia[$dia] as $horario)
                                    <div class="schedule-item-admin">
                                        <div class="schedule-time">
                                            {{ substr($horario->hora_inicio, 0, 5) }} - {{ substr($horario->hora_fin, 0, 5) }}
                                        </div>
                                        <div class="schedule-room">
                                            <i class="fas fa-door-open me-1"></i>
                                            {{ $horario->aula ?? 'Sin aula' }}
                                        </div>
                                        <div class="schedule-actions">
                                            <a href="{{ route('admin.horarios.edit', [$seccion->id, $horario->id]) }}" 
                                               class="btn-sm-edit" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" 
                                                  action="{{ route('admin.horarios.destroy', [$seccion->id, $horario->id]) }}" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('¿Está seguro de eliminar este horario?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-sm-delete" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="empty-day">
                                    <i class="fas fa-calendar-times"></i>
                                    <p>Sin horarios</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Resumen de Horarios -->
    <div class="card-dark mt-4">
        <div class="card-header-dark">
            <h5><i class="fas fa-list me-2"></i>Lista de Horarios</h5>
        </div>
        <div class="card-body-dark">
            @if($seccion->horarios->count() > 0)
                <div class="table-responsive">
                    <table class="table table-dark table-hover">
                        <thead>
                            <tr>
                                <th><i class="fas fa-calendar-day me-2"></i>Día</th>
                                <th><i class="fas fa-clock me-2"></i>Hora Inicio</th>
                                <th><i class="fas fa-clock me-2"></i>Hora Fin</th>
                                <th><i class="fas fa-door-open me-2"></i>Aula</th>
                                <th><i class="fas fa-cogs me-2"></i>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($seccion->horarios->sortBy(['dia_semana', 'hora_inicio']) as $horario)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary">{{ $horario->dia_semana }}</span>
                                    </td>
                                    <td>{{ substr($horario->hora_inicio, 0, 5) }}</td>
                                    <td>{{ substr($horario->hora_fin, 0, 5) }}</td>
                                    <td>{{ $horario->aula ?? 'Sin asignar' }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.horarios.edit', [$seccion->id, $horario->id]) }}" 
                                               class="btn btn-sm btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" 
                                                  action="{{ route('admin.horarios.destroy', [$seccion->id, $horario->id]) }}" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('¿Está seguro de eliminar este horario?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-calendar-times fa-3x mb-3"></i>
                    <h5>No hay horarios registrados</h5>
                    <p>Esta sección aún no tiene horarios asignados.</p>
                    <a href="{{ route('admin.horarios.create', $seccion->id) }}" class="btn-action-neon">
                        <i class="fas fa-plus me-2"></i>Agregar Primer Horario
                    </a>
                </div>
            @endif
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

    .profesor-info-header {
        color: #e2e8f0;
        font-weight: 500;
        font-size: 0.9rem;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
    }

    .profesor-info-header i {
        color: var(--neon-cyan);
        font-size: 0.8rem;
    }

    .profesor-info-header span {
        color: #f1f5f9;
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
        margin: 0;
    }

    .card-body-dark { padding: 1.5rem; }

    /* Schedule Grid */
    .schedule-grid-admin {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .day-column {
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 12px;
        overflow: hidden;
    }

    .day-header {
        background: rgba(0, 212, 255, 0.1);
        padding: 1rem;
        text-align: center;
        border-bottom: 1px solid rgba(0, 212, 255, 0.3);
    }

    .day-header h6 {
        color: var(--neon-cyan);
        font-weight: 600;
        margin: 0;
    }

    .day-content {
        padding: 1rem;
        min-height: 200px;
    }

    .schedule-item-admin {
        background: rgba(0, 212, 255, 0.1);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 8px;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
    }

    .schedule-item-admin:hover {
        background: rgba(0, 212, 255, 0.2);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 212, 255, 0.3);
    }

    .schedule-time {
        color: var(--neon-cyan);
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }

    .schedule-room {
        color: #cbd5e1;
        font-size: 0.8rem;
        margin-bottom: 0.5rem;
    }

    .schedule-actions {
        display: flex;
        gap: 0.5rem;
        justify-content: flex-end;
    }

    .btn-sm-edit, .btn-sm-delete {
        padding: 0.25rem 0.5rem;
        border: none;
        border-radius: 6px;
        font-size: 0.75rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-sm-edit {
        background: rgba(251, 191, 36, 0.2);
        color: #fbbf24;
        border: 1px solid rgba(251, 191, 36, 0.3);
    }

    .btn-sm-edit:hover {
        background: rgba(251, 191, 36, 0.3);
        transform: scale(1.05);
    }

    .btn-sm-delete {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .btn-sm-delete:hover {
        background: rgba(239, 68, 68, 0.3);
        transform: scale(1.05);
    }

    .empty-day {
        text-align: center;
        padding: 2rem 1rem;
        color: #94a3b8;
    }

    .empty-day i {
        font-size: 2rem;
        color: rgba(0, 212, 255, 0.3);
        margin-bottom: 0.5rem;
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
        color: #f1f5f9;
        margin: 1rem 0 0.5rem 0;
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

    .table-dark {
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 8px;
    }

    .table-dark th {
        background: rgba(0, 212, 255, 0.1);
        border-color: rgba(0, 212, 255, 0.3);
        color: var(--neon-cyan);
        font-weight: 600;
    }

    .table-dark td {
        border-color: rgba(0, 212, 255, 0.2);
        color: #cbd5e1;
    }

    .table-hover tbody tr:hover {
        background: rgba(0, 212, 255, 0.1);
    }

    @media (max-width: 768px) {
        .schedule-grid-admin {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush
@endsection
