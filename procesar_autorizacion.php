<?php
require_once 'app/helpers/util.php';
require_once 'app/models/dbconexion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_personal = $_POST['id'] ?? null;
    $cuenta = $_POST['cuenta'] ?? null;
    $id_usuario = $_SESSION['user_id'] ?? 0;

    if (!$id_personal || !$cuenta) {
        die('Faltan datos requeridos.');
    }

    $db = new DBConexion();
    $conn = $db->getConnection();

    // 1. Actualizar cuenta bancaria en la tabla captura
    $stmt1 = $conn->prepare("UPDATE captura SET cuenta = ? WHERE id_personal = ?");
    $stmt1->execute([$cuenta, $id_personal]);

    // 2. Actualizar campo de autorización en tabla personal
    $stmt2 = $conn->prepare("UPDATE personal SET autorizacion = 1 WHERE id = ?");
    $stmt2->execute([$id_personal]);

    // 3. Reenviar archivo al manejador de archivos (guardar_archivos.php)
    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
        $_FILES['autorizacion'] = $_FILES['archivo'];
        unset($_FILES['archivo']);

        $_POST['id_personal'] = $id_personal;

        include '/guardar_archivos.php';  // Este script ya hace redirección a archivodetalle.php
        exit;
    } else {
        // Si no se subió el archivo, redirigir con mensaje de error
        header("Location: archivodetalle.php?id=$id_personal&error=Archivo no cargado");
        exit;
    }
} else {
    header("Location: /autoriza.php");
    exit;
}
