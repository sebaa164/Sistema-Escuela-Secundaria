@extends('layouts.app')

@section('title', 'Editar Usuario')
@section('page-title', 'Editar Usuario')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header-dark mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-user-edit me-3"></i>
                    Editar Usuario
                </h1>
                <p class="header-subtitle mb-0">{{ $usuario->nombre }} {{ $usuario->apellido }}</p>
            </div>
            <a href="{{ route('admin.usuarios.index') }}" class="btn-outline-neon">
                <i class="fas fa-arrow-left me-2"></i>Volver
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card-dark">
                <div class="card-header-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-user me-2"></i>
                        Información del Usuario
                    </h5>
                </div>
                <div class="card-body-dark">
                    <form method="POST" action="{{ route('admin.usuarios.update', $usuario) }}" id="editUserForm" autocomplete="off">
                        @csrf
                        @method('PUT')

                        <!-- Información Personal -->
                        <div class="section-header">
                            <i class="fas fa-user-circle me-2"></i>
                            <h6>Información Personal</h6>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="nombre" class="form-label-dark">
                                    <i class="fas fa-user me-2"></i>
                                    Nombre
                                    <span class="required-mark">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control-dark-lg @error('nombre') is-invalid @enderror" 
                                       id="nombre" 
                                       name="nombre" 
                                       value="{{ old('nombre', $usuario->nombre) }}"
                                       placeholder="Ingrese el nombre"
                                       required
                                       autofocus>
                                @error('nombre')
                                    <div class="invalid-feedback-dark">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="apellido" class="form-label-dark">
                                    <i class="fas fa-user me-2"></i>
                                    Apellido
                                    <span class="required-mark">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control-dark-lg @error('apellido') is-invalid @enderror" 
                                       id="apellido" 
                                       name="apellido" 
                                       value="{{ old('apellido', $usuario->apellido) }}"
                                       placeholder="Ingrese el apellido"
                                       required>
                                @error('apellido')
                                    <div class="invalid-feedback-dark">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label-dark">
                                    <i class="fas fa-envelope me-2"></i>
                                    Correo Electrónico
                                    <span class="required-mark">*</span>
                                </label>
                                <input type="email" 
                                       class="form-control-dark-lg @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email', $usuario->email) }}"
                                       placeholder="correo@ejemplo.com"
                                       required>
                                @error('email')
                                    <div class="invalid-feedback-dark">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="telefono" class="form-label-dark">
                                    <i class="fas fa-phone me-2"></i>
                                    Teléfono
                                    <span class="required-mark">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control-dark-lg @error('telefono') is-invalid @enderror" 
                                       id="telefono" 
                                       name="telefono" 
                                       value="{{ old('telefono', $usuario->telefono) }}"
                                       placeholder="Ej: +54 123 456 7890">
                                @error('telefono')
                                    <div class="invalid-feedback-dark">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="fecha_nacimiento" class="form-label-dark">
                                    <i class="fas fa-calendar me-2"></i>
                                    Fecha de Nacimiento
                                    <span class="required-mark">*</span>
                                </label>
@php
    $fechaValue = old('fecha_nacimiento');
    if (!$fechaValue && $usuario->fecha_nacimiento) {
        try {
            $fechaValue = \Carbon\Carbon::parse($usuario->fecha_nacimiento)->format('Y-m-d');
        } catch (\Exception $e) {
            $fechaValue = '';
        }
    }
