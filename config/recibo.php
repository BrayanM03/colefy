<?php
require(__DIR__ . '/../vendor/autoload.php'); 
/* require_once 'permisos-enum.php';
require_once '../controllers/PermisoController.php'; */
require_once './controllers/ReciboController.php';
$controller_permiso = new PermisoController(); 
$controller_recibo = new ReciboController(); 

$controller_permiso->verificarSesion();
$id_rol = $_SESSION['rol'];
$permiso_pdf = $controller_permiso->validarAcceso(2, CPermiso::VER_RECIBO_PDF->value);

if(!$permiso_pdf['estatus']){
    header("Location: " . BASE_URL . "src/vistas/error/sin_permisos.php");
}

$id_recibo = $_GET['id_recibo'];
$recibo_resp = $controller_recibo->obtener_recibo($id_recibo, 2);
if(!$recibo_resp['estatus']){
    $mensaje = urlencode($recibo_resp['mensaje']);

    header("Location: " . BASE_URL . "src/vistas/error/error.php?msg=$mensaje");

}
$recibo = $recibo_resp['data'];
$estado_recibo = $recibo['estatus'];
$tipos = [1=>'Contado', 2 =>'Parcial'];
$tipo_recibo = $tipos[$recibo['tipo']];


// Datos de la venta (ejemplo)
$datos_empresa = [
    'nombre' => 'Colegio Bilingüe Camerino Martinez',
    'direccion' => 'Calle Amapolas #47, Colonia Las Flores',
    'telefono' => '868-19-8845',
    'folio' => 'REC-'.$recibo['folio_escuela'],
    'fecha' => formatearFechaEsp($recibo['fecha_registro']),
    'alumno' => $recibo['alumno'],
    'grupo' => $recibo['grupo'],
    'tipo' => $tipo_recibo,
    'id_tipo' => $recibo['tipo']
];


$productos = $recibo['conceptos']; /* [
    ['cant' => 1, 'desc' => 'Mensualidad del mes Septiembre ciclo 2025/2026', 'precio' => 1650.00],
    ['cant' => 2, 'desc' => 'Uniforme', 'precio' => 175.00],
    ['cant' => 1, 'desc' => 'Cafeteria', 'precio' => 150.00]
]; */

/* $subtotal = array_sum(array_map(fn($p) => $p['cant'] * $p['precio'], $productos));
$iva_tasa = 0.16; 
$iva_monto = $subtotal * $iva_tasa;*/

