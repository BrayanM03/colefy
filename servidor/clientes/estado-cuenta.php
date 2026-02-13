<?php

// Incluimos el archivo de conexión a la base de datos
include '../database/conexion.php';

$id_cliente = $_POST['id_cliente'];
$response = array();
$data_detalles = traerDetalles($con, $id_cliente);
$data_abonos = traerAbonos($con, $id_cliente);
$data_cliente = traerDatoCliente($con, $id_cliente);


$response = array('data_abonos' => $data_abonos, 'data_cliente' => $data_cliente, 'data_detalles' => $data_detalles);
echo json_encode($response);


function traerDatoCliente($con, $id)
{
    $consultar = "SELECT COUNT(*) FROM clientes WHERE id = ?";
    $resp = $con->prepare($consultar);
    $resp->execute([$id]);
    $total = $resp->fetchColumn();
    $resp->closeCursor();

    if ($total > 0) {
        $consultar = "SELECT * FROM clientes WHERE id = ?";
        $resp = $con->prepare($consultar);
        $resp->execute([$id]);
        while ($row = $resp->fetch()) {
            if($row["fecha_nacimiento"] == "Sin definir") {
                $fecha_formateada = $row["fecha_nacimiento"];
            } else {
                $fecha_obj = new DateTime($row["fecha_nacimiento"]);
                $locale = 'es_ES';
                $formatter = new IntlDateFormatter($locale, IntlDateFormatter::LONG, IntlDateFormatter::NONE);
                $formatter->setPattern('d \'de\' MMMM \'del\' y');
                $fecha_formateada = $formatter->format($fecha_obj);
            }

            $row["fecha_nacimiento_formateada"] = $fecha_formateada;
            $response = $row;
        }
        return $response;

    } else {
        return array();
    }

}

function traerAbonos($con, $id_cliente)
{
    $select_c = "SELECT COUNT(*) FROM abonos a INNER JOIN ordenes ord ON a.orden_id = ord.id INNER JOIN detalle_orden dor ON a.detalle_id = dor.id WHERE ord.id_cliente = ?";
    $stmt = $con->prepare($select_c);
    $stmt->execute([$id_cliente]);
    $total = $stmt->fetchColumn();

    if($total > 0) {
        $select_c = "SELECT a.*, dor.proyecto, dor.manzana, dor.lote, ord.cliente_etiqueta FROM abonos a INNER JOIN ordenes ord ON a.orden_id = ord.id INNER JOIN detalle_orden dor ON a.detalle_id = dor.id WHERE ord.id_cliente = ? ORDER BY a.fecha DESC";
        $stmt = $con->prepare($select_c);
        $stmt->execute([$id_cliente]);
        while ($row = $stmt->fetch()) {
            $data[] = $row;
        }
    } else {
        $data = [];
    }

    return $data;
}


