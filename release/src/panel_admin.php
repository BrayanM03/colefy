<?php

$redireccion = $controller_permiso->redirigirMaestros($_SESSION['rol']);

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

    <title>Panel | Colefy</title>

    <link href="<?php echo STATIC_URL; ?>css/app.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.dataTables.min.css" />
    <style>

/* Estilos para las tarjetas de métricas */
.stat-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: none;
    border-radius: 10px;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    cursor:pointer;
}

/* Iconos de métricas */
.stat {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: rgba(59, 125, 221, 0.1); /* Color suave de fondo */
}

/* Colores específicos para iconos */
.text-primary .stat { background: rgba(59, 125, 221, 0.1); }
.text-info .stat { background: rgba(23, 162, 184, 0.1); }
.text-warning .stat { background: rgba(252, 185, 44, 0.1); }
.text-success .stat { background: rgba(40, 167, 69, 0.1); }

/* Ajustes para la tabla */
.table thead th {
    background-color: #f8f9fa;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.05em;
    font-weight: 700;
    border-top: none;
}

.card-header {
    background-color: transparent;
    border-bottom: 1px solid rgba(0,0,0,.05);
}

    </style>
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
    <h1 class="h3 mb-3">Panel de Control: <strong>Administración Escolar</strong></h1>

    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col mt-0">
                            <h5 class="card-title">Estudiantes</h5>
                        </div>
                        <div class="col-auto">
                            <div class="stat text-primary">
                                <i class="align-middle" data-feather="users"></i>
                            </div>
                        </div>
                    </div>
                    <h1 class="mt-1 mb-3">1,250</h1>
                    <div class="mb-0">
                        <span class="text-success"> <i class="mdi mdi-arrow-bottom-right"></i> +5.2% </span>
                        <span class="text-muted">Desde el mes pasado</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card stat-card shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col mt-0">
                            <h5 class="card-title">Profesores</h5>
                        </div>
                        <div class="col-auto">
                            <div class="stat text-info">
                                <i class="align-middle" data-feather="briefcase"></i>
                            </div>
                        </div>
                    </div>
                    <h1 class="mt-1 mb-3">48</h1>
                    <div class="mb-0">
                        <span class="text-muted">8 Departamentos</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col mt-0">
                            <h5 class="card-title">Grupos Activos</h5>
                        </div>
                        <div class="col-auto">
                            <div class="stat text-warning">
                                <i class="align-middle" data-feather="layers"></i>
                            </div>
                        </div>
                    </div>
                    <h1 class="mt-1 mb-3">24</h1>
                    <div class="mb-0">
                        <span class="text-danger"> <i class="mdi mdi-arrow-bottom-right"></i> -1 </span>
                        <span class="text-muted">Ciclo actual</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stat-card shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col mt-0">
                            <h5 class="card-title">Ingresos Mensuales</h5>
                        </div>
                        <div class="col-auto">
                            <div class="stat text-success">
                                <i class="align-middle" data-feather="dollar-sign"></i>
                            </div>
                        </div>
                    </div>
                    <h1 class="mt-1 mb-3">$45,200</h1>
                    <div class="mb-0">
                        <span class="text-success"> <i class="mdi mdi-arrow-bottom-right"></i> Al corriente </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
    <div class="col-12 col-lg-4 d-flex">
        <div class="card flex-fill w-100 shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Distribución por Nivel</h5>
            </div>
            <div class="card-body d-flex">
                <div class="align-self-center w-100">
                    <div class="py-3">
                        <div class="chart chart-xs">
                            <canvas id="chartjs-dashboard-pie"></canvas>
                        </div>
                    </div>
                    <table class="table mb-0">
                        <tbody>
                            <tr>
                                <td><i class="fas fa-circle text-primary fa-fw"></i> Primaria</td>
                                <td class="text-end">450</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-circle text-warning fa-fw"></i> Secundaria</td>
                                <td class="text-end">380</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-circle text-danger fa-fw"></i> Preparatoria</td>
                                <td class="text-end">420</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-8 d-flex">
        <div class="card flex-fill shadow-sm">
            <div class="card-header">
                <h5 class="card-title mb-0">Alertas del Sistema</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                        <div class="d-flex align-items-center">
                            <div class="stat text-danger me-3" style="width: 35px; height: 35px;">
                                <i data-feather="alert-circle"></i>
                            </div>
                            <div>
                                <strong>5 Profesores</strong> no han pasado asistencia hoy.
                                <div class="text-muted small">Reporte diario de control.</div>
                            </div>
                        </div>
                        <button class="btn btn-sm btn-outline-primary">Notificar</button>
                    </li>
                    <hr>
                    <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                        <div class="d-flex align-items-center">
                            <div class="stat text-warning me-3" style="width: 35px; height: 35px;">
                                <i data-feather="dollar-sign"></i>
                            </div>
                            <div>
                                <strong>12 Pagos pendientes</strong> de vencer mañana.
                                <div class="text-muted small">Corte de caja preventivo.</div>
                            </div>
                        </div>
                        <button class="btn btn-sm btn-outline-primary">Ver lista</button>
                    </li>
                    <hr>
                    <li class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                        <div class="d-flex align-items-center">
                            <div class="stat text-info me-3" style="width: 35px; height: 35px;">
                                <i data-feather="user-plus"></i>
                            </div>
                            <div>
                                <strong>8 Nuevas solicitudes</strong> de inscripción.
                                <div class="text-muted small">Pendientes de revisión de documentos.</div>
                            </div>
                        </div>
                        <button class="btn btn-sm btn-primary">Revisar</button>
                    </li>
                </ul>
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

    <!-- Mis scripts -->
    <script src="<?php echo STATIC_URL; ?>js/solicitudes/traer-lista-solicitudes.js"></script>
    <script src="<?php echo STATIC_URL; ?>js/solicitudes/editar-solicitud.js"></script>
    <!-- <script src="js/usuarios/eliminar-usuario.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('chartjs-dashboard-pie');

    if (!ctx) return;

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Ventas', 'Gastos', 'Ganancia'],
            datasets: [{
                data: [450, 380, 420],
                backgroundColor: [
                    '#4CAF50',
                    '#F44336',
                    '#2196F3'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
});
</script>

</body>

</html>