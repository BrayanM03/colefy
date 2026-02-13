<?php
if ($_POST) {
    include "../database/conexion.php";
    include "../helpers/response_helper.php";
    date_default_timezone_set('America/Matamoros');

    $consultar = $con->prepare("SELECT COUNT(*) FROM grupos");
    $consultar->execute();
    $total = $consultar->fetchColumn(); 
    
    $consultar->closeCursor();
    if($total > 0){

        $consultar = $con->prepare("SELECT * FROM grupos");
        $consultar->execute();
        while ($row = $consultar->fetch()) {
            $data[] = $row;
        }
        $consultar->closeCursor();

        responder(true, 'Se encontraron grupos','success', $data, true);
        
    }else{
        responder(false, 'No se encontrarón grupos', 'danger', $data, true);
    }

}

?>