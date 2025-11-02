@extends('layouts.app')

@section('title', 'Detalle de Inscripción')
@section('page-title', 'Detalle de Inscripción')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            @if(session('success'))
                <div class="alert-success-dark mb-4">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            {{-- ✅ ADVERTENCIA si no hay sección --}}
            @if(!$tienSeccionValida)
                <div class="alert-danger-dark mb-4">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Advertencia:</strong> Esta inscripción no tiene una sección válida asociada. 
                    Por favor, edite la inscripción para asignarle una sección válida o elimínela.
                </div>
            @endif

            <div class="card-dark mb-4">
                <div class="card-header-dark">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <h5 class="mb-0">
                            <i class="fas fa-clipboard-list me-2"></i>Inscripción #{{ $inscripcion->id }}
                        </h5>
                        @php
                            $estadoClase = [
                                'inscrito' => 'status-active',
                                'completado' => 'status-info',
                                'retirado' => 'status-inactive',
                            ][$inscripcion->estado] ?? 'status-inactive';
                        @endphp
                        <span class="status-badge {{ $estadoClase }}">
                            <i class="fas fa-circle"></i> {{ ucfirst($inscripcion->estado) }}
                        </span>
                    </div>
                </div>

                <div class="card-body-dark">
                    <div class="row g-4">
                        {{-- ESTUDIANTE --}}
                        <div class="col-12">
                            <div class="info-section">
                                <div class="info-header">
                                    <i class="fas fa-user-graduate me-2"></i>
                                    <h6>Datos del Estudiante</h6>
                                </div>
                                <div class="student-card">
                                    <div class="student-avatar bg-gradient-primary">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="student-details">
                                        <h4 class="mb-1">{{ $inscripcion->estudiante->nombre_completo ?? 'N/A' }}</h4>
                                        <p class="text-muted mb-0">{{ $inscripcion->estudiante->email ?? 'N/A' }}</p>
                                        <span class="badge-neon mt-2">{{ $inscripcion->estudiante->codigo ?? 'N/A' }}</span>
                                    </div>
                                    @if($inscripcion->estudiante)
                                    <div class="ms-auto student-actions">
                                        <a href="{{ route('admin.usuarios.show', $inscripcion->estudiante) }}" class="btn-neon btn-sm">
                                            <i class="fas fa-external-link-alt me-2"></i>Ver Perfil
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- CURSO Y SECCIÓN --}}
                        <div class="col-md-6">
                            <div class="info-section">
                                <div class="info-header">
                                    <i class="fas fa-book me-2"></i>
                                    <h6>Curso y Sección</h6>
                                </div>
                                <div class="info-content">
                                    @if($tienSeccionValida && isset($inscripcion->seccion))
                                        <div class="detail-item">
                                            <span class="detail-label">Curso:</span>
                                            <span class="detail-value">
                                                @if($inscripcion->seccion->curso)
                                                    <a href="{{ route('admin.cursos.show', $inscripcion->seccion->curso) }}" class="text-neon">
                                                        {{ $inscripcion->seccion->curso->nombre }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">Curso no disponible</span>
                                                @endif
                                            </span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">Sección:</span>
                                            <span class="detail-value">
                                                <span class="badge-info">{{ $inscripcion->seccion->codigo_seccion }}</span>
                                            </span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">Período:</span>
                                            <span class="detail-value">
                                                @if($inscripcion->seccion->periodo)
                                                    <span class="badge-success">{{ $inscripcion->seccion->periodo->nombre }}</span>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="detail-label">Profesor:</span>
                                            <span class="detail-value">
                                                @if($inscripcion->seccion->profesor)
                                                    {{ $inscripcion->seccion->profesor->nombre_completo }}
                                                @else
                                                    <span class="text-muted">Sin asignar</span>
                                                @endif
                                            </span>
                                        </div>
                                    @else
                                        <div class="alert-danger-dark">
                                            <i class="fas fa-exclamation-circle me-2"></i>
                                            <strong>Sección no disponible</strong>
                                            <p class="mb-0 mt-2">Esta inscripción no tiene una sección válida asignada o la sección fue eliminada.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- FECHAS Y ESTADO --}}
                        <div class="col-md-6">
                            <div class="info-section">
                                <div class="info-header">
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    <h6>Fechas y Estado</h6>
                                </div>
                                <div class="info-content">
                                    <div class="detail-item">
    <span class="detail-label">Fecha de Inscripción:</span>
    <span class="detail-value">
        @if($inscripcion->fecha_inscripcion)
            <span class="badge-success">
                <i class="fas fa-calendar-check me-1"></i>
                {{ $inscripcion->fecha_inscripcion->format('d/m/Y') }}
            </span>
        @else
            <span class="text-muted">N/A</span>
        @endif
    </span>
