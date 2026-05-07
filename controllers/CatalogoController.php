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

    public function guardar_bloques($tipo_resp){
    $catalogo = new Catalogo();
    $response = $catalogo->guardarBloques();
    if($tipo_resp==2){
        return $response;
    }else{
        echo json_encode($response);
    }
    }

    public function cargar_config_prehorario_flujo($tipo_resp){
        $catalogo = new Catalogo();
        $response = $catalogo->cargarConfigPrehorarioFlujo();
        if($tipo_resp==2){
            return $response;
        }else{
            echo json_encode($response);
        }
    }

    public function resetear_prehorario($tipo_resp){
        $catalogo = new Catalogo();
        $response = $catalogo->resetearPrehorario();
        if($tipo_resp==2){
            return $response;
        }else{
            echo json_encode($response);
        }
    }

    public function guardar_horario($tipo_resp){
        $catalogo = new Catalogo();
        $response = $catalogo->guardarHorario();
        if($tipo_resp==2){
            return $response;
        }else{
            echo json_encode($response);
        }
    }
}
