<?php
/**
 * Configuración global del sistema Colefy
 */

// 1. DETERMINAR EL PROTOCOLO (http o https)
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

// 2. DEFINIR LA URL BASE
// Si estás en local con MAMP/XAMPP, esto asegura que siempre apunte a la carpeta raíz
// 2. DEFINIR LA URL BASE DE FORMA AUTOMÁTICA
if ($_SERVER['HTTP_HOST'] == 'localhost:8888' || $_SERVER['HTTP_HOST'] == '127.0.0.1:8888') {
    // Si estás en tu Mac (Local)
    define('BASE_URL', $protocol . $_SERVER['HTTP_HOST'] . '/Colefy/');
} else {
    // Si estás en el servidor de Amazon (Producción)
    define('BASE_URL', $protocol . $_SERVER['HTTP_HOST'] . '/');
}
// 3. RUTAS PARA RECURSOS (Assets)
// Esto apunta a donde tienes el CSS, JS e Imágenes de AdminKit
define('STATIC_URL', BASE_URL . 'static/');
define('ROOT_PATH', dirname(__DIR__) . '/');

//Assets para librerias que se repiten en las vistas
//CSS
define('ASSET_DATATABLES_CSS', 'https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css');
define('ASSET_DATATABLES_RESPONSIVE_CSS','https://cdn.datatables.net/responsive/2.3.0/css/responsive.dataTables.min.css');
define('ASSET_ANIMATE_CSS', 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css');
define('ASSET_BOOTSTRAP_SELECT_CSS', STATIC_URL . 'css/bootstrap-select.min.css');
define('ASSET_NUEVO_RECIBO_CSS', STATIC_URL . 'css/nuevo-recibo.css');
define('ASSET_PERMISOS_CSS', STATIC_URL . 'css/permisos.css');
define('ASSET_EDITAR_USUARIO_CSS', STATIC_URL . 'css/editar-usuario.css');
define('ASSET_MENU_USUARIO_CSS', STATIC_URL . 'css/menu-usuario-edit-navegacion-pestañas.css');
define('ASSET_MENU_NAVEGACION_RECIBO_CSS', STATIC_URL . 'css/menu-navegacion-pestañas.css');
define('ASSET_EDITAR_ROL_CSS', STATIC_URL . 'css/editar-rol.css');
define('ASSET_EDITAR_RECIBO_CSS', STATIC_URL . 'css/ediar-recibo.css');
define('ASSET_PANEL_MAESTROS_CSS', STATIC_URL . 'css/panel-maestros.css');

//JS
define('ASSET_FONTAWESOME_JS', 'https://kit.fontawesome.com/5c955c6e98.js');
define('ASSET_SWEETALERT_JS','https://cdn.jsdelivr.net/npm/sweetalert2@11');
define('ASSET_DATATABLES_JS', 'https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js');
define('ASSET_DATATABLES_RESPONSIVE_JS', 'https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js');
define('ASSET_BOOTSTRAP_SELECT_JS', STATIC_URL . 'js/bootstrap-select.min.js');


?>