<?php
require_once __DIR__ . '/../config/conexion.php';

class Profesor {
    private $db;
    private $id_escuela;

    public function __construct() {
        $this->db = new Database();
        $this->id_escuela = $_SESSION['id_escuela'];
    }

    public function obtenerProfesoresDataTable($start, $length, $search) {

        $start = (int)$start;
        $length = (int)$length; 
        $params =[];
        $sql = "SELECT * FROM vista_profesores WHERE estatus = 1 AND id_escuela = :id_escuela";
        $params[':id_escuela'] = $this->id_escuela;
           if (!empty($search)) {
                $sql .= " AND (nombre LIKE :search OR apellido LIKE :search)";
                $params[':search'] = "%$search%";
            }

        $sql .= " LIMIT $start, $length";


        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarProfesores() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM vista_profesores WHERE estatus = 1 AND id_escuela = ?", [$this->id_escuela]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function obtenerListaProfesores() {

        $sql = "SELECT * FROM vista_profesores WHERE estatus = 1 AND id_escuela = ?";
        $stmt = $this->db->query($sql, [$this->id_escuela]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerGruposProfesor($id_profesor){
        $stmt = $this->db->query('SELECT count(DISTINCT g.id) as total FROM grupos g JOIN grupos_horarios gh ON g.id = gh.id_grupo JOIN detalle_horario dh ON gh.id_horario = dh.id_horario WHERE dh.id_profesor = ?;
        ', [$id_profesor]);
        $total_ =  $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        if($total_ > 0){

        $stmt = $this->db->query('SELECT DISTINCT g.* FROM grupos g JOIN grupos_horarios gh ON g.id = gh.id_grupo 
        JOIN detalle_horario dh ON gh.id_horario = dh.id_horario WHERE dh.id_profesor = ?;', [$id_profesor]);
        $data =  $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array('estatus'=>true, 'mensaje'=>'Grupos encontrados', 'data'=> $data);
        }else{
            return array('estatus'=>false, 'mensaje'=>'Grupos no encontrados para este profesor(a)', 'data'=> []);

        }
    }
}
