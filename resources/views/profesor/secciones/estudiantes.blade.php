@extends('layouts.app')

@section('title', 'Estudiantes - ' . $seccion->codigo_seccion)
@section('page-title', 'Estudiantes de la Sección')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header-dark mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="header-title">
                    <i class="fas fa-users me-3"></i>Estudiantes de la Sección
                </h1>
                <p class="header-subtitle mb-0">{{ $seccion->curso->nombre }} - {{ $seccion->codigo_seccion }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('profesor.secciones.show', $seccion) }}" class="btn-outline-neon">
                    <i class="fas fa-arrow-left me-2"></i>Volver a la Sección
                </a>
                <a href="{{ route('profesor.secciones.exportar-estudiantes-pdf', $seccion) }}" class="btn-action-neon">
                    <i class="fas fa-file-pdf me-2"></i>Exportar PDF
                </a>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="stats-grid mb-4">
        <div class="stat-card">
            <div class="stat-icon bg-gradient-primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $estudiantes->count() }}</h3>
                <p>Estudiantes Inscritos</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-gradient-success">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $seccion->cupo_maximo }}</h3>
                <p>Cupo Máximo</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-gradient-info">
                <i class="fas fa-user-plus"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $seccion->cupo_maximo - $estudiantes->count() }}</h3>
                <p>Cupos Disponibles</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-gradient-warning">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $estudiantes->whereNotNull('nota_final')->count() }}/{{ $estudiantes->count() }}</h3>
                <p>Notas Registradas</p>
            </div>
        </div>
    </div>

    <!-- Información de la Sección -->
    <div class="card-dark mb-4">
        <div class="card-header-dark">
            <h5><i class="fas fa-info-circle me-2"></i>Información de la Sección</h5>
        </div>
        <div class="card-body-dark">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="info-item">
                        <strong>Curso:</strong>
                        <span>{{ $seccion->curso->nombre }}</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-item">
                        <strong>Código:</strong>
                        <span>{{ $seccion->codigo_seccion }}</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-item">
                        <strong>Aula:</strong>
                        <span>{{ $seccion->aula ?? 'No asignada' }}</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-item">
                        <strong>Período:</strong>
                        <span>{{ $seccion->periodo->nombre ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Estudiantes -->
    <div class="card-dark">
        <div class="card-header-dark">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>Lista de Estudiantes
                </h5>
                @if($seccion->estudiantes_inscritos < $seccion->cupo_maximo)
                    <button type="button" class="btn-neon-sm" data-bs-toggle="modal" data-bs-target="#modalAgregarEstudiante">
                        <i class="fas fa-plus me-2"></i>Agregar Estudiante
                    </button>
                @endif
            </div>
        </div>
        <div class="card-body-dark">
            @if($estudiantes->count() > 0)
                <div class="table-responsive">
                    <table class="table-dark">
                        <thead>
                            <tr>
                                <th>Estudiante</th>
                                <th>Email</th>
                                <th>Fecha de Inscripción</th>
                                <th>Nota Final</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($estudiantes as $estudiante)
                                <tr>
                                    <td>
                                        <div class="student-info">
                                            <div class="student-avatar">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <div>
                                                <strong>{{ $estudiante['nombre_completo'] }}</strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $estudiante['email'] }}</td>
                                    <td>
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ \Carbon\Carbon::parse($estudiante['fecha_inscripcion'])->format('d/m/Y') }}
                                    </td>
                                    <td>
                                        @if($estudiante['nota_final'])
                                            <span class="badge-grade {{ $estudiante['nota_final'] >= 60 ? 'approved' : 'failed' }}">
                                                {{ number_format($estudiante['nota_final'], 1) }}
                                            </span>
                                        @else
                                            <span class="text-muted">Pendiente</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($estudiante['estado_nota'] === 'Aprobado')
                                            <span class="status-badge status-approved">
                                                <i class="fas fa-check"></i> Aprobado
                                            </span>
                                        @elseif($estudiante['estado_nota'] === 'Reprobado')
                                            <span class="status-badge status-failed">
                                                <i class="fas fa-times"></i> Reprobado
                                            </span>
                                        @else
                                            <span class="status-badge status-pending">
                                                <i class="fas fa-clock"></i> Sin calificar
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('profesor.asistencias.reporte-estudiante', [$seccion->id, $estudiante['id']]) }}"
                                               class="btn-action btn-action-info"
                                               title="Ver asistencia">
                                                <i class="fas fa-calendar-check"></i>
                                            </a>
                                            <a href="{{ route('profesor.evaluaciones.index') }}?seccion_id={{ $seccion->id }}"
                                               class="btn-action btn-action-success"
                                               title="Ver evaluaciones">
                                                <i class="fas fa-star"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn-action btn-action-danger"
                                                    title="Remover estudiante"
                                                    onclick="removerEstudiante({{ $estudiante['id'] }}, '{{ $estudiante['nombre_completo'] }}')">
                                                <i class="fas fa-user-minus"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-4x mb-4" style="color: rgba(0, 212, 255, 0.3);"></i>
                    <h4 style="color: #f1f5f9;">No hay estudiantes inscritos</h4>
                    <p style="color: #94a3b8;">Esta sección aún no tiene estudiantes inscritos</p>
                    @if($seccion->estudiantes_inscritos < $seccion->cupo_maximo)
                        <button type="button" class="btn-neon-sm mt-3" data-bs-toggle="modal" data-bs-target="#modalAgregarEstudiante">
                            <i class="fas fa-plus me-2"></i>Agregar Primer Estudiante
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal para Agregar Estudiante -->
<div class="modal fade" id="modalAgregarEstudiante" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content modal-dark">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus me-2"></i>Agregar Estudiante a la Sección
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('profesor.secciones.agregar-estudiante', $seccion) }}" method="POST" id="formAgregarEstudiante">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label-neon">Buscar Estudiante</label>
                        <div class="input-group">
                            <input type="text" id="busquedaEstudiante" class="form-control-neon" placeholder="Nombre, apellido o email..." autocomplete="off">
                            <button type="button" class="btn btn-outline-neon" onclick="buscarEstudiantes()">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <small class="text-muted">Escribe al menos 2 caracteres para buscar</small>
                    </div>

                    <div id="resultadosBusqueda" class="mb-3" style="display: none;">
                        <label class="form-label-neon">Seleccionar Estudiante</label>
                        <div id="listaEstudiantes" class="border rounded p-3" style="max-height: 200px; overflow-y: auto; background: rgba(15, 23, 42, 0.5); border-color: rgba(0, 212, 255, 0.3) !important;">
                            <!-- Resultados de búsqueda se mostrarán aquí -->
                        </div>
                    </div>

                    <input type="hidden" name="estudiante_id" id="estudianteSeleccionado">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-outline-neon" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-neon" id="btnAgregar" disabled>
                        <i class="fas fa-plus me-2"></i>Agregar Estudiante
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

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    .stat-card {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(30, 41, 59, 0.9) 100%);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 15px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.1);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0 25px rgba(0, 212, 255, 0.3);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }

    .bg-gradient-primary { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); }
    .bg-gradient-success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .bg-gradient-info { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }
    .bg-gradient-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }

    .stat-content h3 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--neon-cyan);
        margin: 0;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .stat-content p { color: #cbd5e1; margin: 0; font-size: 0.875rem; }

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

    .card-body-dark { padding: 2rem; }

    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        background: rgba(0, 212, 255, 0.05);
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 8px;
    }

    .info-item strong { color: var(--neon-cyan); }
    .info-item span { color: #f1f5f9; }

    .student-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .student-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0f172a;
        font-size: 1.25rem;
    }

    .badge-grade {
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .badge-grade.approved {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 1px solid #10b981;
    }

    .badge-grade.failed {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 1px solid #ef4444;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-approved {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 1px solid #10b981;
    }

    .status-failed {
        background: rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border: 1px solid #ef4444;
    }

    .status-pending {
        background: rgba(245, 158, 11, 0.2);
        color: #f59e0b;
        border: 1px solid #f59e0b;
    }

    .table-dark { width: 100%; color: #e2e8f0; }

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

    .table-dark tbody tr { border-bottom: 1px solid rgba(0, 212, 255, 0.1); transition: all 0.3s ease; }
    .table-dark tbody tr:hover { background: rgba(0, 212, 255, 0.05); }
    .table-dark tbody td { padding: 1rem; vertical-align: middle; }

    .action-buttons { display: flex; gap: 0.5rem; }

    .btn-action {
        width: 35px;
        height: 35px;
        border-radius: 8px;
        border: 1px solid;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        cursor: pointer;
        background: transparent;
    }

    .btn-action-info { border-color: #06b6d4; color: #06b6d4; }
    .btn-action-info:hover { background: #06b6d4; color: white; box-shadow: 0 0 15px rgba(6, 182, 212, 0.5); }

    .btn-action-success { border-color: #10b981; color: #10b981; }
    .btn-action-success:hover { background: #10b981; color: white; box-shadow: 0 0 15px rgba(16, 185, 129, 0.5); }

    .btn-action-danger { border-color: #ef4444; color: #ef4444; }
    .btn-action-danger:hover { background: #ef4444; color: white; box-shadow: 0 0 15px rgba(239, 68, 68, 0.5); }

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

    .btn-action-neon {
        padding: 0.75rem 1.5rem;
        background: rgba(0, 212, 255, 0.1);
        border: 1px solid rgba(0, 212, 255, 0.3);
        color: var(--neon-cyan);
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
    }

    .btn-action-neon:hover {
        background: rgba(0, 212, 255, 0.2);
        border-color: var(--neon-cyan);
        transform: translateY(-2px);
        box-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
        color: var(--neon-cyan);
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

    .form-label-neon {
        color: var(--neon-cyan);
        font-weight: 600;
        margin-bottom: 0.5rem;
        display: block;
    }

    .modal-dark {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.98) 0%, rgba(30, 41, 59, 0.98) 100%);
        border: 1px solid rgba(0, 212, 255, 0.3);
        color: #e2e8f0;
    }

    .modal-dark .modal-header {
        border-bottom: 1px solid rgba(0, 212, 255, 0.2);
    }

    .modal-dark .modal-title {
        color: var(--neon-cyan);
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .modal-dark .modal-footer {
        border-top: 1px solid rgba(0, 212, 255, 0.2);
    }

    .estudiante-item {
        padding: 0.75rem;
        border: 1px solid rgba(0, 212, 255, 0.2);
        border-radius: 8px;
        margin-bottom: 0.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
        background: rgba(15, 23, 42, 0.3);
    }

    .estudiante-item:hover {
        border-color: var(--neon-cyan);
        background: rgba(0, 212, 255, 0.1);
    }

    .estudiante-item.selected {
        border-color: var(--neon-cyan);
        background: rgba(0, 212, 255, 0.2);
        box-shadow: 0 0 10px rgba(0, 212, 255, 0.3);
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .student-info {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
// Esperar a que el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Configurar el modal para limpiar cuando se abre
    const modal = document.getElementById('modalAgregarEstudiante');
    modal.addEventListener('show.bs.modal', function() {
        // Limpiar el formulario
        document.getElementById('busquedaEstudiante').value = '';
        document.getElementById('estudianteSeleccionado').value = '';
        document.getElementById('btnAgregar').disabled = true;
        document.getElementById('btnAgregar').innerHTML = '<i class="fas fa-plus me-2"></i>Agregar Estudiante';
        document.getElementById('resultadosBusqueda').style.display = 'none';
        document.getElementById('listaEstudiantes').innerHTML = '';

        // Dar foco al input después de un pequeño delay
        setTimeout(function() {
            document.getElementById('busquedaEstudiante').focus();
        }, 300);
    });

    // Permitir búsqueda con Enter
    document.getElementById('busquedaEstudiante').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            buscarEstudiantes();
        }
    });
});

function buscarEstudiantes() {
    const termino = document.getElementById('busquedaEstudiante').value.trim();
    const resultadosDiv = document.getElementById('resultadosBusqueda');
    const listaDiv = document.getElementById('listaEstudiantes');

    if (termino.length < 2) {
        alert('Por favor ingresa al menos 2 caracteres para buscar.');
        return;
    }

    // Mostrar indicador de carga
    listaDiv.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Buscando...</div>';

    fetch(`{{ route('profesor.secciones.buscar-estudiantes', $seccion) }}?q=${encodeURIComponent(termino)}`)
        .then(response => response.json())
        .then(data => {
            listaDiv.innerHTML = '';

            if (data.length === 0) {
                listaDiv.innerHTML = '<p class="text-muted p-3 mb-0">No se encontraron estudiantes disponibles.</p>';
            } else {
                data.forEach(estudiante => {
                    const div = document.createElement('div');
                    div.className = 'estudiante-item';
                    div.onclick = () => seleccionarEstudiante(estudiante.id, estudiante.nombre + ' ' + estudiante.apellido);
                    div.innerHTML = `
                        <strong>${estudiante.nombre} ${estudiante.apellido}</strong><br>
                        <small class="text-muted">${estudiante.email}</small>
                    `;
                    listaDiv.appendChild(div);
                });
            }

            resultadosDiv.style.display = 'block';
        })
        .catch(error => {
            console.error('Error:', error);
            listaDiv.innerHTML = '<p class="text-danger p-3 mb-0">Error al buscar estudiantes.</p>';
        });
}

function seleccionarEstudiante(id, nombre) {
    // Limpiar selecciones anteriores
    document.querySelectorAll('.estudiante-item').forEach(item => {
        item.classList.remove('selected');
    });

    // Marcar como seleccionado
    event.target.closest('.estudiante-item').classList.add('selected');

    // Actualizar el formulario
    document.getElementById('estudianteSeleccionado').value = id;
    document.getElementById('btnAgregar').disabled = false;
    document.getElementById('btnAgregar').innerHTML = `<i class="fas fa-plus me-2"></i>Agregar: ${nombre}`;
}

function removerEstudiante(estudianteId, nombre) {
    if (confirm(`¿Estás seguro de remover a ${nombre} de esta sección? Esta acción no se puede deshacer.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ route('profesor.secciones.remover-estudiante', [$seccion->id, ':estudianteId']) }}`.replace(':estudianteId', estudianteId);

        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);

        const method = document.createElement('input');
        method.type = 'hidden';
        method.name = '_method';
        method.value = 'DELETE';
        form.appendChild(method);

        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
@endsection
