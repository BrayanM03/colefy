<?php

require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../controllers/DataTableController.php';


class UsuarioController extends DataTableController {
    private $id_sesion;
    protected $current_datatable;
    
    public function __construct(){
        $this->id_sesion = $_SESSION['id'];
        parent::__construct();
        $this->model = new Usuario();
    }

    public function datatable_usuarios() {
        $this->current_datatable = 'usuarios';
        $this->datatable_general();
    }


    // --- Implementación de los métodos de plantilla para la tabla de Recibos ---
    protected function getModelData($id_filtro, $start, $length, $search, $orderColumnName, $orderDir, $filtros) {
        if ($this->current_datatable === 'usuarios') {
            return $this->model->datatablesUsuarios($id_filtro, $start, $length, $search, $orderColumnName, $orderDir);
        } 
        return [];
    }

    protected function getModelTotal($id_filtro, $filtros) {
        if ($this->current_datatable === 'usuarios') {
            return $this->model->contarUsuarios($id_filtro);
        } 
        return 0;
    }

    protected function getModelFilteredTotal($id_filtro,$search, $filtros) {
        if ($this->current_datatable === 'usuarios') {
            return $this->model->contarUsuariosFiltrados($id_filtro, $search);
        }
        return 0;
    } 

    public function obtener_usuario($id_usuario){
        $usuario = new Usuario();
        $usuario->obtenerPorIDUsuario($id_usuario);
    }
}
 
?>
