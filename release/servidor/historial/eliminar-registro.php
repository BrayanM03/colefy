<?php

include "../database/conexion.php";
date_default_timezone_set('America/Matamoros');

include '../database/eliminar-dato.php';
 
$delete = new Eliminar;
$id_reg = $_POST["id_reg"]; 
$tabla = $_POST['tabla'];
$sentencia = $delete->eliminarDato($tabla, $id_reg, false, "detalle_salida", "salida_id", $con);
   


echo json_encode($sentencia, JSON_UNESCAPED_UNICODE);

?>