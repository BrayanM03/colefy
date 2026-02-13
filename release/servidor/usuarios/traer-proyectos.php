

<?php
 include "../database/conexion.php";
 date_default_timezone_set('America/Matamoros');

 $id_sucursal = $_POST['id_sucursal'];

$consultar = $con->prepare("SELECT COUNT(*) FROM proyectos WHERE sucursal_id = ?");
    $consultar->execute([$id_sucursal]);
    $total = $consultar->fetchColumn();

    if ($total > 0) {
        $consultar = $con->prepare("SELECT * FROM proyectos  WHERE sucursal_id = ?");
        $consultar->execute([$id_sucursal]);
        while ($row = $consultar->fetch()) {
            $response['datos'][] = $row;
        }

        $response['estatus'] = true;
        $response['mensaje'] = "Se encontraron datos";
    }else{
        $response['estatus'] = false;
        $response['mensaje'] = "No se encontraron datos";

    }

    echo json_encode($response, JSON_UNESCAPED_UNICODE);

    ?>