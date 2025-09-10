<?php
require_once 'app/controllers/personalController.php';
include 'header.php';

$controller = new PersonalController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id = $_POST['id'] ?? null;

    // Datos generales
    $solicita            = $_POST['solicita'] ?? null;
    $movimiento          = $_POST['movimiento'] ?? null;
    $oficio              = $_POST['oficio'] ?? null;
    $puesto              = $_POST['puesto'] ?? null;
    $programa            = $_POST['programa'] ?? null;
    $rama                = $_POST['rama'] ?? null;
    $adscripcion         = $_POST['adscripcion'] ?? null;
    $centro              = $_POST['centro'] ?? null;
    $sueldo_bruto        = $_POST['sueldo_bruto'] ?? null;

    // Datos del empleado actual
    $nombre_actual       = $_POST['nombre_actual'] ?? null;
    $RFC_actual          = $_POST['RFC_actual'] ?? null;
    $CURP_actual         = $_POST['CURP_actual'] ?? null;
    $quincena_baja       = $_POST['quincena_baja'] ?? null;
    $fecha_baja          = $_POST['fecha_baja'] ?? null;
    $observaciones_baja  = $_POST['observaciones_baja'] ?? null;

    // Datos del empleado nuevo
    $nombre_alta         = $_POST['nombre_alta'] ?? null;
    $RFC                 = $_POST['RFC'] ?? null;
    $CURP                = $_POST['CURP'] ?? null;
    $quincena_alta       = $_POST['quincena_alta'] ?? null;
    $inicio_contratacion = $_POST['inicio_contratacion'] ?? null;
    $cuenta              = $_POST['cuenta'] ?? null;
    $observaciones_alta  = $_POST['observaciones_alta'] ?? null;
    $observaciones_usuario = $_POST['observaciones_usuario'] ?? null;

    // ----------------------------
    // Armado de arrays
    // ----------------------------
    $data_baja = [
        'id'                   => $id,
        'solicita'             => $solicita,
        'movimiento'           => 'baja',
        'quincena_baja'        => $quincena_baja,
        'fecha_baja'           => $fecha_baja,
        'observaciones_baja'   => $observaciones_baja,
        'observaciones_usuario'=> $observaciones_usuario
    ];

    $data_alta = [
        'movimiento'           => 'alta',
        'solicita'             => $solicita,
        'oficio'               => $oficio,
        'puesto'               => $puesto,
        'programa'             => $programa,
        'rama'                 => $rama,
        'adscripcion'          => $adscripcion,
        'centro'               => $centro,
        'RFC'                  => $RFC,
        'CURP'                 => $CURP,
        'sueldo_bruto'         => $sueldo_bruto,
        'inicio_contratacion'  => $inicio_contratacion,
        'quincena_alta'        => $quincena_alta,
        'nombre_alta'          => $nombre_alta,
        'cuenta'               => $cuenta,
        'observaciones_alta'   => $observaciones_alta,
        'observaciones_usuario'=> $observaciones_usuario
    ];

    // ----------------------------
    // Ejecución de funciones
    // ----------------------------
    $resultado_baja = $controller->AltaBajaPersonal($data_baja);
    $resultado_alta = false;

    if ($resultado_baja) {
        $resultado_alta = $controller->AltaBajaPersonalSave($data_alta);
    }

    // ----------------------------
    // Redirección final
    // ----------------------------
    header("Location: autorizapersonal.php");
    exit;

} else {
    echo "<div class='alert alert-warning'>Acceso inválido. No se recibieron datos por POST.</div>";
}
?>
