<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/dates.php';
/* include "../helpers/response_helper.php"; */

class Horario
{
    private $db;
    private $fecha;
    private $id_escuela;

    public function __construct()
    {
        $this->db = new Database();
        $this->fecha = new Date();
        $this->id_escuela = $_SESSION['id_escuela'];
    }

    //-------INICIO FUNCIONES DATATABLES-----

    public function obtenerHorariosDataTable($start, $length, $search, $orderColumn, $orderDir)
    {

        $start = (int)$start;
        $length = (int)$length;
        $params = [];
        $sql = "SELECT * FROM horarios WHERE id_escuela = :id_escuela AND estatus= 1";
        $params[':id_escuela'] = $this->id_escuela;
        if (!empty($search)) {
            $sql .= " AND (nombre LIKE :search)";
            $params[':search'] = "%$search%";
        }

        // Seguridad para evitar SQL Injection en ORDER
        $allowedColumns = ['id', 'nombre', 'hora', 'dia']; // ajusta con tus columnas reales
        if (!in_array($orderColumn, $allowedColumns)) {
            $orderColumn = 'id';
        }

        $orderDir = strtolower($orderDir) === 'desc' ? 'DESC' : 'ASC';
        $sql .= " ORDER BY $orderColumn $orderDir LIMIT $start, $length";


        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerDetallePreHorarioDataTable($start, $length, $search, $id_sesion, $orderColumn, $orderDir)
    {

        $start = (int)$start;
        $length = (int)$length;
        $params = [];
        $sql = "SELECT * FROM vista_detalle_prehorario WHERE id_usuario =:id_sesion";

        $params['id_sesion'] = $id_sesion;
        if (!empty($search)) {
            $sql .= " AND (nombre LIKE :search)";
            $params[':search'] = "%$search%";
        }

        // Seguridad para evitar SQL Injection en ORDER
        $allowedColumns = ['id', 'materia', 'hora']; // ajusta con tus columnas reales
        if (!in_array($orderColumn, $allowedColumns)) {
            $orderColumn = 'id';
        }

        $orderDir = strtolower($orderDir) === 'desc' ? 'DESC' : 'ASC';
        $sql .= " ORDER BY $orderColumn $orderDir LIMIT $start, $length";


        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function obtenerGruposHorarioDataTable($start, $length, $search, $id_sesion, $orderColumn, $orderDir)
    {

        $start = (int)$start;
        $length = (int)$length;
        $params = [];
        $sql = "SELECT * FROM vista_grupos_horario WHERE estatus = 1";

        if (!empty($search)) {
            $sql .= " AND (nombre LIKE :search)";
            $params[':search'] = "%$search%";
        }

        // Seguridad para evitar SQL Injection en ORDER
        $allowedColumns = ['id', 'materia', 'hora']; // ajusta con tus columnas reales
        if (!in_array($orderColumn, $allowedColumns)) {
            $orderColumn = 'id';
        }

        $orderDir = strtolower($orderDir) === 'desc' ? 'DESC' : 'ASC';
        $sql .= " ORDER BY $orderColumn $orderDir LIMIT $start, $length";


        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarHorariosFiltrados($search)
    {
        $params = [];
        $sql = "SELECT COUNT(*) as total FROM horarios WHERE estatus = 1 AND id_escuela = :id_escuela";
        $params[':id_escuela'] = $this->id_escuela;
        if (!empty($search)) {
            $sql .= " AND (nombre LIKE :search)";
            $params[':search'] = "%$search%";
        }

        $stmt = $this->db->query($sql, $params);
        return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function contarPreHorariosFiltrados($search, $id_sesion)
    {
        $params = [$id_sesion];
        $sql = "SELECT COUNT(*) as total FROM detalle_prehorario WHERE id_usuario = ?";

        if (!empty($search)) {
            $sql .= " AND (nombre LIKE :search)";
            $params[':search'] = "%$search%";
        }

     
        $stmt = $this->db->query($sql, $params);
        return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function contarGruposHorarioFiltrados($search)
    {
        $params = [];
        $sql = "SELECT COUNT(*) as total FROM grupos_horarios WHERE estatus = 1";

        if (!empty($search)) {
            $sql .= " AND (nombre LIKE :search)";
            $params[':search'] = "%$search%";
        }

        $stmt = $this->db->query($sql, $params);
        return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function contarPreHorarios($id_sesion)
    {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM detalle_prehorario WHERE id_usuario = ?", [$id_sesion]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
    public function contarHorarios()
    {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM horarios WHERE estatus = 1 AND id_escuela = ?", [$this->id_escuela]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function contarGruposHorario()
    {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM grupos_horarios WHERE estatus = 1");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    //-------FIN FUNCIONES DATATABLES-----

    public function insertarPreHorario($id_profesor, $id_materia, $dia, $hora, $id_sesion)
    {

        $count = "SELECT count(*) FROM detalle_prehorario WHERE 
        (id_materia =? AND dia =? AND hora=? AND id_usuario=? AND id_profesor=?) OR
        (dia = ? AND hora = ?)";
        $res = $this->db->query($count, [$id_materia, $dia, $hora, $id_sesion, $id_profesor, $dia, $hora]);
        $total_ordenes = $res->fetchColumn();

        /* echo $total_ordenes;
        return false; */

        $nuevo_estatus = "Disponible";

        if ($total_ordenes == 0) {

            /* $insert = "INSERT INTO detalle_prehorario(id_materia, dia, hora, id_grupo, id_usuario, id_profesor)
            VALUES(?,?,?,?,?,?)"; */
            $id_detalle_prehorario = $this->db->insert(
                'detalle_prehorario',
                ['id_materia' => $id_materia, 'dia' => $dia, 'hora' => $hora, 'id_usuario' => $id_sesion,
            'id_profesor' => $id_profesor]
            );


            // Consultar el nuevo registro (puedes incluir joins para traer info completa)
            $nuevo = $this->db->query(
                "SELECT dp.*, concat(p.nombre, ' ', p.apellido) as profesor, m.nombre AS materia
                 FROM detalle_prehorario dp
                 INNER JOIN vista_profesores p ON p.id = dp.id_profesor
                 INNER JOIN materias m ON m.id = dp.id_materia
                 WHERE dp.id = ?",
                [$id_detalle_prehorario]
            )->fetch(PDO::FETCH_ASSOC);

            return [
                'estatus' => true,
                'mensaje' => 'Registro insertado correctamente',
                'nuevo'   => $nuevo
            ];
        } else {
            return array('estatus' => false,  'mensaje' => 'No se puede repetir el registro o el día igual a la hora');
        }

    }

    public function eliminarPrehorario($id_prehorario)
    {

        $stmt = $this->db->delete('detalle_prehorario', 'id = ?', [$id_prehorario]);
        return [
            'estatus' => true,
            'mensaje' => 'Registro eliminado correctamente',
            'data'   => $stmt
        ];
    }

    public function registrarHorario($nombre, $id_sesion)
    {
        $fecha_registro = $this->fecha->obtenerFechaRegistro();
        $data = [
            'nombre' => $nombre,
            'estatus' => 1,
            'fecha_registro' => $fecha_registro,
            'id_escuela' => $this->id_escuela
        ];
        $id_horario = $this->db->insert('horarios', $data);
        $select = $this->db->select("SELECT * FROM detalle_prehorario WHERE id_usuario =?", [$id_sesion]);

        foreach ($select as $row) {
            $id_profesor = $row['id_profesor'];
            $id_materia = $row['id_materia'];
            $dia = $row['dia'];
            $hora = $row['hora'];
            $re = $this->insertarDetalle($id_materia, $dia, $hora, $id_horario, $id_profesor);
            if (!$re['estatus']) {
                return array('estatus' => false, 'mensaje' => 'Ocurrio un error al insertar el detalle', 'data' => $nombre);
            }
        }

        return array('estatus' => true, 'mensaje' => 'El horario se registró con exito', 'data' => $nombre);
    }

    public function insertarDetalle($id_materia, $dia, $hora, $id_horario, $id_profesor)
    {
        $data = [
            'id_materia' => $id_materia,
            'dia' => $dia,
            'hora' => $hora,
            'id_horario' => $id_horario,
            'id_profesor' => $id_profesor
        ];
        $id_d_horario = $this->db->insert('detalle_horario', $data);

        if ($id_d_horario > 0) {
            return array('estatus' => true);
        } else {
            return array('estatus' => false);
        }

    }

    public function restearDetallePrehorario($id_sesion)
    {

        $stmt = $this->db->delete('detalle_prehorario', 'id_usuario = ?', [$id_sesion]);
        return [
            'estatus' => true,
            'mensaje' => 'Tabla depurada correctamente',
            'data'   => $stmt
        ];
    }

    public function obtenerListaHorarios(){
        $total_horarios = $this->contarHorarios();
        if($total_horarios > 0){
            $data = $this->db->select('SELECT * FROM horarios WHERE estatus = ? AND id_escuela =?', [1, $this->id_escuela]);
            $mensaje = 'Datos encontrados';
            $estatus = true;
        }else{
            $mensaje = 'Sin datos';
            $estatus = false;
            $data = [];
        }

        return array('estatus'=>$estatus, 'mensaje'=>$mensaje, 'data'=>$data);
    }

    public function insertarGruposHorario($id_grupo, $id_horario){
        $par = array(
            'id_grupo'=>$id_grupo,
            'id_horario'=>$id_horario,
            'estatus'=>1
        );
        $stmt = $this->db->insert('grupos_horarios', $par);
        return true;
    }

    public function cancelarGruposHorario($id_asignacion){
        $stmt = $this->db->delete('grupos_horarios', 'id = ?', [$id_asignacion]);
        return [
            'estatus' => true,
            'mensaje' => 'Registro eliminado correctamente',
            'data'   => $stmt
        ];
    }
}
