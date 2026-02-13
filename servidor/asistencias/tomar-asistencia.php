<?php


if($_POST){

    include "../database/conexion.php";
    include "../helpers/response_helper.php";
    
    date_default_timezone_set('America/Matamoros');
    session_start();

    $id_alumno = $_POST['id_alumno'];
    $id_materia = $_POST['id_materia'];
    $fecha = $_POST['fecha'];
    $asistencia = $_POST['tipo'];
    $id_grupo = $_POST['id_grupo'];
    $deshacer = $_POST['deshacer'];


    $count = "SELECT count(*) FROM asistencias WHERE id_alumno = ? AND id_materia = ? AND fecha = ? AND id_grupo = ?";
    $stmt = $con->prepare($count);
    $stmt->execute([$id_alumno, $id_materia, $fecha, $id_grupo]);
    $total_reg = $stmt->fetchColumn();
    $stmt->closeCursor();

    if($total_reg>0){
        if($deshacer){
            $insert = "UPDATE asistencias SET asistencia = 0 WHERE id_alumno =? AND id_materia=? AND fecha=? AND id_grupo=?";
            $stmt = $con->prepare($insert);
            $stmt->execute([$id_alumno, $id_materia, $fecha, $id_grupo]);
            $stmt->closeCursor();
            responder(true, 'Registro de asistencia deshecho', 'success' ,[], true);
        }else{
            $insert = 'UPDATE asistencias SET asistencia = ? WHERE id_alumno =? AND id_materia=? AND fecha=? AND id_grupo=?';
            $stmt = $con->prepare($insert);
            $stmt->execute([$asistencia, $id_alumno, $id_materia, $fecha, $id_grupo]);
            $stmt->closeCursor();
            responder(true, 'Registro de asistencia actualizado', 'success' ,[], true);
        }
       
    }else{
        $insert = 'INSERT INTO asistencias(id_alumno, id_materia, fecha, asistencia, id_grupo) VALUES(?,?,?,?,?)';
        $stmt = $con->prepare($insert);
        $stmt->execute([$id_alumno, $id_materia, $fecha, $asistencia, $id_grupo]);
        $stmt->closeCursor();

        switch ($asistencia) {
            case 1:
                $tipo_asistencia = 'Asistencia';            # code...
                break;
                case 2:
                    $tipo_asistencia = 'Falta';            # code...
                    break;
                    case 3:
                        $tipo_asistencia = 'Retardo';            # code...
                        break;
            default:
                
                break;
        }
        responder(true, $tipo_asistencia.' registrada', 'success' ,[], true);
    }
   
    

}


?>