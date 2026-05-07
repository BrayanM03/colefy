<?php
require_once __DIR__ . '/../controllers/ProfesorController.php';

$controller = new ProfesorController();
$tipo = $_GET['tipo'];

if($tipo =='datatable'){
    $controller->datatable();
}

if($tipo =='registrar'){
    $controller->registrar_profesor(1, $_POST);
}