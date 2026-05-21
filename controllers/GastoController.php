<?php
require_once __DIR__ . '/../models/Gasto.php';
require_once __DIR__ . '/../controllers/DataTableController.php';

//Copiado de ReciboController
class GastoController extends DataTableController {
    private $id_sesion;
    private $id_escuela;
    protected $current_datatable; 

    public function __construct(){
        $this->id_sesion = $_SESSION['id'];
        $this->id_escuela = $_SESSION['id_escuela'];
        parent::__construct();
        // Inicializa el modelo específico para esta tabla
        $this->model = new Gasto();
    }

    public function datatable_gastos() {
        $this->current_datatable = 'vista_gastos';

        // Capturamos lo que enviamos desde el JS en el objeto "d"
        $filtros = [
            'folio' => $_POST['folio'] ?? null,
            'ciclo' => $_POST['ciclo'] ?? null,
            'subcategoria' => $_POST['subcategoria'] ?? null,
            'comprobante'    => $_POST['comprobante'] ?? null,
            'fecha_inicio'    => $_POST['inicio'] ?? null,
            'fecha_fin'    => $_POST['fin'] ?? null,
            'estatus'      => $_POST['estatus'] ?? null,
            'forma_pago'      => $_POST['forma_pago'] ?? null
        ];

        $this->datatable_general($this->id_escuela, $filtros);
       
    }

    // --- Implementación de los métodos de plantilla para la tabla de Recibos ---
    protected function getModelData($id_filtro, $start, $length, $search, $orderColumnName, $orderDir, $filtros=[]) {
        if ($this->current_datatable === 'vista_gastos') { 
            return $this->model->datatablesGastos($id_filtro, $start, $length, $search, $orderColumnName, $orderDir, $filtros);
        } 
        
        return [];
    }

    protected function getModelTotal($id_filtro, $filtros=[]) {
        if ($this->current_datatable === 'vista_gastos') {
            return $this->model->contarGastos($id_filtro, $filtros);
        } 
        return 0;
    }

    protected function getModelFilteredTotal($id_filtro,$search, $filtros=[]) {
        if ($this->current_datatable === 'vista_gastos') {
            return $this->model->contarGastosFiltrados($id_filtro, $search, $filtros);
        }
        return 0;
    }

    public function combo_categorias_gastos($tipo_resp){
        if($tipo_resp==1){
            echo json_encode($this->model->comboCategoriasGastos());
        }else{
            return ($this->model->comboCategoriasGastos());
        }
    }

    public function registrar_gasto($data, $tipo_resp){
        $resp = $this->model->registrarGasto($data);

        if($tipo_resp == 2){
            return $resp;
        }else{
            echo json_encode($resp);
        }
    }


    public function actualizar_gasto($data, $tipo_resp){
        $resp = $this->model->actualizarGasto($data);

        if($tipo_resp == 2){
            return $resp;
        }else{
            echo json_encode($resp);
        }
    }

    public function obtener_gasto($data, $tipo_resp){
        $resp = $this->model->obtenerGasto($data);

        if($tipo_resp == 2){
            return $resp;
        }else{
            echo json_encode($resp);
        }
    }

    public function cancelar_gasto($data, $tipo_resp){
        $id_gasto = $data['id_gasto'];
        if($data['tipo_cancelacion'] =='cancelado'){
            $resp =   $this->model->cancelarGasto($id_gasto);
        }else{
            $resp =  $this->model->descancelarGasto($id_gasto);
        }
        if($tipo_resp == 2){
            return $resp;
        }else{
            echo json_encode($resp);
        }
    }
    
}