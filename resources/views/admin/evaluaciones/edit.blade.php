@extends('layouts.app')

@section('title', 'Editar Evaluación')
@section('page-title', 'Editar Evaluación')

@section('content')
<div class="container-fluid px-3 px-md-4">

    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <!-- Card Principal -->
            <div class="card-dark">
                <div class="card-header-dark">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="mb-0">
                            <i class="fas fa-edit me-2"></i>Editar Evaluación
                        </h5>
                        <span class="badge-neon">#{{ $evaluacion->id }}</span>
                    </div>
                </div>

                <div class="card-body-dark">
                    <!-- Info del Curso -->
                    @if($evaluacion->seccion)
                        <div class="info-box-dark mb-4">
                            <div class="info-icon">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="info-content">
                                <h6 class="mb-1">Curso y Sección</h6>
                                <p class="mb-0">
                                    {{ $evaluacion->seccion->curso->codigo_curso }} - {{ $evaluacion->seccion->curso->nombre }}
                                    | Sección: {{ $evaluacion->seccion->nombre }}
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="info-box-dark mb-4">
                            <div class="info-icon">
                                <i class="fas fa-triangle-exclamation"></i>
                            </div>
                            <div class="info-content">
                                <h6 class="mb-1">Curso y Sección</h6>
                                <p class="mb-0 text-muted">Sin sección asignada</p>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('admin.evaluaciones.update', $evaluacion) }}" method="POST" id="editEvaluacionForm">
                        @csrf
                        @method('PUT')

                        <div class="row g-3 g-md-4">
                            <!-- Información de la Evaluación -->
                            <div class="col-12">
                                <div class="section-header">
                                    <i class="fas fa-file-alt me-2"></i>
                                    <h6>Información de la Evaluación</h6>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label-dark">
                                    <i class="fas fa-tags me-2"></i>Tipo de Evaluación *
                                </label>
                                <select name="tipo_evaluacion_id" 
                                        class="form-select-dark @error('tipo_evaluacion_id') is-invalid @enderror" 
                                        required>
                                    <option value="">-- Seleccione un tipo --</option>
                                    @foreach($tiposEvaluacion as $tipo)
                                        <option value="{{ $tipo->id }}" {{ old('tipo_evaluacion_id', $evaluacion->tipo_evaluacion_id) == $tipo->id ? 'selected' : '' }}>
                                            {{ $tipo->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tipo_evaluacion_id')
                                    <div class="invalid-feedback-dark">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label-dark">
                                    <i class="fas fa-heading me-2"></i>Nombre de la Evaluación *
                                </label>
                                <input type="text" 
                                       name="nombre" 
                                       class="form-control-dark @error('nombre') is-invalid @enderror" 
                                       value="{{ old('nombre', $evaluacion->nombre) }}"
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
                                          rows="3">{{ old('descripcion', $evaluacion->descripcion) }}</textarea>
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

                            <div class="col-12 col-md-4">
                                <label class="form-label-dark">
                                    <i class="fas fa-calendar-alt me-2"></i>Fecha de Evaluación *
                                </label>
                                <input type="date" 
                                       name="fecha_evaluacion" 
                                       class="form-control-dark @error('fecha_evaluacion') is-invalid @enderror" 
                                       value="{{ old('fecha_evaluacion', $evaluacion->fecha_evaluacion->format('Y-m-d')) }}"
                                       required>
                                @error('fecha_evaluacion')
                                    <div class="invalid-feedback-dark">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label-dark">
                                    <i class="fas fa-trophy me-2"></i>Nota Máxima *
                                </label>
                                <input type="number" 
                                       name="nota_maxima" 
                                       class="form-control-dark @error('nota_maxima') is-invalid @enderror" 
                                       value="{{ old('nota_maxima', $evaluacion->nota_maxima) }}"
                                       min="0"
                                       max="100"
                                       step="0.01"
                                       required>
                                @error('nota_maxima')
                                    <div class="invalid-feedback-dark">{{ $message }}</div>
                                @enderror
                                <small class="form-text-dark">Escala de calificación hasta este valor. Aprobación: 60.</small>
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label-dark">
                                    <i class="fas fa-percentage me-2"></i>Peso en el curso (opcional)
                                </label>
                                <input type="number" 
                                       name="porcentaje" 
                                       class="form-control-dark @error('porcentaje') is-invalid @enderror" 
                                       value="{{ old('porcentaje', $evaluacion->porcentaje) }}"
                                       min="0"
                                       max="100"
                                       step="0.01"
                                       placeholder="Ej: 20 para 20% (dejar vacío si no usa peso)">
                                @error('porcentaje')
                                    <div class="invalid-feedback-dark">{{ $message }}</div>
                                @enderror
                                <small class="form-text-dark">Si no usas ponderación por porcentaje, puedes dejarlo vacío.</small>
                            </div>

                            <div class="col-12 col-md-4">
                                <label class="form-label-dark">
                                    <i class="fas fa-toggle-on me-2"></i>Estado
                                </label>
                                <select name="estado" class="form-select-dark">
                                    <option value="programada" {{ old('estado', $evaluacion->estado) == 'programada' ? 'selected' : '' }}>Programada</option>
                                    <option value="activa" {{ old('estado', $evaluacion->estado) == 'activa' ? 'selected' : '' }}>En Curso</option>
                                    <option value="finalizada" {{ old('estado', $evaluacion->estado) == 'finalizada' ? 'selected' : '' }}>Finalizada</option>
                                    <option value="cancelada" {{ old('estado', $evaluacion->estado) == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                                </select>
                            </div>

                            <!-- Info Adicional -->
                            <div class="col-12 mt-4">
                                <div class="alert-info-dark">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Información:</strong> Esta evaluación tiene {{ $evaluacion->calificaciones->count() }} calificaciones registradas.
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="form-actions mt-4 mt-md-5">
                            <button type="submit" class="btn-neon-lg">
                                <i class="fas fa-save me-2"></i>Guardar Cambios
                            </button>
                            <a href="{{ route('admin.evaluaciones.index') }}" class="btn-outline-neon">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="button" class="btn-danger-neon" onclick="confirmarEliminacion()">
                                <i class="fas fa-trash me-2"></i>Eliminar
                            </button>
                        </div>
                    </form>

                    <!-- Formulario oculto para eliminar -->
                    <form id="delete-form" 
                          action="{{ route('admin.evaluaciones.destroy', $evaluacion) }}" 
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
    }

    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: center;
        padding-top: 2rem;
        border-top: 1px solid rgba(0, 212, 255, 0.2);
        flex-wrap: wrap;
    }

    @media (max-width: 768px) {
        .form-actions {
            flex-direction: column;
        }

        .btn-neon-lg,
        .btn-outline-neon,
        .btn-danger-neon {
            width: 100%;
            text-align: center;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function confirmarEliminacion() {
        if (confirm('¿Estás seguro de que deseas eliminar esta evaluación? Esta acción no se puede deshacer.')) {
            document.getElementById('delete-form').submit();
        }
    }
</script>
@endpush
@endsection