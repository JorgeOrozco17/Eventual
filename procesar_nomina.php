<?php
require_once 'app/controllers/capturacontroller.php';

 // Instanciar el controlador
    $controller = new Capturacontroller();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quincena = $_POST['quincena'];
    $anio = $_POST['año'];
    $tipo = $_POST['tipo'];


    // Validación para asegurar que los valores sean numéricos
    if (!is_numeric($quincena) || !is_numeric($anio)) {
        header("Location: generar_nomina.php");
        exit;
    }

    if ($tipo == 'Ordinaria') {
        // Guardar la nómina y verificar el resultado
        $resultado = $controller->saveNomina($tipo, $quincena, $anio);
    } else if ($tipo == 'EXT') {
        $resultado = $controller->saveNominaExtra($tipo, $quincena, $anio);
    }

    // Si la nómina ya existe, redirigir con un mensaje de error
    if ($resultado === 'existe') {
        header("Location: generar_nomina.php");
    } elseif ($resultado === 'exito') {
        // Si la nómina se generó correctamente, redirigir a la página de la nómina
        header("Location: nomina.php");
    } else {
        // En caso de otro error, redirigir con un mensaje de error
        header("Location: generar_nomina.php?error=2");
    }

    exit;
}

