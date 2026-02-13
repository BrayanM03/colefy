<?php


if($_POST){

    include "../database/conexion.php";
    date_default_timezone_set('America/Matamoros');

    $id_solicitud = $_POST['id_solicitud'];
$query = "SELECT count(*) FROM solicitudes WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->execute([$id_solicitud]);
$exists = $stmt->fetchColumn();
$stmt->closeCursor();

if($exists>0){
    $query = "SELECT * FROM solicitudes WHERE id = ?";
    $stmt = $con->prepare($query);
    $stmt->execute([$id_solicitud]);
    while ($row = $stmt->fetch()) {
        $nombre = $row['nombre'];
        $apellidos = $row['apellido_paterno'] .' ' .$row['apellido_materno'];
    }
    $user = $_POST['usuario'];
    $pass = $_POST['password'];
    $rol = $_POST['rol']; //Admin: 1 Maestro: 2 Estudiante: 3
    if($rol ==2){
        $es_profe=1;
    }else{
        $es_profe=0;
    }
    $id_grupo = empty($_POST['id_grupo']) ? 0 : $_POST['id_grupo'];
    $estatus = 1;
    $fecha = date("Y-m-d");
    $hash = password_hash($pass, PASSWORD_DEFAULT, [15]);
    $datos = array(
        $nombre, $apellidos, $rol, $estatus, $fecha, $user, $hash, $id_solicitud, $es_profe, $id_grupo
    );

   

    $consultar = $con->prepare("SELECT COUNT(*) FROM usuarios WHERE usuario  = ?");
    $consultar->execute([$user]);
    $total = $consultar->fetchColumn();

    if($total == 0) {

        $ingresar = $con->prepare("INSERT INTO usuarios(id, 
                                                        nombre,
                                                        apellido,
                                                        rol,
                                                        estatus,
                                                        fecha_ingreso,
                                                        usuario,
                                                        contraseña,
                                                        id_solicitud,
                                                        es_profesor,
                                                        id_grupo) VALUES(null,?,?,?,?,?,?,?,?,?,?)");
         $ingresar->execute($datos);
         $ingresar->closeCursor();

         $upd = "UPDATE solicitudes SET aprobado = 1 WHERE id = ?";
         $stmt = $con->prepare($upd);
        $stmt->execute([$id_solicitud]);
        $exists = $stmt->fetchColumn();
        $stmt->closeCursor();
         $response['mensaje'] = '¡Usuario registrado con exito!';
         $response['estatus'] = true;
                                             
    }else{
        $response['mensaje'] = 'El usuario ingresado ya existe, intenta con otro.';
         $response['estatus'] = false;
    }
    
}else{
    $response = array('estatus'=>false, 'mensaje'=>'Datos no encontrados');
}
echo json_encode($response);    

    
  
}

?>