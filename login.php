<?php

// CABECERAS PARA EVITAR CACHÉ
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
// Incluir el controlador de login
require_once 'app/controllers/logincontroller.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Si el formulario es enviado, obtenemos los datos
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Instanciar el controlador
    $loginController = new LoginController();
    $errorMessage = $loginController->login($username, $password);
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Meta tags y CSS (mantén tus estilos actuales) -->
    <base href="" />
    <title>Nóminas Eventuales</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" href="assets/media/logos/ss.ico" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="https://fonts.googleapis.com/css2?family=Sansita+One&display=swap" rel="stylesheet" />
    <link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="Public/css/login.css">
</head>
<body>
    <header>
        <div class="d-flex justify-content-between align-items-center p-6">
            <img src="Public/img/logo_coah.png" alt="Izquierda" class="logo-img">
            <img src="Public/img/logoss.jpg" alt="Derecha" class="logo-img2">
        </div>
    </header>
    <div class="main-wrapper">
    <section>
        <div class="logoimagen">
            <img src="Public/img/NominaB.png" alt="logo del sistema">
        </div>
        <div class="container-fluid">
            <form method="POST">
                <div class="form-content">
                    <div class="form-header">
                        <h2 class="form-h1">Iniciar Sesión</h2>
                        <img src="public/img/logo_nomina.png" alt="Logo Nómina">
                    </div>
                    <div class="form-inputs">
                        <div class="text-gray-500 fw-semibold fs-6 mb-3">Ingresa tu usuario y contraseña</div>
                        <div class="fv-row mb-4">
                            <input type="text" placeholder="Usuario" name="username" id="username" autocomplete="off" class="form-control" required />
                        </div>
                        <div class="fv-row mb-4">
                            <input type="password" placeholder="Contraseña" name="password" id="password" autocomplete="off" class="form-control" required />
                        </div>
                        <div class="content__or-text">
                            <span class="line"></span>
                            <span>Sign In</span>
                            <span class="line"></span>
                        </div>
                        <div class="inbutton">
                                <button type="submit" id="submit" class="btn btn-primary">
                                <span class="indicator-label">Entrar</span>
                                <span class="indicator-progress">Cargando...
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                        </div>
                        <!-- Mensaje de error (oculto por defecto) -->
                        <div id="error-message" class="alert alert-danger mt-3 d-none"></div>
                    </div>
                </div>
            </form>
        </div>
    </section>
    </div>
    <!-- Scripts de Metronic (mantén los tuyos) -->
    <script src="assets/plugins/global/plugins.bundle.js"></script>
    <script src="assets/js/scripts.bundle.js"></script>
</body>
</html>