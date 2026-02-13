<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once  ROOT_PATH . 'controllers/GrupoController.php';


$controller = new GrupoController();
if($_GET['tipo']== 'datatable'){
    $controller->datatable();
}
if($_GET['tipo']== 'datatable_pregrupo'){
    $controller->datatable_pregrupo();
}
if($_GET['tipo']== 'datatable_grupo'){
    $controller->datatable_detalle_grupo($_GET['id_grupo'], $_GET['ciclo']);
}
if($_GET['tipo']== 'combo'){
    $controller->combo();
}
if($_GET['tipo']=='preregistrar'){
    $alumno = $_POST['alumno'];
    $controller->registrarAlumnoPreGrupo($alumno);
}
if($_GET['tipo']=='eliminar_detalle_pregrupo'){
    $id_detalle = $_POST['id_detalle'];
    $controller->eliminarAlumnoPreGrupo($id_detalle);
}
if($_GET['tipo']=='registrar'){
    $controller->registrarGrupo($_POST['nombre'], $_POST['nivel'], $_POST['grado'], $_POST['ciclo']);
}
if($_GET['tipo']=='actualizar'){
    $controller->actualizarGrupo($_POST['nombre'], $_POST['nivel'], $_POST['grado'], $_POST['ciclo'], $_POST['id_grupo']);
}
if($_GET['tipo']=='registrar_alumno'){
    $controller->registrarAlumnoGrupo($_POST['id_alumno'], $_POST['id_grupo'], $_POST['id_ciclo']);
}
if($_GET['tipo']== 'cancelar_alumno_grupo'){
    $controller->cancelarAlumnoGrupo($_POST['id_registro'], 0);
}
if($_GET['tipo']== 'reactivar_alumno_grupo'){
    $controller->cancelarAlumnoGrupo($_POST['id_registro'], 1);
} 

if($_GET['tipo']== 'grupo_calificaciones'){
    $controller->obtenerGrupoCalificaciones($_POST['id_grupo'], $_POST['id_ciclo']);
} 