$total = $recibo['monto_total']; //array_sum(array_map(fn($p) => $p['cant'] * $p['precio'], $productos));
$saldo_pendiente = $recibo['saldo_pendiente'];
$abonado = floatval($total)  - floatval($saldo_pendiente);
// Función formatea fecha
function formatearFechaEsp($fecha) {
    if (!$fecha) return '';

    $meses = [
        '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo',
        '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio',
        '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre',
        '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
    ];

    $date = new DateTime($fecha);
    $mes = $meses[$date->format('m')];
    $hora = strtolower($date->format('g:i a')); // 4:33 pm

    return $date->format("j") . " de $mes " . $date->format("Y") . ", " . $hora;
}
// Clase personalizada para el PDF
class PDF extends FPDF
{
    // Función para dibujar un recibo
    // Solo necesitamos $y_inicio para la posición vertical
    // Función para dibujar un recibo
    function DibujarRecibo($y_inicio, $datos_empresa, $productos, $total, $saldo_pendiente, $abonado, $es_cliente, $estatus = 1)
    {
        $this->SetTitle($datos_empresa['folio']);

        $ancho_recibo = 195; 
        
        // --- CONFIGURACIÓN PARA LOGO Y FOLIO ---
        $ancho_logo = 26; 
        $alto_logo = 28;  
        $ancho_folio = 30;
        // Ajustamos el ancho del texto para que el ancho_recibo (195) se use en el Cell
        $ancho_texto_encabezado = $ancho_recibo - ($ancho_logo + $ancho_folio); 
        $ancho_bloque_central = $ancho_recibo - $ancho_logo - $ancho_folio; // 195 - 26 - 30 = 139

        // Función auxiliar para codificación
        $fn_iconv = function($text) {
            return iconv('UTF-8', 'ISO-8859-1', $text);
        };

         // Llama a la marca de agua ANTES de dibujar la tabla.
        // El método Watermark ya fue corregido para no mover el cursor.
        $this->Watermark($y_inicio, $estatus);
        
        $this->SetY($y_inicio);
        $this->SetX(10); 
        $this->SetFillColor(230, 230, 230); 
        $this->SetDrawColor(180, 180, 180); 

        // --- ENCABEZADO: FILA 1 (Título) ---
        $titulo = $es_cliente ? 'RECIBO ORIGINAL - CLIENTE' : 'RECIBO COPIA - COLEGIO';
        $this->SetFont('Arial', 'B', 10);
        $this->Cell($ancho_recibo, 5, $fn_iconv($titulo), 'TRL', 1, 'C', true);

        // --- DIBUJAR EL LOGO (usamos SetY para que quede al lado del texto) ---
        $this->Image('./static/img/logo_2.png', 10 + 7, $y_inicio + 6, $ancho_logo - 10, $alto_logo - 10);
        
        
        // --- ENCABEZADO: FILA 2 (Nombre de la Empresa) ---
        $this->SetX(10);
        $this->SetFont('Arial', 'B', 11);
        
        // 1. Celda Logo (Espacio reservado)
        $this->Cell($ancho_logo, 7, '', 'L', 0); 

        // 2. Celda Nombre
        $this->Cell($ancho_bloque_central, 7, $fn_iconv($datos_empresa['nombre']), 0, 0,'C');
        
        // 3. Celda FOLIO
        if($datos_empresa['id_tipo']==1){
            $this->SetTextColor(48, 173, 255); 
        }else if ($datos_empresa['id_tipo']==2){
            $this->SetTextColor(255, 99, 71); 
        }
        $this->Cell($ancho_folio, 7, 'Tipo:'. $datos_empresa['tipo'], 'R', 1, 'C');
        $this->SetTextColor(0, 0, 0); // Vuelve a negro
        
        

        // --- ENCABEZADO: FILA 3 (Dirección y Folio Número) ---
        $this->SetX(10);
        $this->SetFont('Arial', '', 9);
        
        // 1. Celda Logo (Espacio reservado)
        $this->Cell($ancho_logo, 7, '', 'L', 0); 

        // 2. Celda Dirección
        $this->Cell($ancho_bloque_central, 7, $fn_iconv($datos_empresa['direccion']), 0, 0, 'C');
        
        // 3. Celda Folio Número
        $this->Cell($ancho_folio, 7, $fn_iconv('FOLIO: '.$datos_empresa['folio']), 'R', 1,'C');

        // --- ENCABEZADO: FILA 4 (Teléfono) ---
        $this->SetX(10);
        
        // 1. Celda Logo (Espacio reservado, Borde Inferior cierra el marco)
        $this->Cell($ancho_logo, 7, '', 'LB', 0); 

        // 2. Celda Teléfono (Borde Inferior)

        $ancho_telefono = $datos_empresa['id_tipo']== 1 ? 60 : 40;
        $fechas_sr = $datos_empresa['id_tipo']== 1 ? $fn_iconv('Fecha: ') .' '. $datos_empresa['fecha']: $fn_iconv('Fecha: ') .' '. $datos_empresa['fecha'] . ' -- '.$fn_iconv('Fecha vencimiento: ') . $datos_empresa['fecha'] ;
        $ancho_fechas =($ancho_recibo - $ancho_logo - $ancho_telefono);
        $this->Cell( $ancho_telefono, 7, 'Tel: ' . $datos_empresa['telefono'], 'B', 0, 'R');
        
        // 3. Celda Derecha (Borde Derecho)
        $this->Cell($ancho_fechas, 7, $fechas_sr, 'BR', 1,'L');


        // --- Detalles del Recibo ---
        $this->SetX(10);
        $this->SetFont('Arial', 'B', 9);
        $this->SetTextColor(0, 0, 0);
        // Dividimos la información de recibo/fecha en dos celdas grandes
        $this->Cell($ancho_recibo / 2, 6, 'Alumno: ' . $fn_iconv($datos_empresa['alumno']), 1, 0, 'L', true);
        $grupo_sr = 'Grupo: '. $fn_iconv($datos_empresa['grupo']);
        $this->Cell($ancho_recibo / 2, 6, $grupo_sr, 1, 1, 'L', true);

       
        
        // --- Tabla de Productos - Encabezado ---
        // ********** NUEVOS ANCHOS PARA CUATRO COLUMNAS **********
        // 195mm total: Cantidad (20) + Descripción (90) + P. Unitario (35) + Importe (50) = 195
        $w = [20, 90, 35, 50]; 
        $header = [$fn_iconv('Cant.'), $fn_iconv('Descripción'), $fn_iconv('P. Unitario'), $fn_iconv('Importe')];

        $this->SetX(10);
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(150, 200, 230); 
        for ($i = 0; $i < count($header); $i++) {
            $this->Cell($w[$i], 6, $header[$i], 1, 0, 'C', true);
        }
        $this->Ln();

        // --- Tabla de Productos - Datos ---
        $this->SetFont('Arial', '', 9);
        $this->SetFillColor(255, 255, 255); 
        foreach ($productos as $producto) {
            $importe = $producto['importe'];

            $this->SetX(10);
            $this->Cell($w[0], 6, $producto['cantidad'], 'LR', 0, 'C'); 
            $this->Cell($w[1], 6, $fn_iconv($producto['concepto']), 'R', 0, 'L'); 
            // ********** NUEVA CELDA: PRECIO UNITARIO **********
            $this->Cell($w[2], 6, '$ ' . number_format($producto['precio_unitario'], 2, '.', ','), 'R', 0, 'R'); 
            $this->Cell($w[3], 6, '$ ' . number_format($importe, 2, '.', ','), 'R', 1, 'R'); 
        }
        
        // Celdas vacías para rellenar 
        $num_lineas_productos = count($productos);
        $max_lineas = 5; 
        for ($i = $num_lineas_productos; $i < $max_lineas; $i++) {
            $this->SetX(10);
            $this->Cell($w[0], 6, '', 'LR', 0);
            $this->Cell($w[1], 6, '', 'R', 0);
            $this->Cell($w[2], 6, '', 'R', 0); // Celda vacía para P. Unitario
            $this->Cell($w[3], 6, '', 'R', 1);
        }

        // Línea final de la tabla
        $this->SetX(10);
        $this->Cell($ancho_recibo, 0, '', 'T', 1);
        
        // --- Totales ---
        $this->SetX(10);
        $this->SetFont('Arial', 'B', 11);
            $this->SetFillColor(255, 230, 230); // Otro color
        $this->Cell($ancho_recibo - 60, 8, '', 0, 0); // Espacio en blanco a la izquierda
        $this->Cell(20, 8, $fn_iconv('TOTAL:'), 1, 0, 'L', true);
        $this->Cell(40, 8, '$ ' . number_format($total, 2, '.', ','), 1, 1, 'R', true);

        if($datos_empresa['id_tipo']== 2){
            // Añadir celdas para Abono (Payment/Deposit)
            $this->SetFont('Arial', '', 9);
            $this->SetX(10); // Restablecer posición X
            $this->SetFillColor(220, 240, 240); // Un color diferente para distinguirlo, por ejemplo
            $this->Cell($ancho_recibo - 60, 8, '', 0, 0); // Espacio en blanco a la izquierda
            $this->Cell(20, 8, $fn_iconv('ABONADO:'), 1, 0, 'L', true);
            $this->Cell(40, 8, '$ ' . number_format($abonado, 2, '.', ','), 1, 1, 'R', true); // Se asume una variable $abono

            // Añadir celdas para Restante (Remaining/Balance)
            $this->SetX(10); // Restablecer posición X
        $this->SetFillColor(230, 240, 250); 
            $this->Cell($ancho_recibo - 60, 8, '', 0, 0); // Espacio en blanco a la izquierda
            $this->Cell(20, 8, $fn_iconv('RESTANTE:'), 1, 0, 'L', true);
            $this->Cell(40, 8, '$ ' . number_format($saldo_pendiente, 2, '.', ','), 1, 1, 'R', true); // Se asume una variable $restante
        }
        
        // --- Pie de página del recibo ---
        $this->SetFont('Arial', '', 9);
        $this->SetX(10);
        $this->Cell($ancho_recibo, 6, $fn_iconv('¡Gracias por su preferencia!'), 0, 1, 'C');
        
        // Espacio para la firma 
        if ($es_cliente) {
            $this->SetY($this->GetY() + 7);
            $this->SetX(60); 
            $this->Cell(85, 0, '', 'T', 1, 'C');
            $this->SetX(60);
            $this->Cell(85, 5, $fn_iconv('CP Gabriela Ruiz'), 0, 1, 'C');
        }
    }

