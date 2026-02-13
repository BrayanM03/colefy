<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="<?php echo STATIC_URL; ?>img/icons/icon-48x48.png" />

    <title>Iniciar sesión | Colefy</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?php echo STATIC_URL; ?>css/app.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    
    <style>
        :root {
            --colefy-navy: #1e3a5f;
            --colefy-teal: #26a69a;
            --colefy-accent: #f9b115;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: url('<?php echo STATIC_URL; ?>img/background_2.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        /* Capa de desenfoque sobre el fondo */
        body::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(30, 58, 95, 0.4); /* Tinte azul marino */
            backdrop-filter: blur(8px);
            z-index: 0;
        }

        .login-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            padding: 2.5rem;
        }

        .form-control {
            border-radius: 12px;
            padding: 12px 16px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--colefy-teal);
            box-shadow: 0 0 0 4px rgba(38, 166, 154, 0.15);
        }

        .btn-colefy {
            background-color: var(--colefy-navy);
            color: white;
            border-radius: 12px;
            padding: 14px;
            font-weight: 600;
            width: 100%;
            border: none;
            transition: all 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .btn-colefy:hover {
            background-color: #152a45;
            transform: translateY(-1px);
            box-shadow: 0 10px 15px -3px rgba(30, 58, 95, 0.3);
            color: white;
        }

        /* Preloader modernizado */
        .preloader {
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 0.8s ease-in-out infinite;
            display: none; /* Se activa por JS */
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .brand-logo {
            transition: transform 0.3s ease;
        }
        
        .brand-logo:hover {
            transform: scale(1.05);
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="text-center mb-6 animate__animated animate__fadeInDown">
            <h1 class="h2 text-white fw-bold mb-1">¡Hola de nuevo!</h1>
            <p class="text-white-50">Ingresa tus credenciales para continuar</p>
        </div>

        <div class="glass-card animate__animated animate__zoomIn">
            <div class="text-center mb-8">
                <img src="<?php echo STATIC_URL; ?>img/logo.png" alt="Colefy Logo" class="brand-logo img-fluid" width="160" />
            </div>

            <form>
                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary">Usuario</label>
                    <input class="form-control animate__animated" type="text" name="user" id="user" placeholder="nombre.usuario" required />
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary">Contraseña</label>
                    <input class="form-control animate__animated" type="password" name="pass" id="pass" placeholder="••••••••" required />
                </div>

                <div class="text-center mt-6">
                    <a id="btn-login" 
                       href="javascript:void(0)" 
                       class="btn-colefy animate__animated" 
                       onclick="iniciarSesion()">
                        <span>Entrar al sistema</span>
                        <div class="preloader" id="login-loader"></div>
                    </a>
                </div>
            </form>
        </div>
        
        <div class="text-center mt-4 animate__animated animate__fadeInUp animate__delay-1s">
            <p class="text-white-50 small">
                &copy; <?php echo date('Y'); ?> Colefy - Sistema de Gestión Escolar
            </p>
        </div>
    </div>

    <script>
        const BASE_URL = "<?php echo BASE_URL; ?>";
        const API_URL = "<?php echo STATIC_URL; ?>api/"; 
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="<?php echo STATIC_URL; ?>js/app.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?php echo STATIC_URL; ?>js/login/login.js"></script>

    <script>
        function toggleLoader() {
            const loader = document.getElementById('login-loader');
            loader.style.display = (loader.style.display === 'none' || loader.style.display === '') ? 'block' : 'none';
        }
    </script>
</body>
</html>