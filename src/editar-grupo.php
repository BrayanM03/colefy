
<?php

$controller_permiso->validarAcceso(1, CPermiso::EDITAR_GRUPOS->value);
require_once __DIR__ . '/../controllers/ProfesorController.php';
require_once __DIR__ . '/../controllers/MateriaController.php';
require_once __DIR__ . '/../controllers/GrupoController.php';

$controller_prof = new ProfesorController();
$data_profesores =$controller_prof->combos();
$controller_mate = new MateriaController();
$data_materias =$controller_mate->combos();
$controller_grupo = new Grupocontroller();
$id_grupo = $_GET['id_grupo'];
$data_grupo_resp =$controller_grupo->obtener_grupo(1, $id_grupo);
$data_grupo = $data_grupo_resp['data'][0];

include "vistas/general/header.php";
/* 


$controller_grup = new GrupoController();
$data_grupos =$controller_grup->combos(); */
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

                    <div class="row mb-2 justify-content-center">
                        <div class="col-12 col-md-3">
                            <h1 class="h3 mb-3">Editar grupo</h1>
                        </div>
                        <div class="col-12 col-md-3 text-end">
                           <a href="<?php echo BASE_URL; ?>grupos"><div class="btn btn-success">Volver</div></a> 
                        </div>
                    </div>



                    <div class="row justify-content-center">
                        <div class="col-12 col-md-10">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Aqui pueded actualizar los datos generales del grupo</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-4">
                                        <div class="col-12 col-md-6">
                                            <label for="">Nombre del grupo</label>
                                            <input type="text" value="<?= $data_grupo['nombre']?>" class="form-control" id="nombre-grupo" placeholder="Nombre horario">
                                        </div>
                                            <div class="col-12 col-md-3">
                                                    <label for="">Nivel</label>
                                                    <select name="" id="nivel" class="form-control selectpicker">
                                                        <option value="">Seleccione un nivel</option>
                                                        <option value="Preescolar" <?php if($data_grupo['nivel'] == 'Preescolar'){ echo 'selected';}?>>Preescolar</option>
                                                        <option value="Primaria" <?php if($data_grupo['nivel'] == 'Primaria'){ echo 'selected';}?>>Primaria</option>
                                                       
                                                    </select>
                                            </div>
                                        <div class="col-12 col-md-2">
                                            <label for="">Grado</label>
                                            <select name="" id="grado" class="form-control selectpicker" data-live-search="true">
                                                <option value="">Seleccione un grado</option>
                                                <option value="1" <?php if($data_grupo['grado'] == 1){ echo 'selected';}?>>1</option>
                                                <option value="2" <?php if($data_grupo['grado'] == 2){ echo 'selected';}?>>2</option>
                                                <option value="3" <?php if($data_grupo['grado'] == 3){ echo 'selected';}?>>3</option>
                                                <option value="4" <?php if($data_grupo['grado'] == 4){ echo 'selected';}?>>4</option>
                                                <option value="5" <?php if($data_grupo['grado'] == 5){ echo 'selected';}?>>5</option>
                                                <option value="6" <?php if($data_grupo['grado'] == 6){ echo 'selected';}?>>6</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mt-3 justify-content-center">
                                        <div class="col-4 text-center">
                                            <div class="btn btn-info" id="btn-actualizar-grupo">Actualizar datos generales del grupo</div>
                                        </div>
                                    </div>
                                    <hr class="mt-3">
                                    <h5 class="card-title mb-4">Aqui puedes agregar o cancelar alumnos del grupo</h5>
                                    <div class="row justify-content-center">
                                            <div class="col-12 text-end col-md-6">
                                            <select name=""  id="alumno" class="form-control selectpicker"
                                            placeholder='Selecciona un alumno'
                                            data-live-search="true">
                                                      
                                            </select>
                                        </div>
                                        <div class="col-12 text-start col-md-6">
                                           <div class="btn btn-primary" id="btn-registrar-alumno-grupo">Agregar</div>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <hr class="mt-3">
                                    </div>
                                    <div class="row mt-5">
                                        <div class="row mb-3">
                                            <div class="col-12 col-md-4">
                                                <label class="mb-3"><b>Alumnos del grupo - ciclo escolar</b></label>
                                                <select class="form-control" id="ciclo">
                                                    <option value="1">2025-2026</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <table id="detalle_grupo"></table>
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
        const id_grupo = <?= $_GET['id_grupo']?>;
        const id_ciclo = <?= $data_grupo['id_ciclo']?>;
    </script>
    <script src="<?php echo STATIC_URL; ?>./js/bootstrap-select.min.js"></script>
    <script src="<?php echo STATIC_URL; ?>js/DataTable/datatables-init.js" type="module"></script>
    <script src="<?php echo STATIC_URL; ?>js/grupos/editar-grupo.js" type="module"></script>

</body>

</html>