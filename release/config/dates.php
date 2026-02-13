
<?php
date_default_timezone_set('America/Matamoros'); // o tu zona horaria

class Date{
private $db;

public function __construct() {
    $this->db = new Database();
}

function obtenerFechaRegistro() {
    return date('Y-m-d H:i:s');
}

function obtenerCicloActual() {
    return $this->db->select('SELECT * FROM ciclos_escolares WHERE estatus = 1');
}

}