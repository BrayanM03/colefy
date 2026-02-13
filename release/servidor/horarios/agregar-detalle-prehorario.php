<?php
if ($_POST) {

    /* include "../database/conexion.php";
    include "../helpers/response_helper.php"; */

    date_default_timezone_set('America/Matamoros');
    
    require_once __DIR__ . '/../../controllers/HorarioController.php';
    $controller = new HorarioController();
    $controller->agregarDetallePreHorario();
}
