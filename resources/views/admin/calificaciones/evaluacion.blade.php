@extends('layouts.app')

@section('title', 'Crear Evaluación')

@section('content')
<div class="container-fluid px-3 px-md-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            
            <div class="card-dark">
                <div class="card-header-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i>Crear Nueva Evaluación
                    </h5>
                </div>

                <div class="card-body-dark">
                    <form action="{{ route('admin.evaluaciones.store') }}" method="POST">
                        @csrf

                        <div class="row g-3 g-md-4">
                            <!-- Sección -->
                            <div class="col-12">
                                <label class="form-label-dark">
                                    <i class="fas fa-chalkboard me-2"></i>Sección *
                                </label>
                                <select name="seccion_id" 
                                        class="form-select-dark @error('seccion_id') is-invalid @enderror" 
                                        required>
                                    <option value="">-- Seleccione una sección --</option>
                                    @foreach($secciones as $seccion)
                                        <option value="{{ $seccion->id }}" {{ old('seccion_id') == $seccion->id ? 'selected' : '' }}>
                                            {{ $seccion->curso->codigo_curso }} - {{ $seccion->curso->nombre }}
                                            | Sección: {{ $seccion->nombre }}
                                            | Profesor: {{ $seccion->profesor->nombre_completo }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('seccion_id')
                                    <div class="invalid-feedback-dark">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Tipo de Evaluación -->
                            <div class="col-12 col-md-6">
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

                            <!-- Nombre -->
                            <div class="col-12 col-md-6">
                                <label class="form-label-dark">
                                    <i class="fas fa-heading me-2"></i>Nombre de la Evaluación *
                                </label>
                                <input type="text" 
                                       name="nombre" 
                                       class="form-control-dark @error('nombre') is-invalid @enderror" 
                                       value="{{ old('nombre') }}"
                                       placeholder="Ej: Examen Parcial 1"
                                       required>
                                @error('nombre')
                                    <div class="invalid-feedback-dark">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Descripción -->
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

                            <!-- Fecha y Hora -->
                            <div class="col-12 col-md-6">
                                <label class="form-label-dark">
                                    <i class="fas fa-calendar-alt me-2"></i>Fecha de Evaluación *
                                </label>
                                <input type="datetime-local" 
                                       name="fecha_evaluacion" 
                                       class="form-control-dark @error('fecha_evaluacion') is-invalid @enderror" 
                                       value="{{ old('fecha_evaluacion', now()->format('Y-m-d\TH:i')) }}"
                                       required>
                                @error('fecha_evaluacion')
                                    <div class="invalid-feedback-dark">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Porcentaje -->
                            <div class="col-12 col-md-6">
                                <label class="form-label-dark">
                                    <i class="fas fa-percentage me-2"></i>Porcentaje del Curso *
                                </label>
                                <input type="number" 
                                       name="porcentaje" 
                                       class="form-control-dark @error('porcentaje') is-invalid @enderror" 
                                       value="{{ old('porcentaje') }}"
                                       min="1"
                                       max="100"
                                       step="0.01"
                                       placeholder="20"
                                       required>
                                @error('porcentaje')
                                    <div class="invalid-feedback-dark">{{ $message }}</div>
                                @enderror
                                <small class="form-text-dark">Valor entre 1 y 100. Ejemplo: 20 para 20%</small>
                            </div>

                            <!-- Nota Mínima Aprobatoria -->
                            <div class="col-12 col-md-6">
                                <label class="form-label-dark">
                                    <i class="fas fa-check-circle me-2"></i>Nota Mínima para Aprobar
                                </label>
                                <input type="number" 
                                       name="nota_minima_aprobacion" 
                                       class="form-control-dark" 
                                       value="{{ old('nota_minima_aprobacion', 70) }}"
                                       min="0"
                                       max="100"
                                       step="0.01">
                                <small class="form-text-dark">Por defecto: 70</small>
                            </div>

                            <!-- Estado -->
                            <div class="col-12 col-md-6">
                                <label class="form-label-dark">
                                    <i class="fas fa-toggle-on me-2"></i>Estado
                                </label>
                                <select name="estado" class="form-select-dark">
                                    <option value="programada" {{ old('estado', 'programada') == 'programada' ? 'selected' : '' }}>Programada</option>
                                    <option value="en_curso" {{ old('estado') == 'en_curso' ? 'selected' : '' }}>En Curso</option>
                                    <option value="finalizada" {{ old('estado') == 'finalizada' ? 'selected' : '' }}>Finalizada</option>
                                    <option value="cancelada" {{ old('estado') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                                </select>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="form-actions mt-4 mt-md-5">
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
    /* Reutilizar los mismos estilos de calificaciones */
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

    .form-label-dark {
        color: #e2e8f0;
        font-weight: 500;
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-label-dark i {
        color: var(--neon-cyan);
    }

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
        text-decoration: none;
        display: inline-block;
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
    }

    .btn-outline-neon:hover {
        background: rgba(0, 212, 255, 0.1);
        transform: translateY(-2px);
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
        color: var(--neon-cyan);
    }

    @media (max-width: 768px) {
        .form-actions {
            flex-direction: column;
        }

        .btn-neon-lg,
        .btn-outline-neon {
            width: 100%;
            text-align: center;
        }
    }
</style>
@endpush
@endsection