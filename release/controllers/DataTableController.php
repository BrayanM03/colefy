<?php
// controllers/DataTableController.php (Ajustado)
abstract class DataTableController {
    // La propiedad que contendrá la instancia del modelo que maneja la tabla
    protected $model; 

    public function __construct() {
        // Inicializa id_sesion u otros datos comunes.
    }

    /**
     * Centraliza la lógica de DataTables (obtener parámetros, llamar al modelo, responder).
     * Este es el "Template Method".
     */
    public function datatable_general($id_filtro='', $filtros = []) {
        // --- 1. Obtener Parámetros Comunes --- 
        $request = array_merge($_GET, $_POST);

        $start = $request['start'] ?? 0;
        $length = $request['length'] ?? 10;
        $search = $request['search']['value'] ?? '';
        $orderColumnIndex = $request['order'][0]['column'] ?? 0;
        $orderColumnName  = $request['columns'][$orderColumnIndex]['data'] ?? 'id';
        $orderDir         = $request['order'][0]['dir'] ?? 'asc';
        $draw             = $request['draw'] ?? 1;

        // --- 2. Lógica Específica (Template Methods que las subclases deben implementar) ---
        // Aquí es donde cada controlador hijo proporcionará los datos.
        $data = $this->getModelData($id_filtro, $start, $length, $search, $orderColumnName, $orderDir, $filtros);
        $total = $this->getModelTotal($id_filtro, $filtros);
        $filtered = $this->getModelFilteredTotal($id_filtro, $search, $filtros);

        // --- 3. Respuesta Común ---
        echo json_encode([
            "draw" => $draw,
            "recordsTotal" => $total,
            "recordsFiltered" => $filtered, 
            "data" => $data
        ]);
    }

    // --- Métodos Abstractos / De Plantilla (A implementar por cada controlador) ---
    abstract protected function getModelData($id_filtro, $start, $length, $search, $orderColumnName, $orderDir, $filtros);
    abstract protected function getModelTotal($id_filtro, $filtros);
    abstract protected function getModelFilteredTotal($id_filtro, $search, $filtros);
}
?>