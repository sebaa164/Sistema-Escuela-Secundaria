@extends('layouts.app')

@section('title', 'Configuración del Sistema')
@section('page-title', 'Configuración del Sistema')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->

    <!-- Mensajes de éxito o error -->
    @if(session('success'))
        <div class="alert-success-neon mb-4">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert-danger-neon mb-4">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert-danger-neon mb-4">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Se encontraron los siguientes errores:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Header Info -->
    <div class="system-header mb-4">
        <div class="system-icon">
            <i class="fas fa-cogs"></i>
        </div>
        <div class="system-info">
            <h2>Configuración del Sistema</h2>
            <p>Administra los parámetros principales que controlan el comportamiento del sistema</p>
        </div>
    </div>

    <div class="row">
        <!-- Formulario de Configuración -->
        <div class="col-lg-8">
            <div class="card-dark">
                <div class="card-header-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-sliders-h me-2"></i>Parámetros del Sistema
                    </h5>
                </div>

                <div class="card-body-dark">
                    <form action="{{ route('admin.configuraciones.sistema.actualizar') }}" 
                          method="POST" 
                          id="systemConfigForm">
                        @csrf
                        @method('PUT')

                        <!-- Configuraciones Académicas -->
                        <div class="config-section">
                            <div class="section-title">
                                <i class="fas fa-graduation-cap me-2"></i>
                                <h6>Configuraciones Académicas</h6>
                            </div>

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label-dark">
                                        <i class="fas fa-check-circle me-2"></i>Nota Mínima de Aprobación *
                                    </label>
                                    <div class="input-with-suffix">
                                        <input type="number" 
                                               name="nota_minima_aprobacion" 
                                               class="form-control-dark @error('nota_minima_aprobacion') is-invalid @enderror" 
                                               value="{{ old('nota_minima_aprobacion', $configuracionesComunes['nota_minima_aprobacion']) }}"
                                               min="0"
                                               max="100"
                                               step="0.01"
                                               required>
                                        <span class="input-suffix">pts</span>
                                    </div>
                                    <small class="form-text-dark">
                                        Nota mínima que un estudiante debe obtener para aprobar una materia
                                    </small>
                                    @error('nota_minima_aprobacion')
                                        <div class="invalid-feedback-dark">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label-dark">
                                        <i class="fas fa-users me-2"></i>Máximo de Estudiantes por Sección *
                                    </label>
                                    <div class="input-with-suffix">
                                        <input type="number" 
                                               name="max_estudiantes_seccion" 
                                               class="form-control-dark @error('max_estudiantes_seccion') is-invalid @enderror" 
                                               value="{{ old('max_estudiantes_seccion', $configuracionesComunes['max_estudiantes_seccion']) }}"
                                               min="1"
                                               max="100"
                                               required>
                                        <span class="input-suffix">estudiantes</span>
                                    </div>
                                    <small class="form-text-dark">
                                        Capacidad máxima de estudiantes permitidos en una sección
                                    </small>
                                    @error('max_estudiantes_seccion')
                                        <div class="invalid-feedback-dark">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Configuraciones Generales -->
                        <div class="config-section">
                            <div class="section-title">
                                <i class="fas fa-cog me-2"></i>
                                <h6>Configuraciones Generales</h6>
                            </div>

                            <div class="row g-4">
                                <div class="col-12">
                                    <label class="form-label-dark">
                                        <i class="fas fa-tag me-2"></i>Nombre del Sistema *
                                    </label>
                                    <input type="text" 
                                           name="sistema_nombre" 
                                           class="form-control-dark @error('sistema_nombre') is-invalid @enderror" 
                                           value="{{ old('sistema_nombre', $configuracionesComunes['sistema_nombre']) }}"
                                           maxlength="255"
                                           required>
                                    <small class="form-text-dark">
                                        Nombre que aparecerá en el título y encabezados del sistema
                                    </small>
                                    @error('sistema_nombre')
                                        <div class="invalid-feedback-dark">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label-dark">
                                        <i class="fas fa-globe me-2"></i>Zona Horaria *
                                    </label>
                                    <select name="timezone" 
                                            class="form-select-dark @error('timezone') is-invalid @enderror" 
                                            required>
                                        <option value="">Seleccionar zona horaria...</option>
                                        @php
                                            $timezones = [
                                                'America/Argentina/San_Juan' => 'San Juan, Argentina (GMT-3)',
                                                'America/Argentina/Buenos_Aires' => 'Buenos Aires (GMT-3)',
                                                'America/Argentina/Cordoba' => 'Córdoba (GMT-3)',
                                                'America/Argentina/Mendoza' => 'Mendoza (GMT-3)',
                                                'America/Santiago' => 'Santiago (GMT-4)',
                                                'America/Lima' => 'Lima (GMT-5)',
                                                'America/Bogota' => 'Bogotá (GMT-5)',
                                                'America/Mexico_City' => 'Ciudad de México (GMT-6)',
                                                'America/New_York' => 'Nueva York (GMT-5)',
                                                'America/Los_Angeles' => 'Los Ángeles (GMT-8)',
                                                'Europe/Madrid' => 'Madrid (GMT+1)',
                                                'UTC' => 'UTC (GMT+0)',
                                            ];
                                        @endphp
                                        @foreach($timezones as $value => $label)
                                            <option value="{{ $value }}" {{ old('timezone', $configuracionesComunes['timezone']) == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text-dark">
                                        Zona horaria para fechas y horas del sistema
                                    </small>
                                    @error('timezone')
                                        <div class="invalid-feedback-dark">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label-dark">
                                        <i class="fas fa-calendar me-2"></i>Formato de Fecha
                                    </label>
                                    <select name="formato_fecha" 
                                            class="form-select-dark">
                                        @php
                                            $formatos = [
                                                'Y-m-d' => 'YYYY-MM-DD (2025-01-15)',
                                                'd/m/Y' => 'DD/MM/YYYY (15/01/2025)',
                                                'm/d/Y' => 'MM/DD/YYYY (01/15/2025)',
                                                'd-m-Y' => 'DD-MM-YYYY (15-01-2025)',
                                            ];
                                        @endphp
                                        @foreach($formatos as $value => $label)
                                            <option value="{{ $value }}" {{ old('formato_fecha', $configuracionesComunes['formato_fecha'] ?? 'Y-m-d') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text-dark">
                                        Formato para mostrar fechas en el sistema
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="form-actions mt-5">
                            <button type="submit" class="btn-neon-lg">
                                <i class="fas fa-save me-2"></i>Guardar Configuración
                            </button>
                            <a href="{{ route('admin.configuraciones.index') }}" class="btn-outline-neon">
                                <i class="fas fa-arrow-left me-2"></i>Volver
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Panel de Información -->
        <div class="col-lg-4">
            <!-- Valores Actuales -->
            <div class="card-dark mb-4">
                <div class="card-header-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Valores Actuales
                    </h5>
                </div>
                <div class="card-body-dark">
                    <div class="current-values">
                        <div class="value-item">
                            <div class="value-icon bg-gradient-success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="value-content">
                                <span class="value-label">Nota Aprobación</span>
                                <span class="value-number">{{ $configuracionesComunes['nota_minima_aprobacion'] }} pts</span>
                            </div>
                        </div>

                        <div class="value-item">
                            <div class="value-icon bg-gradient-info">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="value-content">
                                <span class="value-label">Max. Estudiantes</span>
                                <span class="value-number">{{ $configuracionesComunes['max_estudiantes_seccion'] }}</span>
                            </div>
                        </div>

                        <div class="value-item">
                            <div class="value-icon bg-gradient-primary">
                                <i class="fas fa-tag"></i>
                            </div>
                            <div class="value-content">
                                <span class="value-label">Sistema</span>
                                <span class="value-text">{{ $configuracionesComunes['sistema_nombre'] }}</span>
                            </div>
                        </div>

                        <div class="value-item">
                            <div class="value-icon bg-gradient-warning">
                                <i class="fas fa-globe"></i>
                            </div>
                            <div class="value-content">
                                <span class="value-label">Zona Horaria</span>
                                <span class="value-text">{{ $configuracionesComunes['timezone'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ayuda -->
            <div class="card-dark">
                <div class="card-header-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-question-circle me-2"></i>Ayuda
                    </h5>
                </div>
                <div class="card-body-dark">
                    <div class="help-content">
                        <div class="help-item">
                            <i class="fas fa-lightbulb text-warning"></i>
                            <div>
                                <strong>Nota Mínima:</strong>
                                <p>Define la calificación mínima que un estudiante debe obtener para aprobar. Se recomienda usar valores entre 60 y 70 puntos.</p>
                            </div>
                        </div>

                        <div class="help-item">
                            <i class="fas fa-lightbulb text-warning"></i>
                            <div>
                                <strong>Capacidad de Sección:</strong>
                                <p>Establece el límite de estudiantes por sección. Considera la capacidad de las aulas y recursos disponibles.</p>
                            </div>
                        </div>

                        <div class="help-item">
                            <i class="fas fa-exclamation-triangle text-danger"></i>
                            <div>
                                <strong>Importante:</strong>
                                <p>Los cambios se guardarán inmediatamente en la base de datos y afectarán todo el sistema.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Alerts */
    .alert-success-neon {
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid #10b981;
        border-radius: 12px;
        padding: 1rem 1.5rem;
        color: #10b981;
        font-weight: 500;
        box-shadow: 0 0 20px rgba(16, 185, 129, 0.2);
        animation: slideInDown 0.4s ease;
    }

    .alert-danger-neon {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid #ef4444;
        border-radius: 12px;
        padding: 1rem 1.5rem;
        color: #ef4444;
        font-weight: 500;
        box-shadow: 0 0 20px rgba(239, 68, 68, 0.2);
        animation: slideInDown 0.4s ease;
    }

    .alert-danger-neon ul {
        list-style-position: inside;
        padding-left: 1rem;
    }

    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .breadcrumb-dark {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(30, 41, 59, 0.9) 100%);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 10px;
        padding: 1rem 1.5rem;
        margin: 0;
    }

    .breadcrumb-dark .breadcrumb-item { color: #94a3b8; }
    .breadcrumb-dark .breadcrumb-item a {
        color: var(--neon-cyan);
        text-decoration: none;
        transition: all 0.3s ease;
    }
    .breadcrumb-dark .breadcrumb-item a:hover {
        color: var(--neon-blue);
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }
    .breadcrumb-dark .breadcrumb-item.active { color: #e2e8f0; }
    .breadcrumb-dark .breadcrumb-item + .breadcrumb-item::before {
        content: "›";
        color: rgba(0, 212, 255, 0.5);
    }

    /* System Header */
    .system-header {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.95) 100%);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 15px;
        padding: 2rem;
        display: flex;
        align-items: center;
        gap: 2rem;
        box-shadow: 0 0 30px rgba(0, 212, 255, 0.2);
    }

    .system-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        color: #0f172a;
        box-shadow: 0 0 30px rgba(0, 212, 255, 0.5);
    }

    .system-info h2 {
        color: var(--neon-cyan);
        font-weight: 700;
        margin: 0 0 0.5rem 0;
        text-shadow: 0 0 20px rgba(0, 212, 255, 0.5);
    }

    .system-info p {
        color: #94a3b8;
        margin: 0;
        font-size: 1rem;
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
        font-weight: 600;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .card-body-dark { padding: 2rem; }

    /* Config Section */
    .config-section {
        margin-bottom: 2.5rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid rgba(0, 212, 255, 0.1);
    }

    .config-section:last-of-type {
        border-bottom: none;
        margin-bottom: 0;
    }

    .section-title {
        display: flex;
        align-items: center;
        color: var(--neon-cyan);
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid rgba(0, 212, 255, 0.3);
    }

    .section-title i {
        font-size: 1.25rem;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .section-title h6 {
        margin: 0;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.875rem;
    }

    .form-label-dark {
        color: #e2e8f0;
        font-weight: 500;
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-label-dark i { color: var(--neon-cyan); }

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

    .form-control-dark.is-invalid, .form-select-dark.is-invalid {
        border-color: #ef4444;
    }

    .input-with-suffix {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-with-suffix input {
        padding-right: 100px;
    }

    .input-suffix {
        position: absolute;
        right: 15px;
        color: var(--neon-cyan);
        font-weight: 600;
        font-size: 0.9rem;
        pointer-events: none;
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

    /* Current Values */
    .current-values {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .value-item {
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 12px;
        padding: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.3s ease;
    }

    .value-item:hover {
        border-color: var(--neon-cyan);
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.2);
    }

    .value-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
        flex-shrink: 0;
    }

    .bg-gradient-primary { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); }
    .bg-gradient-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .bg-gradient-info { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }
    .bg-gradient-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }

    .value-content {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        flex: 1;
    }

    .value-label {
        color: #94a3b8;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .value-number {
        color: var(--neon-cyan);
        font-size: 1.5rem;
        font-weight: 700;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.3);
    }

    .value-text {
        color: #e2e8f0;
        font-weight: 600;
        font-size: 0.95rem;
    }

    /* Help Content */
    .help-content {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .help-item {
        display: flex;
        gap: 1rem;
        align-items: flex-start;
    }

    .help-item i {
        font-size: 1.5rem;
        flex-shrink: 0;
        margin-top: 0.25rem;
    }

    .help-item strong {
        color: var(--neon-cyan);
        display: block;
        margin-bottom: 0.25rem;
    }

    .help-item p {
        color: #94a3b8;
        margin: 0;
        font-size: 0.875rem;
        line-height: 1.5;
    }

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

    @media (max-width: 991px) {
        .system-header {
            flex-direction: column;
            text-align: center;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn-neon-lg, .btn-outline-neon {
            width: 100%;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.getElementById('systemConfigForm').addEventListener('submit', function(e) {
        const notaMinima = parseFloat(document.querySelector('input[name="nota_minima_aprobacion"]').value);
        const maxEstudiantes = parseInt(document.querySelector('input[name="max_estudiantes_seccion"]').value);
        
        if (notaMinima < 0 || notaMinima > 100) {
            e.preventDefault();
            alert('La nota mínima debe estar entre 0 y 100');
            return false;
        }

        if (maxEstudiantes < 1 || maxEstudiantes > 100) {
            e.preventDefault();
            alert('El máximo de estudiantes debe estar entre 1 y 100');
            return false;
        }

        console.log('Formulario enviado con valores:', {
            nota_minima_aprobacion: notaMinima,
            max_estudiantes_seccion: maxEstudiantes,
            timezone: document.querySelector('select[name="timezone"]').value
        });

        return true;
    });
</script>
@endpush
@endsection