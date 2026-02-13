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


$tipo_corte = $_GET["tipo_corte"];
$proyecto = $_GET['proyecto'];
if($proyecto=='all'){
    $all = true;
}else{
    $all = false;
}

if($tipo_corte == "normal"){
    $fecha_corte = $_GET["fecha"];
    $arreglo =traerAbonos($con, $fecha_corte, $all);
}else{
    $fecha_inicial = $_GET["fecha_inicial"];
    $fecha_final = $_GET["fecha_final"];

    $arreglo =traerRangoAbonos($con, $fecha_inicial, $fecha_final, $all);
}
    

        //Creamos objeto de hoja de excel
        $spreadsheet = new SpreadSheet();
        $spreadsheet->getProperties()->setCreator($nombre_del_usuario)->setTitle("Primer excel");

        //ITERACIONES
      
            
            //Esablecemos y obtenemos la primera hoja activa -- 

            $spreadsheet->createSheet();
            
            $spreadsheet->setActiveSheetIndex(0);
            $hoja_activa = $spreadsheet->getActiveSheet();
            if($tipo_corte == "normal"){

                $hoja_activa->setTitle("Corte " . $fecha_corte);
            }else{
                $hoja_activa->setTitle($fecha_inicial . " al " . $fecha_final);

            }

            //$categoria = 'computadorascat';

           
          
         
            
            $cantidad_resultado = count($arreglo);

             //Establecemos cabezera del reporte
           //Combinar y centrar
           $hoja_activa->mergeCells("A1:B1");
           $hoja_activa->mergeCells("C1:H1");
            if($tipo_corte == "normal") {
                $hoja_activa->setCellValue('C1', 'Reporte de corte: '. $fecha_corte);
            }else{
                $hoja_activa->setCellValue('C1', 'Reporte de corte: '. $fecha_inicial . " al " . $fecha_final);
            }
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
        
        

           //Validamos si se encontraron registros en la tabla, se valida 
           if ($cantidad_resultado == 0) {

            /*  $drawing->getShadow()->setVisible(true);
           $drawing->getShadow()->setDirection(45); */
           $hoja_activa->setAutoFilter('A2:G2');
          // $autofilter = $hoja_activa->getAutofilter();
           

          $hoja_activa->mergeCells("A2:N2");
          $hoja_activa->getStyle('A2:N2')->getAlignment()->setHorizontal('center');
          $hoja_activa->getStyle('A2:N2')->getAlignment()->setVertical('center');
          $hoja_activa->setCellValue('A2', 'Reporte de corte del ' . $fecha);

          $hoja_activa->getColumnDimension('A')->setWidth(5);
          $hoja_activa->setCellValue('A3', '#');
          $hoja_activa->getColumnDimension('B')->setWidth(25);
          $hoja_activa->setCellValue('B3', 'Cliente');
          $hoja_activa->getColumnDimension('C')->setWidth(25);
          $hoja_activa->setCellValue('C3', 'Numero');
          $hoja_activa->getColumnDimension('D')->setWidth(10);
          $hoja_activa->setCellValue('D3', 'Proyecto');
       //   $columnFilter = $autofilter->getColumn('D');
          $hoja_activa->getColumnDimension('E')->setWidth(10);
          $hoja_activa->setCellValue('E3', 'Manzana');
          $hoja_activa->getColumnDimension('F')->setWidth(10);
          $hoja_activa->setCellValue('F3', 'Lote');
          $hoja_activa->getColumnDimension('G')->setWidth(25);
          $hoja_activa->setCellValue('G3', 'Mes');
          $hoja_activa->getColumnDimension('H')->setWidth(25);
          $hoja_activa->setCellValue('H3', 'Año');
          $hoja_activa->getColumnDimension('I')->setWidth(25);
          $hoja_activa->setCellValue('I3', 'Fecha');     
          $hoja_activa->getColumnDimension('J')->setWidth(15);
          $hoja_activa->setCellValue('J3', 'Hora');     
          $hoja_activa->getColumnDimension('K')->setWidth(15);
          $hoja_activa->setCellValue('K3', 'Abonado');
          $hoja_activa->getColumnDimension('L')->setWidth(15);
          $hoja_activa->setCellValue('L3', 'Penalización');
          $hoja_activa->getColumnDimension('M')->setWidth(15);
          $hoja_activa->setCellValue('M3', 'Numero abono');
          $hoja_activa->getColumnDimension('N')->setWidth(15);
          $hoja_activa->setCellValue('N3', 'Tipo abono');
          $hoja_activa->getStyle('A3:N3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('007bcc');
          $hoja_activa->getStyle('A3:N3')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
          $hoja_activa->getStyle('A3:N3')->getFont()->setBold(true);
          $hoja_activa->getRowDimension('3')->setRowHeight(20);

            $hoja_activa->mergeCells("A4:H4");
            $hoja_activa->setCellValue('A4', 'Sin datos por el momento');
           
            
           
            $index = 2;
            /* $count++; */ 
           
        
       }else{

          

        

          /*  $drawing->getShadow()->setVisible(true);
           $drawing->getShadow()->setDirection(45); */
           $hoja_activa->setAutoFilter('A2:G2');
          // $autofilter = $hoja_activa->getAutofilter();
           

          $hoja_activa->mergeCells("A2:N2");
          $hoja_activa->getStyle('A2:N2')->getAlignment()->setHorizontal('center');
          $hoja_activa->getStyle('A2:N2')->getAlignment()->setVertical('center');
          $hoja_activa->setCellValue('A2', 'Ingresos');

          $hoja_activa->getColumnDimension('A')->setWidth(5);
          $hoja_activa->setCellValue('A3', '#');
          $hoja_activa->getColumnDimension('B')->setWidth(25);
          $hoja_activa->setCellValue('B3', 'Cliente');
          $hoja_activa->getColumnDimension('C')->setWidth(25);
          $hoja_activa->setCellValue('C3', 'Numero');
          $hoja_activa->getColumnDimension('D')->setWidth(10);
          $hoja_activa->setCellValue('D3', 'Proyecto');
       //   $columnFilter = $autofilter->getColumn('D');
          $hoja_activa->getColumnDimension('E')->setWidth(10);
          $hoja_activa->setCellValue('E3', 'Manzana');
          $hoja_activa->getColumnDimension('F')->setWidth(10);
          $hoja_activa->setCellValue('F3', 'Lote');
          $hoja_activa->getColumnDimension('G')->setWidth(25);
          $hoja_activa->setCellValue('G3', 'Mes');
          $hoja_activa->getColumnDimension('H')->setWidth(25);
          $hoja_activa->setCellValue('H3', 'Año');
          $hoja_activa->getColumnDimension('I')->setWidth(25);
          $hoja_activa->setCellValue('I3', 'Fecha');     
          $hoja_activa->getColumnDimension('J')->setWidth(15);
          $hoja_activa->setCellValue('J3', 'Hora');     
          $hoja_activa->getColumnDimension('K')->setWidth(15);
          $hoja_activa->setCellValue('K3', 'Abonado');
          $hoja_activa->getColumnDimension('L')->setWidth(15);
          $hoja_activa->setCellValue('L3', 'Penalización');
          $hoja_activa->getColumnDimension('M')->setWidth(15);
          $hoja_activa->setCellValue('M3', 'Numero abono');
          $hoja_activa->getColumnDimension('N')->setWidth(15);
          $hoja_activa->setCellValue('N3', 'Tipo abono');
          $hoja_activa->getStyle('A3:N3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('007bcc');
          $hoja_activa->getStyle('A3:N3')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
          $hoja_activa->getStyle('A3:N3')->getFont()->setBold(true);
          $hoja_activa->getRowDimension('3')->setRowHeight(20);
         
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
           
           //Recorremos el arreglo
           $monto_abonado_total = 0;
           while ($row = array_shift($arreglo)) {
               # trabajos con los datos
          
               $id= $row["id"];
               $orden_id= $row["orden_id"];
               $detalle_id = $row["detalle_id"];
               $monto = $row["total"];
               $fecha = $row["fecha"];
               $mes = $row["mes"];
               $anio = $row["year"];
               $tipo_abono = $row["tipo"];
               $penalizacion = floatval($row["penalizacion_monto"]);
               $hora = $row["hora"];
               $no_abono = $row["no_abono"];
               $datos_de_detalle = traerDetalles($con, $detalle_id);

               if($datos_de_detalle > 0) {
                while ($fi = array_shift($datos_de_detalle)) {

                    $proyecto= $fi["proyecto"];
                    $manzana= $fi["manzana"];
                    $lote= $fi["lote"];

                    $datos_de_ordenes = traerDatosOrdenes($con, $orden_id);

               $total_ordenes = count($datos_de_ordenes);
               /* print_r("total_ordenes " . $total_ordenes); */
               if($total_ordenes > 0){
               
                    while ($row2 = array_shift($datos_de_ordenes)) {
                        $cliente_etiqueta = $row2['cliente_etiqueta'];
                        $telefono = $row2["telefono"];
                        $index = 1;
                       
                            $index = $fila + 1;
                            $indicador = $fila - 1;
                            $hoja_activa->setCellValue('A' . $index, $id);
                            $hoja_activa->setCellValue('B' . $index, $cliente_etiqueta);
                            $hoja_activa->setCellValue('C' . $index, $telefono);
                            $hoja_activa->setCellValue('D' . $index, $proyecto);
                            $hoja_activa->setCellValue('E' . $index, $manzana);
                            $hoja_activa->setCellValue('F' . $index, $lote);
                            $hoja_activa->setCellValue('G' . $index, $mes);
                            $hoja_activa->setCellValue('H' . $index, $anio);
                            $hoja_activa->setCellValue('I' . $index, $fecha);
                            $hoja_activa->setCellValue('J' . $index, $hora);
                            $monto_dado = number_format($monto, 2, '.', ',');
                            $penalizacion = number_format($penalizacion, 2, '.', ',');
                            $hoja_activa->setCellValue('K' . $index, "$".$monto_dado);
                            $hoja_activa->setCellValue('L' . $index, "$".$penalizacion);
                            $hoja_activa->setCellValue('M' . $index, $no_abono);
                            $hoja_activa->setCellValue('N' . $index, $tipo_abono);
                            $hoja_activa->getStyle('A' .$index. ':N' .$index)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('6495ed'));
                            $fila++;

                            $monto_abonado_total += floatval($monto);
                        
                    }
               }else{

               }
                
               $index++;


                }
               }
               

               /* if ($index % 2 == 0) {
                   $hoja_activa->getStyle('A' .$index. ':J' .$index)->applyFromArray($evenRow);
               }else{
                   $hoja_activa->getStyle('A' .$index. ':J' .$index)->applyFromArray($oddRow);    
               } */

              
           }

           

            
           $num = $monto_abonado_total;
            $moneda = number_format($num, 2, '.', ',');
          
           $hoja_activa->setCellValue('M' . $index + 3, "Total:");
           $hoja_activa->setCellValue('N' . $index + 3, "$". $moneda);


           
       }
          
           $count++;
        

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        if($tipo_corte == "normal"){

            header('Content-Disposition: attachment;filename="Reporte de '.$fecha_corte .'.xlsx"');
        }else{
            header('Content-Disposition: attachment;filename="Reporte de '.$fecha_inicial.'-'. $fecha_final .'.xlsx"');

        }
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        
        $writer->save('php://output');

    
        //Funcion que traera los datos de la base de datos
        function traerAbonos($con, $fecha, $all = false){
            $proyecto = $_GET['proyecto'];
            $consulta = "SELECT COUNT(*) FROM abonos a INNER JOIN detalle_orden dor ON dor.id = a.detalle_id WHERE a.fecha = ?";
            if($all){
                $query = '';
                $consulta .= $query;
                $res = $con->prepare($consulta);
                $res->execute([$fecha]);
            }else{
                $query = ' AND dor.id_proyecto = ?';
                $consulta .= $query;
                $res = $con->prepare($consulta);
                $res->execute([$fecha, $proyecto]);
            }
            
            $total = $res->fetchColumn();
           
            if($total > 0){
                $consulta = "SELECT * FROM abonos a INNER JOIN detalle_orden dor ON dor.id = a.detalle_id WHERE a.fecha = ?";
                if($all){
                    $query = '';
                    $consulta .= $query;
                    $res = $con->prepare($consulta);
                    $res->execute([$fecha]);
                }else{
                    $query = ' AND dor.id_proyecto = ?';
                    $consulta .= $query;
                    $res = $con->prepare($consulta);
                    $res->execute([$fecha, $proyecto]);
                }

                  while ($row = $res->fetch()) {
                        $data[] = $row;

                    }
                return $data;
            }else{
                return array();
            }
            
        }

        function traerRangoAbonos($con, $fecha_inicial, $fecha_final, $all=false){
           
            $proyecto = $_GET['proyecto'];
            $consulta = "SELECT COUNT(*) FROM abonos a INNER JOIN detalle_orden dor ON dor.id = a.detalle_id WHERE (fecha BETWEEN ? AND ?)";
            if($all){
                $query= '';
                $consulta .= $query;
                $res = $con->prepare($consulta);
            $res->execute([$fecha_inicial, $fecha_final]);
            }else{
                $query= ' AND dor.id_proyecto = ?';
                $consulta .= $query;
                $res = $con->prepare($consulta);
               
                $res->execute([$fecha_inicial, $fecha_final, $proyecto]);
            }
            
            $total = $res->fetchColumn();

            if($total > 0){
                $consulta = "SELECT a.* FROM abonos a INNER JOIN detalle_orden dor ON dor.id = a.detalle_id WHERE (fecha BETWEEN ? AND ?)";
                
                if($all){
                    $query= '';
                    $consulta .= $query;
                    $res = $con->prepare($consulta);
                $res->execute([$fecha_inicial, $fecha_final]);
                }else{
                    $query= ' AND dor.id_proyecto = ?';
                    $consulta .= $query;
             
                    $res = $con->prepare($consulta);
                    $res->execute([$fecha_inicial, $fecha_final, $proyecto]);
                }

                  while ($row = $res->fetch()) {
                        $data[] = $row;

                    }
                return $data;
            }else{
                return array();
            }
        }

        function traerDatosOrdenes($con, $orden_id){
            $consulta = "SELECT COUNT(*) FROM ordenes WHERE id = ?";
            $res = $con->prepare($consulta);
            $res->execute([$orden_id]);
            $total = $res->fetchColumn();

            if($total > 0){
                $consulta = "SELECT * FROM ordenes WHERE id = ?";
                $res = $con->prepare($consulta);
                $res->execute([$orden_id]);

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

        function traerDetalles($con, $detalle_id){
           
            $consulta = "SELECT COUNT(*) FROM detalle_orden WHERE id = ?";
            $res = $con->prepare($consulta);
            $res->execute([$detalle_id]);
            $total = $res->fetchColumn();

            if($total > 0){
                $consulta = "SELECT * FROM detalle_orden WHERE id = ?";
                $res = $con->prepare($consulta);
                $res->execute([$detalle_id]);

                  while ($row = $res->fetch()) {
                        $data[] = $row;

                    }
                return $data;
            }else{
                return array();
            }
            
        }

   ?>