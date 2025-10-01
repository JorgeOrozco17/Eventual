<?php
include 'header.php';
require_once 'app/models/dbconexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_personal = $_POST['id_personal'] ?? null;
    $id_usuario = $_SESSION['user_id'] ?? 0;

    if (!$id_personal) {
        die('ID del personal no proporcionado.');
    }

    $campos = [
        'autorizacion',
        'baja',
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

    // =========================
    // 📌 Procesar archivos subidos
    // =========================
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
            } else {
                if (!$registro_existente) {
                    $insert_cols[] = $campo;
                    $insert_vals[] = null;
                    $insert_placeholders[] = '?';
                }
            }
        }
    }

    // =========================
    // 📌 Procesar campos "No aplica"
    // =========================
    if (isset($_POST['no_aplica']) && is_array($_POST['no_aplica'])) {
        foreach ($_POST['no_aplica'] as $campo => $valor) {
            if ($valor === "no_aplica" && in_array($campo, $campos)) {
                if ($registro_existente) {
                    $updates[] = "$campo = ?";
                    $valores[] = "no_aplica"; // Guardamos literal
                } else {
                    $insert_cols[] = $campo;
                    $insert_vals[] = "no_aplica";
                    $insert_placeholders[] = '?';
                }
            }
        }
    }

    // =========================
    // 📌 Ejecutar INSERT o UPDATE
    // =========================
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

    // =========================
    // 📌 Guardar anexos generales
    // =========================
    if (!empty($_FILES['anexos']['name'][0])) {
        foreach ($_FILES['anexos']['name'] as $key => $filename) {
            if ($_FILES['anexos']['error'][$key] === UPLOAD_ERR_OK) {
                $tmp_name = $_FILES['anexos']['tmp_name'][$key];
                $unique_name = uniqid("anexo_") . "_" . basename($filename);
                $destino = "uploads/anexos/" . $unique_name;

                if (!is_dir("uploads/anexos")) {
                    mkdir("uploads/anexos", 0777, true);
                }

                if (move_uploaded_file($tmp_name, $destino)) {
                    $stmt = $conn->prepare("INSERT INTO anexos_personal 
                        (id_personal, nombre_archivo, archivo, usuario_id) 
                        VALUES (?, ?, ?, ?)");
                    $stmt->execute([
                        $id_personal,
                        $filename,
                        $unique_name,
                        $id_usuario
                    ]);
                }
            }
        }
    }

    header("Location: archivodetalle.php?id=" . $id_personal);
    exit;
} else {
    header("Location: archivo.php");
    exit;
}
