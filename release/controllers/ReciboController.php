<?php
require_once __DIR__ . '/../models/Recibo.php';
require_once __DIR__ . '/../controllers/DataTableController.php';

class ReciboController extends DataTableController {
    private $id_sesion;
    private $id_escuela;
    protected $current_datatable; 

    public function __construct(){
        $this->id_sesion = $_SESSION['id'];
        $this->id_escuela = $_SESSION['id_escuela'];
        parent::__construct();
        // Inicializa el modelo específico para esta tabla
        $this->model = new Recibo();
    }

    public function datatable_recibos() {
        $this->current_datatable = 'vista_recibos';

        // Capturamos lo que enviamos desde el JS en el objeto "d"
        $filtros = [
            'tipo' => $_POST['f_tipo'] ?? null,
            'alumno' => $_POST['f_alumno'] ?? null,
            'fecha_inicio' => $_POST['f_inicio'] ?? null,
            'fecha_fin'    => $_POST['f_fin'] ?? null,
            'estatus'      => $_POST['f_estatus'] ?? null
        ];

        $this->datatable_general($this->id_escuela, $filtros);
       
    }

    //------LOGICA DE PRUEBA-----//
    public function datatable_pagos($id_recibo){
        $this->current_datatable = 'pagos';
        $this->datatable_general($id_recibo);
    }

    // --- Implementación de los métodos de plantilla para la tabla de Recibos ---
    protected function getModelData($id_filtro, $start, $length, $search, $orderColumnName, $orderDir, $filtros=[]) {
        if ($this->current_datatable === 'vista_recibos') {
          
            return $this->model->datatablesRecibos($id_filtro, $start, $length, $search, $orderColumnName, $orderDir, $filtros);
        } elseif ($this->current_datatable === 'conceptos') {
            return $this->model->datatablesConceptos(
                $id_filtro, $start, $length, $search, $orderColumnName, $orderDir
            );
        } elseif ($this->current_datatable === 'pagos') {
            // Llama al nuevo método del modelo
            return $this->model->datatablesPagos(
                $id_filtro, $start, $length, $search, $orderColumnName, $orderDir
            );
        }
        return [];
    }

    protected function getModelTotal($id_filtro, $filtros=[]) {
        if ($this->current_datatable === 'vista_recibos') {
            return $this->model->contarRecibos($id_filtro, $filtros);
        } elseif ($this->current_datatable === 'conceptos') {
            return $this->model->contarConceptos($id_filtro);
        } elseif ($this->current_datatable === 'pagos') {
            // Llama al nuevo método del modelo
            return $this->model->contarPagos($id_filtro);
        }
        return 0;
    }

    protected function getModelFilteredTotal($id_filtro,$search, $filtros=[]) {
        if ($this->current_datatable === 'vista_recibos') {
            return $this->model->contarRecibosFiltrados($id_filtro, $search, $filtros);
        } elseif ($this->current_datatable === 'conceptos') {
            return $this->model->contarConceptosFiltrados($id_filtro, $search);
        } elseif ($this->current_datatable === 'pagos') {
            // Llama al nuevo método del modelo
            return $this->model->contarPagosFiltrados($id_filtro, $search);
        }
        return 0;
    }

    //------LOGICA PRUEBA-----//

    public function datatable_conceptos($id_recibo, $tipo_filtro = 'nuevo_recibo') {

        // Configuramos la propiedad interna para el flujo de conceptos
        $this->current_datatable = 'conceptos'; 
        
        // Configuramos el modelo ANTES de llamar a datatable_general()
        // Esto reemplaza la lógica que tenías dentro de tu antiguo datatable_conceptos()
        if ($tipo_filtro === 'nuevo_recibo') {
            $this->model->setTablaConceptos('tabla_conceptos');
            $id_filtro = $this->id_sesion;
        } else { 
            $this->model->setTablaConceptos('detalle_conceptos');
            $id_filtro = $id_recibo;
        }

        $this->datatable_general($id_filtro);
    }

