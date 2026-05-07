<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/dates.php';
require_once __DIR__ . '/../models/Profesor.php';
require_once __DIR__ . '/../models/Grupo.php';
require_once __DIR__ . '/../models/Materia.php';
require_once __DIR__ . '/../models/Alumno.php';

class Catalogo {
    private $db;
    private $fecha;
    private $id_escuela;
    private $id_sesion;

    public function __construct() {
        $this->db = new Database();
        $this->fecha = new Date();
        $this->id_escuela = $_SESSION['id_escuela'];
        $this->id_sesion = $_SESSION['id'];

    }

    public function iniciarFlujo($ciclo, $nivel) {
        $profesor = new Profesor;
        $grupo = new Grupo;
        $materia = new Materia;
        $alumno = new Alumno;
        $profesores = $profesor->obtenerListaProfesores();
        $grupos = $grupo->combo($ciclo, $nivel);
        $materias_no = $materia->contarMaterias();
        $alumnos_no = $alumno->contarAlumnos();
        
        /* if(count($profesores)>0){
            if($grupos>0){
                $response = array('estatus'=> true, 'mensaje'=> 'Se encontraron datos','docentes'=>$profesores, 'grupos' => $grupos);
            }else{
                $response = array('estatus'=> false, 'mensaje'=> 'No se encontrarón grupos ¿Quieres crear uno?','docentes'=>$profesores, 'grupos' => $grupos);
            }
        }else{
            $response = array('estatus'=> false, 'mensaje'=> 'No se encontrarón profesores ¿Quieres crear uno primero?','docentes'=>$profesores, 'grupos' => $grupos);

        } */
        $response = array('estatus'=> true, 'mensaje'=> 'Se terminó consulta', 'docentes'=>$profesores, 'grupos' => $grupos, 'alumnos_no'=>$alumnos_no, 'materias'=>$materias_no);
        return $response;
    }

    public function segundoPasoFlujo($nivel){
        $materia = new Materia;
        $materias = $materia->obtenerListaMaterias();
        $response = array('estatus'=> true, 'mensaje'=> 'Se encontraron datos','materias'=>$materias);
        return $response;
    }

    public function guardarBloques(){
        $b = json_decode(file_get_contents('php://input'), true);

        // Borrar bloques anteriores de esa materia (permite re-editar)
        $this->db->query("
            DELETE FROM detalle_prehorario
            WHERE id_usuario=? AND id_grupo=? AND id_ciclo=? AND id_materia=?
        ", [$this->id_sesion, $b['id_grupo'], $b['id_ciclo'], $b['id_materia']]);

       

        foreach ($b['bloques'] as $bloque) {
            $params = [
                'id_materia' => $b['id_materia'], 
                'dia' => $bloque['dia'], 
                'hora' => $bloque['hora'],
                'id_usuario' => $this->id_sesion, 
                'id_profesor' =>$b['id_profesor'], 
                'id_grupo' => $b['id_grupo'], 
                'id_ciclo' => $b['id_ciclo']
            ];
            $this->db->insert('detalle_prehorario', $params);
        }
        echo json_encode(['estatus' => true, 'mensaje'=>'Prehorario guardado']);
    }

    public function cargarConfigPrehorarioFlujo(){
        $post = json_decode(file_get_contents('php://input'), true);
        $params = [$this->id_sesion, $post['id_grupo'], $post['id_ciclo']];
        $stmt = $this->db->query("SELECT dp.id_materia, dp.dia, dp.hora, dp.id_profesor,
        m.nombre AS materia_nombre
        FROM detalle_prehorario dp
        JOIN materias m ON m.id = dp.id_materia
        WHERE dp.id_usuario=? AND dp.id_grupo=? AND dp.id_ciclo=?", $params);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Agrupar por materia
        $agrupado = [];
        foreach ($rows as $r) {
            $mid = $r['id_materia'];
            if (!isset($agrupado[$mid])) {
                $agrupado[$mid] = [
                    'materia_id'     => (int)$mid,
                    'materia_nombre' => $r['materia_nombre'],
                    'docente_id'     => (int)$r['id_profesor'],
                    'grupo_id'       => (int)$post['id_grupo'],
                    'bloques'        => []
                ];
            }
            $agrupado[$mid]['bloques'][] = [
                'dia'  => $r['dia'],
                'hora' => $r['hora']
            ];
        }

        return array('estatus'=>true, 'data' => array_values($agrupado), 'mensaje'=>'Consulta ejecutada con exito');

    }

    public function resetearPrehorario(){
        $body = json_decode(file_get_contents('php://input'), true);
        $stmt = $this->db->delete('detalle_prehorario', 'id_usuario=? AND id_ciclo=? AND id_grupo=?', [$this->id_sesion, $body['id_ciclo'], $body['id_grupo']]);
        
        return array('estatus'=>true, 'mensaje'=>'Configuración reseteada' , 'stmt' => $stmt);
    }

    public function guardarHorario() {
        $b           = json_decode(file_get_contents('php://input'), true);
        $id_usuario  = $this->id_sesion;
        $id_escuela  = $this->id_escuela;
    
        try {
            $this->db->beginTransaction();
    
            // 1. ¿Ya existe un horario activo para este grupo?
            $existing = $this->db->select("
                SELECT id_horario 
                FROM grupos_horarios
                WHERE id_grupo = ? AND estatus = 1
                LIMIT 1
            ", [$b['id_grupo']]);
    
            if (!empty($existing)) {
                // Reutilizar el horario y limpiar sus detalles anteriores
                $id_horario = $existing[0]['id_horario'];
                $this->db->delete('detalle_horario', 'id_horario = ?', [$id_horario]);
            } else {
                // Crear nuevo horario
                $nombre     = $b['nombre_horario'] ?? ('Horario ' . date('Y-m-d H:i'));
                $id_horario = $this->db->insert('horarios', [
                    'nombre'          => $nombre,
                    'estatus'         => 1,
                    'fecha_registro'  => date('Y-m-d H:i:s'),
                    'id_escuela'      => $id_escuela
                ]);
    
                // Vincular el grupo al nuevo horario
                $this->db->insert('grupos_horarios', [
                    'id_grupo'   => $b['id_grupo'],
                    'id_horario' => $id_horario,
                    'estatus'    => 1
                ]);
            }
    
            // 2. Copiar borrador → detalle_horario en una sola operación
            $this->db->query("
                INSERT INTO detalle_horario (id_materia, dia, hora, id_horario, id_profesor)
                SELECT id_materia, dia, hora, ?, id_profesor
                FROM detalle_prehorario
                WHERE id_usuario = ? AND id_grupo = ? AND id_ciclo = ?
            ", [$id_horario, $id_usuario, $b['id_grupo'], $b['id_ciclo']]);
    
            // 3. Limpiar el borrador
            $this->db->delete('detalle_prehorario',
                'id_usuario = ? AND id_grupo = ? AND id_ciclo = ?',
                [$id_usuario, $b['id_grupo'], $b['id_ciclo']]
            );
    
            $this->db->commit();
            return (['estatus' => true, 'id_horario' => $id_horario]);
    
        } catch (Exception $e) {
            $this->db->rollBack();
            return (['estatus' => false, 'error' => $e->getMessage()]);
        }
    }
}
