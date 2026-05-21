<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/dates.php';
require_once __DIR__ . '/../models/Datatable.php';
require_once __DIR__ . '/../models/Alumno.php'; 

/* include "../helpers/response_helper.php"; */

class Gasto extends Datatable
{
    private $fecha;
    
    private $tabla;
    private $allowedColumns = ['id', 'concepto', 'observaciones', 'proveedor', 'usuario', 'tipo_comprobante'];
    private $id_escuela;
    private $vista;
    private $tabla_conceptos;
    private $tabla_pagos;
    private $id_sesion;

    public function __construct()
    {
        $this->db = new Database();
        $this->vista = 'vista_gastos';
        $this->tabla = 'gastos';
        $this->fecha = new Date();
        $this->id_escuela = $_SESSION['id_escuela'];
        $this->id_sesion = $_SESSION['id'];
      
    }
    
    private function prepararFiltrosGastos($id_escuela, $filtros) {
        $extraWhere = "id_escuela = :id_escuela";
        $params     = [':id_escuela' => $id_escuela];
       
        // Folio (id_gasto)
        if (!empty($filtros['folio'])) {
            $extraWhere .= " AND id = :folio";
            $params[':folio'] = $filtros['folio'];
        }
    
        // Ciclo escolar
        if (!empty($filtros['ciclo'])) {
            $extraWhere .= " AND id_ciclo = :ciclo";
            $params[':ciclo'] = $filtros['ciclo'];
        }
    
        // Categoría (id_subcategoria)
        if (!empty($filtros['subcategoria'])) {
            $extraWhere .= " AND id_subcategoria = :subcategoria";
            $params[':subcategoria'] = $filtros['subcategoria'];
        }
    
        // Forma de pago
        if (isset($filtros['forma_pago']) && $filtros['forma_pago'] !== '') {
            $extraWhere .= " AND forma_pago = :forma_pago";
            $params[':forma_pago'] = $filtros['forma_pago'];
        }
    
        // Tipo de comprobante
        if (isset($filtros['comprobante']) && $filtros['comprobante'] !== '') {
            $extraWhere .= " AND tipo_comprobante = :comprobante";
            $params[':comprobante'] = $filtros['comprobante'];
        }
      
        // Rango de fechas
        if (!empty($filtros['fecha_inicio']) || !empty($filtros['fecha_fin'])) {
           if(!empty($filtros['fecha_inicio']) && !empty($filtros['fecha_fin'])) {
            $extraWhere .= " AND fecha BETWEEN :fecha_inicio AND :fecha_fin";
            $params[':fecha_inicio'] = $filtros['fecha_inicio'];
            $params[':fecha_fin']    = $filtros['fecha_fin'];
            }else if(!empty($filtros['fecha_inicio']) && empty($filtros['fecha_fin'])){
                $extraWhere .= " AND fecha >= :fecha_inicio";
                $params[':fecha_inicio'] = $filtros['fecha_inicio'];
            }else if(!empty($filtros['fecha_fin']) && empty($filtros['fecha_inicio'])){
                $extraWhere .= " AND fecha <= :fecha_fin";
                $params[':fecha_fin']    = $filtros['fecha_fin'];
            }
            
        }
    
        // Estatus (puede venir como array o valor simple)
        if (!empty($filtros['estatus'])) {
            if (is_array($filtros['estatus'])) {
                $placeholders = [];
                foreach ($filtros['estatus'] as $index => $valor) {
                    $key = ":est_" . $index;
                    $placeholders[] = $key;
                    $params[$key]   = $valor;
                }
                $extraWhere .= " AND estatus IN (" . implode(',', $placeholders) . ")";
            } else {
                $extraWhere .= " AND estatus = :estatus";
                $params[':estatus'] = $filtros['estatus'];
            }
        }
    
        // Usuario que registró el gasto
        if (!empty($filtros['id_usuario'])) {
            $extraWhere .= " AND id_usuario = :id_usuario";
            $params[':id_usuario'] = $filtros['id_usuario'];
        }
    
        return ['where' => $extraWhere, 'params' => $params];
    }

    //-------INICIO FUNCIONES DATATABLES-----
    /*Funcion que obtiene los datos iniciales recibe el id de la escuela y filtros extra*/
    public function datatablesGastos($id_filtro, $start, $length, $search, $orderColumn, $orderDir, $filtros=[])
    {
        $prep = $this->prepararFiltrosGastos($id_filtro, $filtros);
       
        // 2. Llama al método genérico de la clase Datatable
        return $this->getDataTable(
            $this->vista,
            $start, 
            $length, 
            $search, 
            $orderColumn, 
            $orderDir, 
            ['id', 'concepto', 'observaciones', 'proveedor', 'usuario', 'tipo_comprobante'], 
            $prep['where'], 
            $prep['params']
        );
    }

