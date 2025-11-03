@extends('layouts.app')

@section('title', 'Detalle de Calificación')
@section('page-title', 'Detalle de Calificación')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <!-- Card Principal -->
            <div class="card-dark">
                <div class="card-header-dark">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <h5 class="mb-0">
                            <i class="fas fa-clipboard-check me-2"></i>Información de la Calificación
                        </h5>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.calificaciones.edit', $calificacion) }}" class="btn-neon">
                                <i class="fas fa-edit me-2"></i>Editar
                            </a>
                            <a href="{{ route('admin.calificaciones.index') }}" class="btn-outline-neon">
                                <i class="fas fa-arrow-left me-2"></i>Volver
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body-dark">
                    <!-- Información del Estudiante -->
                    <div class="section-header mb-4">
                        <i class="fas fa-user-graduate me-2"></i>
                        <h6>Información del Estudiante</h6>
                    </div>

                    <div class="student-detail-card mb-4">
                        <div class="student-avatar-large">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div class="student-detail-info">
                            <h4>{{ $calificacion->estudiante->nombre_completo }}</h4>
                            <p class="student-email">
                                <i class="fas fa-envelope me-2"></i>{{ $calificacion->estudiante->email }}
                            </p>
                            @if($calificacion->estudiante->telefono)
                                <p class="student-phone">
                                    <i class="fas fa-phone me-2"></i>{{ $calificacion->estudiante->telefono }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Información de la Evaluación -->
                    <div class="section-header mb-4">
                        <i class="fas fa-clipboard-list me-2"></i>
                        <h6>Información de la Evaluación</h6>
                    </div>

                    <div class="evaluation-detail-grid mb-4">
                        <div class="detail-card">
                            <div class="detail-icon bg-gradient-primary">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="detail-content">
                                <label>Curso</label>
                                <p>{{ $calificacion->evaluacion->seccion->curso->nombre }}</p>
                                <small>{{ $calificacion->evaluacion->seccion->curso->codigo_curso }}</small>
                            </div>
                        </div>

                        <div class="detail-card">
                            <div class="detail-icon bg-gradient-info">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="detail-content">
                                <label>Evaluación</label>
                                <p>{{ $calificacion->evaluacion->nombre }}</p>
                                <small>{{ $calificacion->evaluacion->tipoEvaluacion->nombre ?? 'N/A' }}</small>
                            </div>
                        </div>

                        <div class="detail-card">
                            <div class="detail-icon bg-gradient-warning">
                                <i class="fas fa-percentage"></i>
                            </div>
                            <div class="detail-content">
                                <label>Porcentaje</label>
                                <p>{{ $calificacion->evaluacion->porcentaje }}%</p>
                                <small>Del total del curso</small>
                            </div>
                        </div>

                        <div class="detail-card">
                            <div class="detail-icon bg-gradient-success">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <div class="detail-content">
                                <label>Fecha Evaluación</label>
                                <p>{{ $calificacion->evaluacion->fecha_evaluacion->format('d/m/Y') }}</p>
                                <small>{{ $calificacion->evaluacion->fecha_evaluacion->format('H:i') }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Calificación -->
                    <div class="section-header mb-4">
                        <i class="fas fa-star me-2"></i>
                        <h6>Calificación Obtenida</h6>
                    </div>

                    <div class="grade-showcase mb-4">
                        <!-- Nota principal - Lado izquierdo -->
                        <div class="grade-main">
                            <div class="grade-value {{ $calificacion->esta_aprobada ? 'grade-approved' : 'grade-failed' }}">
                                {{ $calificacion->nota_formateada }}
                            </div>
                            <div class="grade-status">
                                <span class="status-badge {{ $calificacion->esta_aprobada ? 'status-success' : 'status-danger' }}">
                                    <i class="fas {{ $calificacion->esta_aprobada ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                    {{ $calificacion->estado_nota }}
                                </span>
                            </div>
                        </div>

                        <!-- Detalles - Lado derecho -->
                        <div class="grade-details-grid">
                            <div class="grade-detail-item">
                                <i class="fas fa-calendar-check"></i>
                                <div>
                                    <label>Fecha Calificación</label>
                                    <p>{{ $calificacion->fecha_calificacion->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>

                            <div class="grade-detail-item">
                                <i class="fas fa-info-circle"></i>
                                <div>
                                    <label>Estado</label>
                                    <p>
                                        @if($calificacion->estado === 'pendiente')
                                            <span class="status-badge status-pending">
                                                <i class="fas fa-clock"></i> Pendiente
                                            </span>
                                        @elseif($calificacion->estado === 'calificada')
                                            <span class="status-badge status-graded">
                                                <i class="fas fa-check"></i> Calificada
                                            </span>
                                        @else
                                            <span class="status-badge status-reviewed">
                                                <i class="fas fa-check-double"></i> Revisada
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            @if($calificacion->intentos)
                                <div class="grade-detail-item">
                                    <i class="fas fa-redo"></i>
                                    <div>
                                        <label>Intentos</label>
                                        <p>{{ $calificacion->intentos }}</p>
                                    </div>
                                </div>
                            @endif

                            @if($calificacion->tiempo_empleado)
                                <div class="grade-detail-item">
                                    <i class="fas fa-clock"></i>
                                    <div>
                                        <label>Tiempo Empleado</label>
                                        <p>{{ $calificacion->tiempo_empleado_formateado }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Comentarios -->
                    @if($calificacion->comentarios)
                        <div class="section-header mb-4">
                            <i class="fas fa-comment me-2"></i>
                            <h6>Comentarios y Retroalimentación</h6>
                        </div>

                        <div class="comments-box mb-4">
                            <p>{{ $calificacion->comentarios }}</p>
                        </div>
                    @endif

                    <!-- Otras Calificaciones del Estudiante -->
                    @if($otrasCalificaciones->isNotEmpty())
                        <div class="section-header mb-4">
                            <i class="fas fa-list me-2"></i>
                            <h6>Otras Calificaciones en {{ $calificacion->evaluacion->seccion->curso->nombre }}</h6>
                        </div>

                        <div class="table-responsive">
                            <table class="table-dark">
                                <thead>
                                    <tr>
                                        <th>Evaluación</th>
                                        <th>Tipo</th>
                                        <th>Porcentaje</th>
                                        <th>Nota</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($otrasCalificaciones as $otra)
                                        <tr>
                                            <td>
                                                <strong>{{ $otra->evaluacion->nombre }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge-info">
                                                    {{ $otra->evaluacion->tipoEvaluacion->nombre ?? 'N/A' }}
                                                </span>
                                            </td>
                                            <td>{{ $otra->evaluacion->porcentaje }}%</td>
                                            <td>
                                                <span class="grade-number-small {{ $otra->esta_aprobada ? 'grade-pass' : 'grade-fail' }}">
                                                    {{ $otra->nota_formateada }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($otra->estado === 'pendiente')
                                                    <span class="status-badge status-pending">
                                                        <i class="fas fa-clock"></i> Pendiente
                                                    </span>
                                                @elseif($otra->estado === 'calificada')
                                                    <span class="status-badge status-graded">
                                                        <i class="fas fa-check"></i> Calificada
                                                    </span>
                                                @else
                                                    <span class="status-badge status-reviewed">
                                                        <i class="fas fa-check-double"></i> Revisada
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $otra->fecha_calificacion->format('d/m/Y') }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <!-- Información del Sistema -->
                    <div class="system-info mt-5">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="info-text">
                                    <i class="fas fa-user me-2"></i>
                                    <strong>Profesor:</strong> 
                                    {{ $calificacion->evaluacion->seccion->profesor->nombre_completo }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="info-text">
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    <strong>Período:</strong> 
                                    {{ $calificacion->evaluacion->seccion->periodo->nombre }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="info-text">
                                    <i class="fas fa-clock me-2"></i>
                                    <strong>Registro creado:</strong> 
                                    {{ $calificacion->created_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="info-text">
                                    <i class="fas fa-sync me-2"></i>
                                    <strong>Última actualización:</strong> 
                                    {{ $calificacion->updated_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Card Dark */
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

    .card-body-dark {
        padding: 2rem;
    }

    /* Section Header */
    .section-header {
        display: flex;
        align-items: center;
        color: var(--neon-cyan);
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

    /* Student Detail Card - COLORES MEJORADOS ✅ */
    .student-detail-card {
        display: flex;
        align-items: center;
        gap: 2rem;
        padding: 2rem;
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.1) 0%, rgba(14, 165, 233, 0.05) 100%);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 15px;
    }

    .student-avatar-large {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        color: #0f172a;
        box-shadow: 0 0 25px rgba(0, 212, 255, 0.5);
        flex-shrink: 0;
    }

    .student-detail-info h4 {
        color: #ffffff;
        margin-bottom: 0.75rem;
        font-weight: 600;
        font-size: 1.5rem;
    }

    /* EMAIL Y TELÉFONO EN BLANCO ✅ */
    .student-email,
    .student-phone {
        color: #ffffff !important;
        margin: 0.25rem 0;
        font-size: 1rem;
        display: flex;
        align-items: center;
    }

    .student-email i,
    .student-phone i {
        color: var(--neon-cyan);
    }

    /* Evaluation Detail Grid */
    .evaluation-detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    .detail-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.5rem;
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .detail-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.2);
        border-color: var(--neon-cyan);
    }

    .detail-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        flex-shrink: 0;
    }

    .bg-gradient-primary {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .bg-gradient-info {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
    }

    .bg-gradient-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .detail-content label {
        color: #94a3b8;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 0.25rem;
        display: block;
    }

    .detail-content p {
        color: #e2e8f0;
        font-weight: 600;
        font-size: 1.1rem;
        margin: 0;
    }

    .detail-content small {
        color: #94a3b8;
        font-size: 0.875rem;
    }

    /* Grade Showcase - DISEÑO PROFESIONAL Y COMPACTO ✅ */
    .grade-showcase {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(30, 41, 59, 0.9) 100%);
        border: 2px solid rgba(0, 212, 255, 0.3);
        border-radius: 15px;
        padding: 2rem;
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 2rem;
        align-items: center;
    }

    .grade-main {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        background: rgba(0, 212, 255, 0.05);
        border: 2px solid rgba(0, 212, 255, 0.2);
        border-radius: 15px;
        min-width: 180px;
    }

    /* NOTA PROFESIONAL Y COMPACTA ✅ */
    .grade-value {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        text-shadow: 0 0 15px currentColor;
        letter-spacing: 1px;
    }

    .grade-approved {
        color: #10b981;
    }

    .grade-failed {
        color: #ef4444;
    }

    .grade-status {
        margin-top: 0.5rem;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .status-pending {
        background: rgba(245, 158, 11, 0.2);
        color: #f59e0b;
        border: 2px solid #f59e0b;
    }

    .status-graded {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 2px solid #10b981;
    }

    .status-reviewed {
        background: rgba(6, 182, 212, 0.2);
        color: #06b6d4;
        border: 2px solid #06b6d4;
    }

    .status-success {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 2px solid #10b981;
    }

    .status-danger {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 2px solid #ef4444;
    }

    /* Grade Details Grid */
    .grade-details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .grade-detail-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: rgba(0, 212, 255, 0.05);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 10px;
    }

    .grade-detail-item i {
        font-size: 1.5rem;
        color: var(--neon-cyan);
    }

    .grade-detail-item label {
        color: #94a3b8;
        font-size: 0.75rem;
        text-transform: uppercase;
        margin-bottom: 0.25rem;
        display: block;
    }

    .grade-detail-item p {
        color: #e2e8f0;
        font-weight: 600;
        margin: 0;
    }

    /* Comments Box */
    .comments-box {
        background: rgba(0, 212, 255, 0.05);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 12px;
        padding: 1.5rem;
    }

    .comments-box p {
        color: #e2e8f0;
        line-height: 1.8;
        margin: 0;
        white-space: pre-wrap;
    }

    /* Table Dark */
    .table-dark {
        width: 100%;
        color: #e2e8f0;
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

    .grade-number-small {
        padding: 0.35rem 0.75rem;
        border-radius: 8px;
        font-weight: 700;
        font-size: 1rem;
    }

    .grade-pass {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 2px solid #10b981;
    }

    .grade-fail {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 2px solid #ef4444;
    }

    .badge-info {
        background: rgba(6, 182, 212, 0.2);
        color: #06b6d4;
        border: 1px solid #06b6d4;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
    }

    /* System Info */
    .system-info {
        padding-top: 2rem;
        border-top: 1px solid rgba(0, 212, 255, 0.2);
    }

    .info-text {
        color: #94a3b8;
        margin-bottom: 1rem;
    }

    .info-text i {
        color: var(--neon-cyan);
    }

    .info-text strong {
        color: #e2e8f0;
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
    }

    /* Responsive */
    @media (max-width: 768px) {
        .student-detail-card {
            flex-direction: column;
            text-align: center;
        }

        .grade-value {
            font-size: 2rem;
        }

        .grade-showcase {
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        .evaluation-detail-grid,
        .grade-details-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush
@endsection