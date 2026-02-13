<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ .  '/../config/permisos-enum.php'; 
require_once __DIR__ . '/../models/Permiso.php';
//session_start();
class PermisoController {
    private $model;
    public function __construct() {
        $this->model = new Permiso();
    }

    public function verificarSesion(){
       
        if(empty($_SESSION['id'])){
            header("Location: ". BASE_URL ."login");
            exit();
        };
    }

    //Funciones para los roles-permisos
    public function getPermisosPorRol($tipo_resp, $rol_id) {
        $resp = $this->model->obtenerTodosPermisosPorRol($rol_id);
        if($tipo_resp==1){
            echo json_encode($resp);
        }else{
            return $resp;
        }
    } 

    public function validarAcceso($tipo_resp, $slug_permiso) {
        $id_usuario = $_SESSION['id'] ?? null;
        $id_rol = $_SESSION['rol'] ?? null;
       
       if (!$id_usuario || !$id_rol) {
        header("Location: " . BASE_URL . "login");
        exit();
        }
    
        $response_permiso = $this->model->verificarPermiso($id_rol, $id_usuario, $slug_permiso);       
        
        $tipo_resp = intval($tipo_resp);
        
        if (!$response_permiso['estatus'] && $tipo_resp != 2) {
         
            // Si no tiene permiso, lo mandamos a la página de error
            header("Location: " . BASE_URL . "src/vistas/error/sin_permisos.php?modulo=" . $slug_permiso . $tipo_resp);
            exit(); // Importante para detener la ejecución del HTML
        } 
       
       
        if($tipo_resp==2){
            return $response_permiso;
        }   
    }

    public function addPermisoARol($rol_id, $permiso_id) {
       // return $this->model->asignarPermisoARol($rol_id, $permiso_id);
    }

    public function removePermisoDeRol($rol_id, $permiso_id) {
       // return $this->model->eliminarPermisoDeRol($rol_id, $permiso_id);
    }

    public function redirigirMaestros($rol){
        
        $resp_permiso = $this->validarAcceso(2, CPermiso::VER_PANEL_PRINCIPAL->value);
         
       if(!$resp_permiso['estatus']){
            $es_profesor = $_SESSION['es_profesor'];
         
            header("Location:panel_maestros");
       };
    }

    public function obtener_permisos_existentes($tipo_resp){
    
        $resp = $this->model->obtenerPermisosExistentes();
        if($tipo_resp==1){
            echo json_encode($resp);
        }else{
            return $resp;
        }
    }

    public function obtener_permisos_x_usuario($tipo_resp, $id_usuario){
        $resp = $this->model->obtenerPermisosXUsuario($id_usuario);
        if($tipo_resp==1){
            echo json_encode($resp);
        }

    }

    public function traer_permisos_faltantes($tipo_resp, $id_usuario){
    
        $resp = $this->model->traerPermisosFaltantes($id_usuario);
       
        if($tipo_resp==1){
            echo json_encode($resp);
        }else{
            return $resp;
        }
    }

    public function actualizar_permiso_usuario($tipo_resp, $id_usuario, $id_permiso, $valor, $accion) {
        $resp = $this->model->actualizarPermisoUsuario($id_usuario, $id_permiso, $valor, $accion);
        if($tipo_resp==1){
            echo json_encode($resp);
        }else{
            return $resp;
        }
    }

    public function actualizar_permiso_rol($tipo_resp, $id_rol, $id_permiso, $valor) {
        $resp = $this->model->actualizarPermisoRol($id_rol, $id_permiso, $valor);
        if($tipo_resp==1){
            echo json_encode($resp);
        }else{
            return $resp;
        }
    }

   /*  public function agregar_permisos_x_usuario($tipo_resp, $id_usuario, $ids_permisos){
        $resp = $this->model->agregarPermisoXUsuario($id_usuario, $ids_permisos);
        if($tipo_resp==1){
            echo json_encode($resp);
        }else{
            return $resp;
        }
    } */

   /*  public function actualizar_permisos_usuario($tipo_resp, $id_usuario, $arr_permisos) {
        $resp = $this->model->actualizarPermisoUsuario($id_usuario, $arr_permisos);
        if($tipo_resp==1){
            echo json_encode($resp);
        }else{
            return $resp;
        }
    } */

}
