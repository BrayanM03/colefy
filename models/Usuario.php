<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/dates.php';
require_once __DIR__ . '/../models/Datatable.php';
  
 
class Usuario extends Datatable{
    private $tabla_usuarios = 'vista_usuarios';
    private $fecha;
    public function __construct() {
       
        $this->db = new Database();
        $this->fecha = new Date();
    }

    public function datatablesUsuarios($id_filtro, $start, $length, $search, $orderColumn, $orderDir)
    {   
        $extraWhere = "";
        $params = [];
        // 2. Llama al método genérico de la clase Datatable
        return $this->getDataTable(
            $this->tabla_usuarios,
            $start, 
            $length, 
            $search, 
            $orderColumn, 
            $orderDir, 
            ['nombre'], 
            $extraWhere, 
            $params
        );
    }

    public function contarUsuarios($id_filtro)
    {
         // 2. Concatena el campo con el ID de filtro
         $extraWhere = "";
         $params = []; 
         return $this->countAll($this->tabla_usuarios, $extraWhere, $params);
    }

    public function contarUsuariosFiltrados($id_filtro, $search)
    {
        $extraWhere = "";
        $params = []; 
        return $this->countFiltered($this->tabla_usuarios, $search,  ['nombre', 'apellido'], $extraWhere, $params);
  
    }

    public function registrarUsuario($nombre, $apellidos, $id_escuela, $telefono, $usuario, $password, $rol, $cargo) {
        // 1. Verificar si el usuario ya existe
        $existe = $this->obtenerPorUsuario($usuario);
        if ($existe) {
            return [
                'estatus' => false,
                'mensaje' => "El nombre de usuario '{$usuario}' ya se encuentra registrado."
            ];
        }
    
        // 2. Encriptar la contraseña
        // PASSWORD_BCRYPT genera una cadena segura de 60 caracteres
        $password_encriptada = password_hash($password, PASSWORD_BCRYPT);
    
        $fecha_registro = $this->fecha->obtenerFechaRegistro();
    
        // 3. Insertar en la BD
        $id_nuevo = $this->db->insert('usuarios', [
            'nombre'         => $nombre, 
            'apellido'       => $apellidos, 
            'telefono'       => $telefono, 
            'rol' => $rol,
            'estatus' => 1,
            'cargo' => $cargo,
            'fecha_ingreso' => $fecha_registro,
            'usuario'        => $usuario, 
            'contraseña'       => $password_encriptada, // Guardamos la versión segura
            'id_escuela'     => $id_escuela, 
            'fecha_registro' => $fecha_registro
        ]);
    
        if (!$id_nuevo) {
            return ['estatus' => false, 'mensaje' => 'Error al insertar en la base de datos'];
        }
    
        $id_nuevo = intval($id_nuevo);
        $user_data = $this->obtenerPorIDUsuario($id_nuevo);
    
        return [
            'estatus' => true,
            'mensaje' => 'Registro insertado correctamente',
            'nuevo'   => $id_nuevo,
            'usuario' => $user_data
        ];
    }

    public function obtenerPorUsuario($username) {
        $stmt = $this->db->query("SELECT u.*, r.nombre as nombre_rol, e.nombre as escuela, e.logo as logo_escuela FROM usuarios u
        INNER JOIN roles r ON u.rol = r.id 
        INNER JOIN escuelas e ON e.id = u.id_escuela WHERE usuario = ?", [$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function obtenerPorIDUsuario($id_usuario) {
        $stmt = $this->db->query("SELECT u.*, r.nombre as nombre_rol, e.nombre as escuela, e.logo as logo_escuela  FROM usuarios u 
        INNER JOIN roles r ON u.rol = r.id 
        INNER JOIN escuelas e ON e.id = u.id_escuela WHERE u.id = ?", [$id_usuario]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizarUsuario($id_usuario, $data_update){
      
        return $this->db->update('usuarios', $data_update, 'id  = ?', [$id_usuario]);
    }
}
