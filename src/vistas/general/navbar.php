<?php
    /*  require_once '../config/permisos-enum.php'; // Ruta a tu archivo de Enum
     $controller_permiso = new PermisoController(); 
 */
    $escuela_p = $controller_permiso->validarAcceso(2, CPermiso::VER_ESCUELAS->value);
	$permisos_p = $controller_permiso->validarAcceso(2, CPermiso::VER_PANEL_PERMISOS->value);
	$usuarios_p = $controller_permiso->validarAcceso(2, CPermiso::VER_USUARIOS->value);
	$recibos_p = $controller_permiso->validarAcceso(2, CPermiso::VER_RECIBOS->value);
?>
<style>
	/* Transición suave al cambiar tema */
body {
    transition: background-color 0.3s ease, color 0.3s ease;
}

/* Tamaño del switch */
#darkmode-switch {
    width: 2.5rem;
    height: 1.25rem;
    cursor: pointer;
}

/* Ícono de luna */
#darkmode-switch ~ label {
    cursor: pointer;
    margin-left: .3rem;
}
</style>
<nav class="navbar navbar-expand navbar-light navbar-bg">
				<a class="sidebar-toggle js-sidebar-toggle">
					<i class="hamburger align-self-center"></i>
				</a>

				<div class="navbar-collapse collapse">
					<ul class="navbar-nav navbar-align">
					
						<!-- Switch Dark Mode -->
						<li class="nav-item d-flex align-items-center me-2">
							<div class="form-check form-switch mb-0" title="Modo oscuro">
								<input class="form-check-input" type="checkbox" id="darkmode-switch" role="switch">
								<label class="form-check-label" for="darkmode-switch">
									<i class="align-middle" data-feather="moon"></i>
								</label>
							</div>
						</li>
						<li class="nav-item dropdown">
							<a class="nav-icon dropdown-toggle d-inline-block d-sm-none" href="#" data-bs-toggle="dropdown">
								<i class="align-middle" data-feather="settings"></i>
							</a>

							<a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-bs-toggle="dropdown">
								<img src="<?php echo STATIC_URL; ?>img/avatars/<?= isset($_SESSION['foto_perfil']) && $_SESSION['foto_perfil']!=''  ? $_SESSION['foto_perfil'] : 'default.png'?>" id="foto_usuario_navbar" class="avatar img-fluid rounded me-1" alt="foto_usuario" /> <span class="text-dark" id="nombre_usuario_navbar" id_user="<?php echo $_SESSION["id"] ?>"><?php echo $_SESSION["nombre"]. " ". $_SESSION["apellido"]?></span>
							</a>
							<div class="dropdown-menu dropdown-menu-end">
								<div class="profile dropdown-item"><a href="<?php echo BASE_URL; ?>perfil">
									<div class="info">
										<p class="m-0 p-0"><b><?php echo $_SESSION["user"]; ?></b></p>
										<small class="text-muted"><?php 
										echo ($_SESSION['rol_nombre']);
										?></small>
									</div></a>
									
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
			const STATIC_URL = "<?php echo STATIC_URL; ?>";
			const USER_PERMISSIONS = {
				can_view_escuelas: <?php echo $escuela_p['estatus'] ? 'true' : 'false'; ?>,
				can_view_permisos: <?php echo $permisos_p['estatus'] ? 'true' : 'false'; ?>,
				can_view_usuarios: <?php echo $usuarios_p['estatus'] ? 'true' : 'false'; ?>,
				can_view_pagos: <?php echo $recibos_p['estatus'] ? 'true' : 'false'; ?>
			};

			// ── Dark Mode ─────────────────────────────────────────────
			document.addEventListener('DOMContentLoaded', function () {

const THEME_KEY = 'adminkit_theme';
const switchEl  = document.getElementById('darkmode-switch');

function aplicarTema(tema) {
    document.body.classList.toggle('dark', tema === 'dark');
    switchEl.checked = (tema === 'dark');

    const icon = switchEl.nextElementSibling.querySelector('i');
    if (icon) {
        icon.setAttribute('data-feather', tema === 'dark' ? 'sun' : 'moon');
        if (typeof feather !== 'undefined') feather.replace();
    }
}

const temaGuardado = localStorage.getItem(THEME_KEY) || 'light';
aplicarTema(temaGuardado);

switchEl.addEventListener('change', function () {
	const nuevoTema = this.checked ? 'dark' : 'light';
	localStorage.setItem(THEME_KEY, nuevoTema);
	aplicarTema(nuevoTema);
});

});
			</script>  
			<script type="module" src="<?php echo STATIC_URL; ?>js/config/configuraciones.js"></script>