    /*Funcion contar gastos que recibe el id de la escuela y filtros extra*/
    public function contarGastos($id_filtro, $filtros = [])
    {
         $prep = $this->prepararFiltrosGastos($id_filtro, $filtros);
         return $this->countAll($this->vista, $prep['where'], $prep['params']);
    }

    /*Funcion contar recibos filtrados que recibe el id de la escuela y filtros extra, para cuando se usan los filtros*/
    public function contarGastosFiltrados($id_filtro, $search, $filtros = [])
    {
      
        $prep = $this->prepararFiltrosGastos($id_filtro, $filtros);
        return $this->countFiltered($this->vista, $search,  ['alumno', 'ciclo'], $prep['where'], $prep['params']);
  
    }

    public function comboCategoriasGastos(){

        $stmt = $this->db->query("SELECT COUNT(*) as total FROM vista_subcategorias_gastos WHERE activo = 1", []);
        if($stmt->fetch(PDO::FETCH_ASSOC)['total'] ==0){
            return array('estatus'=> false, 'mensaje'=>'Sin categorias registradas registrados');
        }else{
            $query = "SELECT * FROM vista_subcategorias_gastos WHERE activo=1 ORDER BY id_categoria, id_subcategoria;";
            $params = [];
            $stmt = $this->db->query($query, $params);
            return array('estatus'=>true, 'mensaje'=>'Se encontrarón categorias','data'=>$stmt->fetchAll(PDO::FETCH_ASSOC));
        };
    }

