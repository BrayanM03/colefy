<?php
require(__DIR__ . '/../vendor/autoload.php'); 
require_once './controllers/HorarioController.php';
require_once './controllers/CatalogoController.php';
// require_once './controllers/AlumnoController.php';

// Verificaciones de sesión y permisos (Ajusta según tu lógica)

$controller_permiso = new PermisoController(); 
$controller_horario = new HorarioController(); 
$controller_catalogo = new CatalogoController(); 
$controller_permiso->verificarSesion();
$permiso_pdf = $controller_permiso->validarAcceso(2, CPermiso::VER_REPORTE_HORARIO->value);

if(!$permiso_pdf['estatus']){
    header("Location: " . BASE_URL . "sin_permiso/No cuentas con el permiso de ver este modulo");
}

$id_horario   = $_GET['id_horario'] ?? 0;
$tipo_reporte = $_GET['tipo_reporte'] ?? 1; // 1 = Grupo, 2 = Alumno
$id_alumno    = $_GET['id_alumno'] ?? null;

$data_horario = $controller_horario->obtenerHorario($id_horario, $tipo_reporte, 2);
$datos_generales = $data_horario['data']['horario'][0];
$detalle_horario = $data_horario['data']['detalle'];
/* echo json_encode($detalle_horario);
die(); */
$bloques = $detalle_horario;
// ==========================================
// 1. SIMULACIÓN DE DATOS (Reemplaza con tu BD)
// ==========================================
$datos_escuela = [
    'nombre' => $datos_generales['nombre_escuela'],
    'logo'   => $datos_generales['logo'] // Asegúrate de que exista en static/img/escuelas/
];

// Datos dinámicos según el tipo
if ($tipo_reporte == 1) {
    $datos_entidad = [
        'titulo'    => $datos_generales['nombre'],
        'subtitulo' => $datos_generales['grupo'],
        'ciclo'     => 'Ciclo Escolar ' . $datos_generales['ciclo']
    ];
} else {
    // Si es tipo 2, aquí harías tu $alumno_resp = $controller_alumno->obtener($id_alumno);
    $datos_entidad = [
        'titulo'    => 'Horario Individual del Alumno',
        'subtitulo' => 'Alumno: Maldonado, Brayan',
        'ciclo'     => 'Matrícula: 2026001 | Grupo: 1A'
    ];
}

// =========================================================
// 1.5. CONSTRUCCIÓN DINÁMICA DE LA MATRIZ (RESPETANDO EL SNAPSHOT)
// =========================================================
// =========================================================
// CONSTRUCCIÓN DE MATRIZ CON HORAS LIBRES (TIPO 3)
// =========================================================
$matriz_horario = [];
$dias_semana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];

foreach ($bloques as $bloque) {
    $hora_inicio = date('H:i', strtotime($bloque['hora']));
    $hora_fin    = date('H:i', strtotime($bloque['hora_fin']));
    $clave       = $hora_inicio . ' - ' . $hora_fin;

    if (!isset($matriz_horario[$clave])) {
        $matriz_horario[$clave] = [];
        foreach ($dias_semana as $dia) {
            $matriz_horario[$clave][$dia] = ['materia' => '', 'profesor' => ''];
        }
    }

    $dia_clase = $bloque['dia'];

    // Lógica de impresión según el tipo
    if ($bloque['tipo'] == 2) {
        $matriz_horario[$clave][$dia_clase] = [
            'materia'  => 'RECESO',
            'profesor' => ''
        ];
    } elseif ($bloque['tipo'] == 3) {
        $matriz_horario[$clave][$dia_clase] = [
            'materia'  => 'Sin asignar', // <-- Aquí entra tu texto
            'profesor' => ''
        ];
    } else {
        $matriz_horario[$clave][$dia_clase] = [
            'materia'  => $bloque['materia'],
            'profesor' => $bloque['profesor'] ?? ''
        ];
    }
}

ksort($matriz_horario);

// ==========================================
// 2. CONFIGURACIÓN DEL PDF
// ==========================================
$fn_iconv = function($text) {
    return iconv('UTF-8', 'ISO-8859-1', $text);
};

class PDFHorario extends FPDF {
    public $escuela;
    public $entidad;

    function Header() {
        global $fn_iconv;
        
        // Logo
        $logo_path = './static/img/escuelas/' . $this->escuela['logo'];
        /* print_r($logo_path);
        die(); */
        if(file_exists($logo_path)){
            $this->Image($logo_path, 10, 8, 25);
        }

        // Título del Colegio
        $this->SetFont('Arial', 'B', 16);
        $this->SetY(12);
        $this->SetX(40);
        $this->Cell(150, 8, $fn_iconv($this->escuela['nombre']), 0, 1, 'L');

        // Título del Reporte (Horario)
        $this->SetFont('Arial', 'B', 12);
        $this->SetX(40);
        $this->SetTextColor(70, 70, 70);
        $this->Cell(150, 6, $fn_iconv($this->entidad['titulo']), 0, 1, 'L');

        // Datos del Grupo / Alumno
        $this->SetFont('Arial', '', 11);
        $this->SetX(40);
        $this->Cell(150, 6, $fn_iconv($this->entidad['subtitulo'] . ' | ' . $this->entidad['ciclo']), 0, 1, 'L');

        $this->Ln(10);
    }

