<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/dates.php';
require_once __DIR__ . '/../models/Datatable.php';
require_once __DIR__ . '/../models/Alumno.php'; 

/* include "../helpers/response_helper.php"; */

class Recibo extends Datatable
{
    private $fecha;
    private $tabla_recibos = 'vista_recibos';
    private $tabla_conceptos;
    private $catalogo_conceptos = 'conceptos';
    private $tabla_pagos = 'pagos';
    private $allowedColumns = ['id', 'concepto'];
    private $id_escuela;

    public function __construct($nombre_tabla = 'tabla_conceptos')
    {
        $this->db = new Database();
        $this->fecha = new Date();
        $this->id_escuela = $_SESSION['id_escuela'];
        $this->tabla_conceptos = $nombre_tabla;
      
    }

    private function prepararFiltrosRecibos($id_escuela, $filtros) {
        $extraWhere = "id_escuela = :id_escuela"; 
        $params = [':id_escuela' => $id_escuela]; 
        
        // 2. Filtro de Alumno (Múltiple / Array)
        if (!empty($filtros['alumno']) && is_array($filtros['alumno'])) {
            $alumnos = $filtros['alumno'];
            $placeholders = [];
            foreach ($alumnos as $index => $id) {
                $key = ":alm_" . $index;
                $placeholders[] = $key;
                $params[$key] = $id; 
            }
            $extraWhere .= " AND id_alumno IN (" . implode(',', $placeholders) . ")";
        }

        // 2. Filtros de Fecha
        if (!empty($filtros['fecha_inicio']) && !empty($filtros['fecha_fin'])) {
            $extraWhere .= " AND fecha_registro BETWEEN :f_inicio AND :f_fin";
            $params[':f_inicio'] = $filtros['fecha_inicio'] . " 00:00:00";
            $params[':f_fin']    = $filtros['fecha_fin'] . " 23:59:59";
        }

        // 3. Filtro de tipo (Contado o parcial por el momento)
        if (isset($filtros['tipo']) && $filtros['tipo'] !== "") {
            $extraWhere .= " AND tipo = :tipo";
            $params[':tipo'] = $filtros['tipo'];
        }

       
        if (!empty($filtros['estatus']) && is_array($filtros['estatus'])) {
            $alumnos = $filtros['estatus'];
            $placeholders = [];
            foreach ($alumnos as $index => $id) {
                $key = ":est_" . $index;
                $placeholders[] = $key;
                $params[$key] = $id; 
            }
            $extraWhere .= " AND estatus IN (" . implode(',', $placeholders) . ")";
        }
        
        return ['where' => $extraWhere, 'params' => $params];
    }

    //-------INICIO FUNCIONES DATATABLES-----
    /*Funcion que obtiene los datos iniciales recibe el id de la escuela y filtros extra*/
    public function datatablesRecibos($id_filtro, $start, $length, $search, $orderColumn, $orderDir, $filtros=[])
    {
        $prep = $this->prepararFiltrosRecibos($id_filtro, $filtros);
        // 2. Llama al método genérico de la clase Datatable
        return $this->getDataTable(
            $this->tabla_recibos,
            $start, 
            $length, 
            $search, 
            $orderColumn, 
            $orderDir, 
            ['id', 'alumno', 'ciclo', 'fecha_registro', 'monto_total'], 
            $prep['where'], 
            $prep['params']
        );
    }

    /*Funcion contar recibos que recibe el id de la escuela y filtros extra*/
    public function contarRecibos($id_filtro, $filtros = [])
    {
         $prep = $this->prepararFiltrosRecibos($id_filtro, $filtros);
         return $this->countAll($this->tabla_recibos, $prep['where'], $prep['params']);
    }

    /*Funcion contar recibos filtrados que recibe el id de la escuela y filtros extra, para cuando se usan los filtros*/
    public function contarRecibosFiltrados($id_filtro, $search, $filtros = [])
    {
      
        $prep = $this->prepararFiltrosRecibos($id_filtro, $filtros);
        return $this->countFiltered($this->tabla_recibos, $search,  ['alumno', 'ciclo'], $prep['where'], $prep['params']);
  
    }

    public function setTablaConceptos($nombre_tabla)
    {
        $this->tabla_conceptos = $nombre_tabla;
    }

