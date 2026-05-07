<?php
require_once __DIR__ . '/../models/Profesor.php';

class ProfesorController {
    public function datatable() {
        $start = $_GET['start'] ?? 0;
        $length = $_GET['length'] ?? 10;
        $search = $_GET['search']['value'] ?? '';

        $profesor = new Profesor();
        $data = $profesor->obtenerProfesoresDataTable($start, $length, $search);
        $total = $profesor->contarProfesores();

        echo json_encode([
            "draw" => $_GET['draw'],
            "recordsTotal" => $total,
            "recordsFiltered" => $total, 
            "data" => $data
        ]);
    }

    public function combos(){
        $profesor = new Profesor();

        $data = $profesor->obtenerListaProfesores();
        $estatus = count($data) > 0 ? true : false;
        return [
            "data" => $data,
            "estatus" => $estatus
        ];
    } 

    public function obtenerGruposProfesor($id_profesor){
        $profesor = new Profesor();
        return ($profesor->obtenerGruposProfesor($id_profesor));
    }

    public function  registrar_profesor($tipo_resp, $data){
        $nombre = $data['nombre'];
        $apellidos = $data['apellidos'];
        $especialidad = $data['especialidad'];
        $telefono = $data['telefono'];

        $profesor = new Profesor;
        $resp = $profesor->registrarProfesor($nombre, $apellidos, $especialidad, $telefono);

        if($tipo_resp==1){
            echo json_encode($resp);
        }else{
            return $resp;
        }

    }
}
