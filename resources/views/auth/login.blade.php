<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Iniciar Sesión - Sistema de Calificaciones</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #0ea5e9;
            --primary-dark: #0284c7;
            --neon-blue: #00d4ff;
            --neon-cyan: #00ffff;
            --dark-cyan: #0891b2;
            --darker-cyan: #0e7490;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #32baf0ff 0%, #c8c8c9ff 50%, #00ffffff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            overflow: hidden;
            width: 100%;
            max-width: 400px;
            /* Borde celeste oscuro con efecto neón */
            border: 3px solid var(--darker-cyan);
            box-shadow: 
                0 0 20px rgba(8, 145, 178, 0.6),
                0 0 40px rgba(8, 145, 178, 0.4),
                0 0 60px rgba(14, 116, 144, 0.3),
                0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .login-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 2.5rem 2rem;
            text-align: center;
        }

        .login-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            letter-spacing: 1px;
            line-height: 1.3;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .login-header p {
            font-family: 'Inter', sans-serif;
            font-size: 0.95rem;
            font-weight: 400;
            margin: 0;
            opacity: 0.95;
            letter-spacing: 0.5px;
        }

        .login-body {
            padding: 2rem;
        }

        .form-floating input {
            background: rgba(14, 165, 233, 0.05);
            border: 1px solid rgba(14, 165, 233, 0.3);
            border-radius: 10px;
        }

        .form-floating input:focus {
            background: rgba(14, 165, 233, 0.1);
            border-color: var(--neon-blue);
            box-shadow: 0 0 0 0.2rem rgba(0, 212, 255, 0.25);
        }

        .form-floating label {
            color: #64748b;
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(14, 165, 233, 0.4);
            color: white;
        }

        .alert {
            border-radius: 10px;
            border: none;
        }

        .brand-icon {
            font-size: 2.5rem;
            color: var(--neon-cyan);
            text-shadow: 0 0 20px rgba(0, 255, 255, 0.5);
            margin-bottom: 1rem;
        }

        /* Estilo para el botón de mostrar/ocultar contraseña - FORZADO */
        .password-toggle,
        .password-toggle:focus,
        .password-toggle:active,
        .password-toggle:hover,
        .password-toggle:focus-visible,
        button.password-toggle,
        button.password-toggle:focus,
        button.password-toggle:active {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none !important;
            background-color: transparent !important;
            border: none !important;
            color: #64748b;
            cursor: pointer;
            z-index: 10;
            font-size: 1rem;
            transition: color 0.3s ease;
            outline: none !important;
            outline-offset: 0 !important;
            box-shadow: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;
            appearance: none !important;
            -webkit-box-shadow: none !important;
            -moz-box-shadow: none !important;
            -webkit-focus-ring-color: transparent !important;
            -webkit-tap-highlight-color: transparent !important;
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
        }

        .password-toggle:hover {
            color: var(--primary-color) !important;
            background: none !important;
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
        }

        .password-container {
            position: relative;
        }

        /* Responsivo para móviles */
        @media (max-width: 480px) {
            .login-header h1 {
                font-size: 1.2rem;
            }
            .login-container {
                margin: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <i class="fas fa-graduation-cap brand-icon"></i>
            <h1>COLEGIO SECUNDARIO AUGUSTO PULENTA</h1>
            <p>SISTEMA DE CALIFICACIONES</p>
        </div>
        
        <div class="login-body">
            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <strong>Error:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('status'))
                <div class="alert alert-success mb-4">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                
                <div class="form-floating mb-3">
                    <input type="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           id="email" 
                           name="email" 
                           placeholder="correo@ejemplo.com"
                           value="{{ old('email') }}" 
                           required 
                           autocomplete="email" 
                           autofocus>
                    <label for="email">
                        <i class="fas fa-envelope me-2"></i>Correo Electrónico
                    </label>
                    @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="password-container form-floating mb-4">
                    <input type="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           id="password" 
                           name="password" 
                           placeholder="Contraseña"
                           required 
                           autocomplete="current-password">
                    <label for="password">
                        <i class="fas fa-lock me-2"></i>Contraseña
                    </label>
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <i class="fas fa-eye" id="toggleIcon"></i>
                    </button>
                    @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>
                    Iniciar Sesión
                </button>
            </form>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script para mostrar/ocultar contraseña -->
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>