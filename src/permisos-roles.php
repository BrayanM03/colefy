
<?php

$controller_permiso->validarAcceso(1, CPermiso::EDITAR_ROLES->value);
  
require_once  __DIR__ . '/../controllers/RolController.php';
$controller_rol = new RolController();

/* $controller_permiso->verificarSesion();
$id_rol = $_SESSION['rol'];
$permiso_pdf = $controller_permiso->verificarPermiso($id_rol, 4);

if(!$permiso_pdf['estatus']){
    header("Location:../static/vistas/error/sin_permisos.php");
} */

$rol_resp = $controller_rol->obtener_rol(2, $_GET['id_rol']);
if($rol_resp['estatus']){
    $rol = $rol_resp['data'][0];
}else{
    //Mandar a NOT FOUND
}

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
                            <h1 class="h3 mb-3">Gestor de permisos de rol: <?= $rol['nombre'] ?></h1>
                        </div>
                        <div class="col-12 col-md-3 text-end">
                        <a href="<?php echo BASE_URL; ?>roles" style="text-decoration: none; color:white;"> <div class="btn btn-info">
                        <i class="fa-solid fa-left-long"></i> Atras</div></a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <b class="mb-0">En este panel puedes configurar los permisos existentes del rol.</b>
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
        include "vistas/general/scripts.php"
    ?>
    <script>
        const ID_ROL = <?php echo $_GET['id_rol']; ?>
    </script>
    <script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.8.11/dist/dotlottie-wc.js" type="module"></script>

    <script src="<?php echo STATIC_URL; ?>js/DataTable/datatables-init.js" type="module"></script>
    <script src="<?php echo STATIC_URL; ?>js/roles/roles-permisos.js" type="module"></script>
    <script>
    // Inicializar los iconos
    feather.replace();
</script>

</body>

</html>