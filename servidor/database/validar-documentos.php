<?php
  // Obtener los parámetros de entrada
  $_post = json_decode(file_get_contents('php://input'),true);
  
  $id_documento = $_post["id_doc"];
  $ruta = $_post["path"];
  $prefix = $_post["prefix"];

  // Comprobar si el archivo existe en la ruta especificada
  if (file_exists($ruta . "/". $prefix. "-" . $id_documento . ".pdf")) {
    $mensaje = "El documento con ID " . $id_documento . " existe en la ruta " . $ruta;
    $stat = true;
  } else {
    $mensaje = "El documento con ID " . $id_documento . " no existe en la ruta " . $ruta;
    $stat = false;
  }

  $response = array("estatus"=> $stat, "mensaje"=> $mensaje);
  echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>