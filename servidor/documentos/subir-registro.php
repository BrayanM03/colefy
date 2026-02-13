<?php
session_start();
include '../database/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    try {
        // Iniciar la transacción
        $con->beginTransaction();
        $titulo = $_POST['titulo'];
        $descripcion = $_POST['descripcion'];
        $fecha = date('Y-m-d');
        $hora = date('h:i a');
        $id_maestro = $_SESSION['id'];
        // Insertar el documento en la tabla 'documentos'
        $stmt = $con->prepare("INSERT INTO documentos (titulo, descripcion, fecha, hora, maestro_id, estatus) VALUES (?,?,?,?,?,1)");
        $stmt->execute([$titulo, $descripcion, $fecha, $hora, $id_maestro]);

        // Obtener el ID del documento insertado
        $id_documento = $con->lastInsertId();

        // Ruta base para guardar los archivos
        $base_path = '../../static/docs/' . $id_documento . '/';
        
        // Crear directorio si no existe 
        if (!is_dir($base_path)) {
            mkdir($base_path, 0777, true);
        }

        
        // Recorrer todos los archivos subidos
        foreach ($_FILES as $index => $element) {
            $file_name = $element['name'];
            $file_tmp = $element['tmp_name'];
            $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);

            // Insertar un registro en detalle_documentos para cada archivo
            $stmt = $con->prepare("INSERT INTO detalle_documento (id_documento, estatus) VALUES (?, 1)");
    
            $stmt->execute([$id_documento]);

            // Obtener el ID del detalle_documentos insertado
            $id_detalle = $con->lastInsertId();
            $ruta_archivo = 'file_' . $id_detalle . '.' . $file_ext;
            // Mover el archivo a su ubicación final
            $destino_final = $base_path . 'file_' . $id_detalle . '.' . $file_ext;
            move_uploaded_file($file_tmp, $destino_final);

            // Actualizar la ruta con el nombre correcto del archivo
            $stmt = $con->prepare("UPDATE detalle_documento SET ruta = ?, nombre_documento = ?, extension = ? WHERE id = ?");
            $stmt->execute([$ruta_archivo, $file_name, $file_ext, $id_detalle]);
        }

        // Confirmar la transacción
        $con->commit();

        $response = array(
        "message"=>"Archivo(s) correctamente subidos",
        "status"=>"success");
         echo json_encode($response, JSON_UNESCAPED_UNICODE);

    } catch (Exception $e) {
        // En caso de error, deshacer la transacción
        $con->rollBack();
       
        $response = array(
        "message"=> "Error al subir archivos: " . $e->getMessage(),
        "status"=>"error");
         echo json_encode($response, JSON_UNESCAPED_UNICODE);
    }
} else {
    $response = array(
    "message"=> "No se han subido archivos.",
    "status"=>"error");
     echo json_encode($response, JSON_UNESCAPED_UNICODE);
}
?>