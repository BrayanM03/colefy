<?php
require_once dirname(__DIR__) . '/config/config.php';
require_once ROOT_PATH . 'controllers/PanelController.php';

$controller = new PanelController();
if($_GET['tipo'] == 'dashboard'){
    $controller->getDashboard(1);
}