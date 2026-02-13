<?php
require_once __DIR__ . '/../controllers/UsuarioController.php';


$controller = new UsuarioController();
$tipo = $_GET['tipo'];

if($tipo == 'obtener_usuario'){
 $controller->obtener_usuario($_SESSION['id']);
}

if($tipo == 'datatable'){
    $controller->datatable_usuarios();
}



?>