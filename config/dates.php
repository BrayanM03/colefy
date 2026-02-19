
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

/**
 * Convierte Y-m-d a "18 de Febrero 2026"
 */
function formatearFechaEspanol($fecha) {
    if (!$fecha || $fecha == '0000-00-00') return "Fecha no válida";
    
    // Arrays de traducción manual
    $dias = array("Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado");
    $meses = array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
    
    $timestamp = strtotime($fecha);
    
    $numDia = date('d', $timestamp);
    $nombreMes = $meses[date('n', $timestamp)];
    $anio = date('Y', $timestamp);
    
    // Retorna el formato: 18 de Febrero 2026
    return $numDia . " de " . $nombreMes . " " . $anio;
}

}