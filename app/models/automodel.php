<?php

require_once 'dbconexion.php';


class AutoModel {
    private $conn;

    public function __construct(){
        $db = new DBConexion();
        $this->conn = $db->getConnection();
    }     

}
?>