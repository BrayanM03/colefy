<?php
require_once __DIR__ . '/../controllers/HorarioController.php';
//include "../servidor/helpers/response_helper.php";
$controller = new HorarioController();

if($_GET['tipo']=='horarios'){
    $controller->datatable();
}else if($_GET['tipo']=='prehorarios'){
    $controller->datatablePreHorario();
}else if($_GET['tipo']=='eliminar_prehorario'){
    $controller->eliminarPreHorario($_GET['id_prehorario']);
}else if($_GET['tipo']=='registrar_horario'){
    $controller->registrarHorario($_GET['nombre']);
}else if($_GET['tipo']=='tabla_grupos_horario'){
    $controller->datatableGruposHorario();
}else if($_GET['tipo']=='insertar_grupos_horario'){
    $controller->insertarGruposHorario($_POST['ids_grupos'], $_POST['id_horario']);
}else if($_GET['tipo']=='eliminar_grupos_horario'){
    $controller->cancelarGruposHorario($_POST['id']);
}else{
    responder(false, 'No hay solicitud GET','success', [], true);
}
