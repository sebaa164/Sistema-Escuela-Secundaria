@extends('layouts.app')

@section('title', 'Tomar Asistencia')
@section('page-title', 'Tomar Asistencia')

@section('content')
<div class="container-fluid px-3 py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-11">

            <div class="card-dark">
                <div class="card-header-dark">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="mb-0">
                            <i class="fas fa-clipboard-check me-2"></i>Registro de Asistencia
                        </h5>
                        <a href="{{ route('admin.asistencias.index') }}" class="btn-back-header">
                            <i class="fas fa-arrow-left me-2"></i>Volver
                        </a>
                    </div>
                </div>

                <div class="card-body-dark">
                    @if(session('success'))
                        <div class="alert-success-dark mb-4">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                        </div>
                    @endif

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

                    <!-- Formulario de Selección -->
                    <form method="GET" action="{{ route('admin.asistencias.create') }}" id="selectionForm">
                        <div class="row g-4 mb-4">
                            <div class="col-12">
                                <div class="section-header">
                                    <i class="fas fa-filter me-2"></i>
                                    <h6>Seleccionar Sección y Fecha</h6>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <label for="seccion_id" class="form-label-dark">
                                    <i class="fas fa-book me-2"></i>Sección *
                                </label>
                                <select name="seccion_id" id="seccion_id" class="form-select-dark" required>
                                    <option value="">Asignar un Curso</option>
                                    @foreach($secciones as $seccion)
                                        <option value="{{ $seccion->id }}" 
                                                {{ request('seccion_id') == $seccion->id ? 'selected' : '' }}>
                                            {{ $seccion->curso->nombre }} - {{ $seccion->codigo_seccion }} 
                                            ({{ $seccion->periodo->nombre }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="fecha" class="form-label-dark">
                                    <i class="fas fa-calendar me-2"></i>Fecha *
                                </label>
                                <input type="date" 
                                       name="fecha" 
                                       id="fecha" 
                                       class="form-control-dark" 
                                       value="{{ $fecha }}"
                                       max="{{ now()->format('Y-m-d') }}"
                                       required>
                            </div>

                            <div class="col-12">
                                <button type="submit" class="btn-neon">
                                    <i class="fas fa-search me-2"></i>Cargar Estudiantes
                                </button>
                            </div>
                        </div>
                    </form>

                    @if($seccionSeleccionada)
                        <!-- Información de la Sección -->
                        <div class="info-box-dark mb-4">
                            <div class="info-icon">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <div class="info-content">
                                <h6 class="mb-1">{{ $seccionSeleccionada->curso->nombre }}</h6>
                                <p class="mb-0">
                                    <span class="info-badge">
                                        <i class="fas fa-layer-group me-1"></i>
                                        {{ $seccionSeleccionada->codigo_seccion }}
                                    </span>
                                    <span class="info-badge">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        {{ $seccionSeleccionada->periodo->nombre }}
                                    </span>
                                    <span class="info-badge">
                                        <i class="fas fa-users me-1"></i>
                                        {{ $inscripciones->count() }} estudiantes
                                    </span>
                                </p>
                            </div>
                        </div>

                        @if($inscripciones->isEmpty())
                            <div class="empty-state py-5">
                                <i class="fas fa-user-slash fa-3x mb-3"></i>
                                <h5>No hay estudiantes inscritos</h5>
                                <p>Esta sección no tiene estudiantes activos</p>
                                <a href="{{ route('admin.inscripciones.create') }}" class="btn-neon mt-3">
                                    <i class="fas fa-user-plus me-2"></i>Inscribir Estudiantes
                                </a>
                            </div>
                        @else
                            <!-- Formulario de Asistencia -->
                            <form action="{{ route('admin.asistencias.store') }}" method="POST" id="asistenciaForm">
                                @csrf
                                <input type="hidden" name="seccion_id" value="{{ $seccionSeleccionada->id }}">
                                <input type="hidden" name="fecha" value="{{ $fecha }}">

                                <div class="row g-4">
                                    <div class="col-12">
                                        <div class="section-header">
                                            <i class="fas fa-users me-2"></i>
                                            <h6>Lista de Estudiantes</h6>
                                        </div>
                                    </div>

                                    <!-- Controles Rápidos -->
                                    <div class="col-12">
                                        <div class="quick-actions">
                                            <button type="button" class="btn-action-quick btn-quick-success" onclick="marcarTodos('presente')">
                                                <i class="fas fa-check-circle me-2"></i>Todos Presentes
                                            </button>
                                            <button type="button" class="btn-action-quick btn-quick-danger" onclick="marcarTodos('ausente')">
                                                <i class="fas fa-times-circle me-2"></i>Todos Ausentes
                                            </button>
                                            <button type="button" class="btn-action-quick btn-quick-warning" onclick="marcarTodos('tardanza')">
                                                <i class="fas fa-clock me-2"></i>Todos Tardanza
                                            </button>
                                            <button type="button" class="btn-action-quick btn-quick-reset" onclick="limpiarTodos()">
                                                <i class="fas fa-eraser me-2"></i>Limpiar
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Tabla de Asistencia -->
                                    <div class="col-12">
                                        <div class="asistencia-table">
                                            <table class="table-dark">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 50px;">#</th>
                                                        <th>Estudiante</th>
                                                        <th style="width: 200px;">Estado</th>
                                                        <th>Observaciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($inscripciones as $index => $inscripcion)
                                                        <tr class="estudiante-row">
                                                            <td>
                                                                <span class="row-number">{{ $index + 1 }}</span>
                                                            </td>
                                                            <td>
                                                                <div class="student-info">
                                                                    <div class="student-avatar bg-gradient-info">
                                                                        <i class="fas fa-user"></i>
                                                                    </div>
                                                                    <div>
                                                                        <strong>{{ $inscripcion->estudiante->nombre_completo }}</strong>
                                                                        <br><small class="text-muted">
                                                                            <i class="fas fa-id-card me-1"></i>
                                                                            {{ $inscripcion->estudiante->codigo }}
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <input type="hidden" 
                                                                       name="asistencias[{{ $index }}][inscripcion_id]" 
                                                                       value="{{ $inscripcion->id }}">
                                                                
                                                                <select name="asistencias[{{ $index }}][estado]" 
                                                                        class="form-select-dark estado-select"
                                                                        required>
                                                                    @php
                                                                        $estadoActual = $inscripcion->asistencia_existente->estado ?? 'presente';
                                                                    @endphp
                                                                    <option value="presente" {{ $estadoActual == 'presente' ? 'selected' : '' }}>
                                                                        ✅ Presente
                                                                    </option>
                                                                    <option value="tardanza" {{ $estadoActual == 'tardanza' ? 'selected' : '' }}>
                                                                        ⏰ Tardanza
                                                                    </option>
                                                                    <option value="ausente" {{ $estadoActual == 'ausente' ? 'selected' : '' }}>
                                                                        ❌ Ausente
                                                                    </option>
                                                                    <option value="justificado" {{ $estadoActual == 'justificado' ? 'selected' : '' }}>
                                                                        📝 Justificado
                                                                    </option>
                                                                </select>
                                                                
                                                                @if($inscripcion->asistencia_existente)
                                                                    <small class="text-warning d-block mt-1">
                                                                        <i class="fas fa-info-circle me-1"></i>Ya registrada
                                                                    </small>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <input type="text" 
                                                                       name="asistencias[{{ $index }}][observaciones]" 
                                                                       class="form-control-dark form-control-sm"
                                                                       placeholder="Opcional..."
                                                                       value="{{ $inscripcion->asistencia_existente->observaciones ?? '' }}"
                                                                       maxlength="500">
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Resumen Mejorado -->
                                    <div class="col-12">
                                        <div class="summary-box">
                                            <div class="summary-item summary-total">
                                                <div class="summary-icon">
                                                    <i class="fas fa-users"></i>
                                                </div>
                                                <div class="summary-content">
                                                    <span class="summary-label">Total</span>
                                                    <span class="summary-value" id="totalEstudiantes">{{ $inscripciones->count() }}</span>
                                                </div>
                                            </div>
                                            <div class="summary-item summary-success">
                                                <div class="summary-icon">
                                                    <i class="fas fa-check-circle"></i>
                                                </div>
                                                <div class="summary-content">
                                                    <span class="summary-label">Presentes</span>
                                                    <span class="summary-value" id="totalPresentes">0</span>
                                                </div>
                                            </div>
                                            <div class="summary-item summary-warning">
                                                <div class="summary-icon">
                                                    <i class="fas fa-clock"></i>
                                                </div>
                                                <div class="summary-content">
                                                    <span class="summary-label">Tardanzas</span>
                                                    <span class="summary-value" id="totalTardanzas">0</span>
                                                </div>
                                            </div>
                                            <div class="summary-item summary-danger">
                                                <div class="summary-icon">
                                                    <i class="fas fa-times-circle"></i>
                                                </div>
                                                <div class="summary-content">
                                                    <span class="summary-label">Ausentes</span>
                                                    <span class="summary-value" id="totalAusentes">0</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Botones -->
                                    <div class="col-12 mt-4">
                                        <div class="form-actions">
                                            <a href="{{ route('admin.asistencias.index') }}" class="btn-outline-neon">
                                                <i class="fas fa-arrow-left me-2"></i>Cancelar
                                            </a>
                                            <button type="submit" class="btn-neon-lg">
                                                <i class="fas fa-save me-2"></i>Guardar Asistencia
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        @endif
                    @endif
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
        margin: 0;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .card-body-dark {
        padding: 2rem;
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
        font-weight: 600;
    }

    .form-label-dark {
        color: var(--neon-cyan);
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-control-dark,
    .form-select-dark {
        background-color: var(--dark-card);
        border: 1px solid rgba(0, 212, 255, 0.3);
        color: var(--text-color);
        padding: 0.75rem 1rem;
        border-radius: 8px;
        transition: all 0.3s ease;
        width: 100%;
    }

    .form-control-dark:focus,
    .form-select-dark:focus {
        background-color: var(--dark-bg);
        border-color: var(--neon-cyan);
        box-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
        color: var(--text-color);
        outline: none;
    }

    .form-control-sm {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }

    .form-select-dark option {
        background-color: var(--dark-card);
        color: var(--text-color);
    }

    .info-box-dark {
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.08) 0%, rgba(14, 165, 233, 0.05) 100%);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 12px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .info-icon {
        font-size: 2.5rem;
        color: var(--neon-cyan);
        text-shadow: 0 0 15px rgba(0, 212, 255, 0.5);
        flex-shrink: 0;
    }

    .info-content h6 {
        color: var(--neon-cyan);
        margin: 0 0 0.5rem 0;
        font-size: 1.1rem;
        font-weight: 700;
    }

    .info-content p {
        color: var(--text-color);
        margin: 0;
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .info-badge {
        background: rgba(0, 212, 255, 0.15);
        border: 1px solid rgba(0, 212, 255, 0.3);
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        font-size: 0.875rem;
        color: var(--text-color);
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }

    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
        padding: 1.5rem;
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.05) 0%, rgba(14, 165, 233, 0.03) 100%);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 12px;
    }

    .btn-action-quick {
        background: rgba(14, 165, 233, 0.15);
        border: 1px solid rgba(0, 212, 255, 0.3);
        color: var(--neon-cyan);
        padding: 0.75rem 1.25rem;
        border-radius: 10px;
        font-size: 0.875rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-action-quick:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 20px rgba(0, 212, 255, 0.4);
    }

    .btn-quick-success {
        border-color: var(--success-color);
        color: var(--success-color);
        background: rgba(16, 185, 129, 0.15);
    }

    .btn-quick-success:hover {
        background: rgba(16, 185, 129, 0.25);
        box-shadow: 0 5px 20px rgba(16, 185, 129, 0.4);
    }

    .btn-quick-danger {
        border-color: var(--danger-color);
        color: var(--danger-color);
        background: rgba(239, 68, 68, 0.15);
    }

    .btn-quick-danger:hover {
        background: rgba(239, 68, 68, 0.25);
        box-shadow: 0 5px 20px rgba(239, 68, 68, 0.4);
    }

    .btn-quick-warning {
        border-color: var(--warning-color);
        color: var(--warning-color);
        background: rgba(245, 158, 11, 0.15);
    }

    .btn-quick-warning:hover {
        background: rgba(245, 158, 11, 0.25);
        box-shadow: 0 5px 20px rgba(245, 158, 11, 0.4);
    }

    .btn-quick-reset {
        border-color: var(--muted-text);
        color: var(--muted-text);
        background: rgba(148, 163, 184, 0.15);
    }

    .btn-quick-reset:hover {
        background: rgba(148, 163, 184, 0.25);
        box-shadow: 0 5px 20px rgba(148, 163, 184, 0.4);
    }

    .asistencia-table {
        max-height: 600px;
        overflow-y: auto;
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 12px;
        background: rgba(15, 23, 42, 0.5);
    }

    .asistencia-table::-webkit-scrollbar {
        width: 10px;
    }

    .asistencia-table::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.3);
        border-radius: 5px;
    }

    .asistencia-table::-webkit-scrollbar-thumb {
        background: linear-gradient(180deg, var(--neon-cyan) 0%, var(--neon-blue) 100%);
        border-radius: 5px;
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
    }

    .table-dark tbody tr:hover {
        background: rgba(0, 212, 255, 0.08);
        transform: scale(1.005);
    }

    .table-dark tbody td {
        padding: 1rem;
        vertical-align: middle;
    }

    .row-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 35px;
        height: 35px;
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        color: var(--dark-bg);
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.9rem;
        box-shadow: 0 0 10px rgba(0, 212, 255, 0.3);
    }

    .student-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .student-avatar {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .bg-gradient-info {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
        box-shadow: 0 0 15px rgba(6, 182, 212, 0.4);
    }

    .estado-select {
        font-weight: 600;
    }

    .summary-box {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        padding: 1.5rem;
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.05) 0%, rgba(14, 165, 233, 0.03) 100%);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 12px;
    }

    .summary-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: rgba(15, 23, 42, 0.5);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
        transition: all 0.3s ease;
    }

    .summary-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 212, 255, 0.3);
    }

    .summary-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .summary-total .summary-icon {
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        color: var(--dark-bg);
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.5);
    }

    .summary-success .summary-icon {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        box-shadow: 0 0 15px rgba(16, 185, 129, 0.5);
    }

    .summary-warning .summary-icon {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        box-shadow: 0 0 15px rgba(245, 158, 11, 0.5);
    }

    .summary-danger .summary-icon {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        box-shadow: 0 0 15px rgba(239, 68, 68, 0.5);
    }

    .summary-content {
        flex: 1;
    }

    .summary-label {
        display: block;
        color: var(--muted-text);
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }

    .summary-value {
        display: block;
        font-size: 2rem;
        font-weight: 700;
        color: var(--neon-cyan);
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
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
        cursor: pointer;
    }

    .btn-neon:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 30px rgba(0, 212, 255, 0.6);
        color: #0f172a;
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
        cursor: pointer;
    }

    .btn-neon-lg:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 30px rgba(0, 212, 255, 0.6);
        color: var(--dark-bg);
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

    .alert-success-dark {
        background-color: rgba(16, 185, 129, 0.2);
        border: 1px solid #10b981;
        color: #10b981;
        padding: 1rem 1.5rem;
        border-radius: 10px;
        display: flex;
        align-items: center;
    }

    .alert-danger-dark {
        background-color: rgba(239, 68, 68, 0.2);
        border: 1px solid #ef4444;
        color: #ef4444;
        padding: 1rem 1.5rem;
        border-radius: 10px;
    }

    .empty-state {
        text-align: center;
        color: var(--muted-text);
        padding: 3rem 2rem;
    }

    .empty-state i {
        color: rgba(0, 212, 255, 0.3);
    }

    .empty-state h5 {
        color: var(--text-color);
        margin-top: 1rem;
        margin-bottom: 0.5rem;
    }

    .text-muted {
        color: var(--muted-text) !important;
    }

    .text-warning {
        color: var(--warning-color) !important;
    }

    /* Botón Volver en Header */
