<?php
declare(strict_types=1);
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
    public function registrarEscuela($nombre, $direccion, $cedula, $telefono, $fecha_registro,
    $estatus, $fecha_pago, $nombre_logo){
        $fecha_registro = $this->fecha->obtenerFechaRegistro();
        $id_nuevo = $this->db->insert('escuelas',['nombre' => $nombre, 'direccion' => $direccion, 
        'cedula' => $cedula, 'telefono' => $telefono, 'fecha_registro'=>$fecha_registro, 
        'estatus'=>$estatus, 'fecha_pago'=> $fecha_pago, 'logo'=>$nombre_logo]);

        $id_nuevo = intval($id_nuevo);
        $data = $this->traerEscuela($id_nuevo);
        $escuela_data = $data['data'];

        return [
            'estatus' => true,
            'mensaje' => 'Registro insertado correctamente',
            'nuevo'   => $id_nuevo,
            'escuela' => $escuela_data
        ];
    }

    public function actualizarEscuela($data, $id_escuela){
        return $this->db->update('escuelas', $data, ' id = ?', [$id_escuela]);
    }

    public function traerEscuela(int $id_escuela) : array{

       $escuela_data = $this->db->select('SELECT * FROM escuelas WHERE id = ? AND estatus = 1', [$id_escuela]);
       if(empty($escuela_data)){
           $estatus = false;
           $mensaje = 'No se encontró información de esa escuela, revisar';
           $data = [];
        }else{
            $estatus = true;
            $mensaje = 'Se encontró información de la escuela';
            $data = $escuela_data[0];
        };

        return array('estatus' => $estatus, 'mensaje' => $mensaje, 'data' => $data);

    }

}
