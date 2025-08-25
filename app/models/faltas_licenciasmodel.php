<?php
require_once 'dbconexion.php';

class Faltas_licenciasmodel {

    private $conn;

    public function __construct() {
        $db = new DBConexion();
        $this->conn = $db->getConnection();
    }

    public function getAllFaltas(){
        $stmt = $this->conn->prepare("SELECT * FROM faltas WHERE tipo = 'falta' ORDER BY quincena DESC, id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllLicencias(){
        $stmt = $this->conn->prepare("SELECT * FROM faltas WHERE tipo = 'licencia' ORDER BY quincena DESC, id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id){
        $stmt = $this->conn->prepare("SELECT * FROM faltas WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function delete($id){
        $stmt = $this->conn->prepare("DELETE FROM faltas WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getFaltasByQuincena($quincena, $anio){
        $stmt = $this->conn->prepare("SELECT * FROM faltas WHERE quincena = ? AND año = ? AND tipo = 'falta' ORDER BY quincena DESC, id DESC");
        $stmt->execute([$quincena, $anio]);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $res ?: [];
    }

    public function getFaltasByAnio($anio){
        $stmt = $this->conn->prepare("SELECT * FROM faltas WHERE año = ? AND tipo = 'falta' ORDER BY quincena DESC, id DESC");
        $stmt->execute([$anio]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLicenciasByQuincena($quincena, $anio){
        $stmt = $this->conn->prepare("SELECT * FROM faltas WHERE quincena = ? AND año = ? AND tipo = 'licencia' ORDER BY quincena DESC, id DESC");
        $stmt->execute([$quincena, $anio]);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $res ?: [];
    }

    public function save($data) {
        $anio = date('Y');
        $stmt = $this->conn->prepare("
            INSERT INTO faltas (
                id_personal, nombre, adscripcion, jurisdiccion, dias, fechas,
                periodo_1, periodo_2, observaciones, id_usuario, quincena, año, tipo, faltas
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $ok = $stmt->execute([
            $data['id_personal'],
            $data['nombre'],
            $data['adscripcion'],
            $data['jurisdiccion'],
            $data['dias'],
            $data['fechas'],
            $data['periodo_1'],
            $data['periodo_2'],
            $data['observaciones'],
            $data['id_usuario'],
            $data['quincena'],
            $anio,
            $data['tipo'],
            $data['faltas'] ?? 0
        ]);
        return $ok ? (int)$this->conn->lastInsertId() : 0;
    }


    public function update($data) {
        $stmt = $this->conn->prepare("
            UPDATE faltas SET
                id_personal = ?, nombre = ?, adscripcion = ?, jurisdiccion = ?, dias = ?, fechas = ?,
                periodo_1 = ?, periodo_2 = ?, observaciones = ?, id_usuario = ?, quincena = ?, tipo = ?, faltas = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['id_personal'],
            $data['nombre'],
            $data['adscripcion'],
            $data['jurisdiccion'],
            $data['dias'],
            $data['fechas'],
            $data['periodo_1'],
            $data['periodo_2'],
            $data['observaciones'],
            $data['id_usuario'],
            $data['quincena'],
            $data['tipo'],
            $data['faltas'] ?? 0,
            $data['id']
        ]);
    }

    public function updateFaltasColumn(int $id, float $faltas): bool {
        $stmt = $this->conn->prepare("UPDATE faltas SET faltas = ? WHERE id = ?");
        return $stmt->execute([$faltas, $id]);
    }

    public function updateResponsabilidades($id, $responsabilidades): bool {
        $stmt = $this->conn->prepare("UPDATE faltas SET responsabilidades = ? WHERE id = ?");
        return $stmt->execute([$responsabilidades, $id]);
    }


    //////////////////////////////// logs ////////////////////////////////

    public function log_accion($id_usuario, $accion, $id_acciones) {
        $stmt = $this->conn->prepare("INSERT INTO logs (id_usuario, accion, id_acciones) VALUES (?, ?, ?)");
        return $stmt->execute([$id_usuario, $accion, $id_acciones]);
    }

    /**
     *
     * @return array ['ok'=>bool, 'faltas'=>int]
     */
    public function calculodias(int $diasSolicitados, int $id_personal): array {
        if ($diasSolicitados <= 0) {
            return ['ok' => true, 'responsabilidades' => 0.0];
        }

        try {
            $this->conn->beginTransaction();

            // Obtener saldos actuales
            $stmtSel = $this->conn->prepare("
                SELECT dias_integros, dias_medios
                FROM calculo_personal
                WHERE id_personal = ?
                FOR UPDATE
            ");
            $stmtSel->execute([$id_personal]);
            $row = $stmtSel->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                $row = ['dias_integros' => 0, 'dias_medios' => 0];
                $this->conn->prepare("
                    INSERT INTO calculo_personal (id_personal, dias_integros, dias_medios)
                    VALUES (?, 0, 0)
                ")->execute([$id_personal]);
            }

            $integros = (int)$row['dias_integros'];
            $medios   = (int)$row['dias_medios'];
            $restantes = $diasSolicitados;

            $responsabilidades = 0.0;

            // 1) Consumir días íntegros
            $usaInt = min($restantes, $integros);
            $integros -= $usaInt;
            $restantes -= $usaInt;

            // 2) Consumir días medios (cada uno vale 1 día pero genera 0.5 responsabilidades)
            $usaMedios = min($restantes, $medios);
            $medios -= $usaMedios;
            $restantes -= $usaMedios;
            $responsabilidades += $usaMedios * 0.5;

            // 3) Lo que quede son faltas enteras → 1 responsabilidad por día
            if ($restantes > 0) {
                $responsabilidades += $restantes;
                $restantes = 0;
            }

            // Actualizar saldos en la BD
            $this->conn->prepare("
                UPDATE calculo_personal
                SET dias_integros = ?, dias_medios = ?
                WHERE id_personal = ?
            ")->execute([$integros, $medios, $id_personal]);

            $this->conn->commit();

            return [
                'ok' => true,
                'responsabilidades' => $responsabilidades
            ];

        } catch (Throwable $e) {
            if ($this->conn->inTransaction()) $this->conn->rollBack();
            return ['ok' => false, 'responsabilidades' => 0.0];
        }
    }

    

}
