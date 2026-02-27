<?php
session_start();

require_once 'controllers/PermisoController.php';
require_once 'config/config.php';

$controller_permiso = new PermisoController(); 

// Obtener la URL, por ejemplo: "usuarios/editar/6"
$url = isset($_GET['url']) ? $_GET['url'] : 'dashboard';
$datos = explode('/', $url);

$modulo = $datos[0];
$accion = isset($datos[1]) ? $datos[1] : null;
$id     = isset($datos[2]) ? $datos[2] : null;


if ($modulo === 'login') {
    include 'src/login.php'; 
    exit();
}



// 4. VERIFICAR SESIÓN (Para todo lo que no sea login)
$controller_permiso->verificarSesion();

// 5. DETERMINAR QUÉ ARCHIVO DE VISTA CARGAR e INICIALIZAR VARS
$vista_a_cargar = "";

$necesita_datatables = true; // Por defecto Si
$necesita_sweetalert = true; // Por defecto Si
$necesita_fontawesome = true;
$necesita_chartjs = false; // Por defecto no
$necesita_animatecss = true; // Por defecto no
$necesita_bootstrap_select = false; // Por defecto no

$css_especificos = [];
$js_especificos = [];
$titulo_pagina = "Colefy";

//ROUTER
switch ($modulo) {

    case 'dashboard':        
        $titulo_vista = 'Dashboard';
        $necesita_datatables = false;
        $css_especificos[] = ASSET_PANEL_DIRECTIVOS_CSS;
        $vista_a_cargar = 'src/panel-directivos.php';
    break;

    case 'panel_maestros':     
        $titulo_vista = 'Panel maestros';
        $necesita_datatables = false;
        $css_especificos[] = ASSET_PANEL_MAESTROS_CSS;
        $vista_a_cargar = 'src/panel-maestros.php';
    break;

    case 'nuevo_recibo':        
        $titulo_vista = 'Nuevo recibo';
        $necesita_bootstrap_select =true;
        $css_especificos[] = ASSET_NUEVO_RECIBO_CSS; 
        $vista_a_cargar = 'src/nuevo-recibo.php';
    break;

    case 'recibos':        
        if ($accion === 'pdf' && $id) {
            $_GET['id_recibo'] = $id; // Lo inyectamos para que el archivo lo use
            $vista_a_cargar = 'config/recibo.php';
        }else if($accion === 'editar' && $id){
            $titulo_vista = 'Editar recibo';
            $_GET['id_recibo'] = $id;
            $css_especificos[] = ASSET_EDITAR_RECIBO_CSS;
            $css_especificos[] = ASSET_MENU_NAVEGACION_RECIBO_CSS;
            $necesita_bootstrap_select = true;
            $vista_a_cargar = 'src/editar-recibo.php';
        }else {
            $necesita_bootstrap_select = true;
            $titulo_vista = 'Recibos';
            $necesita_animatecss = true;
            $vista_a_cargar = 'src/historial-recibos.php';
        }
    break;

    //CATALOGOS
    case 'grupos':
        $titulo_vista = 'Grupos';
        $vista_a_cargar = 'src/grupos.php';
    break;

    case 'editar_grupo':        
        $titulo_vista = 'Editar grupo';

        if ($accion === 'edit' && $id) {
            $necesita_bootstrap_select=true;
            $_GET['id_grupo'] = $id; // Lo inyectamos para que el archivo lo use
            $vista_a_cargar = 'src/editar-grupo.php';
        } else {
            $vista_a_cargar = 'src/grupos.php';
        }
    break;

    case 'nuevo-grupo':
        $titulo_vista = 'Nuevo grupo';
        $vista_a_cargar = 'src/nuevo-grupo.php';
    break;

    case 'alumnos':
        $titulo_vista = 'Alumnos';
        $vista_a_cargar = 'src/alumnos.php';
    break;  

    case 'profesores':
        $titulo_vista = 'Profesores';
        $vista_a_cargar = 'src/profesores.php';
    break;

    case 'materias':
        $titulo_vista = 'Materias';
        $vista_a_cargar = 'src/materias.php';
    break;
        
    case 'horarios':
        $titulo_vista = 'Horarios';
        $vista_a_cargar = 'src/horarios.php';
    break;

    case 'asignar_horario':
        $titulo_vista = 'Asignar horario';
        $necesita_bootstrap_select = true;
        $vista_a_cargar = 'src/asignar-horario.php';
    break;
   
    case 'nuevo_horario':
        $titulo_vista = 'Nuevo horario';
        $necesita_bootstrap_select = true;
        $vista_a_cargar = 'src/nuevo-horario.php';
    break;

    case 'usuarios':

        if($accion=='registrar'){
            $titulo_vista = 'Registrar usuario';
            $necesita_bootstrap_select = true;
            $vista_a_cargar = 'src/registrar-usuario.php';
        }
        else if ($accion === 'editar' && $id) {
            $titulo_vista = 'Editar usuario';
            $css_especificos[] = ASSET_EDITAR_USUARIO_CSS; 
            $css_especificos[] = ASSET_MENU_USUARIO_CSS; 
            $_GET['id_usuario'] = $id;
            $vista_a_cargar = 'src/editar-usuario.php';
        } else {
            $titulo_vista = 'Usuarios';
            $vista_a_cargar = 'src/usuarios.php';
        }
    break;

    case 'panel_permisos':
        $titulo_vista = 'Panel permisos';
        $vista_a_cargar = 'src/panel-permisos.php';
    break;

    case 'permisos':
        $titulo_vista = 'Permisos';
        $css_especificos[] = ASSET_PERMISOS_CSS; 
        $vista_a_cargar = 'src/permisos.php';
    break;

    case 'permisos_usuarios':
        $titulo_vista = 'Permisos';
        $css_especificos[] = ASSET_PERMISOS_CSS; 
        $vista_a_cargar = 'src/permisos-usuarios.php';
    break;
 
    case 'roles':
        if($accion == 'editar' && $id){
            $css_especificos[] = ASSET_EDITAR_ROL_CSS; 
            $_GET['id_rol'] = $id;
            $titulo_vista = 'Editar rol';
            $vista_a_cargar = 'src/permisos-roles.php';
        }else{
            $titulo_vista = 'Roles';
            $vista_a_cargar = 'src/roles.php';
        }
    break;

    case 'escuelas':
        if($accion == 'editar' && $id && file_exists('src/editar-escuela.php')){
            $css_especificos[] = ASSET_EDITAR_ROL_CSS; 
            $_GET['id_escuela'] = $id;
            $titulo_vista = 'Editar escuela';
            $vista_a_cargar = 'src/editar-escuela.php';
        }else if($accion=='agregar'&& file_exists('src/agregar-escuela.php')){
            $titulo_vista = 'Agregar escuela';
            $vista_a_cargar = 'src/agregar-escuela.php';
        }
        else{
            $titulo_vista = 'Escuelas ' . $accion;
            $vista_a_cargar = 'src/escuelas.php';
        }
    break;

    case 'perfil':
        $titulo_vista = 'Perfil';
        $vista_a_cargar = 'src/perfil.php';
    break;

    case 'flujo':     
        $titulo_vista = 'Iniciar flujo';
        $necesita_datatables = false;
        $css_especificos[] = ASSET_FLUJO_CSS;
        $vista_a_cargar = 'src/flujo.php';
    break;

    case 'not_found':
        $titulo_pagina = 'Pagina no encontrada 404';
        $necesita_datatables = false;
        $necesita_animatecss = false;
        $necesita_sweetalert = false; // Por defecto Si
        $necesita_fontawesome = false;
        $vista_a_cargar = 'src/vistas/error/not_found.php';

    break;

    default:
        $titulo_pagina = 'Pagina no encontrada 404';
        $necesita_datatables = false;
        $necesita_animatecss = false;
        $necesita_sweetalert = false; // Por defecto Si
        $necesita_fontawesome = false;
        $vista_a_cargar = 'src/vistas/error/not_found.php';
        break;
}


if ($necesita_datatables) {
        $css_especificos[] = ASSET_DATATABLES_CSS;
        $css_especificos[] = ASSET_DATATABLES_RESPONSIVE_CSS;

        $js_especificos[] = ASSET_DATATABLES_JS;
        $js_especificos[] = ASSET_DATATABLES_RESPONSIVE_JS;
    }

if ($necesita_sweetalert) {
        $js_especificos[] = ASSET_SWEETALERT_JS;
    }

if ($necesita_fontawesome) {
        $js_especificos[] = ASSET_FONTAWESOME_JS;
    }


if ($necesita_bootstrap_select) {
        $css_especificos[] = ASSET_BOOTSTRAP_SELECT_CSS;
        $js_especificos[] = ASSET_BOOTSTRAP_SELECT_JS;
    }    

    if ($necesita_animatecss) {
        $css_especificos[] = ASSET_ANIMATE_CSS;
    }    

    
if (file_exists($vista_a_cargar)) {
        include $vista_a_cargar;
    } else {
        echo "El archivo $vista_a_cargar no existe.";
    }



?>