    // Método para la marca de agua (Watermark)
    // Recibe el estado para saber si debe mostrar 'CANCELADO'
    function Watermark($y_base, $estatus = 1) 
    {
       // 1. Guardar la posición actual X, Y
            $x_antes = $this->GetX();
            $y_antes = $this->GetY();
            
            // --- CONFIGURACIÓN BASE DEL LOGO ---
            
            $logo_path = './static/img/logo_opaco.png';
            $ancho_recibo = 195;
            $ancho_logo_wm = $ancho_recibo * 0.1; // Ajustado a 10% 
            $alto_logo_wm = $ancho_logo_wm; 

            // Calcular posición X central (195 - 78) / 2 = 58.5mm desde el margen izquierdo (10mm)
            $x_pos = 10 + ($ancho_recibo - $ancho_logo_wm) / 2;
            
            // Calcular posición Y: $y_base (inicio del recibo) + offset (por ejemplo, 50mm hacia abajo)
            $y_logo_pos = $y_base + 40; 
            
            // 2. Dibujar el Logo como Marca de Agua
            $this->Image($logo_path, $x_pos, $y_logo_pos, $ancho_logo_wm, $alto_logo_wm);

            // 3. Dibujar "CANCELADO" si el estatus es 0
            if ($estatus === 0) {
                // Calcular posición Y del texto: $y_base (inicio del recibo) + offset (por ejemplo, 73mm hacia abajo)
                $y_texto_pos = $y_base + 64; 
                
                $this->SetY($y_texto_pos); 
                $this->SetX(10); // Siempre X=10 para el ancho total
                
                // Fuente y color rojo muy claro
                $this->SetFont('Arial', 'B', 20); 
                $this->SetTextColor(255, 200, 200); 
                
                $texto_marca = 'RECIBO CANCELADO';
                $this->Cell(195, 0, $texto_marca, 0, 0, 'C');
                
                // Restablecer el color de texto
                $this->SetTextColor(0, 0, 0); 
            }

            // 4. Restaurar la posición X, Y para que el flujo de la tabla continúe
            $this->SetXY($x_antes, $y_antes); 
            $this->SetFont('Arial', '', 9);
    }
}

 // ... (Tus datos de empresa y productos)
 $fn_iconv = function($text) {
    return iconv('UTF-8', 'ISO-8859-1', $text);
};


