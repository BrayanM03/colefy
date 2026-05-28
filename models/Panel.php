<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../config/dates.php';
/* require_once __DIR__ . '/../models/Profesor.php';
require_once __DIR__ . '/../models/Grupo.php';
require_once __DIR__ . '/../models/Materia.php';
require_once __DIR__ . '/../models/Alumno.php'; */

class Panel {
    private $db;
    private $fecha;
    private $id_escuela;
    private $id_sesion;

    public function __construct() {
        $this->db = new Database();
        $this->fecha = new Date();
        $this->id_escuela = $_SESSION['id_escuela'];
        $this->id_sesion = $_SESSION['id'];

    }


    public function getDashboardData(){
        try {
            return [
                'estatus' => true,
                'stats'   => $this->getStats(),
                'alertas' => $this->getAlertas(),
                'niveles' => $this->getNiveles()
            ];
        } catch(Exception $e){
            return ['estatus' => false, 'mensaje' => 'Error al cargar el panel. Error: ' .$e];
        }
    }

     // ── Stats principales ─────────────────────────────────────────────
     private function getStats(){

        // Total alumnos activos
        $total_alumnos = $this->db->select(
            "SELECT COUNT(*) as total FROM alumnos 
             WHERE id_escuela = ? AND estatus = 1",
            [$this->id_escuela]
        )[0]['total'] ?? 0;

        // Alumnos mes pasado (para calcular % cambio)
        $alumnos_mes_pasado = $this->db->select(
            "SELECT COUNT(*) as total FROM alumnos 
             WHERE id_escuela = ? AND estatus = 1
             AND created_at < DATE_FORMAT(NOW(), '%Y-%m-01')",
            [$this->id_escuela]
        )[0]['total'] ?? 0;

        $alumnos_nuevos = $alumnos_mes_pasado > 0
            ? round((($total_alumnos - $alumnos_mes_pasado) / $alumnos_mes_pasado) * 100, 1)
            : 0;

        // Total profesores
        $total_profesores = $this->db->select(
            "SELECT COUNT(*) as total FROM profesores 
             WHERE id_escuela = ? AND estatus = 1",
            [$this->id_escuela]
        )[0]['total'] ?? 0;

        // Total grupos activos en ciclo actual
        $total_grupos = $this->db->select(
            "SELECT COUNT(*) as total FROM grupos g
             INNER JOIN ciclos_escolares c ON c.id = g.id_ciclo
             WHERE g.id_escuela = ? AND g.estatus = 1",
            [$this->id_escuela]
        )[0]['total'] ?? 0;

        // Ingresos del mes actual (recibos pagados/abonados)
        $ingresos_mes = $this->db->select(
            "SELECT COALESCE(SUM(monto_total - saldo_pendiente), 0) as total 
             FROM recibos
             WHERE id_escuela = ? 
             AND estatus IN (2, 3)
             AND MONTH(fecha_registro) = MONTH(NOW())
             AND YEAR(fecha_registro)  = YEAR(NOW())",
            [$this->id_escuela]
        )[0]['total'] ?? 0;

        // Gastos del mes actual
        $gastos_mes = $this->db->select(
            "SELECT COALESCE(SUM(monto), 0) as total 
             FROM gastos
             WHERE id_escuela = ? AND estatus = 1
             AND MONTH(fecha) = MONTH(NOW())
             AND YEAR(fecha)  = YEAR(NOW())",
            [$this->id_escuela]
        )[0]['total'] ?? 0;

        return [
            'total_alumnos'        => $total_alumnos,
            'alumnos_nuevos'       => $alumnos_nuevos,
            'total_profesores'     => $total_profesores,
            'total_departamentos'  => 0, // ajusta si tienes tabla departamentos
            'total_grupos'         => $total_grupos,
            'grupos_diff'          => 0, // ajusta si quieres comparar ciclos
            'ingresos_mes'         => $ingresos_mes,
            'gastos_mes'           => $gastos_mes,
            'balance'              => $ingresos_mes - $gastos_mes
        ];
    }

    // ── Alertas dinámicas ─────────────────────────────────────────────
    private function getAlertas(){
        $alertas = [];

        // Pagos por vencer mañana
        $pagos_vencer = $this->db->select(
            "SELECT COUNT(*) as total FROM recibos
             WHERE id_escuela = ? AND estatus IN (1, 6)
             AND DATE(fecha_vencimiento) = DATE_ADD(CURDATE(), INTERVAL 1 DAY)",
            [$this->id_escuela]
        )[0]['total'] ?? 0;

        if($pagos_vencer > 0){
            $alertas[] = [
                'tipo'        => 'pagos',
                'titulo'      => "{$pagos_vencer} Pagos pendientes de vencer mañana",
                'descripcion' => 'Corte de caja preventivo.',
                'btn_label'   => 'Ver lista',
                'btn_accion'  => "window.location.href='" . BASE_URL . "recibos'"
            ];
        }

        // Recibos vencidos sin pagar
        $recibos_vencidos = $this->db->select(
            "SELECT COUNT(*) as total FROM recibos
             WHERE id_escuela = ? AND estatus = 4",
            [$this->id_escuela]
        )[0]['total'] ?? 0;

        if($recibos_vencidos > 0){
            $alertas[] = [
                'tipo'        => 'pagos',
                'titulo'      => "{$recibos_vencidos} Recibos vencidos sin liquidar",
                'descripcion' => 'Requieren atención inmediata.',
                'btn_label'   => 'Revisar',
                'btn_accion'  => "window.location.href='" . BASE_URL . "recibos'"
            ];
        }

        // Gastos del mes vs ingresos (alerta si gastos > 80% de ingresos)
        $stats = $this->getStats();
        if($stats['ingresos_mes'] > 0){
            $porcentaje_gasto = ($stats['gastos_mes'] / $stats['ingresos_mes']) * 100;
            if($porcentaje_gasto >= 80){
                $alertas[] = [
                    'tipo'        => 'gasto',
                    'titulo'      => 'Gastos altos este mes (' . round($porcentaje_gasto) . '% de ingresos)',
                    'descripcion' => 'Los gastos están cerca del límite de ingresos.',
                    'btn_label'   => 'Ver gastos',
                    'btn_accion'  => "window.location.href='" . BASE_URL . "historial_gastos'"
                ];
            }
        }

        return $alertas;
    }

    // ── Distribución por nivel ────────────────────────────────────────
    private function getNiveles(){
        $niveles = $this->db->select(
            "SELECT n.nombre, COUNT(a.id) as total
             FROM alumnos a
             INNER JOIN alumnos_grupo ag ON ag.id_alumno = a.id
             INNER JOIN grupos g  ON ag.id_grupo  = g.id
             INNER JOIN niveles_educativos n ON n.id  = g.id_nivel
             WHERE a.id_escuela = ? AND a.estatus = 1
             GROUP BY n.id, n.nombre
             ORDER BY n.id",
            [$this->id_escuela]
        );

        return $niveles ?: [];
    }

}