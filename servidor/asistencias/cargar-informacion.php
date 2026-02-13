<?php


if($_POST){

    include "../database/conexion.php";
    include "../helpers/response_helper.php";
    
    date_default_timezone_set('America/Matamoros');
    session_start();

    $id_grupo = $_POST['id_grupo'];
    $id_profesor = $_SESSION['id'];
    
    $count = "SELECT count(*) FROM detalle_horario WHERE id_profesor = ? AND id_grupo = ?";
    $res = $con->prepare($count);
    $res->execute([$id_profesor, $id_grupo]);
    $total_ordenes = $res->fetchColumn();
    $res->closeCursor(); 

   if($total_ordenes>0){

    $consultar = $con->prepare("SELECT m.*, dh.hora FROM detalle_horario dh INNER JOIN materias m
    ON dh.id_materia = m.id WHERE dh.id_profesor = ? AND dh.id_grupo = ?");
    $consultar->execute([$id_profesor, $id_grupo]);
    while ($row = $consultar->fetch()) {
        $data[] = $row;
    }

    responder(true, 'Registros encontrados', 'success' ,$data, true);

   }else{
    responder(false, 'No tienes materias cargadas o un horario definido', 'warning' ,[], true);
   }
}else{
    responder(false, 'No hay solicitud post', 'danger' ,[], true);

}