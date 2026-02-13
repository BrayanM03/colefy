
<?php

$controller_permiso->validarAcceso(1, CPermiso::VER_PERMISOS->value);
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
                        <div class="col-12 col-md-9">
                            <h1 class="h3 mb-3">Gestor de permisos </h1>
                        </div>
                        <div class="col-12 col-md-3 text-end">
                        <a href="nuevo-recibo.php" style="text-decoration: none; color:white;"> <div class="btn btn-success" >
                               Nuevo permiso</div></a>
                        </div>
                    </div>



                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <b class="mb-0">En este panel puedes configurar los permisos existentes.</b>
                                </div>
                                <div class="row justify-content-center">
                                    <div class="col-12 col-md-7">
                                    <div class="card-body" id="area-permisos">
                                    <div class="row">
                                        <div class="col-12 text-center">
                                            <dotlottie-wc
                                            class="m-auto"
                                            src="https://lottie.host/16abd1c5-90bb-4e18-b98b-a616ac4b71ff/Y552obLciD.lottie"
                                            style="width: 120px;height: 120px"
                                            autoplay
                                            loop
                                            ></dotlottie-wc>
                                        </div>
                                        
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
    <script
  src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.8.11/dist/dotlottie-wc.js"
  type="module"
></script>

    <script src="<?php echo STATIC_URL; ?>js/DataTable/datatables-init.js" type="module"></script>
    <script src="<?php echo STATIC_URL; ?>js/config/permisos.js" type="module"></script>
    <script>
    // Inicializar los iconos
    feather.replace();
</script>

</body>

</html>