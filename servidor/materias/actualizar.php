<?php

include "../database/conexion.php";
date_default_timezone_set('America/Matamoros');

$datos = $_POST;

if(!empty($_POST)){
    $id_materia = $datos['id_materia'];
    $nombre = $datos['nombre'];
    $codigo = $datos['codigo'];
    $valor_creditos = $datos['valor_credito'];

    $count = "SELECT codigo FROM materias WHERE codigo = ? AND estatus =1 AND id != ?";
    $stmt = $con->prepare($count);
    $stmt->execute([$codigo, $id_materia]);
    $total_repetidas = $stmt->fetchColumn();
    $stmt->closeCursor();

    if($total_repetidas>0){

        $response = array('estatus'=>false, 'mensaje'=>'El codigo esta repetido');
        echo json_encode($response);
    }


    $upd = "UPDATE materias SET nombre = ?, codigo = ?, valor_creditos = ? 
    WHERE id = ?";

$stmt = $con->prepare($upd);
$stmt->execute([
    $nombre,
    $codigo,
    $valor_creditos,
    $id_materia
]);


$response = array('estatus'=>true, 'mensaje'=>'Registro actualizado con exito');
echo json_encode($response);

}else{

$response = array('estatus'=>false, 'mensaje'=>'No hay solicitud POST');
echo json_encode($response);
}

?>