<?php
require_once __DIR__ . '/../models/Catalogo.php';

class CatalogoController {

    public function iniciar_flujo($tipo_resp, $data){
        $catalogo = new Catalogo();

        $ciclo = $data['ciclo'];
        $nivel = $data['nivel'];
        $response = $catalogo->IniciarFlujo($ciclo, $nivel);
        if($tipo_resp==2){
            return $response;
        }else{
            echo json_encode($response);
        }
    }

    public function segundo_paso_flujo($tipo_resp, $data){
        $catalogo = new Catalogo();
        $nivel = $data['nivel'];

        $response = $catalogo->segundoPasoFlujo($nivel);
        if($tipo_resp==2){
            return $response;
        }else{
            echo json_encode($response);
        }
    }
}