    public function datatablesConceptos($id_filtro, $start, $length, $search, $orderColumn, $orderDir)
    {

        // 1. Determinar el campo de filtro basado en la tabla actual
        $campo_filtro = '';
        if ($this->tabla_conceptos === 'tabla_conceptos') {
            // En tabla_conceptos filtras por id_sesion
            $campo_filtro = "id_usuario = ?";
        } elseif ($this->tabla_conceptos === 'detalle_conceptos') {
            // En detalle_conceptos filtras por id_recibo
            $campo_filtro = "id_recibo = ?";
        } else {
            // Manejar un caso por defecto o lanzar un error si es una tabla desconocida
            $campo_filtro = "1 = 1"; // Sin filtro
        }

        // La condición WHERE completa será $campo_filtro . $id_filtro
        //extraWhere = $campo_filtro . ":".$id_filtro;

        return $this->getDataTable(
            $this->tabla_conceptos, // Usa la propiedad de instancia
            $start,
            $length,
            $search,
            $orderColumn,
            $orderDir,
            $this->allowedColumns,
            $campo_filtro, // Usa la condición WHERE determinada
            [$id_filtro]
        );
        
    }

    public function contarConceptos($id_filtro, $params = [])
    {
        // 1. Obtiene el prefijo de la condición (ej: "id_sesion = " o "id_recibo = ")
        $campo_filtro = $this->_getCampoFiltro();

        // 2. Concatena el campo con el ID de filtro
        $extraWhere = $campo_filtro . $id_filtro;


        // 3. Llama al método countAll con la tabla y la condición correctas
        // Asumiendo que countAll es un método de la clase padre Datatable que cuenta todos los registros que cumplen extraWhere
        return $this->countAll($this->tabla_conceptos, $extraWhere, $params);
    }

    public function contarConceptosFiltrados($id_filtro, $search, $params = [])
    {
        // 1. Determinar el campo de filtro (id_sesion = o id_recibo =)
        $campo_filtro = $this->_getCampoFiltro();

        // 2. Construir la condición WHERE adicional (ej: "id_sesion = 123")
        $extraWhere = $campo_filtro . $id_filtro;

       
        return $this->countFiltered(
            $this->tabla_conceptos,
            $search,
            $this->allowedColumns,
            $extraWhere, // Aquí pasamos la condición dinámica (id_sesion = X O id_recibo = Y)
            $params
        );
    }

    public function obtenerConcepto($id_concepto)
    {
        $where = 'id = ?';
        $no_conceptos = $this->countAll($this->catalogo_conceptos, $where, [$id_concepto]);
        if ($no_conceptos > 0) {
            $concepto = $this->db->select('SELECT * FROM conceptos WHERE id =?', [$id_concepto]);

            $resp = array('estatus' => true, 'data' => $concepto, 'mensaje' => 'Se encontraron datos');
        } else {
            $resp = array('estatus' => false, 'data' => [], 'mensaje' => 'No se encontraron datos');
        }
        return $resp;
    }

    protected function _getCampoFiltro()
    {
        if ($this->tabla_conceptos === 'tabla_conceptos') {
            // Cuando es tabla_conceptos, el filtro es por id_sesion
            return "id_usuario = ";
        } elseif ($this->tabla_conceptos === 'detalle_conceptos') {
            // Cuando es detalle_conceptos, el filtro es por id_recibo
            return "id_recibo = ";
        }
        // Caso por defecto (puedes ajustarlo según tu necesidad)
        return "1 = 1";
    }

    public function datatablesPagos($id_filtro, $start, $length, $search, $orderColumnName, $orderDir){
        // 1. Condición WHERE específica (filtrar por id_escuela y id_sesion si aplica)
        $extraWhere = "id_recibo = :id_recibo";
        $params = [':id_recibo' => $id_filtro]; 
        
        // 2. Llama al método genérico de la clase Datatable
        return $this->getDataTable(
            $this->tabla_pagos,
            $start, 
            $length, 
            $search, 
            $orderColumnName, 
            $orderDir, 
            ['id_recibo', 'total', 'fecha'], 
            $extraWhere, 
            $params
        );
    }

    public function contarPagos($id_filtro)
    {
        // 2. Concatena el campo con el ID de filtro
        $extraWhere = "id_recibo = :id_recibo";
        $params = [':id_recibo' => $id_filtro]; 


        // 3. Llama al método countAll con la tabla y la condición correctas
        // Asumiendo que countAll es un método de la clase padre Datatable que cuenta todos los registros que cumplen extraWhere
        return $this->countAll($this->tabla_pagos, $extraWhere, $params);
    }