</div>

<div class="detail-item">
    <span class="detail-label">Fecha de Retiro:</span>
    <span class="detail-value">
        @if($inscripcion->estado === 'retirado' && $inscripcion->fecha_retiro)
            <span class="badge-danger">
                <i class="fas fa-calendar-times me-1"></i>
                {{ $inscripcion->fecha_retiro->format('d/m/Y') }}
            </span>
        @elseif($inscripcion->estado === 'retirado' && !$inscripcion->fecha_retiro)
            <span class="badge-danger">
                <i class="fas fa-exclamation-triangle me-1"></i>
                Sin fecha registrada
            </span>
        @else
            <span class="text-muted">
                <i class="fas fa-check-circle me-1"></i>
                Estudiante activo
            </span>
        @endif
    </span>
</div>

<div class="detail-item">
    <span class="detail-label">Estado de la Inscripción:</span>
    <span class="detail-value">
        @php
            $estadoConfig = [
                'inscrito' => ['text' => 'Inscrito Activo', 'class' => 'status-active', 'icon' => 'fa-check-circle'],
                'completado' => ['text' => 'Completado', 'class' => 'status-info', 'icon' => 'fa-graduation-cap'],
                'retirado' => ['text' => 'Retirado', 'class' => 'status-inactive', 'icon' => 'fa-times-circle'],
            ][$inscripcion->estado] ?? ['text' => 'Desconocido', 'class' => 'status-inactive', 'icon' => 'fa-question'];
        @endphp
        <span class="status-badge {{ $estadoConfig['class'] }}">
            <i class="fas {{ $estadoConfig['icon'] }}"></i> 
            {{ $estadoConfig['text'] }}
        </span>
    </span>
