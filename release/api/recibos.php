<?php
require_once __DIR__ . '/../controllers/ReciboController.php';
header('Content-Type: application/json');

$controller = new ReciboController();
if($_GET['tipo']== 'datatable'){
    $controller->datatable_recibos();
}

if($_GET['tipo']== 'tabla_conceptos'){
    $controller->datatable_conceptos($_GET['id_recibo'], $_GET['tipo_filtro']); 
}

if($_GET['tipo']== 'agregar_concepto'){
    $categoria = $_POST['categoria'];
    $cantidad = $_POST['cantidad'];
    $precio = $_POST['precio'];
    $mensualidad = $_POST['mensualidad'];
    $year = $_POST['year'];
    $importe = intval($_POST['cantidad']) * floatval($_POST['precio']);
    $controller->agregar_conceptos($categoria, $cantidad, $precio, $importe, $mensualidad, $year);
}

if($_GET['tipo']=='tabla_pagos'){
    $controller->datatable_pagos($_GET['id_recibo']);
}
 
if($_GET['tipo']== 'eliminar_concepto'){
    $controller->eliminar_concepto($_POST['id']);
}

if($_GET['tipo']=='generar_recibo'){
    $alumno = $_POST['alumno'];
    $tipo_recibo = $_POST['tipo_recibo'];
    $formas_pago = $_POST['formas_pago'];  
    $ciclo = $_POST['ciclo'];  
    $comentario = $_POST['comentarios'];  
    $pago_efectivo = $_POST['Efectivo'] ?? 0;
    $pago_tarjeta = $_POST['Tarjeta'] ?? 0;
    $pago_transferencia = $_POST['Transferencia'] ?? 0; 
    $pago_deposito = $_POST['Deposito'] ?? 0;
    $pago_cheque = $_POST['Cheque'] ?? 0;
    $plazo = $_POST['plazo'] ?? 0;

    $controller->registrar_recibo($alumno, $tipo_recibo, $formas_pago, 
    $pago_efectivo, $pago_tarjeta, $pago_transferencia, $pago_deposito, $pago_cheque, $comentario, $ciclo, $plazo);
} 

if($_GET['tipo']== 'actualizar_estatus_recibo'){
    $controller->actualizar_estatus($_POST['id_recibo'], $_POST['tipo_cancelacion'], 1);
}

if($_GET['tipo']== 'obtener_recibo'){
    $json_data = file_get_contents('php://input');
    
    /* cho json_encode($json_data);
    die(); */
    $data = json_decode($json_data, true);
    $controller->obtener_recibo($data['id_recibo'], 1);
}


if($_GET['tipo']== 'realizar_pago'){

    $id_recibo = $_POST['id_recibo'];
    $formas_pago = $_POST['formas_pago'];
    $pago_efectivo = $_POST['Efectivo'] ?? 0;
    $pago_tarjeta = $_POST['Tarjeta'] ?? 0;
    $pago_transferencia = $_POST['Transferencia'] ?? 0; 
    $pago_deposito = $_POST['Deposito'] ?? 0;
    $pago_cheque = $_POST['Cheque'] ?? 0;
    $comentarios = $_POST['comentarios'];
    $controller->realizar_pago($id_recibo, $formas_pago, 
    $pago_efectivo, $pago_tarjeta, $pago_transferencia, $pago_deposito, $pago_cheque, $comentarios);
}

if($_GET['tipo']== 'cancelar_pago'){
    
    $id_pago = $_POST['id_pago'];
    $controller->cancelar_pago($id_pago); 
}

if($_GET['tipo']== 'actualizar_datos_generales'){
    $data = $_POST['data'];
    $controller->actualizar_datos_generales(1, $data); 

}
?>