    public function contarPagosFiltrados($id_filtro, $search)
    {
        $extraWhere = "id_recibo = :id_recibo";
        $params = [':id_recibo' => $id_filtro];
        return $this->countFiltered($this->tabla_pagos, $search,  ['id_recibo', 'total', 'fecha'], $extraWhere, $params);
    }

    //Agregar conceptos a tabla conceptos
    public function registrarConcepto($concepto, $cantidad, $precio, $importe, $id_sesion, $mensualidad, $year, $id_concepto)
    {

        if ($mensualidad != 0) {
            $count = $this->db->count('tabla_conceptos', 'mensualidad = ? AND year = ? AND id_usuario = ?', [$mensualidad, $year, $id_sesion]);
            if ($count > 0) {
                return array('estatus' => false, 'mensaje' => 'Esta mensualidad ya esta agregada ', 'data' => []);
            }
        }

        $id_concepto = $this->db->insert(
            $this->tabla_conceptos,
            ['id_concepto' => $id_concepto,'concepto' => $concepto, 'cantidad' => $cantidad, 'precio_unitario' => $precio, 'importe' => $importe,
            'mensualidad' => $mensualidad, 'year' => $year, 'id_usuario' => $id_sesion]
        );

        $resp_suma =  $this->sumatoriaConceptos($id_sesion);

        if ($id_concepto > 0) {
            $estatus = true;
            $mensaje = 'Concepto agregado';
            $data = array('id_concepto' => $id_concepto, 'suma' => $resp_suma['suma']);
        } else {
            $estatus = false;
            $mensaje = 'Hubo un error';
            $data = [];
        }

        return array('estatus' => $estatus, 'mensaje' => $mensaje, 'data' => $data);
    }

    public function eliminarConcepto($id_concepto, $id_sesion)
    {
        $this->db->delete('tabla_conceptos', 'id=?', [$id_concepto]);
        $estatus = true;
        $mensaje = 'Concepto eliminado';
        $resp_suma =  $this->sumatoriaConceptos($id_sesion);
        $data = ['suma' => $resp_suma['suma']];

        return array('estatus' => $estatus, 'mensaje' => $mensaje, 'data' => $data);
    }

    public function sumatoriaConceptos($id_sesion)
    {
        $suma = $this->db->sum('tabla_conceptos', 'importe', 'id_usuario = ?', [$id_sesion]);

        $estatus = true;
        $mensaje = 'Sumatoria correcta';
        return array('estatus' => $estatus, 'mensaje' => $mensaje, 'suma' => $suma);
    }


