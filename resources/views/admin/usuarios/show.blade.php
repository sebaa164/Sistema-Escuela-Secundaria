@extends('layouts.app')

@section('title', 'Detalles del Usuario')
@section('page-title', 'Detalles del Usuario')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header-dark mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-user me-3"></i>
                    Detalles del Usuario
                </h1>
                <p class="header-subtitle mb-0">{{ $usuario->nombre }} {{ $usuario->apellido }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="btn-neon">
                    <i class="fas fa-edit me-2"></i>Editar
                </a>
                <a href="{{ route('admin.usuarios.index') }}" class="btn-outline-neon">
                    <i class="fas fa-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Información Principal - Ahora ocupa todo el ancho -->
        <div class="col-lg-8">
            <div class="card-dark mb-4">
                <div class="card-header-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-user-circle me-2"></i>
                        Información Personal
                    </h5>
                </div>
                <div class="card-body-dark">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="info-label">
                                    <i class="fas fa-user me-2"></i>Nombre
                                </label>
                                <p class="info-value">{{ $usuario->nombre }}</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="info-label">
                                    <i class="fas fa-user me-2"></i>Apellido
                                </label>
                                <p class="info-value">{{ $usuario->apellido }}</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="info-label">
                                    <i class="fas fa-envelope me-2"></i>Correo Electrónico
                                </label>
                                <p class="info-value">{{ $usuario->email }}</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="info-label">
                                    <i class="fas fa-phone me-2"></i>Teléfono
                                </label>
                                <p class="info-value">{{ $usuario->telefono ?? 'No especificado' }}</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="info-label">
                                    <i class="fas fa-calendar me-2"></i>Fecha de Nacimiento
                                </label>
                                <p class="info-value">
                                    {{ $usuario->fecha_nacimiento ? \Carbon\Carbon::parse($usuario->fecha_nacimiento)->format('d/m/Y') : 'No especificado' }}
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="info-label">
                                    <i class="fas fa-user-tag me-2"></i>Tipo de Usuario
                                </label>
                                <p class="info-value">
                                    @if($usuario->tipo_usuario === 'estudiante')
                                        <span class="badge-success">
                                            <i class="fas fa-user-graduate me-1"></i>Estudiante
                                        </span>
                                    @elseif($usuario->tipo_usuario === 'profesor')
                                        <span class="badge-info">
                                            <i class="fas fa-chalkboard-teacher me-1"></i>Profesor
                                        </span>
                                    @else
                                        <span class="badge-warning">
                                            <i class="fas fa-user-shield me-1"></i>Administrador
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="info-label">
                                    <i class="fas fa-toggle-on me-2"></i>Estado
                                </label>
                                <p class="info-value">
                                    @if($usuario->estado === 'activo')
                                        <span class="badge-status status-active">
                                            <i class="fas fa-check-circle me-1"></i>Activo
                                        </span>
                                    @elseif($usuario->estado === 'inactivo')
                                        <span class="badge-status status-inactive">
                                            <i class="fas fa-pause-circle me-1"></i>Inactivo
                                        </span>
                                    @else
                                        <span class="badge-status status-suspended">
                                            <i class="fas fa-ban me-1"></i>Suspendido
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="info-item">
                                <label class="info-label">
                                    <i class="fas fa-map-marker-alt me-2"></i>Dirección
                                </label>
                                <p class="info-value">{{ $usuario->direccion ?? 'No especificada' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel Lateral - Solo Información del Sistema -->
        <div class="col-lg-4">
            <!-- Información del Sistema -->
            <div class="card-dark">
                <div class="card-header-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-database me-2"></i>
                        Información del Sistema
                    </h5>
                </div>
                <div class="card-body-dark">
                    <div class="info-item mb-3">
                        <label class="info-label">
                            <i class="fas fa-hashtag me-2"></i>ID
                        </label>
                        <p class="info-value">{{ $usuario->id }}</p>
                    </div>

                    <div class="info-item mb-3">
                        <label class="info-label">
                            <i class="fas fa-calendar-plus me-2"></i>Fecha de Registro
                        </label>
                        <p class="info-value">{{ $usuario->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    <div class="info-item">
                        <label class="info-label">
                            <i class="fas fa-sync me-2"></i>Última Actualización
                        </label>
                        <p class="info-value">{{ $usuario->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botones de Acción -->
    <div class="card-dark mt-4">
        <div class="card-body-dark">
            <div class="form-actions">
                <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="btn-neon-lg">
                    <i class="fas fa-edit me-2"></i>Editar Usuario
                </a>
                <a href="{{ route('admin.usuarios.index') }}" class="btn-outline-neon">
                    <i class="fas fa-list me-2"></i>Ver Todos los Usuarios
                </a>
                <button type="button" 
                        class="btn-danger-neon"
                        onclick="confirmarEliminacion()">
                    <i class="fas fa-trash me-2"></i>Eliminar Usuario
                </button>
            </div>

            <!-- Formulario de eliminación oculto -->
            <form id="delete-form" 
                  action="{{ route('admin.usuarios.destroy', $usuario) }}" 
                  method="POST" 
                  style="display: none;">
                @csrf
                @method('DELETE')
            </form>
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

    /* Info Items */
    .info-item {
        margin-bottom: 0;
    }

    .info-label {
        color: #94a3b8;
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0.5rem;
        display: block;
    }

    .info-label i {
        color: var(--neon-cyan);
    }

    .info-value {
        color: #e2e8f0;
        font-size: 1rem;
        font-weight: 500;
        margin: 0;
        word-break: break-word;
    }

    /* Badges */
    .badge-success {
        background: rgba(16, 185, 129, 0.25);
        color: #10b981;
        border: 2px solid #10b981;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        display: inline-block;
        box-shadow: 0 0 15px rgba(16, 185, 129, 0.3);
    }

    .badge-info {
        background: rgba(6, 182, 212, 0.25);
        color: #06b6d4;
        border: 2px solid #06b6d4;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        display: inline-block;
        box-shadow: 0 0 15px rgba(6, 182, 212, 0.3);
    }

    .badge-warning {
        background: rgba(245, 158, 11, 0.25);
        color: #f59e0b;
        border: 2px solid #f59e0b;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        display: inline-block;
        box-shadow: 0 0 15px rgba(245, 158, 11, 0.3);
    }

    /* Badge Status - Ahora integrado en la tabla principal */
    .badge-status {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .badge-status.status-active {
        background: rgba(16, 185, 129, 0.25);
        color: #10b981;
        border: 2px solid #10b981;
        box-shadow: 0 0 15px rgba(16, 185, 129, 0.3);
    }

    .badge-status.status-inactive {
        background: rgba(100, 116, 139, 0.25);
        color: #94a3b8;
        border: 2px solid #64748b;
        box-shadow: 0 0 15px rgba(100, 116, 139, 0.3);
    }

    .badge-status.status-suspended {
        background: rgba(239, 68, 68, 0.25);
        color: #ef4444;
        border: 2px solid #ef4444;
        box-shadow: 0 0 15px rgba(239, 68, 68, 0.3);
    }

    /* Buttons */
    .btn-neon {
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        border: none;
        color: #0f172a;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
        text-decoration: none;
        display: inline-block;
    }

    .btn-neon:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 30px rgba(0, 212, 255, 0.6);
        color: #0f172a;
    }

    .btn-outline-neon {
        background: transparent;
        border: 2px solid var(--neon-cyan);
        color: var(--neon-cyan);
        font-weight: 600;
        padding: 0.75rem 1.5rem;
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
        text-decoration: none;
        display: inline-block;
    }

    .btn-neon-lg:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 30px rgba(0, 212, 255, 0.6);
        color: #0f172a;
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

    /* Form Actions */
    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
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
        .btn-outline-neon,
        .btn-danger-neon,
        .btn-neon {
            width: 100%;
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
    function confirmarEliminacion() {
        if (confirm('¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.')) {
            document.getElementById('delete-form').submit();
        }
    }
</script>
@endpush
@endsection