<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/dates.php';
require_once __DIR__ . '/../models/Datatable.php';
  
 
class Usuario extends Datatable{
    private $tabla_usuarios = 'usuarios';

    public function __construct() {
        $this->db = new Database();
    }

    public function datatablesUsuarios($id_filtro, $start, $length, $search, $orderColumn, $orderDir)
    {   
        $extraWhere = "";
        $params = [];
        // 2. Llama al método genérico de la clase Datatable
        return $this->getDataTable(
            $this->tabla_usuarios,
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

    public function contarUsuarios($id_filtro)
    {
         // 2. Concatena el campo con el ID de filtro
         $extraWhere = "";
         $params = []; 
         return $this->countAll($this->tabla_usuarios, $extraWhere, $params);
    }

    public function contarUsuariosFiltrados($id_filtro, $search)
    {
        $extraWhere = "";
        $params = []; 
        return $this->countFiltered($this->tabla_usuarios, $search,  ['nombre', 'apellido'], $extraWhere, $params);
  
    }


    public function obtenerPorUsuario($username) {
        $stmt = $this->db->query("SELECT u.*, r.nombre as nombre_rol, e.nombre as escuela FROM usuarios u
        INNER JOIN roles r ON u.rol = r.id 
        INNER JOIN escuelas e ON e.id = u.id_escuela WHERE usuario = ?", [$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function obtenerPorIDUsuario($id_usuario) {
        $stmt = $this->db->query("SELECT u.*, r.nombre as nombre_rol, e.nombre as escuela  FROM usuarios u 
        INNER JOIN roles r ON u.rol = r.id 
        INNER JOIN escuelas e ON e.id = u.id_escuela WHERE u.id = ?", [$id_usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizarUsuario($id_usuario, $data_update){
      
        return $this->db->update('usuarios', $data_update, 'id  = ?', [$id_usuario]);
    }
}