</div>
                                    @if($tienSeccionValida && $inscripcion->seccion->horarios)
                                        <div class="detail-item">
                                            <span class="detail-label">Días de Clase:</span>
                                            <div class="horarios-grid">
                                                @if($inscripcion->seccion->horarios->count() > 0)
                                                    @foreach($inscripcion->seccion->horarios as $horario)
                                                        <span class="badge-neon">
                                                            {{ $horario->dia_semana }} 
                                                            ({{ \Carbon\Carbon::parse($horario->hora_inicio)->format('H:i') }} - 
                                                            {{ \Carbon\Carbon::parse($horario->hora_fin)->format('H:i') }})
                                                        </span>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">Sin Horarios</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- RESUMEN DE CALIFICACIÓN --}}
                        <div class="col-12">
                            <div class="info-section">
                                <div class="info-header">
                                    <i class="fas fa-graduation-cap me-2"></i>
                                    <h6>Resumen de Calificación</h6>
                                </div>
                                <div class="grade-summary">
                                    <div class="grade-box">
                                        <p class="grade-label">Nota Final</p>
                                        @php
                                            $notaClass = 'status-pendiente';
                                            if ($inscripcion->nota_final !== null) {
                                                $notaClass = $inscripcion->esta_aprobado ? 'status-aprobado' : 'status-reprobado';
                                            }
                                        @endphp
                                        <div class="nota-value {{ $notaClass }}">
                                            {{ $inscripcion->nota_final !== null ? number_format($inscripcion->nota_final, 2) : '--' }}
                                        </div>
                                        <p class="grade-status">{{ $inscripcion->estado_nota }}</p>
                                    </div>

                                    <div class="grade-detail">
                                        @if($tienSeccionValida)
                                            <div class="detail-item">
                                                <span class="detail-label">
                                                    <i class="fas fa-chart-line me-1"></i>
                                                    Promedio de Evaluaciones:
                                                </span>
                                                <span class="detail-value">
                                                    @if($estadisticas['promedio_evaluaciones'] !== null)
                                                        <span class="badge-success">
                                                            {{ number_format($estadisticas['promedio_evaluaciones'], 2) }} pts
                                                        </span>
                                                    @else
                                                        <span class="text-muted">Sin evaluaciones calificadas</span>
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="detail-item">
                                                <span class="detail-label">
                                                    <i class="fas fa-calendar-check me-1"></i>
                                                    Asistencia Total:
                                                </span>
                                                <span class="detail-value">
                                                    @if($estadisticas['asistencias']['total'] > 0)
                                                        <span class="badge-info">
                                                            {{ $estadisticas['asistencias']['porcentaje'] }}% 
                                                            ({{ $estadisticas['asistencias']['asistencias_validas'] }}/{{ $estadisticas['asistencias']['total'] }})
                                                        </span>
                                                    @else
                                                        <span class="text-muted">Sin registros de asistencia</span>
                                                    @endif
                                                </span>
                                            </div>
                                            <div class="detail-item">
                                                <span class="detail-label">
                                                    <i class="fas fa-flag me-1"></i>
                                                    Estado de la Sección:
                                                </span>
                                                <span class="detail-value">
                                                    <span class="status-badge {{ $inscripcion->seccion->estado === 'activo' ? 'status-active' : 'status-inactive' }}">
                                                        <i class="fas fa-circle"></i> {{ ucfirst($inscripcion->seccion->estado) }}
                                                    </span>
                                                </span>
                                            </div>
                                        @else
                                            <div class="alert-danger-dark">
                                                <i class="fas fa-info-circle me-2"></i>
                                                No se pueden mostrar estadísticas sin una sección válida
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- RESUMEN DE ASISTENCIA --}}
<div class="col-12 mt-4">
    <div class="info-section">
        <div class="info-header">
            <i class="fas fa-calendar-check me-2"></i>
            <h6>Registro de Asistencia</h6>
        </div>
        
        @if($tienSeccionValida && $inscripcion->asistencias->count() > 0)
            {{-- Estadísticas de Asistencia --}}
            <div class="asistencia-stats mb-4">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="stat-mini bg-gradient-success">
                            <div class="stat-mini-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stat-mini-content">
                                <h4>{{ $estadisticas['asistencias']['presente'] }}</h4>
                                <p>Presente</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-mini bg-gradient-warning">
                            <div class="stat-mini-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-mini-content">
                                <h4>{{ $estadisticas['asistencias']['tardanza'] }}</h4>
                                <p>Tardanza</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-mini bg-gradient-danger">
                            <div class="stat-mini-icon">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <div class="stat-mini-content">
                                <h4>{{ $estadisticas['asistencias']['ausente'] }}</h4>
                                <p>Ausente</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-mini bg-gradient-primary">
                            <div class="stat-mini-icon">
                                <i class="fas fa-percentage"></i>
                            </div>
                            <div class="stat-mini-content">
                                <h4>{{ $estadisticas['asistencias']['porcentaje'] }}%</h4>
                                <p>Porcentaje</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Barra de Progreso --}}
            <div class="asistencia-progress mb-4">
                <div class="progress-label mb-2">
                    <span class="text-neon">Progreso de Asistencia</span>
                    <span class="text-muted">
                        {{ $estadisticas['asistencias']['asistencias_validas'] }} / {{ $estadisticas['asistencias']['total'] }} clases
                    </span>
                </div>
                <div class="progress-asistencia">
                    <div class="progress-bar-asistencia" 
                         style="width: {{ $estadisticas['asistencias']['porcentaje'] }}%"
                         data-porcentaje="{{ $estadisticas['asistencias']['porcentaje'] }}">
                        {{ $estadisticas['asistencias']['porcentaje'] }}%
                    </div>
                </div>
            </div>

            {{-- Tabla de Últimas Asistencias --}}
            <div class="asistencia-table-wrapper">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-neon mb-0">
                        <i class="fas fa-list me-2"></i>Últimos Registros
                    </h6>
                    <a href="{{ route('admin.asistencias.index', ['search' => $inscripcion->estudiante->codigo]) }}" 
                       class="btn-neon btn-sm">
                        <i class="fas fa-external-link-alt me-2"></i>Ver Todos
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table-dark-mini">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Observaciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inscripcion->asistencias()->orderBy('fecha', 'desc')->limit(10)->get() as $asistencia)
                                <tr>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <strong>{{ $asistencia->fecha->format('d/m/Y') }}</strong>
                                            <small class="text-muted">{{ $asistencia->fecha->locale('es')->dayName }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $estadoConfig = [
                                                'presente' => ['text' => 'Presente', 'class' => 'badge-success', 'icon' => 'fa-check-circle'],
                                                'tardanza' => ['text' => 'Tardanza', 'class' => 'badge-warning', 'icon' => 'fa-clock'],
                                                'ausente' => ['text' => 'Ausente', 'class' => 'badge-danger', 'icon' => 'fa-times-circle'],
                                                'justificado' => ['text' => 'Justificado', 'class' => 'badge-info', 'icon' => 'fa-file-alt'],
                                            ][$asistencia->estado] ?? ['text' => 'Desconocido', 'class' => 'badge-secondary', 'icon' => 'fa-question'];
                                        @endphp
                                        <span class="badge-mini {{ $estadoConfig['class'] }}">
                                            <i class="fas {{ $estadoConfig['icon'] }} me-1"></i>
                                            {{ $estadoConfig['text'] }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($asistencia->observaciones)
                                            <span class="text-muted" title="{{ $asistencia->observaciones }}">
                                                {{ Str::limit($asistencia->observaciones, 40) }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        @elseif(!$tienSeccionValida)
            <div class="alert-danger-dark">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>No se puede mostrar asistencia</strong>
                <p class="mb-0 mt-2">Esta inscripción no tiene una sección válida asociada.</p>
            </div>
        @else
            <div class="empty-state-mini">
                <i class="fas fa-calendar-times fa-2x mb-3"></i>
                <h6>Sin registros de asistencia</h6>
                <p class="text-muted mb-0">Aún no se han tomado asistencias para esta inscripción</p>
            </div>
        @endif
    </div>
</div>
                    </div>
                </div>
            </div>



            {{-- BOTONES DE ACCIÓN --}}
            <div class="card-dark">
                <div class="card-body-dark">
                    <div class="action-buttons-large d-flex justify-content-end gap-3 flex-wrap">
                        <a href="{{ route('admin.inscripciones.edit', $inscripcion) }}" class="btn-neon-lg">
                            <i class="fas fa-edit me-2"></i>Editar Inscripción
                        </a>
                        <a href="{{ route('admin.inscripciones.index') }}" class="btn-outline-neon">
                            <i class="fas fa-arrow-left me-2"></i>Volver al Listado
                        </a>
                        <button type="button" class="btn-danger-neon" onclick="confirmarEliminacion()">
                            <i class="fas fa-trash me-2"></i>Eliminar Inscripción
                        </button>
                    </div>

                    <form id="delete-form" 
                          action="{{ route('admin.inscripciones.destroy', $inscripcion) }}" 
                          method="POST" 
                          style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Mini Stats para Asistencia */
.stat-mini {
    padding: 1rem;
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 1rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.3s ease;
}

.stat-mini:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 20px rgba(0, 212, 255, 0.2);
}

.stat-mini-icon {
    font-size: 2rem;
    color: white;
    opacity: 0.9;
}

.stat-mini-content h4 {
    font-size: 1.75rem;
    font-weight: 700;
    color: white;
    margin: 0;
    text-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
}

.stat-mini-content p {
    color: rgba(255, 255, 255, 0.8);
    margin: 0;
    font-size: 0.875rem;
    font-weight: 500;
}

/* Progress Bar Asistencia */
.progress-asistencia {
    background: rgba(15, 23, 42, 0.8);
    border: 1px solid rgba(0, 212, 255, 0.3);
    height: 35px;
    border-radius: 10px;
    overflow: hidden;
    position: relative;
}

.progress-bar-asistencia {
    background: linear-gradient(90deg, #10b981 0%, #0ea5e9 100%);
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 0.875rem;
    transition: width 0.6s ease;
    box-shadow: 0 0 20px rgba(16, 185, 129, 0.5);
}

.progress-bar-asistencia[data-porcentaje^="8"],
.progress-bar-asistencia[data-porcentaje^="9"],
.progress-bar-asistencia[data-porcentaje="100"] {
    background: linear-gradient(90deg, #10b981 0%, #059669 100%);
}

.progress-bar-asistencia[data-porcentaje^="6"],
.progress-bar-asistencia[data-porcentaje^="7"] {
    background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%);
    box-shadow: 0 0 20px rgba(245, 158, 11, 0.5);
}

.progress-bar-asistencia[data-porcentaje^="0"],
.progress-bar-asistencia[data-porcentaje^="1"],
.progress-bar-asistencia[data-porcentaje^="2"],
.progress-bar-asistencia[data-porcentaje^="3"],
.progress-bar-asistencia[data-porcentaje^="4"],
.progress-bar-asistencia[data-porcentaje^="5"] {
    background: linear-gradient(90deg, #ef4444 0%, #dc2626 100%);
    box-shadow: 0 0 20px rgba(239, 68, 68, 0.5);
}

.progress-label {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.875rem;
}

/* Tabla Mini */
.table-dark-mini {
    width: 100%;
    color: var(--text-color);
    font-size: 0.875rem;
}

.table-dark-mini thead th {
    background: linear-gradient(135deg, rgba(14, 165, 233, 0.2) 0%, rgba(0, 212, 255, 0.1) 100%);
    color: var(--neon-cyan);
    text-transform: uppercase;
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.75rem;
    border: none;
}

.table-dark-mini tbody tr {
    border-bottom: 1px solid rgba(0, 212, 255, 0.1);
    transition: all 0.3s ease;
}

.table-dark-mini tbody tr:hover {
    background: rgba(0, 212, 255, 0.05);
}

.table-dark-mini tbody td {
    padding: 0.75rem;
    vertical-align: middle;
}

.badge-mini {
    padding: 0.25rem 0.6rem;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
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

.badge-info {
    background: rgba(6, 182, 212, 0.2);
    color: #06b6d4;
    border: 1px solid #06b6d4;
}

.empty-state-mini {
    text-align: center;
    padding: 3rem 2rem;
    color: var(--muted-text);
}

.empty-state-mini i {
    color: rgba(0, 212, 255, 0.3);
}

.empty-state-mini h6 {
    color: var(--text-color);
    margin-top: 1rem;
    margin-bottom: 0.5rem;
}

.asistencia-table-wrapper {
    background: rgba(15, 23, 42, 0.5);
    border: 1px solid rgba(0, 212, 255, 0.2);
    border-radius: 10px;
    padding: 1rem;
}

.btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
}

@media (max-width: 768px) {
    .stat-mini {
        flex-direction: column;
        text-align: center;
    }

    .stat-mini-icon {
        font-size: 1.5rem;
    }

    .stat-mini-content h4 {
        font-size: 1.5rem;
    }
}
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
        padding: 1rem 1.5rem;
        color: var(--text-color);
    }

    .card-header-dark h5 {
        color: var(--neon-cyan);
    }

    .card-body-dark {
        padding: 1.5rem;
        color: var(--text-color);
    }

    .alert-success-dark {
        background-color: rgba(16, 185, 129, 0.2);
        border: 1px solid #10b981;
        color: #10b981;
        padding: 1rem;
        border-radius: 8px;
    }

    .alert-danger-dark {
        background-color: rgba(239, 68, 68, 0.2);
        border: 1px solid #ef4444;
        color: #ef4444;
        padding: 1rem;
        border-radius: 8px;
    }

    .badge-danger {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 1px solid #ef4444;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }
    
    .btn-neon-lg {
        background: linear-gradient(135deg, #0ea5e9 0%, #00d4ff 100%);
        border: none;
        color: var(--dark-bg);
        font-weight: 700;
        padding: 1rem 2.5rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
        font-size: 1rem;
        text-decoration: none;
        display: inline-block;
    }

    .btn-neon-lg:hover {
        color: var(--dark-bg);
        box-shadow: 0 0 30px rgba(0, 212, 255, 0.6);
        transform: translateY(-2px);
    }

    .btn-neon {
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        border: none;
        color: #0f172a;
        font-weight: 600;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
        text-decoration: none;
        display: inline-block;
    }

    .btn-neon:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 25px rgba(0, 212, 255, 0.6);
        color: #0f172a;
    }

    .btn-outline-neon {
        background: transparent;
        border: 2px solid var(--neon-cyan);
        color: var(--neon-cyan);
        font-weight: 600;
        padding: 1rem 2.5rem;
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
    
    .btn-danger-neon {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        border: none;
        color: white;
        font-weight: 600;
        padding: 1rem 2.5rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        box-shadow: 0 0 20px rgba(239, 68, 68, 0.3);
        font-size: 1rem;
        cursor: pointer;
    }

    .btn-danger-neon:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 30px rgba(239, 68, 68, 0.6);
    }

    .info-section {
        margin-bottom: 2rem;
        padding: 1.5rem;
        border: 1px solid rgba(0, 212, 255, 0.1);
        border-radius: 10px;
        background-color: rgba(14, 165, 233, 0.05);
    }

    .info-header {
        border-bottom: 2px solid var(--neon-cyan);
        padding-bottom: 0.5rem;
        margin-bottom: 1.5rem;
        color: var(--neon-cyan);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-header h6 {
        margin: 0;
        color: inherit;
    }

    .detail-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px dashed rgba(0, 212, 255, 0.1);
    }

    .detail-item:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-weight: 600;
        color: var(--neon-blue);
    }

    .detail-value {
        color: var(--text-color);
        text-align: right;
    }

    .text-neon {
        color: var(--neon-cyan);
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .text-neon:hover {
        color: var(--neon-blue);
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .badge-neon {
        background: rgba(0, 212, 255, 0.1);
        color: var(--neon-cyan);
        padding: 0.4em 0.8em;
        border-radius: 5px;
        font-weight: 600;
        border: 1px solid rgba(0, 212, 255, 0.3);
        display: inline-block;
    }

    .badge-info {
        background: rgba(6, 182, 212, 0.1);
        color: #06b6d4;
        padding: 0.4em 0.8em;
        border-radius: 5px;
        font-weight: 600;
        display: inline-block;
    }

    .badge-success {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
        padding: 0.4em 0.8em;
        border-radius: 5px;
        font-weight: 600;
        display: inline-block;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.3rem 0.75rem;
        border-radius: 15px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-active {
        background-color: rgba(16, 185, 129, 0.2);
        color: var(--success-color);
        border: 1px solid #10b981;
    }

    .status-inactive {
        background-color: rgba(239, 68, 68, 0.2);
        color: var(--danger-color);
        border: 1px solid #ef4444;
    }

    .status-info {
        background-color: rgba(6, 182, 212, 0.2);
        color: var(--neon-cyan);
        border: 1px solid #06b6d4;
    }

    .student-card {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        padding: 1rem;
        background: rgba(30, 41, 59, 0.5);
        border-radius: 8px;
        border: 1px solid rgba(0, 212, 255, 0.1);
    }

    .student-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
    }

    .student-details h4 {
        color: var(--text-color);
        font-size: 1.25rem;
    }

    .text-muted {
        color: var(--muted-text) !important;
    }

    .horarios-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }

    .grade-summary {
        display: flex;
        gap: 2rem;
        align-items: center;
    }

    .grade-box {
        text-align: center;
        padding: 1rem;
        border-right: 1px solid rgba(0, 212, 255, 0.2);
        min-width: 150px;
    }

    .grade-label {
        font-weight: 600;
        color: var(--muted-text);
        margin-bottom: 0.5rem;
    }

    .nota-value {
        font-size: 3.5rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.5rem;
        text-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
        transition: all 0.3s ease;
    }

    .status-aprobado {
        color: var(--success-color);
        text-shadow: 0 0 15px rgba(16, 185, 129, 0.5);
    }

    .status-reprobado {
        color: var(--danger-color);
        text-shadow: 0 0 15px rgba(239, 68, 68, 0.5);
    }

    .status-pendiente {
        color: var(--neon-cyan);
        text-shadow: 0 0 15px rgba(0, 212, 255, 0.5);
    }

    .grade-status {
        color: var(--muted-text);
        font-weight: 600;
    }
    
    .grade-detail {
        flex-grow: 1;
        padding-left: 1rem;
    }

    .grade-detail .detail-item {
        border-bottom: 1px solid rgba(0, 212, 255, 0.1);
    }

    @media (max-width: 992px) {
        .grade-summary {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
        }

        .grade-box {
            border-right: none;
            border-bottom: 1px solid rgba(0, 212, 255, 0.2);
            padding-bottom: 1rem;
            width: 100%;
        }

        .grade-detail {
            padding-left: 0;
            width: 100%;
        }
    }

    @media (max-width: 768px) {
        .student-card {
            flex-direction: column;
            text-align: center;
            align-items: center;
        }
        .student-actions {
            margin-top: 1rem;
            margin-left: 0 !important;
        }
        .detail-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
        .horarios-grid {
            flex-direction: column;
        }
        .action-buttons-large {
            flex-direction: column;
        }
        .btn-neon-lg,
        .btn-outline-neon,
        .btn-danger-neon {
            width: 100%;
        }
        .nota-value {
            font-size: 2.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function confirmarEliminacion() {
        if (confirm('¿Estás seguro de que deseas eliminar esta inscripción? Esta acción es irreversible.')) {
            document.getElementById('delete-form').submit();
        }
    }
</script>
@endpush
@endsection