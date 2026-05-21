<?php
require_once  __DIR__ .  '/../controllers/GastoController.php';
require_once __DIR__ . '/../controllers/GrupoController.php';

$controller_gasto = new GastoController();
$controller_grupo = new GrupoController();

// Validar permiso específico para gastos
$controller_permiso->validarAcceso(1, CPermiso::CREAR_GASTOS->value);

// Si los gastos también se segmentan por ciclo (para reportes anuales)
$data_categorias = $controller_gasto->combo_categorias_gastos(2);
$data_ciclos = $controller_grupo->combo_ciclos();
$ciclos_escolares = $data_ciclos['data'];
$categorias_gastos = $data_categorias['data'];

include "vistas/general/header.php";
?>
<div class="wrapper">
    <?php include "vistas/general/sidebar.php" ?>
    <div class="main">
        <?php include "vistas/general/navbar.php" ?>

        <main class="content">
            <div class="container-fluid p-0">

                <div class="row mb-2">
                    <div class="col-12 col-md-9">
                        <h1 class="h3 mb-3">Registro de Gasto</h1>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-12 col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-8">
                                        <h5 class="card-title mb-0">Completa la información para registrar un egreso.</h5>
                                    </div>
                                    <div class="col-2">
                                        <label for="fecha">Fecha del gasto</label>
                                        <input class="form-control" type="date" id="fecha" value="<?= date('Y-m-d')?>">
                                        <small id="small_fecha" style="color:tomato;"></small>

                                    </div>
                                    <div class="col-2">
                                        <label for="ciclo">Ciclo asociado</label>
                                        <select class="form-control" id="ciclo">
                                            <?php
                                            foreach ($ciclos_escolares as $value) {
                                                echo "<option value='{$value['id']}'>{$value['nombre']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-md-6 mb-2">
                                        <label for="proveedor">Proveedor / Beneficiario</label>
                                        <input type="text" id="proveedor" value="CFE" class="form-control" placeholder="Ej. Papelería Martínez o CFE">
                                        <small id="small_proveedor" style="color:tomato;"></small>
                                    </div>
                                    <div class="col-12 col-md-6 mb-2">
                                        <label for="tipo_comprobante">Tipo de comprobante</label>
                                        <select id="tipo_comprobante" class="form-control selectpicker">
                                        <option value="factura_cfdi">Factura (CFDI) ✓ Deducible</option>
                                        <option value="nota_remision">Nota de remisión</option>
                                        <option value="ticket_recibo">Ticket / Recibo</option>
                                        <option value="sin_comprobante">Sin comprobante oficial</option>
                                        </select>
                                    </div>
                                </div>

                                <hr>

                                <div class="row mt-3 mb-2">
                                <div class="col-12 col-md-6">
                                <?php
                                    // Pre-agrupar antes del HTML
                                    $agrupado = [];
                                    foreach ($categorias_gastos as $item) {
                                        $cat = $item['id_categoria'];
                                        if (!isset($agrupado[$cat])) {
                                            $agrupado[$cat] = [
                                                'nombre' => $item['categoria'],
                                                'items'  => []
                                            ];
                                        }
                                        $agrupado[$cat]['items'][] = [
                                            'id'     => $item['id_subcategoria'],
                                            'nombre' => $item['subcategoria']
                                        ];
                                    }
                                    ?>

                                        <label for="categorias-gastos">Categoría del Gasto</label>
                                        <select id="categorias-gastos" class="form-control" data-live-search="true" title="Selecciona un gasto"> >
                                          <option value="">Seleccione una categoria</option>
                                            <?php foreach ($agrupado as $grupo): ?>
                                                <optgroup label="<?= htmlspecialchars($grupo['nombre']) ?>">
                                                    <?php foreach ($grupo['items'] as $sub): ?>
                                                        <option value="<?= $sub['id'] ?>">
                                                            <?= htmlspecialchars($sub['nombre']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </optgroup>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label for="comprobante_file">Adjuntar Comprobante (PDF o Imagen)</label>
                                        <input type="file" id="comprobante_file" class="form-control" accept="application/pdf,image/*">
                                        <small class="text-muted">Opcional: Subir factura o ticket escaneado.</small>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-12 col-md-6">
                                        <label for="concepto">Concepto específico</label>
                                        <input type="text" class="form-control" id="concepto" value="Pago de luz" placeholder="Ej. Pago de luz mes de mayo">
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label for="monto">Monto Total</label>
                                        <input type="number" class="form-control" id="monto" value="490" placeholder="0.00">
                                        <small id="small_monto" style="color:tomato;"></small>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label for="forma_pago">Método de salida</label>
                                        <select id="forma_pago" class="form-control selectpicker">
                                            <option value="Efectivo">Efectivo (Caja Chica)</option>
                                            <option value="Transferencia">Transferencia Bancaria</option>
                                            <option value="Tarjeta">Tarjeta de Débito/Crédito</option>
                                            <option value="Cheque">Cheque</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12 col-md-8">
                                        <label for="observaciones">Observaciones adicionales</label>
                                        <textarea class="form-control" id="observaciones" value="Se pagan 2 meses" rows="2" placeholder="Detalles extra sobre el gasto..."></textarea>
                                    </div>
                                    <div class="col-12 col-md-4 text-end">
                                        <div style="margin-top: 30px;">
                                            <button id="btn-guardar-gasto" class="btn btn-danger btn-lg">
                                                <i class="fas fa-file-invoice-dollar me-2"></i> Registrar Gasto
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <?php include "vistas/general/footer.php" ?>
    </div>
</div>

<?php include "vistas/general/scripts.php"; ?>
<script src="<?php echo STATIC_URL; ?>js/gastos/nuevo-gasto.js" type="module"></script>
<script>
    $(function () {
        // Destruye cualquier instancia previa por si acaso
        $('#categorias-gastos').selectpicker('destroy');
        // Inicializa una sola vez
        $('#categorias-gastos').addClass('selectpicker').selectpicker({
            liveSearch: true,
            noneSelectedText: 'Selecciona un gasto'
        });
    });
</script>
</body>
</html>