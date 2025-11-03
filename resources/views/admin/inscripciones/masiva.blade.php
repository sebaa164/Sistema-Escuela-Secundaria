@extends('layouts.app')

@section('title', 'Inscripción Masiva')
@section('page-title', 'Inscripción Masiva')

@section('content')
<div class="container-fluid px-3 py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">

            <div class="card-dark">
                <div class="card-header-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>Inscripción Masiva de Estudiantes
                    </h5>
                </div>

                <div class="card-body-dark">
                    @if($errors->any())
                        <div class="alert-danger-dark mb-4">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Por favor corrige los siguientes errores:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    @if(session('warning'))
                        <div class="alert-warning-dark mb-4">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Advertencias:</strong>
                            <p class="mb-0">{{ session('warning') }}</p>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert-success-dark mb-4">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.inscripciones.store-masiva') }}" method="POST" id="massivaForm">
                        @csrf

                        <div class="row g-4">
                            <!-- Sección de Selección de Sección -->
                            <div class="col-12">
                                <div class="section-header">
                                    <i class="fas fa-book me-2"></i>
                                    <h6>Asignación de Curso o aula</h6>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="seccion_id" class="form-label-dark">
                                    <i class="fas fa-layer-group me-2"></i>Sección *
                                </label>
                                <select name="seccion_id" id="seccion_id" class="form-select-dark @error('seccion_id') is-invalid @enderror" required>
                                    <option value="">Asignación de Materia</option>
                                    @foreach($secciones as $seccion)
                                        <option value="{{ $seccion->id }}"
                                                data-cupo-maximo="{{ $seccion->cupo_maximo }}"
                                                data-cupo-actual="{{ $seccion->inscripciones()->where('estado', 'inscrito')->count() }}"
                                                {{ old('seccion_id') == $seccion->id ? 'selected' : '' }}>
                                            {{ $seccion->curso->nombre }} - {{ $seccion->codigo_seccion }} ({{ $seccion->periodo->nombre }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('seccion_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <div id="cupo-info" class="cupo-info-box">
                                    <p class="mb-2">
                                        <i class="fas fa-users me-2"></i>
                                        <span>Cupos Disponibles: </span>
                                        <strong id="cupo-disponible" class="text-warning"></strong>
                                    </p>
                                    <div class="progress">
                                        <div id="cupo-bar" class="cupo-bar" role="progressbar"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sección de Selección de Estudiantes -->
                            <div class="col-12 mt-5">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 section-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-user-check me-2"></i>Selección de Estudiantes
                                    </h6>
                                    <span class="badge-neon" id="selected-count">0 seleccionados</span>
                                </div>
                            </div>

                            <!-- Controles de búsqueda y selección -->
                            <div class="col-12 mb-3">
                                <div class="search-controls">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                        <label class="form-check-label text-neon" for="selectAll">
                                            <i class="fas fa-check-double me-1"></i>Seleccionar Todos
                                        </label>
                                    </div>
                                    <div class="search-box">
                                        <div class="input-group-dark">
                                            <span class="input-icon">
                                                <i class="fas fa-search"></i>
                                            </span>
                                            <input type="text" 
                                                   id="searchEstudiante" 
                                                   class="form-control-dark" 
                                                   placeholder="🔍 Buscar por nombre, apellido o email...">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tabla de estudiantes -->
                            <div class="col-12">
                                <div class="students-table-wrapper">
                                    <table class="table-dark">
                                        <thead>
                                            <tr>
                                                <th style="width: 60px; text-align: center;">
                                                    <i class="fas fa-check-square"></i>
                                                </th>
                                                <th>Estudiante</th>
                                                <th>Email</th>
                                                <th class="text-center">Código</th>
                                            </tr>
                                        </thead>
                                        <tbody id="estudiantesTableBody">
                                            @forelse($estudiantes as $estudiante)
                                                <tr class="estudiante-row" 
                                                    data-name="{{ strtolower($estudiante->nombre_completo) }}" 
                                                    data-email="{{ strtolower($estudiante->email) }}">
                                                    <td class="text-center">
                                                        <input class="form-check-input estudiante-checkbox" 
                                                               type="checkbox" 
                                                               name="estudiantes[]" 
                                                               value="{{ $estudiante->id }}"
                                                               id="est_{{ $estudiante->id }}">
                                                    </td>
                                                    <td>
                                                        <label for="est_{{ $estudiante->id }}" class="student-label">
                                                            <div class="course-info">
                                                                <div class="course-icon bg-gradient-info">
                                                                    <i class="fas fa-user"></i>
                                                                </div>
                                                                <strong>{{ $estudiante->nombre_completo }}</strong>
                                                            </div>
                                                        </label>
                                                    </td>
                                                    <td>
                                                        <label for="est_{{ $estudiante->id }}" class="student-label">
                                                            {{ $estudiante->email }}
                                                        </label>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge-neon">{{ $estudiante->codigo }}</span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center py-5">
                                                        <div class="empty-state">
                                                            <i class="fas fa-user-graduate fa-3x mb-3"></i>
                                                            <h5>No hay estudiantes disponibles</h5>
                                                            <p>Registra estudiantes para poder inscribirlos</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Mensaje cuando no hay resultados de búsqueda -->
                                <div id="no-results" class="empty-state py-5" style="display: none;">
                                    <i class="fas fa-search fa-3x mb-3"></i>
                                    <h5>No se encontraron estudiantes</h5>
                                    <p>Intenta con otro término de búsqueda</p>
                                </div>
                            </div>

                            <!-- Botones de acción -->
                            <div class="col-12 mt-5">
                                <div class="form-actions">
                                    <a href="{{ route('admin.inscripciones.index') }}" class="btn-outline-neon">
                                        <i class="fas fa-arrow-left me-2"></i>Cancelar
                                    </a>
                                    <button type="submit" class="btn-neon-lg" id="submitBtn">
                                        <i class="fas fa-users me-2"></i>Inscribir Seleccionados
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    :root {
        --neon-cyan: #00d4ff;
        --neon-blue: #0ea5e9;
        --dark-bg: #0f172a;
        --dark-card: #1e293b;
        --text-color: #e2e8f0;
        --muted-text: #94a3b8;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
    }
    
    .container-fluid {
        max-width: 100%;
        overflow-x: hidden;
    }
    
    .breadcrumb-dark {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(30, 41, 59, 0.9) 100%);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 10px;
        padding: 1rem 1.5rem;
        margin: 0;
        display: flex;
        flex-wrap: wrap;
        list-style: none;
    }

    .breadcrumb-dark .breadcrumb-item {
        color: var(--muted-text);
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
        color: var(--text-color);
    }

    .breadcrumb-dark .breadcrumb-item + .breadcrumb-item::before {
        content: "›";
        color: rgba(0, 212, 255, 0.5);
        padding-right: 0.5rem;
        padding-left: 0.5rem;
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
        color: var(--text-color);
    }

    .card-header-dark h5 {
        color: var(--neon-cyan);
        margin: 0;
    }

    .card-body-dark {
        padding: 2rem;
        color: var(--text-color);
    }
    
    .form-label-dark {
        color: var(--neon-cyan);
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-select-dark {
        background-color: var(--dark-card);
        border: 1px solid rgba(0, 212, 255, 0.3);
        color: var(--text-color);
        padding: 0.75rem 1rem;
        border-radius: 8px;
        transition: all 0.3s ease;
        width: 100%;
    }

    .form-select-dark:focus {
        background-color: var(--dark-bg);
        border-color: var(--neon-cyan);
        box-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
        color: var(--text-color);
        outline: none;
    }

    .form-select-dark option {
        background-color: var(--dark-card);
        color: var(--text-color);
    }
    
    .section-header {
        border-bottom: 2px solid var(--neon-cyan);
        padding-bottom: 0.5rem;
        margin-bottom: 1.5rem;
        color: var(--neon-cyan);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-header h6 {
        margin: 0;
    }

    .alert-danger-dark {
        background-color: rgba(239, 68, 68, 0.2);
        border: 1px solid #ef4444;
        color: #ef4444;
        padding: 1rem;
        border-radius: 8px;
    }

    .alert-warning-dark {
        background-color: rgba(245, 158, 11, 0.2);
        border: 1px solid #f59e0b;
        color: #f59e0b;
        padding: 1rem;
        border-radius: 8px;
    }

    .alert-success-dark {
        background-color: rgba(16, 185, 129, 0.2);
        border: 1px solid #10b981;
        color: #10b981;
        padding: 1rem;
        border-radius: 8px;
    }
    
    .cupo-info-box {
        display: none;
        padding: 1rem;
        border-radius: 8px;
        border: 1px solid var(--neon-cyan);
        background-color: rgba(0, 212, 255, 0.05);
        height: fit-content;
    }
    
    .progress {
        background-color: var(--dark-bg);
        border: 1px solid rgba(0, 212, 255, 0.3);
        height: 10px;
        border-radius: 5px;
        overflow: hidden;
    }

    .cupo-bar {
        background: linear-gradient(90deg, #10b981 0%, #0ea5e9 100%);
        transition: width 0.6s ease;
        height: 100%;
        width: 0%;
    }

    .cupo-bar.warning {
        background: linear-gradient(90deg, #f59e0b 0%, #f97316 100%);
    }

    .cupo-bar.full {
        background: linear-gradient(90deg, #ef4444 0%, #dc2626 100%);
    }

    .search-controls {
        display: flex;
        gap: 1.5rem;
        align-items: center;
        flex-wrap: wrap;
        padding: 1rem;
        background: rgba(0, 212, 255, 0.05);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
    }

    .form-check-label {
        cursor: pointer;
        user-select: none;
    }

    .text-neon {
        color: var(--neon-cyan);
        font-weight: 600;
    }

    .search-box {
        flex: 1;
        min-width: 250px;
    }

    .input-group-dark {
        position: relative;
        width: 100%;
    }

    .input-icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--neon-cyan);
        z-index: 10;
    }

    .form-control-dark {
        background-color: var(--dark-card);
        border: 1px solid rgba(0, 212, 255, 0.3);
        color: var(--text-color);
        padding: 0.75rem 1rem 0.75rem 2.5rem;
        border-radius: 8px;
        transition: all 0.3s ease;
        width: 100%;
    }

    .form-control-dark:focus {
        background-color: var(--dark-bg);
        border-color: var(--neon-cyan);
        box-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
        color: var(--text-color);
        outline: none;
    }

    .form-control-dark::placeholder {
        color: var(--muted-text);
        opacity: 0.7;
    }

    .students-table-wrapper {
        max-height: 500px;
        overflow-y: auto;
        overflow-x: auto;
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
        background: rgba(15, 23, 42, 0.5);
    }

    /* Scrollbar personalizado */
    .students-table-wrapper::-webkit-scrollbar {
        width: 10px;
        height: 10px;
    }

    .students-table-wrapper::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.2);
        border-radius: 5px;
    }

    .students-table-wrapper::-webkit-scrollbar-thumb {
        background: var(--neon-cyan);
        border-radius: 5px;
    }

    .students-table-wrapper::-webkit-scrollbar-thumb:hover {
        background: var(--neon-blue);
    }

    .table-dark {
        width: 100%;
        color: var(--text-color);
        margin-bottom: 0;
    }

    .table-dark thead th {
        background: linear-gradient(135deg, rgba(14, 165, 233, 0.3) 0%, rgba(0, 212, 255, 0.2) 100%);
        color: var(--neon-cyan);
        text-transform: uppercase;
        font-size: 0.875rem;
        font-weight: 600;
        padding: 1rem;
        border: none;
        position: sticky;
        top: 0;
        z-index: 10;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .table-dark tbody tr {
        border-bottom: 1px solid rgba(0, 212, 255, 0.1);
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .table-dark tbody tr:hover {
        background: rgba(0, 212, 255, 0.08);
        transform: scale(1.01);
    }

    .table-dark tbody tr.row-hidden {
        display: none;
    }

    .table-dark tbody td {
        padding: 1rem;
        vertical-align: middle;
        color: var(--text-color);
    }

    .student-label {
        cursor: pointer;
        margin: 0;
        display: block;
        user-select: none;
    }

    .course-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .course-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        color: white;
        flex-shrink: 0;
    }

    .bg-gradient-info {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
        box-shadow: 0 0 15px rgba(6, 182, 212, 0.3);
    }
    
    .badge-neon {
        background: rgba(0, 212, 255, 0.1);
        color: var(--neon-cyan);
        padding: 0.4em 0.8em;
        border-radius: 5px;
        font-weight: 600;
        border: 1px solid rgba(0, 212, 255, 0.3);
        display: inline-block;
        font-size: 0.85rem;
    }

    .form-check-input {
        background-color: var(--dark-card);
        border: 2px solid rgba(0, 212, 255, 0.5);
        width: 1.25rem;
        height: 1.25rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .form-check-input:checked {
        background-color: var(--neon-cyan);
        border-color: var(--neon-cyan);
        box-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .form-check-input:focus {
        box-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
        outline: none;
    }

    .btn-neon-lg {
        background: linear-gradient(135deg, #0ea5e9 0%, #00d4ff 100%);
        border: none;
        color: var(--dark-bg);
        font-weight: 700;
        padding: 0.875rem 2rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
        font-size: 1rem;
        text-decoration: none;
        display: inline-block;
        cursor: pointer;
    }

    .btn-neon-lg:hover {
        color: var(--dark-bg);
        box-shadow: 0 0 30px rgba(0, 212, 255, 0.6);
        transform: translateY(-2px);
    }

    .btn-neon-lg:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }

    .btn-outline-neon {
        background: transparent;
        border: 2px solid var(--neon-cyan);
        color: var(--neon-cyan);
        font-weight: 600;
        padding: 0.875rem 2rem;
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

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        padding-top: 1rem;
        border-top: 1px solid rgba(0, 212, 255, 0.1);
    }

    .empty-state {
        color: var(--muted-text);
        text-align: center;
    }

    .empty-state i {
        color: rgba(0, 212, 255, 0.3);
    }

    .empty-state h5 {
        color: var(--text-color);
        margin-bottom: 0.5rem;
    }

    .invalid-feedback {
        color: var(--danger-color);
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: block;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .card-body-dark {
            padding: 1.5rem 1rem;
        }

        .search-controls {
            flex-direction: column;
            align-items: stretch;
        }

        .search-box {
            width: 100%;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn-neon-lg,
        .btn-outline-neon {
            width: 100%;
            text-align: center;
        }

        .students-table-wrapper {
            max-height: 400px;
        }

        .course-info {
            flex-direction: row;
            gap: 0.75rem;
        }

        .course-icon {
            width: 35px;
            height: 35px;
            font-size: 1rem;
        }
    }

    /* Animación para filas */
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

    .estudiante-row {
        animation: fadeIn 0.3s ease;
    }
</style>
@endpush

@push('scripts')
<script>
    // Función para actualizar el conteo de estudiantes seleccionados
    function updateSelectedCount() {
        const count = document.querySelectorAll('.estudiante-checkbox:checked').length;
        const badge = document.getElementById('selected-count');
        badge.textContent = `${count} seleccionado${count !== 1 ? 's' : ''}`;
        
        // Habilitar/deshabilitar botón de submit
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = count === 0;
    }

    // Toggle de selección de todos
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.estudiante-checkbox');
        checkboxes.forEach(checkbox => {
            // Solo seleccionar/deseleccionar si la fila es visible
            const row = checkbox.closest('tr');
            if (!row.classList.contains('row-hidden')) {
                checkbox.checked = this.checked;
            }
        });
        updateSelectedCount();
    });

    // Event listener para actualizar el conteo en cualquier cambio de checkbox
    document.querySelectorAll('.estudiante-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectedCount();
            
            // Actualizar estado del "Seleccionar Todos"
            const allCheckboxes = document.querySelectorAll('.estudiante-checkbox:not(.row-hidden .estudiante-checkbox)');
            const checkedCheckboxes = document.querySelectorAll('.estudiante-checkbox:checked:not(.row-hidden .estudiante-checkbox)');
            document.getElementById('selectAll').checked = allCheckboxes.length === checkedCheckboxes.length && allCheckboxes.length > 0;
        });
    });

    // Click en la fila para seleccionar/deseleccionar
    document.querySelectorAll('.estudiante-row').forEach(row => {
        row.addEventListener('click', function(e) {
            // No hacer nada si se clickeó directamente el checkbox
            if (e.target.classList.contains('estudiante-checkbox')) {
                return;
            }
            
            const checkbox = this.querySelector('.estudiante-checkbox');
            if (checkbox) {
                checkbox.checked = !checkbox.checked;
                checkbox.dispatchEvent(new Event('change'));
            }
        });
    });

    // Búsqueda en la tabla
    document.getElementById('searchEstudiante').addEventListener('input', function() {
        const searchValue = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#estudiantesTableBody .estudiante-row');
        const noResults = document.getElementById('no-results');
        const tableWrapper = document.querySelector('.students-table-wrapper');
        let visibleCount = 0;
        
        rows.forEach(row => {
            const name = row.getAttribute('data-name');
            const email = row.getAttribute('data-email');
            
            if (name.includes(searchValue) || email.includes(searchValue)) {
                row.classList.remove('row-hidden');
                row.style.display = '';
                visibleCount++;
            } else {
                row.classList.add('row-hidden');
                row.style.display = 'none';
                // Desmarcar checkbox si la fila se oculta
                const checkbox = row.querySelector('.estudiante-checkbox');
                if (checkbox && checkbox.checked) {
                    checkbox.checked = false;
                }
            }
        });
        
        // Mostrar/ocultar mensaje de "no hay resultados"
        if (visibleCount === 0 && searchValue !== '') {
            tableWrapper.style.display = 'none';
            noResults.style.display = 'block';
        } else {
            tableWrapper.style.display = 'block';
            noResults.style.display = 'none';
        }
        
        updateSelectedCount();
        
        // Actualizar estado del "Seleccionar Todos"
        const selectAllCheckbox = document.getElementById('selectAll');
        selectAllCheckbox.checked = false;
    });
    
    // Lógica de cupo
    document.getElementById('seccion_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const cupoMaximo = parseInt(selectedOption.getAttribute('data-cupo-maximo'));
        const cupoActual = parseInt(selectedOption.getAttribute('data-cupo-actual'));
        const cupoInfo = document.getElementById('cupo-info');
        const cupoDisponibleEl = document.getElementById('cupo-disponible');
        const cupoBar = document.getElementById('cupo-bar');
        
        if (cupoMaximo > 0 && selectedOption.value) {
            const cupoDisponible = cupoMaximo - cupoActual;
            const porcentaje = (cupoActual / cupoMaximo) * 100;

            cupoDisponibleEl.textContent = `${cupoDisponible} / ${cupoMaximo}`;
            
            cupoDisponibleEl.classList.remove('text-success', 'text-warning', 'text-danger');
            
            let colorClass = 'text-success';
            if (porcentaje >= 80 && porcentaje < 100) {
                colorClass = 'text-warning';
            } else if (porcentaje >= 100) {
                colorClass = 'text-danger';
            }
            cupoDisponibleEl.classList.add(colorClass);

            cupoBar.style.width = `${Math.min(porcentaje, 100)}%`;
            
            cupoBar.className = 'cupo-bar';
            if (porcentaje >= 100) {
                cupoBar.classList.add('full');
            } else if (porcentaje >= 80) {
                cupoBar.classList.add('warning');
            }
            
            cupoInfo.style.display = 'block';
        } else {
            cupoInfo.style.display = 'none';
        }
    });

    // Validación final del formulario
    document.getElementById('massivaForm').addEventListener('submit', function(e) {
        const seccionId = document.getElementById('seccion_id').value;
        const checkboxes = document.querySelectorAll('.estudiante-checkbox:checked');
        
        if (!seccionId) {
            e.preventDefault();
            alert('⚠️ Por favor selecciona una sección');
            return false;
        }

        if (checkboxes.length === 0) {
            e.preventDefault();
            alert('⚠️ Por favor selecciona al menos un estudiante');
            return false;
        }

        // Verificar cupo disponible
        const selectedOption = document.getElementById('seccion_id').options[document.getElementById('seccion_id').selectedIndex];
        const cupoActual = parseInt(selectedOption.getAttribute('data-cupo-actual'));
        const cupoMaximo = parseInt(selectedOption.getAttribute('data-cupo-maximo'));
        const cupoDisponible = cupoMaximo - cupoActual;
        
        if (checkboxes.length > cupoDisponible) {
            e.preventDefault();
            alert(`⚠️ Solo hay ${cupoDisponible} cupos disponibles.\nHas seleccionado ${checkboxes.length} estudiantes.`);
            return false;
        }

        // Confirmar inscripción
        if (!confirm(`✅ ¿Estás seguro de inscribir ${checkboxes.length} estudiante(s) en esta sección?`)) {
            e.preventDefault();
            return false;
        }

        // Deshabilitar botón para evitar doble envío
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Inscribiendo...';
    });

    // Inicializar contador y estado del botón
    updateSelectedCount();
    
    // Trigger change en sección si ya hay una seleccionada (por old())
    window.addEventListener('load', function() {
        const seccionSelect = document.getElementById('seccion_id');
        if (seccionSelect.value) {
            seccionSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endpush
@endsection