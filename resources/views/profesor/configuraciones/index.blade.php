@extends('layouts.app')

@section('title', 'Configuraciones - Profesor')
@section('page-title', 'Configuraciones')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header-dark mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-cog me-3"></i>Configuraciones del Sistema
                </h1>
                <p class="header-subtitle mb-0">Personaliza tu experiencia en el sistema</p>
            </div>
            <a href="{{ route('profesor.dashboard') }}" class="btn-outline-neon">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Configuraciones de Notificaciones -->
        <div class="col-lg-6">
            <div class="card-dark">
                <div class="card-header-dark">
                    <h5><i class="fas fa-bell me-2"></i>Notificaciones</h5>
                </div>
                <div class="card-body-dark">
                    <form action="{{ route('profesor.configuraciones.notificaciones') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="notif_evaluaciones"
                                       name="notif_evaluaciones" {{ old('notif_evaluaciones', auth()->user()->profesor->notif_evaluaciones ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="notif_evaluaciones">
                                    <strong>Notificaciones de Evaluaciones</strong>
                                    <br><small class="text-muted">Recibir alertas sobre evaluaciones próximas y recordatorios</small>
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="notif_calificaciones"
                                       name="notif_calificaciones" {{ old('notif_calificaciones', auth()->user()->profesor->notif_calificaciones ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="notif_calificaciones">
                                    <strong>Notificaciones de Calificaciones</strong>
                                    <br><small class="text-muted">Recibir alertas sobre calificaciones pendientes por ingresar</small>
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="notif_asistencias"
                                       name="notif_asistencias" {{ old('notif_asistencias', auth()->user()->profesor->notif_asistencias ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="notif_asistencias">
                                    <strong>Notificaciones de Asistencias</strong>
                                    <br><small class="text-muted">Recordatorios para tomar asistencia en tus secciones</small>
                                </label>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn-neon">
                                <i class="fas fa-save me-2"></i>Guardar Preferencias
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Configuraciones de Visualización -->
        <div class="col-lg-6">
            <div class="card-dark">
                <div class="card-header-dark">
                    <h5><i class="fas fa-palette me-2"></i>Visualización</h5>
                </div>
                <div class="card-body-dark">
                    <form action="{{ route('profesor.configuraciones.visualizacion') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label-neon">Tema de la Interfaz</label>
                            <select name="tema" class="form-control-neon">
                                <option value="dark" {{ old('tema', auth()->user()->profesor->tema ?? 'dark') == 'dark' ? 'selected' : '' }}>Tema Oscuro (Predeterminado)</option>
                                <option value="light" {{ old('tema', auth()->user()->profesor->tema ?? 'dark') == 'light' ? 'selected' : '' }}>Tema Claro</option>
                            </select>
                            <small class="text-muted-neon">El tema oscuro está optimizado para el sistema educativo</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label-neon">Elementos por Página</label>
                            <select name="elementos_pagina" class="form-control-neon">
                                <option value="10" {{ old('elementos_pagina', auth()->user()->profesor->elementos_pagina ?? 10) == 10 ? 'selected' : '' }}>10 elementos</option>
                                <option value="25" {{ old('elementos_pagina', auth()->user()->profesor->elementos_pagina ?? 10) == 25 ? 'selected' : '' }}>25 elementos</option>
                                <option value="50" {{ old('elementos_pagina', auth()->user()->profesor->elementos_pagina ?? 10) == 50 ? 'selected' : '' }}>50 elementos</option>
                            </select>
                            <small class="text-muted-neon">Número de elementos a mostrar en las tablas</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="mostrar_estadisticas"
                                       name="mostrar_estadisticas" {{ old('mostrar_estadisticas', auth()->user()->profesor->mostrar_estadisticas ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="mostrar_estadisticas">
                                    <strong>Mostrar Estadísticas</strong>
                                    <br><small class="text-muted">Mostrar gráficos y estadísticas en el dashboard</small>
                                </label>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn-neon">
                                <i class="fas fa-save me-2"></i>Guardar Configuración
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Configuraciones de Seguridad -->
        <div class="col-lg-6">
            <div class="card-dark">
                <div class="card-header-dark">
                    <h5><i class="fas fa-shield-alt me-2"></i>Seguridad</h5>
                </div>
                <div class="card-body-dark">
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="autenticacion_doble"
                                   name="autenticacion_doble" disabled>
                            <label class="form-check-label" for="autenticacion_doble">
                                <strong>Autenticación de Dos Factores</strong>
                                <br><small class="text-muted">Próximamente disponible</small>
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label-neon">Último Cambio de Contraseña</label>
                        <input type="text" class="form-control-neon"
                               value="{{ auth()->user()->updated_at->format('d/m/Y H:i') }}" disabled>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Recomendación de Seguridad:</strong> Cambia tu contraseña regularmente y usa una combinación segura.
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('profesor.perfil.index') }}" class="btn-outline-neon">
                            <i class="fas fa-key me-2"></i>Cambiar Contraseña
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del Sistema -->
        <div class="col-lg-6">
            <div class="card-dark">
                <div class="card-header-dark">
                    <h5><i class="fas fa-info-circle me-2"></i>Información del Sistema</h5>
                </div>
                <div class="card-body-dark">
                    <div class="info-item">
                        <strong>Versión del Sistema:</strong>
                        <span>1.0.0</span>
                    </div>

                    <div class="info-item">
                        <strong>Última Actualización:</strong>
                        <span>{{ \Carbon\Carbon::now()->format('d/m/Y') }}</span>
                    </div>

                    <div class="info-item">
                        <strong>Usuario:</strong>
                        <span>{{ auth()->user()->email }}</span>
                    </div>

                    <div class="info-item">
                        <strong>Rol:</strong>
                        <span>Profesor</span>
                    </div>

                    <hr>

                    <div class="text-center">
                        <p class="text-muted-neon mb-3">¿Necesitas ayuda con el sistema?</p>
                        <a href="#" class="btn-outline-neon">
                            <i class="fas fa-question-circle me-2"></i>Centro de Ayuda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    :root {
        --neon-cyan: #00ffff;
        --neon-blue: #00d4ff;
    }

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

    .header-subtitle { color: #cbd5e1; font-weight: 500; }

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
        margin: 0;
    }

    .card-body-dark { padding: 1.5rem; }

    .form-label-neon {
        color: var(--neon-cyan);
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-control-neon {
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(0, 212, 255, 0.3);
        color: #f1f5f9;
        padding: 0.75rem 1rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        width: 100%;
    }

    .form-control-neon:focus {
        background: rgba(15, 23, 42, 0.7);
        border-color: var(--neon-cyan);
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
        color: #f1f5f9;
        outline: none;
    }

    .form-control-neon:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .form-check-input {
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(0, 212, 255, 0.3);
    }

    .form-check-input:checked {
        background-color: var(--neon-cyan);
        border-color: var(--neon-cyan);
    }

    .form-check-input:focus {
        border-color: var(--neon-cyan);
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
    }

    .form-check-label {
        color: #f1f5f9;
        cursor: pointer;
    }

    .text-muted-neon {
        color: #94a3b8;
        font-size: 0.875rem;
        display: block;
        margin-top: 0.25rem;
    }

    .btn-neon {
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        color: #0f172a;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: all 0.3s ease;
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
    }

    .btn-neon:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 30px rgba(0, 212, 255, 0.5);
        color: #0f172a;
    }

    .btn-outline-neon {
        background: transparent;
        border: 2px solid var(--neon-cyan);
        color: var(--neon-cyan);
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: all 0.3s ease;
    }

    .btn-outline-neon:hover {
        background: rgba(0, 212, 255, 0.1);
        transform: translateY(-2px);
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
        color: var(--neon-cyan);
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        background: rgba(0, 212, 255, 0.05);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 8px;
    }

    .info-item strong {
        color: var(--neon-cyan);
    }

    .info-item span {
        color: #f1f5f9;
    }

    .alert {
        background: rgba(59, 130, 246, 0.1);
        border: 1px solid #3b82f6;
        color: #93c5fd;
        border-radius: 10px;
    }

    .alert-info {
        background: rgba(6, 182, 212, 0.1);
        border: 1px solid #06b6d4;
        color: #67e8f9;
    }

    .alert-success {
        background: rgba(16, 185, 129, 0.2);
        border: 1px solid #10b981;
        color: #10b981;
        border-radius: 10px;
    }
</style>
@endpush
@endsection
