@extends('layouts.app')

@section('title', 'Crear Inscripción')
@section('page-title', 'Nueva Inscripción')

@section('content')
<div class="container-fluid px-3 py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-9">

            <div class="card-dark">
                <div class="card-header-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-user-plus me-2"></i>Crear Nueva Inscripción
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

                    <form action="{{ route('admin.inscripciones.store') }}" method="POST" id="createForm">
                        @csrf

                        <div class="row g-4">
                            <!-- Sección: Datos del Estudiante -->
                            <div class="col-12">
                                <div class="section-header">
                                    <i class="fas fa-user me-2"></i>
                                    <h6>Datos del Estudiante</h6>
                                </div>
                            </div>

                            <div class="col-12">
                                <label for="estudiante_id" class="form-label-dark">
                                    <i class="fas fa-user-graduate me-2"></i>Estudiante *
                                </label>
                                <select name="estudiante_id" id="estudiante_id" 
                                        class="form-select-dark @error('estudiante_id') is-invalid @enderror" required>
                                    <option value="">Seleccione un estudiante</option>
                                    @foreach($estudiantes as $estudiante)
                                        <option value="{{ $estudiante->id }}" 
                                                {{ old('estudiante_id') == $estudiante->id ? 'selected' : '' }}>
                                            {{ $estudiante->nombre_completo }} ({{ $estudiante->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('estudiante_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Sección: Datos del Curso/Sección -->
                            <div class="col-12 mt-4">
                                <div class="section-header">
                                    <i class="fas fa-book me-2"></i>
                                    <h6>Datos del Curso/Sección</h6>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="seccion_id" class="form-label-dark">
                                    <i class="fas fa-layer-group me-2"></i>Sección *
                                </label>
                                <select name="seccion_id" id="seccion_id" 
                                        class="form-select-dark @error('seccion_id') is-invalid @enderror" required>
                                    <option value="">Asignar una Materia</option>
                                    @foreach($secciones as $seccion)
                                        <option value="{{ $seccion->id }}"
                                                data-cupo-maximo="{{ $seccion->cupo_maximo }}"
                                                data-cupo-actual="{{ $seccion->inscripciones()->where('estado', 'inscrito')->count() }}"
                                                {{ old('seccion_id') == $seccion->id ? 'selected' : '' }}>
                                            {{ $seccion->curso->nombre }} - {{ $seccion->codigo_seccion }} ({{ $seccion->periodo->nombre }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('seccion_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-12 col-md-6">
                                <div id="cupo-info" class="cupo-info-box">
                                    <p class="mb-2">
                                        <i class="fas fa-users me-2"></i>
                                        <span>Cupos Disponibles: </span>
                                        <strong id="cupo-disponible" class="text-warning"></strong>
                                    </p>
                                    <div class="progress">
                                        <div id="cupo-bar" class="cupo-bar" role="progressbar"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones de acción -->
                            <div class="col-12 mt-5">
                                <div class="form-actions">
                                    <a href="{{ route('admin.inscripciones.index') }}" class="btn-outline-neon">
                                        <i class="fas fa-arrow-left me-2"></i>Cancelar
                                    </a>
                                    <button type="submit" class="btn-neon-lg">
                                        <i class="fas fa-save me-2"></i>Registrar Inscripción
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
    
    /* Contenedor principal con mejor spacing */
    .container-fluid {
        max-width: 100%;
        overflow-x: hidden;
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
        padding: 1.5rem;
        color: var(--text-color);
    }

    .card-header-dark h5 {
        color: var(--neon-cyan);
        margin: 0;
    }

    .card-body-dark {
        padding: 2rem;
        color: var(--text-color);
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
    }

    .alert-danger-dark {
        background-color: rgba(239, 68, 68, 0.2);
        border: 1px solid #ef4444;
        color: #ef4444;
        padding: 1rem;
        border-radius: 8px;
    }
    
    .cupo-info-box {
        display: none;
        padding: 1rem;
        border-radius: 8px;
        border: 1px solid var(--neon-cyan);
        background-color: rgba(0, 212, 255, 0.05);
        height: fit-content;
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
        width: 0%;
    }

    .cupo-bar.warning {
        background: linear-gradient(90deg, #f59e0b 0%, #f97316 100%);
    }

    .cupo-bar.full {
        background: linear-gradient(90deg, #ef4444 0%, #dc2626 100%);
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(0, 212, 255, 0.1);
    }

    .invalid-feedback {
        color: var(--danger-color);
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: block;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .card-body-dark {
            padding: 1.5rem 1rem;
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
    document.getElementById('seccion_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const cupoMaximo = parseInt(selectedOption.getAttribute('data-cupo-maximo'));
        const cupoActual = parseInt(selectedOption.getAttribute('data-cupo-actual'));
        const cupoInfo = document.getElementById('cupo-info');
        const cupoDisponibleEl = document.getElementById('cupo-disponible');
        const cupoBar = document.getElementById('cupo-bar');
        
        if (cupoMaximo > 0 && selectedOption.value) {
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

            cupoBar.style.width = `${Math.min(porcentaje, 100)}%`;
            
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

    document.getElementById('createForm').addEventListener('submit', function(e) {
        const estudianteId = document.getElementById('estudiante_id').value;
        const seccionId = document.getElementById('seccion_id').value;
        
        if (!estudianteId || !seccionId) {
            e.preventDefault();
            alert('Por favor selecciona un estudiante y una sección');
            return false;
        }

        const selectedOption = document.getElementById('seccion_id').options[document.getElementById('seccion_id').selectedIndex];
        const cupoActual = parseInt(selectedOption.getAttribute('data-cupo-actual'));
        const cupoMaximo = parseInt(selectedOption.getAttribute('data-cupo-maximo'));
        
        if (cupoActual >= cupoMaximo) {
            e.preventDefault();
            alert('La sección seleccionada no tiene cupos disponibles');
            return false;
        }
    });
</script>
@endpush
@endsection