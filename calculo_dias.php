<?php
require_once 'app/models/dbconexion.php';

$db = new DBConexion();
$conn = $db->getConnection();
if (!$conn) die("ERROR: No se pudo establecer la conexión con la base de datos.");

$stmt = $conn->query("
    SELECT p.id AS id_personal, p.inicio_contratacion, c.id AS id_calculo
    FROM personal p
    JOIN calculo_personal c ON p.id = c.id_personal
");

$tz = new DateTimeZone('America/Monterrey');
$today = new DateTime('now', $tz);

$updateSql = "UPDATE calculo_personal 
              SET dias_integros = :di, dias_medios = :dm 
              WHERE id = :id";
$upd = $conn->prepare($updateSql);

$procesados = 0; $omitidos = 0; $actualizados = 0;

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $dt_ingreso = new DateTime($row['inicio_contratacion'], $tz);

    // ¿Aniversario hoy? (mismo mes-día)
    $esAniversario = ($dt_ingreso->format('m-d') === $today->format('m-d'));

    // Manejo opcional de 29-feb: trátalo como 28-feb en años no bisiestos
    $esBisiesto = (bool)$today->format('L'); // 1 si el año actual es bisiesto
    if (!$esAniversario && !$esBisiesto
        && $today->format('m-d') === '02-28'
        && $dt_ingreso->format('m-d') === '02-29') {
        $esAniversario = true;
    }

    if (!$esAniversario) { $omitidos++; continue; }

    // Años cumplidos al día de hoy
    $anios = $dt_ingreso->diff($today)->y;

    // Si quieres que corra sólo cuando ya cumplieron al menos 1 año:
    if ($anios < 1) { $omitidos++; continue; }

    // Lógica Art. 37
    if ($anios < 5) {
        $dias_integros = 30; $dias_medios = 30;
    } elseif ($anios < 10) {
        $dias_integros = 45; $dias_medios = 45;
    } else {
        $dias_integros = 60; $dias_medios = 60;
    }

    $upd->execute([
        ':di' => $dias_integros,
        ':dm' => $dias_medios,
        ':id' => $row['id_calculo']
    ]);

    $actualizados++; $procesados++;
}

echo "Actualizados: $actualizados | Omitidos: $omitidos";
