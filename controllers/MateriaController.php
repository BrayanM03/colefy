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

    public function  registrar_materia($tipo_resp, $data){
        $nombre = $data['nombre_materia'];
        $codigo = $data['codigo'];

        $profesor = new Materia;
        $resp = $profesor->registrarMateria($nombre, $codigo);

        if($tipo_resp==1){
            echo json_encode($resp);
        }else{
            return $resp;
        }

    }
}
