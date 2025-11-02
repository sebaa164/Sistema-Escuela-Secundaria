@extends('layouts.app')

@section('title', 'Crear Usuario')
@section('page-title', 'Nuevo Usuario')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header-dark mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-user-plus me-3"></i>
                    Crear Nuevo Usuario
                </h1>
                <p class="header-subtitle mb-0">Complete el formulario para registrar un nuevo usuario</p>
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
                    <form method="POST" action="{{ route('admin.usuarios.store') }}" id="createUserForm" autocomplete="off" novalidate>
                        @csrf
                        
                        <!-- Campo oculto para evitar autocompletado -->
                        <input type="text" name="fake_username" style="display:none" autocomplete="new-password">
                        <input type="password" name="fake_password" style="display:none" autocomplete="new-password">

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
                                       class="form-control-dark @error('nombre') is-invalid @enderror" 
                                       id="nombre" 
                                       name="nombre" 
                                       value="{{ old('nombre') }}"
                                       placeholder="Ingrese el nombre completo"
                                       autocomplete="off"
                                       pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+"
                                       title="Solo se permiten letras y espacios"
                                       required
                                       autofocus>
                                @error('nombre')
                                    <div class="invalid-feedback-dark">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                                <small class="form-text-dark">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Solo letras y espacios
                                </small>
                            </div>

                            <div class="col-md-6">
                                <label for="apellido" class="form-label-dark">
                                    <i class="fas fa-user me-2"></i>
                                    Apellido
                                    <span class="required-mark">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control-dark @error('apellido') is-invalid @enderror" 
                                       id="apellido" 
                                       name="apellido" 
                                       value="{{ old('apellido') }}"
                                       placeholder="Ingrese el apellido completo"
                                       autocomplete="off"
                                       pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ\s]+"
                                       title="Solo se permiten letras y espacios"
                                       required>
                                @error('apellido')
                                    <div class="invalid-feedback-dark">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                                <small class="form-text-dark">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Solo letras y espacios
                                </small>
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label-dark">
                                    <i class="fas fa-envelope me-2"></i>
                                    Correo Electrónico
                                    <span class="required-mark">*</span>
                                </label>
                                <input type="email" 
                                       class="form-control-dark @error('email') is-invalid @enderror" 
                                       id="email" 
                                       name="email" 
                                       value="{{ old('email') }}"
                                       placeholder="correo@ejemplo.com"
                                       autocomplete="off"
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
                                <input type="tel" 
                                       class="form-control-dark @error('telefono') is-invalid @enderror" 
                                       id="telefono" 
                                       name="telefono" 
                                       value="{{ old('telefono') }}"
                                       placeholder="Ej: +54 123 456 7890"
                                       autocomplete="off"
                                       pattern="[\+\d\s\-\(\)]+"
                                       title="Solo números, espacios, guiones, paréntesis y el símbolo +"
                                       minlength="8"
                                       maxlength="20"
                                       inputmode="tel"
                                       data-phone-input="true"
                                       required>
                                @error('telefono')
                                    <div class="invalid-feedback-dark">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                                <small class="form-text-dark">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Formato válido: +54 123 456 7890
                                </small>
                            </div>

                            <div class="col-md-6">
                                <label for="fecha_nacimiento" class="form-label-dark">
                                    <i class="fas fa-calendar me-2"></i>
                                    Fecha de Nacimiento
                                    <span class="required-mark">*</span>
                                </label>
@php
    $fechaValue = old('fecha_nacimiento', '');
