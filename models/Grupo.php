<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/dates.php';

class Grupo {
    private $db;
    private $fecha;
    private $id_escuela;
    
    public function __construct() {
        $this->db = new Database();
        $this->fecha = new Date();
        $this->id_escuela = $_SESSION['id_escuela'];
    }

    public function obtenerGruposDataTable($start, $length, $search, $orderColumn, $orderDir) {

        $start = (int)$start;
        $length = (int)$length;
        $params = [];
        $sql = "SELECT g.*, n.nombre as nivel FROM grupos g INNER JOIN niveles_educativos n ON g.id_nivel = n.id  WHERE g.estatus= 1 AND g.id_escuela = :id_escuela";
        $params[':id_escuela'] = $this->id_escuela;
        if (!empty($search)) {
            $sql .= " AND (nombre LIKE :search)";
            $params[':search'] = "%$search%";
        }

        // Seguridad para evitar SQL Injection en ORDER
        $allowedColumns = ['id', 'nombre']; // ajusta con tus columnas reales
        if (!in_array($orderColumn, $allowedColumns)) {
            $orderColumn = 'id';
        }

        $orderDir = strtolower($orderDir) === 'desc' ? 'DESC' : 'ASC';
        $sql .= " ORDER BY $orderColumn $orderDir LIMIT $start, $length";


        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPreGrupoDataTable($start, $length, $search, $orderColumn, $orderDir, $id_sesion) {   

        $start = (int)$start;
        $length = (int)$length;
        $params = [];
        $sql = "SELECT * FROM vista_detalle_pregrupo WHERE id_usuario= :id_usuario";
        $params['id_usuario'] = $id_sesion;

        if (!empty($search)) {
            $sql .= " AND (nombre LIKE :search)";
            $params[':search'] = "%$search%";
        }

        // Seguridad para evitar SQL Injection en ORDER
        $allowedColumns = ['id', 'nombre']; // ajusta con tus columnas reales
        if (!in_array($orderColumn, $allowedColumns)) {
            $orderColumn = 'id';
        }

        $orderDir = strtolower($orderDir) === 'desc' ? 'DESC' : 'ASC';
        $sql .= " ORDER BY $orderColumn $orderDir LIMIT $start, $length";


        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerDetalleGrupoDataTable($start, $length, $search, $orderColumn, $orderDir, $id_grupo, $ciclo) {   

        $start = (int)$start;
        $length = (int)$length;
        $params = [];
        $sql = "SELECT * FROM vista_detalle_grupo WHERE id_grupo= :id_grupo AND ciclo_escolar = :ciclo AND estatus != 0";
        $params['id_grupo'] = $id_grupo;
        $params['ciclo'] = $ciclo;

        if (!empty($search)) {
            $sql .= " AND (nombre LIKE :search)";
            $params[':search'] = "%$search%";
        }

        // Seguridad para evitar SQL Injection en ORDER
        $allowedColumns = ['id', 'nombre']; // ajusta con tus columnas reales
        if (!in_array($orderColumn, $allowedColumns)) {
            $orderColumn = 'id';
        }

        $orderDir = strtolower($orderDir) === 'desc' ? 'DESC' : 'ASC';
        $sql .= " ORDER BY $orderColumn $orderDir LIMIT $start, $length";


        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function combo($ciclo=null, $nivel = null){

        if($ciclo && $nivel){
            $sql_ext="AND id_ciclo = ? AND id_nivel = ?";
            $params = [$this->id_escuela, $ciclo, $nivel];
        }else{
            $sql_ext ='';
            $params = [$this->id_escuela];
        }

        $stmt = $this->db->query("SELECT COUNT(*) as total FROM grupos WHERE estatus = 1 AND id_escuela = ? $sql_ext", $params);
        if($stmt->fetch(PDO::FETCH_ASSOC)['total'] ==0){
            return array('estatus'=> false, 'mensaje'=>'Sin grupos registrados');
        }else{
            $query = "SELECT * FROM grupos WHERE estatus=1 AND id_escuela = ?";
            $params = [$this->id_escuela];
            $stmt = $this->db->query($query, $params);
            return array('estatus'=>true, 'mensaje'=>'Se encontrarón grupos','data'=>$stmt->fetchAll(PDO::FETCH_ASSOC));
        };
    }

    public function comboNiveles(){
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM niveles_educativos WHERE estatus = 1", []);
        if($stmt->fetch(PDO::FETCH_ASSOC)['total'] ==0){
            return array('estatus'=> false, 'mensaje'=>'Sin niveles educativos registrados', 'data'=>[]);
        }else{
            $query = "SELECT * FROM niveles_educativos WHERE estatus=1";
            $params = [];
            $stmt = $this->db->query($query, $params);
            return array('estatus'=>true, 'mensaje'=>'Se encontrarón niveles educativos','data'=>$stmt->fetchAll(PDO::FETCH_ASSOC));
        };
    }

    public function comboCiclos(){
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM ciclos_escolares WHERE estatus = 1", []);
        if($stmt->fetch(PDO::FETCH_ASSOC)['total'] ==0){
            return array('estatus'=> false, 'mensaje'=>'Sin ciclos escolares registrados', 'data'=>[]);
        }else{
            $query = "SELECT * FROM ciclos_escolares WHERE estatus=1";
            $params = [];
            $stmt = $this->db->query($query, $params);
            return array('estatus'=>true, 'mensaje'=>'Se encontrarón ciclos escolares','data'=>$stmt->fetchAll(PDO::FETCH_ASSOC));
        };
    }

    public function contarGrupos()
    {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM grupos WHERE estatus = 1 AND id_escuela =?", [$this->id_escuela]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function contarPreGrupo($id_sesion)
    {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM vista_detalle_pregrupo WHERE id_usuario = ?", [$id_sesion]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function contarDetalleGrupo($id_grupo, $ciclo)
    {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM vista_detalle_grupo WHERE id_grupo = ? AND ciclo_escolar =? AND estatus != 0", [$id_grupo, $ciclo]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function contarGruposFiltrados($search)
    {
        $params = [];
        $sql = "SELECT COUNT(*) as total FROM grupos WHERE estatus = 1 AND id_escuela = :id_escuela";
        $params[':id_escuela'] = $this->id_escuela;
       
        if (!empty($search)) {
            $sql .= " AND (nombre LIKE :search)";
            $params[':search'] = "%$search%";
        }

        $stmt = $this->db->query($sql, $params);
        return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function contarPreGrupoFiltrados($search, $id_sesion)
    {
        $params=[];
        $params['id_sesion']=$id_sesion;
        $sql = "SELECT COUNT(*) as total FROM vista_detalle_pregrupo WHERE id_usuario = :id_sesion";
        $params[':id_sesion']= $id_sesion;

        if (!empty($search)) {
            $sql .= " AND (nombre LIKE :search)";
            $params[':search'] = "%$search%";
        }

        $stmt = $this->db->query($sql, $params);
        return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function contarDetalleGrupoFiltrados($search, $id_grupo, $ciclo)
    {
        $params=[];
        $params['id_grupo']=$id_grupo;
        $params['ciclo']=$ciclo;
        $sql = "SELECT COUNT(*) as total FROM vista_detalle_grupo WHERE id_grupo = :id_grupo AND ciclo_escolar = :ciclo AND estatus != 0";
        if (!empty($search)) {
            $sql .= " AND (nombre LIKE :search)";
            $params[':search'] = "%$search%";
        }

        $stmt = $this->db->query($sql, $params);
        return (int)$stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function registrarAlumnoPreGrupo($alumno, $id_sesion){
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM alumnos_pregrupo WHERE id_alumno = ?", [$alumno]);
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        if($total>0){
            $estatus = false;
            $mensaje = 'Este alumno ya fue registrado para este grupo.';
        }else{
            $stmt = $this->db->insert('alumnos_pregrupo', ['id_alumno'=>$alumno, 'id_usuario'=>$id_sesion]);
            $estatus = true;
            $mensaje = 'Se insertó alumno correctamente a la tabla';
        }

        return array('mensaje'=>$mensaje, 'estatus'=>$estatus);
    }

    public function eliminarAlumnoPreGrupo($id_detalle){
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM alumnos_pregrupo WHERE id = ?", [$id_detalle]);
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        if($total==0){
            $estatus = false;
            $mensaje = 'Este alumno ya fue eliminado para este grupo.';
        }else{
            $stmt = $this->db->delete('alumnos_pregrupo','id=?', [$id_detalle]);
            $estatus = true;
            $mensaje = 'Se eliminó alumno correctamente de la tabla';
        }

        return array('mensaje'=>$mensaje, 'estatus'=>$estatus);
    }

    public function procesarGrupo($nombre, $nivel, $grado, $ciclo, $id_sesion){
        $params = [$nivel, $grado, $this->id_escuela, $ciclo, $nombre];
        $fecha_registro = $this->fecha->obtenerFechaRegistro();
        $total_grupos = $this->db->count('grupos', 'id_nivel = ? AND grado = ? AND id_escuela = ? AND id_ciclo=? AND nombre =?', $params);

        if($total_grupos){
            return array('mensaje'=>'Error al registrar', 'subtitulo'=>'Ya esta registrado un grupo con ese nombre', 'estatus'=>false);
        };

        $params = [$id_sesion];
        $total_detalle = $this->db->count('alumnos_pregrupo', 'id_usuario = ?', $params);
        
        if($total_detalle>0){
            $sql = 'SELECT * FROM alumnos_pregrupo WHERE id_usuario = ?';
            $data_alumnos = $this->db->select($sql, $params);
            foreach ($data_alumnos as $key => $value) {
                $ids_alumnos[] = $value['id_alumno'];
            }
            $placeholders = implode(',', array_fill(0, count($ids_alumnos), '?'));
            $params_alumnos = array_merge($ids_alumnos, [$ciclo]);

           
            $total_alumnos_repetidos_ciclo = $this->db->count(
                'alumnos_grupo',
                "id_alumno IN ($placeholders) AND id_ciclo = ? AND estatus = 1",
                $params_alumnos
            );            
            if($total_alumnos_repetidos_ciclo>0){
                $data = $this->db->select(
                    "SELECT concat(a.nombre, ' ', a.apellido_materno,' ',a.apellido_paterno) as alumno, 
                            c.nombre as ciclo 
                     FROM alumnos_grupo ag 
                     INNER JOIN alumnos a ON ag.id_alumno = a.id 
                     INNER JOIN ciclos_escolares c ON ag.id_ciclo = c.id
                     WHERE ag.id_alumno IN ($placeholders) AND ag.id_ciclo = ? AND ag.estatus = 1",
                    $params_alumnos
                );
        
                $alumnos_repetidos = [];
                foreach ($data as $key => $value) {
                    $ciclo = $value['ciclo'];
                    $alumnos_repetidos[] = $value['alumno'];
                }
               
                $lista_alumnos = '<ul style="text-align:left; margin-top:8px;">';
                foreach ($alumnos_repetidos as $alumno) {
                    $lista_alumnos .= "<li>$alumno</li>";
                }
                $lista_alumnos .= '</ul>';
                    return [
                        'mensaje'   => 'Error al registrar',
                        'subtitulo' => "Los siguientes alumnos ya están registrados en el ciclo escolar $ciclo: $lista_alumnos",
                        'estatus'   => false
                    ];
                }else{
               
                $contador = 0;
                $id_grupo = $this->registrarGrupo($nombre, $nivel, $grado, $ciclo, $fecha_registro);
                foreach ($data_alumnos as $key => $value) {
                    $contador++;
                    $parametros= ['id_alumno' => $value['id_alumno'], 
                                  'id_grupo' => $id_grupo,
                                  'id_ciclo' => $ciclo,
                                  'fecha_registro' =>$fecha_registro,
                                   'estatus' => 1];
                    $id_registro = $this->db->insert('alumnos_grupo', $parametros);
                }
                $this->resetearPreGrupo($id_sesion);

                return array('mensaje'=>"Grupo registrado correctamente", 'subtitulo'=>"Se registraron $contador alumno(s)", 'estatus'=>true);

            };
        }else{
            $id_grupo = $this->registrarGrupo($nombre, $nivel, $grado, $ciclo, $fecha_registro);
            return array('mensaje'=>"Grupo registrado correctamente", 'subtitulo'=>"no se registraron alumnos", 'estatus'=>true);
        }
    }

    public function registrarGrupo($nombre, $nivel, $grado, $id_ciclo, $fecha_reg){
        return $this->db->insert('grupos', ['nombre'=>$nombre, 'grado'=>$grado, 'id_ciclo' => $id_ciclo, 
        'fecha_registro'=>$fecha_reg, 'estatus'=>1, 'id_escuela' => $this->id_escuela, 'id_nivel'=>$nivel]);
    }

    public function actualizarGrupo($nombre, $nivel, $grado, $ciclo, $id_grupo){
        $data = ['nombre'=>$nombre, 'id_nivel'=>$nivel, 'grado'=>$grado];
        $stmt = $this->db->update('grupos', $data, 'id = ?', [$id_grupo]);
        return array('estatus'=>true, 'mensaje'=>'Grupo actualizado', 'subtitulo' => 'Los datos generales del grupo se actualizaron');
    }

    function cancelarGrupo($id_grupo){
        $data_g = ['estatus'=>0];
        $stmt = $this->db->update('grupos', $data_g, 'id = ?', [$id_grupo]);
        $stmt_ag = $this->db->update('alumnos_grupo', $data_g, 'id_grupo = ?', [$id_grupo]);
        $stmt_gh = $this->db->update('grupos_horarios', $data_g, 'id_grupo = ?', [$id_grupo]);
        return array('estatus'=>true, 'mensaje'=>'Grupo actualizado', 'subtitulo' => 'Los datos generales del grupo se actualizaron',  'mensaje'=>'Grupo cancelado', 'subtitulo' => 'Los alumnos ligados y horarios quedaron liberados.',
        'stmt' => $stmt, 'stmt_ag' => $stmt_ag, 'stmt_gh' => $stmt_gh);
    }

    public function resetearPreGrupo($id_sesion){
        $stmt = $this->db->delete('alumnos_pregrupo','id_usuario=?', [$id_sesion]);
    }

    public function insertarAlumnoGrupo($id_alumno, $id_grupo, $id_ciclo){ 
        $fecha_registro = $this->fecha->obtenerFechaRegistro();
        $params = [$id_alumno, $id_ciclo];
        $total_alumnos_repetidos_ciclo = $this->db->count('alumnos_grupo', 'id_alumno = ? AND id_ciclo = ? AND estatus = 1', $params);
        if($total_alumnos_repetidos_ciclo){
            return array('estatus'=>false, 'mensaje'=>'Este alumno ya esta registrado en el ciclo escolar');
        }else{
            $parametros= ['id_alumno' => $id_alumno, 
                          'id_grupo' => $id_grupo,
                          'id_ciclo' => $id_ciclo,
                          'fecha_registro' =>$fecha_registro,
                          'estatus'=>1 ];

            $id_registro = $this->db->insert('alumnos_grupo', $parametros);
            return array('estatus'=>true, 'mensaje'=>'Alumno registrado correctamente', 'id_registro'=>$id_registro);

        }

    }

    public function cancelarAlumnoGrupo($id_registro, $nuevo_estatus){
        $total_reg = $this->db->count('alumnos_grupo', 'id = ?', [$id_registro]);
        if($total_reg > 0){

            if($nuevo_estatus == 0 || $nuevo_estatus == 2){
                $total_reg = $this->db->count('alumnos_grupo', 'id = ? AND estatus = 0', [$id_registro]);
                if($total_reg > 0){
                    return ['estatus'=>false, 'mensaje'=>'Ya esta cancelado el alumno'];
    
                }else{
                    $mensaje = $nuevo_estatus == 0 ? 'eliminado con exito' : 'desactivado con exito';
                    $stmt = $this->db->update('alumnos_grupo', ['estatus'=> $nuevo_estatus], 'id = ?', [$id_registro]);
                    return ['estatus'=>true, 'mensaje'=>'Alumno '. $mensaje];
    
                }
            }else{
                $total_reg = $this->db->count('alumnos_grupo', 'id = ? AND estatus = 1', [$id_registro]);
                if($total_reg > 0){
                    return ['estatus'=>false, 'mensaje'=>'Ya esta activado el alumno'];
    
                }else{
                    $stmt = $this->db->update('alumnos_grupo', ['estatus'=>1], 'id = ?', [$id_registro]);
                    return ['estatus'=>true, 'mensaje'=>'Alumno reactivado con exito'];
    
                }
            }
           
        }else{
            return ['estatus'=>false, 'mensaje'=>'No existe un registro con el id proporcionado'];
        }
    }

    public function obtenerGrupoCalificaciones($id_grupo, $id_ciclo){
        
        $total = $this->contarDetalleGrupo($id_grupo, $id_ciclo);
        if($total >0){
          $data = $this->db->select('SELECT * FROM vista_detalle_grupo WHERE id_grupo = ? AND ciclo_escolar =?', [$id_grupo, $id_ciclo]);
           return array('estatus'=>true, 'mensaje'=>'Se encontraron datos', 'data'=>$data);
        }else{
           return array('estatus'=>false, 'mensaje'=>'No se encontraron datos', 'data'=>[]);

        }
    }

    public function obtenerGrupo($id_grupo){
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM grupos WHERE id = ? AND estatus = 1", [$id_grupo]);
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        if($total>0){
            $data = $this->db->select('SELECT g.*, c.nombre as ciclo FROM grupos g inner join ciclos_escolares c ON g.id_ciclo = c.id 
            WHERE g.id = ? AND g.estatus = 1', [$id_grupo]);
            return array('estatus'=>true, 'mensaje'=>'Se encontraron datos del grupo', 'data'=>$data);
        }else{
            return array('estatus'=>false, 'mensaje'=>'No hay grupos con ese ID', 'id_grupo'=>$id_grupo);
        }

    }


}
    ?>