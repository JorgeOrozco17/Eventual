<?php
require_once 'dbconexion.php';

class deduccionmodel{
    private $conn;

    public function __construct() {
        $db = new DBConexion();
        $this->conn = $db->getConnection();
    }

    public function log_accion($id_usuario, $accion, $id_acciones) {
        $stmt = $this->conn->prepare("INSERT INTO logs (id_usuario, id_acciones, accion) VALUES (?, ?, ?)");
        return $stmt->execute([$id_usuario, $id_acciones, $accion]);
    }

    public function getAllPensiones(){
        $stmt = $this->conn->prepare("SELECT p.*, per.nombre_alta AS empleado FROM pension p JOIN personal per ON p.id_personal = per.id");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deletePension($id) {
        $stmt = $this->conn->prepare("DELETE FROM pension WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getAllTemporales(){
        $stmt = $this->conn->prepare("
            SELECT dt.*, p.nombre_alta AS empleado
            FROM deducciones_temporales dt
            JOIN personal p ON dt.id_personal = p.id "
    );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllTemporalesById($id){
        $stmt = $this->conn->prepare("
            SELECT dt.*, p.nombre_alta AS empleado
            FROM deducciones_temporales dt
            JOIN personal p ON dt.id_personal = p.id
            WHERE dt.id = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

     public function SaveTemporal($data) {
        $sql = "INSERT INTO deducciones_temporales 
                (id_personal, concepto, monto_total, monto, fecha_inicio, fecha_fin, id_usuario) 
                VALUES (:id_personal, :concepto, :monto_total, :monto, :fecha_inicio, :fecha_fin, :id_usuario)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($data);
        return $this->conn->lastInsertId();
    }

    public function UpdateTemporal($data) {
        $sql = "UPDATE deducciones_temporales 
                SET id_personal = :id_personal,
                    concepto    = :concepto,
                    monto_total = :monto_total,
                    monto       = :monto,
                    fecha_inicio= :fecha_inicio,
                    fecha_fin   = :fecha_fin,
                    id_usuario  = :id_usuario
                WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($data);
    }

}
?>