<?php

include '../database/conexion.php';


//require '../../vistas/vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\SpreadSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

//require '../../vendor/autoload.php';

 require_once '../../vendor/phpoffice/phpspreadsheet/samples/Bootstrap.php'; 
date_default_timezone_set("America/Matamoros");
session_start(); 

$nombre_del_usuario = $_SESSION["nombre"] . " ". $_SESSION["apellido"];
$año = date("Y");
$fecha=date("Y-m-d");
$count = 0; 
    

        //Creamos objeto de hoja de excel
        $spreadsheet = new SpreadSheet();
        $spreadsheet->getProperties()->setCreator($nombre_del_usuario)->setTitle("Primer excel");

        //ITERACIONES
      
            
            //Esablecemos y obtenemos la primera hoja activa -- 

            $spreadsheet->createSheet();
            
            $spreadsheet->setActiveSheetIndex(0);
            $hoja_activa = $spreadsheet->getActiveSheet();
            $hoja_activa->setTitle("Reporte inventario " . $fecha);

            //$categoria = 'computadorascat';

            $arreglo = traerDatosOrdenes($con);
            /* echo json_encode($arreglo);
            die(); */
         
            
            $cantidad_resultado = count($arreglo);

             //Establecemos cabezera del reporte
           //Combinar y centrar
           $hoja_activa->mergeCells("A1:B1");
           $hoja_activa->mergeCells("C1:H1");
           $hoja_activa->setCellValue('C1', 'Reporte de creditos vencidos este mes ' . $fecha);
           $hoja_activa->getStyle('C1')->getFont()->setBold(true);
           $hoja_activa->getStyle('C1')->getFont()->setSize(16);
           $hoja_activa->getRowDimension('1')->setRowHeight(50);
           $hoja_activa->getStyle('A1')->getAlignment()->setHorizontal('center');
           $hoja_activa->getStyle('A1')->getAlignment()->setVertical('center');
           $hoja_activa->getStyle('C1')->getAlignment()->setHorizontal('center');
           $hoja_activa->getStyle('C1')->getAlignment()->setVertical('center');

               //Establecer logos
           $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
           $drawing->setName('LogoPSC');
           $drawing->setDescription('Logo');
           $drawing->setPath('../../static/img/logo.jpg'); // put your path and image here
           $drawing->setCoordinates('A1');
           $drawing->setOffsetX(20);
           $drawing->setWidth(80);
           $drawing->setHeight(63);
           $drawing->setWorksheet($hoja_activa);
        
           $hoja_activa->setAutoFilter('A2:G2');

          $hoja_activa->mergeCells("A2:H2");
          $hoja_activa->getStyle('A2:H2')->getAlignment()->setHorizontal('center');
          $hoja_activa->getStyle('A2:H2')->getAlignment()->setVertical('center');
          $hoja_activa->setCellValue('A2', 'Reporte de terrenos vendidos este mes de los cuales la fecha de vencimiento a vencido');

       
          $hoja_activa->getColumnDimension('A')->setWidth(10);
          $hoja_activa->setCellValue('A3', '#');
          $hoja_activa->getColumnDimension('B')->setWidth(10);
          $hoja_activa->setCellValue('B3', 'Proyecto');
          $hoja_activa->getColumnDimension('C')->setWidth(10);
          $hoja_activa->setCellValue('C3', 'Manzana');
          $hoja_activa->getColumnDimension('D')->setWidth(10);
          $hoja_activa->setCellValue('D3', 'Lote');
          $hoja_activa->getColumnDimension('E')->setWidth(10);
          $hoja_activa->setCellValue('E3', 'Area');
          $hoja_activa->getColumnDimension('F')->setWidth(20);
          $hoja_activa->setCellValue('F3', 'Precio');
          $hoja_activa->getColumnDimension('G')->setWidth(20);
          $hoja_activa->setCellValue('G3', 'Pagado');
          $hoja_activa->getColumnDimension('H')->setWidth(20);
          $hoja_activa->setCellValue('H3', 'Restante');
          $hoja_activa->getColumnDimension('I')->setWidth(20);
          $hoja_activa->setCellValue('I3', 'Mensualidad');
          $hoja_activa->getColumnDimension('J')->setWidth(25);
          $hoja_activa->setCellValue('J3', 'Cliente');
          $hoja_activa->getColumnDimension('K')->setWidth(25);
          $hoja_activa->setCellValue('K3', 'Numero');
          $hoja_activa->getColumnDimension('L')->setWidth(25);
          $hoja_activa->setCellValue('L3', 'Fecha compra');
          $hoja_activa->getColumnDimension('M')->setWidth(25);
          $hoja_activa->setCellValue('M3', 'Fecha vencimiento');     
          $hoja_activa->getColumnDimension('N')->setWidth(15);
          $hoja_activa->setCellValue('N3', 'Estatus terreno');    
          $hoja_activa->getColumnDimension('O')->setWidth(15);
          $hoja_activa->setCellValue('O3', 'Estatus Orden');
          $hoja_activa->getColumnDimension('P')->setWidth(15);
          $hoja_activa->setCellValue('P3', 'Orden folio');  
          
          $hoja_activa->getStyle('A3:P3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('007bcc');
          $hoja_activa->getStyle('A3:P3')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
          $hoja_activa->getStyle('A3:P3')->getFont()->setBold(true);
          $hoja_activa->getRowDimension('3')->setRowHeight(20);
           //Validamos si se encontraron registros en la tabla, se valida 
           if ($cantidad_resultado == 0) {
            
                $hoja_activa->mergeCells("A4:P4");
                $hoja_activa->setCellValue('A4', 'Sin terrenos vencidos por el momento');
                $index = 2;
           
        
            }else{
         
           $fila = 3;

           //Estilos de las filas intercaladas
           /* $evenRow = [
               'fill'=>[
                   'fillType' => Fill::FILL_SOLID,
                   'startColor' => [
                       'rgb' => 'f4fbff'
                   ]
                   ] 
           ];

           $oddRow = [
               'fill'=>[
                   'fillType' => Fill::FILL_SOLID,
                   'startColor' => [
                       'rgb' => '90d3ff'
                   ]
                   ]
           ]; */
           $total_ingresos_efectivo = 0;
           $index =4;
           //Recorremos el arreglo
           while ($row = array_shift($arreglo)) {
               # trabajos con los datos
          
               $proyecto= $row['proyecto'];
               $manzana= $row['manzana'];
               $lote= $row['lote'];
               $area= $row['area'];
               $precio = $row['precio'];
               $pagado = $row['pagado'];
               $restante = $row['restante'];
               $mensualidad = $row['mensualidad'];
               $cliente = $row['cliente_etiqueta'];
               $telefono = $row['telefono'];
               $id= $row['id'];
               $fecha_compra = $row['fecha'];
               $fecha_vencimiento = $row['fecha_vencimiento'];
               $estatus_terreno = $row['estatus'];
               $estatus_orden = $row['estatus_orden'];
               $orden_id= $row['orden_folio'];

               $hoja_activa->setCellValue('A' . $index, $id);
               $hoja_activa->setCellValue('B' . $index, $proyecto);
               $hoja_activa->setCellValue('C' . $index, $manzana);
               $hoja_activa->setCellValue('D' . $index, $lote);
               $hoja_activa->setCellValue('E' . $index, $area);
               $hoja_activa->setCellValue('F' . $index, $precio);
               $hoja_activa->setCellValue('G' . $index, $pagado);
               $hoja_activa->setCellValue('H' . $index, $restante);
               $hoja_activa->setCellValue('I' . $index, $mensualidad);
               $hoja_activa->setCellValue('J' . $index, $cliente);
               $hoja_activa->setCellValue('K' . $index, $telefono);
               $hoja_activa->setCellValue('L' . $index, $fecha_compra);
               $hoja_activa->setCellValue('M' . $index, $fecha_vencimiento);
               $hoja_activa->setCellValue('N' . $index, $estatus_terreno);
               $hoja_activa->setCellValue('O' . $index, $estatus_orden);
               $hoja_activa->setCellValue('P' . $index, $orden_id);
               $hoja_activa->getStyle('A' .$index. ':P' .$index)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('6495ed'));
               $fila++;

               $index++;

               /* if ($index % 2 == 0) {
                   $hoja_activa->getStyle('A' .$index. ':J' .$index)->applyFromArray($evenRow);
               }else{
                   $hoja_activa->getStyle('A' .$index. ':J' .$index)->applyFromArray($oddRow);    
               } */

              
           }

           
       }
          
           $count++;
        

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Reporte inventario - '.$fecha .'.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        
        $writer->save('php://output');

    
        //Funcion que traera los datos de la base de datos

        function traerDatosOrdenes($con){
            $consulta = "SELECT COUNT(*) FROM `terrenos` t LEFT JOIN detalle_orden de ON t.manzana = de.manzana AND t.lote = de.lote AND t.proyecto = de.id_proyecto LEFT JOIN ordenes o ON de.orden_id = o.id";
            $res = $con->prepare($consulta);
            $res->execute();
            $total = $res->fetchColumn();
            if($total > 0){
                $consulta = "SELECT t.id, p.nombre as proyecto, t.manzana, t.lote, de.area, de.precio, de.pagado, 
                de.restante, de.mensualidad, o.cliente_etiqueta, o.telefono, o.fecha, de.fecha_vencimiento, 
                t.estatus, de.estatus as estatus_orden, o.id as orden_folio FROM `terrenos` t 
                LEFT JOIN detalle_orden de ON t.manzana = de.manzana AND t.lote = de.lote AND t.proyecto = de.id_proyecto 
                LEFT JOIN ordenes o ON de.orden_id = o.id
                INNER JOIN proyectos p ON t.proyecto = p.id";
                $res = $con->prepare($consulta);
                $res->execute();

                  while ($row = $res->fetch()) {
                        $data[] = $row;
                    }
                return $data;
            }else{
                return array();
            } 
        }


       
        //Funcion que emulara el get_result-----------------*
        function Arreglo_Get_Result( $Statement ) {
            $RESULT = array();
            $Statement->store_result();
            for ( $i = 0; $i < $Statement->num_rows; $i++ ) {
                $Metadata = $Statement->result_metadata();
                $PARAMS = array();
                while ( $Field = $Metadata->fetch_field() ) {
                    $PARAMS[] = &$RESULT[ $i ][ $Field->name ];
                }
                call_user_func_array( array( $Statement, 'bind_result' ), $PARAMS );
                $Statement->fetch();
            }
            return $RESULT;
        }

   ?>