    //Registrar Recibo
    public function registrarRecibo(
        $alumno,
        $tipo_recibo,
        $formas_pago,
        $pago_efectivo,
        $pago_tarjeta,
        $pago_transferencia,
        $pago_deposito,
        $pago_cheque,
        $comentario,
        $ciclo,
        $plazo,
        $id_sesion
    ) {

        $no_conceptos = $this->contarConceptos($id_sesion);

        if ($no_conceptos > 0) {
            $conceptos = $this->db->select('SELECT * FROM tabla_conceptos WHERE id_usuario =?', [$id_sesion]);
            $monto_total = $this->db->sum('tabla_conceptos', 'importe', 'id_usuario =?', [$id_sesion]);
            $fecha = $this->fecha->obtenerFechaRegistro();

            $ingreso = floatval($pago_efectivo) + floatval($pago_tarjeta) +
            floatval($pago_transferencia) + floatval($pago_deposito) + floatval($pago_cheque);

            if ($ingreso == $monto_total) {
                $tipo_recibo = 1;
            }

            if ($tipo_recibo == 1) {
                $plazo = 0;
                $fecha_vencimiento = $fecha;
                $saldo_pendiente = 0;
                $adelanto = 0;
            } elseif ($tipo_recibo == 2) {
                // Convertimos la fecha de registro a objeto DateTime
                $fecha_base = new DateTime($fecha);

                // Según el plazo, sumamos el intervalo correspondiente
                switch ($plazo) {
                    case 1: // 7 días
                        $fecha_base->modify('+7 days');
                        break;
                    case 2: // 15 días
                        $fecha_base->modify('+15 days');
                        break;
                    case 3: // 1 mes
                        $fecha_base->modify('+1 month');
                        break;
                    case 4: // 45 días
                        $fecha_base->modify('+45 days');
                        break;
                    case 5: // 1 día
                        $fecha_base->modify('+1 day');
                        break;
                }

                // Guardamos la nueva fecha formateada
                $fecha_vencimiento = $fecha_base->format('Y-m-d');
                $adelanto = floatval($pago_efectivo) + floatval($pago_tarjeta) +
                floatval($pago_transferencia) + floatval($pago_deposito) + floatval($pago_cheque);
                $saldo_pendiente = $monto_total - $adelanto;
            }

            $sql = '
                SELECT folio_escuela
                FROM recibos
                WHERE id_escuela = ?
                ORDER BY folio_escuela DESC
                LIMIT 1
            ';

            $res = $this->db->select($sql, [$this->id_escuela]);
            $ultimo_folio = $res[0]['folio_escuela'] ?? 0;

            $nuevo_folio = $ultimo_folio + 1;
            $data = array(
                'folio_escuela' => $nuevo_folio,
                'id_alumno' => $alumno,
                'tipo' => $tipo_recibo,
                'id_ciclo' => $ciclo,
                'plazo' => $plazo,
                'fecha_registro' => $fecha,
                'fecha_vencimiento' => $fecha_vencimiento,
                'monto_total' => $monto_total,
                'saldo_pendiente' => $saldo_pendiente,
                'estatus' => 1,
                'comentario' => $comentario,
                'id_escuela' => $this->id_escuela
            );
            $id_recibo =  $this->db->insert('recibos', $data);

            if ($id_recibo > 0) {
                foreach ($conceptos as $key => $value) {

                    $concepto = array(
                        'concepto' => $value['concepto'],
                        'cantidad' => $value['cantidad'],
                        'precio_unitario' => $value['precio_unitario'],
                        'importe' => $value['importe'],
                        'mensualidad' => $value['mensualidad'],
                        'year' => $value['year'],
                        'id_concepto' => $value['id_concepto'],
                        'id_recibo' => $id_recibo
                       );

                    $insert = $this->db->insert('detalle_conceptos', $concepto);
                }

                //Insertando pago
                if (($tipo_recibo == 2 && $adelanto > 0) || $tipo_recibo == 1) {
                    $pago_data = array(
                        'id_recibo' => $id_recibo,
                        'total' => $tipo_recibo == 1 ? $monto_total : $adelanto,
                        'pago_efectivo' => $pago_efectivo,
                        'pago_tarjeta' => $pago_tarjeta,
                        'pago_transferencia' => $pago_transferencia,
                        'pago_deposito' => $pago_deposito,
                        'pago_cheque' => $pago_cheque,
                        'estatus' => 1,
                        'tipo' =>  $tipo_recibo == 1 ? 1 : 0, //Si es contado (1) es pago que liquidó de lo contrario es abono
                        'fecha' => $fecha
                    );
                    $id_pago = $this->db->insert('pagos', $pago_data);
                    return array(
                        'estatus' => true,
                        'mensaje' => 'El recibo se registró correctamente',
                        'data' => [
                            'id_recibo' => $id_recibo,
                            'id_pago' => $id_pago
                        ]
                    );
                }
            } else {
                return array(
                    'estatus' => false,
                    'mensaje' => 'El recibo no se registró, contacta al administrador',
                    'data' => []
                );
            }


        } else {
            return array(
                'estatus' => false,
                'mensaje' => 'No se encontraron conceptos en la tabla',
                'data' => []
            );
        }

    }

    public function obtenerRecibo($id_recibo)
    {
        $count = $this->db->count('recibos', 'id = ?', [$id_recibo]);
        if ($count == 0) {
            return array('estatus' => false, 'mensaje' => 'No se encontró un recibo con el ID: ' . $id_recibo, 'data' => []);
        } else {
            $recibo_data = $this->db->select('SELECT * FROM recibos WHERE id =?', [$id_recibo]);
            $recibo_data = $recibo_data[0];
            $id_alumno = $recibo_data['id_alumno'];
            $count_al = $this->db->count('alumnos', 'id = ? AND id_escuela = ? AND estatus = ?', [$id_alumno, $this->id_escuela, 1]);
            if ($count_al == 0) {
                return array('estatus' => false, 'mensaje' => 'No se encontró alumno en la BD del recibo', 'data' => []);
            } else {
                $alumno_model = new Alumno();
                $alumno_resp = $alumno_model->traerAlumno($id_alumno);//$this->db->select('SELECT * FROM alumnos WHERE id =?',[$id_alumno]);
                $alumno = $alumno_resp['data'];
                /* echo json_encode($alumno_resp['data']['grupo']);
                die(); */
                $recibo_data['grupo'] = $alumno['grupo']['grupo'];
                $recibo_data['id_grupo'] = $alumno['grupo']['id_grupo'];

            }

            $count_conceptos = $this->db->count('recibos', 'id = ?', [$id_recibo]);
            if ($count_conceptos == 0) {
                return array('estatus' => false, 'mensaje' => 'No se encontrarón conceptos en el recibo', 'data' => []);
            } else {
                $conceptos = $this->db->select('SELECT * FROM detalle_conceptos WHERE id_recibo =?', [$id_recibo]);
            }

            $recibo_data['conceptos'] = $conceptos;
            $recibo_data['alumno'] = $alumno['nombre'] .' '. $alumno['apellido_paterno'] . ' '. $alumno['apellido_materno'];

            return array('estatus' => true, 'mensaje' => 'Se encontró información de recibo', 'data' => $recibo_data);
        }
    }

