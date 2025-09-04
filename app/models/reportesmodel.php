<?php 

require_once 'dbconexion.php';

class ReportesModel{
    private $conn;

    public function __construct(){
        $db = new DBConexion();
        $this->conn = $db->getConnection();
    }


    public function getAltasBajasByQuincena($qna, $anio, $tipo){
        if ($tipo === 'all') {
            $stmt = $this->conn->prepare("
                SELECT * 
                FROM personal 
                WHERE quincena_alta = ? 
                AND YEAR(inicio_contratacion) = ?");
            $stmt->execute([$qna, $anio]);
        } else {
            $stmt = $this->conn->prepare("
                SELECT * 
                FROM personal 
                WHERE quincena_alta = ? 
                AND YEAR(inicio_contratacion) = ? 
                AND movimiento = ?");
            $stmt->execute([$qna, $anio, $tipo]);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getAltasBajasByPeriodo($qnaInicio, $qnaFin, $tipo) {
        if ($tipo === 'all') {
            $stmt = $this->conn->prepare("
                SELECT * 
                FROM personal 
                WHERE inicio_contratacion BETWEEN ? AND ? 
                ");
            $stmt->execute([$qnaInicio, $qnaFin]);
        } else {
            $stmt = $this->conn->prepare("
                SELECT * 
                FROM personal 
                WHERE inicio_contratacion BETWEEN ? AND ? 
                AND movimiento = ?");
            $stmt->execute([$qnaInicio, $qnaFin, $tipo]);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}

?>