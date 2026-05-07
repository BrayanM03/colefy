<?php

require_once  __DIR__ . '/../controllers/ProfesorController.php';
require_once  __DIR__ . '/../config/dates.php';

$controller_permiso->verificarSesion();
$permiso_panel_maestros = $controller_permiso->validarAcceso(2, CPermiso::VER_PANEL_MAESTROS->value);
if($permiso_panel_maestros){
    $controller_prof = new ProfesorController();
    $resp_grupos = $controller_prof->obtenerGruposProfesor($_SESSION['id']);
};

$dates = new Date();

require_once __DIR__ . '/../controllers/GrupoController.php';

$controller_grupo = new Grupocontroller();
$data_combo_niveles =$controller_grupo->combo_niveles();
$data_combo_ciclos =$controller_grupo->combo_ciclos();
$data_niveles = $data_combo_niveles['data'];
$data_ciclos = $data_combo_ciclos['data'];



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


<div class="row justify-content-center animate__animated animate__fadeIn">
    <div class="col-12 col-lg-10">
        <div class="wizard-container">
            <div class="wizard-progress mb-5">
                <div class="progress-track"></div>
                <div class="progress-step active" data-step="1"></div>
                <div class="progress-step" data-step="2"></div>
                <div class="progress-step" data-step="3"></div>
                <div class="progress-step" data-step="4"></div>
            </div>

            <div class="wizard-card card">
                <div class="card-body p-5">
                    <span class="badge-step">Paso 1 de 4</span>
                    <h2 class="wizard-title mt-3">Configuración del Ciclo</h2>
                    <p class="wizard-subtitle">Selecciona el periodo académico y el nivel educativo para comenzar a armar el horario.</p>
                    
                    <div class="row mt-5">
                        <div class="col-md-4 mb-4">
                            <label class="form-label-custom">Ciclo Escolar</label>
                            <select class="form-select-airbnb" id="id_ciclo">
                                <option value="" selected disabled>Elegir ciclo...</option>
                                <?php 
                                    foreach ($data_ciclos as $key => $value) {
                                        ?>
                                        <option value="<?= $value['id']?>" 
                                            <?php
                                                if($key ==0) { echo 'selected'; }else{ echo ''; }?>>
                                            <?= $value['nombre']; ?>
                                         </option>
                                        <?php
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="form-label-custom">Nivel Educativo</label>
                            <select class="form-select-airbnb" id="nivel_educativo">
                                <option value="" selected disabled>Elegir nivel...</option>
                            <?php 
                                    foreach ($data_niveles as $key => $value) {
                                        ?>
                                        <option value="<?= $value['id']?>"
                                        <?php
                                            if($key ==1) { echo 'selected'; }else{ echo ''; }?>>
                                        <?= $value['nombre']?></option>
                                        <?php
                                    }
                                ?>
                            </select>
                        </div> 
                        <div class="col-md-4 mb-4">
                            <label class="form-label-custom">Nivel Educativo</label>
                            <select class="form-select-airbnb" id="turno_escolar">
                                <option>Selecciona uno</option>
                                <option value="manana" selected>Matutino</option>
                                <option value="tarde">Vespertino</option>
                                <option value="ambos">Ambos</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button class="btn-airbnb-primary" id="btn-ir-paso-dos">
                            Siguiente 
                            <i class="fas fa-chevron-right ms-2"></i>
                        </button>
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
    <script src="<?php echo STATIC_URL; ?>js/catalogos/flujo.js" type="module"></script>
</body>

</html>