    public function cancelarRecibo($id_recibo)
    {
        $count = $this->db->count('recibos', 'id = ?', [$id_recibo]);
        if ($count == 0) {
            return array('estatus' => false, 'mensaje' => 'No se encontró un recibo con el ID: ' . $id_recibo, 'data' => []);
        } else {
            $this->db->cancel('recibos', $id_recibo);
            return array('estatus' => true, 'mensaje' => '<b>Recibo:</b> REC-' . $id_recibo .' cancelado correctamente', 'data' => []);

        }
    }

    public function descancelarRecibo($id_recibo)
    {
        $count = $this->db->count('recibos', 'id = ?', [$id_recibo]);
        if ($count == 0) {
            return array('estatus' => false, 'mensaje' => 'No se encontró un recibo con el ID: ' . $id_recibo, 'data' => []);
        } else {
            $this->db->uncancel('recibos', $id_recibo);
            return array('estatus' => true, 'mensaje' => '<b>Recibo:</b> REC-' . $id_recibo .' descancelado correctamente', 'data' => []);

        }
    }

    public function realizarPago($id_recibo, $formas_pago, $pago_efectivo, $pago_tarjeta, $pago_transferencia, $pago_deposito, $pago_cheque, $comentarios){

        $resp_recibo = $this->obtenerRecibo($id_recibo);
        if($resp_recibo['estatus']){
            $data_recibo = $resp_recibo['data'];
            $fecha = $this->fecha->obtenerFechaRegistro();
            $monto = floatval($formas_pago) + floatval($pago_efectivo) + floatval($pago_tarjeta) + floatval($pago_transferencia) + floatval($pago_deposito) + floatval($pago_cheque);
            $saldo_pendiente = floatval($data_recibo['saldo_pendiente']);
            $tipo_recibo = $data_recibo['tipo'];
            $estatus_recibo = $data_recibo['estatus'];
           
            if($tipo_recibo == 1 && (($saldo_pendiente - $monto) > 0)){
             
            
                    $estatus = false;
                    $mensaje = 'Cambia el tipo de recibo a "Parcial" para dar abonos o liquidalo con la cantidad total';
                    $data = []; 
                    return array('estatus'=> $estatus, 'mensaje' => $mensaje, 'data'=>$data);
                
            }else
            
            if($saldo_pendiente ==0){
                $estatus = false;
                $mensaje = 'El recibo ya esta liquidado';
                $data = []; 
            }else if($estatus_recibo==0){
                $estatus = false;
                $mensaje = 'El recibo esta cancelado, no se puede abonar';
                $data = []; 
            }else if($estatus_recibo ==3){
                $estatus = false;
                $mensaje = 'El recibo ya esta liquidado, no se puede abonar';
                $data = []; 
            }else if($monto <= 0){
                $estatus = false;
                $mensaje = 'El monto no puede ser igual o menor a 0';
                $data = [];

            }else if($monto > $saldo_pendiente){
                $estatus = false;
                $mensaje = 'El monto no puedes mayor al restante';
                $data = [];
            }else{

                $nuevo_saldo = $saldo_pendiente - $monto;
                if($nuevo_saldo == 0){
                    $nuevo_estatus = 3;
                    $tipo_abono = 1;
                }else{
                    $nuevo_estatus = 2;
                    $tipo_abono = 0;
                }
             
                $pago_data = array(
                    'id_recibo' => $id_recibo,
                    'total' => $monto,
                    'pago_efectivo' => $pago_efectivo,
                    'pago_tarjeta' => $pago_tarjeta,
                    'pago_transferencia' => $pago_transferencia,
                    'pago_deposito' => $pago_deposito,
                    'pago_cheque' => $pago_cheque,
                    'estatus' => 1,
                    'tipo' =>  $tipo_abono, //Si es contado (1) es pago que liquidó de lo contrario es abono
                    'fecha' => $fecha,
                    'comentarios'=>$comentarios
                );
                $id_pago = $this->db->insert('pagos', $pago_data);
                
                $data_update = ['estatus'=> $nuevo_estatus, 'saldo_pendiente' => $nuevo_saldo];
                $this->actualizarRecibo($id_recibo, $data_update);
                
                $resp_recibo_ = $this->obtenerRecibo($id_recibo);
                $data_recibo = $resp_recibo_['data'];
                $estatus = true;
                $mensaje = 'Pago realizado con exito';
                $data = array('id_pago' => $id_pago, 'nuevo_saldo' => $nuevo_saldo, 'recibo'=> $data_recibo);
            }
          
        }else{
            $estatus = false;
            $mensaje = 'No se encontro un estatus valido al consultar el recibo';
            $data = [];
        }

        return array('estatus'=> $estatus, 'mensaje' => $mensaje, 'data'=>$data);
        
    }

