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
        $sql = "SELECT h.*, g.nombre as asignado FROM horarios h INNER JOIN grupos_horarios gh ON h.id  = gh.id_horario
        INNER JOIN grupos g ON gh.id_grupo = g.id  WHERE h.id_escuela = :id_escuela AND h.estatus= 1";
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
            'id_escuela' => $this->id_escuela,
            'tipo' =>1 //Por ahora para horarios escolarizados sera 1 
        ];
        $id_horario = $this->db->insert('horarios', $data);
        $select = $this->db->select("SELECT * FROM detalle_prehorario WHERE id_usuario =?", [$id_sesion]);

        foreach ($select as $row) {
            $id_profesor = $row['id_profesor'];
            $id_materia = $row['id_materia'];
            $dia = $row['dia'];
            $id_ciclo = $row['id_ciclo'];
            $hora = $row['hora'];
            $re = $this->insertarDetalle($id_materia, $dia, $hora, $id_horario, $id_profesor);
            if (!$re['estatus']) {
                return array('estatus' => false, 'mensaje' => 'Ocurrio un error al insertar el detalle', 'data' => $nombre);
            }
        }

        $campos = ['id_ciclo'=> $id_ciclo];
        $stmt_ = $this->db->update('horarios', $campos, 'id_horario = ?',[$id_horario]);
        

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

    public function obtenerHorario($id_horario, $tipo_horario){
        if($tipo_horario==1){
            $horario = $this->db->select('SELECT h.*, g.nombre as grupo, e.nombre as nombre_escuela, e.direccion as direccion_escuela, e.logo, 
            e.telefono as telefono_escuela, c.nombre as ciclo FROM horarios h 
            INNER JOIN escuelas e ON h.id_escuela = e.id
            INNER JOIN ciclos_escolares c ON h.id_ciclo = c.id
            INNER JOIN grupos_horarios gh ON gh.id_horario = h.id
            INNER JOIN grupos g ON gh.id_grupo = g.id
            WHERE h.id = ? AND h.id_escuela =?', [$id_horario, $this->id_escuela]);
            $mensaje = 'Datos encontrados';
            $estatus = true;
        }else{
            $horario =[]; //Este apartado de codigo será para los horarios flexibles
        }
        
        if(!empty($horario)){

            $detalle = $this->db->select('
            SELECT dh.*, 
                   concat(p.nombre, " ", p.apellido) as profesor, 
                   m.nombre as materia 
            FROM detalle_horario dh 
            LEFT JOIN materias m ON dh.id_materia = m.id 
            LEFT JOIN profesores p ON dh.id_profesor = p.id
            WHERE dh.id_horario = ?
        ', [$id_horario]);
            
        }else{
            return array('estatus'=> false, 'mensaje' => 'No se encontró un horario valido');
        }

        $data['horario'] = $horario;
        $data['detalle'] = $detalle;
       
        return array('estatus'=> true, 'mensaje' => 'Se encontró informacion', 'data' => $data);

    }

    public function obtenerHorarioProfesor($datos, $id_sesion){
        // Asumo que tienes el id del maestro en la sesión
        $id_profesor = $this->obtenerIDProfesor($id_sesion);

        // La Query que trae todo cruzado
        $query = "
            SELECT 
                dh.dia, 
                dh.hora, 
                dh.hora_fin, 
                m.nombre AS materia, 
                g.nombre AS grupo
            FROM detalle_horario dh
            INNER JOIN materias m ON dh.id_materia = m.id
            INNER JOIN grupos_horarios gh ON dh.id_horario = gh.id_horario
            INNER JOIN grupos g ON gh.id_grupo = g.id
            WHERE dh.id_profesor = ? 
            AND gh.estatus = 1
            ORDER BY 
                FIELD(dh.dia, 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'),
                dh.hora
        ";

        $resultados = $this->db->select($query, [$id_profesor]);

        // Agrupamos por día en PHP para que sea más fácil armar el HTML en JavaScript
        $horario_agrupado = [];
        foreach ($resultados as $row) {
            // Esto creará un arreglo como: ['Lunes' => [...clases], 'Martes' => [...clases]]
            $horario_agrupado[$row['dia']][] = [
                'hora'    => $row['hora'],
                'hora_fin'    => $row['hora_fin'],
                'materia' => $row['materia'],
                'grupo'   => $row['grupo']
            ];  
        }

        // Devuelves el JSON
        return (['estatus' => true, 'data' => $horario_agrupado]);
    }

    public function obtenerClases($datos, $id_sesion){
        $id_profesor = $this->obtenerIDProfesor($id_sesion);
        
        // 1. Configurar zona horaria para precisión
        date_default_timezone_set('America/Matamoros'); 
        
        // 2. Obtener el día de hoy en español
        $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        $dia_hoy = $dias[date('w')];
        $fecha_hoy =$this->fecha->fecha(); // Fecha actual YYYY-MM-DD
        $hora_actual = 1785352254;//time(); // Timestamp actual para comparar
        /* print_r($dia_hoy);
        print_r($hora_actual);
        die(); */
        // 3. Consultar las clases EXCLUSIVAMENTE del día de hoy
        $query = "
            SELECT 
                dh.id as id_dh,
                dh.hora, 
                dh.hora_fin, 
                m.id AS id_materia,
                m.nombre AS materia, 
                g.nombre AS grupo,
                g.id AS id_grupo,
                h.tipo AS tipo_asistencia
            FROM detalle_horario dh
            INNER JOIN materias m ON dh.id_materia = m.id
            INNER JOIN grupos_horarios gh ON dh.id_horario = gh.id_horario
            INNER JOIN grupos g ON gh.id_grupo = g.id
            INNER JOIN horarios h ON dh.id_horario = h.id
            WHERE dh.id_profesor = ? 
              AND gh.estatus = 1
              AND dh.dia = ?
            ORDER BY dh.hora ASC
        ";
        
        $clases = $this->db->select($query, [$id_profesor, $dia_hoy]);
    

        // 4. Procesar el estado de cada clase (Pasada, Actual, Próxima)
        $clases_procesadas = [];
        foreach ($clases as $clase) {
            // Separamos el rango "08:00 - 09:00"
            $hora_inicio = strtotime($clase['hora']);
            $hora_fin = strtotime($clase['hora_fin']);
            
            $estado = 'proxima';
          
            if ($hora_actual >= $hora_inicio && $hora_actual <= $hora_fin) {
                $estado = 'actual';
            } elseif ($hora_actual > $hora_fin) {
                $estado = 'pasada';
            }
            $clase['estado'] = $estado;

            $tipo_modalidad = (int)$clase['tipo_asistencia'];

            if ($tipo_modalidad === 1) {
                // MODALIDAD 1: POR DÍA (Kínder / Primaria)
                // Revisa si ya hay al menos un registro de asistencia para el grupo el día de hoy
                $date_asis = $this->db->select(
                    'SELECT id FROM asistencias WHERE id_grupo = ? AND fecha = ? LIMIT 1', 
                    [$clase['id_grupo'], $fecha_hoy]
                );
            } else {
                // MODALIDAD 2: POR MATERIA (Secundaria / Prepa / Uni)
                // Revisa si hay asistencia para esa materia específica + grupo + fecha
                $date_asis = $this->db->select(
                    'SELECT id FROM asistencias WHERE id_grupo = ? AND id_dh = ? AND fecha = ? LIMIT 1', 
                    [$clase['id_grupo'], $clase['id_dh'], $fecha_hoy]);
            }
            // Asignamos la bandera a la tarjeta
            $clase['asistencia_tomada'] = (count($date_asis) > 0) ? 1 : 0;
            $clases_procesadas[] = $clase;
        }
        return ['estatus' => true, 'dia' => $dia_hoy, 'data' => $clases_procesadas];
    }
    //Funcion auxiliar para obtener el ID del profesor
    public function obtenerIDProfesor($id_sesion){
       $data = $this->db->select('SELECT * FROM profesores WHERE id_usuario = ?', [$id_sesion]);
       return $data[0]['id'];
    }
}
