<?php
require_once 'dbconexion.php';

class CatalogoModel {
    private $conn;
    
    public function __construct() {
        $db = new DBConexion();
        $this->conn = $db->getConnection();
    }

    //                            Metodos para jurisdiccion 
    public function getAllJurisdicciones() {
        $stmt = $this->conn->prepare("SELECT * FROM jurisdicciones");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getJurisdiccionById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM jurisdicciones WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function savejuris($data){
        try{
            $stmt = $this->conn->prepare("INSERT INTO jurisdiccion (nombre, ubicacion) VALUES (?, ?)");
            $stmt->execute([$data['nombre'], $data['ubicacion']]);
            return true;
        }catch(Exception){

        }
    }

    public function updateJuris($id, $nombre, $ubicacion){
        try {
            $stmt = $this->conn->prepare("UPDATE jurisdicciones SET nombre = ?, ubicacion = ? WHERE id = ?");
            $stmt->execute([$nombre, $ubicacion, $id]);
            return true;
        } catch(Exception $e){
            return false;
        }
    }
    

    public function deleteJurisdiccion($id) {
        $stmt = $this->conn->prepare("DELETE FROM jurisdicciones WHERE id = ?");
        return $stmt->execute([$id]);
    }

    //                Fin metodos de jurisdiccion

    //                  Metodos centros
    public function getAllCentros() {
        $sql = "SELECT 
                    c.id, 
                    c.nombre,
                    c.clues,
                    j.nombre AS jurisdiccion 
                FROM centros c
                INNER JOIN jurisdicciones j ON c.id_juris = j.id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCentrosByAdscripcion($adscrip_id) {
        $stmt = $this->conn->prepare("SELECT id, nombre FROM centros WHERE id_juris = ?");
        $stmt->execute([$adscrip_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // <-- Aquí sí devuelve los centros
    }
    

    //                 Fin metodos centros

    //                 Metodos recursos
    public function getAllRecursos() {
        $stmt = $this->conn->prepare("SELECT * FROM recurso");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    //              Fin Metodos recursos

    //               Metodos Puestos
    public function getAllPuestos(){
        $stmt = $this->conn->prepare("SELECT * FROM puestos");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //             Fin metodos puestos

    //             Metodos quincenas
    public function getAllQuincenas(){
        $stmt = $this->conn->prepare("SELECT * FROM quincenas");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getQuincenaByDate($date) {
    $year = date('Y');
    $stmt = $this->conn->prepare("SELECT * FROM quincenas");
    $stmt->execute();
    $quincenas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $hoy = DateTime::createFromFormat('d/m/Y', "$date/$year");
    foreach ($quincenas as $q) {
        $inicio = DateTime::createFromFormat('d/m/Y', "{$q['inicio']}/$year");
        $fin = DateTime::createFromFormat('d/m/Y', "{$q['fin']}/$year");
        if ($hoy >= $inicio && $hoy <= $fin) {
            return $q;
        }
    }
    return null;
}


    //          Fin metodos quincenas


    //            Metodos archivos

    public function getArchivosById($id){
        $stmt = $this->conn->prepare("SELECT * FROM archivos WHERE id_personal = ?");
        $stmt->execute([$id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //                 Fin metodos archivos

    public function getAllFijas(){
        $stmt = $this->conn->prepare("SELECT * FROM fijos");
        $stmt->execute();
        return $stmt->fetchall(PDO::FETCH_ASSOC);
    }

    public function getFijosById($id){
        $stmt = $this->conn->prepare("SELECT * FROM fijos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insertFijo($data) {
        $stmt = $this->conn->prepare("INSERT INTO fijos (concepto, nombre_concepto, cantidad) VALUES (?, ?, ?)");
        return $stmt->execute([
            $data['concepto'],
            $data['nombre_concepto'],
            $data['cantidad']
        ]);
    }

    public function updateFijo($data) {
        $stmt = $this->conn->prepare("UPDATE fijos SET concepto = ?, nombre_concepto = ?, cantidad = ? WHERE id = ?");
        return $stmt->execute([
            $data['concepto'],
            $data['nombre_concepto'],
            $data['cantidad'],
            $data['id']
        ]);
    }

    public function deleteFijo($id){
        $stmt = $this->conn->prepare("DELETE FROM fijos WHERE id = ?");
        return $stmt->execute([$id]);
    }

}
