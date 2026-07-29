
<?php

$controller_permiso->validarAcceso(1, CPermiso::VER_CONTROL_ASISTENCIAS->value);
require_once __DIR__ . '/../controllers/GrupoController.php';
require_once __DIR__ . '/../controllers/AsistenciaController.php';
$controller_as = new AsistenciaController();
$grupo_control = new GrupoController();

$data_grupo =$grupo_control->combo_grupos_profesor(1);
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
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-secondary">Control de Asistencias</h3>
    </div>

    <!-- Panel de Filtros -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body bg-light">
            <div class="row align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold text-muted">Selecciona un Grupo</label>
                    <select id="filtro_grupo" class="form-select">
                        <option value="" disabled selected>Cargando grupos...</option>
                        <!-- Aquí se llenarán los grupos del maestro -->
                        <?php
                               foreach ($data_grupo['data'] as $key => $value) {
                                 echo "<option value=".$value['id'].">".$value['nombre']."</option>";
                               }
                           ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold text-muted">Semana a consultar</label>
                    <!-- Usamos type="date" y el JS calculará el Lunes y Viernes de esa semana -->
                    <input type="date" id="filtro_fecha" class="form-control" value="<?= date('Y-m-d')?>"> 
                </div>
                <div class="col-md-4">
                    <button class="btn btn-primary w-100" onclick="cargarCuadriculaSemanal()">
                        <i data-feather="search" class="me-2"></i> Buscar Asistencias
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenedor de la Cuadrícula -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0 table-responsive" id="contenedor-cuadricula">
            <div class="text-center py-5 text-muted">
                <i data-feather="calendar" style="width: 48px; height: 48px; opacity: 0.5"></i>
                <p class="mt-3">Selecciona un grupo y haz clic en "Buscar Asistencias" para ver la semana.</p>
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
    <script src="<?php echo STATIC_URL; ?>js/asistencias/control-asistencias.js"></script>
  

</body>

</html>