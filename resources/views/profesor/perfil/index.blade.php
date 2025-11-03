@extends('layouts.app')

@section('title', 'Mi Perfil - Profesor')
@section('page-title', 'Mi Perfil')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header-dark mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-user-circle me-3"></i>Mi Perfil
                </h1>
                <p class="header-subtitle mb-0">Información personal y configuración de cuenta</p>
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
        <!-- Columna Izquierda: Foto y Estadísticas -->
        <div class="col-lg-4">
            <!-- Foto de Perfil -->
            <div class="card-dark mb-4">
                <div class="card-body-dark text-center">
                    <div class="profile-photo-container">
                        @if($profesor->foto_perfil)
                            <img src="{{ asset('storage/' . $profesor->foto_perfil) }}" alt="Foto de perfil" class="profile-photo">
                        @else
                            <div class="profile-photo-placeholder">
                                <i class="fas fa-user-tie"></i>
                            </div>
                        @endif
                    </div>

                    <h4 class="profile-name">{{ $profesor->nombre_completo }}</h4>
                    <p class="profile-role">
                        <i class="fas fa-chalkboard-teacher me-2"></i>Profesor
                    </p>

                    <div class="profile-actions">
                        <button type="button" class="btn-neon-sm" data-bs-toggle="modal" data-bs-target="#modalFoto">
                            <i class="fas fa-camera me-2"></i>Cambiar Foto
                        </button>
                        @if($profesor->foto_perfil)
                            <form action="{{ route('profesor.perfil.eliminar-foto') }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger-sm" onclick="return confirm('¿Eliminar foto de perfil?')">
                                    <i class="fas fa-trash me-2"></i>Eliminar
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Estadísticas Académicas -->
            <div class="card-dark">
                <div class="card-header-dark">
                    <h5><i class="fas fa-chart-bar me-2"></i>Estadísticas Académicas</h5>
                </div>
                <div class="card-body-dark">
                    <div class="stat-item">
                        <div class="stat-icon bg-primary">
                            <i class="fas fa-chalkboard"></i>
                        </div>
                        <div class="stat-info">
                            <h5>{{ $stats['total_secciones'] }}</h5>
                            <p>Secciones Asignadas</p>
                        </div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-icon bg-success">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div class="stat-info">
                            <h5>{{ $stats['total_estudiantes'] }}</h5>
                            <p>Estudiantes Totales</p>
                        </div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-icon bg-warning">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="stat-info">
                            <h5>{{ $stats['evaluaciones_activas'] }}</h5>
                            <p>Evaluaciones Activas</p>
                        </div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-icon bg-info">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="stat-info">
                            <h5>{{ $stats['calificaciones_pendientes'] }}</h5>
                            <p>Por Calificar</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Información y Configuración -->
        <div class="col-lg-8">
            <!-- Información Personal -->
            <div class="card-dark mb-4">
                <div class="card-header-dark">
                    <h5><i class="fas fa-user me-2"></i>Información Personal</h5>
                </div>
                <div class="card-body-dark">
                    <form action="{{ route('profesor.perfil.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label-neon">Nombre Completo</label>
                                <input type="text" class="form-control-neon" value="{{ $profesor->nombre_completo }}" disabled>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-neon">DNI</label>
                                <input type="text" class="form-control-neon" value="{{ $profesor->dni }}" disabled>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-neon">Email *</label>
                                <input type="email" name="email" class="form-control-neon @error('email') is-invalid @enderror"
                                       value="{{ old('email', $profesor->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-neon">Teléfono</label>
                                <input type="text" name="telefono" class="form-control-neon @error('telefono') is-invalid @enderror"
                                       value="{{ old('telefono', $profesor->telefono) }}">
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-neon">Fecha de Nacimiento</label>
                                <input type="date" name="fecha_nacimiento" class="form-control-neon @error('fecha_nacimiento') is-invalid @enderror"
                                       value="{{ old('fecha_nacimiento', $profesor->fecha_nacimiento) }}">
                                @error('fecha_nacimiento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-neon">Género</label>
                                <input type="text" class="form-control-neon" value="{{ $profesor->genero ?? 'No especificado' }}" disabled>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-neon">Especialidad</label>
                                <input type="text" class="form-control-neon" value="{{ $profesor->especialidad ?? 'No especificada' }}" disabled>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-neon">Fecha de Contratación</label>
                                <input type="date" class="form-control-neon" value="{{ $profesor->fecha_contratacion }}" disabled>
                            </div>

                            <div class="col-12">
                                <label class="form-label-neon">Dirección</label>
                                <textarea name="direccion" class="form-control-neon @error('direccion') is-invalid @enderror"
                                          rows="2">{{ old('direccion', $profesor->direccion) }}</textarea>
                                @error('direccion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn-neon">
                                <i class="fas fa-save me-2"></i>Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Cambiar Contraseña -->
            <div class="card-dark">
                <div class="card-header-dark">
                    <h5><i class="fas fa-lock me-2"></i>Cambiar Contraseña</h5>
                </div>
                <div class="card-body-dark">
                    <form action="{{ route('profesor.perfil.cambiar-password') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label-neon">Contraseña Actual *</label>
                                <input type="password" name="password_actual" class="form-control-neon @error('password_actual') is-invalid @enderror" required>
                                @error('password_actual')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-neon">Nueva Contraseña *</label>
                                <input type="password" name="password" class="form-control-neon @error('password') is-invalid @enderror" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted-neon">Mínimo 8 caracteres</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label-neon">Confirmar Contraseña *</label>
                                <input type="password" name="password_confirmation" class="form-control-neon" required>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn-neon">
                                <i class="fas fa-key me-2"></i>Actualizar Contraseña
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Cambiar Foto -->
<div class="modal fade" id="modalFoto" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content modal-dark">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-camera me-2"></i>Cambiar Foto de Perfil
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('profesor.perfil.subir-foto') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label-neon">Seleccionar Imagen</label>
                        <input type="file" name="foto" class="form-control-neon @error('foto') is-invalid @enderror" accept="image/*" required>
                        @error('foto')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted-neon">Formatos: JPG, PNG. Tamaño máximo: 2MB</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-outline-neon" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-neon">
                        <i class="fas fa-upload me-2"></i>Subir Foto
                    </button>
                </div>
            </form>
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

    /* Profile Photo */
    .profile-photo-container {
        margin: 0 auto 1.5rem;
        width: 180px;
        height: 180px;
        position: relative;
    }

    .profile-photo {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid var(--neon-cyan);
        box-shadow: 0 0 30px rgba(0, 212, 255, 0.5);
    }

    .profile-photo-placeholder {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.2) 0%, rgba(14, 165, 233, 0.2) 100%);
        border: 4px solid rgba(0, 212, 255, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 4rem;
        color: var(--neon-cyan);
    }

    .profile-name {
        color: #f1f5f9;
        font-weight: 700;
        margin: 1rem 0 0.5rem 0;
        font-size: 1.5rem;
    }

    .profile-role {
        color: var(--neon-cyan);
        font-weight: 600;
        margin-bottom: 1.5rem;
    }

    .profile-actions {
        display: flex;
        gap: 0.5rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    /* Stats */
    .stat-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    .stat-item:hover {
        background: rgba(0, 212, 255, 0.05);
        border-color: rgba(0, 212, 255, 0.4);
        transform: translateX(5px);
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
        flex-shrink: 0;
    }

    .stat-icon.bg-primary { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); }
    .stat-icon.bg-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .stat-icon.bg-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .stat-icon.bg-info { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }

    .stat-info h5 {
        color: var(--neon-cyan);
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .stat-info p {
        color: #cbd5e1;
        margin: 0;
        font-size: 0.875rem;
    }

    /* Forms */
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

    .text-muted-neon {
        color: #94a3b8;
        font-size: 0.875rem;
        display: block;
        margin-top: 0.25rem;
    }

    .is-invalid {
        border-color: #ef4444 !important;
    }

    .invalid-feedback {
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    /* Buttons */
    .btn-neon, .btn-neon-sm {
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

    .btn-neon-sm {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }

    .btn-neon:hover, .btn-neon-sm:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 30px rgba(0, 212, 255, 0.5);
        color: #0f172a;
    }

    .btn-danger-sm {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.875rem;
        transition: all 0.3s ease;
    }

    .btn-danger-sm:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 20px rgba(239, 68, 68, 0.5);
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

    /* Modal */
    .modal-dark {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.98) 0%, rgba(30, 41, 59, 0.98) 100%);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 15px;
    }

    .modal-dark .modal-header {
        border-bottom: 1px solid rgba(0, 212, 255, 0.3);
    }

    .modal-dark .modal-title {
        color: var(--neon-cyan);
    }

    /* Alert */
    .alert-success {
        background: rgba(16, 185, 129, 0.2);
        border: 1px solid #10b981;
        color: #10b981;
        border-radius: 10px;
    }
</style>
@endpush
@endsection
