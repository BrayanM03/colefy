
<?php


$controller_permiso->validarAcceso(1, CPermiso::VER_RECIBOS->value);
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
                <div class="container-fluid p-0 animate__animated animate__fadeIn animate__faster">

                    <div class="row mb-2">
                        <div class="col-12 col-md-9">
                            <h1 class="h3 mb-3">Gestor de recibos y pagos </h1>
                        </div>
                        <div class="col-12 col-md-3 text-end">
                        <a href="<?php echo BASE_URL; ?>nuevo_recibo" style="text-decoration: none; color:white;"> <div class="btn btn-success" >
                               Nuevo recibo</div></a>
                        </div>
                    </div>



                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">En esta tabla estan los recibos realizados</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <label for="f-folio" style="color: #1ba594"><b>Folio</b></label>
                                            <input id="f-folio" class="form-control" placeholder="Folio REC..." type="number">
                                        </div> 
                                        <div class="col-md-3">
                                            <label for="f-ciclo" style="color: #1ba594"><b>Ciclo</b></label>
                                            <select id="f-ciclo" class="form-control selectpicker" data-live-search="false">
                                                <option value="">Selecciona un ciclo</option>
                                                <option value="1">2025/2026</option>
                                                <option value="2">2026/2027</option>
                                            </select>
                                        </div> 
                                    </div> 
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label style="color: #1ba594"><b>Alumno</b></label>
                                            <select id="f-alumno" class="form-control selectpicker" multiple placeholder="Selecciona un alumno"
                                            data-live-search="true">
                                        </select>
                                        </div> 
                                        <div class="col-md-2">
                                            <label style="color: #1ba594" for="f-tipo"><b>Tipo</b></label>
                                            <select id="f-tipo" class="form-control selectpicker" data-live-search="false">
                                                <option value="">Selecciona tipo</option>
                                                <option value="1">Contado</option>
                                                <option value="2">Parcial</option>
                                            </select>
                                        </div>   

                                        <div class="col-md-2">
                                            <label style="color: #1ba594"><b>Fecha inicial</b></label>
                                            <input type="date" id="f-fecha-inicio" class="form-control mb-2">
                                        </div>
                                        <div class="col-md-2">
                                        <label style="color: #1ba594"><b>Fecha final</b></label>
                                            <input type="date" id="f-fecha-fin" class="form-control">
                                        </div> 
                                        <div class="col-md-3">
                                            <label style="color: #1ba594"><b>Estatus</b></label>
                                            <select id="f-estatus" class="form-control selectpicker" data-live-search="true" multiple>
                                                <option value="">Selecciona estatus</option>
                                                <option value="1">Emitido</option>
                                                <option value="0">Cancelado</option>
                                                <option value="2">Abonado</option>
                                                <option value="3">Pagado</option>
                                                <option value="4">Vencido</option>
                                                <option value="5">Condonado</option>
                                                <option value="6">Pendiente</option>
                                                <option value="7">Verificando</option>
                                            </select>
                                        </div>
                                    </div>    
                                    <div class="row mb-4">
                                        <div class="col-md-3 d-flex align-items-center">
                                            <button id="btn-limpiar-filtros" style="color:black; margin-top:4px !important; margin-right:.7rem;" class="mr-2 btn btn-warning">Limpiar Filtros</button>
                                            <button id="btn-buscar" style="color:white; margin-top:4px !important;" class="ml-2 btn btn-info">Buscar</button>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12 col-md-12">
                                        <table id="recibos" class="table table-hover nowrap" style="width:100%">
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
    <script src="<?php echo STATIC_URL; ?>js/recibos/recibos.js" type="module"></script>
  

</body>

</html>