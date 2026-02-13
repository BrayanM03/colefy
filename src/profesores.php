<?php
$controller_permiso->validarAcceso(1, CPermiso::VER_PROFESORES->value);
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
                        <div class="col-12 col-md-6">
                            <h1 class="h3 mb-3">Lista de profesores</h1>
                        </div>
                        <!-- <div class="col-12 col-md-6 text-end">
                            <a href="registrar.php"><div class="btn btn-success">Agregar nuevo</div></a>
                        </div> -->
                    </div>



                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">En esta tabla estan los profesores activos actualmente</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 col-md-12">
                                            <table id="example" class="table table-hover nowrap" style="width:100%">
                                            <!-- <thead>
                                                <tr>
                                                    <th>Subscriber ID</th>
                                                    <th>Install Location</th>
                                                    <th>Subscriber Name</th>
                                                    <th>some data</th>
                                                </tr>
                                            </thead> -->
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
    <!-- Mis scripts -->
    <script src="<?php echo STATIC_URL; ?>js/DataTable/datatables-init.js" type="module"></script>
    <script src="<?php echo STATIC_URL; ?>js/profesores/traer-lista-profesores.js" type="module"></script>
    <script src="<?php echo STATIC_URL; ?>js/solicitudes/editar-solicitud.js"></script>
    <!-- <script src="js/usuarios/eliminar-usuario.js"></script> -->

</body>

</html>