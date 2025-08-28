<?php

require_once 'app/controllers/personalController.php'; // Ajusta la ruta si es necesario

include 'header.php'; // Asegúrate de que este archivo existe y contiene el encabezado HTML necesario

$controller = new PersonalController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $ok = $controller->BajaPersonal();
        // Si el método ya redirige, no necesitas nada más aquí.
        // Si quieres controlar la redirección aquí:
        if ($ok) {
            header("Location: autorizapersonal.php");
        } else {
            header("Location: personal.php?error=1");
        }
        exit();
    } catch (Exception $e) {
        echo "Error al procesar la baja: " . $e->getMessage();
        exit();
    }
} else {
    header("Location: bajaform.php?error=1");
    exit();
}
?>
