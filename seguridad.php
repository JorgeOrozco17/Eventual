<?php
require_once 'app/controllers/usercontroller.php';

ob_start();
session_start();

// CABECERAS PARA EVITAR CACHÉ
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION["s_usuario"])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$usuario = $_SESSION["s_usuario"];
$rol = $_SESSION["role"];
$imagen = $_SESSION["image"];
$user_juris = $_SESSION['juris'];

$userController = new UserController();

$archivo_actual = basename($_SERVER['PHP_SELF']);

$id_pagina = $userController->getPaginaIDByArchivo($archivo_actual);

if ($id_pagina) {
	
	$permitido_usuario = $userController->usuarioTienePermiso($user_id, $id_pagina);

	$permitido_rol = $userController->rolTienePermiso($rol, $id_pagina);

	if (!$permitido_usuario && !$permitido_rol) {
		header("Location: acceso_denegado.php");
		exit();
	}
}
