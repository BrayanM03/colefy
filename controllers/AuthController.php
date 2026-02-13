<?php
require_once __DIR__ . '/../models/Usuario.php';

class AuthController {
    public function login($username, $password) {
        $usuarioModel = new Usuario();
        $usuario = $usuarioModel->obtenerPorUsuario($username);
       
        if (!$usuario) {
            return ['estado' => 2]; // Usuario no existe
        } 

        if ($usuario['estatus'] != 1) {
            return ["estado" => 4]; // Usuario desactivado
        }

        if (!password_verify($password, $usuario['contraseña'])) { 
            return ['estado' => 3]; // Contraseña incorrecta
        }
      
        // Guardar sesión
        $_SESSION['sistema'] = 'colefy';
        $_SESSION["id"] = $usuario['id'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['apellido'] = $usuario['apellido'];
        $_SESSION['user'] = $usuario['usuario'];
        $_SESSION['fecha_ingreso'] = $usuario['fecha_ingreso'];
        $_SESSION['rol'] = $usuario['rol'];
        $_SESSION['estatus'] = $usuario['estatus'];
        $_SESSION['es_profesor'] = $usuario['es_profesor'];
        $_SESSION['id_escuela'] = $usuario['id_escuela'];
       
        return [
            "estado" => 1,
            "rol" => $usuario['rol']
        ];
    }
}
