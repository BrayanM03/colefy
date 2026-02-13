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
}
