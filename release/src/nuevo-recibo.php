
<?php
require_once  __DIR__ .  '/../controllers/ReciboController.php';

$controller_permiso->validarAcceso(1, CPermiso::CREAR_RECIBOS->value);
$controller = new ReciboController();
$response = $controller->sumatoria_conceptos(1);
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

                    <div class="row mb-2">
                        <div class="col-12 col-md-9">
                            <h1 class="h3 mb-3">Nuevo recibo de pago</h1>
                        </div>
                        <div class="col-12 col-md-3 text-end">
                        </div>
                    </div>

                    <div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-10">
                        <h5 class="card-title mb-0">En modulo puedes generar una nueva nota o recibo para el alumno</h5>
                    </div>
                    <div class="col-2">
                        <label for="">Ciclo escolar</label>
                        <select class="form-control" id="ciclo" disabled>
                            <option value="1">2025-2026</option>
                        </select>
                        <small id="small_ciclo" style="color:tomato;"></small>

                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                            <div class="col-12 col-md-6 mb-2">
                                <label for="alumno">Alumno</label>
                                <select id="alumno" class="form-control selectpicker"
                                    placeholder="Selecciona un alumno"
                                    data-live-search="true">
                                </select>
                                <small id="small_alumno" style="color:tomato;"></small>
 
                            </div>
                            <div class="col-12 col-md-6 mb-2">
                                <label for="tipo">Tipo de recibo</label>
                                <select id="tipo" class="form-control selectpicker"
                                    placeholder="Selecciona un tipo de recibo" 
                                    data-live-search="true">
                                    <option value="1">Contado</option>
                                    <option value="2">Parcialidad</option>
                                </select>
                                <small id="small_tipo" style="color:tomato;"></small>

                            </div>
                            
                </div>
                <div class="row" id="area-plazo"> 
                </div>

                        <hr>

                            <div class="row mt-3 mb-2">
                            <label for="" class="mb-2"><b>Concepto</b></label>

                            <div class="col-12 col-md-6">
                                <label for="categoria">Categoria del concepto</label>
                                <select id="categoria" class="form-control selectpicker"
                                    placeholder="Selecciona una categoria del concepto"
                                    data-live-search="false">
                                    <option value="1">Mensualidad</option>
                                    <option value="2">Uniforme</option>
                                    <option value="3">Transporte</option>
                                    <option value="4">Cafeteria</option>
                                </select>
                                <small id="small_categoria" style="color:tomato;"></small>

                            </div>

                            <div class="col-12 col-md-6">
                                <div class="row" id="area-categoria">                                    
                                </div>
                            </div>
                        </div>   
                    
            
                <div class="row">
                            <div class="col-12 col-md-3">
                                <label for="cantidad">Cantidad</label>
                                <input type="number" class="form-control input-verificacion" id="cantidad" placeholder="0">
                                <small id="small_cantidad" style="color:tomato;"></small>

                            </div>
                            <div class="col-12 col-md-3">
                                <label for="precio">Monto del concepto unit.</label>
                                <input class="form-control input-verificacion" id="precio" placeholder="0.00">
                                <small id="small_precio" style="color:tomato;"></small>

                            </div>
                            <div class="col-12 col-md-2">
                                <div class="btn-primary btn" id="btn-agregar-concepto" style="margin-top:20px;">Agregar</div>
                            </div>
                        </div>
                <hr>
                 <!-- Fila tabla -->
                 <div class="row mt-4 justify-content-center">
                    <div class="col-12 col-md-11 col-lg-11 col-sm-12">
                        <label for="" class="mb-2"><b>Tabla de conceptos</b></label>
                       <table class="mt-3 table table-bordered" id="tabla-conceptos">
                       </table>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12 col-md-6 mb-2">
                                <label for="forma-pago" id="label-forma-pago">Forma de pago</label>
                                <select id="forma-pago" class="form-control selectpicker"
                                    placeholder="Selecciona una forma de pago"
                                    data-live-search="true"
                                    multiple>
                                    <option value="Efectivo">Efectivo</option>
                                    <option value="Tarjeta">Tarjeta</option>
                                    <option value="Transferencia">Transferencia</option>
                                    <option value="Deposito">Deposito</option>
                                    <option value="Cheque">Cheque</option>
                                </select>
                                <small id="small_forma-pago" style="color:tomato;"></small>
                    </div>

                    <!-- Columna derecha -->
                    <div class="col-12 col-md-6">
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="area-formas-pago" id="area-formas-pago">
                                    <label><b>Montos de la forma de pago</b></label><br>
                                    <div class="row mt-3" id="lista-formas-pago">
                                        <div class="col-12 text-start">
                                            Sin formas de pago seleccionadas
                                        </div>
                                    </div>
                                </div>
                                <small id="small_area-formas-pago" style="color:tomato;"></small>

                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fila para los montos -->
                <div class="row mt-3">
                    <div class="col-12 col-md-4">
                        <label for="comentarios">Comentarios</label>
                        <textarea class="form-control" id="comentarios" placeholder="Escribe algun comentario..."></textarea>
<!--                         <small id="small_comentarios" style="color:tomato;"></small>-->
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="">Suma</label>
                        <input class="form-control" id="suma_total" placeholder="0.00" disabled>
                        <small id="small_suma_total" style="color:tomato;"></small>
                    </div>
                    <div class="col-12 col-md-4">
                        <label for="">Importe total</label>
                        <input class="form-control" id="importe_total" placeholder="0.00" value="<?= floatval($response['suma'])?>" disabled>
                    </div>
                    <div class="col-12 col-md-4">
                        <div id="btn-hacer-recibo" class="btn-secondary btn" style="margin-top:20px;">Realizar pago</div>
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
    
    <script src="<?php echo STATIC_URL; ?>js/DataTable/datatables-init.js" type="module"></script>
    <script src="<?php echo STATIC_URL; ?>js/recibos/nuevo-recibo.js" type="module"></script>
  

</body>

</html>