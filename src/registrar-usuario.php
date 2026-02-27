<?php
$controller_permiso->validarAcceso(1, CPermiso::CREAR_USUARIOS->value);
include "vistas/general/header.php";

require_once  __DIR__ . '/../controllers/EscuelaController.php';
require_once  __DIR__ . '/../controllers/RolController.php';

$controller_escuela = new EscuelaController;
$controller_rol = new RolController;

$resp_combo_escuelas = $controller_escuela->combo(2);
$resp_combo_roles = $controller_rol->combo(2);
$data_escuelas = $resp_combo_escuelas['data'];
$data_roles = $resp_combo_roles['data'];

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
                <div class="container-fluid p-0 animate__animated animate__fadeIn animate__faster">

                    <div class="row mb-2">
                        <div class="col-12 col-md-6">
                            <h1 class="h3 mb-3">Registrar usuario</h1>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Completa este formulario para el registro del nuevo usuario</h5>
                                </div>
                                <div class="card-body">
                                    <form id="form-registro-usuario" enctype="multipart/form-data" autocomplete="off">
    <div class="row">
        <div class="col-md-4 text-center border-end">
            <div class="mb-3">
                <label class="form-label d-block">Foto de Perfil</label>
                <div class="position-relative d-inline-block">
                    <img id="avatar-preview" src="static/img/avatars/default.jpg" 
                         class="img-thumbnail rounded-circle mb-2" 
                         style="width: 150px; height: 150px; object-fit: cover; opacity: 1;">
                    <div id="avatar-loader" class="spinner-border text-primary position-absolute d-none" 
                         style="top: 40%; left: 40%;" role="status"></div>
                </div>
                <input type="file" class="form-control form-control-sm mt-2" id="input-avatar" name="avatar" accept="image/*">
                <small class="text-muted d-block mt-1">Máximo 2MB (JPG, PNG, WEBP)</small>
            </div>
        </div>

        <div class="col-md-8">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nombre(s)</label>
                    <input type="text" class="form-control" name="nombre" placeholder="Ej. Juan" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Apellidos</label>
                    <input type="text" class="form-control" name="apellidos" placeholder="Ej. Pérez García" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Asignar a Escuela</label><br>
                    <select class="selectpicker w-100" name="id_escuela" required data-live-search="true">
                        <option value="" selected disabled>Selecciona una escuela...</option>
                        <?php foreach($data_escuelas as $esc): ?>
                            <option value="<?= $esc['id'] ?>"><?= $esc['nombre'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Rol de Usuario</label>
                    <select class="selectpicker w-100" name="rol" required data-live-search="true">
                        <option value="" selected disabled>Selecciona un rol...</option>
                        <?php foreach($data_roles as $rol): ?>
                            <option value="<?= $rol['id'] ?>"><?= $rol['nombre'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Cargo</label>
                    <input type="text" class="form-control" name="cargo" placeholder="Docente" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Correo</label>
                    <input type="text" class="form-control" name="correo" placeholder="alguien@compañia.com" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Telefono</label>
                    <input type="text" class="form-control" name="telefono" placeholder="Telefono" required>
                </div>
                <hr>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nombre de Usuario</label>
                    <input type="text" class="form-control" name="usuario" placeholder="juan.perez" required autocomplete="one-time-code">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Contraseña Temporal</label>
                    <input type="password" class="form-control" name="password" required autocomplete="new-password">
                </div>
            </div>
        </div>
    </div>

    <hr>
    
    <div class="text-end">
        <a href="<?= BASE_URL?>usuarios"><button type="button" class="btn btn-secondary me-2">Cancelar</button></a>
        <button type="submit" id="btn-registrar-usuario" class="btn btn-primary">
            <i class="align-middle me-1" data-feather="save"></i> Registrar Usuario
        </button>
    </div>
</form>
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
    <script src="<?php echo STATIC_URL; ?>js/usuarios/registrar.js" type="module"></script>

</body>

</html>