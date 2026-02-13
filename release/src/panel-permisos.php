<?php
$controller_permiso->validarAcceso(1, CPermiso::VER_PANEL_PERMISOS->value);
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
                            <h1 class="h3 mb-3">Lista de permisos</h1>
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
                    <div class="col-12">
                        <div class="row">
                            
                            <div class="col-12 col-md-4 mb-3">
                                <div class="card text-center h-100">
                                    <div class="card-body">
                                        <i class="fas fa-list-ul fa-3x text-primary mb-3"></i> 
                                        <h5 class="card-title">Lista de Permisos</h5>
                                        <p class="card-text">Ver todos los permisos definidos en el sistema.</p>
                                        <a href="permisos" class="btn btn-sm btn-outline-primary">Ver</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-4 mb-3">
                                <div class="card text-center h-100">
                                    <div class="card-body">
                                        <i class="fas fa-user-shield fa-3x text-success mb-3"></i> 
                                        <h5 class="card-title">Permisos por Usuarios</h5>
                                        <p class="card-text">Asignar o modificar permisos a usuarios individuales.</p>
                                        <a href="permisos_usuarios" class="btn btn-sm btn-outline-success">Gestionar</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-4 mb-3">
                                <div class="card text-center h-100">
                                    <div class="card-body">
                                        <i class="fas fa-building fa-3x text-info mb-3"></i> 
                                        <h5 class="card-title">roles</h5>
                                        <p class="card-text">Definir y aplicar reglas de permisos para roles/departamentos.</p>
                                        <a href="roles" class="btn btn-sm btn-outline-info">Configurar</a>
                                    </div>
                                </div>
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
    <!-- Mis scripts -->
   
</body>

</html>