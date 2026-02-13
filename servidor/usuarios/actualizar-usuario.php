<?php
session_start();

    include "../database/conexion.php";

    $id_usuario = $_POST["id"];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $proyecto_id = intval($_POST['proyecto_id']);
    $sucursal_id = intval($_POST['sucursal_id']);
    $rol = $_POST['rol'];
    

    $select = "SELECT COUNT(*) FROM usuarios WHERE id = ?";
    $res = $con->prepare($select);
    $res->execute([$id_usuario]);
    $total = $res->fetchColumn();
    $res->closeCursor();

    if($total > 0){
        $updt = "UPDATE usuarios SET nombre = ?, 
                                     apellido = ?, 
                                     rol = ?,
                                     proyecto_id = ?, 
                                     sucursal_id = ?  WHERE id = ? ";
        $re = $con->prepare($updt);
        $re->execute([$nombre, $apellido, $rol, $proyecto_id, $sucursal_id, $id_usuario]);
        $re->closeCursor();

        $response = array("estatus" => true, "mensaje" => "Datos actualizados");
    }else{
        $response = array("estatus" => false, "mensaje" => "No hay un usuario con ese ID");

    }

    echo json_encode($response, JSON_UNESCAPED_UNICODE);