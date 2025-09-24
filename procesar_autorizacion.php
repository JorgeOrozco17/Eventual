<?php
require_once 'app/helpers/util.php';
require_once 'app/models/dbconexion.php';
require_once 'app/models/personalModel.php';

session_start();

$personalModel = new PersonalModel();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_personal = $_POST['id'] ?? null;
    $id_usuario = $_SESSION['user_id'] ?? 0;
    $accion = $_POST['accion'] ?? '';

    if (!$id_personal) {
        die('Faltan datos requeridos.');
    }

    $db = new DBConexion();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("SELECT * FROM personal WHERE id = ?");
    $stmt->execute([$id_personal]);
    $actual = $stmt->fetch(PDO::FETCH_ASSOC);

    // Recoger campos del formulario
    $solicita      = $_POST['solicita'] ?? $actual['solicita'];
    $movimiento    = $_POST['movimiento'] ?? $actual['movimiento'];
    $oficio        = $_POST['oficio'] ?? $actual['oficio'];
    $puesto        = $_POST['puesto'] ?? $actual['puesto'];
    $programa      = $_POST['programa'] ?? $actual['programa'];
    $adscripcion   = $_POST['adscripcion'] ?? $actual['adscripcion'];
    $centro        = $_POST['centro'] ?? $actual['centro'];
    $RFC           = $_POST['RFC'] ?? $actual['RFC'];
    $CURP          = $_POST['CURP'] ?? $actual['CURP'];
    $sueldo_neto   = $_POST['sueldo_neto'] ?? $actual['sueldo_neto'];
    $sueldo_bruto  = $_POST['sueldo_bruto'] ?? $actual['sueldo_bruto'];
    $nombre_alta   = $_POST['nombre_alta'] ?? $actual['nombre_alta'];
    $quincena_alta = $_POST['quincena_alta'] ?? $actual['quincena_alta'];
    $inicio_contratacion = $_POST['inicio_contratacion'] ?? null;
    $quincena_baja = $_POST['quincena_baja'] ?? $actual['quincena_baja'];
    $fecha_baja    = $_POST['fecha_baja'] ?? $actual['fecha_baja'];
    $cuenta        = $_POST['cuenta'] ?? $actual['cuenta'];
    $observaciones_alta = $_POST['observaciones_alta'] ?? $actual['observaciones_alta'];
    $observaciones_baja = $_POST['observaciones_baja'] ?? $actual['observaciones_baja'];
    

    if ($accion === 'autorizar') {
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

        include 'guardar_archivos.php';  // Este script ya hace redirección a archivodetalle.php
        exit;
    } else {
        // Si no se subió el archivo, redirigir con mensaje de error
        header("Location: autorizapersonal.php?id=$id_personal&error=Archivo no cargado");
        exit;
    }

    } elseif ($accion === 'actualizar') {
        $stmtcentro = $conn->prepare("SELECT nombre, clues FROM centros WHERE id = ?");
        $stmtcentro->execute([$centro]);
        $centro_data = $stmtcentro->fetch(PDO::FETCH_ASSOC);

        //  Actualizar datos SIN marcar autorización
        $stmt = $conn->prepare("UPDATE personal 
            SET solicita=?, movimiento=?, oficio=?, puesto=?, programa=?, adscripcion=?, centro=?, clues=?, RFC=?, CURP=?, 
                sueldo_bruto=?, nombre_alta=?, quincena_alta=?, inicio_contratacion=?, 
                quincena_baja=?, fecha_baja=?, cuenta=?, observaciones_alta=?, observaciones_baja=?
            WHERE id=?");
        $stmt->execute([
            $solicita, $movimiento, $oficio, $puesto, $programa, 'J' .$adscripcion, $centro_data['nombre'], $centro_data['clues'],
            $RFC, $CURP, $sueldo_bruto, $nombre_alta, $quincena_alta,
            $inicio_contratacion, $quincena_baja, $fecha_baja, $cuenta, $observaciones_alta,
            $observaciones_baja, $id_personal
        ]);

        // Si se cambió el sueldo_bruto, recalcular
        if ($sueldo_bruto != $actual['sueldo_bruto']) {
            $personalModel->UpdateCalculoPersonal($id_personal);
        }


        header("Location: autorizapersonal.php?id=$id_personal&msg=Datos actualizados");
        exit;
    }

} else {
    header("Location: /autoriza.php");
    exit;
}
