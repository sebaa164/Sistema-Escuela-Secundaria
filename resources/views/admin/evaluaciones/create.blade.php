@extends('layouts.app')

@section('title', 'Crear Evaluación')
@section('page-title', 'Nueva Evaluación')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Card Principal -->
            <div class="card-dark">
                <div class="card-header-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i>Crear Nueva Evaluación
                    </h5>
                </div>

                <div class="card-body-dark">
                    <form action="{{ route('admin.evaluaciones.store') }}" method="POST" id="createEvaluacionForm">
                        @csrf

                        <div class="row g-4">
                            <!-- Sección -->
                            <div class="col-12">
                                <div class="section-header">
                                    <i class="fas fa-chalkboard me-2"></i>
                                    <h6>Seleccionar Materia/Sección</h6>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label-dark">
                                    <i class="fas fa-chalkboard me-2"></i>Sección *
                                </label>
                                <select name="seccion_id" 
                                        id="seccion_id"
                                        class="form-select-dark @error('seccion_id') is-invalid @enderror" 
                                        required>
                                    <option value="">-- Seleccione una sección --</option>
                                    @foreach($secciones as $seccion)
                                        <option value="{{ $seccion->id }}" 
                                                {{ old('seccion_id') == $seccion->id ? 'selected' : '' }}
                                                data-inscritos="{{ $seccion->estudiantes_inscritos }}"
                                                data-cupo="{{ $seccion->cupo_maximo }}">
                                            {{ $seccion->curso->codigo_curso }} - {{ $seccion->curso->nombre }}
                                            | Sección: {{ $seccion->nombre }}
                                            | Profesor: {{ $seccion->profesor->nombre_completo }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('seccion_id')
                                    <div class="invalid-feedback-dark">{{ $message }}</div>
                                @enderror
                                <div id="seccion-info" class="alert-info-dark mt-3" style="display:none;"></div>
                            </div>

                            <!-- Información de la Evaluación -->
                            <div class="col-12">
                                <div class="section-header mt-3">
                                    <i class="fas fa-file-alt me-2"></i>
                                    <h6>Información de la Evaluación</h6>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-dark">
                                    <i class="fas fa-tags me-2"></i>Tipo de Evaluación *
                                </label>
                                <select name="tipo_evaluacion_id" 
                                        class="form-select-dark @error('tipo_evaluacion_id') is-invalid @enderror" 
                                        required>
                                    <option value="">-- Seleccione un tipo --</option>
                                    @foreach($tiposEvaluacion as $tipo)
                                        <option value="{{ $tipo->id }}" {{ old('tipo_evaluacion_id') == $tipo->id ? 'selected' : '' }}>
                                            {{ $tipo->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tipo_evaluacion_id')
                                    <div class="invalid-feedback-dark">{{ $message }}</div>
                                @enderror
                                <small class="form-text-dark">Ejemplos: Examen, Tarea, Proyecto, Quiz</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-dark">
                                    <i class="fas fa-heading me-2"></i>Nombre de la Evaluación *
                                </label>
                                <input type="text" 
                                       name="nombre" 
                                       class="form-control-dark @error('nombre') is-invalid @enderror" 
                                       value="{{ old('nombre') }}"
                                       placeholder="Ej: Examen Parcial 1"
                                       maxlength="150"
                                       required>
                                @error('nombre')
                                    <div class="invalid-feedback-dark">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label-dark">
                                    <i class="fas fa-align-left me-2"></i>Descripción
                                </label>
                                <textarea name="descripcion" 
                                          class="form-control-dark @error('descripcion') is-invalid @enderror" 
                                          rows="3"
                                          placeholder="Descripción opcional de la evaluación...">{{ old('descripcion') }}</textarea>
                                @error('descripcion')
                                    <div class="invalid-feedback-dark">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Configuración -->
                            <div class="col-12">
                                <div class="section-header mt-3">
                                    <i class="fas fa-cog me-2"></i>
                                    <h6>Configuración</h6>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label-dark">
                                    <i class="fas fa-calendar-alt me-2"></i>Fecha de Evaluación *
                                </label>
                                <input type="date" 
                                       name="fecha_evaluacion" 
                                       class="form-control-dark @error('fecha_evaluacion') is-invalid @enderror" 
                                       value="{{ old('fecha_evaluacion', now()->format('Y-m-d')) }}"
                                       required>
                                @error('fecha_evaluacion')
                                    <div class="invalid-feedback-dark">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label-dark">
                                    <i class="fas fa-trophy me-2"></i>Nota Máxima *
                                </label>
                                <input type="number" 
                                       name="nota_maxima" 
                                       class="form-control-dark @error('nota_maxima') is-invalid @enderror" 
                                       value="{{ old('nota_maxima', 100) }}"
                                       min="0" max="100" step="0.01" required>
                                @error('nota_maxima')
                                    <div class="invalid-feedback-dark">{{ $message }}</div>
                                @enderror
                                <small class="form-text-dark">Escala de calificación hasta este valor. Aprobación: 60.</small>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label-dark">
                                    <i class="fas fa-percentage me-2"></i>Peso en el curso (opcional)
                                </label>
                                <input type="number" 
                                       name="porcentaje" 
                                       class="form-control-dark @error('porcentaje') is-invalid @enderror" 
                                       value="{{ old('porcentaje') }}"
                                       min="0"
                                       max="100"
                                       step="0.01"
                                       placeholder="Ej: 20 para 20% (dejar vacío si no usa peso)">
                                @error('porcentaje')
                                    <div class="invalid-feedback-dark">{{ $message }}</div>
                                @enderror
                                <small class="form-text-dark">Si no usas ponderación por porcentaje, puedes dejarlo vacío.</small>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label-dark">
                                    <i class="fas fa-toggle-on me-2"></i>Estado
                                </label>
                                <select name="estado" class="form-select-dark">
                                    <option value="programada" {{ old('estado', 'programada') == 'programada' ? 'selected' : '' }}>Programada</option>
                                    <option value="activa" {{ old('estado') == 'activa' ? 'selected' : '' }}>En Curso</option>
                                    <option value="finalizada" {{ old('estado') == 'finalizada' ? 'selected' : '' }}>Finalizada</option>
                                    <option value="cancelada" {{ old('estado') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                                </select>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="form-actions mt-5">
                            <button type="submit" class="btn-neon-lg">
                                <i class="fas fa-save me-2"></i>Crear Evaluación
                            </button>
                            <a href="{{ route('admin.evaluaciones.index') }}" class="btn-outline-neon">
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
    /* Card Dark */
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
        font-size: 0.875rem;
    }

    /* Form Labels */
    .form-label-dark {
        color: #e2e8f0;
        font-weight: 500;
        margin-bottom: 0.5rem;
        display: block;
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
    }

    .form-control-dark:focus, .form-select-dark:focus {
        background: rgba(15, 23, 42, 0.95);
        border-color: var(--neon-cyan);
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
        outline: none;
        color: white;
    }

    .form-control-dark.is-invalid, .form-select-dark.is-invalid {
        border-color: #ef4444;
    }

    .form-text-dark {
        color: #94a3b8;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: block;
    }

    .invalid-feedback-dark {
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: block;
    }

    /* Form Actions */
    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: center;
        padding-top: 2rem;
        border-top: 1px solid rgba(0, 212, 255, 0.2);
    }

    .btn-neon-lg {
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        border: none;
        color: #0f172a;
        font-weight: 600;
        padding: 1rem 2.5rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
        font-size: 1rem;
    }

    .btn-neon-lg:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 30px rgba(0, 212, 255, 0.6);
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

    /* Responsive */
    @media (max-width: 768px) {
        .form-actions {
            flex-direction: column;
        }

        .btn-neon-lg,
        .btn-outline-neon {
            width: 100%;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Validación básica del formulario
    document.getElementById('createEvaluacionForm').addEventListener('submit', function(e) {
        const seccionId = document.getElementById('seccion_id').value;
        if (!seccionId) {
            e.preventDefault();
            alert('Por favor selecciona una sección');
            return false;
        }
    });
</script>
@endpush
@endsection