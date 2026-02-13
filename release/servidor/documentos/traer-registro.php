<?php
session_start();
include '../database/conexion.php';
$id_registro = $_POST['id_documento'];

$query = "SELECT count(*) FROM documentos WHERE id = ? AND estatus != 0";
$stmt = $con->prepare($query);
$stmt->execute([$id_registro]);
$doc_exists = $stmt->fetchColumn();
$stmt->closeCursor();

if($doc_exists>0){
    $query = "SELECT * FROM documentos WHERE id = ? AND estatus != 0";
    $stmt = $con->prepare($query);
    $stmt->execute([$id_registro]);
    while ($row = $stmt->fetch()) {
        $response['datos'] = $row;
    }

    $query = "SELECT count(*) FROM detalle_documento WHERE id_documento = ? AND estatus != 0";
    $stmt = $con->prepare($query);
    $stmt->execute([$id_registro]);
    $exists = $stmt->fetchColumn();
    $stmt->closeCursor();

    if($exists>0){
        $query = "SELECT * FROM detalle_documento WHERE id_documento = ? AND estatus !=0";
        $stmt = $con->prepare($query);
        $stmt->execute([$id_registro]);
        while ($row = $stmt->fetch()) {
            $response['datos']['archivos'][] = $row;
        }
        $response['mensaje'] = 'Datos encontrados';
        $response['estatus'] = true;
       
    }else{
        $response = array('estatus'=>false, 'mensaje'=>'Datos no encontrados');
    }
    echo json_encode($response);
}


#