@endphp
                                <input type="date" 
                                       class="form-control-dark-lg @error('fecha_nacimiento') is-invalid @enderror" 
                                       id="fecha_nacimiento" 
                                       name="fecha_nacimiento" 
                                       value="{{ $fechaValue }}">
                                @error('fecha_nacimiento')
                                    <div class="invalid-feedback-dark">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="tipo_usuario" class="form-label-dark">
                                    <i class="fas fa-user-tag me-2"></i>
                                    Tipo de Usuario
                                    <span class="required-mark">*</span>
                                </label>
                                <select class="form-select-dark-lg @error('tipo_usuario') is-invalid @enderror" 
                                        id="tipo_usuario" 
                                        name="tipo_usuario" 
                                        required>
                                    <option value="">Seleccione un tipo</option>
                                    <option value="estudiante" {{ old('tipo_usuario', $usuario->tipo_usuario) == 'estudiante' ? 'selected' : '' }}>Estudiante</option>
                                    <option value="profesor" {{ old('tipo_usuario', $usuario->tipo_usuario) == 'profesor' ? 'selected' : '' }}>Profesor</option>
                                    <option value="administrador" {{ old('tipo_usuario', $usuario->tipo_usuario) == 'administrador' ? 'selected' : '' }}>Administrador</option>
                                </select>
                                @error('tipo_usuario')
                                    <div class="invalid-feedback-dark">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6" id="campo_estado_general" style="{{ $usuario->tipo_usuario === 'estudiante' ? 'display: none;' : 'display: block;' }}">
                                <label for="estado" class="form-label-dark">
                                    <i class="fas fa-toggle-on me-2"></i>
                                    Estado
                                    <span class="required-mark">*</span>
                                </label>
                                <select class="form-select-dark-lg @error('estado') is-invalid @enderror" 
                                        id="estado" 
                                        name="estado" 
                                        required>
                                    <option value="activo" {{ old('estado', $usuario->estado) == 'activo' ? 'selected' : '' }}>Activo</option>
                                    <option value="inactivo" {{ old('estado', $usuario->estado) == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                                    <option value="suspendido" {{ old('estado', $usuario->estado) == 'suspendido' ? 'selected' : '' }}>Suspendido</option>
                                </select>
                                @error('estado')
                                    <div class="invalid-feedback-dark">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-12">
                                <label for="direccion" class="form-label-dark">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    Dirección
                                </label>
                                <textarea class="form-control-dark-lg @error('direccion') is-invalid @enderror" 
                                          id="direccion" 
                                          name="direccion" 
                                          rows="4"
                                          placeholder="Ingrese la dirección completa">{{ old('direccion', $usuario->direccion) }}</textarea>
                                @error('direccion')
                                    <div class="invalid-feedback-dark">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Campos específicos de estudiantes -->
                        <div id="campos_estudiante" style="{{ $usuario->tipo_usuario === 'estudiante' ? 'display: block;' : 'display: none;' }}">
                            <div class="section-header mt-4">
                                <i class="fas fa-user-shield me-2"></i>
                                <h6>Información del Responsable</h6>
                            </div>

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label for="estado_estudiante" class="form-label-dark">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Estado Académico
                                        @if($usuario->tipo_usuario === 'estudiante')<span class="required-mark">*</span>@endif
                                    </label>
                                    <select class="form-select-dark-lg @error('estado_estudiante') is-invalid @enderror" 
                                            id="estado_estudiante" 
                                            name="estado_estudiante">
                                        <option value="">Seleccione un estado</option>
                                        <option value="regular" {{ old('estado_estudiante', $usuario->estado_estudiante) == 'regular' ? 'selected' : '' }}>Regular</option>
                                        <option value="suspendido" {{ old('estado_estudiante', $usuario->estado_estudiante) == 'suspendido' ? 'selected' : '' }}>Suspendido</option>
                                        <option value="libre" {{ old('estado_estudiante', $usuario->estado_estudiante) == 'libre' ? 'selected' : '' }}>Libre</option>
                                        <option value="preinscripto" {{ old('estado_estudiante', $usuario->estado_estudiante) == 'preinscripto' ? 'selected' : '' }}>Preinscripto</option>
                                    </select>
                                    @error('estado_estudiante')
                                        <div class="invalid-feedback-dark">
                                            <i class="fas fa-exclamation-circle me-1"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="representante_parentesco" class="form-label-dark">
                                        <i class="fas fa-user-friends me-2"></i>
                                        Responsable del Estudiante
                                    </label>
                                    <select class="form-select-dark-lg @error('representante_parentesco') is-invalid @enderror" 
                                            id="representante_parentesco" 
                                            name="representante_parentesco">
                                        <option value="">Seleccione responsable</option>
                                        <option value="padre" {{ old('representante_parentesco', $tutor->parentesco ?? '') == 'padre' ? 'selected' : '' }}>Padre</option>
                                        <option value="madre" {{ old('representante_parentesco', $tutor->parentesco ?? '') == 'madre' ? 'selected' : '' }}>Madre</option>
                                        <option value="tutor" {{ old('representante_parentesco', $tutor->parentesco ?? '') == 'tutor' ? 'selected' : '' }}>Tutor</option>
                                        <option value="abuelo" {{ old('representante_parentesco', $tutor->parentesco ?? '') == 'abuelo' ? 'selected' : '' }}>Abuelo/Abuela</option>
                                        <option value="tio" {{ old('representante_parentesco', $tutor->parentesco ?? '') == 'tio' ? 'selected' : '' }}>Tío/Tía</option>
                                        <option value="otro" {{ old('representante_parentesco', $tutor->parentesco ?? '') == 'otro' ? 'selected' : '' }}>Otro</option>
                                    </select>
                                    @error('representante_parentesco')
                                        <div class="invalid-feedback-dark">
                                            <i class="fas fa-exclamation-circle me-1"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-4 mt-2">
                                <div class="col-md-6">
                                    <label for="representante_nombre" class="form-label-dark">
                                        <i class="fas fa-user me-2"></i>
                                        Nombre del Representante
                                    </label>
                                    <input type="text" 
                                           class="form-control-dark-lg @error('representante_nombre') is-invalid @enderror" 
                                           id="representante_nombre" 
                                           name="representante_nombre" 
                                           value="{{ old('representante_nombre', $tutor->nombre ?? '') }}"
                                           placeholder="Ingrese el nombre"
                                           autocomplete="off">
                                    @error('representante_nombre')
                                        <div class="invalid-feedback-dark">
                                            <i class="fas fa-exclamation-circle me-1"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="representante_apellido" class="form-label-dark">
                                        <i class="fas fa-user me-2"></i>
                                        Apellido del Representante
                                    </label>
                                    <input type="text" 
                                           class="form-control-dark-lg @error('representante_apellido') is-invalid @enderror" 
                                           id="representante_apellido" 
                                           name="representante_apellido" 
                                           value="{{ old('representante_apellido', $tutor->apellido ?? '') }}"
                                           placeholder="Ingrese el apellido"
                                           autocomplete="off">
                                    @error('representante_apellido')
                                        <div class="invalid-feedback-dark">
                                            <i class="fas fa-exclamation-circle me-1"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-4 mt-2">
                                <div class="col-md-6">
                                    <label for="representante_telefono" class="form-label-dark">
                                        <i class="fas fa-phone me-2"></i>
                                        Teléfono del Representante
                                        @if($usuario->tipo_usuario === 'estudiante')<span class="required-mark">*</span>@endif
                                    </label>
                                    <input type="tel" 
                                           class="form-control-dark-lg @error('representante_telefono') is-invalid @enderror" 
                                           id="representante_telefono" 
                                           name="representante_telefono" 
                                           value="{{ old('representante_telefono', $tutor->telefono ?? '') }}"
                                           placeholder="Ej: +54 123 456 7890"
                                           autocomplete="off">
                                    @error('representante_telefono')
                                        <div class="invalid-feedback-dark">
                                            <i class="fas fa-exclamation-circle me-1"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="representante_email" class="form-label-dark">
                                        <i class="fas fa-envelope me-2"></i>
                                        Email del Representante
                                    </label>
                                    <input type="email" 
                                           class="form-control-dark-lg @error('representante_email') is-invalid @enderror" 
                                           id="representante_email" 
                                           name="representante_email" 
                                           value="{{ old('representante_email', $tutor->email ?? '') }}"
                                           placeholder="correo@ejemplo.com"
                                           autocomplete="off">
                                    @error('representante_email')
                                        <div class="invalid-feedback-dark">
                                            <i class="fas fa-exclamation-circle me-1"></i>
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Cambiar Contraseña (Opcional) -->
                        <div class="section-header mt-4">
                            <i class="fas fa-lock me-2"></i>
                            <h6>Cambiar Contraseña (Opcional)</h6>
                        </div>

                        <div class="alert-info-dark mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            Deja estos campos vacíos si no deseas cambiar la contraseña
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="password" class="form-label-dark">
                                    <i class="fas fa-key me-2"></i>
                                    Nueva Contraseña
                                </label>
                                <input type="password" 
                                       class="form-control-dark-lg @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Mínimo 8 caracteres">
                                @error('password')
                                    <div class="invalid-feedback-dark">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                                <small class="form-text-dark">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Deja en blanco para mantener la contraseña actual
                                </small>
                            </div>

                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label-dark">
                                    <i class="fas fa-key me-2"></i>
                                    Confirmar Nueva Contraseña
                                </label>
                                <input type="password" 
                                       class="form-control-dark-lg" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       placeholder="Repita la contraseña">
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="form-actions mt-5">
                            <button type="submit" class="btn-neon-lg">
                                <i class="fas fa-save me-2"></i>Guardar Cambios
                            </button>
                            <a href="{{ route('admin.usuarios.index') }}" class="btn-outline-neon">
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
        min-height: 800px;
    }

    .col-lg-10 .card-dark {
        min-height: 900px;
    }

    /* Section Header */
    .section-header {
        display: flex;
        align-items: center;
        color: var(--neon-cyan);
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid rgba(0, 212, 255, 0.3);
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

    /* Alert Info */
    .alert-info-dark {
        background: rgba(6, 182, 212, 0.15);
        border: 1px solid rgba(6, 182, 212, 0.3);
        border-radius: 10px;
        color: #06b6d4;
        padding: 1rem;
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

    .required-mark {
        color: #f59e0b;
        margin-left: 0.25rem;
    }

    /* Form Controls */
    .form-control-dark,
    .form-control-dark-lg {
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 10px;
        color: #e2e8f0;
        width: 100%;
        transition: all 0.3s ease;
    }

    .form-control-dark {
        padding: 0.75rem 1rem;
    }

    .form-control-dark-lg {
        padding: 1.25rem 1.5rem;
        font-size: 1.05rem;
    }

    .form-control-dark:focus,
    .form-control-dark-lg:focus {
        background: rgba(15, 23, 42, 0.95);
        border-color: var(--neon-cyan);
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
        outline: none;
        color: white;
    }

    .form-control-dark::placeholder,
    .form-control-dark-lg::placeholder {
        color: #64748b;
    }

    .form-control-dark.is-invalid,
    .form-control-dark-lg.is-invalid {
        border-color: #f59e0b;
        box-shadow: 0 0 0 0.2rem rgba(245, 158, 11, 0.25);
    }

    .form-select-dark,
    .form-select-dark-lg {
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 10px;
        color: #e2e8f0;
        width: 100%;
        transition: all 0.3s ease;
    }

    .form-select-dark {
        padding: 0.75rem 1rem;
    }

    .form-select-dark-lg {
        padding: 1.25rem 1.5rem;
        font-size: 1.05rem;
    }

    .form-select-dark:focus,
    .form-select-dark-lg:focus {
        background: rgba(15, 23, 42, 0.95);
        border-color: var(--neon-cyan);
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
        outline: none;
    }

    .form-select-dark option,
    .form-select-dark-lg option {
        background: #1e293b;
        color: #e2e8f0;
    }

    .form-text-dark {
        color: #94a3b8;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: block;
    }

    .invalid-feedback-dark {
        color: #f59e0b;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: block;
        background: rgba(245, 158, 11, 0.1);
        border: 1px solid rgba(245, 158, 11, 0.3);
        border-radius: 8px;
        padding: 0.5rem 0.75rem;
        font-weight: 500;
    }

    /* Form Actions */
    .form-actions {
        display: flex;
        gap: 1rem;
        justify-content: center;
        padding-top: 2rem;
        border-top: 1px solid rgba(0, 212, 255, 0.2);
        flex-wrap: wrap;
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
        text-decoration: none;
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
        .btn-outline-neon {
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
    // Función para hacer scroll al primer campo con error
    function scrollToFirstError() {
        // Buscar primero por campos con clase is-invalid
        let firstErrorField = document.querySelector('.is-invalid');
        
        // Si no encuentra, buscar campos que estén cerca de mensajes de error
        if (!firstErrorField) {
            const errorMessage = document.querySelector('.invalid-feedback-dark');
            if (errorMessage) {
                // Buscar el campo más cercano al mensaje de error
                const nearbyInputs = errorMessage.parentNode.querySelectorAll('input, select, textarea');
                if (nearbyInputs.length > 0) {
                    firstErrorField = nearbyInputs[0];
                }
            }
        }
        
        if (firstErrorField) {
            // Calcular la posición del campo considerando el header fijo
            const headerHeight = document.querySelector('.header') ? document.querySelector('.header').offsetHeight : 70;
            const fieldPosition = firstErrorField.getBoundingClientRect().top + window.pageYOffset - headerHeight - 20;
            
            // Hacer scroll suave hacia el campo
            window.scrollTo({
                top: fieldPosition,
                behavior: 'smooth'
            });
            
            // Enfocar el campo después de un pequeño delay
            setTimeout(() => {
                firstErrorField.focus();
                // Si es un select, intentar abrirlo
                if (firstErrorField.tagName === 'SELECT') {
                    firstErrorField.click();
                }
            }, 600);
        }
    }

    // Función para configurar campos requeridos del representante
    function configurarCamposRepresentante(requeridos) {
        const representanteCampos = [
            'representante_parentesco',
            'representante_nombre', 
            'representante_apellido',
            'representante_telefono'
        ];
        
        // Campo estado estudiante
        const estadoEstudianteCampo = document.getElementById('estado_estudiante');
        
        representanteCampos.forEach(campo => {
            const elemento = document.getElementById(campo);
            if (elemento) {
                elemento.required = requeridos;
                
                // Actualizar visualmente los asteriscos
                const label = elemento.closest('.col-md-6, .col-md-12').querySelector('.form-label-dark');
                if (label) {
                    const asterisco = label.querySelector('.required-mark');
                    if (requeridos) {
                        if (!asterisco) {
                            const span = document.createElement('span');
                            span.className = 'required-mark';
                            span.textContent = '*';
                            label.appendChild(span);
                        }
                    } else {
                        if (asterisco) {
                            asterisco.remove();
                        }
                    }
                }
            }
        });
        
        // Configurar estado_estudiante
        if (estadoEstudianteCampo) {
            // Actualizar asterisco del estado_estudiante
            const labelEstado = estadoEstudianteCampo.closest('.col-md-6, .col-md-12').querySelector('.form-label-dark');
            if (labelEstado) {
                const asterisco = labelEstado.querySelector('.required-mark');
                if (requeridos) {
                    if (!asterisco) {
                        const span = document.createElement('span');
                        span.className = 'required-mark';
                        span.textContent = '*';
                        labelEstado.appendChild(span);
                    }
                } else {
                    if (asterisco) {
                        asterisco.remove();
                    }
                }
            }
        }
    }

    // Configurar campos al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        // Solo configurar si no hay errores de validación
        const hasErrors = document.querySelector('.invalid-feedback-dark') !== null;
        
        if (!hasErrors) {
            const tipoUsuarioActual = '{{ $usuario->tipo_usuario }}';
            configurarCamposRepresentante(tipoUsuarioActual === 'estudiante');
        } else {
            // Si hay errores, hacer scroll al primer campo con error
            setTimeout(() => {
                scrollToFirstError();
            }, 100);
            
            // Si hay errores, mantener la configuración actual pero asegurar que los campos requeridos estén marcados
            const tipoUsuarioActual = '{{ $usuario->tipo_usuario }}';
            if (tipoUsuarioActual === 'estudiante') {
                configurarCamposRepresentante(true);
                
                // Asegurar que el campo estado_estudiante tenga required
                const estadoEstudianteCampo = document.getElementById('estado_estudiante');
                if (estadoEstudianteCampo) {
                    estadoEstudianteCampo.required = true;
                }
            }
        }
    });

    // Validación del formulario
    document.getElementById('editUserForm').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const password_confirmation = document.getElementById('password_confirmation').value;
        
        // Solo validar si se ingresó una contraseña
        if (password || password_confirmation) {
            if (password !== password_confirmation) {
                e.preventDefault();
                alert('Las contraseñas no coinciden');
                return false;
            }
            
            if (password.length < 8) {
                e.preventDefault();
                alert('La contraseña debe tener al menos 8 caracteres');
                return false;
            }
        }
    });

    // Mostrar/ocultar campos específicos de estudiantes y estado general
    document.getElementById('tipo_usuario').addEventListener('change', function() {
        // No cambiar configuración si hay errores de validación
        const hasErrors = document.querySelector('.invalid-feedback-dark') !== null;
        if (hasErrors) return;
        
        const camposEstudiante = document.getElementById('campos_estudiante');
        const campoEstadoGeneral = document.getElementById('campo_estado_general');
        
        if (this.value === 'estudiante') {
            camposEstudiante.style.display = 'block';
            campoEstadoGeneral.style.display = 'none';
            configurarCamposRepresentante(true);
        } else {
            camposEstudiante.style.display = 'none';
            campoEstadoGeneral.style.display = 'block';
            configurarCamposRepresentante(false);
        }
    });
</script>
@endpush
@endsection