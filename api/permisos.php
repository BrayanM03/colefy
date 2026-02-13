<?php
 
require_once __DIR__ . '/../controllers/PermisoController.php'; 

$controller = new PermisoController();
if($_GET['tipo']== 'datatable'){
    //$controller->datatable();
}

if($_GET['tipo']=='ver'){
    $id_sesion = $_SESSION['id'];
   // $controller->registrarAlumno($nombre, $apellido_paterno, $apellido_materno, $cumple, $genero, $telefono);
}

if($_GET['tipo']=='ver_lista_permisos'){
    /**
 * Obtiene los permisos existentes
 * 
 * @param int   si el tipo de respuesta será 1: un echo o 2 un return
 *
 * @return array respuesta de los permisos existentes
 */
    $controller->obtener_permisos_existentes(1);
}

if($_GET['tipo']=='ver_lista_permisos_x_rol'){
    $id_rol = $_POST['id_rol'];
    /**
 * Obtiene los permisos existentes
 * @param int   si el tipo de respuesta será 1: un echo o 2 un return
 * 
 * @param id_rol  El ID del rol en cuestion que se traeran los permisos
 *
 * @return array respuesta de los permisos existentes
 */
    $controller->getPermisosPorRol(1, $id_rol);
}

if($_GET['tipo']=='ver_lista_permisos_faltantes'){
    /**
 * Obtiene los permisos existentes
 * 
 * @param int   si el tipo de respuesta será 1: un echo o 2 un return
 * @param int   ID de la sesion para ver que permisos le faltan
 * @return array respuesta de los permisos existentes
 */ 
    $id_usuario = $_POST['id_usuario'];

   $controller->traer_permisos_faltantes(1, $id_usuario);
}


if($_GET['tipo']=='permisos_usuario'){
    $id_usuario = $_POST['id_usuario'];
   
    /**
 * Obtiene los permisos existentes
 *
 * @param int 1   si el tipo de respuesta será 1: un echo o 2 un return
 * @param int $id_sesion Sesion del usuario para obtener sus permisos

 *
 * @return array respuesta de los permisos existentes
 */
    $controller->obtener_permisos_x_usuario(1, $id_usuario);
}

if($_GET['tipo']=='gestion_permiso_individual'){
    $id_usuario = $_POST['id_usuario'];
    $id_permiso = $_POST['id_permiso'];
    $accion = $_POST['accion'];
    $valor = isset($_POST['valor']) ? $_POST['valor']: 0;
    $controller->actualizar_permiso_usuario(1, $id_usuario, $id_permiso, $valor, $accion);

}

if($_GET['tipo']=='gestion_permiso_rol'){
    $id_rol = $_POST['id_rol'];
    $id_permiso = $_POST['id_permiso'];
    $valor = isset($_POST['valor']) ? $_POST['valor']: 0;
    $controller->actualizar_permiso_rol(1, $id_rol, $id_permiso, $valor);

}


//--------------------//
//Tampoco creo ocupar esto revisar y borrar si es necesario
if($_GET['tipo']=='agregar_permiso_usuario'){
    $id_usuario = $_POST['id_usuario'];
    $ids_permisos = $_POST['ids_permisos'];
    /**
 * Obtiene los permisos existentes
 *
 * @param int 1   si el tipo de respuesta será 1: un echo o 2 un return
 * @param int $id_usuario ID del usuario al que se le estan agregando los permisos a la tabla permisos_usuario

 *
 * @return array respuesta si los permisos fueron agregados
 */
   // $controller->agregar_permisos_x_usuario(1, $id_usuario, $ids_permisos);
}

//Pendiente revisa a lo mejor ya no ocupo esto es el boton de guardado
if($_GET['tipo']=='guardar_permisos_usuario'){
    $id_usuario = $_POST['id_usuario'];
    $arr_permisos = $_POST['permisos'];
    /**
 * Obtiene los permisos existentes
 *
 * @param int 1   si el tipo de respuesta será 1: un echo o 2 un return
 * @param int $id_usuario ID del usuario al que se le estan actualizando sus permisos
 * @param int $arr_permisos Arreglo de permisos que se estan actualizando
 *
 * @return array respuesta de los permisos existentes
 */
    //$controller->actualizar_permisos_usuario(1, $id_usuario, $arr_permisos);
}
