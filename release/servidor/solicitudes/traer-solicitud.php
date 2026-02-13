<?php
session_start();
include "../database/conexion.php";
$query = $_POST['id_solicitud'];

$id_solicitud = $_POST['id_solicitud'];
$query = "SELECT count(*) FROM solicitudes WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->execute([$id_solicitud]);
$exists = $stmt->fetchColumn();
$stmt->closeCursor();

if($exists>0){
    $query = "SELECT * FROM solicitudes WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->execute([$id_solicitud]);
    while ($row = $stmt->fetch()) {
        $response['datos'] = $row;
    }

    $query = "SELECT count(*) FROM roles";
    $stmt = $con->prepare($query);
    $stmt->execute();
    $rols_exists = $stmt->fetchColumn();
    $stmt->closeCursor();
    if($rols_exists>0){
        $query = "SELECT * FROM roles";
        $stmt = $con->prepare($query);
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            $response['datos']['roles'][] = $row;
        }}

    $response['mensaje'] = 'Datos encontrados';
    $response['estatus'] = true;
    echo json_encode($response);
}else{
    $response = array('estatus'=>false, 'mensaje'=>'Datos no encontrados');
}


?>