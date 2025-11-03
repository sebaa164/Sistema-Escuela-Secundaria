@extends('layouts.app')

@section('title', 'Crear Período Académico')
@section('page-title', 'Nuevo Período Académico')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Breadcrumb -->

            <!-- Card Principal -->
            <div class="card-dark">
                <div class="card-header-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-plus me-2"></i>Crear Nuevo Período Académico
                    </h5>
                </div>

                <div class="card-body-dark">
                    <form action="{{ route('admin.periodos.store') }}" method="POST" id="createPeriodForm">
                        @csrf

                        <div class="row g-4">
                            <!-- Información Básica -->
                            <div class="col-12">
                                <div class="section-header">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <h6>Información del Período</h6>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label-dark">
                                    <i class="fas fa-tag me-2"></i>Nombre del Período *
                                </label>
                                <input type="text" 
                                       name="nombre" 
                                       class="form-control-dark @error('nombre') is-invalid @enderror" 
                                       value="{{ old('nombre') }}"
                                       placeholder="Ej: Ciclo Lectivo 2025, Primer Cuatrimestre 2025"
                                       required>
                                <small class="form-text-dark">Nombre identificativo del período académico</small>
                                @error('nombre')
                                    <div class="invalid-feedback-dark">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Fechas -->
                            <div class="col-12">
                                <div class="section-header mt-3">
                                    <i class="fas fa-calendar me-2"></i>
                                    <h6>Fechas del Período</h6>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-dark">
                                    <i class="fas fa-play me-2"></i>Fecha de Inicio *
                                </label>
                                <input type="date" 
                                       name="fecha_inicio" 
                                       class="form-control-dark @error('fecha_inicio') is-invalid @enderror" 
                                       value="{{ old('fecha_inicio') }}"
                                       required>
                                @error('fecha_inicio')
                                    <div class="invalid-feedback-dark">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-dark">
                                    <i class="fas fa-stop me-2"></i>Fecha de Finalización *
                                </label>
                                <input type="date" 
                                       name="fecha_fin" 
                                       class="form-control-dark @error('fecha_fin') is-invalid @enderror" 
                                       value="{{ old('fecha_fin') }}"
                                       required>
                                @error('fecha_fin')
                                    <div class="invalid-feedback-dark">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Información Adicional -->
                            <div class="col-12">
                                <div class="info-box mt-4">
                                    <h6 class="mb-3">
                                        <i class="fas fa-lightbulb me-2"></i>Información Importante
                                    </h6>
                                    <ul class="info-list">
                                        <li>
                                            <i class="fas fa-check me-2"></i>
                                            El período se creará con estado <strong>Activo</strong> por defecto
                                        </li>
                                        <li>
                                            <i class="fas fa-check me-2"></i>
                                            La fecha de fin debe ser posterior a la fecha de inicio
                                        </li>
                                        <li>
                                            <i class="fas fa-check me-2"></i>
                                            No puede solaparse con otros períodos activos
                                        </li>
                                        <li>
                                            <i class="fas fa-check me-2"></i>
                                            Podrás cambiar el estado del período más adelante
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Preview de Duración -->
                            <div class="col-12">
                                <div class="duration-preview" id="durationPreview" style="display: none;">
                                    <div class="duration-icon">
                                        <i class="fas fa-hourglass-half"></i>
                                    </div>
                                    <div class="duration-content">
                                        <h6>Duración del Período</h6>
                                        <p id="durationText">Selecciona ambas fechas para ver la duración</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="form-actions mt-5">
                            <button type="submit" class="btn-neon-lg">
                                <i class="fas fa-save me-2"></i>Crear Período
                            </button>
                            <a href="{{ route('admin.periodos.index') }}" class="btn-outline-neon">
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

    /* Info Box */
    .info-box {
        background: rgba(0, 212, 255, 0.05);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
        padding: 1.5rem;
    }

    .info-box h6 {
        color: var(--neon-cyan);
        font-weight: 600;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.3);
    }

    .info-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .info-list li {
        color: #94a3b8;
        padding: 0.5rem 0;
        display: flex;
        align-items: flex-start;
    }

    .info-list li i {
        color: #10b981;
        margin-top: 0.25rem;
        flex-shrink: 0;
    }

    .info-list li strong {
        color: #e2e8f0;
    }

    /* Duration Preview */
    .duration-preview {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(6, 182, 212, 0.1) 100%);
        border: 2px solid rgba(16, 185, 129, 0.3);
        border-radius: 15px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.5rem;
        animation: fadeIn 0.5s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .duration-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        background: linear-gradient(135deg, #10b981 0%, #06b6d4 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        flex-shrink: 0;
    }

    .duration-content h6 {
        color: var(--neon-cyan);
        font-weight: 600;
        margin: 0 0 0.5rem 0;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.3);
    }

    .duration-content p {
        color: #e2e8f0;
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
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

        .duration-preview {
            flex-direction: column;
            text-align: center;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Validación del formulario
    document.getElementById('createPeriodForm').addEventListener('submit', function(e) {
        const nombre = document.querySelector('input[name="nombre"]').value.trim();
        const fechaInicio = document.querySelector('input[name="fecha_inicio"]').value;
        const fechaFin = document.querySelector('input[name="fecha_fin"]').value;
        
        if (!nombre || !fechaInicio || !fechaFin) {
            e.preventDefault();
            alert('Por favor completa todos los campos obligatorios');
            return false;
        }

        // Validar que fecha fin sea posterior a fecha inicio
        if (new Date(fechaFin) <= new Date(fechaInicio)) {
            e.preventDefault();
            alert('La fecha de finalización debe ser posterior a la fecha de inicio');
            return false;
        }
    });

    // Calcular y mostrar duración del período
    function calcularDuracion() {
        const fechaInicio = document.querySelector('input[name="fecha_inicio"]').value;
        const fechaFin = document.querySelector('input[name="fecha_fin"]').value;
        const preview = document.getElementById('durationPreview');
        const durationText = document.getElementById('durationText');

        if (fechaInicio && fechaFin) {
            const inicio = new Date(fechaInicio);
            const fin = new Date(fechaFin);
            const diffTime = Math.abs(fin - inicio);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if (diffDays > 0) {
                const meses = Math.floor(diffDays / 30);
                const dias = diffDays % 30;
                let texto = `${diffDays} días`;
                
                if (meses > 0) {
                    texto += ` (aprox. ${meses} ${meses === 1 ? 'mes' : 'meses'}`;
                    if (dias > 0) {
                        texto += ` y ${dias} ${dias === 1 ? 'día' : 'días'}`;
                    }
                    texto += ')';
                }

                durationText.textContent = texto;
                preview.style.display = 'flex';
            } else {
                preview.style.display = 'none';
            }
        } else {
            preview.style.display = 'none';
        }
    }

    // Event listeners para las fechas
    document.querySelector('input[name="fecha_inicio"]').addEventListener('change', calcularDuracion);
    document.querySelector('input[name="fecha_fin"]').addEventListener('change', calcularDuracion);
</script>
@endpush
@endsection