function traerDetalles($con, $id_cliente)
{
    $resultado= array();
    $fecha_actual_str = date('Y-m-d');
    $fecha_actual = DateTime::createFromFormat('Y-m-d', $fecha_actual_str);
    $arreglo_meses = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
            
    $red = $con->prepare('SELECT COUNT(*) FROM ordenes WHERE id_cliente = ?');
    $red->execute([$id_cliente]);
    $total_orders = $red->fetchColumn();
    $red->closeCursor();

    if($total_orders > 0) {
        $red = $con->prepare('SELECT id FROM ordenes WHERE id_cliente = ?');
        $red->execute([$id_cliente]);
        $ids_orders = $red->fetchAll();
        $red->closeCursor();
        $id_strings = implode(',', $ids_orders[0]);

        $placeholders = implode(',', array_fill(0, count($ids_orders), '?'));
        $select = "SELECT COUNT(*) FROM detalle_orden WHERE orden_id IN ($placeholders)";
        $re = $con->prepare($select);
        $re->execute([$id_strings]);
        $total_detalle = $re->fetchColumn();
        $re->closeCursor();

        //Trayendo detalle de la orden

        if ($total_detalle > 0) {
            $select = "SELECT * FROM detalle_orden WHERE orden_id IN ($placeholders)";
            $re = $con->prepare($select);
            $re->execute([$id_strings]);
            $arreglo = $re->fetchAll();
            $resultado['detalles'] = $arreglo;
            
            
            $consulta = "SELECT COUNT(*) FROM detalle_orden WHERE fecha_vencimiento <= ? AND estatus != 'Pagado' AND orden_id IN ($placeholders)";
            $res = $con->prepare($consulta);
            $res->execute([$fecha_actual_str, $id_strings]);
            $total = $res->fetchColumn();

            if($total > 0){
                $consulta = "SELECT * FROM detalle_orden WHERE fecha_vencimiento <= ? AND estatus != 'Pagado' AND orden_id IN($placeholders)";
                $res = $con->prepare($consulta);
                $res->execute([$fecha_actual_str, $id_strings]);
    
              while ($row = $res->fetch()) {
                    $fecha_vencimiento_str = $row['fecha_vencimiento'];
                    $fecha_vencimiento = DateTime::createFromFormat('Y-m-d', $fecha_vencimiento_str);
    
                    //$fecha_vencimiento = new DateTime($fecha_vencimiento_str);
    
                    $precio = $row['precio'];
                    $mensualidad = $row['mensualidad'];
                    $interval = $fecha_actual->diff($fecha_vencimiento);
                    $total_meses = ($interval->y * 12) + $interval->m;
                    $row['total_meses'] = $total_meses;
                    
                    if ($total_meses > 0) {
                        $meses_nombres = '';
                        $total_meses++;
                        $saldo_vencido = $mensualidad * $total_meses;
                        $row['saldo_vencido'] = $saldo_vencido;
                        $currentMonth = $fecha_vencimiento->format('n');
                        for ($i = 0; $i < $total_meses; $i++) {
                            $monthName = DateTime::createFromFormat('!m', $currentMonth)->format('F');
                            $nombreMes = $arreglo_meses[$currentMonth];
                            $meses_nombres .= ' ' .$nombreMes . ", ";
                            $currentMonth++;
                            if ($currentMonth > 12) {
                                $currentMonth = 1;
                            }
                        }
                        //
                    } else {
                        $currentMonth = $fecha_vencimiento->format('n');
                        $row['saldo_vencido'] = $mensualidad;
                        $nombreMes = $arreglo_meses[$currentMonth];
                        $meses_nombres= $nombreMes;
                    }
                    $row['meses_nombres']=$meses_nombres;
                    $data[] = $row;
    
                }
                $resultado['vencido'] = $data;
            }else{
                $resultado['vencido'] =[] ;
            }
        
        } else {
            $resultado = [];
        }
    } else {
        $resultado = [];
    }
    return $resultado;

}

function traerVencidos($con, $fecha_actual_str, $id_detalles){
    $fecha_actual = DateTime::createFromFormat('Y-m-d', $fecha_actual_str);
    //$fecha_actual = new DateTime($fecha_actual_str);

    $arreglo_meses = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    $consulta = "SELECT COUNT(*) FROM detalle_orden WHERE fecha_vencimiento <= ? AND estatus != 'Pagado'";
    $res = $con->prepare($consulta);
    $res->execute([$fecha_actual_str]);
    $total = $res->fetchColumn();

    if($total > 0){
        $consulta = "SELECT * FROM detalle_orden WHERE fecha_vencimiento <= ? AND estatus != 'Pagado' AND id IN($id_detalles)";
        $res = $con->prepare($consulta);
        $res->execute([$fecha_actual_str]);

          while ($row = $res->fetch()) {
                $fecha_vencimiento_str = $row['fecha_vencimiento'];
                $fecha_vencimiento = DateTime::createFromFormat('Y-m-d', $fecha_vencimiento_str);

                //$fecha_vencimiento = new DateTime($fecha_vencimiento_str);

                $precio = $row['precio'];
                $mensualidad = $row['mensualidad'];
                $interval = $fecha_actual->diff($fecha_vencimiento);
                $total_meses = ($interval->y * 12) + $interval->m;
                $row['total_meses'] = $total_meses;
                if($row['id']==406){
                    /* print_r($fecha_actual_str .'  -  ');
                    print_r($fecha_vencimiento_str .'  -  ');
                    print_r($total_meses);
                    die(); */
                }
                if ($total_meses > 0) {
                    $meses_nombres = '';
                    $total_meses++;
                    $saldo_vencido = $mensualidad * $total_meses;
                    $row['saldo_vencido'] = $saldo_vencido;
                    $currentMonth = $fecha_vencimiento->format('n');
                    for ($i = 0; $i < $total_meses; $i++) {
                        $monthName = DateTime::createFromFormat('!m', $currentMonth)->format('F');
                        $nombreMes = $arreglo_meses[$currentMonth];
                        $meses_nombres .= ' ' .$nombreMes . ", \n";
                        $currentMonth++;
                        if ($currentMonth > 12) {
                            $currentMonth = 1;
                        }
                    }
                    //
                } else {
                    $currentMonth = $fecha_vencimiento->format('n');
                    $saldo_vencido = $mensualidad * $total_meses;
                    $row['saldo_vencido'] = $saldo_vencido;
                    $nombreMes = $arreglo_meses[$currentMonth];
                    $meses_nombres= $nombreMes;
                }
                $row['meses_nombres']=$meses_nombres;
                $data[] = $row;

            }
        return $data;
    }else{
        return array();
    }
    
}