    function Footer() {
        global $fn_iconv;
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(128);
        // Pie de página Colefy
        $this->Cell(0, 10, $fn_iconv('Generado por Colefy by Mabac - ' . date('d/m/Y H:i')), 0, 0, 'C');
    }
}

// Instanciar PDF en modo 'L' (Landscape)
$pdf = new PDFHorario('L', 'mm', 'Letter');
$pdf->escuela = $datos_escuela;
$pdf->entidad = $datos_entidad;
$pdf->SetAutoPageBreak(true, 20);
$pdf->AddPage();

// ==========================================
// 3. DIBUJAR LA TABLA DEL HORARIO
// ==========================================
$ancho_hora = 35; 
$ancho_dia = 45; // 35 + (45 * 5) = 260mm (Casi el ancho total de hoja carta apaisada)
$alto_celda = 12; // Fila alta para que quepa materia y profesor

// Encabezados de los días
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(48, 173, 255); // Azul Colefy
$pdf->SetTextColor(255, 255, 255);

$pdf->Cell($ancho_hora, 8, 'Hora', 1, 0, 'C', true);
foreach ($dias_semana as $dia) {
    $pdf->Cell($ancho_dia, 8, $fn_iconv($dia), 1, 0, 'C', true);
}
$pdf->Ln();

// Dibujar el cuerpo del horario
$pdf->SetTextColor(0, 0, 0);
$fill = false; // Alternar colores de fila

// Recorremos la matriz que ya armamos previamente
foreach ($matriz_horario as $hora => $dias) {
    
    // 1. Detección: ¿Es esta fila un receso en toda la semana?
    $es_descanso_semanal = true;
    foreach ($dias_semana as $dia) {
        // Si hay al menos un día que NO sea receso, cancelamos la bandera
        if (!isset($dias[$dia]['materia']) || $dias[$dia]['materia'] !== 'RECESO') {
            $es_descanso_semanal = false;
            break;
        }
    }

    // 2. Dibujamos la columna de la HORA (Esta siempre va igual)
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->SetTextColor(0, 0, 0); // Texto negro
    $pdf->Cell($ancho_hora, $alto_celda, $hora, 1, 0, 'C');

    // 3. Lógica bifurcada: Dibujar celda gigante o celdas normales
    if ($es_descanso_semanal) {
        
        // DIBUJAR DESCANSO: Una sola celda del tamaño de los 5 días
        $ancho_total_dias = $ancho_dia * 5; 
        
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetTextColor(100, 100, 100); // Texto gris oscuro
        $pdf->SetFillColor(240, 240, 240); // Fondo gris clarito
        
        // Imprimimos la celda. El true al final activa el color de fondo. El 1 antes de la 'C' hace el salto de línea.
        $pdf->Cell($ancho_total_dias, $alto_celda, 'R E C E S O', 1, 1, 'C', true);
        
    } else {
        
        // DIBUJAR CLASES: Imprimimos día por día normalmente
        foreach ($dias_semana as $dia) {
            $materia  = $fn_iconv($dias[$dia]['materia']);
            $profesor = $fn_iconv($dias[$dia]['profesor']);
            
            // Guardamos las coordenadas actuales (X, Y) antes de dibujar
            $x = $pdf->GetX();
            $y = $pdf->GetY();
            
            // 1. Dibujamos SOLO el borde de la celda (completamente vacía)
            $pdf->Cell($ancho_dia, $alto_celda, '', 1, 0, 'C');
            
            // 2. Jugamos con el cursor para escribir texto en el interior
            if ($materia === 'Sin asignar') {
                $pdf->SetXY($x, $y ); // Bajamos un poco para centrar
                $pdf->SetFont('Arial', '', 9);
                $pdf->SetTextColor(180, 180, 180); // Gris claro
                $pdf->Cell($ancho_dia, $alto_celda, 'Sin asignar', 0, 0, 'C');
            } else {
                // Escribir MATERIA (Arriba)
                $pdf->SetXY($x, $y -1); // Margen superior de 2mm
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->SetTextColor(0, 0, 0); // Negro
                $pdf->Cell($ancho_dia, $alto_celda, $materia, 0, 2, 'C');
                //$pdf->MultiCell($ancho_dia, 4, $materia."\n".$profesor, 0, 'C'); // El '2' al final hace salto de línea corto

                // Escribir PROFESOR (Abajo)
                if(!empty($profesor)){
                    $pdf->SetXY($x, $y + 2.5);
                    $pdf->SetFont('Arial', 'I', 7); // Cursiva y un poco más pequeña
                    $pdf->SetTextColor(80, 80, 80); // Gris oscuro
                    $pdf->SetX($x); // Alineamos al borde izquierdo de la celda actual
                    $pdf->Cell($ancho_dia, $alto_celda, $profesor, 0, 0, 'C');
                }
            }
            
            // 3. Regresamos el cursor a la esquina superior derecha para el siguiente día
            $pdf->SetXY($x + $ancho_dia, $y);
        }
        
        // Salto de línea al terminar de imprimir los 5 días normales
        $pdf->Ln();
    }
}
// Salida
$nombre_archivo = ($tipo_reporte == 1) ? 'Horario_Grupo.pdf' : 'Horario_Alumno.pdf';
$pdf->Output('I', $nombre_archivo);
?>