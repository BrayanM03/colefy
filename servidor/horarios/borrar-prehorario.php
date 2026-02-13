<?php


if($_POST){

    include "../database/conexion.php";
    include "../helpers/response_helper.php";

    $id_prehorario = $_POST['id'];

    $count = "SELECT count(*) FROM detalle_prehorario WHERE id=?";
    $res = $con->prepare($count);
    $res->execute([$id_prehorario]);
    $total_reg = $res->fetchColumn();
    $res->closeCursor(); 
    
    $nuevo_estatus = "Disponible";
    
    if($total_reg > 0){
        $insert = "DELETE FROM detalle_prehorario WHERE id =?";
        $stmt = $con->prepare($insert);
        $stmt->execute([$id_prehorario]);
        $stmt->closeCursor();

        responder(true, 'Registro borrado correctamente', [], true);
    }else{
        responder(false, 'No se pudo encontrar el registro','success', [], true);
    }

}
?>