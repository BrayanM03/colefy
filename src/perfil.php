<?php
require_once  __DIR__ . '/../controllers/UsuarioController.php';

$controller_permiso->validarAcceso(2, CPermiso::VER_PERFIL->value);
$controller_usuario = new UsuarioController;
$usuario_resp = $controller_usuario->obtener_usuario(2, $_SESSION['id']);
$usuario = $usuario_resp['data'];
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

        <div class="mb-3">
            <h1 class="h3 d-inline align-middle">Perfil de Usuario</h1>
        </div>

        <div class="row">
            <div class="col-md-4 col-xl-3">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Detalles del Perfil</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="position-relative d-inline-block">
                            <img src="<?= STATIC_URL ?>img/avatars/<?= !empty($usuario['foto_perfil']) ? $usuario['foto_perfil'] : 'default.png' ?>" 
                                alt="Usuario" 
                                id="avatar-preview"
                                class="img-fluid rounded-circle mb-2" 
                                style="width: 150px; height: 150px; object-fit: cover; object-position: center; border: 3px solid #f1f1f1;" />
                                
                            <div id="avatar-loader" class="d-none position-absolute top-50 start-50 translate-middle">
                                <div class="lds-dual-ring-md" style="width: 5rem; height: 5rem;"></div>
                             </div>
                        </div>  

                        <h5 class="card-title mb-0"><?= $usuario['usuario'] ?? 'Nombre de Usuario' ?></h5>
                        <div class="text-muted mb-2">Rol: <span style="color:cadetblue"> <?= $usuario['nombre_rol'] ?? 'Sin Rol' ?></span></div>

                        <form id="form-avatar" action="<?= BASE_URL ?>api/usuarios.php?tipo=actualizar_foto_perfil" method="POST" enctype="multipart/form-data">
                            <input type="file" id="input-avatar" name="avatar" style="display: none;" accept="image/*">
                            
                            <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('input-avatar').click();">
                                <span data-feather="upload-cloud"></span> Cambiar Foto
                            </button>
                        </form>
                    </div>
                    <hr class="my-0" />
                    <div class="card-body">
                        <h5 class="h6 card-title">Sobre mí</h5>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-1"><span data-feather="home" class="feather-sm me-1"></span> Colegio: <a href="#"><?= $usuario['escuela']?></a></li>
                            <li class="mb-1"><span data-feather="briefcase" class="feather-sm me-1"></span> Cargo: <?= $usuario['cargo']?></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-8 col-xl-9">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Configuración de la Cuenta</h5>
                    </div>
                    <div class="card-body">
                        <div>
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label class="form-label" for="inputFirstName">Nombre(s)</label>
                                    <input type="text" class="form-control" id="nombre" value="<?= $usuario['nombre']?>" placeholder="Nombre">
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label class="form-label" for="inputLastName">Apellidos</label>
                                    <input type="text" class="form-control" value="<?= $usuario['apellido']?>" id="apellido" placeholder="Apellidos">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="inputEmail4">Correo Electrónico</label>
                                <input type="email" class="form-control"  value="<?= $usuario['correo']?>" id="correo" placeholder="Email">
                            </div>
                            <div class="btn btn-primary" id="btn-guardar-datos">Guardar Cambios</div>

                            <hr />
                            
                            <h5 class="h6 card-title">Seguridad</h5>
                            <div class="row">
                                <div class="mb-3 col-md-4">
                                    <label class="form-label" for="pass_actual">Contraseña Actual</label>
                                    <div class="input-group">
                                        <input type="password" placeholder="Actual" class="form-control" id="pass_actual">
                                        <button class="btn btn-outline-secondary toggle-password" type="button">
                                            <i class="align-middle" data-feather="eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label class="form-label" for="pass_nueva">Nueva Contraseña</label>
                                    <div class="input-group">
                                        <input type="password" placeholder="Nueva" class="form-control" id="pass_nueva">
                                        <button class="btn btn-outline-secondary toggle-password" type="button">
                                            <i class="align-middle" data-feather="eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-3 col-md-4">
                                    <label class="form-label" for="pass_repite">Repite la contraseña</label>
                                    <div class="input-group">
                                        <input type="password" placeholder="Repite nueva" class="form-control" id="pass_repite">
                                        <button class="btn btn-outline-secondary toggle-password" type="button">
                                            <i class="align-middle" data-feather="eye"></i>
                                        </button>
                                        <div class="invalid-feedback">
                                            Las contraseñas no coinciden.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-info disabled" id="btn-cambiar-pass">
                            <span id="btn-text">Guardar cambios</span>
                                <span id="btn-spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            </button>
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
    <script src="<?php echo STATIC_URL; ?>js/usuarios/perfil.js" type="module"></script>
    <!-- <script src="js/usuarios/eliminar-usuario.js"></script> -->
    <script> 
 //alert('La resolución de pantalla que tienes en este momento es de: ' + screen.width + ' x ' + screen.height) 
 </script>
</body>

</html>