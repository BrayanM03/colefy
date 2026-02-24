
<?php


$controller_permiso->validarAcceso(1, CPermiso::EDITAR_ESCUELAS->value);

require_once __DIR__ . '/../controllers/EscuelaController.php';
$controller_esc = new EscuelaController();
$resp_escuelas = $controller_esc->traer_escuela(2, $_GET['id_escuela']);
$data_escuelas = $resp_escuelas['data'];
if (!$resp_escuelas['estatus']) {
        header("Location: ".BASE_URL."not_found");
        exit;
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
    <div class="container-fluid p-0 container-fluid animate__animated animate__fadeIn animate__faster">
        <div class="row mb-3">
            <div class="col-12 col-md-9">
                <h1 class="h3 mb-3"><strong>Configuración/Edición</strong> de Escuela</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-xl-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title mb-0">Información General</h5>
                        <p class="text-muted small mb-0">Formulario para la edición de la institución.</p>
                    </div>
                    <div class="card-body">
                        <form action="tu_script_procesador.php" method="POST" enctype="multipart/form-data">
                            
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Nombre de la Institución</label>
                                    <input type="text" name="nombre" value="<?= $data_escuelas['nombre']?>" class="form-control form-control-lg" placeholder="Ej: Instituto Tecnológico Moderno" required maxlength="300">
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <label class="form-label fw-bold">Dirección</label>
                                    <input type="text" name="direccion" value="<?= $data_escuelas['direccion']?>" class="form-control" placeholder="Calle, Número, Colonia..." required maxlength="180">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Cédula / Registro</label>
                                    <input type="text"v alue="<?= $data_escuelas['cedula']?>" name="cedula" class="form-control" placeholder="Opcional" maxlength="50">
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Teléfono de contacto</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="align-middle" data-feather="phone"></i></span>
                                        <input type="tel" value="<?= $data_escuelas['telefono']?>"  name="telefono" class="form-control" placeholder="18 caracteres máx." required maxlength="18">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Estatus Inicial</label>
                                    <select name="estatus" class="form-select" required>
                                        <option value="1" selected>Activa</option>
                                        <option value="0">Inactiva / Pendiente</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Fecha de Registro</label>
                                    <input type="datetime-local" value="<?= $data_escuelas['fecha_registro']?>" name="fecha_registro" class="form-control" value="<?php echo date('Y-m-d\TH:i'); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Próxima Fecha de Pago</label>
                                    <input type="datetime-local" value="<?= $data_escuelas['fecha_pago']?>"  name="fecha_pago" class="form-control" required>
                                </div>
                            </div>

                            

                            <div class="d-flex justify-content-end gap-2 border-top pt-4">
                                <button type="button" class="btn btn-light px-4"><a href="<?= BASE_URL?>escuelas">Cancelar</a></button>
                                <button type="submit" class="btn btn-primary px-4">Guardar Institución</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-4">
            <div class="card mb-3"> 
                    <div class="card-header">
                        <h5 class="card-title mb-0">Logotipo de la escuela</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="position-relative d-inline-block">
                            <img src="<?= STATIC_URL ?>img/<?= !empty($data_escuelas['logo']) ? 'escuelas/'.$data_escuelas['logo'] : 'default.png' ?>" 
                                alt="Usuario" 
                                id="avatar-preview"
                                class="img-fluid rounded-circle mb-2" 
                                style="width: 150px; height: 150px; object-fit: cover; object-position: center; border: 3px solid #f1f1f1;" />
                                
                            <div id="avatar-loader" class="d-none position-absolute top-50 start-50 translate-middle">
                                <div class="lds-dual-ring-md" style="width: 5rem; height: 5rem;"></div>
                             </div>
                        </div> 

                        <h5 class="card-title mb-3"><?= $usuario['usuario'] ?? 'Sin logo' ?></h5>

                        <form id="form-avatar" action="<?= BASE_URL ?>api/usuarios.php?tipo=actualizar_foto_perfil" method="POST" enctype="multipart/form-data">
                            <input type="file" id="input-logo" name="avatar" style="display: none;" accept="image/*" id_escuela = "<?= $_GET['id_escuela']?>">
                            
                            <button type="button" class="btn btn-primary btn-sm" id="btn-cambiar-foto" onclick="document.getElementById('input-logo').click();">
                                <span data-feather="upload-cloud"></span> Cambiar Foto
                            </button>
                        </form>
                    </div>
                    <hr class="my-0" />
                    <!-- <div class="card-body">
                        <h5 class="h6 card-title">Sobre mí</h5>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-1"><span data-feather="home" class="feather-sm me-1"></span> Colegio: <a href="#"><?= $usuario['escuela']?></a></li>
                            <li class="mb-1"><span data-feather="briefcase" class="feather-sm me-1"></span> Cargo: <?= $usuario['cargo']?></li>
                        </ul>
                    </div> -->
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


    <script src="<?php echo STATIC_URL; ?>./js/bootstrap-select.min.js"></script>
    <script src="<?php echo STATIC_URL; ?>js/DataTable/datatables-init.js" type="module"></script>
    <script src="<?php echo STATIC_URL; ?>js/escuelas/editar-escuela.js" type="module"></script>
  

</body>

</html>