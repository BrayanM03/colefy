<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - Página no encontrada</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
        }
        .error-container {
            text-align: center;
            max-width: 400px;
            padding: 20px;
            border-radius: 15px;
            background: white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .error-logo {
            width: 120px;
            height: auto;
            margin-bottom: 20px;
        }
        .btn-home {
            margin-top: 15px;
        }
    </style>
</head>
<body>
<?php
$msg = $_GET['msg'] ?? 'Ha ocurrido un error inesperado.';
?>
    <div class="error-container">
        <img src="../../img/logo_2.png" alt="Logo" class="error-logo">
        <h3 class="text-danger fw-bold">¡Ups! Algo salió mal</h3>
        <p><?php echo htmlspecialchars($msg); ?></p>
        <a href="../../index.php" class="btn btn-info btn-home">Volver al inicio</a>
    </div>

    <!-- Bootstrap JS (opcional si lo necesitas) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
