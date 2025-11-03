@extends('layouts.app')

@section('title', 'Crear Sección')
@section('page-title', 'Nueva Sección')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header-dark mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-plus-circle me-3"></i>
                    Crear Nueva Sección
                </h1>
                <p class="header-subtitle mb-0">Complete el formulario para crear una nueva sección</p>
            </div>
            <a href="{{ route('admin.secciones.index') }}" class="btn-outline-neon">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card-dark">
                <div class="card-header-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-chalkboard-teacher me-2"></i>
                        Información de la Sección
                    </h5>
                </div>
                <div class="card-body-dark">
                    <form method="POST" action="{{ route('admin.secciones.store') }}" id="createSectionForm">
                        @csrf

                        <!-- Información Básica -->
                        <div class="section-header">
                            <i class="fas fa-info-circle me-2"></i>
                            <h6>Información Básica</h6>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="codigo_seccion" class="form-label-dark">
                                    <i class="fas fa-barcode me-2"></i>
                                    Código de Sección
                                    <span class="required-mark">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control-dark @error('codigo_seccion') is-invalid @enderror" 
                                       id="codigo_seccion" 
                                       name="codigo_seccion" 
                                       value="{{ old('codigo_seccion') }}"
                                       placeholder="Ej: SEC-001, A1, MAT-L1"
                                       maxlength="50"
                                       required
                                       autofocus>
                                @error('codigo_seccion')
                                    <div class="invalid-feedback-dark">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                                <small class="form-text-dark">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Código único identificador (máximo 50 caracteres)
                                </small>
                            </div>

                            <div class="col-md-6">
                                <label for="cupo_maximo" class="form-label-dark">
                                    <i class="fas fa-users me-2"></i>
                                    Cupo Máximo
                                    <span class="required-mark">*</span>
                                </label>
                                <input type="number" 
                                       class="form-control-dark @error('cupo_maximo') is-invalid @enderror" 
                                       id="cupo_maximo" 
                                       name="cupo_maximo" 
                                       value="{{ old('cupo_maximo', 30) }}"
                                       min="1"
                                       max="100"
                                       required>
                                @error('cupo_maximo')
                                    <div class="invalid-feedback-dark">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                                <small class="form-text-dark">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Número máximo de estudiantes (1-100)
                                </small>
                            </div>
                        </div>

                        <div class="row g-4 mt-2">
                            <div class="col-md-4">
                                <label for="curso_id" class="form-label-dark">
                                    <i class="fas fa-book me-2"></i>
                                    Curso
                                    <span class="required-mark">*</span>
                                </label>
                                <select class="form-select-dark @error('curso_id') is-invalid @enderror" 
                                        id="curso_id" 
                                        name="curso_id" 
                                        required>
                                    <option value="">Seleccione un curso</option>
                                    @foreach($cursos as $curso)
                                        <option value="{{ $curso->id }}" {{ old('curso_id') == $curso->id ? 'selected' : '' }}>
                                            {{ $curso->codigo_curso }} - {{ $curso->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('curso_id')
                                    <div class="invalid-feedback-dark">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="profesor_id" class="form-label-dark">
                                    <i class="fas fa-user-tie me-2"></i>
                                    Profesor
                                    <span class="required-mark">*</span>
                                </label>
                                <select class="form-select-dark @error('profesor_id') is-invalid @enderror" 
                                        id="profesor_id" 
                                        name="profesor_id" 
                                        required>
                                    <option value="">Seleccione un profesor</option>
                                    @foreach($profesores as $profesor)
                                        <option value="{{ $profesor->id }}" {{ old('profesor_id') == $profesor->id ? 'selected' : '' }}>
                                            {{ $profesor->nombre_completo }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('profesor_id')
                                    <div class="invalid-feedback-dark">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="periodo_id" class="form-label-dark">
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    Período Académico
                                    <span class="required-mark">*</span>
                                </label>
                                <select class="form-select-dark @error('periodo_id') is-invalid @enderror" 
                                        id="periodo_id" 
                                        name="periodo_id" 
                                        required>
                                    <option value="">Seleccione un período</option>
                                    @foreach($periodos as $periodo)
                                        <option value="{{ $periodo->id }}" {{ old('periodo_id') == $periodo->id ? 'selected' : '' }}>
                                            {{ $periodo->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('periodo_id')
                                    <div class="invalid-feedback-dark">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Modalidad -->
                        <div class="section-header mt-4">
                            <i class="fas fa-graduation-cap me-2"></i>
                            <h6>Modalidad de Enseñanza</h6>
                        </div>

                        <div class="radio-group">
                            <div class="radio-option">
                                <input type="radio" 
                                       id="presencial" 
                                       name="modalidad" 
                                       value="presencial" 
                                       {{ old('modalidad', 'presencial') == 'presencial' ? 'checked' : '' }}
                                       required>
                                <label for="presencial" class="radio-label">
                                    <i class="fas fa-school"></i>
                                    <span>Presencial</span>
                                    <small class="radio-description">Clases en el campus universitario</small>
                                </label>
                            </div>

                            <div class="radio-option">
                                <input type="radio" 
                                       id="virtual" 
                                       name="modalidad" 
                                       value="virtual" 
                                       {{ old('modalidad') == 'virtual' ? 'checked' : '' }}
                                       required>
                                <label for="virtual" class="radio-label">
                                    <i class="fas fa-laptop-house"></i>
                                    <span>Virtual</span>
                                    <small class="radio-description">Clases 100% en línea</small>
                                </label>
                            </div>
                        </div>
                        @error('modalidad')
                            <div class="invalid-feedback-dark d-block mt-2">
                                <i class="fas fa-exclamation-circle me-1"></i>
                                {{ $message }}
                            </div>
                        @enderror

                        <!-- Horario -->
                        <div class="section-header mt-4">
                            <i class="fas fa-clock me-2"></i>
                            <h6>Horario</h6>
                        </div>

                        <div class="row g-4">
                            <div class="col-12">
                                <label for="horario" class="form-label-dark">
                                    <i class="fas fa-calendar-week me-2"></i>
                                    Descripción del Horario
                                </label>
                                <textarea class="form-control-dark @error('horario') is-invalid @enderror" 
                                          id="horario" 
                                          name="horario" 
                                          rows="4"
                                          placeholder="Ej: Lunes y Miércoles 8:00-10:00 AM, Viernes 14:00-16:00 PM">{{ old('horario') }}</textarea>
                                @error('horario')
                                    <div class="invalid-feedback-dark">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                                <small class="form-text-dark">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Describe los días y horarios de clase
                                </small>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="form-actions mt-5">
                            <button type="submit" class="btn-neon-lg">
                                <i class="fas fa-save me-2"></i>Crear Sección
                            </button>
                            <a href="{{ route('admin.secciones.index') }}" class="btn-outline-neon">
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
    /* Variables */
    :root {
        --neon-cyan: #00ffff;
        --neon-blue: #00d4ff;
        --electric-blue: #0ea5e9;
        --deep-blue: #0284c7;
    }

    /* Page Header Dark */
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

    .header-subtitle {
        color: #94a3b8;
        margin-top: 0.5rem;
    }

    /* Card Dark */
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
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid rgba(0, 212, 255, 0.3);
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

    .required-mark {
        color: #ef4444;
        margin-left: 0.25rem;
    }

    /* Form Controls */
    .form-control-dark {
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 10px;
        color: #e2e8f0;
        padding: 0.75rem 1rem;
        width: 100%;
        transition: all 0.3s ease;
    }

    .form-control-dark:focus {
        background: rgba(15, 23, 42, 0.95);
        border-color: var(--neon-cyan);
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
        outline: none;
        color: white;
    }

    .form-control-dark::placeholder {
        color: #64748b;
    }

    .form-control-dark.is-invalid {
        border-color: #ef4444;
    }

    .form-select-dark {
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 10px;
        color: #e2e8f0;
        padding: 0.75rem 1rem;
        width: 100%;
        transition: all 0.3s ease;
    }

    .form-select-dark:focus {
        background: rgba(15, 23, 42, 0.95);
        border-color: var(--neon-cyan);
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
        outline: none;
    }

    .form-select-dark option {
        background: #1e293b;
        color: #e2e8f0;
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

    /* Radio Group - 2 columns */
    .radio-group {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }

    .radio-option {
        position: relative;
    }

    .radio-option input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    .radio-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
        background: rgba(15, 23, 42, 0.8);
        border: 2px solid rgba(0, 212, 255, 0.3);
        border-radius: 15px;
        cursor: pointer;
        transition: all 0.3s ease;
        color: #e2e8f0;
        text-align: center;
        min-height: 180px;
    }

    .radio-label i {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: var(--neon-cyan);
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.3);
    }

    .radio-label span {
        font-weight: 600;
        font-size: 1.25rem;
        margin-bottom: 0.5rem;
    }

    .radio-description {
        color: #94a3b8;
        font-size: 0.875rem;
        margin-top: 0.5rem;
    }

    .radio-option input[type="radio"]:checked + .radio-label {
        background: linear-gradient(135deg, rgba(14, 165, 233, 0.3) 0%, rgba(0, 212, 255, 0.2) 100%);
        border-color: var(--neon-cyan);
        box-shadow: 0 0 25px rgba(0, 212, 255, 0.4);
    }

    .radio-option input[type="radio"]:checked + .radio-label i {
        text-shadow: 0 0 20px rgba(0, 212, 255, 0.8);
        color: var(--neon-cyan);
        transform: scale(1.1);
    }

    .radio-label:hover {
        background: rgba(0, 212, 255, 0.08);
        transform: translateY(-5px);
        border-color: rgba(0, 212, 255, 0.5);
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
        cursor: pointer;
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
        text-decoration: none;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .header-title {
            font-size: 1.5rem;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn-neon-lg,
        .btn-outline-neon {
            width: 100%;
        }

        .radio-group {
            grid-template-columns: 1fr;
        }

        .page-header-dark {
            padding: 1.5rem;
        }

        .card-body-dark {
            padding: 1rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Validación del formulario
    document.getElementById('createSectionForm').addEventListener('submit', function(e) {
        const codigo = document.querySelector('input[name="codigo_seccion"]').value.trim();
        const cupo = document.querySelector('input[name="cupo_maximo"]').value;
        const curso = document.querySelector('select[name="curso_id"]').value;
        const profesor = document.querySelector('select[name="profesor_id"]').value;
        const periodo = document.querySelector('select[name="periodo_id"]').value;
        const modalidad = document.querySelector('input[name="modalidad"]:checked');
        
        if (!codigo || !cupo || !curso || !profesor || !periodo || !modalidad) {
            e.preventDefault();
            alert('Por favor completa todos los campos obligatorios, incluyendo la modalidad');
            return false;
        }

        // Validar longitud del código
        if (codigo.length > 50) {
            e.preventDefault();
            alert('El código de sección no puede exceder los 50 caracteres');
            return false;
        }
    });

    // Debug: Mostrar modalidad seleccionada
    document.querySelectorAll('input[name="modalidad"]').forEach(radio => {
        radio.addEventListener('change', function() {
            console.log('Modalidad seleccionada:', this.value);
        });
    });
</script>
@endpush
@endsection