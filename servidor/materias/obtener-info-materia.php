<?php
session_start();
include "../database/conexion.php";

$id_materia = $_POST['id_materia'];
$query = "SELECT count(*) FROM materias WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->execute([$id_materia]);
$exists = $stmt->fetchColumn();
$stmt->closeCursor();

if($exists>0){
    $query = "SELECT * FROM materias WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->execute([$id_materia]);
    while ($row = $stmt->fetch()) {
        $response['datos'] = $row;
    }

    $response['mensaje'] = 'Datos encontrados';
    $response['estatus'] = true;
    echo json_encode($response);
}else{
    $response = array('estatus'=>false, 'mensaje'=>'Datos no encontrados');
}


?>