<?php
require_once __DIR__ . '/../models/Escuela.php';
require_once __DIR__ . '/../utils/FileHelper.php';
class EscuelaController {
    private $id_sesion;
    private $model;
    public function __construct(){
        // Iniciar sesión si no se ha iniciado
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // 1. Validar que existe una sesión activa
    if (!isset($_SESSION['id'])) {
        http_response_code(401); // No autorizado
        echo json_encode(['estatus' => false, 'mensaje' => 'Sesión no válida']);
        exit;
    }

        $this->id_sesion = $_SESSION['id'];
        $this->model = new Escuela;
    }
    public function datatable() {
        $start = $_GET['start'] ?? 0;
        $length = $_GET['length'] ?? 10;
        $search = $_GET['search']['value'] ?? '';

        $alumno = new Escuela();
        $data = $alumno->obtenerEscuelasDataTable($start, $length, $search);
        $total = $alumno->contarEscuelas();

        echo json_encode([
            "draw" => $_GET['draw'],
            "recordsTotal" => $total,
            "recordsFiltered" => $total, 
            "data" => $data
        ]);
    }

    public function combo($tipo_resp, $busqueda = null){
        if($busqueda){
            $params =[$_SESSION['id_escuela'],'%'.$busqueda.'%'];
            $sql_where = " AND id_escuela = ? AND (nombre LIKE ?)";
        }else{
            $params = [];
            $sql_where = '';//' AND id_escuela = ?';
        } 

        
        $total = $this->model->contarEscuelas($sql_where, $params);
        if($total > 0){
            $data = $this->model->obtenerEscuelas($sql_where, $params);
            $estatus = true;
            $mensaje = 'Busqueda con resultados';
        }else{
            $mensaje='No se encontraron';
            $estatus = false;
            $data = [];
        }

        $response = array('estatus'=>$estatus, 'mensaje'=>$mensaje, 'data'=>$data, 'sql'=>$sql_where);
        if($tipo_resp == 1){
            echo json_encode($response);
        }else{
            return ($response);
        }
    }

    public function registrar_escuela($tipo_resp, $data){
        $escuela = new Escuela();
        $nombre         = $data['nombre'] ?? '';
        $direccion      = $data['direccion'] ?? '';
        $cedula         = $data['cedula'] ?? null;
        $telefono       = $data['telefono'] ?? '';
        $fecha_registro = $data['fecha_registro'] ?? date('Y-m-d H:i:s');
        $estatus        = $data['estatus'] ?? 1;
        $fecha_pago     = $data['fecha_pago'] ?? null;
        $nombre_logo    = 'default.png'; // Valor por defecto

       
        //Registro de la escuela
        $response = $escuela->registrarEscuela($nombre, $direccion, $cedula, $telefono, $fecha_registro,
        $estatus, $fecha_pago, $nombre_logo);
        $id_escuela = $response['nuevo'];
        $escuela_data = $response['escuela'];
        $nuevo_logo = FileHelper::uploadImage($_FILES['logo'], 'escuelas', 'logo_' . $id_escuela, $escuela_data['logo']);
        if ($nuevo_logo) {
            $datos= ['logo' => $nuevo_logo];
            $this->actualizar_escuela(2, $datos, $id_escuela);
        }

         if($tipo_resp==2){
                    return $response;
                }else{
                    echo json_encode($response);
        }
    }

    public function traer_escuela($tipo_resp, $id_escuela){
                $response = $this->model->traerEscuela($id_escuela);
                if($tipo_resp==2){
                    return $response;
                }else{
                    echo json_encode($response);
                }
    }

    public function actualizar_escuela($tipo_resp, $datos, $id_escuela){
        
                $response =  $this->model->actualizarEscuela($datos, $id_escuela);
                if($tipo_resp==2){
                    return $response;
                }else{
                    echo json_encode($response);
                }
            }
          
            
    public function actualizar_logo($tipo_resp,$id_escuela){
       
        $escuela_resp =  $this->model->traerEscuela($id_escuela);
        $escuela_data = $escuela_resp['data'];
       
        // Reutilizamos la misma lógica con diferentes parámetros
        $nuevo_logo = FileHelper::uploadImage($_FILES['logo'], 'escuelas', 'logo_' . $id_escuela, $escuela_data['logo']);

        if ($nuevo_logo) {
        $this->model->actualizarEscuela(['logo' => $nuevo_logo], $id_escuela);
        $response = ['estatus' => true, 'filename' => $nuevo_logo, 'mensaje' => 'Logo actualizado correctamente'];
        if($tipo_resp==2){
            return $response;
        }else{
            echo json_encode($response);
        }

     }
    }


}