// Creación del PDF
$pdf = new PDF('P', 'mm', 'Letter'); // Tamaño Carta
$pdf->AddPage();
$pdf->SetAutoPageBreak(false); // Desactivamos el salto de página automático

// --- RECIBO SUPERIOR: Cliente ---
$y_cliente = 10; // Posición Y inicial
$pdf->DibujarRecibo($y_cliente, $datos_empresa, $productos, $total,  $saldo_pendiente, $abonado, true, $estado_recibo);

// --- Línea de Corte Horizontal ---
// La línea irá justo después del primer recibo, con un poco de espacio
$y_corte = $pdf->GetY() + 10;
$pdf->SetDrawColor(0, 0, 0);
$pdf->SetLineWidth(0.2); 
$pdf->Line(10, $y_corte, 205, $y_corte); // Línea horizontal de margen a margen (10 a 205)

// Texto de corte
$pdf->SetFont('Arial', 'I', 7);
$pdf->SetTextColor(100, 100, 100); 
$pdf->SetXY(80, $y_corte - 4); 
$pdf->Cell(50, 4,  $fn_iconv('Colefy by Mabac', 'ISO-8859-1', '--- RECORTE AQUÍ ---'), 0, 0, 'C'); 
$pdf->SetTextColor(0, 0, 0);
$pdf->SetLineWidth(0.1); 

// --- RECIBO INFERIOR: Vendedor ---
$y_vendedor = $y_corte + 10; // 10 mm debajo de la línea de corte
$pdf->DibujarRecibo($y_vendedor, $datos_empresa, $productos, $total, $saldo_pendiente, $abonado, false, $estado_recibo);


// Salida del PDF (mostrar en el navegador)
$pdf->Output('I', 'Recibo ' . $datos_empresa['folio'] . '.pdf');
?>
?>
?>
