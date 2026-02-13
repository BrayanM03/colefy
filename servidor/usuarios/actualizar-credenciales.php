<?php
session_start();

    include "../database/conexion.php";

    $id_usuario = $_POST["id_usuario"];
    $tipo = $_POST["tipo"];

    if($tipo == "usuario"){

        $usuario_actual = $_POST['usuario_actual'];
        $usuario_nuevo = $_POST['usuario_nuevo'];

        $select = "SELECT COUNT(*) FROM usuarios WHERE usuario = ?";
        $res = $con->prepare($select);
        $res->execute([$usuario_nuevo]);
        $total = $res->fetchColumn();
        $res->closeCursor();
    
        if($total == 0 || $usuario_actual === $usuario_nuevo){
    
            $updt = "UPDATE usuarios SET usuario = ? WHERE id = ? ";
            $re = $con->prepare($updt);
            $re->execute([$usuario_nuevo, $id_usuario]);
            $re->closeCursor();
    
            $response = array("estatus" => true, "mensaje" => "Datos actualizados");
        }else{
            $response = array("estatus" => false, "mensaje" => "Ya existe un usuario con es nombre");
    
        }

    }else if($tipo == "password"){

        $password = $_POST['password'];

        $select = "SELECT COUNT(*) FROM usuarios WHERE id = ?";
        $res = $con->prepare($select);
        $res->execute([$id_usuario]);
        $total = $res->fetchColumn();
        $res->closeCursor();
    
        if($total > 0){
            $hash = password_hash($password, PASSWORD_DEFAULT, [15]);
    
            $updt = "UPDATE usuarios SET contraseña = ? WHERE id = ? ";
            $re = $con->prepare($updt);
            $re->execute([$hash, $id_usuario]);
            $re->closeCursor();
    
            $response = array("estatus" => true, "mensaje" => "Datos actualizados");
        }else{
            $response = array("estatus" => false, "mensaje" => "NO existe ese usuario");
    
        }
        
    }
   
    

   

    echo json_encode($response, JSON_UNESCAPED_UNICODE);