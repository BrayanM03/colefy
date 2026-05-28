<?php

$redireccion = $controller_permiso->redirigirMaestros($_SESSION['id_rol']);
include "vistas/general/header.php";

?>
<style>
    /* Skeleton loader */
.skeleton {
    display       : inline-block;
    background    : linear-gradient(90deg, #e0e0e0 25%, #f0f0f0 50%, #e0e0e0 75%);
    background-size: 200% 100%;
    animation     : skeleton-shimmer 1.5s infinite;
    border-radius : 4px;
}
.skeleton-text    { width: 80px;  height: 2rem;   }
.skeleton-text-sm { width: 120px; height: 1rem;   }
.skeleton-line    { width: 100%;  height: 3.5rem; }

@keyframes skeleton-shimmer {
    0%   { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* Dark mode */
body.dark .skeleton {
    background: linear-gradient(90deg, #2c3e50 25%, #34495e 50%, #2c3e50 75%);
    background-size: 200% 100%;
}
</style>
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
                    <h1 class="mt-1 mb-3" id="stat-estudiantes">1,250</h1>
                    <div class="mb-0" id="stat-estudiantes-sub">
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
                    <h1 class="mt-1 mb-3" id="stat-profesores">48</h1>
                    <div class="mb-0" id="stat-profesores-sub">
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
                    <h1 class="mt-1 mb-3" id="stat-grupos">24</h1>
                    <div class="mb-0" id="stat-grupos-sub">
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
                    <h1 class="mt-1 mb-3" id="stat-ingresos">$45,200</h1>
                    <div class="mb-0" id="stat-ingresos-sub">
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
                        <tbody id="distribucion-nivel">
                           
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
                <ul class="list-group list-group-flush" id="lista-alertas">
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
    <script src="<?php echo STATIC_URL; ?>js/panel/directivos.js"></script>
    <!-- <script src="js/usuarios/eliminar-usuario.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
/* document.addEventListener('DOMContentLoaded', () => {
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
}); */
</script>

</body>

</html>