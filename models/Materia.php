<?php
require_once __DIR__ . '/../config/conexion.php';

class Materia {
    private $db;
    private $id_escuela;
    public function __construct() {
        $this->db = new Database();
        $this->id_escuela = $_SESSION['id_escuela'];
    }

    public function obtenerMateriasDataTable($start, $length, $search) {
        
        $start = (int)$start;
        $length = (int)$length;
        $params =[];
        $sql = "SELECT * FROM materias WHERE id_escuela = :id_escuela AND (estatus = 1 OR estatus = 2)";
        $params[':id_escuela'] = $this->id_escuela;
           if (!empty($search)) {
                $sql .= " AND (nombre LIKE :search OR codigo LIKE :search)";
                $params[':search'] = "%$search%";
            }

        $sql .= " LIMIT $start, $length";


        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarMaterias() {
      
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM materias WHERE id_escuela = ? AND (estatus = 1 OR estatus = 2)", [$this->id_escuela]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function obtenerListaMaterias() {

        $sql = "SELECT * FROM materias WHERE id_escuela = ? AND (estatus = 1 OR estatus = 2)";
        $stmt = $this->db->query($sql, [$this->id_escuela]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrarMateria($nombre, $codigo){
        if(empty($nombre)) return array('estatus'=>false, 'mensaje' => 'Coloca un nombre', 'data'=>[]);

        $data = array('nombre' =>$nombre, 'codigo'=> $codigo,
        'estatus'=>1, 'id_escuela' => $this->id_escuela);
        $id_materia = $this->db->insert('materias', $data);

        if($id_materia>0){
            $resp = array('estatus'=>true, 'mensaje' => 'Materia registrada correctamente', 'data'=>['id_nuevo' => $id_materia]);
        }else{
            $resp = array('estatus'=>false, 'mensaje' => 'No se pudo registrar la materia', 'data'=>[]);
        }
        return $resp;
    }
}
