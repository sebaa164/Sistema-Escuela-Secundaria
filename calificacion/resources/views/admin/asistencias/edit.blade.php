@extends('layouts.app')

@section('title', 'Editar Asistencia')
@section('page-title', 'Editar Asistencia')

@section('content')
<div class="container-fluid px-3 py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">

            <div class="card-dark">
                <div class="card-header-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Editar Registro de Asistencia
                    </h5>
                </div>

                <div class="card-body-dark">
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

                    <!-- Información del Estudiante -->
                    <div class="info-box-dark mb-4">
                        <div class="info-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div class="info-content">
                            <h6 class="mb-1">{{ $asistencia->inscripcion->estudiante->nombre_completo }}</h6>
                            <p class="mb-0">
                                <span class="info-badge">
                                    <i class="fas fa-id-card me-1"></i>
                                    {{ $asistencia->inscripcion->estudiante->codigo }}
                                </span>
                                <span class="info-badge">
                                    <i class="fas fa-book me-1"></i>
                                    {{ $asistencia->inscripcion->seccion->curso->nombre }}
                                </span>
                                <span class="info-badge">
                                    <i class="fas fa-layer-group me-1"></i>
                                    {{ $asistencia->inscripcion->seccion->codigo_seccion }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <form action="{{ route('admin.asistencias.update', $asistencia) }}" method="POST" id="editForm">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            <div class="col-12">
                                <div class="section-header">
                                    <i class="fas fa-clipboard-check me-2"></i>
                                    <h6>Datos de Asistencia</h6>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="fecha" class="form-label-dark">
                                    <i class="fas fa-calendar me-2"></i>Fecha *
                                </label>
                                <input type="date" 
                                       name="fecha" 
                                       id="fecha" 
                                       class="form-control-dark @error('fecha') is-invalid @enderror" 
                                       value="{{ old('fecha', $asistencia->fecha->format('Y-m-d')) }}"
                                       max="{{ now()->format('Y-m-d') }}"
                                       required>
                                @error('fecha')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                                <small class="text-muted d-block mt-1">
                                    <i class="fas fa-info-circle me-1"></i>Fecha actual: {{ $asistencia->fecha->format('d/m/Y') }}
                                </small>
                            </div>

                            <div class="col-md-6">
                                <label for="estado" class="form-label-dark">
                                    <i class="fas fa-check-circle me-2"></i>Estado *
                                </label>
                                <select name="estado" id="estado" 
                                        class="form-select-dark @error('estado') is-invalid @enderror" 
                                        required>
                                    <option value="" disabled>Selecciona un estado</option>
                                    <option value="presente" {{ old('estado', $asistencia->estado) == 'presente' ? 'selected' : '' }}>
                                        ✅ Presente
                                    </option>
                                    <option value="tardanza" {{ old('estado', $asistencia->estado) == 'tardanza' ? 'selected' : '' }}>
                                        ⏰ Tardanza
                                    </option>
                                    <option value="ausente" {{ old('estado', $asistencia->estado) == 'ausente' ? 'selected' : '' }}>
                                        ❌ Ausente
                                    </option>
                                    <option value="justificado" {{ old('estado', $asistencia->estado) == 'justificado' ? 'selected' : '' }}>
                                        📝 Justificado
                                    </option>
                                </select>
                                @error('estado')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="observaciones" class="form-label-dark">
                                    <i class="fas fa-sticky-note me-2"></i>Observaciones
                                </label>
                                <textarea name="observaciones" 
                                          id="observaciones" 
                                          rows="5"
                                          class="form-control-dark @error('observaciones') is-invalid @enderror"
                                          placeholder="Escribe observaciones adicionales sobre la asistencia (opcional)"
                                          maxlength="500">{{ old('observaciones', $asistencia->observaciones) }}</textarea>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>Máximo 500 caracteres
                                    </small>
                                    <small class="text-muted" id="charCount">
                                        <span id="currentChars">{{ strlen(old('observaciones', $asistencia->observaciones ?? '')) }}</span>/500
                                    </small>
                                </div>
                                @error('observaciones')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Información Adicional -->
                            <div class="col-12">
                                <div class="timestamp-box">
                                    <div class="timestamp-item">
                                        <i class="fas fa-calendar-plus me-2"></i>
                                        <span class="timestamp-label">Registrado:</span>
                                        <span class="timestamp-value">{{ $asistencia->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    @if($asistencia->updated_at->ne($asistencia->created_at))
                                        <div class="timestamp-item">
                                            <i class="fas fa-calendar-check me-2"></i>
                                            <span class="timestamp-label">Última modificación:</span>
                                            <span class="timestamp-value">{{ $asistencia->updated_at->format('d/m/Y H:i') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="col-12 mt-4">
                                <div class="form-actions">
                                    <a href="{{ route('admin.asistencias.show', $asistencia) }}" class="btn-outline-neon">
                                        <i class="fas fa-arrow-left me-2"></i>Cancelar
                                    </a>
                                    <button type="submit" class="btn-neon-lg">
                                        <i class="fas fa-save me-2"></i>Guardar Cambios
                                    </button>
                                </div>
                            </div>
                        </div>
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
        margin: 0;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .card-body-dark {
        padding: 2rem;
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

    .info-box-dark {
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.08) 0%, rgba(14, 165, 233, 0.05) 100%);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 12px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.5rem;
        transition: all 0.3s ease;
    }

    .info-box-dark:hover {
        border-color: rgba(0, 212, 255, 0.5);
        box-shadow: 0 5px 20px rgba(0, 212, 255, 0.15);
    }

    .info-icon {
        font-size: 2.5rem;
        color: var(--neon-cyan);
        text-shadow: 0 0 15px rgba(0, 212, 255, 0.5);
        flex-shrink: 0;
    }

    .info-content h6 {
        color: var(--neon-cyan);
        margin: 0 0 0.5rem 0;
        font-size: 1.1rem;
        font-weight: 700;
    }

    .info-content p {
        color: var(--text-color);
        margin: 0;
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .info-badge {
        background: rgba(0, 212, 255, 0.15);
        border: 1px solid rgba(0, 212, 255, 0.3);
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        font-size: 0.875rem;
        color: var(--text-color);
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
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
        padding: 0.75rem 1rem;
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

    .form-control-dark::placeholder {
        color: var(--muted-text);
        opacity: 0.7;
    }

    textarea.form-control-dark {
        resize: vertical;
        min-height: 120px;
    }

    .timestamp-box {
        background: rgba(0, 212, 255, 0.05);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
        padding: 1rem 1.5rem;
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
    }

    .timestamp-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--muted-text);
        font-size: 0.9rem;
    }

    .timestamp-item i {
        color: var(--neon-cyan);
    }

    .timestamp-label {
        font-weight: 600;
    }

    .timestamp-value {
        color: var(--text-color);
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

    .form-control-dark.is-invalid,
    .form-select-dark.is-invalid {
        border-color: var(--danger-color);
    }

    .alert-danger-dark {
        background-color: rgba(239, 68, 68, 0.2);
        border: 1px solid #ef4444;
        color: #ef4444;
        padding: 1rem 1.5rem;
        border-radius: 10px;
    }

    .alert-danger-dark ul {
        margin-left: 1.5rem;
        margin-top: 0.5rem;
    }

    .btn-neon-lg {
        background: linear-gradient(135deg, #0ea5e9 0%, #00d4ff 100%);
        border: none;
        color: var(--dark-bg);
        font-weight: 700;
        padding: 0.875rem 2rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
        cursor: pointer;
    }

    .btn-neon-lg:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 30px rgba(0, 212, 255, 0.6);
        color: var(--dark-bg);
    }

    .btn-outline-neon {
        background: transparent;
        border: 2px solid var(--neon-cyan);
        color: var(--neon-cyan);
        font-weight: 600;
        padding: 0.875rem 2rem;
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

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(0, 212, 255, 0.1);
    }

    #charCount {
        background: rgba(0, 212, 255, 0.1);
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        border: 1px solid rgba(0, 212, 255, 0.2);
    }

    @media (max-width: 768px) {
        .card-body-dark {
            padding: 1.5rem 1rem;
        }

        .info-box-dark {
            flex-direction: column;
            text-align: center;
        }

        .info-content p {
            justify-content: center;
        }

        .timestamp-box {
            flex-direction: column;
            gap: 0.75rem;
        }

        .timestamp-item {
            justify-content: flex-start;
        }

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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Contador de caracteres para observaciones
        const observacionesTextarea = document.getElementById('observaciones');
        const currentCharsSpan = document.getElementById('currentChars');
        
        if (observacionesTextarea && currentCharsSpan) {
            observacionesTextarea.addEventListener('input', function() {
                const length = this.value.length;
                currentCharsSpan.textContent = length;
                
                // Cambiar color cuando se acerque al límite
                const charCount = document.getElementById('charCount');
                if (length > 450) {
                    charCount.style.color = 'var(--danger-color)';
                    charCount.style.borderColor = 'var(--danger-color)';
                } else if (length > 400) {
                    charCount.style.color = 'var(--warning-color)';
                    charCount.style.borderColor = 'var(--warning-color)';
                } else {
                    charCount.style.color = 'var(--muted-text)';
                    charCount.style.borderColor = 'rgba(0, 212, 255, 0.2)';
                }
            });
        }

        // Validación antes de enviar
        const editForm = document.getElementById('editForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                const fecha = document.getElementById('fecha').value;
                const estado = document.getElementById('estado').value;

                if (!fecha || !estado) {
                    e.preventDefault();
                    alert('⚠️ Por favor completa todos los campos obligatorios (*)');
                    return false;
                }

                // Confirmación
                if (!confirm('✅ ¿Confirmar la actualización de este registro de asistencia?')) {
                    e.preventDefault();
                    return false;
                }
            });
        }

        // Highlight del campo estado al cambiar
        const estadoSelect = document.getElementById('estado');
        if (estadoSelect) {
            estadoSelect.addEventListener('change', function() {
                // Añadir efecto visual al cambiar
                this.style.transform = 'scale(1.02)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 200);
            });
        }
    });
</script>
@endpush
@endsection