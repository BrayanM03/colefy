<?php
require_once __DIR__ . '/../models/Materia.php';

class MateriaController {
    public function datatable() {
        $start = $_GET['start'] ?? 0;
        $length = $_GET['length'] ?? 10;
        $search = $_GET['search']['value'] ?? '';

        $profesor = new Materia();
        $data = $profesor->obtenerMateriasDataTable($start, $length, $search);
        $total = $profesor->contarMaterias();

        echo json_encode([
            "draw" => $_GET['draw'],
            "recordsTotal" => $total,
            "recordsFiltered" => $total, 
            "data" => $data
        ]);
    }

    public function combos(){
        $materia = new Materia();

        $data = $materia->obtenerListaMaterias();
        $estatus = count($data) > 0 ? true : false;
        return [
            "data" => $data,
            "estatus" => $estatus
        ];
    }
}
