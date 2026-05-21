<?php
// 1. Subimos un nivel para encontrar el config
require_once dirname(__DIR__) . '/config/config.php';// 2. ERROR CORREGIDO: No uses BASE_URL aquí. 
// Usa la ruta física relativa al archivo actual.
require_once ROOT_PATH . 'controllers/GastoController.php';

$controller = new GastoController();

if($_GET['tipo']== 'datatable'){
    $controller->datatable_gastos();
}

if($_GET['tipo']== 'nuevo_gasto'){
    $controller->registrar_gasto($_POST,1);
}

if($_GET['tipo']== 'actualizar_gasto'){
    $controller->actualizar_gasto($_POST,1);
}

if($_GET['tipo']=='actualizar_estatus_gasto'){
   $controller->cancelar_gasto($_POST, 1);
}
?>