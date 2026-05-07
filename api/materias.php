<?php
require_once __DIR__ . '/../controllers/MateriaController.php';

$controller = new MateriaController();
$tipo = $_GET['tipo'];

if($tipo =='datatable'){
    $controller->datatable();
}

if($tipo =='registrar'){
    $controller->registrar_materia(1, $_POST);
}