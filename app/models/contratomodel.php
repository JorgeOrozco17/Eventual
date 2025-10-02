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

    public function getEmpleadosByCentro($user_id) {
        $stmt = $this->conn->prepare("SELECT p.*
            FROM personal p
            JOIN responsables r ON p.id_centro = r.id_centro
            WHERE id_usuario = :id_usuario
            ORDER BY p.nombre_alta");
        $stmt->execute(['id_usuario' => $user_id]);
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

    public function getCargoById($user_id){
        $stmt = $this->conn->prepare("SELECT cargo FROM responsables WHERE id_usuario = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn();
    }

    public function getResponsableByUser($user_id){
        $stmt = $this->conn->prepare("SELECT u.nombre AS nombre_responsable
            FROM usuarios u
            JOIN responsables r ON u.id = r.rh_responsable
            WHERE r.id_usuario = :id_usuario
            ORDER BY r.id_centro DESC
            LIMIT 1");
        $stmt->execute(['id_usuario' => $user_id]);
        return $stmt->fetchColumn();
    }

}

?>