@extends('layouts.app')

@section('title', 'Agregar Horario')
@section('page-title', 'Nuevo Horario')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header-dark mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-plus me-3"></i>Agregar Horario
                </h1>
                <p class="header-subtitle mb-0">
                    {{ $seccion->curso->codigo_curso }} - {{ $seccion->curso->nombre }} 
                    (Sección {{ $seccion->codigo_seccion }})
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.horarios.index', $seccion->id) }}" class="btn-outline-neon">
                    <i class="fas fa-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Error:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Formulario -->
    <div class="card-dark">
        <div class="card-header-dark">
            <h5><i class="fas fa-clock me-2"></i>Información del Horario</h5>
        </div>
        <div class="card-body-dark">
            <form method="POST" action="{{ route('admin.horarios.store', $seccion->id) }}">
                @csrf
                
                <div class="row g-3">
                    <!-- Día de la Semana -->
                    <div class="col-md-6">
                        <label for="dia_semana" class="form-label">
                            <i class="fas fa-calendar-day me-2"></i>Día de la Semana
                        </label>
                        <select class="form-select form-control-dark" id="dia_semana" name="dia_semana" required>
                            <option value="">Seleccionar día...</option>
                            @foreach($diasSemana as $dia)
                                <option value="{{ $dia }}" {{ old('dia_semana') == $dia ? 'selected' : '' }}>
                                    {{ $dia }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Aula -->
                    <div class="col-md-6">
                        <label for="aula" class="form-label">
                            <i class="fas fa-door-open me-2"></i>Aula
                        </label>
                        <input type="text" 
                               class="form-control form-control-dark" 
                               id="aula" 
                               name="aula" 
                               value="{{ old('aula') }}"
                               placeholder="Ej: A-101, Lab-1, Virtual">
                    </div>

                    <!-- Hora de Inicio -->
                    <div class="col-md-6">
                        <label for="hora_inicio" class="form-label">
                            <i class="fas fa-clock me-2"></i>Hora de Inicio
                        </label>
                        <input type="text" 
                               class="form-control form-control-dark" 
                               id="hora_inicio" 
                               name="hora_inicio" 
                               value="{{ old('hora_inicio') }}"
                               placeholder="08:30"
                               pattern="^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$"
                               maxlength="5"
                               required>
                    </div>

                    <!-- Hora de Fin -->
                    <div class="col-md-6">
                        <label for="hora_fin" class="form-label">
                            <i class="fas fa-clock me-2"></i>Hora de Fin
                        </label>
                        <input type="text" 
                               class="form-control form-control-dark" 
                               id="hora_fin" 
                               name="hora_fin" 
                               value="{{ old('hora_fin') }}"
                               placeholder="10:30"
                               pattern="^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$"
                               maxlength="5"
                               required>
                    </div>
                </div>

                <!-- Vista previa del horario -->
                <div class="mt-4">
                    <div class="preview-card" id="horario-preview" style="display: none;">
                        <h6><i class="fas fa-eye me-2"></i>Vista Previa</h6>
                        <div class="preview-content">
                            <div class="preview-item">
                                <span class="preview-label">Día:</span>
                                <span class="preview-value" id="preview-dia">-</span>
                            </div>
                            <div class="preview-item">
                                <span class="preview-label">Horario:</span>
                                <span class="preview-value" id="preview-horario">-</span>
                            </div>
                            <div class="preview-item">
                                <span class="preview-label">Aula:</span>
                                <span class="preview-value" id="preview-aula">-</span>
                            </div>
                            <div class="preview-item">
                                <span class="preview-label">Duración:</span>
                                <span class="preview-value" id="preview-duracion">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="d-flex justify-content-end gap-3 mt-4">
                    <a href="{{ route('admin.horarios.index', $seccion->id) }}" class="btn-outline-neon">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </a>
                    <button type="submit" class="btn-action-neon">
                        <i class="fas fa-save me-2"></i>Guardar Horario
                    </button>
                </div>
            </form>
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

    .form-label {
        color: var(--neon-cyan);
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .form-control-dark, .form-select {
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(0, 212, 255, 0.3);
        color: #f1f5f9;
        border-radius: 8px;
        padding: 0.75rem;
        transition: all 0.3s ease;
    }

    .form-control-dark:focus, .form-select:focus {
        background: rgba(15, 23, 42, 0.9);
        border-color: var(--neon-cyan);
        box-shadow: 0 0 0 0.2rem rgba(0, 212, 255, 0.25);
        color: #f1f5f9;
    }

    .form-control-dark::placeholder {
        color: #94a3b8;
    }

    /* Ocultar segundos en campos de tiempo */
    input[type="time"]::-webkit-datetime-edit-second-field,
    input[type="time"]::-webkit-datetime-edit-millisecond-field {
        display: none;
    }

    input[type="time"]::-webkit-datetime-edit-text {
        color: #f1f5f9;
    }

    /* Para Firefox */
    input[type="time"] {
        -moz-appearance: textfield;
    }

    .preview-card {
        background: rgba(0, 212, 255, 0.1);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 12px;
        padding: 1.5rem;
        margin-top: 1rem;
    }

    .preview-card h6 {
        color: var(--neon-cyan);
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .preview-content {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .preview-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem;
        background: rgba(15, 23, 42, 0.5);
        border-radius: 8px;
    }

    .preview-label {
        color: #94a3b8;
        font-weight: 500;
    }

    .preview-value {
        color: var(--neon-cyan);
        font-weight: 600;
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
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const diaSelect = document.getElementById('dia_semana');
    const horaInicio = document.getElementById('hora_inicio');
    const horaFin = document.getElementById('hora_fin');
    const aulaInput = document.getElementById('aula');
    const preview = document.getElementById('horario-preview');

    // Formatear automáticamente la entrada de tiempo
    function formatTimeInput(input) {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^\d]/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + ':' + value.substring(2, 4);
            }
            e.target.value = value;
        });

        input.addEventListener('blur', function(e) {
            let value = e.target.value;
            if (value.length === 5 && value.includes(':')) {
                let [hours, minutes] = value.split(':');
                hours = parseInt(hours);
                minutes = parseInt(minutes);
                
                if (hours > 23) hours = 23;
                if (minutes > 59) minutes = 59;
                
                e.target.value = String(hours).padStart(2, '0') + ':' + String(minutes).padStart(2, '0');
            }
        });
    }

    formatTimeInput(horaInicio);
    formatTimeInput(horaFin);

    function updatePreview() {
        const dia = diaSelect.value;
        const inicio = horaInicio.value;
        const fin = horaFin.value;
        const aula = aulaInput.value;

        if (dia || inicio || fin || aula) {
            preview.style.display = 'block';
            
            document.getElementById('preview-dia').textContent = dia || '-';
            document.getElementById('preview-aula').textContent = aula || 'Sin especificar';
            
            if (inicio && fin) {
                document.getElementById('preview-horario').textContent = `${inicio} - ${fin}`;
                
                // Calcular duración
                const inicioDate = new Date(`2000-01-01T${inicio}`);
                const finDate = new Date(`2000-01-01T${fin}`);
                const diffMs = finDate - inicioDate;
                const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
                const diffMinutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));
                
                if (diffMs > 0) {
                    document.getElementById('preview-duracion').textContent = 
                        `${diffHours}h ${diffMinutes}m`;
                } else {
                    document.getElementById('preview-duracion').textContent = 'Horario inválido';
                }
            } else {
                document.getElementById('preview-horario').textContent = '-';
                document.getElementById('preview-duracion').textContent = '-';
            }
        } else {
            preview.style.display = 'none';
        }
    }

    diaSelect.addEventListener('change', updatePreview);
    horaInicio.addEventListener('change', updatePreview);
    horaFin.addEventListener('change', updatePreview);
    aulaInput.addEventListener('input', updatePreview);

    // Validación en tiempo real
    horaFin.addEventListener('change', function() {
        if (horaInicio.value && horaFin.value) {
            if (horaFin.value <= horaInicio.value) {
                horaFin.setCustomValidity('La hora de fin debe ser posterior a la hora de inicio');
            } else {
                horaFin.setCustomValidity('');
            }
        }
    });

    // Actualizar preview inicial si hay valores
    updatePreview();
});
</script>
@endpush
@endsection
