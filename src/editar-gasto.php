<?php
require_once  __DIR__ .  '/../controllers/GastoController.php';
require_once __DIR__ . '/../controllers/GrupoController.php';

$controller_gasto = new GastoController();
$controller_grupo = new GrupoController();

// Validar permiso específico para gastos
$controller_permiso->validarAcceso(1, CPermiso::CREAR_GASTOS->value);
$resp_gasto = $controller_gasto->obtener_gasto($_GET['id_gasto'], 2);

// Si los gastos también se segmentan por ciclo (para reportes anuales)
$data_categorias = $controller_gasto->combo_categorias_gastos(2);
$data_ciclos = $controller_grupo->combo_ciclos();
$ciclos_escolares = $data_ciclos['data'];
$categorias_gastos = $data_categorias['data'];
$data_gastos = $resp_gasto['data'];

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
                        <h1 class="h3 mb-3">Editar Gasto</h1>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-12 col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-8">
                                        <h5 class="card-title mb-0">Desde este formulario puedes editar el gasto</h5>
                                    </div>
                                    <div class="col-2">
                                        <label for="fecha">Fecha del gasto</label>
                                        <input class="form-control" type="date" id="fecha" value="<?= $data_gastos['fecha'] ?>">
                                        <small id="small_fecha" style="color:tomato;"></small>

                                    </div>
                                    <div class="col-2">
                                        <label for="ciclo">Ciclo asociado</label>
                                        <select class="form-control" id="ciclo" value="<?= $data_gastos['id_ciclo'] ?>">
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
                                        <input type="text" id="proveedor" value="<?= $data_gastos['proveedor'] ?>" class="form-control" placeholder="Ej. Papelería Martínez o CFE">
                                        <small id="small_proveedor" style="color:tomato;"></small>
                                    </div>
                                    <div class="col-12 col-md-6 mb-2">
                                        <label for="tipo_comprobante">Tipo de comprobante</label>
                                        <select id="tipo_comprobante" class="form-control selectpicker" value="<?= $data_gastos['tipo_comprobante'] ?>">
                                            <?php
                                            $arreglo_comprobantes = [
                                                'factura_cfdi'    => 'Factura (CFDI) ✓ Deducible',
                                                'nota_remision'   => 'Nota de remisión',
                                                'ticket_recibo'   => 'Ticket / Recibo',
                                                'sin_comprobante' => 'Sin comprobante oficial'
                                            ];

                                            foreach ($arreglo_comprobantes as $key => $value) {
                                                $selected_type = ($data_gastos['tipo_comprobante'] == $key) ? 'selected' : '';
                                                echo "<option value='$key' $selected_type>$value</option>";
                                            }
                                            ?>

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
                                        <select id="categorias-gastos" class="form-control" data-live-search="true" title="Selecciona un gasto">
                                            <option value="">Seleccione una categoria</option>
                                            <?php foreach ($agrupado as $grupo): ?>
                                                <optgroup label="<?= htmlspecialchars($grupo['nombre']) ?>">
                                                    <?php foreach ($grupo['items'] as $sub):
                                                        $selected_category = ($data_gastos['id_subcategoria'] == $sub['id']) ? 'selected' : ''
                                                    ?>
                                                        <option value="<?= $sub['id'] ?>" <?= $selected_category ?>>
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

                                        <!-- Preview del comprobante actual -->
                                        <?php if (!empty($data_gastos['comprobante_archivo'])): ?>
                                            <div id="preview-comprobante-actual" class="mt-2">
                                                <small class="text-muted d-block mb-1">Comprobante actual:</small>
                                                <?php
                                                $ruta      = BASE_URL . $data_gastos['comprobante_archivo'];
                                                $extension = strtolower(pathinfo($data_gastos['comprobante_archivo'], PATHINFO_EXTENSION));
                                                ?>
                                                <?php if ($extension === 'pdf'): ?>
                                                    <!-- Preview PDF -->
                                                    <div class="d-flex align-items-center gap-2 p-2 border rounded bg-light">
                                                        <i class="fa-solid fa-file-pdf fa-2x" style="color: tomato;"></i>
                                                        <div>
                                                            <span class="d-block text-truncate" style="max-width:200px;">
                                                                <?= basename($data_gastos['comprobante_archivo']) ?>
                                                            </span>
                                                            <a href="<?= $ruta ?>" target="_blank" class="btn btn-sm btn-outline-danger mt-1">
                                                                <i class="fa-solid fa-eye"></i> Ver PDF
                                                            </a>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <!-- Preview Imagen -->
                                                    <img src="<?= $ruta ?>"
                                                        id="preview-img-actual"
                                                        alt="Comprobante"
                                                        class="img-thumbnail mt-1"
                                                        style="max-height: 150px; cursor:pointer;"
                                                        onclick="window.open('<?= $ruta ?>', '_blank')">
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Preview del archivo NUEVO que seleccione el usuario -->
                                        <div id="preview-nuevo-comprobante" class="mt-2" style="display:none;">
                                            <small class="text-muted d-block mb-1">Nuevo comprobante seleccionado:</small>
                                            <div id="preview-nuevo-contenido"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-12 col-md-6">
                                        <label for="concepto">Concepto específico</label>
                                        <input type="text" class="form-control" id="concepto" value="<?= $data_gastos['concepto'] ?>" placeholder="Ej. Pago de luz mes de mayo">
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label for="monto">Monto Total</label>
                                        <input type="number" class="form-control" id="monto" placeholder="0.00" value="<?= $data_gastos['monto'] ?>">
                                        <small id="small_monto" style="color:tomato;"></small>
                                    </div>
                                    <div class="col-12 col-md-3">
                                        <label for="forma_pago">Método de salida</label>
                                        <select id="forma_pago" class="form-control selectpicker" value="<?= $data_gastos['forma_pago'] ?>">
                                            <?php
                                            $arreglo_formas_pago = [
                                                'efectivo'    => 'Efectivo (caja chica)',
                                                'transferencia'   => 'Transferencia Bancaria',
                                                'tarjeta'   => 'Tarjeta de Débito/Credito',
                                                'cheque' => 'Cheque'
                                            ];

                                            foreach ($arreglo_formas_pago as $key => $value) {
                                                $selected_type = ($data_gastos['forma_pago'] == $key) ? 'selected' : '';
                                                echo "<option value='$key' $selected_type>$value</option>";
                                            }
                                            ?>


                                        </select>
                                    </div>
                                </div>

                                <div class="row mt-4">
                                    <div class="col-12 col-md-8">
                                        <label for="observaciones">Observaciones adicionales</label>
                                        <textarea class="form-control" id="observaciones" rows="2" placeholder="Detalles extra sobre el gasto..."><?= $data_gastos['observaciones'] ?></textarea>
                                    </div>
                                    <div class="col-12 col-md-4 text-end">
                                        <div style="margin-top: 30px;">
                                            <button id="btn-actualizar-gasto" class="btn btn-primary btn-lg" onclick="actualizarGasto(<?= $_GET['id_gasto'] ?>)">
                                                <i class="fas fa-file-invoice-dollar me-2"></i> Actualizar Gasto
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
<script src="<?php echo STATIC_URL; ?>js/gastos/verificar.js" type="module"></script>
<script src="<?php echo STATIC_URL; ?>js/gastos/editar.js" type="module"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    $('#categorias-gastos').selectpicker('refresh');
})
</script>
</body>

</html>