
<?php

$controller_permiso->validarAcceso(1, CPermiso::CREAR_HORARIOS->value);


require_once __DIR__ . '/../controllers/ProfesorController.php';
require_once __DIR__ . '/../controllers/MateriaController.php';

$controller_prof = new ProfesorController();
$data_profesores =$controller_prof->combos();
$controller_mate = new MateriaController();
$data_materias =$controller_mate->combos();

include "vistas/general/header.php";

?>

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
                            <h1 class="h3 mb-3">Crear nuevo horario</h1>
                        </div>
                        <div class="col-12 col-md-3 text-end">
                           <a href="horarios"><div class="btn btn-info">Volver</div></a> 
                        </div>
                    </div>



                    <div class="row justify-content-center">
                        <div class="col-12 col-md-10">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Selecciona las materias, los grupos y los horas para este horario</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12 col-md-12">
                                            <label for="">Nombre del horario</label>
                                            <input type="text" class="form-control" id="nombre-horario" placeholder="Nombre horario">
                                        </table>
                                        </div>
                                    </div>

                                    <div class="row mt-3 mb-3">
                                            <div class="col-12 col-md-2">
                                                    <label for="">Profesores</label>
                                                    <select name="" id="profesor" class="form-control selectpicker">
                                                        <option value="">Seleccione un profesor</option>
                                                        <option value="0">No aplica</option>
                                                        <?php
                                                        
                                                        foreach($data_profesores['data'] as $row) {
                                                            ?><option value="<?=$row['id']?>"><?=$row['nombre'] . ' ' . $row['apellido']?></option><?php
                                                            }

                                                        ?>
                                                    </select>
                                            </div>
                                        <div class="col-12 col-md-2">
                                            <label for="">Materias</label>
                                            <select name="" id="materia" class="form-control selectpicker">
                                                <option value="">Seleccione una materia</option>
                                                <?php
                                                        
                                                 foreach($data_materias['data'] as $row) {
                                                     ?><option value="<?=$row['id']?>"><?=$row['nombre']?></option><?php
                                                     }
                                             ?>
                                    
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-2">
                                            <label for="">Dias</label>
                                            <select name="" id="dia" class="form-control selectpicker" multiple>
                                                <option value="">Seleccione un día</option>
                                                <option value="lunes">Lunes</option>
                                                <option value="martes">Martes</option>
                                                <option value="miercoles">Miercoles</option>
                                                <option value="jueves">Jueves</option>
                                                <option value="viernes">Viernes</option>
                                                <option value="sabado">Sabado</option>
                                                <option value="domingo">Domingo</option>
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-2">
                                        <label for="appt-time">Hora: </label>
                                        <input id="hora" class="form-control" type="time" name="appt-time" value="13:30" />
                                        </div>
                                    </div>
                                    <div class="row justify-content-center">
                                        <div class="col-12 text-center col-md-4">
                                           <div class="btn btn-primary" id="btn-agregar-prehorario">Agregar</div>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <hr class="mt-3">
                                    </div>
                                    <div class="row mt-5">
                                        <div class="col-12">
                                            <table id="tabla-prehorario"></table>
                                        </div>
                                    </div>
                                    <div class="row mt-5 justify-content-center">
                                        <div class="col-4 text-center">
                                            <div class="btn btn-success" id="btn-registrar-horario">Registrar horario</div>
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
    <script src="<?php echo STATIC_URL; ?>js/horarios/prehorario.js" type="module"></script>
  

</body>

</html>