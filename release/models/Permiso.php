<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/Usuario.php';

class Permiso {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    //Funciones pendientes de modificar
    public function obtenerProfesoresDataTable($start, $length, $search) {

        $start = (int)$start;
        $length = (int)$length;
        $params =[];
        $sql = "SELECT * FROM vista_profesores WHERE estatus = 1";

           if (!empty($search)) {
                $sql .= " AND (nombre LIKE :search OR apellido LIKE :search)";
                $params[':search'] = "%$search%";
            }

        $sql .= " LIMIT $start, $length";


        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarProfesores() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM vista_profesores WHERE estatus = 1");
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function obtenerListaProfesores() {

        $sql = "SELECT * FROM vista_profesores WHERE estatus = 1";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } 

    //Permisos funciones
    public function obtenerPermisosPorRol($id_rol) {
        $stmt = $this->db->query("SELECT p.permiso 
        FROM permisos p
        INNER JOIN permisos_roles pr ON pr.id_permiso = p.id
        WHERE pr.id_rol = ?", [$id_rol]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerTodosPermisosPorRol($id_rol) {
        $sql = "SELECT p.*, IFNULL(pr.activo, 0) as activo, cp.nombre categoria 
            FROM permisos p
            LEFT JOIN permisos_roles pr ON pr.id_permiso = p.id AND pr.id_rol = ?
            LEFT JOIN categorias_permisos cp ON cp.id = p.id_categoria";
        $stmt = $this->db->query($sql, [$id_rol]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array(
            'estatus' => true,
            'mensaje' => 'Se encontrarón permisos',
            'data' =>  $data
        );
    } 


    public function verificarPermiso($id_rol, $id_usuario, $slug_permiso) {
        $sql = "SELECT 
        CASE 
            WHEN pu.activo IS NOT NULL THEN pu.activo
            WHEN pr.activo IS NOT NULL THEN pr.activo
            ELSE 0 
        END as tiene_acceso
    FROM permisos p
    -- Ahora usamos el parámetro directo ? en lugar de la subconsulta
    LEFT JOIN permisos_roles pr ON p.id = pr.id_permiso AND pr.id_rol = ?
    LEFT JOIN permisos_usuarios pu ON p.id = pu.id_permiso AND pu.id_usuario = ?
    WHERE p.slug = ? AND p.estatus = 1;";
    
        $stmt = $this->db->query($sql, [$id_rol, $id_usuario, $slug_permiso]);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);   
        $tiene_permiso =  $resultado ? (int)$resultado['tiene_acceso'] : 0;
        $mensaje = 'Tiene permiso';

        if($tiene_permiso==0){
            $mensaje = 'No tiene permiso';
        }

        return  array('estatus' => $tiene_permiso, 'mensaje' => $mensaje);

    }

    public function obtenerPermisosExistentes(){
        $count = $this->db->count('categorias_permisos', ' estatus = 1',  []);
        if($count>0){
            $data_array = $this->db->select("SELECT p.*, cp.nombre categoria FROM permisos p LEFT JOIN categorias_permisos cp ON cp.id = p.id_categoria
            ORDER BY p.id_categoria, p.bandera ASC", []);
            $estatus = true;
            $mensaje = 'Se encontraron permisos';
            $data = $data_array;
        }else{
            $estatus = false;
            $mensaje = 'No se encontraron permisos';
            $data =[];
        }
        return array('estatus'=>$estatus, 'mensaje'=>$mensaje, 'data' => $data);

    }

    public function obtenerPermisosXUsuario($id_usuario){

        $usuario_model = new Usuario; 
        $usuario = $usuario_model->obtenerPorIDUsuario($id_usuario);
        if($usuario != ''){
        $id_rol = $usuario['rol'];
         $count = $this->db->count('permisos', ' estatus = ?', [1]);
         if($count>0){
            // Supongamos que ya tienes $id_rol y $id_usuario definidos
            $sql = "SELECT 
            p.id,
            p.permiso,
            p.id_categoria as id_categoria,
            p.descripcion,
            cp.nombre AS categoria,
            p.tipo as tipo,
            -- Valor que viene del ROL
            r.nombre AS rol,
            pr.activo AS valor_rol, 
            -- Valor que viene del USUARIO (Especial)
            pu.activo AS valor_usuario,
            -- Si este ID no es nulo, es un permiso especial
            pu.id AS id_registro_pu 
            FROM permisos p
            LEFT JOIN categorias_permisos cp ON cp.id = p.id_categoria
            -- Unimos los permisos del ROL que tiene el usuario
            LEFT JOIN permisos_roles pr ON pr.id_permiso = p.id AND pr.id_rol = ?
            -- Unimos los permisos específicos del USUARIO
            LEFT JOIN roles r ON r.id = pr.id_rol
            LEFT JOIN permisos_usuarios pu ON pu.id_permiso = p.id AND pu.id_usuario = ?
            ORDER BY p.id_categoria, p.bandera ASC";

            $data_array = $this->db->select($sql, [$id_rol, $id_usuario]);
            $estatus = true;
            $mensaje = 'Se encontraron permisos';
            $data = $data_array;
        }else{
            $estatus = false;
            $mensaje = 'No se encontraron permisos';
            $data =[];
        }
        }else{
            $estatus = false;
            $mensaje = 'No existe el usuario';
            $data =[];

        }

       
        return array('estatus'=>$estatus, 'mensaje'=>$mensaje, 'data' => $data);

    }

    public function traerPermisosFaltantes($id_usuario){
        $count = $this->db->count('categorias_permisos', ' estatus = 1',  []);
        if($count>0){
            $sql = "SELECT p.*, cp.nombre AS categoria 
            FROM permisos p
            LEFT JOIN categorias_permisos cp ON cp.id = p.id_categoria
            LEFT JOIN permisos_usuarios pu ON pu.id_permiso = p.id AND pu.id_usuario = ?
            WHERE pu.id_permiso IS NULL
            ORDER BY p.id_categoria, p.bandera ASC";
            $data_array = $this->db->select($sql, [$id_usuario]);
            $estatus = true;
            $mensaje = 'Se encontraron permisos';
            $data = $data_array;
        }else{
            $estatus = false;
            $mensaje = 'No se encontraron permisos';
            $data =[];
        }
        return array('estatus'=>$estatus, 'mensaje'=>$mensaje, 'data' => $data);
        
    }

    public function actualizarPermisoUsuario($id_usuario, $id_permiso, $valor, $accion){
        $usuario_model = new Usuario;
        $count_permiso = $this->db->count('permisos', 'id = ?',  [$id_permiso]);

        if($count_permiso > 0){
            $usuario = $usuario_model->obtenerPorIDUsuario($id_usuario);
            if($usuario != ''){
                $id_rol = $usuario['rol'];
                $nombre_rol = $usuario['nombre_rol'];
                $count_usuario_permiso = $this->db->count('permisos_usuarios', 'id_permiso = ? AND id_usuario = ?',  [$id_permiso, $id_usuario]);
                $count_permiso_rol = $this->db->count('permisos_roles', 'id_permiso = ? AND id_rol = ?',  [$id_permiso, $id_rol]);

                if($accion == 'actualizar'){
                    if($count_usuario_permiso > 0){
                  
                        $data_update = ['activo' => $valor];
                        if($valor==0){
                            $this->db->delete('permisos_usuarios', 'id_usuario = ? AND id_permiso = ?', [$id_usuario, $id_permiso]);

                        }else{
                            $this->db->update('permisos_usuarios', $data_update, 'id_usuario = ? AND id_permiso = ?', [$id_usuario, $id_permiso]);
                        }
                        
                    }else{
                        $this->db->insert('permisos_usuarios', ['id_permiso'=>$id_permiso, 'id_usuario'=>$id_usuario, 'activo'=>$valor]);
                    }
                }else if($accion == 'reset'){
                    $this->db->delete('permisos_usuarios', 'id_usuario = ? AND id_permiso = ?', [$id_usuario, $id_permiso]);
                }
                
                $data =[];
                $permiso_usuario = $this->db->select('SELECT * FROM permisos_usuarios WHERE id_permiso =? AND id_usuario = ?', [$id_permiso, $id_usuario]);
                if(!empty($permiso_usuario)){
                    $valor_usuario = $permiso_usuario[0]['activo'];
                }else{
                    $valor_usuario='x'; //Cualquier variable con tal de que no entré en la primera validacion del switch
                    if($accion == 'actualizar'){
                        $data['switch_off'] = true; //Apagar el switch
                    }else{
                        $data['switch_off'] = false;
                    }
                }
                
                if($count_permiso_rol>0){
                    $permiso_rol = $this->db->select('SELECT * FROM permisos_roles WHERE id_permiso =? AND id_rol = ?', [$id_permiso, $id_rol]);
                    $valor_rol = $permiso_rol[0]['activo'];
                }else{
                    $valor_rol =0;
                }
             
               /*  print_r($valor_usuario .'--');
                print_r($valor_rol);
                die(); */
                if ($valor_usuario == 0 && $valor_rol == 1) {
                    $data['switch' ] = 3;
                }else if ($valor_rol == 1) {
                    $data['switch' ] = 2;
                } else if ($valor_usuario == 1){
                    $data['switch' ] = 1;
                }else{
                    $data['switch' ] = 0;
                }
                $data['id_permiso'] = $id_permiso;
                $data['nombre_rol'] = $nombre_rol;
                $estatus = true;
                $mensaje = 'Se actualizó permiso';
             
             
            }else{
                $estatus = false;
                $mensaje = 'No se encontró usuario';
                $data =[];
             }
        }else{
            $estatus = false;
            $mensaje = 'No se encontraron permisos';
            $data =[];
        }

        return array('estatus'=>$estatus, 'mensaje'=>$mensaje, 'data' => $data);

    }

    public function actualizarPermisoRol($id_rol, $id_permiso, $valor){
        $count_permiso = $this->db->count('permisos', 'id = ?',  [$id_permiso]);

        if($count_permiso >0){
            $no_roles = $this->db->count('roles', 'id = ?',  [$id_rol]);
            if($no_roles>0){
                $count_permiso_rol = $this->db->count('permisos_roles', 'id_permiso = ? AND id_rol = ?',  [$id_permiso, $id_rol]);
                
                if($count_permiso_rol >0){
                    $data_update = ['activo' => $valor];
                    if($valor ==0){
                        $this->db->delete('permisos_roles', 'id_rol = ? AND id_permiso = ?', [$id_rol, $id_permiso]);
                    }else{
                        $this->db->update('permisos_roles', $data_update, 'id_rol = ? AND id_permiso = ?', [$id_rol, $id_permiso]);
                    }
                }else{
                    $this->db->insert('permisos_roles', ['id_permiso'=>$id_permiso, 'id_rol'=>$id_rol, 'activo'=>$valor]);
                }

                $estatus = true;
                $mensaje = 'Permiso actualizado correctamente';
                $data =[];

            }else{
                $estatus = false;
                $mensaje = 'No se encontró un rol con ese ID';
                $data =[];
            }
         }else{
            $estatus = false;
            $mensaje = 'No se encontraró un permiso con ese ID';
            $data =[];
        }

        return array('estatus'=>$estatus, 'mensaje'=>$mensaje, 'data' => $data);

    }

   /*  public function agregarPermisoXUsuario($id_usuario, $ids_permisos) {
        // Validamos que sea un arreglo, si es un solo ID lo convertimos a arreglo
        if (!is_array($ids_permisos)) {
            $ids_permisos = [$ids_permisos];
        }
    
        $insertados = 0;
        $errores = 0;
    
        foreach ($ids_permisos as $id_permiso) {
            // 1. Validamos si ya existe
            $count = $this->db->count('permisos_usuarios', 'id_usuario = ? AND id_permiso = ?', [$id_usuario, $id_permiso]);
            
            if ($count == 0) {
                $params = [
                    'id_permiso' => $id_permiso,
                    'id_usuario' => $id_usuario,
                    'activo' => 0
                ];
                
                $res = $this->db->insert('permisos_usuarios', $params);
                
                if ($res) {
                    $insertados++;
                } else {
                    $errores++;
                }
            } else {
                // Ya existía, podrías contarlo como error o simplemente saltarlo
                $errores++;
            }
        }
    
        if ($insertados > 0) {
            $estatus = true;
            $mensaje = "Se registraron $insertados permiso(s) correctamente.";
        } else {
            $estatus = false;
            $mensaje = 'No se realizaron inserciones (los permisos ya existían o hubo un error).';
        }
    
        return array(
            'estatus' => $estatus, 
            'mensaje' => $mensaje, 
            'data' => ['insertados' => $insertados, 'errores' => $errores]
        );
    } */

    /* public function actualizarPermisosUsuario($id_usuario, $arr_permisos){

        if(count($arr_permisos) >= 0){
            foreach ($arr_permisos as $permiso) {
                $data_update = ['activo' => $permiso['activo']];
                $this->db->update('permisos_usuarios', $data_update, 'id = ?', [$permiso['id']]);;
            }

            $estatus = true;
            $mensaje = 'Permisos actualizados con exito';
            $data = $arr_permisos;

        }else{
            $estatus = false;
            $mensaje = 'No hay ningun permisos para guardar';
            $data =[];
        }
        return array('estatus'=> $estatus, 'mensaje' => $mensaje, 'data'=>$data);

    } */
}
