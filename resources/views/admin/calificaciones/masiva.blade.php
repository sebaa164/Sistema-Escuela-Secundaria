@extends('layouts.app')

@section('title', 'Calificación Masiva')
@section('page-title', 'Calificación Masiva')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <!-- Breadcrumb -->

            <!-- Card de Selección de Evaluación -->
            <div class="card-dark mb-4">
                <div class="card-header-dark">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-clipboard-list me-2"></i>Seleccionar Evaluación
                        </h5>
                        <a href="{{ route('admin.calificaciones.index') }}" class="btn-outline-neon-sm">
                            <i class="fas fa-arrow-left me-2"></i>Volver
                        </a>
                    </div>
                </div>
                <div class="card-body-dark">
                    <form method="GET" action="{{ route('admin.calificaciones.masiva') }}">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-10">
                                <label class="form-label-dark">
                                    <i class="fas fa-file-alt me-2"></i>Seleccione la Evaluación *
                                </label>
                                <select name="evaluacion_id" 
                                        class="form-select-dark" 
                                        required
                                        onchange="this.form.submit()">
                                    <option value="">-- Seleccione una evaluación --</option>
                                    @foreach($evaluaciones as $eval)
                                        <option value="{{ $eval->id }}" 
                                                {{ request('evaluacion_id') == $eval->id ? 'selected' : '' }}>
                                            {{ $eval->seccion->curso->codigo_curso }} - {{ $eval->nombre }}
                                            ({{ $eval->fecha_evaluacion->format('d/m/Y') }}) - 
                                            Sección: {{ $eval->seccion->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
    <button type="submit" class="btn-generar-reporte w-100">
        <i class="fas fa-sync-alt me-2"></i>Autogenerar
    </button>
</div>
                        </div>
                    </form>

                    @if($evaluacion)
                        <div class="evaluation-info-banner mt-4">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="info-item">
                                        <i class="fas fa-book"></i>
                                        <div>
                                            <label>Curso</label>
                                            <p>{{ $evaluacion->seccion->curso->nombre }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-item">
                                        <i class="fas fa-users"></i>
                                        <div>
                                            <label>Estudiantes</label>
                                            <p>{{ $estudiantes->count() }} inscritos</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-item">
                                        <i class="fas fa-percentage"></i>
                                        <div>
                                            <label>Porcentaje</label>
                                            <p>{{ $evaluacion->porcentaje }}%</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-item">
                                        <i class="fas fa-calendar"></i>
                                        <div>
                                            <label>Fecha Evaluación</label>
                                            <p>{{ $evaluacion->fecha_evaluacion->format('d/m/Y') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            @if($evaluacion && $estudiantes->isNotEmpty())
                <!-- Formulario de Calificación Masiva -->
                <div class="card-dark">
                    <div class="card-header-dark">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-users me-2"></i>Calificar Estudiantes
                            </h5>
                            <span class="badge-neon">
                                <i class="fas fa-clipboard-check me-2"></i>
                                {{ $estudiantes->where('calificaciones', '!=', '[]')->count() }} de {{ $estudiantes->count() }} calificados
                            </span>
                        </div>
                    </div>

                    <div class="card-body-dark">
                        <form action="{{ route('admin.calificaciones.procesarMasiva') }}" 
                              method="POST" 
                              id="massiveGradeForm">
                            @csrf
                            <input type="hidden" name="evaluacion_id" value="{{ $evaluacion->id }}">

                            <!-- Acciones Rápidas -->
                            <div class="quick-actions mb-4">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label-dark">
                                            <i class="fas fa-magic me-2"></i>Aplicar Nota a Todos
                                        </label>
                                        <div class="input-group">
                                            <input type="number" 
                                                   id="notaGlobal" 
                                                   class="form-control-dark" 
                                                   placeholder="Nota"
                                                   min="0"
                                                   max="100"
                                                   step="0.01">
                                            <button type="button" 
                                                    class="btn-action-primary"
                                                    onclick="aplicarNotaGlobal()">
                                                Aplicar
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label-dark">
                                            <i class="fas fa-comment me-2"></i>Comentario Global
                                        </label>
                                        <div class="input-group">
                                            <input type="text" 
                                                   id="comentarioGlobal" 
                                                   class="form-control-dark" 
                                                   placeholder="Comentario para todos">
                                            <button type="button" 
                                                    class="btn-action-primary"
                                                    onclick="aplicarComentarioGlobal()">
                                                Aplicar
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label-dark">
                                            <i class="fas fa-filter me-2"></i>Filtrar Estudiantes
                                        </label>
                                        <select class="form-select-dark" onchange="filtrarEstudiantes(this.value)">
                                            <option value="todos">Todos</option>
                                            <option value="sin_calificar">Sin Calificar</option>
                                            <option value="calificados">Calificados</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabla de Estudiantes -->
                            <div class="table-responsive">
                                <table class="table-dark" id="studentsTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 5%">
                                                <div class="form-check">
                                                    <input type="checkbox" 
                                                           class="form-check-input" 
                                                           id="selectAll"
                                                           onchange="toggleSelectAll(this)">
                                                </div>
                                            </th>
                                            <th style="width: 30%">Estudiante</th>
                                            <th style="width: 20%">Nota</th>
                                            <th style="width: 35%">Comentarios</th>
                                            <th style="width: 10%">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($estudiantes as $index => $inscripcion)
                                            @php
                                                $calificacionExistente = $inscripcion->calificaciones->first();
                                                $yaCalificado = $calificacionExistente !== null;
                                            @endphp
                                            <tr class="student-row" data-estado="{{ $yaCalificado ? 'calificado' : 'sin_calificar' }}">
                                                <td>
                                                    <div class="form-check">
                                                        <input type="checkbox" 
                                                               class="form-check-input student-checkbox" 
                                                               name="estudiantes_seleccionados[]"
                                                               value="{{ $inscripcion->estudiante->id }}">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="student-info-compact">
                                                        <div class="student-avatar-small">
                                                            <i class="fas fa-user-graduate"></i>
                                                        </div>
                                                        <div>
                                                            <strong>{{ $inscripcion->estudiante->nombre_completo }}</strong>
                                                            <br><small class="text-muted">{{ $inscripcion->estudiante->email }}</small>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" 
                                                           name="calificaciones[{{ $index }}][estudiante_id]" 
                                                           value="{{ $inscripcion->estudiante->id }}">
                                                </td>
                                                <td>
                                                    <input type="number" 
                                                           name="calificaciones[{{ $index }}][nota]" 
                                                           class="form-control-dark nota-input"
                                                           value="{{ $yaCalificado ? $calificacionExistente->nota : '' }}"
                                                           min="0"
                                                           max="100"
                                                           step="0.01"
                                                           placeholder="0.00"
                                                           oninput="validarNota(this)">
                                                    <small class="nota-status" style="display: none;"></small>
                                                </td>
                                                <td>
                                                    <textarea name="calificaciones[{{ $index }}][comentarios]" 
                                                              class="form-control-dark comentario-input"
                                                              rows="2"
                                                              maxlength="500"
                                                              placeholder="Comentarios opcionales...">{{ $yaCalificado ? $calificacionExistente->comentarios : '' }}</textarea>
                                                </td>
                                                <td class="text-center">
                                                    @if($yaCalificado)
                                                        <span class="status-badge status-graded">
                                                            <i class="fas fa-check"></i> Calificado
                                                        </span>
                                                    @else
                                                        <span class="status-badge status-pending">
                                                            <i class="fas fa-clock"></i> Pendiente
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Botones de Acción -->
                            <div class="form-actions mt-4">
                                <button type="submit" class="btn-neon-lg">
                                    <i class="fas fa-save me-2"></i>Guardar Todas las Calificaciones
                                </button>
                                <button type="button" class="btn-outline-neon" onclick="guardarSeleccionados()">
                                    <i class="fas fa-check-square me-2"></i>Guardar Solo Seleccionados
                                </button>
                                <a href="{{ route('admin.calificaciones.index') }}" class="btn-outline-neon">
                                    <i class="fas fa-times me-2"></i>Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

            @elseif($evaluacion && $estudiantes->isEmpty())
                <div class="card-dark">
                    <div class="card-body-dark">
                        <div class="empty-state">
                            <i class="fas fa-user-slash fa-3x mb-3"></i>
                            <h5>No hay estudiantes para calificar</h5>
                            <p>Todos los estudiantes de esta evaluación ya han sido calificados o no hay estudiantes inscritos en la sección.</p>
                            <a href="{{ route('admin.calificaciones.index') }}" class="btn-neon mt-3">
                                <i class="fas fa-arrow-left me-2"></i>Volver a Calificaciones
                            </a>
                        </div>
                    </div>
                </div>
            @endif
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
        width: 100%;
        max-width: 100%;
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
        overflow-x: auto;
    }

    .badge-neon {
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        color: #0f172a;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.875rem;
        box-shadow: 0 0 10px rgba(0, 212, 255, 0.3);
    }

    /* Form Controls */
    .form-label-dark {
        color: #e2e8f0;
        font-weight: 500;
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-label-dark i {
        color: var(--neon-cyan);
    }

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

    /* Evaluation Info Banner */
    .evaluation-info-banner {
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.1) 0%, rgba(14, 165, 233, 0.05) 100%);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 12px;
        padding: 1.5rem;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .info-item i {
        font-size: 2rem;
        color: var(--neon-cyan);
        text-shadow: 0 0 15px rgba(0, 212, 255, 0.5);
    }

    .info-item label {
        color: #64748b;
        font-size: 0.75rem;
        text-transform: uppercase;
        margin-bottom: 0.25rem;
        display: block;
    }

    .info-item p {
        color: #e2e8f0;
        font-weight: 600;
        margin: 0;
        font-size: 1.1rem;
    }

    /* Quick Actions */
    .quick-actions {
        background: rgba(0, 212, 255, 0.05);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 12px;
        padding: 1.5rem;
    }

    .input-group {
        display: flex;
        gap: 0.5rem;
    }

    .btn-action-primary {
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        border: none;
        color: #0f172a;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
        cursor: pointer;
        white-space: nowrap;
    }

    .btn-action-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 25px rgba(0, 212, 255, 0.5);
    }

    /* Table Dark */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table-dark {
        width: 100%;
        color: #e2e8f0;
        min-width: 900px;
        table-layout: auto;
    }

    .table-dark thead th {
        background: linear-gradient(135deg, rgba(14, 165, 233, 0.3) 0%, rgba(0, 212, 255, 0.2) 100%);
        color: var(--neon-cyan);
        text-transform: uppercase;
        font-size: 0.875rem;
        font-weight: 600;
        padding: 1rem;
        border: none;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .table-dark tbody tr {
        border-bottom: 1px solid rgba(0, 212, 255, 0.1);
        transition: all 0.3s ease;
    }

    .table-dark tbody tr:hover {
        background: rgba(0, 212, 255, 0.05);
    }

    .table-dark tbody td {
        padding: 1rem;
        vertical-align: middle;
    }

    /* Student Info Compact */
    .student-info-compact {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .student-avatar-small {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        color: #0f172a;
        box-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
        flex-shrink: 0;
    }

    /* Form Check */
    .form-check-input {
        width: 1.25rem;
        height: 1.25rem;
        border: 2px solid var(--neon-cyan);
        background: rgba(15, 23, 42, 0.8);
        cursor: pointer;
    }

    .form-check-input:checked {
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        border-color: var(--neon-cyan);
    }

    .form-check-input:focus {
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
    }

    /* Nota Input */
    .nota-input {
        font-size: 1.1rem;
        font-weight: 600;
        text-align: center;
        min-width: 100px;
    }

    .nota-status {
        display: block;
        margin-top: 0.25rem;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .nota-status.aprobado {
        color: #10b981;
    }

    .nota-status.reprobado {
        color: #ef4444;
    }

    /* Textarea */
    .comentario-input {
        min-width: 200px;
        resize: vertical;
    }

    .text-muted {
        color: #94a3b8 !important;
    }

    /* Status Badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-pending {
        background: rgba(245, 158, 11, 0.2);
        color: #f59e0b;
        border: 1px solid #f59e0b;
    }

    .status-graded {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 1px solid #10b981;
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
    }

    .btn-neon-lg:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 30px rgba(0, 212, 255, 0.6);
    }

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

    /* Empty State */
    .empty-state {
        text-align: center;
        color: #94a3b8;
        padding: 3rem;
    }

    .empty-state i {
        color: rgba(0, 212, 255, 0.3);
    }

    .empty-state h5 {
        color: #e2e8f0;
        margin-bottom: 0.5rem;
    }

    /* Container */
    .container-fluid {
        max-width: 100%;
        overflow-x: hidden;
    }

    /* Responsive */
    /* Responsive */
    @media (max-width: 768px) {
        .form-actions {
            flex-direction: column;
        }

        .btn-neon-lg,
        .btn-outline-neon,
        .btn-neon {
            width: 100%;
        }

        .table-dark {
            font-size: 0.875rem;
        }

        .student-info-compact {
            flex-direction: column;
            text-align: center;
        }

        .evaluation-info-banner .row,
        .quick-actions .row {
            gap: 1rem;
        }
    }

    /* Botón pequeño para header */
    .btn-outline-neon-sm {
        background: transparent;
        border: 2px solid var(--neon-cyan);
        color: var(--neon-cyan);
        font-weight: 600;
        padding: 0.5rem 1rem;
        border-radius: 10px;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        font-size: 0.875rem;
        white-space: nowrap;
    }

    .btn-outline-neon-sm:hover {
        background: rgba(0, 212, 255, 0.1);
        transform: translateY(-2px);
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
        color: var(--neon-cyan);
    }
    /* Botón Autogenerar - Color Cyan Original ✨ */
.btn-generar-reporte {
    background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
    border: none;
    color: #0f172a;
    font-weight: 700;
    font-size: 0.95rem;
    padding: 0.85rem 1rem;
    border-radius: 10px;
    transition: all 0.3s ease;
    box-shadow: 0 0 20px rgba(0, 212, 255, 0.4);
    text-decoration: none;
    display: inline-block;
    position: relative;
    overflow: hidden;
    cursor: pointer;
    text-transform: none;
    white-space: nowrap;
}

.btn-generar-reporte::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s ease;
}

.btn-generar-reporte:hover {
    transform: translateY(-2px);
    box-shadow: 0 0 35px rgba(0, 212, 255, 0.6);
    background: linear-gradient(135deg, #0ea5e9 0%, #00d4ff 100%);
}

.btn-generar-reporte:hover::before {
    left: 100%;
}

.btn-generar-reporte:active {
    transform: translateY(-1px);
}

.btn-generar-reporte i {
    font-size: 1rem;
}
</style> 
@endpush

@push('scripts')
<script>
    // Seleccionar todos los checkboxes
    function toggleSelectAll(checkbox) {
        const checkboxes = document.querySelectorAll('.student-checkbox');
        checkboxes.forEach(cb => cb.checked = checkbox.checked);
    }

    // Aplicar nota global
    function aplicarNotaGlobal() {
        const nota = document.getElementById('notaGlobal').value;
        if (nota === '' || nota < 0 || nota > 100) {
            alert('Por favor ingrese una nota válida entre 0 y 100');
            return;
        }

        const inputs = document.querySelectorAll('.nota-input');
        inputs.forEach(input => {
            input.value = nota;
            validarNota(input);
        });

        alert(`Nota ${nota} aplicada a todos los estudiantes`);
        document.getElementById('notaGlobal').value = '';
    }

    // Aplicar comentario global
    function aplicarComentarioGlobal() {
        const comentario = document.getElementById('comentarioGlobal').value;
        if (comentario === '') {
            alert('Por favor ingrese un comentario');
            return;
        }

        const textareas = document.querySelectorAll('.comentario-input');
        textareas.forEach(textarea => {
            textarea.value = comentario;
        });

        alert('Comentario aplicado a todos los estudiantes');
        document.getElementById('comentarioGlobal').value = '';
    }

    // Validar nota y mostrar estado - NOTA MÍNIMA 60
    function validarNota(input) {
        const nota = parseFloat(input.value);
        const statusElement = input.parentElement.querySelector('.nota-status');
        
        if (isNaN(nota) || input.value === '') {
            if (statusElement) statusElement.style.display = 'none';
            input.style.borderColor = 'rgba(0, 212, 255, 0.3)';
            return;
        }

        if (nota < 0 || nota > 100) {
            if (statusElement) {
                statusElement.style.display = 'block';
                statusElement.textContent = 'Nota inválida';
                statusElement.className = 'nota-status reprobado';
            }
            input.style.borderColor = '#ef4444';
            return;
        }

        if (statusElement) {
            statusElement.style.display = 'block';
            // Nota mínima de aprobación: 60
            if (nota >= 60) {
                statusElement.textContent = 'Aprobado';
                statusElement.className = 'nota-status aprobado';
                input.style.borderColor = '#10b981';
            } else {
                statusElement.textContent = 'Reprobado';
                statusElement.className = 'nota-status reprobado';
                input.style.borderColor = '#ef4444';
            }
        }
    }

    // Filtrar estudiantes
    function filtrarEstudiantes(filtro) {
        const rows = document.querySelectorAll('.student-row');
        
        rows.forEach(row => {
            const estado = row.getAttribute('data-estado');
            
            if (filtro === 'todos') {
                row.style.display = '';
            } else if (filtro === 'sin_calificar' && estado === 'sin_calificar') {
                row.style.display = '';
            } else if (filtro === 'calificados' && estado === 'calificado') {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // Guardar solo seleccionados
    function guardarSeleccionados() {
        const checkboxes = document.querySelectorAll('.student-checkbox:checked');
        
        if (checkboxes.length === 0) {
            alert('Por favor seleccione al menos un estudiante');
            return;
        }

        // Deshabilitar inputs de estudiantes no seleccionados
        const allCheckboxes = document.querySelectorAll('.student-checkbox');
        allCheckboxes.forEach(checkbox => {
            const row = checkbox.closest('tr');
            const inputs = row.querySelectorAll('input[type="number"], textarea');
            
            if (!checkbox.checked) {
                inputs.forEach(input => {
                    input.disabled = true;
                });
            }
        });

        // Enviar formulario
        document.getElementById('massiveGradeForm').submit();
    }

    // Validación al enviar
    document.getElementById('massiveGradeForm').addEventListener('submit', function(e) {
        const notaInputs = document.querySelectorAll('.nota-input');
        let hasError = false;
        let notasVacias = 0;

        notaInputs.forEach(input => {
            const nota = parseFloat(input.value);
            
            if (input.value !== '' && (isNaN(nota) || nota < 0 || nota > 100)) {
                hasError = true;
            }
            
            if (input.value === '') {
                notasVacias++;
            }
        });

        if (hasError) {
            e.preventDefault();
            alert('Hay notas inválidas. Por favor revise los campos marcados en rojo.');
            return false;
        }

        if (notasVacias === notaInputs.length) {
            e.preventDefault();
            alert('Debe ingresar al menos una nota');
            return false;
        }

        return confirm(`¿Está seguro de guardar las calificaciones? Se guardarán ${notaInputs.length - notasVacias} calificaciones.`);
    });
</script>
@endpush
@endsection