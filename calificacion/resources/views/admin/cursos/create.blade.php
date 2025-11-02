@extends('layouts.app')

@section('title', 'Crear Curso')
@section('page-title', 'Nuevo Curso')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Breadcrumb -->

            <!-- Card Principal -->
            <div class="card-dark">
                <div class="card-header-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-book-medical me-2"></i>Crear Nuevo Curso
                    </h5>
                </div>

                <div class="card-body-dark">
                    <form action="{{ route('admin.cursos.store') }}" method="POST" id="createCourseForm">
                        @csrf

                        <div class="row g-4">
                            <!-- Información Básica -->
                            <div class="col-12">
                                <div class="section-header">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <h6>Información Básica</h6>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label-dark">
                                    <i class="fas fa-barcode me-2"></i>Código del Curso *
                                </label>
                                <input type="text" 
                                       name="codigo_curso" 
                                       class="form-control-dark @error('codigo_curso') is-invalid @enderror" 
                                       value="{{ old('codigo_curso') }}"
                                       placeholder="Ej: MAT101"
                                       required>
                                @error('codigo_curso')
                                    <div class="invalid-feedback-dark">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-8">
                                <label class="form-label-dark">
                                    <i class="fas fa-book me-2"></i>Nombre del Curso *
                                </label>
                                <input type="text" 
                                       name="nombre" 
                                       class="form-control-dark @error('nombre') is-invalid @enderror" 
                                       value="{{ old('nombre') }}"
                                       placeholder="Ej: Matemáticas Básicas"
                                       maxlength="255"
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
                                          rows="4"
                                          placeholder="Descripción detallada del curso...">{{ old('descripcion') }}</textarea>
                                @error('descripcion')
                                    <div class="invalid-feedback-dark">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Detalles Académicos -->
                            <div class="col-12">
                                <div class="section-header mt-3">
                                    <i class="fas fa-graduation-cap me-2"></i>
                                    <h6>Detalles Académicos</h6>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-dark">
                                    <i class="fas fa-university me-2"></i>Carrera *
                                </label>
                                <input type="text" 
                                       name="carrera" 
                                       class="form-control-dark @error('carrera') is-invalid @enderror" 
                                       value="{{ old('carrera') }}"
                                       placeholder="Ej: Ingeniería en Sistemas"
                                       required>
                                @error('carrera')
                                    <div class="invalid-feedback-dark">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-dark">
                                    <i class="fas fa-layer-group me-2"></i>Nivel
                                </label>
                                <input type="text" 
                                       name="nivel" 
                                       class="form-control-dark @error('nivel') is-invalid @enderror" 
                                       value="{{ old('nivel') }}"
                                       placeholder="Ej: Primer Año, Ciclo I">
                                @error('nivel')
                                    <div class="invalid-feedback-dark">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label-dark">
                                    <i class="fas fa-clock me-2"></i>Horas Semanales *
                                </label>
                                <input type="number" 
                                       name="horas_semanales" 
                                       class="form-control-dark @error('horas_semanales') is-invalid @enderror" 
                                       value="{{ old('horas_semanales', 4) }}"
                                       min="1"
                                       max="40"
                                       required>
                                @error('horas_semanales')
                                    <div class="invalid-feedback-dark">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label-dark">
                                    <i class="fas fa-list-ul me-2"></i>Requisitos
                                </label>
                                <textarea name="requisitos" 
                                          class="form-control-dark @error('requisitos') is-invalid @enderror" 
                                          rows="3"
                                          placeholder="Ej: Haber aprobado Matemáticas I, Física básica...">{{ old('requisitos') }}</textarea>
                                <small class="form-text-dark">Cursos o conocimientos previos necesarios</small>
                                @error('requisitos')
                                    <div class="invalid-feedback-dark">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="form-actions mt-5">
                            <button type="submit" class="btn-neon-lg">
                                <i class="fas fa-save me-2"></i>Crear Curso
                            </button>
                            <a href="{{ route('admin.cursos.index') }}" class="btn-outline-neon">
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
    // Validación del formulario
    document.getElementById('createCourseForm').addEventListener('submit', function(e) {
        const codigo = document.querySelector('input[name="codigo_curso"]').value.trim();
        const nombre = document.querySelector('input[name="nombre"]').value.trim();
        const carrera = document.querySelector('input[name="carrera"]').value.trim();
        
        if (!codigo || !nombre || !carrera) {
            e.preventDefault();
            alert('Por favor completa los campos obligatorios');
            return false;
        }
    });
</script>
@endpush
@endsection