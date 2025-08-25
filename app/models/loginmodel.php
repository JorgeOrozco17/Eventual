<?php


class LoginModel {

    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // Método para verificar las credenciales del usuario
    public function checkLogin($username, $password) {
        // 1. Buscar usuario por nombre de usuario
        $query = "SELECT * FROM usuarios WHERE usuario = :username";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
    
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // 2. Verificar contraseña con password_verify
        if ($user && $password === $user['contraseña']) {
            return $user;
        }        
    
        return false; // Login fallido
    }
}
?>
