<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/dates.php';
require_once __DIR__ . '/../models/Profesor.php';
require_once __DIR__ . '/../models/Grupo.php';
require_once __DIR__ . '/../models/Materia.php';

class Catalogo {
    private $db;
    private $fecha;

    public function __construct() {
        $this->db = new Database();
        $this->fecha = new Date();
    }

    public function iniciarFlujo($ciclo, $nivel) {
        $profesor = new Profesor;
        $grupo = new Grupo;
        $profesores = $profesor->obtenerListaProfesores();
        $grupos = $grupo->combo();

        $response = array('estatus'=> true, 'mensaje'=> 'Se encontraron datos','docentes'=>$profesores, 'grupos' => $grupos);
        return $response;
    }

    public function segundoPasoFlujo($nivel){
        $materia = new Materia;
        $materias = $materia->obtenerListaMaterias();
        $response = array('estatus'=> true, 'mensaje'=> 'Se encontraron datos','materias'=>$materias);
        return $response;
    }
}
