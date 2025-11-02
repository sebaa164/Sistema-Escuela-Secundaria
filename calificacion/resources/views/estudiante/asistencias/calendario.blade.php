@extends('layouts.app')

@section('title', 'Calendario de Asistencias - Estudiante')
@section('page-title', 'Calendario de Asistencias')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header-dark mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-calendar-alt me-3"></i>Calendario de Asistencias
                </h1>
                <p class="header-subtitle mb-0">
                    {{ $inscripcion->seccion->curso->nombre }} - {{ $inscripcion->seccion->curso->codigo_curso }}
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('estudiante.asistencias.materia', $inscripcion->id) }}" class="btn-outline-neon">
                    <i class="fas fa-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Navegación de Mes -->
    <div class="card-dark mb-4">
        <div class="card-body-dark">
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('estudiante.asistencias.calendario', ['inscripcionId' => $inscripcion->id, 'mes' => $mes == 1 ? 12 : $mes - 1, 'año' => $mes == 1 ? $año - 1 : $año]) }}" 
                   class="btn-nav-calendar">
                    <i class="fas fa-chevron-left"></i>
                </a>
                
                <h4 class="calendar-title mb-0">
                    {{ \Carbon\Carbon::create($año, $mes, 1)->locale('es')->monthName }} {{ $año }}
                </h4>
                
                <a href="{{ route('estudiante.asistencias.calendario', ['inscripcionId' => $inscripcion->id, 'mes' => $mes == 12 ? 1 : $mes + 1, 'año' => $mes == 12 ? $año + 1 : $año]) }}" 
                   class="btn-nav-calendar">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Leyenda -->
    <div class="card-dark mb-4">
        <div class="card-body-dark">
            <div class="calendar-legend">
                <div class="legend-item">
                    <span class="legend-dot presente"></span>
                    <span>Presente</span>
                </div>
                <div class="legend-item">
                    <span class="legend-dot tardanza"></span>
                    <span>Tardanza</span>
                </div>
                <div class="legend-item">
                    <span class="legend-dot ausente"></span>
                    <span>Ausente</span>
                </div>
                <div class="legend-item">
                    <span class="legend-dot justificada"></span>
                    <span>Justificada</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendario -->
    <div class="card-dark">
        <div class="card-body-dark">
            <div class="calendar-grid">
                <!-- Encabezados de días -->
                <div class="calendar-header">Domingo</div>
                <div class="calendar-header">Lunes</div>
                <div class="calendar-header">Martes</div>
                <div class="calendar-header">Miércoles</div>
                <div class="calendar-header">Jueves</div>
                <div class="calendar-header">Viernes</div>
                <div class="calendar-header">Sábado</div>

                @php
                    $primerDia = \Carbon\Carbon::create($año, $mes, 1);
                    $ultimoDia = $primerDia->copy()->endOfMonth();
                    $diasEnMes = $ultimoDia->day;
                    $diaSemanaInicio = $primerDia->dayOfWeek; // 0 = Domingo
                    $hoy = \Carbon\Carbon::now();
                @endphp

                <!-- Espacios vacíos antes del primer día -->
                @for($i = 0; $i < $diaSemanaInicio; $i++)
                    <div class="calendar-day empty"></div>
                @endfor

                <!-- Días del mes -->
                @for($dia = 1; $dia <= $diasEnMes; $dia++)
                    @php
                        $fecha = \Carbon\Carbon::create($año, $mes, $dia);
                        $fechaStr = $fecha->format('Y-m-d');
                        $asistencia = $asistencias->get($fechaStr);
                        $esHoy = $fecha->isSameDay($hoy);
                    @endphp
                    
                    <div class="calendar-day {{ $esHoy ? 'today' : '' }} {{ $asistencia ? 'has-attendance' : '' }}" 
                         data-fecha="{{ $fechaStr }}"
                         @if($asistencia)
                            data-estado="{{ $asistencia->estado }}"
                            data-observaciones="{{ $asistencia->observaciones }}"
                         @endif>
                        <div class="day-number">{{ $dia }}</div>
                        
                        @if($asistencia)
                            <div class="attendance-indicator {{ $asistencia->estado }}">
                                @switch($asistencia->estado)
                                    @case('presente')
                                        <i class="fas fa-check"></i>
                                        @break
                                    @case('tardanza')
                                        <i class="fas fa-clock"></i>
                                        @break
                                    @case('ausente')
                                        <i class="fas fa-times"></i>
                                        @break
                                    @case('justificada')
                                        <i class="fas fa-file-alt"></i>
                                        @break
                                @endswitch
                            </div>
                            @if($asistencia->observaciones)
                                <div class="has-note">
                                    <i class="fas fa-comment-dots"></i>
                                </div>
                            @endif
                        @endif
                    </div>
                @endfor

                <!-- Espacios vacíos después del último día -->
                @php
                    $diasRestantes = 7 - (($diaSemanaInicio + $diasEnMes) % 7);
                    if($diasRestantes < 7) {
                        for($i = 0; $i < $diasRestantes; $i++) {
                            echo '<div class="calendar-day empty"></div>';
                        }
                    }
                @endphp
            </div>
        </div>
    </div>
</div>

