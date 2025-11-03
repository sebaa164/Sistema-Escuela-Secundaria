@extends('layouts.app')

@section('title', 'Editar Calificación')
@section('page-title', 'Editar Calificación')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Breadcrumb -->

            <!-- Card Principal -->
            <div class="card-dark">
                <div class="card-header-dark">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-edit me-2"></i>Editar Calificación
                        </h5>
                        <span class="badge-neon">{{ $calificacion->estudiante->nombre_completo }}</span>
                    </div>
                </div>

                <div class="card-body-dark">
                    <!-- Información de Contexto -->
                    <div class="context-info mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="fas fa-user-graduate"></i>
                                    </div>
                                    <div class="info-content">
                                        <label>Estudiante</label>
                                        <p>{{ $calificacion->estudiante->nombre_completo }}</p>
                                        <small>{{ $calificacion->estudiante->email }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="fas fa-clipboard-list"></i>
                                    </div>
                                    <div class="info-content">
                                        <label>Evaluación</label>
                                        <p>{{ $calificacion->evaluacion->nombre }}</p>
                                        <small>{{ $calificacion->evaluacion->seccion->curso->nombre }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('admin.calificaciones.update', $calificacion) }}" method="POST" id="editGradeForm">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            <!-- Información de la Calificación -->
                            <div class="col-12">
                                <div class="section-header">
                                    <i class="fas fa-star me-2"></i>
                                    <h6>Información de la Calificación</h6>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label-dark">
                                    <i class="fas fa-chart-line me-2"></i>Nota *
                                </label>
                                <input type="number" 
                                       name="nota" 
                                       id="nota"
                                       class="form-control-dark @error('nota') is-invalid @enderror" 
                                       value="{{ old('nota', $calificacion->nota) }}"
                                       min="0"
                                       max="100"
                                       step="0.01"
                                       required
                                       oninput="calcularEstado()">
                                @error('nota')
                                    <div class="invalid-feedback-dark">{{ $message }}</div>
                                @enderror
                                
                                <div id="nota-preview" class="nota-preview mt-2">
                                    <span class="preview-label">Estado:</span>
                                    <span id="nota-estado" class="preview-value"></span>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label-dark">
                                    <i class="fas fa-toggle-on me-2"></i>Estado *
                                </label>
                                <select name="estado" class="form-select-dark @error('estado') is-invalid @enderror" required>
                                    <option value="pendiente" {{ old('estado', $calificacion->estado) == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="calificada" {{ old('estado', $calificacion->estado) == 'calificada' ? 'selected' : '' }}>Calificada</option>
                                    <option value="revisada" {{ old('estado', $calificacion->estado) == 'revisada' ? 'selected' : '' }}>Revisada</option>
                                </select>
                                @error('estado')
                                    <div class="invalid-feedback-dark">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label-dark">
                                    <i class="fas fa-calendar-check me-2"></i>Fecha de Calificación
                                </label>
                                <input type="datetime-local" 
                                       name="fecha_calificacion" 
                                       class="form-control-dark @error('fecha_calificacion') is-invalid @enderror" 
                                       value="{{ old('fecha_calificacion', $calificacion->fecha_calificacion->format('Y-m-d\TH:i')) }}">
                                @error('fecha_calificacion')
                                    <div class="invalid-feedback-dark">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label-dark">
                                    <i class="fas fa-comment me-2"></i>Comentarios
                                </label>
                                <textarea name="comentarios" 
                                          class="form-control-dark @error('comentarios') is-invalid @enderror" 
                                          rows="5"
                                          maxlength="1000"
                                          placeholder="Observaciones, retroalimentación o comentarios sobre el desempeño del estudiante...">{{ old('comentarios', $calificacion->comentarios) }}</textarea>
                                @error('comentarios')
                                    <div class="invalid-feedback-dark">{{ $message }}</div>
                                @enderror
                                <small class="form-text-dark">Máximo 1000 caracteres</small>
                            </div>

                            <!-- Información Adicional -->
                            <div class="col-12">
                                <div class="section-header mt-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <h6>Información Adicional</h6>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="info-box">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <p class="info-item">
                                                <i class="fas fa-percentage me-2"></i>
                                                <strong>Porcentaje Evaluación:</strong> 
                                                {{ $calificacion->evaluacion->porcentaje }}%
                                            </p>
                                        </div>
                                        <div class="col-md-3">
                                            <p class="info-item">
                                                <i class="fas fa-calendar me-2"></i>
                                                <strong>Fecha Evaluación:</strong> 
                                                {{ $calificacion->evaluacion->fecha_evaluacion->format('d/m/Y') }}
                                            </p>
                                        </div>
                                        <div class="col-md-3">
                                            <p class="info-item">
                                                <i class="fas fa-clock me-2"></i>
                                                <strong>Creada:</strong> 
                                                {{ $calificacion->created_at->format('d/m/Y H:i') }}
                                            </p>
                                        </div>
                                        <div class="col-md-3">
                                            <p class="info-item">
                                                <i class="fas fa-sync me-2"></i>
                                                <strong>Actualizada:</strong> 
                                                {{ $calificacion->updated_at->format('d/m/Y H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="form-actions mt-5">
                            <button type="submit" class="btn-neon-lg">
                                <i class="fas fa-save me-2"></i>Guardar Cambios
                            </button>
                            <a href="{{ route('admin.calificaciones.index') }}" class="btn-outline-neon">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="button" 
                                    class="btn-danger-neon"
                                    onclick="confirmarEliminacion()">
                                <i class="fas fa-trash me-2"></i>Eliminar Calificación
                            </button>
                        </div>
                    </form>

                    <!-- Formulario de eliminación oculto -->
                    <form id="delete-form" 
                          action="{{ route('admin.calificaciones.destroy', $calificacion) }}" 
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
    /* Breadcrumb Dark */
    .breadcrumb-dark {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(30, 41, 59, 0.9) 100%);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 10px;
        padding: 1rem 1.5rem;
        margin: 0;
    }

    .breadcrumb-dark .breadcrumb-item {
        color: #94a3b8;
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
        color: #e2e8f0;
    }

    .breadcrumb-dark .breadcrumb-item + .breadcrumb-item::before {
        content: "›";
        color: rgba(0, 212, 255, 0.5);
    }

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

    .badge-neon {
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        color: #0f172a;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.875rem;
        box-shadow: 0 0 10px rgba(0, 212, 255, 0.3);
    }

    /* Context Info */
    .context-info {
        padding: 1.5rem;
        background: rgba(0, 212, 255, 0.05);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 15px;
    }

    .info-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
        height: 100%;
    }

    .info-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: #0f172a;
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.5);
        flex-shrink: 0;
    }

    .info-content label {
        color: #94a3b8;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 0.25rem;
        display: block;
    }

    .info-content p {
        color: #e2e8f0;
        font-weight: 600;
        margin: 0;
    }

    .info-content small {
        color: #64748b;
        font-size: 0.875rem;
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

    .form-control-dark.is-invalid {
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

    /* Nota Preview */
    .nota-preview {
        background: rgba(0, 212, 255, 0.05);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
        padding: 0.75rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .preview-label {
        color: #94a3b8;
        font-weight: 500;
    }

    .preview-value {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.875rem;
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

    /* Info Box */
    .info-box {
        background: rgba(0, 212, 255, 0.05);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
        padding: 1.5rem;
    }

    .info-item {
        color: #94a3b8;
        margin: 0.5rem 0;
    }

    .info-item i {
        color: var(--neon-cyan);
    }

    .info-item strong {
        color: #e2e8f0;
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

    /* Responsive */
    @media (max-width: 768px) {
        .form-actions {
            flex-direction: column;
        }

        .btn-neon-lg,
        .btn-outline-neon,
        .btn-danger-neon {
            width: 100%;
        }

        .context-info .row {
            gap: 1rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    function calcularEstado() {
        const notaInput = document.getElementById('nota');
        const notaPreview = document.getElementById('nota-preview');
        const notaEstado = document.getElementById('nota-estado');
        const nota = parseFloat(notaInput.value);

        if (!isNaN(nota) && nota >= 0 && nota <= 100) {
            // Nota mínima de aprobación: 60
            if (nota >= 60) {
                notaEstado.textContent = 'Aprobado';
                notaEstado.className = 'preview-value aprobado';
            } else {
                notaEstado.textContent = 'Reprobado';
                notaEstado.className = 'preview-value reprobado';
            }
        }
    }

    // Inicializar estado al cargar
    document.addEventListener('DOMContentLoaded', function() {
        calcularEstado();
    });

    function confirmarEliminacion() {
        if (confirm('¿Estás seguro de que deseas eliminar esta calificación? Esta acción no se puede deshacer y afectará la nota final del estudiante.')) {
            document.getElementById('delete-form').submit();
        }
    }

    // Validación del formulario
    document.getElementById('editGradeForm').addEventListener('submit', function(e) {
        const nota = document.getElementById('nota').value;
        
        if (!nota) {
            e.preventDefault();
            alert('Por favor ingresa una nota');
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