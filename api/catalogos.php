<?php
// 1. Subimos un nivel para encontrar el config
require_once dirname(__DIR__) . '/config/config.php';// 2. ERROR CORREGIDO: No uses BASE_URL aquí. 
// Usa la ruta física relativa al archivo actual.
require_once ROOT_PATH . 'controllers/CatalogoController.php';

$controller = new CatalogoController();

if($_GET['tipo']== 'iniciar_flujo'){
    $data = json_decode(file_get_contents('php://input'), true);
    $controller->iniciar_flujo(1, $data);
}


if($_GET['tipo']== 'segundo_paso'){
    $data = json_decode(file_get_contents('php://input'), true);
    $controller->segundo_paso_flujo(1, $data);
}
