
<?php

$controller_permiso->validarAcceso(1, CPermiso::VER_HORARIOS->value);
include "vistas/general/header.php";

?>
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
                        <div class="col-12 col-md-8">
                            <h1 class="h3 mb-3">Gestor de horarios </h1>
                        </div>
                        <div class="col-12 col-md-4 text-end">
                        <a href="<?php echo BASE_URL; ?>asignar_horario" class="ml-2" style="text-decoration: none; color:white;"> <div class="btn btn-info" >
                               Asignar horario</div></a>
                        <a href="<?php echo BASE_URL; ?>nuevo_horario" style="text-decoration: none; color:white;"> <div class="btn btn-success" >
                               Crear horario</div></a>
                        </div>
                    </div>



                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">En esta tabla estan los horarios actuales</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 col-md-12">
                                        <table id="horarios" class="table table-hover nowrap" style="width:100%">
                                        </table>
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
        include "vistas/general/scripts.php"
    ?>

    <script src="<?php echo STATIC_URL; ?>js/DataTable/datatables-init.js" type="module"></script>
    <script src="<?php echo STATIC_URL; ?>js/horarios/traer-lista-horarios.js" type="module"></script>
  

</body>

</html>