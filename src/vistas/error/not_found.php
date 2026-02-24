<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determinamos el destino y el texto del botón
if (isset($_SESSION['id'])) {
    $url_destino = ""; // O la ruta de tu Dashboard
    $texto_boton = "Volver al Dashboard";
} else {
    $url_destino = "login.php";
    $texto_boton = "Ir al Inicio de Sesión";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página no encontrada | Colefy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-container {
            text-align: center;
            max-width: 500px;
            padding: 20px;
        }
        .logo-error {
            max-width: 180px;
            margin-bottom: 30px;
        }
        .error-code {
            font-size: 10rem;
            font-weight: 800;
            color: #1a2b4c; /* Azul oscuro del logo */
            line-height: 1;
            margin-bottom: 10px;
        }
        .error-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 15px;
        }
        .error-text {
            color: #7f8c8d;
            margin-bottom: 30px;
        }
        .btn-colefy {
            background-color: #34495e; /* Ajustado al tono del libro izquierdo */
            color: white;
            padding: 12px 30px;
            border-radius: 12px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
            border: none;
        }
        .btn-colefy:hover {
            background-color: #1a2b4c;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            color: white;
        }
        .signal-icon {
            color: #5bb4a5; /* Verde del logo */
            font-size: 3rem;
        }
    </style>
</head>
<body>

    <div class="error-container animate__animated animate__fadeIn">
        <img src="<?php echo STATIC_URL; ?>img/logo.png" alt="Colefy Logo" class="brand-logo img-fluid mb-5" width="260" />
        
        <div class="error-code">404</div>
        <h1 class="error-title">¡Ups! Parece que te has perdido.</h1>
        <p class="error-text">
            La página que buscas no existe o ha sido movida. 
            No te preocupes, el sistema de gestión sigue bajo control.
        </p>
        
        <a href="<?php echo BASE_URL . $url_destino; ?>" class="btn-colefy">
    <i class="fas <?php echo isset($_SESSION['id']) ? 'fa-th-large' : 'fa-sign-in-alt'; ?> me-2"></i> 
    <?php echo $texto_boton; ?>
</a>
    </div>

</body>
</html>