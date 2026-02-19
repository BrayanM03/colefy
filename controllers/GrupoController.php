<?php

require_once __DIR__ . '/../models/Grupo.php';

class GrupoController {
    private $id_sesion;
    public function __construct(){
        $this->id_sesion = $_SESSION['id'];
    }

    public function datatable() {
        $start = $_GET['start'] ?? 0;
        $length = $_GET['length'] ?? 10;
        $search = $_GET['search']['value'] ?? '';

        // Ordenamiento que manda DataTables
        $orderColumnIndex = $_GET['order'][0]['column'] ?? 0;
        $orderColumnName  = $_GET['columns'][$orderColumnIndex]['data'] ?? 'id';
        $orderDir         = $_GET['order'][0]['dir'] ?? 'asc';

        $grupo = new Grupo();
        $data = $grupo->obtenerGruposDataTable($start, $length, $search, $orderColumnName, $orderDir);
        $total = $grupo->contarGrupos();
        $filtered = $grupo->contarGruposFiltrados($search, $this->id_sesion);


        echo json_encode([
            "draw" => $_GET['draw'],
            "recordsTotal" => $total,
            "recordsFiltered" => $filtered, 
            "data" => $data
        ]);
    }

    public function datatable_pregrupo() {
        $start = $_GET['start'] ?? 0;
        $length = $_GET['length'] ?? 10;
        $search = $_GET['search']['value'] ?? '';

        // Ordenamiento que manda DataTables
        $orderColumnIndex = $_GET['order'][0]['column'] ?? 0;
        $orderColumnName  = $_GET['columns'][$orderColumnIndex]['data'] ?? 'id';
        $orderDir         = $_GET['order'][0]['dir'] ?? 'asc';

        $grupo = new Grupo();
        $data = $grupo->obtenerPreGrupoDataTable($start, $length, $search, $orderColumnName, $orderDir, $this->id_sesion );
        $total = $grupo->contarPreGrupo($this->id_sesion);
        $filtered = $grupo->contarPreGrupoFiltrados($search, $this->id_sesion);


        echo json_encode([
            "draw" => $_GET['draw'],
            "recordsTotal" => $total,
            "recordsFiltered" => $filtered, 
            "data" => $data
        ]);
    }

    public function datatable_detalle_grupo($id_grupo, $ciclo) {
        $start = $_GET['start'] ?? 0;
        $length = $_GET['length'] ?? 10;
        $search = $_GET['search']['value'] ?? '';

        // Ordenamiento que manda DataTables
        $orderColumnIndex = $_GET['order'][0]['column'] ?? 0;
        $orderColumnName  = $_GET['columns'][$orderColumnIndex]['data'] ?? 'id';
        $orderDir         = $_GET['order'][0]['dir'] ?? 'asc';

        $grupo = new Grupo();
        $data = $grupo->obtenerDetalleGrupoDataTable($start, $length, $search, $orderColumnName, $orderDir, $id_grupo, $ciclo);
        $total = $grupo->contarDetalleGrupo($id_grupo, $ciclo);
        $filtered = $grupo->contarDetalleGrupoFiltrados($search, $id_grupo, $ciclo);


        echo json_encode([
            "draw" => $_GET['draw'],
            "recordsTotal" => $total,
            "recordsFiltered" => $filtered, 
            "data" => $data
        ]);
    }

    public function combo(){
        $grupo = new Grupo();
        return ($grupo->combo());
    }

    public function registrarAlumnoPreGrupo($alumno){
        $grupo = new Grupo();
        $resp = $grupo->registrarAlumnoPreGrupo($alumno, $this->id_sesion);
        echo json_encode($resp);
    }   
    
    public function eliminarAlumnoPreGrupo($id_detalle){
        $grupo = new Grupo();
        $resp = $grupo->eliminarAlumnoPreGrupo($id_detalle, $this->id_sesion);
        echo json_encode($resp);
    }

    public function registrarGrupo($nombre, $nivel, $grado, $ciclo){
        $grupo = new Grupo();
        echo json_encode($grupo->procesarGrupo($nombre, $nivel, $grado, $ciclo, $this->id_sesion));
        
    }

    public function obtener_grupo($tipo_resp, $id_grupo){
        $grupo = new Grupo();
        $resp = $grupo->obtenerGrupo($id_grupo);
        if($tipo_resp==1){
            return $resp;
        }else{
            echo json_encode($resp);
        }
    }
    
    public function actualizarGrupo($nombre, $nivel, $grado, $ciclo, $id_grupo){
        $grupo = new Grupo();
        echo json_encode($grupo->actualizarGrupo($nombre, $nivel, $grado, $ciclo, $id_grupo));
    }

    public function registrarAlumnoGrupo($id_alumno, $id_grupo, $id_ciclo){
        $grupo = new Grupo();
        echo json_encode($grupo->insertarAlumnoGrupo($id_alumno, $id_grupo, $id_ciclo));
    }

    public function cancelarAlumnoGrupo($id_registro, $nuevo_estatus){
        $grupo = new Grupo();
        echo json_encode($grupo->cancelarAlumnoGrupo($id_registro, $nuevo_estatus));
    }

    public function obtenerGrupoCalificaciones($id_grupo, $id_ciclo){
        $grupo = new Grupo();
        echo json_encode($grupo->obtenerGrupoCalificaciones($id_grupo, $id_ciclo));
    }
        
    }