<?php


if($_POST){

    include "../database/conexion.php";
    include "../helpers/response_helper.php";

    date_default_timezone_set('America/Matamoros');
    session_start();

    function insertarDetalle($con, $id_materia, $dia, $hora, $id_grupo, $id_horario, $id_profesor){
        $insert ="INSERT INTO detalle_horario (id_materia, dia, hora, id_grupo, id_horario, id_profesor) 
        VALUES(?,?,?,?,?,?)";
        $stmt = $con->prepare($insert);
        $stmt->execute([$id_materia, $dia, $hora, $id_grupo, $id_horario, $id_profesor]);
        $stmt->closeCursor();
    }
    if(isset($_POST)){
        $nombre_horario = $_POST['nombre_horario'];
        $id_usuario = $_SESSION['id'];
        $consultar = $con->prepare("SELECT COUNT(*) FROM detalle_prehorario WHERE id_usuario =?");
        $consultar->execute([$id_usuario]);
        $total = $consultar->fetchColumn(); 
        
            $consultar->closeCursor();
            if($total > 0){

                $insert = "INSERT INTO horarios (nombre, estatus) VALUES(?,1)";
                $stmt = $con->prepare($insert);
                $stmt->execute([$nombre_horario]);
                $stmt->closeCursor();
                $id_horario = $con->lastInsertId();

                $consultar = $con->prepare("SELECT * FROM detalle_prehorario WHERE id_usuario =?");
                $consultar->execute([$id_usuario]);
                while ($row = $consultar->fetch()) {
                    $id_profesor = $row['id_profesor'];
                    $id_materia = $row['id_materia'];
                    $dia = $row['dia'];
                    $hora = $row['hora'];
                    $id_grupo = $row['id_grupo'];
                    insertarDetalle($con, $id_materia, $dia, $hora, $id_grupo, $id_horario, $id_profesor);
                }
                $consultar->closeCursor();

                $del = "DELETE FROM detalle_prehorario WHERE id_usuario =?";
                $stmt = $con->prepare($del);
                $stmt->execute([$id_usuario]);
                $stmt->closeCursor();
                responder(true, 'El horario se registró correctamente','success',[], true);
                
            }else{
                responder(false, 'No se encontraron registros en la tabla', 'danger', [], true);
            }

            }else{
                responder(false, 'No se econtró solicitud POST', 'danger', [], true);
            }

}