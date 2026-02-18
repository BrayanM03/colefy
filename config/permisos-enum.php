<?php

enum CPERMISO: string {
    // Panel Principal
    case VER_PANEL_PRINCIPAL = 'ver_panel_principal';
    case VER_PANEL_MAESTROS = 'ver_panel_maestros'; 

    // Recibos
    case VER_RECIBOS = 'ver_recibos';
    case VER_RECIBO_PDF = 'ver_recibo_pdf';
    case CREAR_RECIBOS = 'crear_nuevo_recibo';
    case EDITAR_RECIBOS = 'editar_recibo';
    case CANCELAR_RECIBOS = 'cancelar_recibos';

    // Alumnos
    case VER_ALUMNOS = 'ver_alumnos';
    case CREAR_ALUMNOS = 'crear_alumnos';
    case EDITAR_ALUMNOS = 'editar_alumnos';
    case CANCELAR_ALUMNOS = 'cancelar_alumnos';

    // Profesores
    case VER_PROFESORES = 'ver_profesores';
    case CREAR_PROFESORES = 'crear_profesores';
    case EDITAR_PROFESORES = 'editar_profesores';
    case CANCELAR_PROFESORES = 'cancelar_profesores';
 
    // Materias
    case VER_MATERIAS = 'ver_materias';
    case CREAR_MATERIAS = 'crear_materias';
    case EDITAR_MATERIAS = 'editar_materias';
    case CANCELAR_MATERIAS = 'cancelar_materias';

    // Horarios
    case VER_HORARIOS = 'ver_horarios';
    case CREAR_HORARIOS = 'crear_horarios';
    case EDITAR_HORARIOS = 'editar_horarios';
    case CANCELAR_HORARIOS = 'cancelar_horarios';
    case ASIGNAR_HORARIOS = 'asignar_horarios';

    // Escuelas
    case VER_GRUPOS = 'ver_grupos';
    case CREAR_GRUPOS = 'crear_grupos';
    case EDITAR_GRUPOS = 'editar_grupos';
    case CANCELAR_GRUPOS = 'cancelar_grupos';

    // Usuarios
    case VER_USUARIOS = 'ver_usuarios';
    case CREAR_USUARIOS = 'crear_usuarios';
    case EDITAR_USUARIOS = 'editar_usuarios';
    case CANCELAR_USUARIOS = 'cancelar_usuarios';

    // Escuelas
    case VER_ESCUELAS = 'ver_escuelas';
    case CREAR_ESCUELAS = 'crear_escuelas';
    case EDITAR_ESCUELAS = 'editar_escuelas';
    case CANCELAR_ESCUELAS = 'cancelar_escuelas';

    // Configuración y Permisos
    case VER_CATALOGOS = 'ver_catalogos';
    case VER_PERMISOS = 'ver_permisos';
    case CREAR_PERMISOS = 'crear_permisos';
    case ACTIVAR_PERMISOS = 'activar_permisos';
    case DESACTIVAR_PERMISOS = 'desactivar_permisos';
    case VER_PANEL_PERMISOS = 'ver_panel_permisos';
    case VER_PERMISOS_USUARIOS = 'ver_permisos_usuarios';

    // Roles
    case VER_ROLES = 'ver_roles';
    case CREAR_ROLES = 'crear_roles';
    case EDITAR_ROLES = 'editar_roles';
    case CANCELAR_ROLES = 'cancelar_roles';

    // Perfil
    case VER_PERFIL = 'ver_perfil';
    case EDITAR_PERFIL = 'editar_perfil';
}