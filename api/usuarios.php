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

if($tipo == 'actualizar_foto_perfil'){
    $controller->cambiar_foto_perfil();

}

if($tipo == 'guardar_datos_generales'){
    $controller->actualizar_datos_generales(1, $_POST);
}

if($tipo == 'cambiar_contraseña'){
    $controller->cambiar_contrasena_perfil(1);
}


if($tipo == 'registrar'){
    $controller->registrar_usuario(1, $_POST);
}



?>