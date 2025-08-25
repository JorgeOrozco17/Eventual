<?php
// controllers/logincontroller.php

require_once __DIR__ . '/../models/dbconexion.php';  // Ruta corregida
require_once __DIR__ . '/../models/loginmodel.php';

class LoginController {

    private $model;

    public function __construct() {
        // Inicializa la conexión y el modelo
        $db = new DBConexion();  // Asegúrate de que DBConexion esté bien configurado
        $this->model = new LoginModel($db->getConnection());
    }

    // Método para procesar el inicio de sesión
    public function login($username, $password) {
        $user = $this->model->checkLogin($username, $password);

        if ($user) {
            // Si el login es exitoso, podrías guardar datos en la sesión
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['s_usuario'] = $user['usuario'];
            $_SESSION['role'] = $user['rol'];
            $_SESSION['image'] = $user['archivo'];
            $_SESSION['juris'] = $user['juris'];

            // Redirigir a la página principal
            header("Location: index.php");
            exit();
        } else {
            // Si el login falla, enviar mensaje de error
            return "Usuario o contraseña incorrectos.";
        }
    }
}
?>
