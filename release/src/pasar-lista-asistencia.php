
<?php
session_start();

if (empty($_SESSION["id"])) {
    if($_SESSION["rol"] !== "manager"){

        header("Location:not-found.php");
    }
}
include "../servidor/database/conexion.php";
function formatoFechaEspañol($fecha) {
    // Configurar el arreglo de meses en español
    $meses = [
        1 => 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    ];

    // Crear un objeto DateTime
    $fechaObjeto = new DateTime($fecha);

    // Obtener el día, mes y año
    $dia = $fechaObjeto->format('d');
    $mes = $meses[(int)$fechaObjeto->format('m')];
    $año = $fechaObjeto->format('Y');

    // Retornar la fecha en el formato deseado
    return "$dia de $mes de $año";
}
$fecha_fort = formatoFechaEspañol($_GET['fecha']);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Responsive Admin &amp; Dashboard Template based on Bootstrap 5">
    <meta name="author" content="AdminKit">
    <meta name="keywords" content="adminkit, bootstrap, bootstrap 5, admin, dashboard, template, responsive, css, sass, html, theme, front-end, ui kit, web">

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="shortcut icon" href="img/icons/icon-48x48.png" />

    <link rel="canonical" href="https://demo-basic.adminkit.io/pages-blank.html" />

    <title>Asistencia | IIBGV manager</title>

    <link href="css/app.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.dataTables.min.css" />
    <style>
        .asistencia_ok {
            background-color: #ccfbc4;
        }

        .falta_ok{
            background-color:#ffbaad;
        }

        .retardo_ok{
            background-color:#ffe3ad ;
        }
    </style>
</head>

<body>
    <div class="wrapper">

        <?php
        include "vistas/general/sidebar.php"
        ?>
        <div class="main">
            <?php
            include "vistas/general/navbar.php";

            
            date_default_timezone_set('America/Matamoros');

            $id_grupo = $_GET['id_grupo'];
            $fecha = $_GET['fecha'];
            $id_profesor = $_SESSION['id'];
            $id_materia = $_GET['id_materia'];
    
            $count = "SELECT count(*) FROM usuarios u WHERE u.id_grupo = ? AND u.estatus = 1";
            $res = $con->prepare($count);
            $res->execute([$id_grupo]);
            $total_ordenes = $res->fetchColumn();
            $res->closeCursor(); 

        if($total_ordenes>0){
            $consultar = $con->prepare("SELECT m.nombre as materia, g.nombre as grupo, dh.hora FROM detalle_horario dh
            INNER JOIN materias m ON dh.id_materia = m.id 
            INNER JOIN grupos g ON dh.id_grupo = g.id  WHERE dh.id_profesor = ? AND dh.id_grupo = ?");
            $consultar->execute([$id_profesor, $id_grupo]);
            while ($row = $consultar->fetch()) {
                $data_general = $row;
            }

            $consultar = $con->prepare("SELECT u.*, COALESCE(a.asistencia, '') AS asistencia FROM usuarios u 
            LEFT JOIN asistencias a ON a.id_alumno = u.id AND a.id_materia = ? AND a.fecha = ?
            WHERE u.id_grupo = ? AND u.estatus = 1 ORDER BY u.nombre ASC");
            $consultar->execute([$id_materia, $fecha, $id_grupo]);
            while ($row = $consultar->fetch()) {
                $data[] = $row;
            }
        
   }else{
        $data_general=[];
        $data_general['grupo'] = 'No especificado';
        $data=[];
        
   }

            ?>

            <main class="content">
                <div class="container-fluid p-0">

                    <div class="row mb-2">
                        <div class="col-12 col-md-9">
                            <h1 class="h3 mb-3">Gestión de asistencia del grupo: <?= $data_general['grupo']?> -
                                Fecha: <?= $fecha_fort ?>
                            </h1>
                        </div>
                    </div>



                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3><?= $data_general['materia']?></h3>
                                    <h5 class="card-title mb-0">En esta tabla estan los alumnos actuales del grupo,
                                        selecciona su asistencia, su falta o su retardo.
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row justify-content-center ">
                                        <div class="col-12 col-md-8">
                                        <ul class="list-group">
                                        <li class="list-group-item active">
                                            <div class="row">
                                                <div class="col-3">
                                                    Nombre
                                                </div>
                                                <div class="col-5">
                                                    Apellido
                                                </div>
                                                <div class="col-4">
                                                    Asistencia
                                                </div>
                                            </div>
                                        </li>
                                        <?php 
                                        foreach ($data as $key => $value) {
                                           if($value['asistencia']==1){
                                            $asistencia_tipo ='asistencia_ok';
                                           }else if($value['asistencia']==2)
                                           {
                                            $asistencia_tipo ='falta_ok';
                                           }else if($value['asistencia']==3)
                                           {
                                            $asistencia_tipo ='retardo_ok';
                                           }else{
                                            $asistencia_tipo ='';
                                           }
                                           
                                           ?>
                                           <li class="list-group-item <?=$asistencia_tipo ?>">
                                           <div class="row">
                                               <div class="col-3">
                                                  <?=$value['nombre']?>
                                               </div>
                                               <div class="col-5">
                                                <?= $value['apellido']?>
                                               </div>
                                               <div class="col-4">
                                                <div class="row">
                                                    <?php
                                                        if($asistencia_tipo==''){
                                                    ?>
                                                    <div class="col-md-12"><div class="btn btn-success" onclick="tomarAsistencia(1, <?= $value['id']?>,<?=$id_materia?>, `<?= $fecha?>`, <?=$id_grupo?>,0)">Asistencia</div>
                                                   <div onclick="tomarAsistencia(2, <?= $value['id']?>, <?=$id_materia?>, `<?=$fecha?>`, <?=$id_grupo?>,0)" class="btn btn-danger">Falta</div>
                                                    <div onclick="tomarAsistencia(3, <?=$value['id']?>, <?=$id_materia?>, `<?=$fecha?>`, <?=$id_grupo?>,0)" class="btn btn-warning">Retardo</div>
                                                    </div>
                                                    <?php }else{
                                                        ?>
                                                         <div class="col-md-12">
                                                            <span class="">
                                                                <div class="row">
                                                                    <div class="col-8">
                                                                    <?php
                                                                switch ($value['asistencia']) {
                                                                    case 1:
                                                                        ?>Asistencia<?php
                                                                        break;
                                                                        case 2:
                                                                            ?>Falta<?php
                                                                            break;
                                                                            case 3:
                                                                                ?>Retardo<?php
                                                                                break;
                                                                    default:
                                                                        # code...
                                                                        break;
                                                                }
                                                            ?>
                                                                    </div>
                                                                    <div class="col-4">
                                                                    <div onclick="tomarAsistencia(0, <?=$value['id']?>, <?=$id_materia?>, `<?=$fecha?>`, <?=$id_grupo?>, 1)" class="btn btn-primary"><i class="fas fa-undo-alt"></i></div>
                                                                    </div>
                                                                </div>
                                                                 </span>
                                                         </div>
                                                            <?php
                                                    } ?>
                                                </div>
                                               </div>
                                           </div>
                                       </li><?php
                                        }
                                        ?>
                                    </ul>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </main>

            <?php
        include "vistas/general/footer.php"
        ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="js/app.js"></script>

    <!-- Librerias -->
    <script src="https://kit.fontawesome.com/5c955c6e98.js" crossorigin="anonymous"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>
    <script src="js/asistencia/cargar-informacion.js"></script>
   

</body>

</html>