@endphp
                                <input type="date" 
                                       class="form-control-dark @error('fecha_nacimiento') is-invalid @enderror" 
                                       id="fecha_nacimiento" 
                                       name="fecha_nacimiento" 
                                       value="{{ $fechaValue }}"
                                       max="{{ date('Y-m-d') }}"
                                       autocomplete="off"
                                       required>
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
                                <select class="form-select-dark @error('tipo_usuario') is-invalid @enderror" 
                                        id="tipo_usuario" 
                                        name="tipo_usuario" 
                                        required>
                                    <option value="">Seleccione un tipo</option>
                                    <option value="estudiante" {{ old('tipo_usuario') == 'estudiante' ? 'selected' : '' }}>Estudiante</option>
                                    <option value="profesor" {{ old('tipo_usuario') == 'profesor' ? 'selected' : '' }}>Profesor</option>
                                    <option value="administrador" {{ old('tipo_usuario') == 'administrador' ? 'selected' : '' }}>Administrador</option>
                                </select>
                                @error('tipo_usuario')
                                    <div class="invalid-feedback-dark">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="col-md-6" id="campo_estado_general" style="display: block;">
                                <label for="estado" class="form-label-dark">
                                    <i class="fas fa-toggle-on me-2"></i>
                                    Estado
                                    <span class="required-mark">*</span>
                                </label>
                                <select class="form-select-dark @error('estado') is-invalid @enderror" 
                                        id="estado" 
                                        name="estado" 
                                        required>
                                    <option value="activo" {{ old('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                                    <option value="inactivo" {{ old('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                                    <option value="suspendido" {{ old('estado') == 'suspendido' ? 'selected' : '' }}>Suspendido</option>
                                </select>
                                @error('estado')
                                    <div class="invalid-feedback-dark">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-12">
                                <label for="direccion" class="form-label-dark">
                                    <i class="fas fa-map-marker-alt me-2"></i>
                                    Dirección
                                </label>
                                <textarea class="form-control-dark @error('direccion') is-invalid @enderror" 
                                          id="direccion" 
                                          name="direccion" 
                                          rows="3"
                                          placeholder="Ingrese la dirección completa"
                                          autocomplete="off"
                                          maxlength="255">{{ old('direccion') }}</textarea>
                                @error('direccion')
                                    <div class="invalid-feedback-dark">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>

                        <!-- Campos específicos de estudiantes -->
                        <div id="campos_estudiante" style="display: none;">
                            <div class="section-header mt-4">
                                <i class="fas fa-user-shield me-2"></i>
                                <h6>Información del Responsable</h6>
                            </div>

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label for="estado_estudiante" class="form-label-dark">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Estado Académico
                                        <span class="required-mark">*</span>
                                    </label>
                                    <select class="form-select-dark @error('estado_estudiante') is-invalid @enderror" 
                                            id="estado_estudiante" 
                                            name="estado_estudiante">
                                        <option value="regular" {{ old('estado_estudiante') == 'regular' ? 'selected' : '' }}>Regular</option>
                                        <option value="suspendido" {{ old('estado_estudiante') == 'suspendido' ? 'selected' : '' }}>Suspendido</option>
                                        <option value="libre" {{ old('estado_estudiante') == 'libre' ? 'selected' : '' }}>Libre</option>
                                        <option value="preinscripto" {{ old('estado_estudiante') == 'preinscripto' ? 'selected' : '' }}>Preinscripto</option>
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
                                        <span class="required-mark">*</span>
                                    </label>
                                    <select class="form-select-dark @error('representante_parentesco') is-invalid @enderror" 
                                            id="representante_parentesco" 
                                            name="representante_parentesco">
                                        <option value="">Seleccione responsable</option>
                                        <option value="padre" {{ old('representante_parentesco') == 'padre' ? 'selected' : '' }}>Padre</option>
                                        <option value="madre" {{ old('representante_parentesco') == 'madre' ? 'selected' : '' }}>Madre</option>
                                        <option value="tutor" {{ old('representante_parentesco') == 'tutor' ? 'selected' : '' }}>Tutor</option>
                                        <option value="abuelo" {{ old('representante_parentesco') == 'abuelo' ? 'selected' : '' }}>Abuelo/Abuela</option>
                                        <option value="tio" {{ old('representante_parentesco') == 'tio' ? 'selected' : '' }}>Tío/Tía</option>
                                        <option value="otro" {{ old('representante_parentesco') == 'otro' ? 'selected' : '' }}>Otro</option>
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
                                        <span class="required-mark">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control-dark @error('representante_nombre') is-invalid @enderror" 
                                           id="representante_nombre" 
                                           name="representante_nombre" 
                                           value="{{ old('representante_nombre') }}"
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
                                        <span class="required-mark">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control-dark @error('representante_apellido') is-invalid @enderror" 
                                           id="representante_apellido" 
                                           name="representante_apellido" 
                                           value="{{ old('representante_apellido') }}"
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
                                        <span class="required-mark">*</span>
                                    </label>
                                    <input type="tel" 
                                           class="form-control-dark @error('representante_telefono') is-invalid @enderror" 
                                           id="representante_telefono" 
                                           name="representante_telefono" 
                                           value="{{ old('representante_telefono') }}"
                                           placeholder="Ej: +54 123 456 7890"
                                           autocomplete="off"
                                           pattern="[\+\d\s\-\(\)]+"
                                           title="Solo números, espacios, guiones, paréntesis y el símbolo +"
                                           minlength="8"
                                           maxlength="20"
                                           inputmode="tel"
                                           data-phone-input="true">
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
                                           class="form-control-dark @error('representante_email') is-invalid @enderror" 
                                           id="representante_email" 
                                           name="representante_email" 
                                           value="{{ old('representante_email') }}"
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

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="password" class="form-label-dark">
                                    <i class="fas fa-key me-2"></i>
                                    Contraseña
                                    <span class="required-mark">*</span>
                                </label>
                                <input type="password" 
                                       class="form-control-dark @error('password') is-invalid @enderror" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Mínimo 8 caracteres"
                                       autocomplete="new-password"
                                       minlength="8"
                                       required>
                                @error('password')
                                    <div class="invalid-feedback-dark">
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                                <small class="form-text-dark">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Las credenciales se mostrarán después de crear el usuario
                                </small>
                            </div>

                            <div class="col-md-6">
                                <label for="password_confirmation" class="form-label-dark">
                                    <i class="fas fa-key me-2"></i>
                                    Confirmar Contraseña
                                    <span class="required-mark">*</span>
                                </label>
                                <input type="password" 
                                       class="form-control-dark" 
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       placeholder="Repita la contraseña"
                                       autocomplete="new-password"
                                       minlength="8"
                                       required>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="form-actions mt-5">
                            <button type="submit" class="btn-neon-lg">
                                <i class="fas fa-save me-2"></i>Crear Usuario
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
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
        outline: none;
        color: white;
    }

    .form-control-dark::placeholder {
        color: #64748b;
    }

    .form-control-dark.is-invalid,
    .form-control-dark-lg.is-invalid {
        border-color: #f59e0b;
        box-shadow: 0 0 0 0.2rem rgba(245, 158, 11, 0.25);
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
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
        outline: none;
    }

    .form-select-dark option {
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
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('createUserForm');
    
    // Validación del formulario - ahora manual ya que tenemos novalidate
    form.addEventListener('submit', function(e) {
        // Validar campos requeridos manualmente
        const camposRequeridos = [
            { id: 'nombre', nombre: 'Nombre' },
            { id: 'apellido', nombre: 'Apellido' },
            { id: 'email', nombre: 'Correo Electrónico' },
            { id: 'telefono', nombre: 'Teléfono' },
            { id: 'fecha_nacimiento', nombre: 'Fecha de Nacimiento' },
            { id: 'password', nombre: 'Contraseña' },
            { id: 'password_confirmation', nombre: 'Confirmar Contraseña' }
        ];

        // Agregar validación condicional para tipo_usuario
        const tipoUsuario = document.getElementById('tipo_usuario').value;
        if (tipoUsuario) {
            camposRequeridos.push({ id: 'tipo_usuario', nombre: 'Tipo de Usuario' });
            
            if (tipoUsuario !== 'estudiante') {
                camposRequeridos.push({ id: 'estado', nombre: 'Estado' });
            }
        }

        // Validar campos requeridos
        let formularioValido = true;
        for (let campo of camposRequeridos) {
            const elemento = document.getElementById(campo.id);
            if (elemento && !elemento.value.trim()) {
                mostrarError(elemento, `Por favor, completa el campo: ${campo.nombre}`);
                if (formularioValido) {
                    elemento.focus();
                    formularioValido = false;
                }
            } else if (elemento) {
                ocultarError(elemento);
            }
        }

        // Si el formulario no es válido, prevenir envío
        if (!formularioValido) {
            e.preventDefault();
            return false;
        }
        
        // Validar lógica adicional (contraseñas)
        const password = document.getElementById('password').value;
        const password_confirmation = document.getElementById('password_confirmation').value;
        
        if (password !== password_confirmation) {
            e.preventDefault();
            mostrarError(document.getElementById('password_confirmation'), 'Las contraseñas no coinciden');
            document.getElementById('password_confirmation').focus();
            return false;
        }
        
        if (password.length < 8) {
            e.preventDefault();
            mostrarError(document.getElementById('password'), 'La contraseña debe tener al menos 8 caracteres');
            document.getElementById('password').focus();
            return false;
        }
        
        // Validar campos de estudiante si es necesario
        if (tipoUsuario === 'estudiante') {
            const camposEstudiante = [
                { id: 'estado_estudiante', nombre: 'Estado Académico' },
                { id: 'representante_parentesco', nombre: 'Parentesco del Responsable' },
                { id: 'representante_nombre', nombre: 'Nombre del Representante' },
                { id: 'representante_apellido', nombre: 'Apellido del Representante' },
                { id: 'representante_telefono', nombre: 'Teléfono del Representante' }
            ];
            
            for (let campo of camposEstudiante) {
                const elemento = document.getElementById(campo.id);
                if (elemento && !elemento.value.trim()) {
                    mostrarError(elemento, `Por favor, completa el campo: ${campo.nombre}`);
                    if (formularioValido) {
                        elemento.focus();
                        formularioValido = false;
                    }
                } else if (elemento) {
                    ocultarError(elemento);
                }
            }
        }

        if (!formularioValido) {
            e.preventDefault();
            return false;
        }
    });

    // Mostrar/ocultar campos de estudiante
    const tipoUsuarioSelect = document.getElementById('tipo_usuario');
    
    // Función para actualizar campos según tipo de usuario
    function actualizarCamposPorTipoUsuario() {
        const camposEstudiante = document.getElementById('campos_estudiante');
        const campoEstadoGeneral = document.getElementById('campo_estado_general');
        const tipoUsuario = tipoUsuarioSelect.value;
        
        console.log('Tipo de usuario seleccionado:', tipoUsuario);
        
        if (tipoUsuario === 'estudiante') {
            camposEstudiante.style.display = 'block';
            campoEstadoGeneral.style.display = 'none';
            configurarCamposRepresentante(true);
            console.log('Mostrando campos de estudiante');
        } else {
            camposEstudiante.style.display = 'none';
            campoEstadoGeneral.style.display = 'block';
            configurarCamposRepresentante(false);
            console.log('Ocultando campos de estudiante');
        }
    }
    
    // Event listener para cambios
    tipoUsuarioSelect.addEventListener('change', actualizarCamposPorTipoUsuario);
    
    // Inicialización: verificar si ya está seleccionado estudiante
    if (tipoUsuarioSelect.value === 'estudiante') {
        document.getElementById('campos_estudiante').style.display = 'block';
        document.getElementById('campo_estado_general').style.display = 'none';
        configurarCamposRepresentante(true);
        console.log('Inicialización: estudiante ya seleccionado, mostrando campos');
    } else {
        console.log('Inicialización: tipo de usuario no es estudiante');
    }

    // Función para configurar campos requeridos del representante
    function configurarCamposRepresentante(requeridos) {
        const representanteCampos = [
            'representante_parentesco',
            'representante_nombre', 
            'representante_apellido',
            'representante_telefono'
        ];
        
        const estadoEstudianteCampo = document.getElementById('estado_estudiante');
        
        representanteCampos.forEach(campo => {
            const elemento = document.getElementById(campo);
            if (elemento) {
                elemento.required = requeridos;
                
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
        
        if (estadoEstudianteCampo) {
            estadoEstudianteCampo.required = requeridos;
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

    // Validación solo letras para nombre y apellido
    function validarSoloLetras(input) {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '');
        });
    }
    
    validarSoloLetras(document.getElementById('nombre'));
    validarSoloLetras(document.getElementById('apellido'));

    // Validación para teléfono
    document.querySelectorAll('[data-phone-input="true"]').forEach(input => {
        input.addEventListener('input', () => {
            const sanitized = input.value.replace(/[^0-9+\-\s\(\)]/g, '');
            if (sanitized !== input.value) {
                input.value = sanitized;
            }
        });
    });

    // Hacer scroll al primer error si existen errores de Laravel
    const hasErrors = document.querySelector('.invalid-feedback-dark') !== null;
    if (hasErrors) {
        setTimeout(() => {
            scrollToFirstError();
        }, 100);
    }
});

// Función para hacer scroll al primer campo con error
function scrollToFirstError() {
    let firstErrorField = document.querySelector('.is-invalid');
    
    if (!firstErrorField) {
        const errorMessage = document.querySelector('.invalid-feedback-dark');
        if (errorMessage) {
            const nearbyInputs = errorMessage.parentNode.querySelectorAll('input, select, textarea');
            if (nearbyInputs.length > 0) {
                firstErrorField = nearbyInputs[0];
            }
        }
    }
    
    if (firstErrorField) {
        const headerHeight = document.querySelector('.header') ? document.querySelector('.header').offsetHeight : 70;
        const fieldPosition = firstErrorField.getBoundingClientRect().top + window.pageYOffset - headerHeight - 20;
        
        window.scrollTo({
            top: fieldPosition,
            behavior: 'smooth'
        });
        
        setTimeout(() => {
            firstErrorField.focus();
            if (firstErrorField.tagName === 'SELECT') {
                firstErrorField.click();
            }
        }, 600);
    }
}
</script>
@endpush
@endsection