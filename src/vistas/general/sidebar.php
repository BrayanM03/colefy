<?php
 
    /* require_once '../config/permisos-enum.php'; // Ruta a tu archivo de Enum
    require_once '../controllers/PermisoController.php'; 
    $controller_permiso = new PermisoController(); 
    $controller_permiso->verificarSesion(); */
    $permiso_catalogo = $controller_permiso->validarAcceso(2, CPermiso::VER_CATALOGOS->value); 
    $permiso_recibos = $controller_permiso->validarAcceso(2, CPermiso::VER_RECIBOS->value);
  
?> 
<nav id="sidebar" class="sidebar js-sidebar">
    <div class="sidebar-content js-simplebar">
        <a class="sidebar-brand text-center mb-2" style="border-bottom:1px solid gray" href="<?php echo BASE_URL; ?>dashboard" id="role" role="<?php /* echo $rol */ ?>">
<!--    <img src="./img/logo_2.png" alt="" style="width:80px; border-radius:7px; margin-right:1rem;"><br> -->
        <img src="<?php echo STATIC_URL; ?>/img/colefy-logo.png" alt="logo" class="bord" style="width:150px; border-radius:7px; margin-right:1rem; margin-bottom:0px;"><br>
        <span style="font-size:10px; position: absolute; top:5.2rem; right: 80px; font-weight: 100">Gestion escolar</span>
        </a>

        <ul class="sidebar-nav">
            
            <?php /* if (verificarPermiso($con, $rol, 'index.php', 'ver')):  */?>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?php echo BASE_URL; ?>dashboard">
                        <i class="align-middle" data-feather="sliders"></i> <span class="align-middle">Panel</span>
                    </a>
                </li>
            <?php /* endif; */ ?>

            <?php if($permiso_recibos['estatus']): ?>
            <li class="sidebar-item accordion-button" aria-expanded="true">
                <a class="sidebar-link" href="<?php echo BASE_URL; ?>nuevo_recibo">
                    <i class="align-middle" data-feather="shopping-cart"></i> <span class="align-middle">Nuevo recibo</span>
                </a>
            </li>
            <?php  endif; ?>
           <!--  <li class="sidebar-item accordion-button" aria-expanded="true">
                <a class="sidebar-link" href="<?php echo BASE_URL; ?>asistencia">
                    <i class="align-middle" data-feather="clock"></i> <span class="align-middle">Asistencia</span>
                </a>
            </li> -->
            <div class="accordion" id="accordionExample2">
                <div class="accordion-item">
               
                    <li class="sidebar-item accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#collapseHistory" aria-expanded="true" aria-controls="collapseHistory">
                        <a class="sidebar-link d-flex align-items-center w-100" href="#">
                            <i class="align-middle" data-feather="folder"></i> <span class="align-middle">Historial</span>
                            <i class="ms-auto arrow-icon" data-feather="chevron-right"></i>

                        </a>
                    </li>
       
                    <div id="collapseHistory" class="accordion-collapse collapse" style="margin-left:13px;" aria-labelledby="headingHistory" data-bs-parent="#accordionExample2">
                        <div class="accordion-body">
                           <!--  <li class="sidebar-item">
                                <a class="sidebar-link" href="documentos-subidos.php">
                                    <i class="align-middle" data-feather="book"></i> <span class="align-middle">Documentos subidos</span>
                                </a>
                            </li> -->
                           <?php if($permiso_recibos['estatus']){?>
                            <li class="sidebar-item">
                                <a class="sidebar-link" href="<?php echo BASE_URL; ?>recibos">
                                    <i class="align-middle" data-feather="book"></i> <span class="align-middle">Recibos</span>
                                </a>
                            </li>
                            <?php } ?>
                        </div>
                    </div>

                </div>
            </div>

            <?php 
            if($permiso_catalogo['estatus']){ ?>
            <div class="accordion" id="accordionExample3">
                <div class="accordion-item">

                <li class="sidebar-item accordion-button collapsed" 
                data-bs-toggle="collapse" data-bs-target="#collapseCatalogos" 
                aria-expanded="true" aria-controls="collapseCatalogos">
                <a class="sidebar-link d-flex align-items-center w-100" href="#">
                        <i class="align-middle" data-feather="clipboard"></i> 
                        <span class="align-middle ms-2">Catalogos</span>
                        <i class="ms-auto arrow-icon" data-feather="chevron-right"></i>
                    </a>
                </li>

                    <div id="collapseCatalogos" class="accordion-collapse collapse" style="margin-left:13px;" aria-labelledby="headingCatalogo" data-bs-parent="#accordionExample3">
                        <div class="accordion-body">
                            <!-- <li class="sidebar-item">
                                <a class="sidebar-link" href="<?php echo BASE_URL; ?>flujo">
                                    <i class="align-middle" data-feather="share-2"></i> <span class="align-middle">Iniciar flujo</span>
                                </a>
                            </li> -->
                            <li class="sidebar-item">
                                <a class="sidebar-link" href="<?php echo BASE_URL; ?>grupos">
                                    <i class="align-middle" data-feather="star"></i> <span class="align-middle">Grupos</span>
                                </a>
                            </li>
                        <?php /* if (verificarPermiso($con, $rol, 'alumnos.php', 'ver')):  */?>
                            <li class="sidebar-item">
                                <a class="sidebar-link" href="<?php echo BASE_URL; ?>alumnos">
                                    <i class="align-middle" data-feather="users"></i> <span class="align-middle">Alumnos</span>
                                </a>
                            </li>
                            <?php /* endif; */ ?>
                            <?php /* if (verificarPermiso($con, $rol, 'profesores.php', 'ver')): */ ?>
                            <li class="sidebar-item">
                                <a class="sidebar-link" href="<?php echo BASE_URL; ?>profesores">
                                    <i class="align-middle" data-feather="user"></i> <span class="align-middle">Profesores</span>
                                </a>
                            </li>
                            <?php /* endif; */ ?>
                            <?php /* if (verificarPermiso($con, $rol, 'materias.php', 'ver')): */ ?>
                            <li class="sidebar-item">
                                <a class="sidebar-link" href="<?php echo BASE_URL; ?>materias">
                                    <i class="align-middle" data-feather="book"></i> <span class="align-middle">Materias</span>
                                </a>
                            </li>
                            <?php /* endif; */ ?>
                            <?php /* if (verificarPermiso($con, $rol, 'horarios.php', 'ver')):  */?>
                            <li class="sidebar-item accordion-button" aria-expanded="true">
                                <a class="sidebar-link" href="<?php echo BASE_URL; ?>horarios">
                                    <i class="align-middle" data-feather="clock"></i> <span class="align-middle">Horarios</span>
                                </a>
                            </li>
                            <?php /* endif; */ ?>
                        </div>
                    </div>

                </div>
            </div>
            <?php }?>
            <!-- <li class="sidebar-item">
                <a class="sidebar-link" href="pages-blank.html">
                    <i class="align-middle" data-feather="book"></i> <span class="align-middle">Blank</span>
                </a>
            </li> -->
           
        </ul>

        <div class="sidebar-cta">
            <div class="sidebar-cta-content text-center">
                <img src="<?= STATIC_URL . 'img/escuelas/' . $_SESSION['logo_escuela'];?>" style="width: 90px; border-radius: 8px;">
                <strong class="d-inline-block mb-2 mt-3"><?= $_SESSION['escuela']?></strong>
            </div>
            <div class="sidebar-cta-content">
                <strong class="d-inline-block mb-2">Sistema en proceso</strong>
                <div class="mb-3 text-sm">
                    Algunas funciones estan en proceso de desarollo.
                </div>
                <!-- <div class="d-grid">
                    <a href="upgrade-to-pro.html" class="btn btn-primary">Upgrade to Pro</a>
                </div> -->
            </div>
        </div>
    </div>
</nav>
<script>
    document.addEventListener("DOMContentLoaded", function () {
    // Obtener la ruta actual de la URL y el nombre del archivo
    const currentPath = window.location.pathname;
    const currentPage = currentPath.substring(currentPath.lastIndexOf('/') + 1); // Extraer solo el nombre del archivo

    // Seleccionar todos los enlaces en el sidebar
    const sidebarLinks = document.querySelectorAll(".sidebar-link");

    sidebarLinks.forEach(link => {
        // Obtener el nombre del archivo en href para cada enlace
        const linkPath = link.getAttribute("href");
        const linkPage = linkPath.substring(linkPath.lastIndexOf('/') + 1); // Extraer solo el nombre del archivo

        // Verificar si el archivo del enlace coincide con el archivo de la URL actual
        if (linkPage === currentPage) {
            // Agregar clase 'active' al elemento actual
            link.parentElement.classList.add("active");

            // Abrir el acordeón si es parte de uno
            const accordionBody = link.closest(".accordion-collapse");
            if (accordionBody) {
                accordionBody.classList.remove("collapse"); // Quitar "collapse" para abrir
                accordionBody.classList.add("show"); // Añadir "show" para mantener abierto
            }
        }
    });
});


</script>