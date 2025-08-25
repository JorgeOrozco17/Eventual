<?php
require_once 'dbconexion.php';

class Contratomodel {

    private $conn;

    public function __construct() {
        $db = new DBConexion();
        $this->conn = $db->getConnection();
    }

    public function getAllEmpleados() {
        $stmt = $this->conn->prepare("SELECT * FROM personal");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEmpleadoById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM personal WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }   

    public function getEmpleadosByJurisdiccion($jurisdiccion) {
        $stmt = $this->conn->prepare("SELECT * FROM personal WHERE id_adscripcion = ?");
        $stmt->execute([$jurisdiccion]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDatosContrato(){
        $stmt = $this->conn->prepare("SELECT  * FROM personal");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTrimestres(){
        $stmt = $this->conn->prepare("SELECT * FROM trimestres");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }   

}

?>