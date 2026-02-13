<?php
$controller_permiso->validarAcceso(1, CPermiso::EDITAR_RECIBOS->value);
require_once __DIR__ . '/../controllers/ReciboController.php';
$controller_recibo = new ReciboController();
$data_recibo = $controller_recibo->obtener_recibo($_GET['id_recibo'], 2);
$folio_interno = $data_recibo['data']['folio_escuela'];
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

                    <div class="row mb-2 justify-content-center">
                        <div class="col-12 col-md-3">
                            <h1 class="h3 mb-3" 
                            id="contenedor-ids-recibo"
                            data-id-recibo="<?php echo htmlspecialchars($_GET['id_recibo']); ?>" 
                            data-folio-interno="<?php echo htmlspecialchars($folio_interno); ?>">
                            Actualización de recibo de pago</h1>
                        </div>
                        <div class="col-12 col-md-3 text-end">
                            <a href="<?= BASE_URL; ?>recibos">
                                <div class="btn btn-success">Volver</div>
                            </a>
                        </div>
                    </div>

                    <!-- PRUEBA DE NAV POR PESTAÑAS -->
                    <div class="row mb-2 justify-content-center">
                        <div class="col-12 col-md-12">
                            <div class="tabs">
                                <div class="tab-container">

                                    <div id="tab3" class="tab">
                                        <a href="#tab3">Conceptos</a>
                                        <div class="tab-content">
                                        
                                            
                                            <div class="row justify-content-center">
                                                <div class="col-12 text-start col-md-10">
                                                    <h5 class="card-title mb-4">Aqui puedes agregar o cancelar conceptos del
                                                         recibo</h5>
                                                </div>
                                                <div class="col-12 text-end col-md-2">
                                                    <div  id="btn-agregar-concepto" class="btn btn-primary">
                                                        Agregar concepto</div>
                                                </div>
                                            </div>
                                            <div class="mt-3 row">
                                                <div class="col-12">
                                                    <table id="tabla-conceptos-editar" style="width: 100%" class= "tabla-redondeada table table-striped">
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="tab2" class="tab">
                                        <a href="#tab2">Pagos</a>
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

                                    <div id="tab1" class="tab">
                                        <a href="#tab1">Datos generales</a>
                                        <div class="tab-content">
                                            <div class="row justify-content-center">
                                                <div class="col-12 col-md-10">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h5 class="card-title mb-0">Aqui puedes actualizar los datos
                                                                generales del recibo</h5>
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

                                                            <div class="row mb-4">
                                                                <div class="col-12 col-md-9">
                                                                    <label for="comentarios">Comentarios</label>
                                                                    <textarea class="form-control" id="comentarios" placeholder="Escribe un comentario..."></textarea>
                                                                </div>
                                                            </div>


                                                            <div class="row mt-3 justify-content-center">
                                                                <div class="col-4 text-center">
                                                                    <div class="btn btn-info"
                                                                        id="btn-actualizar-recibo"
                                                                        >Actualizar
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
        const id_recibo = <?php echo $_GET['id_recibo']; ?>
    </script>
    <script src="<?php echo STATIC_URL; ?>js/utils/dates.js" type="module"></script>
    <script src="<?php echo STATIC_URL; ?>js/DataTable/datatables-init.js" type="module"></script>
    <script src="<?php echo STATIC_URL; ?>js/recibos/actualizar-recibo.js" type="module"></script>
    <script src="<?php echo STATIC_URL; ?>js/recibos/pagos.js" type="module"></script>
    <script>
       
    </script>
</body>

</html>