.btn-back-header {
    background: transparent;
    border: 1px solid var(--neon-cyan);
    color: var(--neon-cyan);
    padding: 0.5rem 1.25rem;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    font-size: 0.875rem;
}

.btn-back-header:hover {
    background: rgba(0, 212, 255, 0.15);
    border-color: var(--neon-blue);
    color: var(--neon-blue);
    transform: translateX(-2px);
    box-shadow: 0 0 15px rgba(0, 212, 255, 0.4);
}

.btn-back-header i {
    transition: transform 0.3s ease;
}

.btn-back-header:hover i {
    transform: translateX(-3px);
}

    @media (max-width: 768px) {
        .card-body-dark {
            padding: 1.5rem 1rem;
        }

        .quick-actions {
            grid-template-columns: 1fr;
        }

        .summary-box {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column;
        }

        .btn-neon-lg,
        .btn-outline-neon {
            width: 100%;
            text-align: center;
        }

        .student-info {
            gap: 0.75rem;
        }

        .info-box-dark {
            flex-direction: column;
            text-align: center;
        }

        .info-content p {
            justify-content: center;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Marcar todos con un estado específico
    function marcarTodos(estado) {
        const selects = document.querySelectorAll('.estado-select');
        selects.forEach(select => {
            select.value = estado;
        });
        actualizarResumen();
    }

    // Limpiar todos a presente (por defecto)
    function limpiarTodos() {
        marcarTodos('presente');
    }

    // Actualizar resumen de asistencia
    function actualizarResumen() {
        const selects = document.querySelectorAll('.estado-select');
        let presentes = 0;
        let tardanzas = 0;
        let ausentes = 0;

        selects.forEach(select => {
            const estado = select.value;
            if (estado === 'presente') presentes++;
            else if (estado === 'tardanza') tardanzas++;
            else if (estado === 'ausente') ausentes++;
        });

        document.getElementById('totalPresentes').textContent = presentes;
        document.getElementById('totalTardanzas').textContent = tardanzas;
        document.getElementById('totalAusentes').textContent = ausentes;
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Actualizar resumen al cambiar cualquier select
        const selects = document.querySelectorAll('.estado-select');
        selects.forEach(select => {
            select.addEventListener('change', actualizarResumen);
        });

        // Calcular resumen inicial
        actualizarResumen();

        // Auto-submit del formulario de selección al cambiar sección o fecha
        const seccionSelect = document.getElementById('seccion_id');
        const fechaInput = document.getElementById('fecha');
        
        if (seccionSelect && fechaInput) {
            seccionSelect.addEventListener('change', function() {
                if (this.value && fechaInput.value) {
                    document.getElementById('selectionForm').submit();
                }
            });

            fechaInput.addEventListener('change', function() {
                if (this.value && seccionSelect.value) {
                    document.getElementById('selectionForm').submit();
                }
            });
        }

        // Validación antes de enviar
        const asistenciaForm = document.getElementById('asistenciaForm');
        if (asistenciaForm) {
            asistenciaForm.addEventListener('submit', function(e) {
                const selects = document.querySelectorAll('.estado-select');
                let valid = true;

                selects.forEach(select => {
                    if (!select.value) {
                        valid = false;
                        select.classList.add('is-invalid');
                    } else {
                        select.classList.remove('is-invalid');
                    }
                });

                if (!valid) {
                    e.preventDefault();
                    alert('⚠️ Por favor selecciona el estado de asistencia para todos los estudiantes');
                    return false;
                }

                // Confirmación
                if (!confirm('✅ ¿Confirmar el registro de asistencia?')) {
                    e.preventDefault();
                    return false;
                }
            });
        }
    });
</script>
@endpush
@endsection