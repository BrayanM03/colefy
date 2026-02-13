<?php
    /*  require_once '../config/permisos-enum.php'; // Ruta a tu archivo de Enum
     $controller_permiso = new PermisoController(); 
 */
    $escuela_p = $controller_permiso->validarAcceso(2, CPermiso::VER_ESCUELAS->value);
	$permisos_p = $controller_permiso->validarAcceso(2, CPermiso::VER_PANEL_PERMISOS->value);
	$usuarios_p = $controller_permiso->validarAcceso(2, CPermiso::VER_USUARIOS->value);
	$recibos_p = $controller_permiso->validarAcceso(2, CPermiso::VER_RECIBOS->value);
?>
<nav class="navbar navbar-expand navbar-light navbar-bg">
				<a class="sidebar-toggle js-sidebar-toggle">
					<i class="hamburger align-self-center"></i>
				</a>

				<div class="navbar-collapse collapse">
					<ul class="navbar-nav navbar-align">
					
						
						<li class="nav-item dropdown">
							<a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#" data-bs-toggle="dropdown">
								<i class="align-middle" data-feather="settings"></i>
							</a>

							<a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-bs-toggle="dropdown">
								<img src="<?php echo STATIC_URL; ?>/img/logo.png" class="avatar img-fluid rounded me-1" alt="Charles Hall" /> <span class="text-dark" id="user-data"  id_user="<?php echo $_SESSION["id"] ?>"><?php echo $_SESSION["nombre"]. " ". $_SESSION["apellido"]?></span>
							</a>
							<div class="dropdown-menu dropdown-menu-end">
								<div class="profile dropdown-item">
									<div class="info">
										<p class="m-0 p-0"><b><?php echo $_SESSION["user"]; ?></b></p>
										<small class="text-muted"><?php 
										if($_SESSION["rol"]==3){
											echo 'Estudiante';
										}else if($_SESSION["rol"]==1){
											echo 'Administrador';
										}else if($_SESSION["rol"]==2){
											echo 'Maestro';
										}; 
										?></small>
									</div>
									
								</div>
								<div class="dropdown-item" style="cursor:pointer" id="mostrar-configuraciones"><i class="align-middle me-1" data-feather="settings"></i> Configuración</div>
								<!--
								<a class="dropdown-item" href="pages-profile.html"><i class="align-middle me-1" data-feather="user"></i> Perfil</a>
								<a class="dropdown-item" href="#"><i class="align-middle me-1" data-feather="pie-chart"></i> Analiticas</a>
								<div class="dropdown-divider"></div>--><!--
								<a class="dropdown-item" href="index.html"><i class="align-middle me-1" data-feather="settings"></i> Configuración</a>
								<a class="dropdown-item" href="#"><i class="align-middle me-1" data-feather="help-circle"></i> Centro de Ayuda</a>
								<div class="dropdown-divider"></div> -->
								<a class="dropdown-item" href="<?php echo BASE_URL; ?>servidor/database/cerrar-sesion.php"><i class="align-middle me-1" data-feather="log-out"></i> Cerrar Sesion</a>
							</div>
						</li>
					</ul>
				</div>
			</nav>  
			<script>
			const BASE_URL = "<?php echo BASE_URL; ?>";
			const USER_PERMISSIONS = {
				can_view_escuelas: <?php echo $escuela_p['estatus'] ? 'true' : 'false'; ?>,
				can_view_permisos: <?php echo $permisos_p['estatus'] ? 'true' : 'false'; ?>,
				can_view_usuarios: <?php echo $usuarios_p['estatus'] ? 'true' : 'false'; ?>,
				can_view_pagos: <?php echo $recibos_p['estatus'] ? 'true' : 'false'; ?>
			};
			</script>  
			<script type="module" src="<?php echo STATIC_URL; ?>js/config/configuraciones.js"></script>
