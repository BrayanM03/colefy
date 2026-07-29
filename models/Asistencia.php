<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/dates.php';

class Asistencia{  

private $db;
private $fecha;

public function __construct() {
    $this->db = new Database();
    $this->fecha = new Date();
}

public function guardarAsistencia($data, $id_escuela, $id_sesion){
    $id_grupo   = $data['id_grupo'];
    $id_materia = $data['id_materia']; 
    $id_dh      = isset($data['id_dh']) ? $data['id_dh'] : null;
    //$tipo       = $data['tipo'];
    $fecha_hoy  = date('Y-m-d'); // Siempre la fecha del día en que se pasa lista
    
    try {
        $this->db->beginTransaction();

        $querySelHor = 'SELECT h.tipo 
        FROM detalle_horario dh 
        INNER JOIN horarios h ON h.id = dh.id_horario 
        WHERE dh.id = ?';
        
        $sel_hor = $this->db->select($querySelHor, [$id_dh]);
        $tipo = $sel_hor[0]['tipo'];

        // TRUCO: Si el maestro se equivocó y volvió a pasar lista hoy mismo, 
        // borramos los registros de HOY para esta clase y los volvemos a insertar frescos.
        // Si es tipo 1 (Grupo), borramos solo por grupo. Si es tipo 2, por grupo y materia.
        if ($tipo == 1) {
            $this->db->delete('asistencias', 'id_grupo = ? AND fecha = ?', [$id_grupo, $fecha_hoy]);
        } else {
            $this->db->delete('asistencias', 'id_grupo = ? AND id_dh = ? AND fecha = ? AND tipo = 2', [$id_grupo, $id_dh, $fecha_hoy]);
        }

        // Insertamos alumno por alumno
        foreach ($data['asistencias'] as $asistencia) {
            $this->db->insert('asistencias', [
                'id_escuela'          => $id_escuela,
                'id_alumno'           => $asistencia['id_alumno'],
                'id_grupo'            => $id_grupo,
                'id_materia'          => ($tipo == 1) ? null : $id_materia,
                'id_dh'               => ($tipo == 1) ? null : $id_dh,
                'fecha'               => $fecha_hoy,
                'estatus'             => $asistencia['estatus'],
                'tipo'                => $tipo,
                'id_usuario_registra' => $id_sesion
            ]);
        }

        $this->db->commit();
        return ['estatus' => true, 'mensaje' => 'Guardado exitoso'];

    } catch (Exception $e) {
        $this->db->rollBack();
        return ['estatus' => false, 'mensaje' => 'Error al guardar: ' . $e->getMessage()];
    }
}

public function obtenerSemanal($id_grupo, $fecha_inicio, $fecha_fin) {
    // Recibir parámetros por GET

    if (!$id_grupo || !$fecha_inicio || !$fecha_fin) {
        echo json_encode(['estatus' => false, 'mensaje' => 'Faltan parámetros']);
        return;
    }

    // 1. Obtener los alumnos del grupo (Ordenados alfabéticamente)
    $queryAlumnos = "
        SELECT a.id, a.nombre, a.apellido_paterno, a.apellido_materno, a.id_interno AS matricula 
        FROM alumnos a INNER JOIN alumnos_grupo ag ON a.id = ag.id_alumno
        WHERE ag.id_grupo = ? AND ag.estatus = 1 AND a.estatus = 1
        ORDER BY a.apellido_paterno ASC, a.apellido_materno ASC, a.nombre ASC
    ";
    $alumnos = $this->db->select($queryAlumnos, [$id_grupo]);

    // 2. Obtener todas las asistencias de ese grupo en esa semana
    // Solo traemos el estatus y la fecha.
    $queryAsistencias = "
        SELECT id_alumno, fecha, estatus 
        FROM asistencias 
        WHERE id_grupo = ? 
          AND fecha BETWEEN ? AND ?
    ";
    $asistenciasRaw = $this->db->select($queryAsistencias, [$id_grupo, $fecha_inicio, $fecha_fin]);

    // 3. ¡EL TRUCO DE ORO! Formatear las asistencias para que JavaScript las lea fácil.
    // Crearemos un arreglo donde la llave sea el ID del alumno y adentro tenga las fechas.
    $asistenciasProcesadas = [];
    foreach ($asistenciasRaw as $asis) {
        $id_alum = $asis['id_alumno'];
        $fecha = $asis['fecha'];
        $estatus = $asis['estatus'];
        
        // Estructura: [ "19" => [ "2026-07-13" => 1, "2026-07-14" => 0 ] ]
        $asistenciasProcesadas[$id_alum][$fecha] = $estatus;
    }

    return ([
        'estatus' => true, 
        'data_alumnos' => $alumnos,
        'data_asistencias' => $asistenciasProcesadas
    ]);
}

public function registrarPorQR($data, $id_escuela, $id_sesion){
    // Asumimos que el QR contiene la matrícula del alumno
    $matricula_qr = $data['qr_data']; 
    $id_grupo     = $data['id_grupo'];
    $id_dh        = isset($data['id_dh']) ? $data['id_dh'] : null;
    $id_materia   = isset($data['id_materia']) ? $data['id_materia'] : null;
    $tipo         = $data['tipo']; 
    
    date_default_timezone_set('America/Matamoros'); 
    $fecha_hoy = date('Y-m-d');
    $hora_actual = date('H:i:s');

    try {
        // 1. Buscar al alumno por la matrícula leída en el QR
        $alumno = $this->db->select("
            SELECT id, nombre, apellido, foto 
            FROM alumnos 
            WHERE matricula = ? AND id_escuela = ? 
            LIMIT 1
        ", [$matricula_qr, $id_escuela]);

        if (empty($alumno)) {
            return ['estatus' => false, 'mensaje' => 'Código QR no reconocido o alumno no pertenece a esta escuela.'];
        }

        $id_alumno = $alumno[0]['id'];
        $nombre_completo = $alumno[0]['nombre'] . ' ' . $alumno[0]['apellido'];
        // Si no tiene foto, mandamos una por defecto para la UI
        $foto = !empty($alumno[0]['foto']) ? $alumno[0]['foto'] : 'default_avatar.png';

        // 2. Verificar si ya le pasaron lista hoy en esta misma clase para evitar duplicados
        // Si el alumno por jugar vuelve a pasar su gafete, el sistema no truena, solo avisa.
        $condicion = ($tipo == 1) ? 'id_grupo = ? AND fecha = ? AND id_alumno = ? AND tipo = 1' 
                                  : 'id_grupo = ? AND id_dh = ? AND fecha = ? AND id_alumno = ? AND tipo = 2';
        
        $params_check = ($tipo == 1) ? [$id_grupo, $fecha_hoy, $id_alumno] 
                                     : [$id_grupo, $id_dh, $fecha_hoy, $id_alumno];

        $ya_registrado = $this->db->select("SELECT id FROM asistencias WHERE $condicion LIMIT 1", $params_check);

        if (!empty($ya_registrado)) {
            // Ya estaba registrado, devolvemos success de todos modos para que la UI muestre la foto
            // pero le agregamos un mensaje para que el maestro sepa que ya había pasado
            return [
                'estatus' => true, 
                'mensaje' => 'Asistencia ya estaba registrada.',
                'alumno'  => ['nombre' => $nombre_completo, 'foto' => $foto, 'hora' => $hora_actual]
            ];
        }

        // 3. Insertar la asistencia como "Presente" (estatus = 1)
        $this->db->insert('asistencias', [
            'id_escuela'          => $id_escuela,
            'id_alumno'           => $id_alumno,
            'id_grupo'            => $id_grupo,
            'id_materia'          => ($tipo == 1) ? null : $id_materia,
            'id_dh'               => ($tipo == 1) ? null : $id_dh,
            'fecha'               => $fecha_hoy,
            'estatus'             => 1, // 1 = Presente por defecto al leer QR
            'tipo'                => $tipo,
            'id_usuario_registra' => $id_sesion
        ]);

        // 4. Retornar el success con los datos del alumno para la alerta visual
        return [
            'estatus' => true, 
            'mensaje' => 'Asistencia registrada correctamente.',
            'alumno'  => ['nombre' => $nombre_completo, 'foto' => $foto, 'hora' => $hora_actual]
        ];

    } catch (Exception $e) {
        return ['estatus' => false, 'mensaje' => 'Error en la base de datos: ' . $e->getMessage()];
    }
}

}