
<?php


$controller_permiso->validarAcceso(1, CPermiso::ASIGNAR_HORARIOS->value);


require_once __DIR__ . '/../controllers/GrupoController.php';
require_once __DIR__ . '/../controllers/HorarioController.php';

$horario_control = new HorarioController();
$grupo_control = new GrupoController();
$data_horario =$horario_control->combo();
$data_grupo =$grupo_control->combo();
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
                            <h1 class="h3 mb-3">Alumnos</h1>
                        </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12 col-md-3">
                        <label>Grupo</label>
                        <select name=""  id="grupo" class="form-control selectpicker"
                            placeholder='Selecciona un  grupo' data-live-search="true" multiple>
                            <option value="">Selecciona un grupo</option>
                            <?php
                               
                                foreach ($data_grupo['data'] as $key => $value) {
                                  echo "<option value=".$value['id'].">".$value['nombre']."</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="col-12 col-md-3">
                        <label>Horario</label>
                        <select name=""  id="horario" class="form-control selectpicker"
                            placeholder='Selecciona un horario' data-live-search="true">
                            <option value="">Seleccionar horario</option>
                            <?php
                                foreach ($data_horario['data'] as $key => $value) {
                                    echo "<option value=".$value['id'].">".$value['nombre']."</option>";
                                }
                                ?>
                        </select>
                       
                    </div>
                    <div class="col-12 col-md-3">
                        <div style="margin-top:18px;" class="btn btn-success" id="bnt-asignar-horario">Asignar</div>
                    </div>
                    <div class="col-12 col-md-3 text-end">
                        <a href="<?php echo BASE_URL; ?>horarios"><div style="margin-top:18px;" class="btn btn-info">Volver</div></a>
                    </div>
                </div>



                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">En esta estan los horarios asignados a cada grupo</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 col-md-12">
                                        <table id="grupos-horarios" class="table table-hover nowrap" style="width:100%">
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


    <script src="<?php echo STATIC_URL; ?>./js/bootstrap-select.min.js"></script>

    <script src="<?php echo STATIC_URL; ?>js/DataTable/datatables-init.js" type="module"></script>
    <script src="<?php echo STATIC_URL; ?>js/horarios/asignar-horario.js" type="module"></script>
  

</body>

</html>