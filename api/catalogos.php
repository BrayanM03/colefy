<?php
// 1. Subimos un nivel para encontrar el config
require_once dirname(__DIR__) . '/config/config.php';// 2. ERROR CORREGIDO: No uses BASE_URL aquí. 
// Usa la ruta física relativa al archivo actual.
require_once ROOT_PATH . 'controllers/CatalogoController.php';
require_once __DIR__ . '/../controllers/GrupoController.php';

$controller = new CatalogoController();

if($_GET['tipo']== 'combos_iniciales'){ 
    $controller_grupo = new Grupocontroller();
    $data_combo_niveles =$controller_grupo->combo_niveles();
    $data_combo_ciclos =$controller_grupo->combo_ciclos();
    $data_niveles = $data_combo_niveles['data'];
    $data_ciclos = $data_combo_ciclos['data'];

    $data = ['niveles'=>$data_niveles, 'ciclos' =>$data_ciclos];
    echo json_encode(array('estatus'=>true,
    'data'=>$data));
}


if($_GET['tipo']== 'iniciar_flujo'){ 
    $data = json_decode(file_get_contents('php://input'), true);
    $controller->iniciar_flujo(1, $data);
}


if($_GET['tipo']== 'segundo_paso'){
    $data = json_decode(file_get_contents('php://input'), true);
    $controller->segundo_paso_flujo(1, $data);
}

if($_GET['tipo'] == 'guardar_bloques'){
    $controller->guardar_bloques(1);
}

if($_GET['tipo']=='cargar_conf_prehorario_flujo'){
    $controller->cargar_config_prehorario_flujo(1);
}

if($_GET['tipo']=='resetear'){
    $controller->resetear_prehorario(1);
}

if($_GET['tipo']=='finalizar'){
    $controller->guardar_horario(1);
}
