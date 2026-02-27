<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../utils/FileHelper.php';
require_once __DIR__ . '/../controllers/DataTableController.php';


class UsuarioController extends DataTableController {
    private $id_sesion;
    protected $current_datatable;
    
    public function __construct(){
        $this->id_sesion = $_SESSION['id'];
        parent::__construct();
        $this->model = new Usuario();
    }

    public function datatable_usuarios() {
        $this->current_datatable = 'usuarios';
        $this->datatable_general();
    }

    // --- Implementación de los métodos de plantilla para la tabla de Recibos ---
    protected function getModelData($id_filtro, $start, $length, $search, $orderColumnName, $orderDir, $filtros) {
        if ($this->current_datatable === 'usuarios') {
            return $this->model->datatablesUsuarios($id_filtro, $start, $length, $search, $orderColumnName, $orderDir);
        } 
        return [];
    }

    protected function getModelTotal($id_filtro, $filtros) {
        if ($this->current_datatable === 'usuarios') {
            return $this->model->contarUsuarios($id_filtro);
        } 
        return 0;
    }

    protected function getModelFilteredTotal($id_filtro,$search, $filtros) {
        if ($this->current_datatable === 'usuarios') {
            return $this->model->contarUsuariosFiltrados($id_filtro, $search);
        }
        return 0;
    } 

    public function obtener_usuario($tipo_resp, $id_usuario){
        $usuario = new Usuario();
        $user_data =  $usuario->obtenerPorIDUsuario($id_usuario);
        if($tipo_resp==2){
            return ([
                'estatus' =>true,
                'mensaje' => 'Informacion usuario',
                'data' => $user_data
            ]);
        }else{
            echo json_encode ([
                'estatus' =>true,
                'mensaje' => 'Informacion usuario',
                'data' => $user_data
            ]);
        }
    }

    public function cambiar_foto_perfil() {
        //header('Content-Type: application/json');
        $usuario = new Usuario();

        if (!isset($_FILES['avatar'])) {
            echo json_encode(['status' => 'error', 'message' => 'No se recibió archivo']);
            exit;
        }
    
        $file = $_FILES['avatar'];
        $user_data =  $usuario->obtenerPorIDUsuario($this->id_sesion);
        $user_foto_perfil_vieja = $user_data['foto_perfil']; //Nombre del archivo

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $nuevo_nombre = "perfil_" . $this->id_sesion . "_" . time() . "." . $extension;
        $ruta_destino = ROOT_PATH . "static/img/avatars/" . $nuevo_nombre;

    
    
        if (move_uploaded_file($file['tmp_name'], $ruta_destino)) {

            // --- LÓGICA PARA BORRAR FOTO ANTERIOR ---
            if (!empty($user_foto_perfil_vieja)) {
                $ruta_vieja = ROOT_PATH . "static/img/avatars/" . $user_foto_perfil_vieja;
                
                // Verificamos que el archivo exista y que no sea una imagen por defecto
                if (file_exists($ruta_vieja) && $user_foto_perfil_vieja !== 'avatar.jpg') {
                    unlink($ruta_vieja); // Borra el archivo físico
                }
            }

            // Actualizar BD
            $this->model->actualizarUsuario($this->id_sesion, ['foto_perfil' => $nuevo_nombre]);
            $_SESSION['foto_perfil'] = $nuevo_nombre;
            // Respondemos con el nombre del archivo para que el JS lo use
            echo json_encode([
                'estatus' => true, 
                'mensaje' => 'Foto subida actualizada',
                'filename' => $nuevo_nombre
            ]);
        } else {
            echo json_encode(['estatus' => false, 'mensaje' => 'Error al mover el archivo']);
        }
        exit;
    }

