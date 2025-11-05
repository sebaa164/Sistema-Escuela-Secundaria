@extends('layouts.app')

@section('title', 'Editar Período Académico')
@section('page-title', 'Editar Período')

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
                            <i class="fas fa-edit me-2"></i>Editar Período: {{ $periodo->nombre }}
                        </h5>
                        @if($periodo->es_vigente)
                            <span class="badge-vigente">
                                <i class="fas fa-circle pulse"></i> Vigente
                            </span>
                        @endif
                    </div>
                </div>

                <div class="card-body-dark">
                    <form action="{{ route('admin.periodos.update', $periodo) }}" method="POST" id="editPeriodForm">
                        @csrf
                        @method('PUT')

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
                                       value="{{ old('nombre', $periodo->nombre) }}"
                                       required>
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
                                       value="{{ old('fecha_inicio', $periodo->fecha_inicio->format('Y-m-d')) }}"
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
                                       value="{{ old('fecha_fin', $periodo->fecha_fin->format('Y-m-d')) }}"
                                       required>
                                @error('fecha_fin')
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
                                    <option value="Activo" {{ old('estado', $periodo->estado) == 'Activo' ? 'selected' : '' }}>Activo</option>
                                    <option value="Inactivo" {{ old('estado', $periodo->estado) == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                                    <option value="Finalizado" {{ old('estado', $periodo->estado) == 'Finalizado' ? 'selected' : '' }}>Finalizado</option>
                                </select>
                            </div>

                            <!-- Estadísticas del Período -->
                            <div class="col-12">
                                <div class="info-box mt-4">
                                    <h6 class="mb-3">
                                        <i class="fas fa-chart-bar me-2"></i>Estadísticas del Período
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <p class="info-item">
                                                <i class="fas fa-layer-group me-2"></i>
                                                <strong>Secciones:</strong> 
                                                {{ $periodo->secciones_count ?? 0 }}
                                            </p>
                                        </div>
                                        <div class="col-md-3">
                                            <p class="info-item">
                                                <i class="fas fa-calendar-day me-2"></i>
                                                <strong>Duración:</strong> 
                                                {{ $periodo->duracion_dias }} días
                                            </p>
                                        </div>
                                        <div class="col-md-3">
                                            <p class="info-item">
                                                <i class="fas fa-hourglass-half me-2"></i>
                                                <strong>Días Restantes:</strong> 
                                                {{ $periodo->dias_restantes }}
                                            </p>
                                        </div>
                                        <div class="col-md-3">
                                            <p class="info-item">
                                                <i class="fas fa-database me-2"></i>
                                                <strong>ID:</strong> 
                                                {{ $periodo->id }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <p class="info-item">
                                                <i class="fas fa-clock me-2"></i>
                                                <strong>Creado:</strong> 
                                                {{ $periodo->created_at->format('d/m/Y H:i') }}
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="info-item">
                                                <i class="fas fa-sync me-2"></i>
                                                <strong>Actualizado:</strong> 
                                                {{ $periodo->updated_at->format('d/m/Y H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Preview de Duración -->
                            <div class="col-12">
                                <div class="duration-preview" id="durationPreview" style="display: none;">
                                    <div class="duration-icon">
                                        <i class="fas fa-hourglass-half"></i>
                                    </div>
                                    <div class="duration-content">
                                        <h6>Nueva Duración del Período</h6>
                                        <p id="durationText">Calcula la duración...</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="form-actions mt-5">
                            <button type="submit" class="btn-neon-lg">
                                <i class="fas fa-save me-2"></i>Guardar Cambios
                            </button>
                            <a href="{{ route('admin.periodos.index') }}" class="btn-outline-neon">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            @if(!$periodo->secciones()->exists())
                                <button type="button" 
                                        class="btn-danger-neon"
                                        onclick="confirmarEliminacion()">
                                    <i class="fas fa-trash me-2"></i>Eliminar Período
                                </button>
                            @endif
                        </div>
                    </form>

                    <!-- Formulario de eliminación oculto -->
                    @if(!$periodo->secciones()->exists())
                        <form id="delete-form" 
                              action="{{ route('admin.periodos.destroy', $periodo) }}" 
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

    /* Badge Vigente */
    .badge-vigente {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 1px solid #10b981;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .badge-vigente .pulse {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
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
    document.getElementById('editPeriodForm').addEventListener('submit', function(e) {
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

    function confirmarEliminacion() {
        if (confirm('¿Estás seguro de que deseas eliminar este período académico? Esta acción no se puede deshacer.')) {
            document.getElementById('delete-form').submit();
        }
    }

    // Calcular y mostrar duración del período
    const fechaInicioOriginal = '{{ $periodo->fecha_inicio->format("Y-m-d") }}';
    const fechaFinOriginal = '{{ $periodo->fecha_fin->format("Y-m-d") }}';

    function calcularDuracion() {
        const fechaInicio = document.querySelector('input[name="fecha_inicio"]').value;
        const fechaFin = document.querySelector('input[name="fecha_fin"]').value;
        const preview = document.getElementById('durationPreview');
        const durationText = document.getElementById('durationText');

        // Solo mostrar si las fechas cambiaron
        if (fechaInicio !== fechaInicioOriginal || fechaFin !== fechaFinOriginal) {
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