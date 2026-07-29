<?php
// 1. Subimos un nivel para encontrar el config
require_once dirname(__DIR__) . '/config/config.php';// 2. ERROR CORREGIDO: No uses BASE_URL aquí. 
// Usa la ruta física relativa al archivo actual.
require_once ROOT_PATH . 'controllers/AsistenciaController.php';

$controller = new AsistenciaController();
if($_GET['tipo']== 'datatable'){
    //$controller->datatable();
}

if($_GET['tipo']== 'guardar'){
    $data = json_decode(file_get_contents('php://input'), true);
    $controller->guardar_asistencia($data, 1);
    
}

if($_GET['tipo']== 'obtener_semanal'){
    $data = json_decode(file_get_contents('php://input'), true);
    $id_grupo = $_GET['id_grupo'] ?? null;
    $fecha_inicio = $_GET['fecha_inicio'] ?? null; // Será un Lunes (ej. 2026-07-13)
    $fecha_fin = $_GET['fecha_fin'] ?? null;       // Será un Viernes (ej. 2026-07-17)
    
    $controller->obtener_semanal(1, $id_grupo, $fecha_inicio, $fecha_fin);
}

if($_GET['tipo'] == 'registrar_qr'){
    // Recibimos los datos del escáner (ej. matrícula, id_grupo, id_dh, etc.)
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Asumiendo que obtienes estas variables de tu sesión activa
    $id_escuela = $_SESSION['id_escuela'];
    $id_sesion  = $_SESSION['id_usuario'];
    
    $controller->registrar_qr($data, $id_escuela, $id_sesion); 
}