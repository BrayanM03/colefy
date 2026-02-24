
<?php


$controller_permiso->validarAcceso(1, CPermiso::CREAR_ESCUELAS->value);

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
                <h1 class="h3 mb-3"><strong>Configuración</strong> de Escuela</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-xl-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="card-title mb-0">Información General</h5>
                        <p class="text-muted small mb-0">Complete los datos obligatorios para registrar la institución.</p>
                    </div>
                    <div class="card-body">
                        <form action="tu_script_procesador.php" method="POST" enctype="multipart/form-data">
                            
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Nombre de la Institución</label>
                                    <input type="text" name="nombre" class="form-control form-control-lg" placeholder="Ej: Instituto Tecnológico Moderno" required maxlength="300">
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <label class="form-label fw-bold">Dirección</label>
                                    <input type="text" name="direccion" class="form-control" placeholder="Calle, Número, Colonia..." required maxlength="180">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold">Cédula / Registro</label>
                                    <input type="text" name="cedula" class="form-control" placeholder="Opcional" maxlength="50">
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Teléfono de contacto</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class="align-middle" data-feather="phone"></i></span>
                                        <input type="tel" name="telefono" class="form-control" placeholder="18 caracteres máx." required maxlength="18">
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
                                    <input type="datetime-local" name="fecha_registro" class="form-control" value="<?php echo date('Y-m-d\TH:i'); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Próxima Fecha de Pago</label>
                                    <input type="datetime-local" name="fecha_pago" class="form-control" required>
                                </div>
                            </div>

                            <div class="row mb-4">
                                <div class="col-12">
                                    <label class="form-label fw-bold">Logo de la Escuela</label>
                                    <div class="border rounded-3 p-4 text-center bg-light">
                                        <i class="mb-2 text-muted" data-feather="upload-cloud" style="width: 40px; height: 40px;"></i>
                                        <input class="form-control mt-2" type="file" name="logo" id="formFile" accept="image/*">
                                        <div class="form-text mt-2">Formatos permitidos: PNG, JPG (Máx. 50 caracteres en nombre de archivo).</div>
                                    </div>
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
                <div class="card bg-primary text-white shadow-sm border-0">
                    <div class="card-body p-4">
                        <h6>¿Por qué completar todo?</h6>
                        <p class="small opacity-75">La información de la dirección y el logo aparecerán automáticamente en los reportes y facturas generadas por el sistema.</p>
                        <hr class="my-3 opacity-25">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <span class="badge bg-white text-primary">Sugerencia</span>
                                <p class="mb-0 mt-2 small">Usa un logo de alta resolución con fondo transparente (PNG).</p>
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


    <script src="<?php echo STATIC_URL; ?>./js/bootstrap-select.min.js"></script>

    <script src="<?php echo STATIC_URL; ?>js/DataTable/datatables-init.js" type="module"></script>
    <script src="<?php echo STATIC_URL; ?>js/escuelas/agregar-escuela.js" type="module"></script>
  

</body>

</html>