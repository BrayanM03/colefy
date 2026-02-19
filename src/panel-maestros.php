<?php

require_once  __DIR__ . '/../controllers/ProfesorController.php';
require_once  __DIR__ . '/../config/dates.php';

$controller_permiso->verificarSesion();
$permiso_panel_maestros = $controller_permiso->validarAcceso(2, CPermiso::VER_PANEL_MAESTROS->value);
if($permiso_panel_maestros){
    $controller_prof = new ProfesorController();
    $resp_grupos = $controller_prof->obtenerGruposProfesor($_SESSION['id']);
};

$dates = new Date();

include "vistas/general/header.php";

?>

<body>
    <div class="wrapper">

        <?php
        include "vistas/general/sidebar.php"
        ?>
        <div class="main">
            <?php
            include "vistas/general/navbar.php"
            ?>

            <main class="content">
                <div class="container-fluid p-0">

                    <div class="row mb-2">
                        <div class="col-12 col-md-6">
                            <h1 class="h3 mb-3">Panel de información</h1>
                        </div>
                        <!-- <div class="col-12 col-md-6 text-end">
                            <a href="registrar.php"><div class="btn btn-success">Agregar nuevo</div></a>
                        </div> -->
                    </div>



                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                               <!--  <div class="card-header">
                                    <h5 class="card-title mb-0">Ciclo escolar actual</h5>
                                </div> -->
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 col-md-3">
                                            <h5 class="card-title mb-2">Ciclo escolar actual</h5>
                                            <h3 id="ciclo" id_ciclo="1">2025-2026</h3>
                                        </div>
                                        <div class="col-12 col-md-3">
                                            <h5 class="card-title mb-2">Fecha actual:</h5>
                                            <h3 id="ciclo" id_ciclo="1"><?php
                                             $fecha_hoy = date('Y-m-d');
                                             $fecha_ft = $dates->formatearFechaEspanol($fecha_hoy);
                                             echo $fecha_ft;
                                             ?></h3>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-12 mb-3">
                                            <label>Grupos asignados, puedes seleccionar uno</labe>
                                        </div>
                                        <?php
                                            foreach($resp_grupos['data'] as $element){
                                                print_r('
                                                 <div class="col-12 col-md-2">
                                                    <div onclick="setearTablaGrupo('. $element['id'] .')" class="tarjeta-grupo d-flex justify-content-center align-items-center">
                                                        <span>'. $element['nombre'] .'</span>
                                                    </div>
                                                </div>');
                                            }
                                        ?>
                                       
                                        
                                    </div>

                                    <div class="row mt-4">
                                        <div class="col-8">
                                        <div class="border p-4" style="border-radius:8px;">
                                                <label for="">Alumnos asignados</label></br>
                                                <div id="area-grupo">
                                                <span class="mensaje">Selecciona un grupo</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="border p-4" style="border-radius:8px;">
                                                <label for="">Detalles del alumno</label></br>
                                                <span class="mensaje">Selecciona un alumno</span>
                                            </div>
                                        </div>
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
    
    <?php
    include "vistas/general/scripts.php";
    ?>

    <!-- Mis scripts -->
    <script src="<?php echo STATIC_URL; ?>js/panel/panel-profesores.js"></script>
    <!-- <script src="js/usuarios/eliminar-usuario.js"></script> -->
    <script> 
 //alert('La resolución de pantalla que tienes en este momento es de: ' + screen.width + ' x ' + screen.height) 
 </script>
</body>

</html>