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
        $sql = "SELECT * FROM grupos WHERE estatus= 1 AND id_escuela = :id_escuela";
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
        $sql = "SELECT * FROM vista_detalle_grupo WHERE id_grupo= :id_grupo AND ciclo_escolar = :ciclo";
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


    public function combo(){
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM grupos WHERE estatus = 1 AND id_escuela = ?", [$this->id_escuela]);
        if($stmt->fetch(PDO::FETCH_ASSOC)['total'] ==0){
            return array('estatus'=> false, 'mensaje'=>'Sin grupos registrados');
        }else{
            $query = "SELECT * FROM grupos WHERE estatus=1 AND id_escuela = ?";
            $params = [$this->id_escuela];
            $stmt = $this->db->query($query, $params);
            return array('estatus'=>true, 'mensaje'=>'Se encontrarón grupos','data'=>$stmt->fetchAll(PDO::FETCH_ASSOC));
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
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM vista_detalle_grupo WHERE id_grupo = ? AND ciclo_escolar =?", [$id_grupo, $ciclo]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function contarGruposFiltrados($search)
    {
        $params = [];
        $sql = "SELECT COUNT(*) as total FROM grupos";
       
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
        $sql = "SELECT COUNT(*) as total FROM vista_detalle_grupo WHERE id_grupo = :id_grupo AND ciclo_escolar = :ciclo";
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
        $params = [$nivel, $grado, $this->id_escuela];
        $fecha_registro = $this->fecha->obtenerFechaRegistro();
        $total_grupos = $this->db->count('grupos', 'nivel = ? AND grado = ? AND id_escuela = ?', $params);

        if($total_grupos){
            return array('mensaje'=>'Error al registrar', 'subtitulo'=>'Ya esta registrado un grupo con ese grado o nivel', 'estatus'=>false);
        };

        $params = [$id_sesion];
        $total_detalle = $this->db->count('alumnos_pregrupo', 'id_usuario = ?', $params);
        
        if($total_detalle>0){
            $sql = 'SELECT * FROM alumnos_pregrupo WHERE id_usuario = ?';
            $data_alumnos = $this->db->select($sql, $params);
            foreach ($data_alumnos as $key => $value) {
                $ids_alumnos[] = $value['id_alumno'];
            }
            $ids_alumnos_str = implode(',', $ids_alumnos);
            $params = [$ids_alumnos_str, $ciclo];
            $total_alumnos_repetidos_ciclo = $this->db->count('alumnos_grupo', 'id_alumno IN (?) AND ciclo_escolar = ? AND estatus = 1', $params);
            
            if($total_alumnos_repetidos_ciclo>0){
                $data = $this->db->select("SELECT concat(a.nombre, ' ', a.apellido_paterno,' ',a.apellido_paterno) as alumno 
                FROM alumnos_grupo ag INNER JOIN alumnos a ON ag.id_alumno = a.id
                WHERE ag.id_alumno IN (?) AND ag.ciclo_escolar = ? AND ag.estatus = 1 LIMIT 1", $params);
                $alumno_repetido = ($data[0]['alumno']);
                return array('mensaje'=>'Error al registrar', 'subtitulo'=>"El alumno $alumno_repetido ya esta registrado para el ciclo escolar $ciclo", 'estatus'=>false);
            }else{
               
                $contador = 0;
                $id_grupo = $this->registrarGrupo($nombre, $nivel, $grado, $fecha_registro);
                foreach ($data_alumnos as $key => $value) {
                    $contador++;
                    $parametros= ['id_alumno' => $value['id_alumno'], 
                                  'id_grupo' => $id_grupo,
                                  'ciclo_escolar' => $ciclo,
                                  'fecha_registro' =>$fecha_registro];
                    $id_registro = $this->db->insert('alumnos_grupo', $parametros);
                }
                $this->resetearPreGrupo($id_sesion);

                return array('mensaje'=>"Grupo registrado correctamente", 'subtitulo'=>"Se registraron $contador alumno(s)", 'estatus'=>true);

            };
        }else{
            $id_grupo = $this->registrarGrupo($nombre, $nivel, $grado, $fecha_registro);
            return array('mensaje'=>"Grupo registrado correctamente", 'subtitulo'=>"no se registraron alumnos", 'estatus'=>true);
        }
    }

    public function registrarGrupo($nombre, $nivel, $grado, $fecha_reg){
        return $this->db->insert('grupos', ['nombre'=>$nombre, 'nivel'=>$nivel, 'grado'=>$grado,
        'fecha_registro'=>$fecha_reg, 'estatus'=>1]);
    }

    public function actualizarGrupo($nombre, $nivel, $grado, $ciclo, $id_grupo){
        $data = ['nombre'=>$nombre, 'nivel'=>$nivel, 'grado'=>$grado];
        $stmt = $this->db->update('grupos', $data, 'id = ?', [$id_grupo]);
        return array('estatus'=>true, 'mensaje'=>'Grupo actualizado', 'subtitulo' => 'Los datos generales del grupo se actualizaron');
    }

    public function resetearPreGrupo($id_sesion){
        $stmt = $this->db->delete('alumnos_pregrupo','id_usuario=?', [$id_sesion]);
    }

    public function insertarAlumnoGrupo($id_alumno, $id_grupo, $id_ciclo){
        $fecha_registro = $this->fecha->obtenerFechaRegistro();
        $params = [$id_alumno, $id_ciclo];
        $total_alumnos_repetidos_ciclo = $this->db->count('alumnos_grupo', 'id_alumno = ? AND id_ciclo = ? AND estatus = 1', $params);
        if($total_alumnos_repetidos_ciclo){
            return array('estatus'=>false, 'mensaje'=>'Este alumno ya esta registrado');
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

            if($nuevo_estatus == 0){
                $total_reg = $this->db->count('alumnos_grupo', 'id = ? AND estatus = 0', [$id_registro]);
                if($total_reg > 0){
                    return ['estatus'=>false, 'mensaje'=>'Ya esta cancelado el alumno'];
    
                }else{
                    $stmt = $this->db->update('alumnos_grupo', ['estatus'=>0], 'id = ?', [$id_registro]);
                    return ['estatus'=>true, 'mensaje'=>'Alumno dado de baja'];
    
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
            $data = $this->db->select('SELECT * FROM grupos WHERE id = ? AND estatus = 1', [$id_grupo]);
            return array('estatus'=>true, 'mensaje'=>'Se encontraron datos del grupo', 'data'=>$data);
        }else{
            return array('estatus'=>false, 'mensaje'=>'No hay grupos con ese ID', 'id_grupo'=>$id_grupo);
        }

    }


}
    ?>