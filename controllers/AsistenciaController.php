<?php
require_once __DIR__ . '/../models/Asistencia.php';

class AsistenciaController {

    private $model;
    private $id_sesion;
    private $id_escuela;
    public function __construct(){
        // Iniciar sesión si no se ha iniciado
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->id_sesion = $_SESSION['id'];
        $this->id_escuela = $_SESSION['id_escuela'];
        $this->model = new Asistencia;
    }



    function guardar_asistencia($data, $tipo_resp){
       
        $res = $this->model->guardarAsistencia($data, $this->id_escuela, $this->id_sesion);
        if($tipo_resp==2){
            return $res;
        }else{
            echo json_encode($res);
        }
    }

    function obtener_semanal($tipo_resp, $id_grupo, $fecha_inicio, $fecha_fin){
        $res = $this->model->obtenerSemanal($id_grupo, $fecha_inicio, $fecha_fin);
        if($tipo_resp==2){
            return $res;
        }else{
            echo json_encode($res);
        }
    }

    public function registrar_qr($data, $id_escuela, $id_sesion){
        // Pasamos todo al modelo y simplemente imprimimos el JSON de respuesta
        $res = $this->model->registrarPorQR($data, $id_escuela, $id_sesion);
        echo json_encode($res);
    }
}