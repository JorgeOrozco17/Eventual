<?php
require_once 'app/models/dbconexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_personal = $_POST['id_personal'] ?? null;
    $id_usuario = $_SESSION['user_id'] ?? 0;

    if (!$id_personal) {
        die('ID del personal no proporcionado.');
    }

    $campos = [
        'solicitud',
        'curriculum',
        'acta_nacimiento',
        'curp',
        'cartilla_militar',
        'certificado_estudios',
        'titulo_profesional',
        'cedula_profesional',
        'certificacion_cedula',
        'titulo_cedula_especialidad',
        'cursos_capacitacion',
        'ine',
        'domicilio',
        'cartas_recomendacion',
        'carta_protesta',
        'compatibilidad_horario',
        'carta_compromiso',
        'certificado_medico',
        'no_antecedentes',
        'no_inhabilitado',
        'situacion_fiscal',
        'acuse_declaracion',
        'comprobante_banco'
    ];

    $db = new DBConexion();
    $conn = $db->getConnection();

    // Verificar si ya existe un registro para ese personal
    $stmt_check = $conn->prepare("SELECT id FROM archivos WHERE id_personal = ?");
    $stmt_check->execute([$id_personal]);
    $registro_existente = $stmt_check->fetch(PDO::FETCH_ASSOC);

    $valores = [];
    $updates = [];
    $insert_cols = ['id_personal', 'id_usuario'];
    $insert_vals = [$id_personal, $id_usuario];
    $insert_placeholders = ['?', '?'];

    foreach ($campos as $campo) {
        if (isset($_FILES[$campo]) && $_FILES[$campo]['error'] === UPLOAD_ERR_OK) {
            $filename = basename($_FILES[$campo]['name']);
            $unique_name = uniqid($campo . "_") . "_" . $filename;
            $destination = 'uploads/' . $unique_name;

            if (move_uploaded_file($_FILES[$campo]['tmp_name'], $destination)) {
                if ($registro_existente) {
                    $updates[] = "$campo = ?";
                    $valores[] = $unique_name;
                } else {
                    $insert_cols[] = $campo;
                    $insert_vals[] = $unique_name;
                    $insert_placeholders[] = '?';
                }
            }
        }
    }

    if ($registro_existente) {
        if (!empty($updates)) {
            $valores[] = $id_personal;
            $sql = "UPDATE archivos SET " . implode(', ', $updates) . " WHERE id_personal = ?";
            $stmt = $conn->prepare($sql);
            $stmt->execute($valores);
        }
    } else {
        $sql = "INSERT INTO archivos (" . implode(', ', $insert_cols) . ") VALUES (" . implode(', ', $insert_placeholders) . ")";
        $stmt = $conn->prepare($sql);
        $stmt->execute($insert_vals);
    }

    header("Location: archivodetalle.php?id=" . $id_personal . "&success=1");
    exit;
} else {
    header("Location: archivo.php");
    exit;
}
