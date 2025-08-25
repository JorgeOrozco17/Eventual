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
        $stmt = $this->conn->prepare("SELECT * FROM pension");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deletePension($id) {
        $stmt = $this->conn->prepare("DELETE FROM pension WHERE id = ?");
        return $stmt->execute([$id]);
    }

}


?>