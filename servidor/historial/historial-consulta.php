<?php
session_start();
include '../database/conexion.php';


if (isset($_POST)) {

    $lote = isset($_GET['lote']) ? $_GET['lote'] :'';
    $manzana = isset($_GET['manzana']) ? $_GET['manzana'] :'';
    $search_value = isset($_POST['search']['value']) ? $_POST['search']['value'] : ''; // Valor de búsqueda enviado desde DataTables
    $search_query = "%{$search_value}%";
    $proyecto_id = $_GET['proyecto_id'];
    
    $where_count=' WHERE 1';
    $params = array();

    if($proyecto_id !=''){
        $where_count .= ' AND o.proyecto_id = :proyecto';
        $params[':proyecto'] =$proyecto_id;
    }

    if($manzana !=''){
        $where_count .= ' AND do.manzana = :manzana';
        $params[':manzana'] =$manzana;
    }
    if($lote !=''){
        $where_count .= ' AND do.lote = :lote'; 
        $params[':lote'] =$lote;
    }

    

    if($search_value==''){
        $query_mostrar = $con->prepare("SELECT COUNT(*) total FROM ordenes o 
        INNER JOIN detalle_orden do ON o.id = do.orden_id $where_count");

    }else{
        $query_mostrar = $con->prepare("SELECT COUNT(*) total FROM ordenes o INNER JOIN detalle_orden do ON o.id = do.orden_id 
        $where_count AND o.cliente_etiqueta LIKE :cliente_etiqueta OR o.telefono LIKE :telefono OR o.correo LIKE :correo");
       // $query_mostrar->bind_param('ssss', $search_query, $search_query, $search_query, $search_query);
        $params[':cliente_etiqueta'] = $search_query;
        $params[':telefono'] = $search_query;
        $params[':correo'] = $search_query;
    }

   // Vincular los parámetros dinámicamente
  
    foreach ($params as $key => &$val) {
        $query_mostrar->bindParam($key, $val);
    }
    $query_mostrar->execute();
    $total = $query_mostrar->fetchColumn();
    $query_mostrar->closeCursor();
    // Pagination
    //$results_per_page = 10;
    $results_per_page = isset($_POST['length']) ? $_POST['length'] :10;
    $current_page = isset($_POST['page']) ? $_POST['page'] : 1;
    $total_pages = ceil($total / $results_per_page);
    $offset = ($current_page - 1) * $results_per_page;

    $order_column_index = $_POST['order'][0]['column']; // Índice de la columna de ordenamiento
    $order_column_name = $_POST['columns'][$order_column_index]['data']; // Nombre de la columna de ordenamiento
    $order_direction = $_POST['order'][0]['dir']; // Dirección de ordenamiento (ascendente o descendente)

    $order_by = 'o.' . $order_column_name . ' ' . $order_direction;
    
    if ($total > 0) {

        if($search_value==''){
            $sqlTraerOrdenes = $con->prepare("SELECT DISTINCT o.* FROM ordenes o 
            INNER JOIN detalle_orden do ON o.id = do.orden_id $where_count ORDER BY $order_by LIMIT :offset, :result_per_page");
    
        }else{
            $sqlTraerOrdenes = $con->prepare("SELECT DISTINCT o.* FROM ordenes o INNER JOIN detalle_orden do ON o.id = do.orden_id 
            $where_count AND (o.cliente_etiqueta LIKE :cliente_etiqueta OR o.telefono LIKE :telefono OR o.correo LIKE :correo) 
             ORDER BY $order_by LIMIT :offset, :result_per_page");
        }
      
                                                      
        $params[':offset'] = $offset;
        $params[':result_per_page'] =  $results_per_page;   
                                
        foreach ($params as $key => &$val) {
            if ($key == ':result_per_page' || $key == ':offset') {
                $sqlTraerOrdenes->bindValue($key, $val, PDO::PARAM_INT);
            } else {
                $sqlTraerOrdenes->bindParam($key, $val);
            }
        }
        $sqlTraerOrdenes->execute();
        while ($fila = $sqlTraerOrdenes->fetch()) {

            $data['data'][] = $fila;
        }

        $data['total_pages'] = $total_pages;
        $data['current_page'] = $current_page;
        $data['recordsTotal'] = $total;
        $data['recordsFiltered'] = $total;

        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    } else {
        $data['data'] = [];
        $data['total_pages'] = 0;
        $data['current_page'] = 1;
        $data['recordsTotal'] = 0;
        $data['recordsFiltered'] = 0;
        echo json_encode($data, JSON_UNESCAPED_UNICODE);

    }
} else {
    print_r("Error al conectar");
}
?>
