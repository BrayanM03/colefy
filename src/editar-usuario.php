<?php

$controller_permiso->validarAcceso(1, CPermiso::EDITAR_USUARIOS->value);
include "vistas/general/header.php"; 

// Recibimos el ID del formulario POST
/* $id_recibo = null;
$folio_interno = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_POST['id'])) {
        // Sanitizar el ID antes de usarlo
        $id_recibo = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
    }
    if (isset($_POST['folio_interno'])) {
        // Sanitizar el ID antes de usarlo
        $folio_interno = filter_var($_POST['folio_interno'], FILTER_SANITIZE_NUMBER_INT);
    }

    $_SESSION['id_recibo'] = $id_recibo;
    $_SESSION['folio_interno'] = $folio_interno;

    header('Location: editar-recibo.php'); // Redirige a la misma URL, ahora como GET
    exit;
} 

if (isset($_SESSION['id_recibo'])) {
    $id_recibo = $_SESSION['id_recibo'];
    $folio_interno = $_SESSION['folio_interno'];
}
 */
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
                            <h1 class="h3 mb-3" 
                            id="contenedor-ids-recibo">
                            Editar usuario</h1>
                        </div>
                        <div class="col-12 col-md-3 text-end">
                            <a href="<?php echo BASE_URL; ?>permisos_usuarios">
                                <div class="btn btn-success">Volver</div>
                            </a>
                        </div>
                    </div>

                    <!-- PRUEBA DE NAV POR PESTAÑAS -->
                    <div class="row mb-2 justify-content-center">
                        <div class="col-12 col-md-12">
                            <div class="tabs">
                                <div class="tab-container">

                                    <div id="permisos" class="tab">
                                        <a href="#permisos">Permisos</a>
                                        <div class="tab-content">
                                        
                                            
                                            <div class="row justify-content-center">
                                                <div class="col-12 text-start col-md-7">
                                                    <h5 class="card-title mb-4">Aqui gestionar los permisos del usuario</h5>
                                                </div>
                                                <div class="col-12 text-end col-md-5">
                                                <!-- <div  id="btn-guardar-permisos" class="btn btn-success">
                                                        Guardar</div> 
                                                    <div  id="btn-agregar-permiso" class="btn btn-primary">
                                                        Agregar permiso</div>-->
                                                </div>
                                            </div>
                                            <div class="mt-3 row">
											<div class="col-12">
													<div class="row justify-content-center">
														<div class="col-12 col-md-12">
														<div class="card-body" id="area-permisos">
														<div class="row">
															<div class="col-12 text-center">
																<dotlottie-wc
																class="m-auto"
																src="https://lottie.host/16abd1c5-90bb-4e18-b98b-a616ac4b71ff/Y552obLciD.lottie"
																style="width: 120px;height: 120px"
																autoplay
																loop
																></dotlottie-wc>
															</div>
															
														</div>
													</div>
														</div>
													</div>
												
											</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="tab2" class="tab">
                                        <a href="#tab2">Datos extra</a>
                                        <div class="tab-content">
                                            
                                            <div class="row mb-2">
                                                <div class="col-6 col-md-10">
                                                        <label class="mb-3"><b>Pagos del recibo</b></label>
                                                </div>
                                                <div class="col-6 col-md-2 text-end">
                                                    <div id="btn-nuevo-pago" class="btn btn-primary">Nuevo pago</div>
                                                </div>
                                            </div>
                                            <div class="row jutify-content-center mb-2">
                                                <div class="col-12 mt-1 mr-auto ml-auto">
                                                    <table id="tabla-pagos" style="width: 100%" class="display table tabla-redondeada table-striped"></table>
                                                </div>
                                            </div>
                                            <div class="row justify-content-center mb-2">
                                                <div class="col-12 col-md-3 p-2 mt-1">
                                                   <label for="total">Total</label>
                                                   <input class="form-control" id="total" placeholder="0.00" disabled>
                                                </div>
                                                <div class="col-12 col-md-3 p-2 mt-1">
                                                   <label for="pagado">Pagado</label>
                                                   <input class="form-control" id="pagado" placeholder="0.00" disabled>
                                                </div>
                                                <div class="col-12 col-md-3 p-2 mt-1">
                                                   <label for="restante">Restante</label>
                                                   <input class="form-control" id="restante" placeholder="0.00" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="datos_generales" class="tab">
                                        <a href="#datos_generales">Datos generales</a>
                                        <div class="tab-content">
                                            <div class="row justify-content-center">
                                                <div class="col-12 col-md-10">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h5 class="card-title mb-0">Aqui puedes actualizar los datos
                                                                generales del grupo</h5>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row mb-4">
                                                                <div class="col-12 col-md-7">
                                                                    <label for="">Nombre del Alumno</label>
                                                                    <select id="alumno" class="form-control selectpicker"
                                                                        placeholder="Selecciona un alumno"
                                                                        data-live-search="true">
                                                                    </select>
                                                                </div>
                                                                <div class="col-12 col-md-5">
                                                                    <label for="">Grupo</label>
                                                                    <input type="text" id="grupo" class="form-control" disabled>
                                                                </div>
                                                            </div>

                                                            <div class="row mb-4">

                                                                <div class="col-12 col-md-3">
                                                                    <label for="">Tipo de recibo</label>
                                                                    <select name="" id="tipo"
                                                                        class="form-control selectpicker"
                                                                        data-live-search="true">
                                                                        <option value="">Seleccione un tipo</option>
                                                                        <option value="1">Contado</option>
                                                                        <option value="2">Parcial</option>
                                                                    </select>
                                                                </div>
                                                                
                                                                <div class="col-12 col-md-9">
                                                                    <div class="row" id="area-fechas">
                                                                        <div class="col-12 col-md-3">
                                                                            <label for="">Plazo</label>
                                                                            <select name="" id="plazo"
                                                                                class="form-control selectpicker"
                                                                                data-live-search="true">
                                                                                <option value="">Seleccione un plazo</option>
                                                                                <option value="1">7 dias</option>
                                                                                <option value="2">15 dias</option>
                                                                                <option value="3">1 mes</option>
                                                                                <option value="4">45 días</option>
                                                                                <option value="5">1 día</option>
                                                                                <option value="7">Personalizado</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="col-12 col-md-3">
                                                                            <label for="">Fecha</label>
                                                                            <input class="form-control" type="date" id="fecha">
                                                                        </div>
                                                                        <div class="col-12 col-md-4">
                                                                            <label for="">Fecha de vencimiento</label>
                                                                            <input class="form-control" type="date" id="fecha-vencimiento">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row mb-4">
                                                                <div class="col-12 mt-3 col-md-3">
                                                                    <label for="">Estatus</label>
                                                                    <select name="" id="estatus"
                                                                        class="form-control selectpicker"
                                                                        data-live-search="true">
                                                                        <option value="">Seleccione un estatus</option>
                                                                        <option value="1">Emitido</option>
                                                                        <option value="2">Abonado</option>
                                                                        <option value="3">Pagado</option>
                                                                        <option value="4">Vencido</option>
                                                                        <option value="5">Condonado</option>
                                                                        <option value="6">Pendiente</option>
                                                                        <option value="7">Verificando</option>
                                                                        <option value="0">Cancelado</option>
                                                                        
                                                                    </select>
                                                                </div>
                                                                <div class="col-12 mt-3 col-md-3">
                                                                    <label for="importe-total">Importe total</label>
                                                                    <input class="form-control" type="number" id="importe-total" placeholder="0.00" disabled>
                                                                </div>
                                                                <div class="col-12 mt-3 col-md-3">
                                                                    <label for="saldo-pendiente">Saldo pendiente</label>
                                                                    <input class="form-control" type="number" id="saldo-pendiente" placeholder="0.00" disabled>
                                                                </div>
                                                                
                                                            </div>
                                                            <div class="row mt-3 justify-content-center">
                                                                <div class="col-4 text-center">
                                                                    <div class="btn btn-info"
                                                                        onclick="actualizarDatosRecibo()">Actualizar
                                                                        datos generales del recibo</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
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
    <script>
       const ID_USER = <?php echo $_GET['id_usuario']; ?>;
    </script>
    <script src="https://unpkg.com/@lottiefiles/dotlottie-wc@0.8.11/dist/dotlottie-wc.js" type="module"></script>

    <script src="<?php echo STATIC_URL; ?>./js/bootstrap-select.min.js"></script>
    <script src="<?php echo STATIC_URL; ?>js/utils/dates.js" type="module"></script>
    <script src="<?php echo STATIC_URL; ?>js/DataTable/datatables-init.js" type="module"></script>
    <script src="<?php echo STATIC_URL; ?>js/usuarios/editar-usuario.js" type="module"></script>
</body>

</html>