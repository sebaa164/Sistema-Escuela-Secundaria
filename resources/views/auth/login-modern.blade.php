<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar Sesión - I.E.S Normal Superior</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            /* Colores neón azules */
            --primary-blue: #00D4FF;
            --primary-dark-blue: #0099CC;
            --accent-blue: #00FFFF;
            --neon-purple: #7B2FFF;
            --neon-pink: #FF006E;

            /* Neutros nuevos */
            --gray-900: #0a0a0a;
            --gray-800: #1a1a1a;
            --gray-700: #2a2a2a;
            --gray-600: #3a3a3a;
            --gray-500: #555;
            --gray-400: #888;
            --gray-300: #b0b0b0;
            --gray-200: #d0d0d0;
            --gray-100: #e7e7e7;

            /* Fondos */
            --bg-main: #0f0f0f;
            --bg-soft: #1a1a1a;
            --bg-card: #2a2a2a;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
            background: linear-gradient(135deg, var(--gray-900), var(--gray-800));
        }

        /* =============== LAYOUT PRINCIPAL =============== */
        .login-wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* =============== HERO SECTION (IZQUIERDA) =============== */
        .hero-section {
            flex: 1;
            background: linear-gradient(135deg, var(--primary-dark-blue) 0%, var(--primary-blue) 50%, var(--accent-blue) 100%);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 4rem;
            color: white;
            box-shadow: 0 0 50px rgba(0, 212, 255, 0.3);
        }

        .hero-pattern {
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(circle at 20% 50%, rgba(0, 255, 255, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(123, 47, 255, 0.15) 0%, transparent 50%),
                linear-gradient(45deg, transparent 30%, rgba(0, 212, 255, 0.1) 50%, transparent 70%);
            pointer-events: none;
            animation: neonPulse 4s ease-in-out infinite;
        }

        @keyframes neonPulse {
            0%, 100% { opacity: 0.8; }
            50% { opacity: 1; }
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 600px;
        }

        .hero-logo {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 3rem;
        }

        .hero-logo-icon {
            width: 60px;
            height: 60px;
            background: rgba(0, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(0, 255, 255, 0.3);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            box-shadow: 0 0 20px rgba(0, 255, 255, 0.5);
            animation: neonGlow 2s ease-in-out infinite alternate;
        }

        @keyframes neonGlow {
            from { box-shadow: 0 0 20px rgba(0, 255, 255, 0.5); }
            to { box-shadow: 0 0 30px rgba(0, 255, 255, 0.8), 0 0 40px rgba(0, 212, 255, 0.4); }
        }

        .hero-logo-text h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            line-height: 1.3;
        }

        .hero-logo-text p {
            font-size: 0.875rem;
            opacity: 0.85;
            margin: 0;
        }

        .slide {
            display: none;
            animation: fadeIn 0.6s ease-in-out;
        }

        .slide.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .slide h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 2.75rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1.5rem;
            text-shadow: 0 0 20px rgba(0, 255, 255, 0.5);
        }

        .slide p {
            font-size: 1.1rem;
            line-height: 1.6;
            opacity: 0.95;
            margin-bottom: 2rem;
        }

        .slide-features {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: rgba(0, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 255, 255, 0.2);
            padding: 1rem 1.5rem;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .feature-item:hover {
            background: rgba(0, 255, 255, 0.15);
            border-color: rgba(0, 255, 255, 0.4);
            box-shadow: 0 0 15px rgba(0, 255, 255, 0.3);
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            background: rgba(0, 255, 255, 0.2);
            border: 1px solid rgba(0, 255, 255, 0.3);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 0 10px rgba(0, 255, 255, 0.3);
        }

        .feature-text h4 {
            font-size: 1.125rem;
            font-weight: 600;
            margin: 0 0 0.25rem 0;
        }

        .feature-text p {
            font-size: 0.875rem;
            margin: 0;
            opacity: 0.9;
        }

        .slider-dots {
            display: flex;
            gap: 0.5rem;
            margin-top: 2rem;
        }

        .dot {
            width: 40px;
            height: 4px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 2px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .dot.active {
            background: rgba(255, 255, 255, 0.95);
            width: 60px;
        }

        /* =============== LOGIN SECTION (DERECHA) =============== */
        .login-section {
            flex: 0 0 480px;
            background: var(--bg-soft);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem;
            box-shadow: -10px 0 30px rgba(0, 0, 0, 0.3);
            border-left: 1px solid rgba(0, 255, 255, 0.2);
        }

        .login-container {
            max-width: 400px;
            margin: 0 auto;
            background: var(--bg-card);
            padding: 2.5rem 2rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(0, 255, 255, 0.1);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary-blue);
            text-shadow: 0 0 10px rgba(0, 212, 255, 0.3);
        }

        .login-header p {
            color: var(--gray-500);
            font-size: 0.9375rem;
        }

        .form-label {
            color: var(--primary-blue);
            font-weight: 500;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            text-shadow: 0 0 5px rgba(0, 212, 255, 0.2);
        }

        .form-control-modern {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid rgba(0, 255, 255, 0.2);
            border-radius: 12px;
            font-size: 0.9375rem;
            color: #fff;
            background: var(--bg-soft);
            transition: all 0.3s ease;
        }

        .form-control-modern:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 4px rgba(0, 212, 255, 0.25);
            background: rgba(0, 212, 255, 0.05);
        }

        .password-toggle {
            position: relative;
        }

        .toggle-btn {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: none;
            color: var(--primary-blue);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .toggle-btn:hover {
            color: var(--accent-blue);
            text-shadow: 0 0 10px rgba(0, 255, 255, 0.5);
        }

        .btn-login {
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary-dark-blue), var(--primary-blue));
            color: white;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0, 212, 255, 0.25);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 212, 255, 0.4), 0 0 30px rgba(0, 255, 255, 0.2);
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(0, 255, 255, 0.3), transparent);
            transition: left 0.5s ease;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .alert-modern {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            font-size: 0.875rem;
        }

        .alert-danger {
            background: rgba(255, 0, 110, 0.1);
            color: var(--neon-pink);
            border: 1px solid rgba(255, 0, 110, 0.3);
        }

        .alert-success {
            background: rgba(0, 255, 255, 0.1);
            color: var(--accent-blue);
            border: 1px solid rgba(0, 255, 255, 0.3);
        }

        @media (max-width: 768px) {
            .login-wrapper {
                flex-direction: column;
            }
            .hero-section { min-height: 40vh; }
            .login-section { padding: 2rem 1.5rem; }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="hero-section">
            <div class="hero-pattern"></div>
            <div class="hero-content">
                <div class="hero-logo">
                    <div class="hero-logo-icon"><i class="fas fa-graduation-cap"></i></div>
                    <div class="hero-logo-text">
                        <h1>I.E.S Normal<br>Superior</h1>
                        <p>Sistema de Gestión Académica</p>
                    </div>
                </div>
                <div class="hero-slider">
                    <div class="slide active" data-slide="1">
                        <h2>Bienvenido a tu Portal Educativo</h2>
                        <p>Gestiona tus calificaciones, asistencias y actividades académicas en un solo lugar.</p>
                    </div>
                    <div class="slide" data-slide="2">
                        <h2>Para Profesores</h2>
                        <p>Herramientas potentes para gestionar tus clases de manera eficiente.</p>
                    </div>
                    <div class="slide" data-slide="3">
                        <h2>Administración Integral</h2>
                        <p>Control total del sistema académico desde una sola plataforma.</p>
                    </div>
                </div>
                <div class="slider-dots">
                    <div class="dot active" data-slide="1"></div>
                    <div class="dot" data-slide="2"></div>
                    <div class="dot" data-slide="3"></div>
                </div>
            </div>
        </div>

        <div class="login-section">
            <div class="login-container">
                <div class="login-header">
                    <h2>Iniciar Sesión</h2>
                    <p>Ingresa tus credenciales para acceder</p>
                </div>

                @if ($errors->any())
                    <div class="alert-modern alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <div>
                            <strong>Error:</strong>
                            <ul style="margin: 0.5rem 0 0 0; padding-left: 1.25rem;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                @if (session('status'))
                    <div class="alert-modern alert-success">
                        <i class="fas fa-check-circle"></i>
                        <div>{{ session('status') }}</div>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}">
                    @csrf
                    <div class="form-group mb-3">
                        <label class="form-label" for="email"><i class="fas fa-envelope me-2"></i>Correo Electrónico</label>
                        <input type="email" id="email" name="email" placeholder="correo@ejemplo.com" class="form-control-modern" required>
                    </div>

                    <div class="form-group mb-4">
                        <label class="form-label" for="password"><i class="fas fa-lock me-2"></i>Contraseña</label>
                        <div class="password-toggle">
                            <input type="password" id="password" name="password" placeholder="••••••••" class="form-control-modern" required>
                            <button type="button" class="toggle-btn" onclick="togglePassword()">
                                <i class="fas fa-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            input.type = input.type === 'password' ? 'text' : 'password';
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        }

        let currentSlide = 1;
        const totalSlides = 3;
        const slideInterval = 6000;
        function showSlide(slideNumber) {
            document.querySelectorAll('.slide').forEach(s => s.classList.remove('active'));
            document.querySelector(`.slide[data-slide="${slideNumber}"]`).classList.add('active');
            document.querySelectorAll('.dot').forEach(d => d.classList.remove('active'));
            document.querySelector(`.dot[data-slide="${slideNumber}"]`).classList.add('active');
        }
        setInterval(() => {
            currentSlide = currentSlide >= totalSlides ? 1 : currentSlide + 1;
            showSlide(currentSlide);
        }, slideInterval);
        document.querySelectorAll('.dot').forEach(dot => {
            dot.addEventListener('click', function() {
                showSlide(parseInt(this.getAttribute('data-slide')));
            });
        });
    </script>
</body>
</html>