    //Nuevo recibo
    //Agregar concepto a tabla temporal
    public function agregar_conceptos($categoria, $cantidad, $precio, $importe, $mensualidad, $year){
        $recibo = new Recibo();
        $concepto = '';
        $concepto_response = $recibo->obtenerConcepto($categoria);
        $descripcion_concepto = $concepto_response['data'][0]['nombre'];
      
        switch($categoria){
            case 1:
                $meses = ['No borrar', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
                $concepto = "$descripcion_concepto de $meses[$mensualidad] $year.";
                break;

                   default:
                        $mensualidad=0;
                        $year=0;
                        $concepto = $descripcion_concepto . '.';
                        break;
        }

        $response = $recibo->registrarConcepto($concepto, $cantidad, $precio, $importe, $this->id_sesion, $mensualidad, $year, $categoria);


        echo json_encode([
            'estatus' => $response['estatus'],
            'mensaje' => $response['mensaje'],
            'data' => $response['data']
        ]);
    }

    public function eliminar_concepto($id_concepto){
        $recibo = new Recibo();
        $response = $recibo->eliminarConcepto($id_concepto, $this->id_sesion);

        echo json_encode([
            'estatus' => $response['estatus'],
            'mensaje' => $response['mensaje'],
            'data' => $response['data']
        ]);
    }

    public function sumatoria_conceptos($origen){

        $recibo = new Recibo();
        $resp = $recibo->sumatoriaConceptos($this->id_sesion);
        if($origen ==1){
            return $resp;
        }
    }

    public function registrar_recibo($alumno, $tipo_recibo, $formas_pago, 
    $pago_efectivo, $pago_tarjeta, $pago_transferencia, $pago_deposito, $pago_cheque, $comentario, $ciclo, $plazo){
    
        $recibo = new Recibo();
        $response = $recibo->registrarRecibo($alumno, $tipo_recibo, $formas_pago, 
        $pago_efectivo, $pago_tarjeta, $pago_transferencia, $pago_deposito, $pago_cheque, $comentario, $ciclo, $plazo, $this->id_sesion, $this->id_escuela);
       
        echo json_encode([
            'estatus' => $response['estatus'],
            'mensaje' => $response['mensaje'],
            'data' => $response['data']
        ]);
    }

    public function obtener_recibo($id_recibo, $tipo){
        $recibo = new Recibo();
        $response = $recibo->obtenerRecibo($id_recibo);
        if($tipo== 2){
            return ([
                'estatus' => $response['estatus'],
                'mensaje' => $response['mensaje'],
                'data' => $response['data']
            ]);
        }else if($tipo == 1){
            echo json_encode ([
                'estatus' => $response['estatus'],
                'mensaje' => $response['mensaje'],
                'data' => $response['data']
            ]);
        }
        
    }

    public function actualizar_estatus($id_recibo, $tipo_cancelacion ,$tipo){
        $recibo = new Recibo();
        if($tipo_cancelacion == 1){
            $response = $recibo->cancelarRecibo($id_recibo);
        }else{
            $response = $recibo->descancelarRecibo($id_recibo);
        } 
        if($tipo==2){
            return ([
                'estatus' => $response['estatus'],
                'mensaje' => $response['mensaje'],
                'data' => $response['data']
            ]);
        }else{
            echo json_encode ([
                'estatus' => $response['estatus'],
                'mensaje' => $response['mensaje'],
                'data' => $response['data']
            ]);
        }
        
    }

    public function realizar_pago($id_recibo, $formas_pago, $pago_efectivo, $pago_tarjeta, $pago_transferencia, $pago_deposito, $pago_cheque, $comentarios){
        $recibo = new Recibo();
        $response = $recibo
        ->realizarPago($id_recibo, $formas_pago, $pago_efectivo, $pago_tarjeta, $pago_transferencia, $pago_deposito, $pago_cheque, $comentarios);

        echo json_encode ([
            'estatus' => $response['estatus'],
            'mensaje' => $response['mensaje'],
            'data' => $response['data']
        ]);
    }

    public function cancelar_pago($id_pago){
        $recibo = new Recibo();
        $response = $recibo->cancelarPago($id_pago);

        echo json_encode ([
            'estatus' => $response['estatus'],
            'mensaje' => $response['mensaje'],
            'data' => $response['data']
        ]);
    }

    public function actualizar_datos_generales($tipo_resp, $data){
        $recibo = new Recibo();
        $response = $recibo->actualizarDatosGenerales($data);

        if($tipo_resp == 1){
            echo json_encode ([
                'estatus' => $response['estatus'],
                'mensaje' => $response['mensaje'],
                'data' => $response['data']
            ]);
        }else{
           return json_encode ([
                'estatus' => $response['estatus'],
                'mensaje' => $response['mensaje'],
                'data' => $response['data']
            ]);
        }
       
    }
}

