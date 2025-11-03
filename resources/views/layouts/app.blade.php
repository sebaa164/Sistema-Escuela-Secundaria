<!DOCTYPE html>
<html lang="es">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistema de Calificaciones')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
       :root {
    /* Paleta de colores profesional - coherente con login */
    --primary-color: #0ea5e9;
    --primary-dark: #0284c7;
    --primary-light: #38bdf8;
    --accent-color: #0891b2;
    
    /* Colores neutros */
    --white: #ffffff;
    --gray-50: #f8fafc;
    --gray-100: #f1f5f9;
    --gray-200: #e2e8f0;
    --gray-300: #cbd5e1;
    --gray-400: #94a3b8;
    --gray-500: #64748b;
    --gray-600: #475569;
    --gray-700: #334155;
    --gray-800: #1e293b;
    --gray-900: #0f172a;
    
    /* Colores de estado */
    --success: #10b981;
    --success-light: #d1fae5;
    --warning: #f59e0b;
    --warning-light: #fef3c7;
    --danger: #ef4444;
    --danger-light: #fee2e2;
    --info: #3b82f6;
    --info-light: #dbeafe;
    
    /* Sombras minimalistas */
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    
    /* Transiciones */
    --transition: all 0.2s ease;
    
    /* Espaciado */
    --sidebar-width: 260px;
    --header-height: 64px;
}

/* Reset y base */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background-color: var(--gray-50);
    color: var(--gray-800);
    line-height: 1.6;
    min-height: 100vh;
    font-size: 15px;
}

/* ============================================
   SIDEBAR - Minimalista y profesional
   ============================================ */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    width: var(--sidebar-width);
    background-color: var(--white);
    border-right: 1px solid var(--gray-200);
    transform: translateX(-100%);
    transition: transform 0.3s ease;
    z-index: 1000;
    overflow-y: auto;
    overflow-x: hidden;
}

.sidebar::-webkit-scrollbar {
    width: 6px;
}

.sidebar::-webkit-scrollbar-track {
    background: transparent;
}

.sidebar::-webkit-scrollbar-thumb {
    background: var(--gray-300);
    border-radius: 3px;
}

.sidebar.show {
    transform: translateX(0);
}

/* Desktop: sidebar siempre visible */
@media (min-width: 768px) {
    .sidebar {
        transform: translateX(0);
    }
    
    .sidebar.collapsed {
        transform: translateX(-260px);
    }
}

/* Brand del sidebar */
.sidebar-brand {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--gray-200);
    background-color: var(--white);
}

.sidebar-brand h4 {
    color: var(--primary-color);
    font-weight: 600;
    font-size: 1.1rem;
    margin: 0;
    letter-spacing: -0.01em;
}

/* Navegación del sidebar */
.sidebar-nav {
    padding: 1rem 0.75rem;
}

.sidebar-nav .nav-link {
    color: var(--gray-600);
    padding: 0.65rem 1rem;
    border-radius: 8px;
    transition: var(--transition);
    display: flex;
    align-items: center;
    text-decoration: none;
    margin-bottom: 0.25rem;
    font-size: 0.9rem;
    font-weight: 500;
    border: none;
    background: transparent;
}

.sidebar-nav .nav-link:hover {
    background-color: var(--gray-100);
    color: var(--primary-color);
}

.sidebar-nav .nav-link.active {
    background-color: var(--primary-color);
    color: var(--white);
}

.sidebar-nav .nav-link i {
    width: 20px;
    margin-right: 0.75rem;
    text-align: center;
    font-size: 1rem;
}

/* ============================================
   HEADER - Limpio y moderno
   ============================================ */
.header {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: var(--header-height);
    background-color: var(--white);
    border-bottom: 1px solid var(--gray-200);
    z-index: 999;
    display: flex;
    align-items: center;
    padding: 0 1.5rem;
    transition: left 0.3s ease;
}

@media (min-width: 768px) {
    .header {
        left: var(--sidebar-width);
    }
    
    .sidebar.collapsed ~ .header {
        left: 0;
    }
}

.header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.header-left h5 {
    color: var(--gray-700);
    font-size: 1rem;
    font-weight: 600;
    margin: 0;
}

