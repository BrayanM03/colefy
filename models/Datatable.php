<?php
class Datatable {
    protected $db; // instancia de Database

    public function __construct(Database $conexion) {
        $this->db = $conexion;
    }

    public function getDataTable(
        string $tabla,
        int $start,
        int $length,
        ?string $search,
        string $orderColumn,
        string $orderDir,
        array $allowedColumns,
        string $extraWhere = "",
        array $params = [] 
    ): array {
        $sql = "SELECT * FROM $tabla WHERE 1=1";

        // Condiciones extra
        if (!empty($extraWhere)) {
            $sql .= " AND ($extraWhere)";
        }

        // Search global
        if (!empty($search)) {
            $searchConditions = [];
            foreach ($allowedColumns as $col) {
                $searchConditions[] = "$col LIKE :search";
            }
            $sql .= " AND (" . implode(" OR ", $searchConditions) . ")";
            $params[':search'] = "%$search%";
        }

        // Seguridad en orden
        if (!in_array($orderColumn, $allowedColumns)) {
            $orderColumn = $allowedColumns[0];
        }
        $orderDir = strtolower($orderDir) === 'desc' ? 'DESC' : 'ASC';

        $sql .= " ORDER BY $orderColumn $orderDir LIMIT $start, $length";

        return $this->db->select($sql, $params); // usa tu wrapper
    }

    public function countAll(string $tabla, string $extraWhere = "", array $params = []): int {
        $sql = "1=1";
        if (!empty($extraWhere)) {
            $sql .= " AND ($extraWhere)";
        }
        return (int)$this->db->count($tabla, $sql, $params);
    }

    public function countFiltered(
        string $tabla,
        ?string $search,
        array $allowedColumns,
        string $extraWhere = "",
        array $params = []
    ): int {
        $sql = "1=1";

        if (!empty($extraWhere)) {
            $sql .= " AND ($extraWhere)";
        }

        if (!empty($search)) {
            $searchConditions = [];
            foreach ($allowedColumns as $col) {
                $searchConditions[] = "$col LIKE :search";
            }
            $sql .= " AND (" . implode(" OR ", $searchConditions) . ")";
            $params[':search'] = "%$search%";
        }

        return (int)$this->db->count($tabla, $sql, $params);
    }
}
?>