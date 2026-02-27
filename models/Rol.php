<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/dates.php';
require_once __DIR__ . '/../models/Datatable.php';


class Rol extends Datatable{
    private $tabla = 'roles';

    public function __construct() {
        $this->db = new Database();
    }

    public function datatables($id_filtro, $start, $length, $search, $orderColumn, $orderDir)
    {   
        $extraWhere = "";
        $params = [];
        // 2. Llama al método genérico de la clase Datatable
        return $this->getDataTable(
            $this->tabla,
            $start, 
            $length, 
            $search, 
            $orderColumn, 
            $orderDir, 
            ['nombre'], 
            $extraWhere, 
            $params
        );
    }

    public function contar($id_filtro)
    {
         // 2. Concatena el campo con el ID de filtro
         $extraWhere = "";
         $params = []; 
         return $this->countAll($this->tabla, $extraWhere, $params);
    }

    public function contarFiltrados($id_filtro, $search)
    {
        $extraWhere = "";
        $params = []; 
        return $this->countFiltered($this->tabla, $search,  ['nombre'], $extraWhere, $params);
  
    }

    public function obtenerRol($id_rol){

       $data = $this->db->select('SELECT * FROM roles WHERE id = ?', [$id_rol]);

       if(!empty($data)){
        $estatus = true;
        $mensaje = 'Se encontró rol';
       }else{
        $estatus = false;
        $mensaje = 'No se encontró';
       }
       return array('estatus'=> $estatus, 'mensaje'=> $mensaje, 'data'=>$data);
    }

    public function obtenerRoles($sql_where='', $params=[]) {
        
        $stmt = $this->db->query("SELECT * FROM roles WHERE estatus = 1" . $sql_where . ' ORDER BY nombre ASC', $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


}
?>