.header-right {
    margin-left: auto;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

/* Toggle del sidebar */
.sidebar-toggle {
    background: transparent;
    border: none;
    font-size: 1.25rem;
    color: var(--gray-600);
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 6px;
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
}

.sidebar-toggle:hover {
    background-color: var(--gray-100);
    color: var(--primary-color);
}

/* Notificaciones */
.notification-bell {
    position: relative;
    background: transparent;
    border: none;
    font-size: 1.25rem;
    color: var(--gray-600);
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 6px;
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
}

.notification-bell:hover {
    background-color: var(--gray-100);
    color: var(--primary-color);
}

.notification-badge {
    position: absolute;
    top: 6px;
    right: 6px;
    background-color: var(--danger);
    color: var(--white);
    font-size: 0.65rem;
    padding: 0.15rem 0.35rem;
    border-radius: 10px;
    min-width: 16px;
    height: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    line-height: 1;
}

/* ============================================
   MAIN CONTENT
   ============================================ */
.main-content {
    margin-top: var(--header-height);
    padding: 1.5rem;
    min-height: calc(100vh - var(--header-height));
    transition: margin-left 0.3s ease;
}

@media (min-width: 768px) {
    .main-content {
        margin-left: var(--sidebar-width);
        padding: 2rem;
    }
    
    .sidebar.collapsed ~ .main-content {
        margin-left: 0;
    }
}

/* ============================================
   DROPDOWN MENUS
   ============================================ */
.dropdown-menu {
    background-color: var(--white);
    border: 1px solid var(--gray-200);
    box-shadow: var(--shadow-lg);
    border-radius: 8px;
    padding: 0.5rem;
    margin-top: 0.5rem;
}

.dropdown-item {
    color: var(--gray-700);
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    transition: var(--transition);
    font-size: 0.9rem;
}

.dropdown-item:hover {
    background-color: var(--gray-100);
    color: var(--primary-color);
}

.dropdown-item:active {
    background-color: var(--gray-200);
}

.dropdown-header {
    color: var(--gray-500);
    font-weight: 600;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 0.5rem 0.75rem 0.25rem;
}

.dropdown-divider {
    border-color: var(--gray-200);
    margin: 0.5rem 0;
}

/* ============================================
   BUTTONS
   ============================================ */
.btn {
    font-weight: 500;
    border-radius: 6px;
    padding: 0.5rem 1rem;
    transition: var(--transition);
    font-size: 0.9rem;
    border: none;
}

.btn-primary {
    background-color: var(--primary-color);
    color: var(--white);
}

.btn-primary:hover {
    background-color: var(--primary-dark);
    color: var(--white);
}

.btn-outline-primary {
    border: 1px solid var(--primary-color);
    color: var(--primary-color);
    background: transparent;
}

.btn-outline-primary:hover {
    background-color: var(--primary-color);
    color: var(--white);
    border-color: var(--primary-color);
}

.btn-success {
    background-color: var(--success);
    color: var(--white);
}

.btn-danger {
    background-color: var(--danger);
    color: var(--white);
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.85rem;
}

.btn-lg {
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
}

/* ============================================
   PAGE HEADER
   ============================================ */
.page-header {
    background-color: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: 10px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.page-header h1 {
    color: var(--gray-900);
    font-weight: 600;
    margin-bottom: 0.25rem;
    font-size: 1.75rem;
}

.page-header p {
    color: var(--gray-500);
    margin: 0;
    font-size: 0.95rem;
}

/* ============================================
   CARDS
   ============================================ */
.card {
    background-color: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: 10px;
    margin-bottom: 1.5rem;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
}

.card:hover {
    box-shadow: var(--shadow-md);
}

.card-header {
    background-color: var(--gray-50);
    color: var(--gray-800);
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--gray-200);
    font-weight: 600;
    border-radius: 10px 10px 0 0;
    font-size: 1rem;
}

.card-body {
    padding: 1.25rem;
}

.card-footer {
    background-color: var(--gray-50);
    border-top: 1px solid var(--gray-200);
    padding: 0.75rem 1.25rem;
}

/* ============================================
   TABLES
   ============================================ */
.table {
    background-color: var(--white);
    color: var(--gray-700);
    border-collapse: separate;
    border-spacing: 0;
}

.table thead th {
    background-color: var(--gray-50);
    color: var(--gray-700);
    border-bottom: 2px solid var(--gray-200);
    font-weight: 600;
    padding: 0.75rem 1rem;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.table tbody td {
    vertical-align: middle;
    padding: 0.875rem 1rem;
    border-bottom: 1px solid var(--gray-200);
    color: var(--gray-700);
    font-size: 0.9rem;
}

.table tbody tr:last-child td {
    border-bottom: none;
}

.table tbody tr:hover {
    background-color: var(--gray-50);
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: rgba(248, 250, 252, 0.5);
}

/* ============================================
   ALERTS
   ============================================ */
.alert {
    border-radius: 8px;
    border: 1px solid;
    padding: 1rem 1.25rem;
    margin-bottom: 1rem;
    font-size: 0.9rem;
}

.alert-success {
    background-color: var(--success-light);
    border-color: var(--success);
    color: #065f46;
}

.alert-danger {
    background-color: var(--danger-light);
    border-color: var(--danger);
    color: #991b1b;
}

.alert-warning {
    background-color: var(--warning-light);
    border-color: var(--warning);
    color: #92400e;
}

.alert-info {
    background-color: var(--info-light);
    border-color: var(--info);
    color: #1e40af;
}

/* ============================================
   BADGES
   ============================================ */
.badge {
    padding: 0.35rem 0.65rem;
    border-radius: 4px;
    font-weight: 500;
    font-size: 0.75rem;
    letter-spacing: 0.02em;
}

.badge-primary {
    background-color: var(--primary-color);
    color: var(--white);
}

.badge-success {
    background-color: var(--success);
    color: var(--white);
}

.badge-danger {
    background-color: var(--danger);
    color: var(--white);
}

.badge-warning {
    background-color: var(--warning);
    color: var(--white);
}

.badge-secondary {
    background-color: var(--gray-500);
    color: var(--white);
}

/* ============================================
   FORMS
   ============================================ */
.form-control,
.form-select {
    border: 1px solid var(--gray-300);
    border-radius: 6px;
    padding: 0.5rem 0.75rem;
    font-size: 0.9rem;
    transition: var(--transition);
}

.form-control:focus,
.form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
}

.form-label {
    font-weight: 500;
    color: var(--gray-700);
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

/* ============================================
   OVERLAY MOBILE
   ============================================ */
.sidebar-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 999;
    transition: opacity 0.3s ease;
}

.sidebar-overlay.show {
    display: block;
}

/* ============================================
   RESPONSIVE
   ============================================ */
@media (max-width: 767px) {
    .header {
        left: 0;
    }
    
    .main-content {
        margin-left: 0;
        padding: 1rem;
    }
    
    .page-header {
        padding: 1rem;
    }
    
    .page-header h1 {
        font-size: 1.5rem;
    }
    
    .card-body {
        padding: 1rem;
    }
    
    .table {
        font-size: 0.85rem;
    }
    
    .table thead th,
    .table tbody td {
        padding: 0.5rem;
    }
}

@media (max-width: 480px) {
    body {
        font-size: 14px;
    }
    
    .page-header h1 {
        font-size: 1.25rem;
    }
    
    .btn {
        padding: 0.5rem 0.75rem;
        font-size: 0.85rem;
    }
}

/* ============================================
   UTILIDADES
   ============================================ */
.btn-close {
    filter: none;
    opacity: 0.6;
}

.btn-close:hover {
    opacity: 1;
}

/* Smooth scroll */
html {
    scroll-behavior: smooth;
}

/* Text utilities */
.text-muted {
    color: var(--gray-500) !important;
}

.text-primary {
    color: var(--primary-color) !important;
}

/* Spacing utilities adicionales si son necesarias */
.mb-4 {
    margin-bottom: 1.5rem;
}

.mt-4 {
    margin-top: 1.5rem;
}
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <h4><i class="fas fa-graduation-cap me-2"></i>EduSystem</h4>
        </div>
        
        <div class="sidebar-nav">
            @auth
                @if(auth()->user()->tipo_usuario === 'administrador')
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i>Dashboard
                    </a>
                    <a href="{{ route('admin.usuarios.index') }}" class="nav-link {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i>Usuarios
                    </a>
                    <a href="{{ route('admin.cursos.index') }}" class="nav-link {{ request()->routeIs('admin.cursos.*') ? 'active' : '' }}">
                        <i class="fas fa-book"></i>Cursos
                    </a>
                    <a href="{{ route('admin.secciones.index') }}" class="nav-link {{ request()->routeIs('admin.secciones.*') ? 'active' : '' }}">
                        <i class="fas fa-layer-group"></i>Secciones
                    </a>
                    <a href="{{ route('admin.periodos.index') }}" class="nav-link {{ request()->routeIs('admin.periodos.*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt"></i>Períodos
                    </a>
                    <a href="{{ route('admin.inscripciones.index') }}" class="nav-link {{ request()->routeIs('admin.inscripciones.*') ? 'active' : '' }}">
                        <i class="fas fa-clipboard-list"></i>Inscripciones
                    </a>
                    <a href="{{ route('admin.evaluaciones.index') }}" class="nav-link {{ request()->routeIs('admin.evaluaciones.*') ? 'active' : '' }}">
                        <i class="fas fa-file-alt"></i>Evaluaciones
                    </a>
                    <a href="{{ route('admin.asistencias.index') }}" class="nav-link {{ request()->routeIs('admin.asistencias.*') ? 'active' : '' }}">
                        <i class="fas fa-clipboard-check"></i>Asistencias
                    </a>
                    <a href="{{ route('admin.calificaciones.index') }}" class="nav-link {{ request()->routeIs('admin.calificaciones.*') ? 'active' : '' }}">
                        <i class="fas fa-star"></i>Calificaciones
                    </a>
                    <a href="{{ route('admin.reportes.index') }}" class="nav-link {{ request()->routeIs('admin.reportes.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar"></i>Reportes
                    </a>
                    <a href="{{ route('admin.configuraciones.index') }}" class="nav-link {{ request()->routeIs('admin.configuraciones.*') ? 'active' : '' }}">
                        <i class="fas fa-cog"></i>Configuración
                    </a>
                    
                @elseif(auth()->user()->tipo_usuario === 'profesor')
                    <a href="{{ route('profesor.dashboard') }}" class="nav-link {{ request()->routeIs('profesor.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i>Dashboard
                    </a>
                    <a href="{{ route('profesor.secciones.index') }}" class="nav-link {{ request()->routeIs('profesor.secciones.*') ? 'active' : '' }}">
                        <i class="fas fa-chalkboard-teacher"></i>Mis Secciones
                    </a>
                    <a href="{{ route('profesor.evaluaciones.index') }}" class="nav-link {{ request()->routeIs('profesor.evaluaciones.*') ? 'active' : '' }}">
                        <i class="fas fa-file-alt"></i>Evaluaciones
                    </a>
                    <a href="{{ route('profesor.asistencias.index') }}" class="nav-link {{ request()->routeIs('profesor.asistencias.*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-check"></i>Asistencias
                    </a>
                    <a href="{{ route('profesor.evaluaciones.index') }}" class="nav-link {{ request()->routeIs('profesor.calificaciones.*') ? 'active' : '' }}">
                        <i class="fas fa-star"></i>Calificaciones
                    </a>
                    <a href="{{ route('profesor.horario.index') }}" class="nav-link {{ request()->routeIs('profesor.horario.*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-week"></i>Mi Horario
                    </a>
                    <a href="{{ route('profesor.mensajes.index') }}" class="nav-link {{ request()->routeIs('profesor.mensajes.*') ? 'active' : '' }}">
                        <i class="fas fa-comments"></i>Mensajes
                    </a>
                    <a href="{{ route('profesor.reportes.index') }}" class="nav-link {{ request()->routeIs('profesor.reportes.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar"></i>Reportes
                    </a>
                    <a href="{{ route('profesor.configuraciones.index') }}" class="nav-link {{ request()->routeIs('profesor.configuraciones.*') ? 'active' : '' }}">
                        <i class="fas fa-cog"></i>Configuración
                    </a>
                    <a href="{{ route('profesor.perfil.index') }}" class="nav-link {{ request()->routeIs('profesor.perfil.*') ? 'active' : '' }}">
                        <i class="fas fa-user-circle"></i>Mi Perfil
                    </a>
                    
                @elseif(auth()->user()->tipo_usuario === 'estudiante')
                    <a href="{{ route('estudiante.dashboard') }}" class="nav-link {{ request()->routeIs('estudiante.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i>Dashboard
                    </a>
                    <a href="{{ route('estudiante.inscripciones.index') }}" class="nav-link {{ request()->routeIs('estudiante.inscripciones.*') ? 'active' : '' }}">
                        <i class="fas fa-clipboard-list"></i>Mis Inscripciones
                    </a>
                    <a href="{{ route('estudiante.calificaciones.index') }}" class="nav-link {{ request()->routeIs('estudiante.calificaciones.*') ? 'active' : '' }}">
                        <i class="fas fa-star"></i>Calificaciones
                    </a>
                    <a href="{{ route('estudiante.asistencias.index') }}" class="nav-link {{ request()->routeIs('estudiante.asistencias.*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-check"></i>Asistencias
                    </a>
                    <a href="{{ route('estudiante.horario.index') }}" class="nav-link {{ request()->routeIs('estudiante.horario.*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-week"></i>Mi Horario
                    </a>
                    <a href="{{ route('estudiante.tareas.index') }}" class="nav-link {{ request()->routeIs('estudiante.tareas.*') ? 'active' : '' }}">
                        <i class="fas fa-tasks"></i>Tareas
                    </a>
                    <a href="{{ route('estudiante.mensajes.index') }}" class="nav-link {{ request()->routeIs('estudiante.mensajes.*') ? 'active' : '' }}">
                        <i class="fas fa-comments"></i>Mensajes
                    </a>
                    <a href="{{ route('estudiante.perfil.index') }}" class="nav-link {{ request()->routeIs('estudiante.perfil.*') ? 'active' : '' }}">
                        <i class="fas fa-user-circle"></i>Mi Perfil
                    </a>
                @endif
            @endauth
        </div>
    </nav>

    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Header -->
    <header class="header">
        <div class="header-left">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <h5 class="mb-0 text-primary">@yield('page-title', 'Sistema de Calificaciones')</h5>
        </div>
        
        <div class="header-right">
            @auth
                <!-- Notification Bell -->
                <div class="dropdown">
                    <button class="notification-bell" data-bs-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">3</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li><h6 class="dropdown-header">Notificaciones</h6></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-info-circle me-2 text-info"></i>Nueva calificación disponible</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-calendar me-2 text-warning"></i>Evaluación programada</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-center text-primary" href="#">Ver todas</a></li>
                    </ul>
                </div>
                
                <!-- User Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-2"></i>{{ auth()->user()->nombre }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li><a class="dropdown-item" href="{{ auth()->user()->tipo_usuario === 'profesor' ? route('profesor.perfil.index') : '#' }}"><i class="fas fa-user me-2"></i>Mi Perfil</a></li>
                        <li><a class="dropdown-item" href="{{ auth()->user()->tipo_usuario === 'profesor' ? route('profesor.configuraciones.index') : '#' }}"><i class="fas fa-cog me-2"></i>Configuración</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endauth
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
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

        @yield('content')
    </main>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Sidebar Toggle - Animación suave y natural
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const mainContent = document.querySelector('.main-content');
            const header = document.querySelector('.header');
            
            if (window.innerWidth < 768) {
                // MÓVIL: Toggle sidebar y overlay
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            } else {
                // DESKTOP: Toggle suave con transiciones
                const isCurrentlyCollapsed = sidebar.classList.contains('collapsed');
                
                if (isCurrentlyCollapsed) {
                    // Expandir sidebar
                    sidebar.classList.remove('collapsed');
                    
                    // Usar requestAnimationFrame para animación más suave
                    requestAnimationFrame(() => {
                        mainContent.style.marginLeft = '280px';
                        header.style.left = '280px';
                    });
                } else {
                    // Colapsar sidebar
                    sidebar.classList.add('collapsed');
                    
                    requestAnimationFrame(() => {
                        mainContent.style.marginLeft = '0px';
                        header.style.left = '0px';
                    });
                }
            }
        });

        // Close sidebar when clicking overlay (solo móvil)
        document.getElementById('sidebarOverlay').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });

        // Manejar resize de ventana con debounce para mejor performance
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                const mainContent = document.querySelector('.main-content');
                const header = document.querySelector('.header');
                
                if (window.innerWidth >= 768) {
                    // Desktop
                    overlay.classList.remove('show');
                    sidebar.classList.remove('show');
                    
                    if (sidebar.classList.contains('collapsed')) {
                        mainContent.style.marginLeft = '0px';
                        header.style.left = '0px';
                    } else {
                        mainContent.style.marginLeft = '280px';
                        header.style.left = '280px';
                    }
                } else {
                    // Móvil
                    sidebar.classList.remove('collapsed');
                    mainContent.style.marginLeft = '0px';
                    header.style.left = '0px';
                    
                    if (sidebar.classList.contains('show')) {
                        overlay.classList.add('show');
                    } else {
                        overlay.classList.remove('show');
                    }
                }
            }, 250);
        });

        // Inicializar estado del sidebar
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.main-content');
            const header = document.querySelector('.header');
            
            if (window.innerWidth >= 768) {
                // Desktop: mostrar sidebar expandido por defecto
                sidebar.classList.remove('collapsed');
                sidebar.classList.remove('show');
                mainContent.style.marginLeft = '280px';
                header.style.left = '280px';
            } else {
                // Móvil: ocultar sidebar por defecto
                sidebar.classList.remove('show');
                sidebar.classList.remove('collapsed');
                mainContent.style.marginLeft = '0px';
                header.style.left = '0px';
            }
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
    
    @stack('scripts')
</body>
</html>