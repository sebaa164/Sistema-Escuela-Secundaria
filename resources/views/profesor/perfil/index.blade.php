@extends('layouts.app')

@section('title', 'Mi Perfil')
@section('page-title', 'Mi Perfil')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1>
                    <i class="fas fa-user-circle me-2"></i>Mi Perfil
                </h1>
                <p class="text-muted mb-0">Información personal y configuración de cuenta</p>
            </div>
            <a href="{{ auth()->user()->tipo_usuario === 'administrador' ? route('admin.dashboard') : route('profesor.dashboard') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Columna Izquierda: Foto y Estadísticas -->
        <div class="col-lg-4">
            <!-- Foto de Perfil -->
            <div class="card">
                <div class="card-body text-center">
                    <div class="profile-photo-container">
                        @if($usuario->foto_perfil)
                            <img src="{{ asset('storage/' . $usuario->foto_perfil) }}" alt="Foto de perfil" class="profile-photo">
                        @else
                            <div class="profile-photo-placeholder">
                                <i class="fas {{ $usuario->tipo_usuario === 'administrador' ? 'fa-user-shield' : 'fa-user-tie' }}"></i>
                            </div>
                        @endif
                    </div>

                    <h4 class="profile-name">{{ $usuario->nombre_completo }}</h4>
                    <p class="profile-role">
                        <i class="fas {{ $usuario->tipo_usuario === 'administrador' ? 'fa-shield-alt' : 'fa-chalkboard-teacher' }} me-2"></i>
                        {{ ucfirst($usuario->tipo_usuario) }}
                    </p>
                    <span class="badge badge-primary mb-3">
                        <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>{{ ucfirst($usuario->estado) }}
                    </span>

                    <div class="profile-actions">
                        <button type="button" class="btn btn-primary btn-sm w-100 mb-2" data-bs-toggle="modal" data-bs-target="#modalFoto">
                            <i class="fas fa-camera me-2"></i>Cambiar Foto
                        </button>
                        @if($usuario->foto_perfil)
                            <form action="{{ auth()->user()->tipo_usuario === 'administrador' ? route('admin.perfil.eliminar-foto') : route('profesor.perfil.eliminar-foto') }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100" onclick="return confirm('¿Está seguro de eliminar la foto de perfil?')">
                                    <i class="fas fa-trash me-2"></i>Eliminar Foto
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Estadísticas</h6>
                </div>
                <div class="card-body">
                    @foreach($stats as $key => $stat)
                    <div class="stat-item-minimal">
                        <div class="stat-icon-minimal {{ $stat['color'] }}">
                            <i class="fas {{ $stat['icon'] }}"></i>
                        </div>
                        <div class="stat-info-minimal">
                            <h5 class="mb-0">{{ $stat['value'] }}</h5>
                            <small class="text-muted">{{ $stat['label'] }}</small>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Información y Configuración -->
        <div class="col-lg-8">
            <!-- Información Personal -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Información Personal</h5>
                </div>
                <div class="card-body">
                    <form action="{{ auth()->user()->tipo_usuario === 'administrador' ? route('admin.perfil.update') : route('profesor.perfil.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nombre</label>
                                <input type="text" class="form-control" value="{{ $usuario->nombre }}" disabled>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Apellido</label>
                                <input type="text" class="form-control" value="{{ $usuario->apellido }}" disabled>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $usuario->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Teléfono</label>
                                <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror"
                                       value="{{ old('telefono', $usuario->telefono) }}" placeholder="Ej: +54 261 123-4567">
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Fecha de Nacimiento</label>
                                <input type="date" name="fecha_nacimiento" class="form-control @error('fecha_nacimiento') is-invalid @enderror"
                                       value="{{ old('fecha_nacimiento', $usuario->fecha_nacimiento ? $usuario->fecha_nacimiento->format('Y-m-d') : '') }}">
                                @error('fecha_nacimiento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Tipo de Usuario</label>
                                <input type="text" class="form-control" value="{{ ucfirst($usuario->tipo_usuario) }}" disabled>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Dirección</label>
                                <textarea name="direccion" class="form-control @error('direccion') is-invalid @enderror"
                                          rows="2" placeholder="Ingrese su dirección completa">{{ old('direccion', $usuario->direccion) }}</textarea>
                                @error('direccion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar Cambios
                            </button>
                            <button type="reset" class="btn btn-outline-secondary">
                                <i class="fas fa-undo me-2"></i>Restablecer
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Cambiar Contraseña -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-lock me-2"></i>Cambiar Contraseña</h5>
                </div>
                <div class="card-body">
                    <form action="{{ auth()->user()->tipo_usuario === 'administrador' ? route('admin.perfil.cambiar-password') : route('profesor.perfil.cambiar-password') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Requisitos de contraseña:</strong> Mínimo 8 caracteres. Se recomienda usar mayúsculas, minúsculas, números y símbolos.
                        </div>

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Contraseña Actual <span class="text-danger">*</span></label>
                                <input type="password" name="password_actual" class="form-control @error('password_actual') is-invalid @enderror" required>
                                @error('password_actual')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Nueva Contraseña <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Confirmar Nueva Contraseña <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
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
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-camera me-2"></i>Cambiar Foto de Perfil
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ auth()->user()->tipo_usuario === 'administrador' ? route('admin.perfil.subir-foto') : route('profesor.perfil.subir-foto') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Seleccionar Imagen</label>
                        <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg" required>
                        @error('foto')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted d-block mt-2">
                            <i class="fas fa-info-circle me-1"></i>Formatos permitidos: JPG, PNG. Tamaño máximo: 2MB
                        </small>
                    </div>

                    <div class="preview-container" id="previewContainer" style="display: none;">
                        <label class="form-label">Vista Previa:</label>
                        <div class="text-center">
                            <img id="imagePreview" src="" alt="Vista previa" class="img-thumbnail" style="max-width: 200px; max-height: 200px; border-radius: 50%;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-2"></i>Subir Foto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Profile Photo Container */
    .profile-photo-container {
        margin: 0 auto 1.5rem;
        width: 150px;
        height: 150px;
        position: relative;
    }

    .profile-photo {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid var(--primary-color);
        box-shadow: 0 4px 12px rgba(14, 165, 233, 0.2);
    }

    .profile-photo-placeholder {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary-color) 100%);
        border: 4px solid var(--primary-color);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3.5rem;
        color: var(--white);
    }

    .profile-name {
        color: var(--gray-900);
        font-weight: 700;
        margin: 1rem 0 0.5rem 0;
        font-size: 1.5rem;
    }

    .profile-role {
        color: var(--gray-600);
        font-weight: 600;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }

    .profile-actions {
        padding-top: 0.5rem;
    }

    /* Stats Item Minimal */
    .stat-item-minimal {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: var(--gray-50);
        border-radius: 8px;
        margin-bottom: 0.75rem;
        transition: all 0.2s ease;
    }

    .stat-item-minimal:last-child {
        margin-bottom: 0;
    }

    .stat-item-minimal:hover {
        background: var(--gray-100);
        transform: translateX(3px);
    }

    .stat-icon-minimal {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: var(--white);
        flex-shrink: 0;
    }

    .stat-icon-minimal.bg-primary { background: var(--primary-color); }
    .stat-icon-minimal.bg-success { background: var(--success); }
    .stat-icon-minimal.bg-warning { background: var(--warning); }
    .stat-icon-minimal.bg-info { background: var(--info); }

    .stat-info-minimal h5 {
        color: var(--gray-900);
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0;
        line-height: 1;
    }

    .stat-info-minimal small {
        color: var(--gray-500);
        font-size: 0.85rem;
    }

    /* Form improvements */
    .form-label {
        font-weight: 600;
        color: var(--gray-700);
        margin-bottom: 0.5rem;
    }

    .form-control:disabled {
        background-color: var(--gray-100);
        opacity: 0.7;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .profile-photo-container {
            width: 120px;
            height: 120px;
        }

        .profile-photo-placeholder {
            font-size: 2.5rem;
        }

        .profile-name {
            font-size: 1.25rem;
        }

        .stat-item-minimal {
            padding: 0.875rem;
        }

        .stat-icon-minimal {
            width: 42px;
            height: 42px;
            font-size: 1rem;
        }

        .stat-info-minimal h5 {
            font-size: 1.25rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
// Preview de imagen antes de subir
document.querySelector('input[name="foto"]')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').src = e.target.result;
            document.getElementById('previewContainer').style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
});
</script>
@endpush
@endsection