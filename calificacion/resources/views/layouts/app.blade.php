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
            --primary-color: #0e99e9ff;
            --primary-dark: #0284c7;
            --neon-blue: #00d4ff;
            --neon-cyan: #00ffff;
            --sidebar-bg: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            --header-bg: rgba(15, 15, 42, 0.95);
            --dark-bg: #0a1929; /* Deep petroleum blue */
            --dark-bg-secondary: #1b263b; /* Medium petroleum */
            --dark-bg-tertiary: #415a77; /* Light petroleum */
            --transition-duration: 0.4s;
            --transition-timing: cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--dark-bg) 0%, var(--dark-bg-secondary) 50%, var(--dark-bg-tertiary) 100%);
            background-attachment: fixed;
            color: #e2e8f0;
            line-height: 1.6;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 280px;
            background: var(--sidebar-bg);
            color: white;
            transform: translateX(-100%);
            transition: transform var(--transition-duration) var(--transition-timing);
            z-index: 1000;
            overflow-y: auto;
            overflow-x: hidden;
            box-shadow: 4px 0 20px rgba(0, 212, 255, 0.2);
            border-right: 1px solid rgba(0, 212, 255, 0.3);
        }

        /* Para móvil: mostrar/ocultar completamente */
        .sidebar.show {
            transform: translateX(0);
        }

        /* Para desktop: siempre visible por defecto */
        @media (min-width: 768px) {
            .sidebar {
                transform: translateX(0) !important; /* Siempre visible en desktop */
            }
            
            /* Cuando está colapsado en desktop, se oculta */
            .sidebar.collapsed {
                transform: translateX(-280px) !important;
            }
        }

        .sidebar-brand {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(0, 212, 255, 0.3);
        }

        .sidebar-brand h4 {
            color: var(--neon-blue);
            font-weight: 700;
            text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
            margin: 0;
        }

        .sidebar-nav {
            padding: 1rem 0;
        }

        .sidebar-nav .nav-link {
            color: #cbd5e1;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            text-decoration: none;
            margin: 0.25rem 0;
        }

        .sidebar-nav .nav-link:hover {
            background: rgba(0, 212, 255, 0.1);
            color: var(--neon-blue);
            box-shadow: inset 3px 0 0 var(--neon-blue);
        }

        .sidebar-nav .nav-link.active {
            background: rgba(0, 212, 255, 0.2);
            color: var(--neon-cyan);
            box-shadow: inset 3px 0 0 var(--neon-cyan);
        }

        .sidebar-nav .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
            text-align: center;
        }

        /* Header Styles */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 70px;
            background: var(--header-bg);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 212, 255, 0.3);
            z-index: 999;
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 212, 255, 0.2);
            transition: left var(--transition-duration) var(--transition-timing);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .header-left h5 {
            color: var(--neon-cyan) !important;
        }

        .header-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .sidebar-toggle {
            background: none;
            border: none;
            font-size: 1.25rem;
            color: var(--neon-cyan);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .sidebar-toggle:hover {
            background: rgba(0, 212, 255, 0.2);
            transform: scale(1.05);
            box-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
        }

        /* Notification Bell */
        .notification-bell {
            position: relative;
            background: none;
            border: none;
            font-size: 1.25rem;
            color: var(--neon-cyan);
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .notification-bell:hover {
            background: rgba(0, 212, 255, 0.2);
            transform: scale(1.1);
            box-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
        }

        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            background: linear-gradient(45deg, #ff0080, var(--neon-cyan));
            color: white;
            font-size: 0.75rem;
            padding: 0.2rem 0.4rem;
            border-radius: 50px;
            min-width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(255, 0, 128, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(255, 0, 128, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 0, 128, 0); }
        }

        /* Main Content */
        .main-content {
            margin-top: 70px;
            padding: 2rem;
            min-height: calc(100vh - 70px);
            background: transparent;
            transition: margin-left var(--transition-duration) var(--transition-timing),
                        transform var(--transition-duration) var(--transition-timing);
            will-change: margin-left, transform;
        }

        /* Efecto suave en los elementos internos */
        .main-content > * {
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        /* Dropdown menus */
        .dropdown-menu {
            background: var(--dark-bg-secondary);
            border: 1px solid rgba(0, 212, 255, 0.3);
            box-shadow: 0 0 20px rgba(0, 212, 255, 0.2);
        }

        .dropdown-item {
            color: #e2e8f0;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background: rgba(0, 212, 255, 0.1);
            color: var(--neon-cyan);
        }

        .dropdown-header {
            color: var(--neon-cyan);
            font-weight: 600;
        }

        .dropdown-divider {
            border-color: rgba(0, 212, 255, 0.2);
        }

        /* User Dropdown Button */
        .btn-outline-primary {
            border-color: var(--neon-cyan);
            color: var(--neon-cyan);
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: rgba(0, 212, 255, 0.2);
            border-color: var(--neon-cyan);
            color: var(--neon-cyan);
            box-shadow: 0 0 15px rgba(0, 212, 255, 0.3);
        }

        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, rgba(14, 165, 233, 0.2) 0%, rgba(0, 212, 255, 0.1) 100%);
            border: 1px solid rgba(0, 212, 255, 0.3);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .page-header h1 {
            color: var(--neon-cyan);
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .page-header p {
            color: #94a3b8;
            margin: 0;
        }

        /* Cards - Mantener transparente para que no opaque */
        .card {
            background: transparent;
            border: none;
            box-shadow: none;
            margin-bottom: 1.5rem;
        }

        .card-header {
            background: transparent;
            color: inherit;
            border-radius: 0;
            padding: 1rem 1.5rem;
            border-bottom: none;
            font-weight: 600;
        }

        /* Buttons */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(14, 165, 233, 0.4);
        }

        .btn-neon {
            background: linear-gradient(135deg, var(--neon-blue) 0%, var(--neon-cyan) 100%);
            border: none;
            color: #0f172a;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-neon:hover {
            transform: translateY(-1px);
            box-shadow: 0 0 20px rgba(0, 212, 255, 0.5);
            color: #0f172a;
        }

        /* Alerts */
        .alert {
            border-radius: 10px;
            border: 1px solid;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border-color: #10b981;
            color: #6ee7b7;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border-color: #ef4444;
            color: #fca5a5;
        }

        /* Tables - Mantener transparente */
        .table {
            background: transparent;
            color: inherit;
        }

        .table thead th {
            background: transparent;
            color: inherit;
            border: none;
            font-weight: 600;
            padding: 1rem;
        }

        .table tbody td {
            vertical-align: middle;
            padding: 1rem;
            background: transparent;
            color: inherit;
        }

        .table tbody tr:hover {
            background: transparent;
        }

        /* Badges */
        .badge {
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            font-weight: 500;
        }

        /* Close button for alerts */
        .btn-close {
            filter: brightness(0) invert(1);
        }

        /* Responsive */
        @media (min-width: 768px) {
            .main-content {
                margin-left: 280px;
            }
            
            .header {
                left: 280px;
            }
        }

        /* Overlay for mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 999;
            transition: opacity var(--transition-duration) ease;
        }

        .sidebar-overlay.show {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @media (max-width: 767px) {
            .header {
                left: 0;
            }
            
            .main-content {
                margin-left: 0;
            }
        }

        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }

        /* Hardware acceleration for better performance */
        .sidebar,
        .header,
        .main-content {
            -webkit-transform: translateZ(0);
            -moz-transform: translateZ(0);
            -ms-transform: translateZ(0);
            -o-transform: translateZ(0);
            transform: translateZ(0);
            -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
            -webkit-perspective: 1000;
            perspective: 1000;
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