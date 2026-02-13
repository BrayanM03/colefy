<?php

include "../database/conexion.php";
date_default_timezone_set('America/Matamoros');

$datos = $_POST;

if(!empty($_POST)){
    $id_solicitud = $datos['id_solicitud'];
    $nombre = $datos['nombre'];
    $apellido_paterno = $datos['apellido_paterno'];
    $apellido_materno = $datos['apellido_materno'];
    $fecha_cumple = $datos['fecha_cumple'];
    $sexo = $datos['sexo'];
    $pais = $datos['pais'];
    $estado = $datos['estado'];
    $ciudad = $datos['ciudad'];
    $telefono = $datos['telefono'];
    $telefono_casa = $datos['telefono_casa'];
    $correo = $datos['correo'];
    $nombre_iglesia = $datos['nombre_iglesia'];
    $nombre_pastor = $datos['nombre_pastor'];
    $telefono_pastor = $datos['telefono_pastor'];
    $curso_interes = $datos['curso_interes'];

    $upd = "UPDATE solicitudes SET
    nombre = ?,
    apellido_paterno = ?,
    apellido_materno = ?,
    fecha_cumple = ?,
    sexo = ?,
    pais = ?,
    estado = ?,
    ciudad = ?,
    telefono = ?,
    telefono_casa = ?,
    correo = ?,
    nombre_iglesia = ?,
    nombre_pastor = ?,
    telefono_pastor = ?,
    curso_interes = ?
    WHERE id = ?";

$stmt = $con->prepare($upd);
$stmt->execute([
    $nombre,
    $apellido_paterno,
    $apellido_materno,
    $fecha_cumple,
    $sexo,
    $pais,
    $estado,
    $ciudad,
    $telefono,
    $telefono_casa,
    $correo,
    $nombre_iglesia,
    $nombre_pastor,
    $telefono_pastor,
    $curso_interes,
    $id_solicitud // Aquí va el ID de la solicitud a actualizar
]);


$response = array('estatus'=>true, 'mensaje'=>'Registro exitoso');
echo json_encode($response);

}else{

$response = array('estatus'=>false, 'mensaje'=>'No hay solicitud POST');
echo json_encode($response);
}

?>