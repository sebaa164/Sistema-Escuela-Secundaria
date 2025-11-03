@extends('layouts.app')

@section('title', 'Editar Inscripción')
@section('page-title', 'Editar Inscripción')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <div class="card-dark">
                <div class="card-header-dark">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="mb-0">
                            <i class="fas fa-edit me-2"></i>Editar Inscripción
                        </h5>
                        <div class="d-flex gap-2 align-items-center flex-wrap">
                            <span class="badge-neon">Nº {{ $inscripcion->id }}</span>
                            @if($inscripcion->seccion)
                                <span class="badge-info">{{ $inscripcion->seccion->codigo_seccion }}</span>
                            @endif
                            @php
                                $estadoConfig = [
                                    'inscrito' => ['text' => 'Activo', 'class' => 'status-badge status-active'],
                                    'completado' => ['text' => 'Completado', 'class' => 'status-badge status-info'],
                                    'retirado' => ['text' => 'Retirado', 'class' => 'status-badge status-inactive'],
                                ][$inscripcion->estado] ?? ['text' => 'Desconocido', 'class' => 'badge-neon'];
                            @endphp
                            <span class="{{ $estadoConfig['class'] }}">
                                <i class="fas fa-circle"></i> {{ $estadoConfig['text'] }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card-body-dark">
                    {{-- Información del estudiante --}}
                    <div class="info-box-dark mb-4">
                        <div class="info-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div class="info-content">
                            <h6 class="mb-1">Estudiante Inscrito</h6>
                            <p class="mb-0">{{ $inscripcion->estudiante->nombre_completo }} ({{ $inscripcion->estudiante->email }})</p>
                        </div>
                    </div>

                    @if($errors->any())
                        <div class="alert-danger-dark mb-4">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Por favor corrige los siguientes errores:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.inscripciones.update', $inscripcion) }}" method="POST" id="editForm">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            {{-- Sección del Curso/Sección --}}
                            <div class="col-12">
                                <div class="section-header">
                                    <i class="fas fa-book me-2"></i>
                                    <h6>Datos del Curso/Sección</h6>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="seccion_id" class="form-label-dark">
                                    <i class="fas fa-layer-group me-2"></i>Sección *
                                </label>
                                <select name="seccion_id" id="seccion_id" class="form-select-dark @error('seccion_id') is-invalid @enderror" required>
                                    <option value="">Asignar un Curso</option>
                                    @foreach($secciones as $seccion)
                                        <option value="{{ $seccion->id }}"
                                                data-cupo-maximo="{{ $seccion->cupo_maximo }}"
                                                data-cupo-actual="{{ $seccion->inscripciones()->where('estado', 'inscrito')->count() - ($inscripcion->seccion_id == $seccion->id ? 1 : 0) }}"
                                                {{ old('seccion_id', $inscripcion->seccion_id) == $seccion->id ? 'selected' : '' }}>
                                            {{ $seccion->curso->nombre }} - {{ $seccion->codigo_seccion }} ({{ $seccion->periodo->nombre }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('seccion_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <div id="cupo-info" style="display: none; padding: 1rem; border-radius: 8px; border: 1px solid var(--neon-cyan); background-color: rgba(0, 212, 255, 0.05);">
                                    <p class="mb-2"><i class="fas fa-users me-2"></i>Cupos Disponibles: <strong id="cupo-disponible" class="text-warning"></strong></p>
                                    <div class="progress" style="height: 10px; background-color: var(--dark-bg); border-radius: 5px;">
                                        <div id="cupo-bar" class="cupo-bar" role="progressbar" style="width: 0%; border-radius: 5px;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Sección de Estado y Calificación --}}
                            <div class="col-12 mt-4">
                                <div class="section-header">
                                    <i class="fas fa-sliders-h me-2"></i>
                                    <h6>Estado y Calificación</h6>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label for="estado" class="form-label-dark">
                                    <i class="fas fa-list-alt me-2"></i>Estado *
                                </label>
                                <select name="estado" id="estado" class="form-select-dark @error('estado') is-invalid @enderror" required>
                                    <option value="inscrito" {{ old('estado', $inscripcion->estado) == 'inscrito' ? 'selected' : '' }}>Inscrito</option>
                                    <option value="retirado" {{ old('estado', $inscripcion->estado) == 'retirado' ? 'selected' : '' }}>Retirado</option>
                                    <option value="completado" {{ old('estado', $inscripcion->estado) == 'completado' ? 'selected' : '' }}>Completado</option>
                                </select>
                                @error('estado')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="nota_final" class="form-label-dark">
                                    <i class="fas fa-percent me-2"></i>Nota Final
                                </label>
                                <input type="number" 
                                       name="nota_final" 
                                       id="nota_final" 
                                       class="form-control-dark @error('nota_final') is-invalid @enderror" 
                                       placeholder="Ej: 85.50"
                                       step="0.01"
                                       min="0"
                                       max="100"
                                       value="{{ old('nota_final', $inscripcion->nota_final) }}">
                                @error('nota_final')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
    <label for="fecha_retiro" class="form-label-dark">
        <i class="fas fa-calendar-times me-2"></i>Fecha de Retiro
    </label>
    <input type="date" 
           name="fecha_retiro" 
           id="fecha_retiro" 
           class="form-control-dark @error('fecha_retiro') is-invalid @enderror" 
           value="{{ old('fecha_retiro', $inscripcion->fecha_retiro ? $inscripcion->fecha_retiro->format('Y-m-d') : '') }}">
    @error('fecha_retiro')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="text-muted d-block mt-1">
        <i class="fas fa-info-circle me-1"></i>Solo si el estado es "Retirado"
    </small>
</div>

<script>
// Script para manejar automáticamente la fecha de retiro según el estado
document.getElementById('estado').addEventListener('change', function() {
    const fechaRetiroInput = document.getElementById('fecha_retiro');
    
    if (this.value === 'retirado') {
        // Si se selecciona "retirado" y no hay fecha, poner la fecha actual
        if (!fechaRetiroInput.value) {
            const today = new Date().toISOString().split('T')[0];
            fechaRetiroInput.value = today;
        }
        fechaRetiroInput.removeAttribute('disabled');
    } else {
        // Si NO es retirado, limpiar y deshabilitar el campo
        fechaRetiroInput.value = '';
        fechaRetiroInput.setAttribute('disabled', 'disabled');
    }
});

// Ejecutar al cargar la página
window.addEventListener('load', function() {
    const estadoSelect = document.getElementById('estado');
    const fechaRetiroInput = document.getElementById('fecha_retiro');
    
    if (estadoSelect.value !== 'retirado') {
        fechaRetiroInput.setAttribute('disabled', 'disabled');
    }
});
</script>

                            {{-- Botones de acción --}}
                            <div class="col-12 mt-5">
                                <div class="action-buttons-large d-flex justify-content-end gap-3 flex-wrap">
                                    <a href="{{ route('admin.inscripciones.index') }}" class="btn-outline-neon">
                                        <i class="fas fa-arrow-left me-2"></i>Volver
                                    </a>
                                    <button type="button" class="btn-danger-neon" onclick="confirmarEliminacion()">
                                        <i class="fas fa-trash me-2"></i>Eliminar Inscripción
                                    </button>
                                    <button type="submit" class="btn-neon-lg">
                                        <i class="fas fa-save me-2"></i>Guardar Cambios
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    {{-- Formulario oculto para eliminar --}}
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
    
    .breadcrumb-dark {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(30, 41, 59, 0.9) 100%);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 10px;
        padding: 1rem 1.5rem;
        margin: 0;
        display: flex;
        flex-wrap: wrap;
        list-style: none;
    }

    .breadcrumb-dark .breadcrumb-item {
        color: var(--muted-text);
    }

    .breadcrumb-dark .breadcrumb-item a {
        color: var(--neon-cyan);
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .breadcrumb-dark .breadcrumb-item a:hover {
        color: var(--neon-blue);
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .breadcrumb-dark .breadcrumb-item.active {
        color: var(--text-color);
    }

    .breadcrumb-dark .breadcrumb-item + .breadcrumb-item::before {
        content: "›";
        color: rgba(0, 212, 255, 0.5);
        padding-right: 0.5rem;
        padding-left: 0.5rem;
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
        padding: 2rem;
        color: var(--text-color);
    }

    .info-box-dark {
        background: rgba(0, 212, 255, 0.05);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
        padding: 1rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .info-box-dark .info-icon {
        font-size: 2rem;
        color: var(--neon-cyan);
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
        flex-shrink: 0;
    }
    
    .info-box-dark .info-content h6 {
        color: var(--neon-blue);
        margin: 0;
        font-size: 0.875rem;
        font-weight: 600;
    }
    
    .info-box-dark .info-content p {
        color: var(--text-color);
        margin: 0;
        font-size: 0.95rem;
    }

    .form-label-dark {
        color: var(--neon-cyan);
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-control-dark,
    .form-select-dark {
        background-color: var(--dark-card);
        border: 1px solid rgba(0, 212, 255, 0.3);
        color: var(--text-color);
        padding: 0.75rem 1.25rem;
        border-radius: 8px;
        transition: all 0.3s ease;
        width: 100%;
    }

    .form-control-dark:focus,
    .form-select-dark:focus {
        background-color: var(--dark-bg);
        border-color: var(--neon-cyan);
        box-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
        color: var(--text-color);
        outline: none;
    }

    .form-select-dark option {
        background-color: var(--dark-card);
        color: var(--text-color);
    }
    
    .section-header {
        border-bottom: 2px solid var(--neon-cyan);
        padding-bottom: 0.5rem;
        margin-bottom: 1.5rem;
        color: var(--neon-cyan);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-header h6 {
        margin: 0;
        font-weight: 600;
    }

    .alert-danger-dark {
        background-color: rgba(239, 68, 68, 0.2);
        border: 1px solid #ef4444;
        color: #ef4444;
        padding: 1rem;
        border-radius: 8px;
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
        cursor: pointer;
    }

    .btn-neon-lg:hover {
        color: var(--dark-bg);
        box-shadow: 0 0 30px rgba(0, 212, 255, 0.6);
        transform: translateY(-2px);
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

    .badge-neon {
        background: rgba(0, 212, 255, 0.1);
        color: var(--neon-cyan);
        padding: 0.4em 0.8em;
        border-radius: 5px;
        font-weight: 600;
        border: 1px solid rgba(0, 212, 255, 0.3);
        font-size: 0.875rem;
        display: inline-block;
    }

    .badge-info {
        background: rgba(6, 182, 212, 0.2);
        color: #06b6d4;
        padding: 0.4em 0.8em;
        border-radius: 5px;
        font-weight: 600;
        border: 1px solid #06b6d4;
        font-size: 0.875rem;
        display: inline-block;
    }

    .badge-success {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        padding: 0.4em 0.8em;
        border-radius: 5px;
        font-weight: 600;
        border: 1px solid #10b981;
        font-size: 0.875rem;
        display: inline-block;
    }

    .badge-danger {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        padding: 0.4em 0.8em;
        border-radius: 5px;
        font-weight: 600;
        border: 1px solid #ef4444;
        font-size: 0.875rem;
        display: inline-block;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.4em 0.8em;
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
        color: #06b6d4;
        border: 1px solid #06b6d4;
    }

    .status-badge i {
        font-size: 0.5rem;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }

    .progress {
        background-color: var(--dark-bg);
        border: 1px solid rgba(0, 212, 255, 0.3);
        height: 10px;
        border-radius: 5px;
        overflow: hidden;
    }
    
    .cupo-bar {
        background: linear-gradient(90deg, #10b981 0%, #0ea5e9 100%);
        transition: width 0.6s ease;
        height: 100%;
    }
    
    .cupo-bar.warning {
        background: linear-gradient(90deg, #f59e0b 0%, #f97316 100%);
    }
    
    .cupo-bar.full {
        background: linear-gradient(90deg, #ef4444 0%, #dc2626 100%);
    }

    .text-muted {
        color: var(--muted-text) !important;
        font-size: 0.875rem;
    }

    .invalid-feedback {
        color: var(--danger-color);
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: block;
    }

    .action-buttons-large {
        padding-top: 1rem;
        border-top: 1px solid rgba(0, 212, 255, 0.1);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .card-body-dark {
            padding: 1.5rem;
        }

        .info-box-dark {
            flex-direction: column;
            text-align: center;
        }

        .action-buttons-large {
            flex-direction: column;
        }

        .btn-neon-lg,
        .btn-outline-neon,
        .btn-danger-neon {
            width: 100%;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Función para actualizar información de cupos
    document.getElementById('seccion_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const cupoMaximo = parseInt(selectedOption.getAttribute('data-cupo-maximo'));
        const cupoActual = parseInt(selectedOption.getAttribute('data-cupo-actual'));
        const cupoInfo = document.getElementById('cupo-info');
        const cupoDisponibleEl = document.getElementById('cupo-disponible');
        const cupoBar = document.getElementById('cupo-bar');
        
        if (cupoMaximo > 0) {
            const cupoDisponible = cupoMaximo - cupoActual;
            const porcentaje = (cupoActual / cupoMaximo) * 100;

            cupoDisponibleEl.textContent = `${cupoDisponible} / ${cupoMaximo}`;
            
            cupoDisponibleEl.classList.remove('text-success', 'text-warning', 'text-danger');
            
            let colorClass = 'text-success';
            if (porcentaje >= 80 && porcentaje < 100) {
                colorClass = 'text-warning';
            } else if (porcentaje >= 100) {
                colorClass = 'text-danger';
            }
            cupoDisponibleEl.classList.add(colorClass);

            cupoBar.style.width = `${porcentaje}%`;
            
            cupoBar.className = 'cupo-bar';
            if (porcentaje >= 100) {
                cupoBar.classList.add('full');
            } else if (porcentaje >= 80) {
                cupoBar.classList.add('warning');
            }
            
            cupoInfo.style.display = 'block';
        } else {
            cupoInfo.style.display = 'none';
        }
    });

    // Trigger al cargar si ya hay una sección seleccionada
    window.addEventListener('load', function() {
        const seccionSelect = document.getElementById('seccion_id');
        if (seccionSelect.value) {
            seccionSelect.dispatchEvent(new Event('change'));
        }
    });

    // Validación del formulario
    document.getElementById('editForm').addEventListener('submit', function(e) {
        const seccionId = document.getElementById('seccion_id').value;
        
        if (!seccionId) {
            e.preventDefault();
            alert('Por favor selecciona una sección');
            return false;
        }
    });

    // Función para confirmar eliminación
    function confirmarEliminacion() {
        if (confirm('¿Estás seguro de que deseas eliminar esta inscripción? Esta acción no se puede deshacer y eliminará todas las calificaciones y asistencias asociadas.')) {
            document.getElementById('delete-form').submit();
        }
    }
</script>
@endpush
@endsection