    public function registrarGasto($data){

        $fecha            = $data['fecha'];
        $proveedor        = $data['proveedor'];
        $ciclo            = $data['ciclo'];
        $tipo_comprobante = $data['tipo_comprobante'];
        $categoria        = $data['categoria'];
        $concepto         = $data['concepto'];
        $monto            = $data['monto'];
        $observaciones    = $data['observaciones'];
        $forma_pago       = $data['formas_pago'];
        $comprobante      = isset($_FILES['comprobante']) ? $_FILES['comprobante']:null;
        
    
        // ── Validaciones básicas ──────────────────────────────────────────
        if(empty($fecha) || empty($ciclo) || empty($categoria) || empty($concepto) || empty($monto)){
            return ['estatus' => false, 'mensaje' => 'Faltan campos obligatorios', 'data' => []];
        }
    
        if(!is_numeric($monto) || $monto <= 0){
            return ['estatus' => false, 'mensaje' => 'El monto debe ser un número mayor a 0', 'data' => []];
        }
    
     
        // ── Manejo del comprobante (opcional) ────────────────────────────
        $ruta_guardar = null;
    
        $hay_archivo = isset($comprobante) 
                    && $comprobante['error'] !== UPLOAD_ERR_NO_FILE 
                    && $comprobante['size']  > 0;
    
        if($hay_archivo){
    
            // Validar error de subida
            if($comprobante['error'] !== UPLOAD_ERR_OK){
                return ['estatus' => false, 'mensaje' => 'Error al subir el archivo', 'data' => []];
            }
    
            // Validar tipo de archivo (PDF e imágenes)
            $tipos_permitidos = ['application/pdf', 'image/jpeg', 'image/png', 'image/webp'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime  = finfo_file($finfo, $comprobante['tmp_name']);
            finfo_close($finfo);
    
            if(!in_array($mime, $tipos_permitidos)){
                return ['estatus' => false, 'mensaje' => 'Solo se permiten archivos PDF, JPG, PNG o WEBP', 'data' => []];
            }
    
            // Validar tamaño (máx 5MB)
            $max_bytes = 5 * 1024 * 1024;
            if($comprobante['size'] > $max_bytes){
                return ['estatus' => false, 'mensaje' => 'El archivo no debe superar 5MB', 'data' => []];
            }
    
            // Generar nombre único para evitar colisiones
            $extension    = pathinfo($comprobante['name'], PATHINFO_EXTENSION);
            $nombre_unico = 'gasto_' . date('Ymd') . '_' . uniqid() . '.' . strtolower($extension);
    
            $directorio = ROOT_PATH . 'static/docs/comprobantes_gastos/';
    
            // Crear carpeta si no existe
            if(!is_dir($directorio)){
                mkdir($directorio, 0755, true);
            }
    
            $ruta_guardar = $directorio . $nombre_unico;
            $ruta_bd = 'static/docs/comprobantes_gastos/' . $nombre_unico;
    
            if(!move_uploaded_file($comprobante['tmp_name'], $ruta_guardar)){
                return ['estatus' => false, 'mensaje' => 'No se pudo guardar el archivo en el servidor', 'data' => []];
            }
        }else{
            $ruta_guardar = [];
            $ruta_bd =null;
        }
    
        // ── Insertar en BD ───────────────────────────────────────────────
        $id_gasto = $this->db->insert(
            $this->tabla,
            [
                'id_ciclo'            => $ciclo,
                'id_subcategoria'     => $categoria,
                'fecha'               => $fecha,
                'proveedor'           => $proveedor        ?: null,
                'tipo_comprobante'    => $tipo_comprobante,
                'concepto'            => $concepto,
                'forma_pago'          => $forma_pago,
                'monto'               => $monto,
                'comprobante_archivo' => $ruta_bd,    // null si no subieron archivo
                'observaciones'       => $observaciones    ?: null,
                'estatus'             => 1,
                'id_escuela'          => $this->id_escuela,
                'id_usuario'          => $this->id_sesion
            ]
        );
    
        if(!$id_gasto){
            // Si ya se subió el archivo y falló el insert, lo eliminamos
            if($ruta_guardar && file_exists($ruta_guardar)){
                unlink($ruta_guardar);
            }
            return ['estatus' => false, 'mensaje' => 'Error al registrar el gasto en la base de datos', 'data' => []];
        }
    
        return [
            'estatus' => true,
            'mensaje' => 'Gasto registrado correctamente',
            'data'    => ['id_gasto' => $id_gasto, 'ruta' => $ruta_guardar]
        ];
    }

    public function actualizarGasto($data){

        $id_gasto         = $data['id_gasto'];
        $fecha            = $data['fecha'];
        $proveedor        = $data['proveedor'];
        $ciclo            = $data['ciclo'];
        $tipo_comprobante = $data['tipo_comprobante'];
        $categoria        = $data['categoria'];
        $concepto         = $data['concepto'];
        $monto            = $data['monto'];
        $observaciones    = $data['observaciones'];
        $forma_pago       = $data['formas_pago'];
        $comprobante      = isset($_FILES['comprobante']) ? $_FILES['comprobante']:null;
        
    
        // ── Validaciones básicas ──────────────────────────────────────────
        if(empty($fecha) || empty($ciclo) || empty($categoria) || empty($concepto) || empty($monto)){
            return ['estatus' => false, 'mensaje' => 'Faltan campos obligatorios', 'data' => []];
        }
    
        if(!is_numeric($monto) || $monto <= 0){
            return ['estatus' => false, 'mensaje' => 'El monto debe ser un número mayor a 0', 'data' => []];
        }
    
     
    // ── Obtener el gasto actual para conservar/eliminar comprobante ───
    $gasto_actual = $this->db->select(
        "SELECT comprobante_archivo FROM {$this->tabla} WHERE id = ? AND id_escuela = ?",
        [$id_gasto, $this->id_escuela]
    );

    if(empty($gasto_actual)){
        return ['estatus' => false, 'mensaje' => 'El gasto no existe o no tienes permiso para editarlo', 'data' => []];
    }

    $comprobante_anterior = $gasto_actual[0]['comprobante_archivo']; // ruta anterior en BD
    $ruta_bd              = $comprobante_anterior;                   // por defecto conserva el anterior

    // ── Manejo del nuevo comprobante (opcional) ───────────────────────
    $hay_archivo = isset($comprobante)
                && $comprobante['error'] !== UPLOAD_ERR_NO_FILE
                && $comprobante['size']  > 0;

    $ruta_fisica_nueva = null;

    if($hay_archivo){

        if($comprobante['error'] !== UPLOAD_ERR_OK){
            return ['estatus' => false, 'mensaje' => 'Error al subir el archivo', 'data' => []];
        }

        // Validar tipo MIME real
        $tipos_permitidos = ['application/pdf', 'image/jpeg', 'image/png', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $comprobante['tmp_name']);
        finfo_close($finfo);

        if(!in_array($mime, $tipos_permitidos)){
            return ['estatus' => false, 'mensaje' => 'Solo se permiten archivos PDF, JPG, PNG o WEBP', 'data' => []];
        }

        // Validar tamaño (máx 5MB)
        if($comprobante['size'] > 5 * 1024 * 1024){
            return ['estatus' => false, 'mensaje' => 'El archivo no debe superar 5MB', 'data' => []];
        }

        // Generar nombre único
        $extension         = strtolower(pathinfo($comprobante['name'], PATHINFO_EXTENSION));
        $nombre_unico      = 'gasto_' . date('Ymd') . '_' . uniqid() . '.' . $extension;
        $directorio        = ROOT_PATH . 'static/docs/comprobantes_gastos/';
        $ruta_fisica_nueva = $directorio . $nombre_unico;
        $ruta_bd_nueva     = 'static/docs/comprobantes_gastos/' . $nombre_unico;

        if(!is_dir($directorio)){
            mkdir($directorio, 0755, true);
        }

        if(!move_uploaded_file($comprobante['tmp_name'], $ruta_fisica_nueva)){
            return ['estatus' => false, 'mensaje' => 'No se pudo guardar el archivo en el servidor', 'data' => []];
        }

        // Subida exitosa → usar la nueva ruta en BD
        $ruta_bd = $ruta_bd_nueva;
    }

    // ── Actualizar en BD ──────────────────────────────────────────────
    $actualizado = $this->db->update(
        $this->tabla,
        [
            'id_ciclo'            => $ciclo,
            'id_subcategoria'     => $categoria,
            'fecha'               => $fecha,
            'proveedor'           => $proveedor     ?: null,
            'tipo_comprobante'    => $tipo_comprobante,
            'concepto'            => $concepto,
            'forma_pago'          => $forma_pago,
            'monto'               => $monto,
            'comprobante_archivo' => $ruta_bd,
            'observaciones'       => $observaciones ?: null,
        ],
        "id = ? AND id_escuela = ?",
        [$id_gasto, $this->id_escuela]
    );

    if(!$actualizado){
        // Falló el update → eliminar el archivo nuevo que ya se subió
        if($ruta_fisica_nueva && file_exists($ruta_fisica_nueva)){
            unlink($ruta_fisica_nueva);
        }
        return ['estatus' => false, 'mensaje' => 'Error al actualizar el gasto', 'data' => []];
    }

    // ── Update exitoso → eliminar comprobante anterior si había uno nuevo
    if($hay_archivo && !empty($comprobante_anterior)){
        $ruta_fisica_anterior = ROOT_PATH . $comprobante_anterior;
        if(file_exists($ruta_fisica_anterior)){
            unlink($ruta_fisica_anterior);
        }
    }

    return [
        'estatus' => true,
        'mensaje' => 'Gasto actualizado correctamente',
        'data'    => ['id_gasto' => $id_gasto, 'comprobante' => $ruta_bd]
    ];
     
    }


    public function obtenerGasto($id_gasto)
    {
        $count = $this->db->count($this->vista, 'id = ? AND id_escuela = ?', [$id_gasto, $this->id_escuela]);
        if ($count == 0) {
            return array('estatus' => false, 'mensaje' => 'No se encontró un gasto con el ID: ' . $id_gasto, 'data' => []);
        } else {
            $gasto_data = $this->db->select("SELECT * FROM {$this->vista} WHERE id =? AND id_escuela = ?", [$id_gasto, $this->id_escuela]);
            $escuela_data = $this->db->select('SELECT * FROM escuelas WHERE id = ? AND estatus != 0', [$this->id_escuela]);
            $gasto_data = $gasto_data[0];
            $usuario_data = $this->db->select('SELECT id, concat(nombre," ",apellido) as nombre_completo FROM usuarios WHERE id = ?', [$gasto_data['id_usuario']]);
            $usuario_data = $usuario_data[0];
            $escuela_data = $escuela_data[0];
            /* print_r($escuela_data);
            die(); */
            $gasto_data['escuela'] = $escuela_data;
            $gasto_data['usuario'] = $usuario_data;

            return array('estatus' => true, 'mensaje' => 'Se encontró información de recibo', 'data' => $gasto_data);
        }
    }

    public function cancelarGasto($id_gasto)
    {
        $count = $this->db->count('gastos', 'id = ?', [$id_gasto]);
        if ($count == 0) {
            return array('estatus' => false, 'mensaje' => 'No se encontró un gasto con el ID: ' . $id_gasto, 'data' => []);
        } else {
            $this->db->cancel('gastos', $id_gasto);
            return array('estatus' => true, 'mensaje' => '<b>Gasto:</b> GST-' . $id_gasto .' cancelado correctamente', 'data' => []);

        }
    }

    public function descancelarGasto($id_gasto)
    {
        $count = $this->db->count('gastos', 'id = ?', [$id_gasto]);
        if ($count == 0) {
            return array('estatus' => false, 'mensaje' => 'No se encontró un gastoo con el ID: ' . $id_gasto, 'data' => []);
        } else {
            $this->db->uncancel('gastos', $id_gasto);
            return array('estatus' => true, 'mensaje' => '<b>Gasto:</b> GST-' . $id_gasto .' descancelado correctamente', 'data' => []);

        }
    }
}
