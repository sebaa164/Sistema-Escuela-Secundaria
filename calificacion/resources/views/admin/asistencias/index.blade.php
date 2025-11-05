@extends('layouts.app')

@section('title', 'Gestión de Asistencias')
@section('page-title', 'Asistencias')

@section('content')
<div class="container-fluid">
    <!-- Estadísticas -->
    <div class="stats-grid mb-4">
        <div class="stat-card">
            <div class="stat-icon bg-gradient-primary">
                <i class="fas fa-clipboard-check"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['total'] }}</h3>
                <p>Total Registros</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-success">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['presente'] }}</h3>
                <p>Presentes</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['tardanza'] }}</h3>
                <p>Tardanzas</p>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon bg-gradient-danger">
                <i class="fas fa-user-times"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['ausente'] }}</h3>
                <p>Ausentes</p>
            </div>
        </div>
    </div>

    <div class="card-dark">
        <div class="card-header-dark">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0">
                    <i class="fas fa-book me-2"></i>Materias Disponibles
                </h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.asistencias.reporte') }}" class="btn-neon btn-sm">
                        <i class="fas fa-chart-bar me-2"></i>Reportes
                    </a>
                    <a href="{{ route('admin.asistencias.create') }}" class="btn-neon btn-sm">
                        <i class="fas fa-plus me-2"></i>Tomar Asistencia
                    </a>
                </div>
            </div>
        </div>

        <div class="card-body-dark">
            @if(session('success'))
                <div class="alert-success-dark mb-4">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Filtros -->
            <form method="GET" action="{{ route('admin.asistencias.index') }}" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <select name="periodo_id" class="form-select-dark">
                            <option value="">📅 Todos los Períodos</option>
                            @foreach($periodos as $periodo)
                                <option value="{{ $periodo->id }}" {{ request('periodo_id') == $periodo->id ? 'selected' : '' }}>
                                    {{ $periodo->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn-neon w-100">
                            <i class="fas fa-filter me-2"></i>Filtrar
                        </button>
                    </div>
                </div>
            </form>

            <!-- Lista de Materias -->
            <div class="row g-3">
                @forelse($secciones as $seccion)
                    <div class="col-md-6 col-lg-4">
                        <div class="materia-card" onclick="window.location.href='{{ route('admin.asistencias.index', ['seccion_id' => $seccion->id]) }}'" style="cursor: pointer;">
                            <div class="materia-header">
                                <div class="materia-icon">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div class="materia-info">
                                    <h6 class="materia-nombre">{{ $seccion->curso->nombre }}</h6>
                                    <span class="materia-codigo">{{ $seccion->codigo_seccion }}</span>
                                </div>
                            </div>
                            <div class="materia-details">
                                <div class="materia-stats">
                                    <span class="badge badge-neon">{{ $seccion->inscripciones()->where('estado', 'inscrito')->count() }} estudiantes</span>
                                </div>
                                <div class="materia-periodo">
                                    <small class="text-muted">{{ $seccion->periodo->nombre ?? 'Sin período' }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="empty-state">
                            <i class="fas fa-book fa-3x mb-3"></i>
                            <h5>No se encontraron materias</h5>
                            <p>No hay secciones activas disponibles</p>
                        </div>
                    </div>
                @endforelse
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

    /* Stats Grid */
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
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.1);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0 30px rgba(0, 212, 255, 0.3);
        border-color: var(--neon-cyan);
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
    .bg-gradient-warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .bg-gradient-danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
    .bg-gradient-info { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }

    .stat-content h3 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--neon-cyan);
        margin: 0;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
    }

    .stat-content p {
        color: var(--muted-text);
        margin: 0;
        font-size: 0.875rem;
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
        font-weight: 600;
        text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
        margin: 0;
    }

    .card-body-dark {
        padding: 2rem;
        color: var(--text-color);
    }

    .alert-success-dark {
        background-color: rgba(16, 185, 129, 0.2);
        border: 1px solid #10b981;
        color: #10b981;
        padding: 1rem;
        border-radius: 8px;
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

    .btn-neon.btn-sm {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }

    .form-select-dark {
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 10px;
        color: #e2e8f0;
        padding: 0.75rem 1rem;
        width: 100%;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .form-select-dark:focus {
        background: rgba(15, 23, 42, 0.95);
        border-color: var(--neon-cyan);
        box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
        outline: none;
    }

    .form-select-dark option {
        background-color: var(--dark-card);
        color: var(--text-color);
    }

    /* Materia Cards */
    .materia-card {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(30, 41, 59, 0.9) 100%);
        border: 1px solid rgba(0, 212, 255, 0.3);
        border-radius: 12px;
        padding: 1.5rem;
        color: var(--text-color); /* forzar texto claro dentro de la tarjeta */
        transition: all 0.3s ease;
        box-shadow: 0 2px 10px rgba(0, 212, 255, 0.1);
    }

    .materia-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 20px rgba(0, 212, 255, 0.3);
        border-color: var(--neon-cyan);
    }

    .materia-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .materia-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--neon-cyan) 0%, var(--electric-blue) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff; /* icon text claro sobre fondo neon */
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    .materia-info {
        flex: 1;
        min-width: 0;
    }

    .materia-nombre {
        color: var(--neon-cyan);
        font-weight: 600;
        margin: 0 0 0.25rem 0;
        text-shadow: 0 0 8px rgba(0, 212, 255, 0.4);
    }
    .materia-codigo {
        color: #e6eef8; /* tono claro para mayor contraste */
        font-size: 0.875rem;
    }

    .materia-details {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .materia-stats {
        flex: 1;
    }

    .badge-neon {
        background: linear-gradient(135deg, var(--neon-cyan) 0%, var(--electric-blue) 100%);
        color: #ffffff; /* texto claro en badge */
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.75rem;
        box-shadow: 0 0 15px rgba(0, 255, 255, 0.4);
        display: inline-block;
        white-space: nowrap;
    }

    .empty-state {
        color: #94a3b8;
        padding: 3rem 1rem;
        text-align: center;
    }

    .empty-state i {
        color: rgba(0, 212, 255, 0.3);
    }

    .empty-state h5 {
        color: #e2e8f0;
        margin-bottom: 0.5rem;
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .stat-content h3 {
            font-size: 1.5rem;
        }

        .card-body-dark {
            padding: 1.5rem;
        }

        .materia-header {
            flex-direction: column;
            box-shadow: 0 0 15px rgba(0, 212, 255, 0.5);
            flex-shrink: 0;
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

        .status-active {
            background: rgba(16, 185, 129, 0.2);
            color: #10b981;
            border: 1px solid #10b981;
        }

        .status-inactive {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid #ef4444;
        }

        .status-warning {
            background: rgba(245, 158, 11, 0.2);
            color: #f59e0b;
            border: 1px solid #f59e0b;
        }

        .status-info {
            background: rgba(6, 182, 212, 0.2);
            color: #06b6d4;
            border: 1px solid #06b6d4;
        }

        .status-badge i {
            font-size: 0.5rem;
            animation: statusPulse 2s infinite;
        }

        @keyframes statusPulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .badge-info {
            background: rgba(6, 182, 212, 0.2);
            color: #06b6d4;
            border: 1px solid #06b6d4;
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .text-muted {
            color: var(--muted-text) !important;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

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

        .btn-action-info {
            border-color: #06b6d4;
            color: #06b6d4;
        }

        .btn-action-info:hover {
            background: #06b6d4;
            color: white;
            box-shadow: 0 0 15px rgba(6, 182, 212, 0.5);
        }

        .btn-action-warning {
            border-color: #84cc16;
            color: #84cc16;
        }

        .btn-action-warning:hover {
            background: #84cc16;
            color: #0f172a;
            box-shadow: 0 0 15px rgba(132, 204, 22, 0.5);
            transform: scale(1.1);
        }

        .btn-action-danger {
            border-color: #ef4444;
            color: #ef4444;
        }

        .btn-action-danger:hover {
            background: #ef4444;
            color: white;
            box-shadow: 0 0 15px rgba(239, 68, 68, 0.5);
        }

        .empty-state {
            color: #94a3b8;
            padding: 3rem;
            text-align: center;
        }

        .empty-state i {
            color: rgba(0, 212, 255, 0.3);
        }

        .empty-state h5 {
            color: #e2e8f0;
            margin-bottom: 0.5rem;
        }

        .pagination-dark {
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(0, 212, 255, 0.2);
            border-radius: 10px;
            padding: 1rem 1.5rem;
        }

        .pagination-info {
            color: var(--muted-text);
            font-size: 0.875rem;
        }

        .pagination-links {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .page-link {
            min-width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(0, 212, 255, 0.3);
            background: rgba(15, 23, 42, 0.8);
            color: var(--neon-cyan);
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.875rem;
        }

        .page-link:hover:not(.disabled):not(.active) {
            background: rgba(0, 212, 255, 0.2);
            border-color: var(--neon-cyan);
            transform: translateY(-2px);
            box-shadow: 0 0 10px rgba(0, 212, 255, 0.3);
        }

        .page-link.active {
            background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
            color: #0f172a;
            border-color: var(--neon-cyan);
            font-weight: 600;
            box-shadow: 0 0 15px rgba(0, 212, 255, 0.5);
        }

        .page-link.disabled {
            opacity: 0.3;
            cursor: not-allowed;
            border-color: rgba(0, 212, 255, 0.1);
            color: var(--muted-text);
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: row;
                flex-wrap: wrap;
            }
            
            .table-responsive {
                overflow-x: auto;
            }

            .pagination-dark .d-flex {
                flex-direction: column;
                gap: 1rem;
            }

            .pagination-info {
                text-align: center;
            }

            .pagination-links {
                justify-content: center;
                flex-wrap: wrap;
            }

            .card-body-dark {
                padding: 1.5rem;
            }
        }

        /* Secciones por Día */
        .dia-section {
            border: 1px solid rgba(0, 212, 255, 0.2);
            border-radius: 12px;
            overflow: hidden;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(30, 41, 59, 0.9) 100%);
            box-shadow: 0 2px 15px rgba(0, 212, 255, 0.1);
        }

        .dia-header {
            background: linear-gradient(135deg, rgba(14, 165, 233, 0.15) 0%, rgba(0, 212, 255, 0.05) 100%);
            padding: 1.5rem;
            border-bottom: 1px solid rgba(0, 212, 255, 0.2);
            transition: all 0.3s ease;
        }

        .dia-header:hover {
            background: linear-gradient(135deg, rgba(14, 165, 233, 0.25) 0%, rgba(0, 212, 255, 0.1) 100%);
        }

        .dia-header h5 {
            color: var(--neon-cyan);
            font-weight: 600;
            text-shadow: 0 0 8px rgba(0, 212, 255, 0.4);
            margin: 0;
        }

        .toggle-icon {
            transition: transform 0.3s ease;
            color: var(--neon-cyan);
        }

        .dia-content {
            padding: 1.5rem;
            background: rgba(15, 23, 42, 0.95);
        }

        .dia-collapsed .toggle-icon {
            transform: rotate(180deg);
        }

        /* VALIDACIÓN DE BÚSQUEDA */
        .search-error-message {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.95) 0%, rgba(220, 38, 38, 0.95) 100%);
            color: white;
            padding: 0.75rem 1rem;
            border-radius: 0 0 10px 10px;
            font-size: 0.85rem;
            font-weight: 500;
            z-index: 100;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.5);
            animation: slideDownError 0.3s ease;
            margin-top: 2px;
        }

        .search-success-message {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.95) 0%, rgba(5, 150, 105, 0.95) 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0 0 10px 10px;
            font-size: 0.8rem;
            font-weight: 500;
            z-index: 100;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.5);
            animation: slideDownSuccess 0.3s ease;
            margin-top: 2px;
        }

        @keyframes slideDownError {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideDownSuccess {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-control-dark.error {
            border-color: #ef4444 !important;
            box-shadow: 0 0 20px rgba(239, 68, 68, 0.4) !important;
            animation: shake 0.5s;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
    </style>
@endpush

@push('scripts')
    <script>
        function confirmarEliminacion(id) {
            if (confirm('¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }

        function validarBusquedaAsistencias(input) {
            const valor = input.value.trim();
            const errorDiv = document.getElementById('search-error-asistencias');
            const successDiv = document.getElementById('search-success-asistencias');
            const btnBuscar = document.querySelector('#filterForm button[type="submit"]');
            const caracteresProhibidos = /[<>;'"=\\]/;
            
            if (valor === '') {
                // Campo vacío - estado neutral
                input.classList.remove('error', 'success');
                if (errorDiv) errorDiv.style.display = 'none';
                if (successDiv) successDiv.style.display = 'none';
                if (btnBuscar) btnBuscar.disabled = false;
                return;
            }
            
            if (caracteresProhibidos.test(valor)) {
                // Contiene caracteres peligrosos
                input.classList.add('error');
                input.classList.remove('success');
                if (errorDiv) {
                    errorDiv.style.display = 'block';
                    errorDiv.innerHTML = '<i class="fas fa-exclamation-circle me-1"></i>No se permiten los caracteres: < > ; \' " = \\';
                }
                if (successDiv) successDiv.style.display = 'none';
                if (btnBuscar) btnBuscar.disabled = true;
            } else {
                // Texto válido
                input.classList.remove('error');
                input.classList.add('success');
                if (errorDiv) errorDiv.style.display = 'none';
                if (successDiv) successDiv.style.display = 'block';
                if (btnBuscar) btnBuscar.disabled = false;
                
                // Auto-ocultar mensaje de éxito después de 2 segundos
                setTimeout(() => {
                    if (successDiv) successDiv.style.display = 'none';
                    input.classList.remove('success');
                }, 2000);
            }
        }

        // Validar al enviar el formulario
        document.getElementById('filterForm').addEventListener('submit', function(e) {
            const input = document.getElementById('mainSearch');
            const valor = input.value.trim();
            const caracteresProhibidos = /[<>;'"=\\]/;
            
            if (valor !== '' && caracteresProhibidos.test(valor)) {
                e.preventDefault();
                input.focus();
                validarBusquedaAsistencias(input);
            }
        });

        function toggleAdvancedFilters() {
            const filters = document.getElementById('advancedFilters');
            const toggleBtn = document.getElementById('toggleFiltersBtn');
            
            if (filters.style.display === 'none' || filters.style.display === '') {
                filters.style.display = 'block';
                toggleBtn.innerHTML = '<i class="fas fa-times"></i>';
            } else {
                filters.style.display = 'none';
                toggleBtn.innerHTML = '<i class="fas fa-sliders-h"></i>';
            }
        }

        function clearMainSearch() {
            document.getElementById('mainSearch').value = '';
            document.getElementById('filterForm').submit();
        }

        function removeFilter(filterName) {
            const form = document.getElementById('filterForm');
            const input = form.querySelector(`[name="${filterName}"]`);
            if (input) {
                input.value = '';
            }
            form.submit();
        }

        function clearAllFilters() {
            window.location.href = '{{ route('admin.asistencias.index') }}';
        }

        window.addEventListener('DOMContentLoaded', function() {
            const hasAdvancedFilters = {{ request()->hasAny(['seccion_id', 'periodo_id', 'estado', 'fecha']) ? 'true' : 'false' }};
            if (hasAdvancedFilters) {
                const filters = document.getElementById('advancedFilters');
                const toggleBtn = document.getElementById('toggleFiltersBtn');
                filters.style.display = 'block';
                toggleBtn.innerHTML = '<i class="fas fa-times"></i>';
            }
        });
    </script>
@endpush
@endsection