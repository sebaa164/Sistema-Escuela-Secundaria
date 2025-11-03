@extends('layouts.app')

@section('title', 'Editar Configuración')
@section('page-title', 'Editar Configuración')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->

    <!-- Card Principal -->
    <div class="card-dark">
        <div class="card-header-dark">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>Editar Configuración
                </h5>
                <span class="badge-neon">{{ $configuracion->clave }}</span>
            </div>
        </div>

        <div class="card-body-dark">
            <form action="{{ route('admin.configuraciones.update', $configuracion) }}" method="POST" id="editConfigForm">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <!-- Información Básica -->
                    <div class="col-12">
                        <div class="section-header">
                            <i class="fas fa-info-circle me-2"></i>
                            <h6>Información Básica</h6>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-dark">
                            <i class="fas fa-key me-2"></i>Clave *
                        </label>
                        <input type="text" 
                               name="clave" 
                               class="form-control-dark @error('clave') is-invalid @enderror" 
                               value="{{ old('clave', $configuracion->clave) }}"
                               required>
                        <small class="form-text-dark">
                            Usar snake_case (minúsculas con guiones bajos)
                        </small>
                        @error('clave')
                            <div class="invalid-feedback-dark">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label-dark">
                            <i class="fas fa-tag me-2"></i>Tipo *
                        </label>
                        <select name="tipo" 
                                id="tipo" 
                                class="form-select-dark @error('tipo') is-invalid @enderror" 
                                required>
                            <option value="string" {{ old('tipo', $configuracion->tipo) == 'string' ? 'selected' : '' }}>Texto</option>
                            <option value="number" {{ old('tipo', $configuracion->tipo) == 'number' ? 'selected' : '' }}>Número</option>
                            <option value="boolean" {{ old('tipo', $configuracion->tipo) == 'boolean' ? 'selected' : '' }}>Booleano (true/false)</option>
                            <option value="json" {{ old('tipo', $configuracion->tipo) == 'json' ? 'selected' : '' }}>JSON</option>
                        </select>
                        @error('tipo')
                            <div class="invalid-feedback-dark">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label-dark">
                            <i class="fas fa-edit me-2"></i>Valor *
                        </label>
                        <div id="valor-container">
                            @if($configuracion->tipo === 'boolean')
                                <select name="valor" id="valor-input" class="form-select-dark @error('valor') is-invalid @enderror" required>
                                    <option value="true" {{ old('valor', $configuracion->valor) == 'true' || old('valor', $configuracion->valor) == '1' ? 'selected' : '' }}>True (Verdadero)</option>
                                    <option value="false" {{ old('valor', $configuracion->valor) == 'false' || old('valor', $configuracion->valor) == '0' ? 'selected' : '' }}>False (Falso)</option>
                                </select>
                            @elseif($configuracion->tipo === 'number')
                                <input type="number" 
                                       name="valor" 
                                       id="valor-input"
                                       class="form-control-dark @error('valor') is-invalid @enderror" 
                                       value="{{ old('valor', $configuracion->valor) }}"
                                       step="any"
                                       required>
                            @elseif($configuracion->tipo === 'json')
                                <textarea name="valor" 
                                          id="valor-input"
                                          class="form-control-dark @error('valor') is-invalid @enderror" 
                                          rows="5"
                                          required>{{ old('valor', $configuracion->valor) }}</textarea>
                            @else
                                <input type="text" 
                                       name="valor" 
                                       id="valor-input"
                                       class="form-control-dark @error('valor') is-invalid @enderror" 
                                       value="{{ old('valor', $configuracion->valor) }}"
                                       required>
                            @endif
                        </div>
                        <small class="form-text-dark" id="valor-help">
                            @switch($configuracion->tipo)
                                @case('string')
                                    Cualquier texto o cadena de caracteres
                                    @break
                                @case('number')
                                    Un valor numérico (puede tener decimales)
                                    @break
                                @case('boolean')
                                    Un valor booleano (verdadero o falso)
                                    @break
                                @case('json')
                                    Un objeto JSON válido
                                    @break
                            @endswitch
                        </small>
                        @error('valor')
                            <div class="invalid-feedback-dark">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label-dark">
                            <i class="fas fa-align-left me-2"></i>Descripción
                        </label>
                        <textarea name="descripcion" 
                                  class="form-control-dark @error('descripcion') is-invalid @enderror" 
                                  rows="3"
                                  maxlength="1000"
                                  placeholder="Describe para qué sirve esta configuración...">{{ old('descripcion', $configuracion->descripcion) }}</textarea>
                        <small class="form-text-dark">
                            Explicación clara del propósito de esta configuración
                        </small>
                        @error('descripcion')
                            <div class="invalid-feedback-dark">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Información Adicional -->
                    <div class="col-12">
                        <div class="info-box mt-3">
                            <h6 class="info-title">
                                <i class="fas fa-info-circle me-2"></i>Información de la Configuración
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <span class="info-label">
                                            <i class="fas fa-clock me-2"></i>Creado:
                                        </span>
                                        <span class="info-value">{{ $configuracion->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <span class="info-label">
                                            <i class="fas fa-sync me-2"></i>Última actualización:
                                        </span>
                                        <span class="info-value">{{ $configuracion->updated_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <span class="info-label">
                                            <i class="fas fa-database me-2"></i>ID:
                                        </span>
                                        <span class="info-value">{{ $configuracion->id }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <span class="info-label">
                                            <i class="fas fa-code me-2"></i>Valor tipificado:
                                        </span>
                                        <span class="info-value">
                                            @if(is_array($configuracion->valor_tipificado))
                                                [Array]
                                            @elseif(is_bool($configuracion->valor_tipificado))
                                                {{ $configuracion->valor_tipificado ? 'true' : 'false' }}
                                            @else
                                                {{ $configuracion->valor_tipificado }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="form-actions mt-5">
                    <button type="submit" class="btn-neon-lg">
                        <i class="fas fa-save me-2"></i>Guardar Cambios
                    </button>
                    <a href="{{ route('admin.configuraciones.index') }}" class="btn-outline-neon">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </a>
                    <button type="button" 
                            class="btn-danger-neon"
                            onclick="confirmarEliminacion()">
                        <i class="fas fa-trash me-2"></i>Eliminar
                    </button>
                </div>
            </form>

            <!-- Formulario de eliminación oculto -->
            <form id="delete-form" 
                  action="{{ route('admin.configuraciones.destroy', $configuracion) }}" 
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

    .badge-neon {
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        color: #0f172a;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.875rem;
        box-shadow: 0 0 10px rgba(0, 212, 255, 0.3);
        font-family: 'Courier New', monospace;
    }

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

    .info-box {
        background: rgba(0, 212, 255, 0.05);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 12px;
        padding: 1.5rem;
    }

    .info-title {
        color: var(--neon-cyan);
        font-weight: 600;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        font-size: 1rem;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .info-label {
        color: #94a3b8;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
    }

    .info-value {
        color: #e2e8f0;
        font-weight: 600;
        font-family: 'Courier New', monospace;
    }

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

    @media (max-width: 768px) {
        .form-actions {
            flex-direction: column;
        }

        .btn-neon-lg, .btn-outline-neon, .btn-danger-neon {
            width: 100%;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tipoSelect = document.getElementById('tipo');
        const valorContainer = document.getElementById('valor-container');
        const valorHelp = document.getElementById('valor-help');
        const currentValue = '{{ old("valor", $configuracion->valor) }}';

        tipoSelect.addEventListener('change', function() {
            const tipo = this.value;
            let inputHTML = '';
            let helpText = 'El valor de la configuración';

            switch(tipo) {
                case 'string':
                    inputHTML = `<input type="text" name="valor" id="valor-input" class="form-control-dark" value="${currentValue}" required>`;
                    helpText = 'Cualquier texto o cadena de caracteres';
                    break;
                case 'number':
                    inputHTML = `<input type="number" name="valor" id="valor-input" class="form-control-dark" value="${currentValue}" step="any" required>`;
                    helpText = 'Un valor numérico (puede tener decimales)';
                    break;
                case 'boolean':
                    const isTrue = currentValue === 'true' || currentValue === '1';
                    inputHTML = `
                        <select name="valor" id="valor-input" class="form-select-dark" required>
                            <option value="true" ${isTrue ? 'selected' : ''}>True (Verdadero)</option>
                            <option value="false" ${!isTrue ? 'selected' : ''}>False (Falso)</option>
                        </select>
                    `;
                    helpText = 'Un valor booleano (verdadero o falso)';
                    break;
                case 'json':
                    inputHTML = `<textarea name="valor" id="valor-input" class="form-control-dark" rows="5" required>${currentValue}</textarea>`;
                    helpText = 'Un objeto JSON válido';
                    break;
            }

            valorContainer.innerHTML = inputHTML;
            valorHelp.textContent = helpText;
        });

        // Validación del formulario
        document.getElementById('editConfigForm').addEventListener('submit', function(e) {
            const tipo = tipoSelect.value;
            const valorInput = document.getElementById('valor-input');
            
            if (!valorInput || !valorInput.value.trim()) {
                e.preventDefault();
                alert('Por favor ingresa un valor');
                return false;
            }

            // Validación adicional para JSON
            if (tipo === 'json') {
                try {
                    JSON.parse(valorInput.value);
                } catch (error) {
                    e.preventDefault();
                    alert('El valor JSON no es válido. Por favor verifica la sintaxis.');
                    return false;
                }
            }
        });
    });

    function confirmarEliminacion() {
        if (confirm('¿Estás seguro de que deseas eliminar esta configuración? Esta acción no se puede deshacer.')) {
            document.getElementById('delete-form').submit();
        }
    }
</script>
@endpush
@endsection