<?php
require_once __DIR__ . '/dbconexion.php';

class DashboardModel {
    private $conn;

    public function __construct() {
        $db = new DBConexion();
        $this->conn = $db->getConnection();
    }

    public function getEmpleadosActivos() {
        $stmt = $this->conn->query("SELECT COUNT(*) FROM personal WHERE estatus = 'activo'");
        return (int)$stmt->fetchColumn();
    }

    public function getLicenciasActuales($qnaActual, $anioActual) {
        $stmt = $this->conn->prepare("SELECT COALESCE(SUM(dias),0) 
                                      FROM faltas 
                                      WHERE tipo='licencia' AND quincena = ? AND año = ?");
        $stmt->execute([$qnaActual, $anioActual]);
        return (int)$stmt->fetchColumn();
    }

    public function getSerieNomina() {
        $stmt = $this->conn->query("SELECT AÑO AS anio, QNA AS quincena, 
                                           COALESCE(SUM(TOTAL_NETO),0) AS total_neto,
                                           COALESCE(SUM(PERCEPCIONES),0) AS percepciones,
                                           COALESCE(SUM(DEDUCCIONES),0) AS deducciones,
                                           COUNT(*) AS empleados
                                    FROM captura
                                    GROUP BY AÑO, QNA
                                    ORDER BY anio DESC, quincena DESC
                                    LIMIT 8");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_reverse($data);
    }

    public function getSerieFaltasLic() {
        $stmt = $this->conn->query("SELECT año, quincena,
                                           SUM(CASE WHEN tipo='falta' THEN COALESCE(dias,1) ELSE 0 END) AS faltas_dias,
                                           SUM(CASE WHEN tipo='licencia' THEN COALESCE(dias,1) ELSE 0 END) AS licencias_dias
                                    FROM faltas
                                    GROUP BY año, quincena
                                    ORDER BY año DESC, quincena DESC
                                    LIMIT 8");
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_reverse($data);
    }

    public function getPieAdscripciones() {
        $stmt = $this->conn->query("SELECT COALESCE(adscripcion, CONCAT('Juris ', adscripcion)) AS etiqueta,
                                           COUNT(*) AS total
                                    FROM personal
                                    WHERE estatus='activo'
                                    GROUP BY adscripcion, adscripcion
                                    ORDER BY total DESC
                                    LIMIT 9");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUltimosMovs() {
        $stmt = $this->conn->query("SELECT id, nombre, jurisdiccion, quincena, año, tipo, dias, fechas, NOW() AS ts
                                    FROM faltas
                                    ORDER BY id DESC
                                    LIMIT 10");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