<!-- Modal de Detalles -->
<div class="modal fade" id="attendanceModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-dark">
            <div class="modal-header">
                <h5 class="modal-title">Detalles de Asistencia</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="detail-row">
                    <strong>Fecha:</strong>
                    <span id="modal-fecha"></span>
                </div>
                <div class="detail-row">
                    <strong>Estado:</strong>
                    <span id="modal-estado"></span>
                </div>
                <div class="detail-row" id="modal-observaciones-row" style="display: none;">
                    <strong>Observaciones:</strong>
                    <span id="modal-observaciones"></span>
                </div>
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

    .card-body-dark { padding: 1.5rem; }

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

    .btn-nav-calendar {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        background: rgba(0, 212, 255, 0.1);
        border: 1px solid rgba(0, 212, 255, 0.3);
        color: var(--neon-cyan);
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .btn-nav-calendar:hover {
        background: rgba(0, 212, 255, 0.2);
        border-color: var(--neon-cyan);
        transform: scale(1.1);
        color: var(--neon-cyan);
    }

    .calendar-title {
        color: var(--neon-cyan);
        font-weight: 700;
        text-transform: capitalize;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    /* Leyenda */
    .calendar-legend {
        display: flex;
        gap: 2rem;
        flex-wrap: wrap;
        justify-content: center;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #cbd5e1;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .legend-dot {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    }

    .legend-dot.presente {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
    }

    .legend-dot.tardanza {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        box-shadow: 0 0 10px rgba(245, 158, 11, 0.5);
    }

    .legend-dot.ausente {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        box-shadow: 0 0 10px rgba(239, 68, 68, 0.5);
    }

    .legend-dot.justificada {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
        box-shadow: 0 0 10px rgba(6, 182, 212, 0.5);
    }

    /* Calendario */
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 0.5rem;
    }

    .calendar-header {
        padding: 1rem;
        text-align: center;
        font-weight: 700;
        color: var(--neon-cyan);
        background: rgba(0, 212, 255, 0.1);
        border-radius: 10px;
        font-size: 0.875rem;
        text-transform: uppercase;
    }

    .calendar-day {
        aspect-ratio: 1;
        padding: 0.75rem;
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
        transition: all 0.3s ease;
        cursor: default;
    }

    .calendar-day.empty {
        background: transparent;
        border: none;
    }

    .calendar-day.has-attendance {
        cursor: pointer;
    }

    .calendar-day.has-attendance:hover {
        background: rgba(0, 212, 255, 0.1);
        border-color: var(--neon-cyan);
        transform: scale(1.05);
    }

    .calendar-day.today {
        border: 2px solid var(--neon-cyan);
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
    }

    .day-number {
        font-size: 1rem;
        font-weight: 700;
        color: #f1f5f9;
        margin-bottom: 0.25rem;
    }

    .attendance-indicator {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .attendance-indicator.presente {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
    }

    .attendance-indicator.tardanza {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        box-shadow: 0 0 10px rgba(245, 158, 11, 0.5);
    }

    .attendance-indicator.ausente {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        box-shadow: 0 0 10px rgba(239, 68, 68, 0.5);
    }

    .attendance-indicator.justificada {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
        box-shadow: 0 0 10px rgba(6, 182, 212, 0.5);
    }

    .has-note {
        position: absolute;
        top: 5px;
        right: 5px;
        color: var(--neon-cyan);
        font-size: 0.75rem;
    }

    /* Modal */
    .modal-dark .modal-content {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.98) 0%, rgba(30, 41, 59, 0.98) 100%);
        border: 1px solid rgba(0, 212, 255, 0.3);
        box-shadow: 0 0 30px rgba(0, 212, 255, 0.2);
    }

    .modal-dark .modal-header {
        border-bottom: 1px solid rgba(0, 212, 255, 0.3);
    }

    .modal-dark .modal-title {
        color: var(--neon-cyan);
        font-weight: 700;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        background: rgba(0, 212, 255, 0.05);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 8px;
    }

    .detail-row strong {
        color: var(--neon-cyan);
    }

    .detail-row span {
        color: #f1f5f9;
    }

    @media (max-width: 768px) {
        .calendar-grid {
            gap: 0.25rem;
        }

        .calendar-header {
            padding: 0.5rem;
            font-size: 0.7rem;
        }

        .calendar-day {
            padding: 0.5rem;
        }

        .day-number {
            font-size: 0.875rem;
        }

        .attendance-indicator {
            width: 25px;
            height: 25px;
            font-size: 0.75rem;
        }

        .calendar-legend {
            gap: 1rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const attendanceDays = document.querySelectorAll('.calendar-day.has-attendance');
        const modal = new bootstrap.Modal(document.getElementById('attendanceModal'));
        
        attendanceDays.forEach(day => {
            day.addEventListener('click', function() {
                const fecha = this.dataset.fecha;
                const estado = this.dataset.estado;
                const observaciones = this.dataset.observaciones;
                
                document.getElementById('modal-fecha').textContent = new Date(fecha + 'T00:00:00').toLocaleDateString('es-ES', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
                
                const estadoTexto = {
                    'presente': 'Presente',
                    'tardanza': 'Tardanza',
                    'ausente': 'Ausente',
                    'justificada': 'Justificada'
                };
                
                document.getElementById('modal-estado').textContent = estadoTexto[estado] || estado;
                
                if(observaciones && observaciones !== 'null' && observaciones !== '') {
                    document.getElementById('modal-observaciones').textContent = observaciones;
                    document.getElementById('modal-observaciones-row').style.display = 'flex';
                } else {
                    document.getElementById('modal-observaciones-row').style.display = 'none';
                }
                
                modal.show();
            });
        });
    });
</script>
@endpush
@endsection
