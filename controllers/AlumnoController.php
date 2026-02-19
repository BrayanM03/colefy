<?php
require_once __DIR__ . '/../models/Alumno.php';

class AlumnoController {
    public function datatable() {
        $start = $_GET['start'] ?? 0;
        $length = $_GET['length'] ?? 10;
        $search = $_GET['search']['value'] ?? '';
        // En AlumnoController.php (Línea 9 aproximadamente)

        // 1. Obtenemos el índice de la columna, si no existe, por defecto es 0
        $orderColumnIndex = $_POST['order'][0]['column'] ?? 0;

        // 2. Obtenemos la dirección, si no existe, por defecto es 'asc'
        $orderDir = $_GET['order'][0]['dir'] ?? 'asc';

        // 3. (Opcional) El término de búsqueda también puede dar warning si está vacío

        $alumno = new Alumno();
        $data = $alumno->obtenerAlumnosDataTable($start, $length, $search, $orderColumnIndex, $orderDir);
        $total = $alumno->contarAlumnos();

        echo json_encode([
            "draw" => $_GET['draw'],
            "recordsTotal" => $total,
            "recordsFiltered" => $total, 
            "data" => $data
        ]);
    }

    public function combo($busqueda = null){
        if($busqueda){
            $params =[$_SESSION['id_escuela'],'%'.$busqueda.'%',  '%'.$busqueda.'%', '%'.$busqueda.'%'];
            $sql_where = " AND id_escuela = ? AND (nombre LIKE ? OR apellido_paterno LIKE ? OR apellido_materno LIKE ?)";
        }else{
            $params = [$_SESSION['id_escuela']];
            $sql_where = '';//' AND id_escuela = ?';
        } 

        $alumno = new Alumno();
        
        $total = $alumno->contarAlumnos($sql_where, $params);
        if($total > 0){
            $data = $alumno->obtenerAlumnos($sql_where, $params);
            $estatus = true;
            $mensaje = 'Busqueda con resultados';
        }else{
            $mensaje='No se encontraron';
            $estatus = false;
            $data = [];
        }

        $response = array('estatus'=>$estatus, 'mensaje'=>$mensaje, 'data'=>$data, 'sql'=>$sql_where);
        echo json_encode($response);
    }

    public function registrarAlumno($nombre, $apellido_paterno, $apellido_materno, $cumple, $genero, $telefono){
        $alumno = new Alumno();
        $response = $alumno->registrarAlumno($nombre, $apellido_paterno, $apellido_materno, $cumple, $genero, $telefono);
        echo json_encode($response);
    }

    public function traerAlumno($id_alumno){
        $alumno = new Alumno();
        echo json_encode($alumno->traerAlumno($id_alumno));
    }

    public function actualizarAlumno($id_alumno, $nombre, $apellido_paterno, $apellido_materno, $cumple, $genero, $telefono){
        $alumno = new Alumno();
        $response = $alumno->actualizarAlumno($id_alumno, $nombre, $apellido_paterno, $apellido_materno, $cumple, $genero, $telefono);
        echo json_encode($response);
    }
}
