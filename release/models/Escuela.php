<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/dates.php';

class Escuela {
    private $db;
    private $fecha;

    public function __construct() {
        $this->db = new Database();
        $this->fecha = new Date();
    }

    public function obtenerEscuelasDataTable($start, $length, $search) {

        $start = (int)$start;
        $length = (int)$length;
        $params = [];
        $sql = "SELECT * FROM escuelas WHERE estatus = 1";

           if (!empty($search)) {
                $sql .= " AND (nombre LIKE :search)";
                $params[':search'] = "%$search%";
            }

        $sql .= " LIMIT $start, $length";


        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarEscuelas($sql_where='', $params=[]) {
        /* $params['id_escuela'] = $this->id_escuela; */
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM escuelas WHERE estatus = 1 " . $sql_where, $params);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }


    /* public function contarEscuela($id_alumno) {
        $total = $this->db->count('alumnos', 'id = ? AND id_escuela = ?', [$id_alumno, $this->id_escuela]);
        return $total;
    } */


  /*   public function obtenerPorUsuario($username) {
        $stmt = $this->db->query("SELECT * FROM alumnos WHERE usuario = ?", [$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
 */
    public function obtenerEscuelas($sql_where='', $params=[]) {
        
        $stmt = $this->db->query("SELECT * FROM alumnos WHERE estatus = 1" . $sql_where . ' ORDER BY nombre ASC', $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Registrando
    public function registrarAlumno($nombre, $apellido_paterno, $apellido_materno, $cumple, $genero, $telefono){
        $fecha_registro = $this->fecha->obtenerFechaRegistro();
        $id_nuevo = $this->db->insert('alumnos',['nombre' => $nombre, 'apellido_paterno' => $apellido_paterno, 
        'apellido_materno' => $apellido_materno, 'fecha_cumple' => $cumple, 'genero' => $genero,
        'telefono' => $telefono, 'fecha_registro'=>$fecha_registro, 'estatus'=>1]);

        return [
            'estatus' => true,
            'mensaje' => 'Registro insertado correctamente',
            'nuevo'   => $id_nuevo
        ];
    }

    public function traerAlumno($id_alumno){
        $ciclo = $this->fecha->obtenerCicloActual();
        $ciclo = $ciclo[0];
        if($total>0){
            $data = $this->db->select('SELECT * FROM alumnos WHERE id = ? AND estatus = ?', [$id_alumno,1]);
            $data = $data[0];
            $count = $this->db->count('alumnos_grupo', 'id_alumno = ? AND id_ciclo = ? AND estatus = 1', 
            [$id_alumno, $ciclo['id']]);
            if($count >0){
                $data_group = $this->db->select('SELECT ag.*, g.nombre as grupo FROM alumnos_grupo ag INNER JOIN grupos g ON ag.id_grupo = g.id
                 WHERE ag.id_alumno = ? AND ag.id_ciclo = ? AND ag.estatus = 1', 
                [$id_alumno, $ciclo['id']]);
                $data['grupo']= $data_group[0];
                $data['grupo']['estatus'] = true;
                $data['grupo']['mensaje'] = 'Se encontró grupo';
                /* print_r($data[0]['grupo']);
                die(); */

            }else{
                $data['grupo']['estatus'] = false;
                $data['grupo']['mensaje'] = 'No se encontró grupo';
                $data['grupo']['grupo'] = 'Sin grupo';
            }
            $response= array('estatus'=>true, 'mensaje'=> 'Datos encontrados', 'data'=>$data);
        }else{
            $response= array('estatus'=>false, 'mensaje'=> 'Datos NO encontrados con ese ID', 'data'=>[]);
        }
        return $response;
    }

    public function actualizarAlumno($id_alumno, $nombre, $apellido_paterno, $apellido_materno, $cumple, $genero, $telefono){
        if($total>0){
            $campos = [
                'nombre'=> $nombre,
                'apellido_paterno' => $apellido_paterno,
                'apellido_materno' => $apellido_materno,
                'fecha_cumple' => $cumple,
                'genero' => $genero,
                'telefono' => $telefono, 
            ]; 
            $stmt = $this->db->update('alumnos', $campos, 'id=?',[$id_alumno]);
            $data = $this->db->select('SELECT * FROM alumnos WHERE id = ? AND estatus = 1', [$id_alumno]);
            $response= array('estatus'=>true, 'mensaje'=> 'Alumno actualizado correctamente', 'data'=>$data);
        }else{
            $response= array('estatus'=>false, 'mensaje'=> 'Datos NO encontrados con ese ID', 'data'=>[]);
        }
        return $response;
    }
}