    function actualizar_datos_generales($tipo_resp, $datos){

        $datos_generales = array('nombre'=> $datos['nombre'], 'apellido' => $datos['apellido'], 'correo' => $datos['correo']);
        $no_filas_afectadas = $this->model->actualizarUsuario($this->id_sesion, $datos_generales);
        $resp = array(
            'estatus' => true,
            'mensaje' => 'Datos actualizados con exito',
            'data' => array(
                'nombre' =>$datos['nombre'],
                'apellido' => $datos['apellido'],
                'correo' => $datos['correo']
            )
        );
        $_SESSION['nombre'] = $datos['nombre'];
        $_SESSION['apellido'] = $datos['apellido'];
        $_SESSION['correo'] = $datos['correo'];
        if($tipo_resp == 2){
            return $resp;
        }else{
            echo json_encode($resp);
        }
    }

    public function cambiar_contrasena_perfil($tipo_resp = 1) {
        // Solo enviamos header si la respuesta es para AJAX (tipo 1)
        if ($tipo_resp == 1) {
            header('Content-Type: application/json');
        }
    
        $pass_actual = $_POST['pass_actual'] ?? '';
        $pass_nueva = $_POST['pass_nueva'] ?? '';
    
        // 1. Obtener datos del usuario
        $user_data = $this->model->obtenerPorIDUsuario($this->id_sesion);
        
        // Preparamos el array base de respuesta
        $respuesta = ['estatus' => false, 'mensaje' => ''];
    
        if (!$user_data) {
            $respuesta['mensaje'] = 'Usuario no encontrado';
            return $this->enviarRespuesta($respuesta, $tipo_resp);
        }
    
        // 2. Verificar contraseña actual
        if (!password_verify($pass_actual, $user_data['contraseña'])) {
            $respuesta['mensaje'] = 'La contraseña actual es incorrecta';
            return $this->enviarRespuesta($respuesta, $tipo_resp);
        }
    
        // 3. Validar que la nueva no sea igual a la actual
        if ($pass_actual === $pass_nueva) {
            $respuesta['mensaje'] = 'La nueva contraseña no puede ser igual a la anterior';
            return $this->enviarRespuesta($respuesta, $tipo_resp);
        }
    
        // 4. Encriptar y Actualizar
        $nuevo_hash = password_hash($pass_nueva, PASSWORD_BCRYPT, ['cost' => 12]);
        $resultado = $this->model->actualizarUsuario($this->id_sesion, ['contraseña' => $nuevo_hash]);
    
        if ($resultado) {
            $respuesta['estatus'] = true;
            $respuesta['mensaje'] = '¡Contraseña actualizada con éxito!';
        } else {
            $respuesta['mensaje'] = 'Error al guardar en la base de datos';
        }
    
        return $this->enviarRespuesta($respuesta, $tipo_resp);
    }

    public function registrar_usuario($tipo_resp, $data){
        $nombre         = $data['nombre'] ?? '';
        $apellidos      = $data['apellidos'] ?? '';
        $id_escuela     = $data['id_escuela'] ?? null;
        $telefono       = $data['telefono'] ?? '';
        $usuario        = $data['usuario'];
        $password       = $data['password'];
        $cargo          = $data['cargo'];
        $rol            = $data['rol'];

       
        //Registro de la escuela
        $response = $this->model->registrarUsuario($nombre, $apellidos, $id_escuela, $telefono, $usuario, $password, $rol, $cargo);
        if($response['estatus']){
            $usuario_data = $response['usuario'];
            $id_usuario_nuevo = $response['nuevo'];
            $nuevo_foto_perfil = FileHelper::uploadImage($_FILES['avatar'], 'avatars', 'perfil_' . $id_usuario_nuevo, $usuario_data['foto_perfil']);
            if ($nuevo_foto_perfil) {
                $datos= ['foto_perfil' =>  $nuevo_foto_perfil];
                $this->model->actualizarUsuario($id_usuario_nuevo, $datos);
            }
        }

         if($tipo_resp==2){
                    return $response;
                }else{
                    echo json_encode($response);
        }
    }
    
    /**
     * Función auxiliar para gestionar el tipo de salida
     */
    private function enviarRespuesta($data, $tipo) {
        if ($tipo == 1) {
            echo json_encode($data);
            exit; // Cortamos ejecución en AJAX
        }
        return $data; // Retornamos el array para uso interno en PHP
    }
}
 
?>
