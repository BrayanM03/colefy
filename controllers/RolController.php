<?php

require_once __DIR__ . '/../models/Rol.php';
require_once __DIR__ . '/../controllers/DataTableController.php';


class RolController extends DataTableController {
    private $id_sesion;
    protected $current_datatable;
     
    public function __construct(){
        $this->id_sesion = $_SESSION['id'];
        parent::__construct();
        $this->model = new Rol();

    }

    public function datatable_roles(){
        $this->current_datatable = 'roles';
        $this->datatable_general();
    }

     // --- Implementación de los métodos de plantilla para la tabla de Recibos ---
     protected function getModelData($id_filtro, $start, $length, $search, $orderColumnName, $orderDir, $filtros) {
       
            return $this->model->datatables($id_filtro, $start, $length, $search, $orderColumnName, $orderDir);
    }

    protected function getModelTotal($id_filtro, $filtros) {
        
            return $this->model->contar($id_filtro);
    }

    protected function getModelFilteredTotal($id_filtro,$search, $filtros) {
       
        return $this->model->contarFiltrados($id_filtro, $search);
    } 

    function obtener_rol($tipo_resp, $id_rol){
        $resp = $this->model->obtenerRol($id_rol);
        if($tipo_resp==1){
            echo json_encode($resp);
        }else{
            return $resp;
        }
    }

    public function combo($tipo_resp, $busqueda = null){
        if($busqueda){
            $params =[$_SESSION['id_rol'],'%'.$busqueda.'%'];
            $sql_where = " AND id = ? AND (nombre LIKE ?)";
        }else{
            $params = [];
            $sql_where = '';//' AND id_escuela = ?';
        } 

        
        $total = $this->model->contar($sql_where, $params);
        if($total > 0){
            $data = $this->model->obtenerRoles($sql_where, $params);
            $estatus = true;
            $mensaje = 'Busqueda con resultados';
        }else{
            $mensaje='No se encontraron';
            $estatus = false;
            $data = [];
        }

        $response = array('estatus'=>$estatus, 'mensaje'=>$mensaje, 'data'=>$data, 'sql'=>$sql_where);
        if($tipo_resp == 1){
            echo json_encode($response);
        }else{
            return ($response);
        }
    }


}
?>
