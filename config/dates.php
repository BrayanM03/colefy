
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
    if (!$fecha) return "Fecha no válida";

    $dateTime = new DateTime($fecha);
    
    $formatter = new IntlDateFormatter(
        'es_ES', // Localización en español
        IntlDateFormatter::LONG, 
        IntlDateFormatter::NONE,
        'America/Mexico_City', // Tu zona horaria
        IntlDateFormatter::GREGORIAN,
        "d 'de' MMMM y" // Formato: día de Mes año
    );

    return $formatter->format($dateTime);
}

}