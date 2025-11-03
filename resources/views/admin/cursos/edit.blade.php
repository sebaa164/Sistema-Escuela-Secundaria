@extends('layouts.app')

@section('title', 'Editar Curso')
@section('page-title', 'Editar Curso')

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
                            <i class="fas fa-edit me-2"></i>Editar Curso: {{ $curso->nombre }}
                        </h5>
                        <span class="badge-neon">{{ $curso->codigo_curso }}</span>
                    </div>
                </div>

                <div class="card-body-dark">
                    <form action="{{ route('admin.cursos.update', $curso) }}" method="POST" id="editCourseForm">
                        @csrf
                        @method('PUT')

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
                                       value="{{ old('codigo_curso', $curso->codigo_curso) }}"
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
                                       value="{{ old('nombre', $curso->nombre) }}"
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
                                          maxlength="1000">{{ old('descripcion', $curso->descripcion) }}</textarea>
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
                                       value="{{ old('carrera', $curso->carrera) }}"
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
                                       value="{{ old('nivel', $curso->nivel) }}">
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
                                       value="{{ old('horas_semanales', $curso->horas_semanales) }}"
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
                                          maxlength="500">{{ old('requisitos', $curso->requisitos) }}</textarea>
                                <small class="form-text-dark">Cursos o conocimientos previos necesarios</small>
                                @error('requisitos')
                                    <div class="invalid-feedback-dark">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Estado -->
                            <div class="col-12">
                                <div class="section-header mt-3">
                                    <i class="fas fa-cogs me-2"></i>
                                    <h6>Configuración</h6>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-dark">
                                    <i class="fas fa-toggle-on me-2"></i>Estado *
                                </label>
                                <select name="estado" class="form-select-dark" required>
                                    <option value="activo" {{ old('estado', $curso->estado) == 'activo' ? 'selected' : '' }}>Activo</option>
                                    <option value="inactivo" {{ old('estado', $curso->estado) == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                                </select>
                            </div>

                            <!-- Estadísticas del Curso -->
                            <div class="col-12">
                                <div class="info-box mt-4">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <p class="info-item">
                                                <i class="fas fa-list me-2"></i>
                                                <strong>Secciones:</strong> 
                                                {{ $curso->secciones_count ?? 0 }}
                                            </p>
                                        </div>
                                        <div class="col-md-4">
                                            <p class="info-item">
                                                <i class="fas fa-clock me-2"></i>
                                                <strong>Creado:</strong> 
                                                {{ $curso->created_at->format('d/m/Y') }}
                                            </p>
                                        </div>
                                        <div class="col-md-4">
                                            <p class="info-item">
                                                <i class="fas fa-sync me-2"></i>
                                                <strong>Actualizado:</strong> 
                                                {{ $curso->updated_at->format('d/m/Y') }}
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
                            <a href="{{ route('admin.cursos.index') }}" class="btn-outline-neon">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            @if(!$curso->secciones()->exists())
                                <button type="button" 
                                        class="btn-danger-neon"
                                        onclick="confirmarEliminacion()">
                                    <i class="fas fa-trash me-2"></i>Eliminar Curso
                                </button>
                            @endif
                        </div>
                    </form>

                    <!-- Formulario de eliminación oculto -->
                    @if(!$curso->secciones()->exists())
                        <form id="delete-form" 
                              action="{{ route('admin.cursos.destroy', $curso) }}" 
                              method="POST" 
                              style="display: none;">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
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
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
        outline: none;
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
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        color: #0f172a;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.875rem;
        box-shadow: 0 0 10px rgba(0, 212, 255, 0.3);
    }

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
        .btn-outline-neon,
        .btn-danger-neon {
            width: 100%;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Validación del formulario
    document.getElementById('editCourseForm').addEventListener('submit', function(e) {
        const codigo = document.querySelector('input[name="codigo_curso"]').value.trim();
        const nombre = document.querySelector('input[name="nombre"]').value.trim();
        const carrera = document.querySelector('input[name="carrera"]').value.trim();
        
        if (!codigo || !nombre || !carrera) {
            e.preventDefault();
            alert('Por favor completa los campos obligatorios');
            return false;
        }
    });

    function confirmarEliminacion() {
        if (confirm('¿Estás seguro de que deseas eliminar este curso? Esta acción no se puede deshacer.')) {
            document.getElementById('delete-form').submit();
        }
    }
</script>
@endpush
@endsection