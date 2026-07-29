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

        // 1. VALIDACIÓN: Comprobar si la escuela tiene una plantilla de turnos configurada
            $plantilla = $this->db->select("
            SELECT id 
            FROM plantilla_turnos 
            WHERE id_escuela = ? 
            LIMIT 1
        ", [$this->id_escuela]);

        // Si el arreglo viene vacío, significa que no hay registros para esta escuela
        if (empty($plantilla)) {
            return [
                'estatus' => false, 
                'error'   => 'Aún no se ha configurado la plantilla de turnos de la escuela (horas de clase y descansos). Por favor, configúrala antes de armar el horario.'
            ];
        }

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
       return (['estatus' => true, 'mensaje'=>'Prehorario guardado']);
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

        // A) Validar que el profesor no esté asignado en otro grupo el mismo día y a la misma hora
        $choqueProfesor = $this->db->select("
                SELECT dp.dia, dp.hora, dp.id_profesor, p.nombre, p.apellido
                FROM detalle_horario dh  
                INNER JOIN detalle_prehorario dp ON dp.id_profesor = dh.id_profesor AND dp.dia = dh.dia AND dp.hora = dh.hora
                INNER JOIN grupos_horarios gh ON dh.id_horario = gh.id_horario
                INNER JOIN horarios h ON dh.id_horario = h.id
                LEFT JOIN profesores p ON dp.id_profesor = p.id 
                WHERE gh.estatus= 1 AND dp.id_grupo = ? AND dp.id_ciclo = ?
                AND dp.id_profesor IS NOT NULL
                LIMIT 1;
            ", [$b['id_grupo'], $b['id_ciclo']]);


            if (!empty($choqueProfesor)) {
                $nombreProfe = trim($choqueProfesor[0]['nombre'] . ' ' . $choqueProfesor[0]['apellido']);
                // Si por alguna razón no trae nombre, le ponemos un texto genérico
                if (empty($nombreProfe)) $nombreProfe = "Este docente";
                return ['estatus' => false, 'error' => 'Empalme de Profesor: ' . $nombreProfe . ' ya tiene clase asignada el día ' . $choqueProfesor[0]['dia'] . ' a la hora ' . $choqueProfesor[0]['hora'] . ' en otro grupo.'];
            }


            $choqueHoraGrupo = $this->db->select("
            SELECT dia, hora, COUNT(*) as repeticiones
            FROM detalle_prehorario
            WHERE id_usuario = ? AND id_grupo = ? AND id_ciclo = ?
            GROUP BY dia, hora
            HAVING repeticiones > 1
            LIMIT 1
                ", [$id_usuario, $b['id_grupo'], $b['id_ciclo']]);

                if (!empty($choqueHoraGrupo)) {
                    return [
                        'estatus' => false, 
                        'error'   => 'Empalme de Grupo: Has asignado más de una clase el día ' . $choqueHoraGrupo[0]['dia'] . ' a la hora ' . $choqueHoraGrupo[0]['hora'] . '. Corrige el horario antes de guardar.'
                    ];
                }

    
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
                    'id_escuela'      => $id_escuela,
                    'id_ciclo'        => $b['id_ciclo'],
                    'tipo'            => 1 //Por ahora para horarios escolarizados sera 1 
                ]);
    
                // Vincular el grupo al nuevo horario
                $this->db->insert('grupos_horarios', [
                    'id_grupo'   => $b['id_grupo'],
                    'id_horario' => $id_horario,
                    'estatus'    => 1
                ]);
            }
          
            // 2. Copiar borrador → detalle_horario en una sola operación
            $rre= $this->db->query("
            INSERT INTO detalle_horario (id_materia, dia, hora, hora_fin, id_horario, id_profesor, tipo)
            SELECT dp.id_materia, dp.dia, dp.hora, pt.hora_fin, ?, dp.id_profesor, 1
            FROM detalle_prehorario dp
            INNER JOIN plantilla_turnos pt ON pt.hora_inicio = dp.hora AND pt.id_escuela = ?
            WHERE dp.id_usuario = ? AND dp.id_grupo = ? AND dp.id_ciclo = ?
        ", [$id_horario, $this->id_escuela, $id_usuario, $b['id_grupo'], $b['id_ciclo']]);
    

        // 2.5 Insertar descansos (INCLUYENDO HORA_FIN)
        $this->db->query("
            INSERT INTO detalle_horario (id_materia, dia, hora, hora_fin, id_horario, id_profesor, tipo)
            SELECT NULL, semanas.dia, p.hora_inicio, p.hora_fin, ?, 0, 2
            FROM plantilla_turnos p
            CROSS JOIN (
                SELECT 'Lunes' AS dia UNION ALL
                SELECT 'Martes' UNION ALL
                SELECT 'Miércoles' UNION ALL
                SELECT 'Jueves' UNION ALL
                SELECT 'Viernes'
            ) semanas
            WHERE p.id_escuela = ? AND p.tipo = 'descanso'
        ", [$id_horario, $this->id_escuela]);

            // 2.6. Rellenar los huecos vacíos como "Sin asignar" (TIPO = 3)
            // Cruzamos la plantilla con los 5 días y buscamos los que NO existen en el borrador
            $this->db->query("
            INSERT INTO detalle_horario (id_materia, dia, hora, hora_fin, id_horario, id_profesor, tipo)
            SELECT NULL, semanas.dia, p.hora_inicio, p.hora_fin, ?, NULL, 3
            FROM plantilla_turnos p
            CROSS JOIN (
                SELECT 'Lunes' AS dia UNION ALL
                SELECT 'Martes' UNION ALL
                SELECT 'Miércoles' UNION ALL
                SELECT 'Jueves' UNION ALL
                SELECT 'Viernes'
            ) semanas
            LEFT JOIN detalle_prehorario dp 
                ON dp.dia = semanas.dia 
                AND dp.hora = p.hora_inicio 
                AND dp.id_usuario = ? AND dp.id_grupo = ? AND dp.id_ciclo = ?
            WHERE p.id_escuela = ? 
            AND p.tipo = 'clase' 
            AND dp.id_materia IS NULL
        ", [$id_horario, $id_usuario, $b['id_grupo'], $b['id_ciclo'], $this->id_escuela]);
            
            // 3. Limpiar el borrador
            $this->db->delete('detalle_prehorario',
                'id_usuario = ? AND id_grupo = ? AND id_ciclo = ?',
                [$id_usuario, $b['id_grupo'], $b['id_ciclo']]
            );
    
            $this->db->commit();
            return (['estatus' => true, 'id_horario' => $id_horario]);
    
        } catch (Exception $e) {
            $this->db->rollBack();
            print_r($e->getMessage());
            return (['estatus' => false, 'error' => $e->getMessage()]);
        }
    }

    public function guardarPlantilla($datos) {
        $datos= json_decode(file_get_contents('php://input'), true);
        $bloques = $datos['bloques'];
        try {
            // Iniciamos la transacción
            $this->db->beginTransaction();
    
            // 1. Limpiamos la plantilla actual de esta escuela
            $del = $this->db->delete('plantilla_turnos', 'id_escuela = ?', [$this->id_escuela]); 
    
            // 2. Insertamos bloque por bloque respetando el orden del usuario
            foreach ($bloques as $bloque) {
                $data=[  
                    'id_escuela'    => $this->id_escuela,
                    'numero_bloque' => $bloque['numero_bloque'],
                    'hora_inicio'   => $bloque['hora_inicio'],
                    'hora_fin'      => $bloque['hora_fin'],
                    'tipo'          => $bloque['tipo']];
                $insert =  $this->db->insert('plantilla_turnos', $data);

            }
    
            // Si todo salió bien, confirmamos los cambios en la BD
            $this->db->commit();
    
            return ['estatus' => true, 'mensaje' => 'Plantilla actualizada correctamente'];
    
        } catch (Exception $e) {
            // Si hay error, deshacemos todo para no dejar la tabla a medias
            $this->db->rollBack();
            return ['estatus' => false, 'error' => 'Error de BD: ' . $e->getMessage()];
        }
    }

    public function obtenerPlantilla(){
            try {
                // Consultamos los bloques de la escuela ordenados secuencialmente
                $sql = "SELECT numero_bloque, hora_inicio, hora_fin, tipo 
                        FROM plantilla_turnos 
                        WHERE id_escuela = ? 
                        ORDER BY numero_bloque ASC";
                        
                $stmt = $this->db->query($sql, [$this->id_escuela]);
                
                // Obtenemos todos los registros como un arreglo asociativo
                $plantilla = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
                return [
                    'estatus' => true, 
                    'plantilla' => $plantilla
                ];
        
            } catch (Exception $e) {
                return [
                    'estatus' => false, 
                    'error' => 'Error al obtener la plantilla: ' . $e->getMessage(),
                    'plantilla' => []
                ];
            }
        
    }
}
