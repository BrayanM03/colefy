<?php

session_start();
include '../database/conexion.php';
$id_registro = $_POST['id_documento'];
$tipo = $_POST['tipo'];

if($tipo ==1){
    $query = "SELECT count(*) FROM documentos WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->execute([$id_registro]);
    $doc_exists = $stmt->fetchColumn();
    $stmt->closeCursor();

    if($doc_exists>0){

            $upda = "UPDATE documentos SET estatus = 0 WHERE id = ?";
            $stmt = $con->prepare($upda);
            $stmt->execute([$id_registro]);
            $stmt->closeCursor();
            $upda = "UPDATE detalle_documento SET estatus = 0 WHERE id_documento = ?";
            $stmt = $con->prepare($upda);
            $stmt->execute([$id_registro]);
            $stmt->closeCursor();
            $response = array('estatus' =>true, 'mensaje' =>'Actualizado correctamente');

    }else{
        $response = array('estatus' =>false, 'mensaje' =>'No hay documentos relacionados');
    }

}else if($tipo == 2){

    $query = "SELECT count(*) FROM detalle_documento WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->execute([$id_registro]);
    $doc_exists = $stmt->fetchColumn();
    $stmt->closeCursor();

    if($doc_exists>0){
        $upda = "UPDATE detalle_documento SET estatus = 0 WHERE id = ?";
        $stmt = $con->prepare($upda);
        $stmt->execute([$id_registro]);
        $stmt->closeCursor();
        $response = array('estatus' =>true, 'mensaje' =>'Actualizado correctamente');
    }else{
        $response = array('estatus' =>false, 'mensaje' =>'No hay documentos relacionados');
    }
        
    }

    echo json_encode($response);

?>