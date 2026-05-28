<?php
require_once __DIR__ . '/../models/Panel.php';

class PanelController { 
    public function getDashboard($tipo_resp){
        $panel    = new Panel();
        $response = $panel->getDashboardData();

        if($tipo_resp == 2){
            return $response;
        } else {
            echo json_encode($response);
        }
    }
}