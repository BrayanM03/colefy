<?php
// 1. Subimos un nivel para encontrar el config
require_once dirname(__DIR__) . '/config/config.php';// 2. ERROR CORREGIDO: No uses BASE_URL aquí. 
// Usa la ruta física relativa al archivo actual.
require_once ROOT_PATH . 'controllers/EscuelaController.php';

$controller = new EscuelaController();
if($_GET['tipo']== 'datatable'){
    $controller->datatable();
}

if($_GET['tipo']== 'combo'){
    $busqueda= isset($_POST['busqueda']) ? $_POST['busqueda'] : '';
    $controller->combo($busqueda);
}

if($_GET['tipo']=='registrar'){

    $nombre = $_POST['nombre'];
    $apellido_paterno = $_POST['apellido_paterno'];
    $apellido_materno = $_POST['apellido_materno'];
    $cumple= isset($_POST['cumple']) ?  $_POST['cumple'] : '';
    $genero = isset($_POST['genero']) ? $_POST['genero'] : '';
    $telefono = isset($_POST['telefono']) ? $_POST['telefono'] : '';

    $controller->registrarAlumno($nombre, $apellido_paterno, $apellido_materno, $cumple, $genero, $telefono);

}

if($_GET['tipo']=='traer'){
    $id_alumno = $_POST['id_alumno'];
    $controller->traerAlumno($id_alumno);
}

if($_GET['tipo']=='actualizar'){
    $id_alumno = $_POST['id_alumno'];
    $nombre = $_POST['nombre'];
    $apellido_paterno = $_POST['apellido_paterno'];
    $apellido_materno = $_POST['apellido_materno'];
    $cumple= isset($_POST['cumple']) ?  $_POST['cumple'] : '';
    $genero = isset($_POST['genero']) ? $_POST['genero'] : '';
    $telefono = isset($_POST['telefono']) ? $_POST['telefono'] : '';
    $controller->actualizarAlumno($id_alumno, $nombre, $apellido_paterno, $apellido_materno, $cumple, $genero, $telefono);
}