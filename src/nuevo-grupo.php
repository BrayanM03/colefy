
<?php
$controller_permiso->validarAcceso(1, CPermiso::CREAR_GRUPOS->value);

require_once __DIR__ . '/../controllers/ProfesorController.php';
require_once __DIR__ . '/../controllers/MateriaController.php';
require_once __DIR__ . '/../controllers/GrupoController.php';

$controller_prof = new ProfesorController();
$data_profesores =$controller_prof->combos();
$controller_mate = new MateriaController();
$data_materias =$controller_mate->combos();
$controller_grupo = new GrupoController();
$data_niveles = $controller_grupo->combo_niveles();
$data_ciclos = $controller_grupo->combo_ciclos();

$niveles_educativos = $data_niveles['data'];
$ciclos_escolares = $data_ciclos['data'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Responsive Admin &amp; Dashboard Template based on Bootstrap 5">
    <meta name="author" content="AdminKit">
    <meta name="keywords" content="adminkit, bootstrap, bootstrap 5, admin, dashboard, template, responsive, css, sass, html, theme, front-end, ui kit, web">

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="shortcut icon" href="<?php echo STATIC_URL; ?>img/icons/icon-48x48.png" />

    <link rel="canonical" href="https://demo-basic.adminkit.io/pages-blank.html" />

    <title>Nuevo grupo | Colefy</title>

    <link href="<?php echo STATIC_URL; ?>css/app.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.dataTables.min.css" />
    <link href="<?php echo STATIC_URL; ?>css/bootstrap-select.min.css" rel="stylesheet" />
  
</head>

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
                            <h1 class="h3 mb-3">Crear nuevo grupo</h1>
                        </div>
                        <div class="col-12 col-md-3 text-end">
                           <a href="grupos"><div class="btn btn-success">Volver</div></a> 
                        </div>
                    </div>



                    <div class="row justify-content-center">
                        <div class="col-12 col-md-10">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Selecciona las materias, los grupos y los horas para este horario</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-4">
                                    <div class="col-12 col-md-4">
                                                <label for="ciclo">Ciclo escolar</label>
                                                <select class="form-control" id="ciclo"><?php
                                                            foreach ($ciclos_escolares as $key => $value) {
                                                                $nombre_ciclo = $value['nombre'];
                                                                $id_ciclo = $value['id'];
                                                                print_r("<option value='$id_ciclo'>$nombre_ciclo</option>");
                                                            }
                                                        ?>
                                                </select>
                                            </div>
                                        <div class="col-12 col-md-4">
                                                    <label for="">Nivel</label>
                                                    <select name="" id="nivel" class="form-control selectpicker">
                                                        <option value="">Seleccione un nivel</option>
                                                        <?php
                                                            foreach ($niveles_educativos as $key => $value) {
                                                                $nombre_nivel = $value['nombre'];
                                                                $id_nivel = $value['id'];
                                                                print_r("<option value='$id_nivel'>$nombre_nivel</option>");
                                                            }
                                                        ?>
                                                       
                                                       
                                                    </select>
                                            </div>
                                        <div class="col-12 col-md-3">
                                            <label for="">Grado</label>
                                            <select name="" id="grado" class="form-control selectpicker" data-live-search="true">
                                                <option value="">Seleccione un grado</option>
                                                <option value="">-</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                            <div class="col-12 col-md-6">
                                                    <label for="">Nombre del grupo</label>
                                                    <input type="text" class="form-control" id="nombre-horario" placeholder="Nombre horario">
                                            </div>
                                    </div>
                                    <hr class="mt-3">
                                    <div class="row justify-content-center">
                                            <div class="col-12 text-end col-md-6">
                                            <select name=""  id="alumno" class="form-control selectpicker"
                                            placeholder='Selecciona un alumno'
                                            data-live-search="true">
                                                      
                                            </select>
                                        </div>
                                        <div class="col-12 text-start col-md-6">
                                           <div class="btn btn-primary" id="btn-registrar-alumno-pregrupo">Agregar</div>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <hr class="mt-3">
                                    </div>
                                    <div class="row mt-5">
                                        <div class="row mb-3">
                                            <div class="col-12 col-md-4">
                                                <label class="mb-3"><b>Alumnos del grupo</b></label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <table id="detalle_pregrupo"></table>
                                        </div>
                                    </div>
                                    <div class="row mt-5 justify-content-center">
                                        <div class="col-4 text-center">
                                            <div class="btn btn-success" id="btn-registrar-grupo">Registrar grupo</div>
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
    
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="<?php echo STATIC_URL; ?>js/app.js"></script>

    <!-- Librerias -->
    <script src="https://kit.fontawesome.com/5c955c6e98.js" crossorigin="anonymous"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>
    <script src="<?php echo STATIC_URL; ?>js/bootstrap-select.min.js"></script>
    <script src="<?php echo STATIC_URL; ?>js/DataTable/datatables-init.js" type="module"></script>
    <script src="<?php echo STATIC_URL; ?>js/grupos/nuevo-grupo.js" type="module"></script>
    <script>
         // Convertimos el array de PHP a un objeto de JS
         const configuracionNiveles = <?php echo json_encode($niveles_educativos); ?>;

         document.getElementById('nivel').addEventListener('change', function() {
    const idNivelSeleccionado = this.value;
    const selectGrado = document.getElementById('grado');
    
    // Limpiar opciones actuales
    selectGrado.innerHTML = '<option value="">Seleccione un grado</option>';

    if (idNivelSeleccionado) {
        // Buscamos el nivel en nuestro objeto de configuración
        const nivelEncontrado = configuracionNiveles.find(n => n.id == idNivelSeleccionado);

        if (nivelEncontrado) {
            const totalGrados = parseInt(nivelEncontrado.duracion_grados);
            const tipoPeriodo = nivelEncontrado.tipo_periodo; // Por si quieres usarlo luego

            for (let i = 1; i <= totalGrados; i++) {
                const option = document.createElement('option');
                option.value = i;
                option.text = i + '°'; // Ejemplo: 1°, 2°, etc.
                selectGrado.appendChild(option);
            }
        }
    }

    // IMPORTANTE: Si usas bootstrap-select (selectpicker), hay que refrescarlo
    if ($('.selectpicker').length > 0) {
        $('.selectpicker').selectpicker('refresh');
    }
});
    </script>

</body>

</html>