    public function actualizarRecibo($id_recibo, $data){

        return $this->db->update('recibos', $data, 'id = ?', [$id_recibo]); 
    }

    public function actualizarPago($id_pago, $data){

        $this->db->update('pagos', $data, 'id = ?', [$id_pago]);; 
    }

    public function cancelarPago($id_pago){
        $resp_pago = $this->obtenerPago($id_pago);
        $data_pago = $resp_pago['data'];
       
        $monto_pago = $data_pago['total'];
        $id_recibo = $data_pago['id_recibo'];
        $resp_recibo = $this->obtenerRecibo($id_recibo);
        $data_recibo = $resp_recibo['data'];
        $saldo_pendiente = $data_recibo['saldo_pendiente'];
        $total_recibo = $data_recibo['monto_total'];
        $nuevo_saldo = $saldo_pendiente + $monto_pago;
        /* print_r($id_recibo);
        die(); */
        if($nuevo_saldo == $total_recibo){
            $nuevo_estatus = 1;
        }
        if($nuevo_saldo < $total_recibo && $nuevo_saldo > 0){
            $nuevo_estatus = 2;
        }

        $data_recibo_upd = ['saldo_pendiente' => $nuevo_saldo, 'estatus' => $nuevo_estatus];
        $data_pago_upd = ['estatus' => 0];
        $this->actualizarRecibo($id_recibo, $data_recibo_upd);
        $this->actualizarPago($id_pago, $data_pago_upd);

        $resp_recibo_actualizado = $this->obtenerRecibo($id_recibo);
        $data_recibo_actualizado = $resp_recibo_actualizado['data'];

        return ['estatus' => true, 'mensaje' => 'Pago cancelado con exito', 'data'=> $data_recibo_actualizado];
        
    }

    public function obtenerPago($id_pago){
        $count = $this->db->count('pagos', 'id = ?', [$id_pago]);
        if ($count == 0) {
            return array('estatus' => false, 'mensaje' => 'No se encontró un pago con el ID: ' . $id_pago, 'data' => []);
        } else {
            $pago_select = $this->db->select('SELECT * FROM pagos WHERE id =?', [$id_pago]);
            $pago_data = $pago_select[0];
            return array('estatus' => true, 'mensaje' => 'Se encontró información del pago', 'data' => $pago_data);
        }
    }

    public function actualizarDatosGenerales($data){
        $id_recibo = $data['id_recibo'];
        $data_recibo_upd =
        ['id_alumno' =>  $data['id_alumno'], 'tipo'=> $data['tipo'], 'fecha_registro'=>$data['fecha'], 'comentario'=> $data['comentarios'],
         'estatus' => $data['estatus']];

         if($data['tipo'] == 2){
            $data_recibo_upd['fecha_vencimiento'] = $data['fecha_vencimiento'];
         }
        
       $filas_afectadas = $this->actualizarRecibo($id_recibo, $data_recibo_upd);
       $data_resp = array('filas_afectadas' => $filas_afectadas);
       $response =  array('estatus' => true, 'mensaje' => 'Recibo actualizado', 'data' => $data_resp);
    
       return$response ;
    }


}
