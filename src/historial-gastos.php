<?php

$controller_permiso->validarAcceso(1, CPermiso::VER_RECIBOS->value);
require_once  __DIR__ .  '/../controllers/GastoController.php';
include "vistas/general/header.php";

$controller_gasto = new GastoController();
$data_categorias = $controller_gasto->combo_categorias_gastos(2);
$categorias_gastos = $data_categorias['data'];

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
                <div class="container-fluid p-0 animate__animated animate__fadeIn animate__faster">

                    <div class="row mb-2">
                        <div class="col-12 col-md-9">
                            <h1 class="h3 mb-3">Gestor de gastos </h1>
                        </div>
                        <div class="col-12 col-md-3 text-end">
                            <a href="<?php echo BASE_URL; ?>nuevo_gasto" style="text-decoration: none; color:white;">
                                <div class="btn btn-success">
                                    Nuevo gasto</div>
                            </a>
                        </div>
                    </div>



                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">En esta tabla estan los gastos registrados</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <label for="f-folio" style="color: #1ba594"><b>Folio</b></label>
                                            <input id="f-folio" class="form-control" placeholder="GST-00001..." type="number">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="f-ciclo" style="color: #1ba594"><b>Ciclo escolar</b></label>
                                            <select id="f-ciclo" class="form-control selectpicker" data-live-search="false">
                                                <option value="">Todos los ciclos</option>
                                                <option value="1">2025/2026</option>
                                                <option value="2">2026/2027</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
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
                                            <label for="f-subcategoria" style="color: #1ba594"><b>Subategoría</b></label>
                                            <select id="f-subcategoria" class="form-control selectpicker" data-live-search="true" title="Todas las categorías">
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
                                        <div class="col-md-3">
                                            <label for="f-forma-pago" style="color: #1ba594"><b>Forma de pago</b></label>
                                            <select id="f-forma-pago" class="form-control selectpicker" data-live-search="false">
                                                <option value="">Todas</option>
                                                <option value="efectivo">Efectivo</option>
                                                <option value="transferencia">Transferencia</option>
                                                <option value="tarjeta">Tarjeta débito</option>
                                                <option value="deposito">Deposito</option>
                                                <option value="cheque">Cheque</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <label for="f-comprobante" style="color: #1ba594"><b>Tipo de comprobante</b></label>
                                            <select id="f-comprobante" class="form-control selectpicker" data-live-search="false">
                                                <option value="">Todos</option>
                                                <option value="factura_cfdi">Factura (CFDI)</option>
                                                <option value="nota_remision">Nota de remisión</option>
                                                <option value="ticket_recibo">Ticket / Recibo</option>
                                                <option value="sin_comprobante">Sin comprobante</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label style="color: #1ba594"><b>Fecha inicial</b></label>
                                            <input type="date" id="f-fecha-inicio" class="form-control">
                                        </div>
                                        <div class="col-md-2">
                                            <label style="color: #1ba594"><b>Fecha final</b></label>
                                            <input type="date" id="f-fecha-fin" class="form-control">
                                        </div>
                                        <div class="col-md-2">
                                            <label for="f-estatus" style="color: #1ba594"><b>Estatus</b></label>
                                            <select id="f-estatus" class="form-control selectpicker">
                                                <option value="">Todos</option>
                                                <option value="activo">Activo</option>
                                                <option value="cancelado">Cancelado</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row mb-4">
                                        <div class="col-md-3 d-flex align-items-center">
                                            <button id="btn-limpiar-filtros"
                                                style="color:black; margin-top:4px !important; margin-right:.7rem;"
                                                class="btn btn-warning">
                                                <i class="fa-solid fa-broom"></i> Limpiar
                                            </button>
                                            <button id="btn-buscar"
                                                style="color:white; margin-top:4px !important;"
                                                class="btn btn-info">
                                                <i class="fa-solid fa-magnifying-glass"></i> Buscar
                                            </button>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12 col-md-12">
                                            <table id="gastos" class="table table-hover nowrap" style="width:100%">
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
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.6/dist/css/tom-select.css" rel="stylesheet">
    <script src="<?php echo STATIC_URL; ?>js/DataTable/datatables-init.js" type="module"></script>
    <script src="<?php echo STATIC_URL; ?>js/gastos/gastos.js" type="module"></script>


</body>

</html>