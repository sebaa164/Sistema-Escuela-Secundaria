@extends('layouts.app')

@section('title', 'Registrar Calificación')
@section('page-title', 'Nueva Calificación')

@section('content')
<div class="container-fluid px-3 px-md-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <!-- Card Principal -->
            <div class="card-dark">
                <div class="card-header-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-clipboard-check me-2"></i>Registrar Nueva Calificación
                    </h5>
                </div>

                <div class="card-body-dark">
                    <form action="{{ route('admin.calificaciones.store') }}" method="POST" id="createGradeForm">
                        @csrf

                        <div class="row g-3 g-md-4">
                            <!-- Selección de Evaluación -->
                            <div class="col-12">
                                <div class="section-header">
                                    <i class="fas fa-file-alt me-2"></i>
                                    <h6>Seleccionar Evaluación</h6>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label-dark">
                                    <i class="fas fa-clipboard-list me-2"></i>Evaluación *
                                </label>
                                <select name="evaluacion_id" 
                                        id="evaluacion_id"
                                        class="form-select-dark @error('evaluacion_id') is-invalid @enderror" 
                                        required
                                        onchange="cargarEstudiantes()">
                                    <option value="">Seleccione una evaluación</option>
                                    @foreach($evaluaciones as $evaluacion)
                                        <option value="{{ $evaluacion->id }}" 
                                                {{ old('evaluacion_id', request('evaluacion_id')) == $evaluacion->id ? 'selected' : '' }}
                                                data-curso="{{ $evaluacion->seccion->curso->nombre }}"
                                                data-fecha="{{ $evaluacion->fecha_evaluacion->format('d/m/Y') }}"
                                                data-porcentaje="{{ $evaluacion->porcentaje }}">
                                            {{ $evaluacion->seccion->curso->codigo_curso }} - {{ $evaluacion->nombre }}
                                            ({{ $evaluacion->fecha_evaluacion->format('d/m/Y') }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('evaluacion_id')
                                    <div class="invalid-feedback-dark">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Selección de Estudiante -->
                            <div class="col-12">
                                <div class="section-header mt-3">
                                    <i class="fas fa-user-graduate me-2"></i>
                                    <h6>Seleccionar Estudiante</h6>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label-dark">
                                    <i class="fas fa-user me-2"></i>Estudiante *
                                </label>
                                <select name="estudiante_id" 
                                        id="estudiante_id"
                                        class="form-select-dark @error('estudiante_id') is-invalid @enderror" 
                                        required
                                        {{ !$evaluacion ? 'disabled' : '' }}>
                                    <option value="">{{ $evaluacion ? 'Seleccione un estudiante' : 'Primero seleccione una evaluación' }}</option>
                                    @if($evaluacion)
                                        @foreach($estudiantes as $estudiante)
                                            <option value="{{ $estudiante->id }}" {{ old('estudiante_id') == $estudiante->id ? 'selected' : '' }}>
                                                {{ $estudiante->nombre_completo }} - {{ $estudiante->email }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('estudiante_id')
                                    <div class="invalid-feedback-dark">{{ $message }}</div>
                                @enderror

                                @if($evaluacion && $estudiantes->isEmpty())
                                    <div class="alert-warning-dark mt-3">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        No hay estudiantes disponibles para calificar en esta evaluación.
                                        Todos los estudiantes ya tienen calificación registrada.
                                    </div>
                                @endif
                            </div>

                            <!-- Calificación -->
                            <div class="col-12">
                                <div class="section-header mt-3">
                                    <i class="fas fa-star me-2"></i>
                                    <h6>Información de la Calificación</h6>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label-dark">
                                    <i class="fas fa-chart-line me-2"></i>Nota *
                                </label>
                                <input type="number" 
                                       name="nota" 
                                       id="nota"
                                       class="form-control-dark @error('nota') is-invalid @enderror" 
                                       value="{{ old('nota') }}"
                                       min="0"
                                       max="100"
                                       step="0.01"
                                       placeholder="Ingrese la nota (0-100)"
                                       required
                                       oninput="calcularEstado()">
                                @error('nota')
                                    <div class="invalid-feedback-dark">{{ $message }}</div>
                                @enderror
                                
                                <div id="nota-preview" class="nota-preview mt-2" style="display: none;">
                                    <span class="preview-label">Estado:</span>
                                    <span id="nota-estado" class="preview-value"></span>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label-dark">
                                    <i class="fas fa-calendar-check me-2"></i>Fecha de Calificación
                                </label>
                                <input type="datetime-local" 
                                       name="fecha_calificacion" 
                                       class="form-control-dark @error('fecha_calificacion') is-invalid @enderror" 
                                       value="{{ old('fecha_calificacion', now()->format('Y-m-d\TH:i')) }}">
                                @error('fecha_calificacion')
                                    <div class="invalid-feedback-dark">{{ $message }}</div>
                                @enderror
                                <small class="form-text-dark">Si no se especifica, se usará la fecha actual</small>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label-dark">
                                    <i class="fas fa-toggle-on me-2"></i>Estado
                                </label>
                                <select name="estado" class="form-select-dark">
                                    <option value="calificada" {{ old('estado', 'calificada') == 'calificada' ? 'selected' : '' }}>Calificada</option>
                                    <option value="pendiente" {{ old('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="revisada" {{ old('estado') == 'revisada' ? 'selected' : '' }}>Revisada</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label-dark">
                                    <i class="fas fa-comment me-2"></i>Comentarios
                                </label>
                                <textarea name="comentarios" 
                                          class="form-control-dark @error('comentarios') is-invalid @enderror" 
                                          rows="4"
                                          maxlength="1000"
                                          placeholder="Observaciones, retroalimentación o comentarios sobre el desempeño del estudiante...">{{ old('comentarios') }}</textarea>
                                @error('comentarios')
                                    <div class="invalid-feedback-dark">{{ $message }}</div>
                                @enderror
                                <small class="form-text-dark">Máximo 1000 caracteres</small>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="form-actions mt-4 mt-md-5">
                            <button type="submit" class="btn-neon-lg">
                                <i class="fas fa-save me-2"></i>Registrar Calificación
                            </button>
                            <a href="{{ route('admin.calificaciones.index') }}" class="btn-outline-neon">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Contenedor principal */
    .container-fluid {
        max-width: 100%;
        overflow-x: hidden;
    }

    /* Card Dark */
    .card-dark {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.95) 100%);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 0 30px rgba(0, 212, 255, 0.2);
        width: 100%;
        max-width: 100%;
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
        font-size: clamp(1rem, 3vw, 1.25rem);
    }

    .card-body-dark {
        padding: 1.5rem;
    }

    @media (min-width: 768px) {
        .card-body-dark {
            padding: 2rem;
        }
    }

    /* Section Header */
    .section-header {
        display: flex;
        align-items: center;
        color: var(--neon-cyan);
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid rgba(0, 212, 255, 0.3);
    }

    .section-header i {
        font-size: 1.25rem;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .section-header h6 {
        margin: 0;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: clamp(0.75rem, 2vw, 0.875rem);
    }

    /* Form Labels */
    .form-label-dark {
        color: #e2e8f0;
        font-weight: 500;
        margin-bottom: 0.5rem;
        display: block;
        font-size: clamp(0.875rem, 2vw, 1rem);
    }

    .form-label-dark i {
        color: var(--neon-cyan);
    }

    /* Form Controls */
    .form-control-dark, .form-select-dark {
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 10px;
        color: #e2e8f0;
        padding: 0.75rem 1rem;
        width: 100%;
        transition: all 0.3s ease;
        font-size: clamp(0.875rem, 2vw, 1rem);
    }

    .form-control-dark:focus, .form-select-dark:focus {
        background: rgba(15, 23, 42, 0.95);
        border-color: var(--neon-cyan);
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
        outline: none;
        color: white;
    }

    /* Select desplegable hacia abajo - Estilos personalizados */
    .form-select-dark option {
        background: #1e293b;
        color: #e2e8f0;
        padding: 0.75rem;
    }

    .form-select-dark option:hover,
    .form-select-dark option:checked {
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.2) 0%, rgba(14, 165, 233, 0.2) 100%);
        color: var(--neon-cyan);
    }

    /* Agregar espacio extra debajo de los selects */
    .select-container {
        min-height: 400px;
        padding-bottom: 350px;
    }

    .form-control-dark.is-invalid {
        border-color: #ef4444;
    }

    .form-control-dark:disabled, .form-select-dark:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .form-text-dark {
        color: #94a3b8;
        font-size: clamp(0.75rem, 2vw, 0.875rem);
        margin-top: 0.25rem;
        display: block;
    }

    .invalid-feedback-dark {
        color: #ef4444;
        font-size: clamp(0.75rem, 2vw, 0.875rem);
        margin-top: 0.25rem;
        display: block;
    }

    /* Alert Warning Dark */
    .alert-warning-dark {
        background: rgba(245, 158, 11, 0.1);
        border: 1px solid rgba(245, 158, 11, 0.3);
        border-radius: 10px;
        color: #f59e0b;
        padding: 1rem;
        font-size: clamp(0.875rem, 2vw, 1rem);
    }

    /* Nota Preview */
    .nota-preview {
        background: rgba(0, 212, 255, 0.05);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
        padding: 0.75rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .preview-label {
        color: #94a3b8;
        font-weight: 500;
        font-size: clamp(0.875rem, 2vw, 1rem);
    }

    .preview-value {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: clamp(0.75rem, 2vw, 0.875rem);
    }

    .preview-value.aprobado {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 1px solid #10b981;
    }

    .preview-value.reprobado {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 1px solid #ef4444;
    }

    /* Form Actions */
    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: center;
        padding-top: 2rem;
        border-top: 1px solid rgba(0, 212, 255, 0.2);
        flex-wrap: wrap;
    }

    .btn-neon-lg {
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        border: none;
        color: #0f172a;
        font-weight: 600;
        padding: 1rem 2rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
        font-size: clamp(0.875rem, 2vw, 1rem);
        text-decoration: none;
        display: inline-block;
        white-space: nowrap;
    }

    .btn-neon-lg:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 30px rgba(0, 212, 255, 0.6);
        color: #0f172a;
    }

    .btn-outline-neon {
        background: transparent;
        border: 2px solid var(--neon-cyan);
        color: var(--neon-cyan);
        font-weight: 600;
        padding: 1rem 2rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        font-size: clamp(0.875rem, 2vw, 1rem);
        white-space: nowrap;
    }

    .btn-outline-neon:hover {
        background: rgba(0, 212, 255, 0.1);
        transform: translateY(-2px);
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
        color: var(--neon-cyan);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .form-actions {
            flex-direction: column;
        }

        .btn-neon-lg,
        .btn-outline-neon {
            width: 100%;
            text-align: center;
            justify-content: center;
        }

        .card-body-dark {
            padding: 1rem;
        }
    }

    /* Fix para evitar scroll horizontal */
    body {
        overflow-x: hidden;
    }

    .row {
        margin-left: 0;
        margin-right: 0;
    }
</style>
@endpush

@push('scripts')
<script>
    // Forzar dropdown hacia abajo
    document.addEventListener('DOMContentLoaded', function() {
        const selects = document.querySelectorAll('.form-select-dark');
        
        selects.forEach(select => {
            select.addEventListener('click', function(e) {
                // Calcular espacio disponible
                const rect = this.getBoundingClientRect();
                const spaceBelow = window.innerHeight - rect.bottom;
                const spaceAbove = rect.top;
                
                // Si hay más espacio abajo, asegurar que se abra hacia abajo
                if (spaceBelow > spaceAbove || spaceBelow > 300) {
                    this.style.position = 'relative';
                    
                    // Scroll suave si es necesario
                    if (spaceBelow < 200) {
                        window.scrollBy({
                            top: 150,
                            behavior: 'smooth'
                        });
                    }
                }
            });
        });
    });

    function cargarEstudiantes() {
        const evaluacionId = document.getElementById('evaluacion_id').value;
        if (evaluacionId) {
            window.location.href = `{{ route('admin.calificaciones.create') }}?evaluacion_id=${evaluacionId}`;
        }
    }

    function calcularEstado() {
        const notaInput = document.getElementById('nota');
        const notaPreview = document.getElementById('nota-preview');
        const notaEstado = document.getElementById('nota-estado');
        const nota = parseFloat(notaInput.value);

        if (!isNaN(nota) && nota >= 0 && nota <= 100) {
            notaPreview.style.display = 'flex';
            
            // Nota mínima de aprobación: 60
            if (nota >= 60) {
                notaEstado.textContent = 'Aprobado';
                notaEstado.className = 'preview-value aprobado';
            } else {
                notaEstado.textContent = 'Reprobado';
                notaEstado.className = 'preview-value reprobado';
            }
        } else {
            notaPreview.style.display = 'none';
        }
    }

    // Validación del formulario
    document.getElementById('createGradeForm').addEventListener('submit', function(e) {
        const evaluacionId = document.getElementById('evaluacion_id').value;
        const estudianteId = document.getElementById('estudiante_id').value;
        const nota = document.getElementById('nota').value;
        
        if (!evaluacionId || !estudianteId || !nota) {
            e.preventDefault();
            alert('Por favor completa todos los campos obligatorios');
            return false;
        }

        const notaNum = parseFloat(nota);
        if (isNaN(notaNum) || notaNum < 0 || notaNum > 100) {
            e.preventDefault();
            alert('La nota debe estar entre 0 y 100');
            return false;
        }
    });
</script>
@endpush
@endsection