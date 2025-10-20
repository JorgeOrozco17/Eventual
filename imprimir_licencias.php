<?php
$quincena = $_GET['quincena'] ?? 'all';
$anio = $_GET['anio'] ?? date('Y');

require_once 'app/controllers/faltas_licenciascontroller.php';
require_once 'app/controllers/catalogocontroller.php';

$controller = new Faltas_licenciascontroller();
$cat = new CatalogoModel();

function render_quincena_licencia($q_id, $q_nombre, $anio, $controller, $add_break = false) {
    // Solo trae licencias
    $registros = $controller->getLicenciasByQuincena($q_id, $anio); // <-- debes tener este método
    if (empty($registros)) return;
?>
<div class="center">
    <b>
        SUBDIRECCION DE RECURSOS HUMANOS<br>
        DEPARTAMENTO DE RECLUTAMIENTO Y SELECCIÓN DE PERSONAL<br>
        REPORTE DE INCIDENCIAS ( LICENCIAS )<br>
        <?= strtoupper($q_nombre) . " DEL $anio"; ?>
    </b>
</div>
<br>
<table class="table-reporte">
    <tr>
        <th>N°</th>
        <th>NOMBRE DEL TRABAJADOR</th>
        <th>ADSCRIPCION</th>
        <th>JURISDICCION</th>
        <th>DIAS</th>
        <th>PERIODO</th>
        <th>OBSERVACIONES</th>
    </tr>
    <?php $i = 1; foreach ($registros as $p): ?>
        <tr>
            <td class="center"><?= $i++ ?></td>
            <td><?= htmlspecialchars($p['nombre']) ?></td>
            <td><?= htmlspecialchars($p['adscripcion']) ?></td>
            <td><?= htmlspecialchars($p['jurisdiccion']) ?></td>
            <td class="center"><?= htmlspecialchars($p['dias']) ?></td>
            <td>
                <?php
                    // Muestra periodo_1 al periodo_2 (formato mexicano)
                    $ini = !empty($p['periodo_1']) ? date('d/m/Y', strtotime($p['periodo_1'])) : '';
                    $fin = !empty($p['periodo_2']) ? date('d/m/Y', strtotime($p['periodo_2'])) : '';
                    echo "$ini al $fin";
                ?>
            </td>
            <td><?= htmlspecialchars($p['observaciones']) ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<br>
<table class="firmas">
    <tr>
        <td><b>ELABORO</b></td>
        <td><b>ENTREGO</b></td>
        <td><b>RECIBIO</b></td>
    </tr>
    <tr>
        <td>LIC. NORMA ALEJANDRINA RAMIREZ DE LA CRUZ</td>
        <td>
            LIC. JENDY NAYELI GONZALEZ CASTAÑEDA<br>
            JEFE DEL DEPARTAMENTO DE RECLUTAMIENTO<br>
            Y SELECCIÓN DE PERSONAL.
        </td>
        <td>____________________________</td>
    </tr>
</table>
<br><br>
<div class="left">
    SALTILLO, COAHUILA DE ZARAGOZA, <?= date('d \d\e M \d\e\l Y') ?>.
</div>
<?php if ($add_break): ?>
<div class="page-break"></div>
<?php endif; ?>
<?php
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Licencias</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
        .center { text-align: center; }
        .table-reporte {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }
        .table-reporte th, .table-reporte td {
            border: 1px solid #000;
            padding: 4px 6px;
            font-size: 12px;
            text-align: left;
        }
        .table-reporte th {
            background: #f4f4f4;
            font-size: 13px;
        }
        .firmas {
            width: 100%;
            margin-top: 30px;
            text-align: center;
        }
        .firmas td { padding: 30px 5px 5px 0px; }
        .left { text-align: left; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>

<htmlpagefooter name="piepagina">
    <div class="center" style="font-size:10px;">
        Página {PAGE_NUM} de {PAGE_COUNT}
    </div>
</htmlpagefooter>
<sethtmlpagefooter name="piepagina" value="on" />

<?php
$quincenas_impresas = [];

// Si es "todas"
if ($quincena === 'all' || $quincena === '') {
    $quincenas = $cat->getAllQuincenas();
    foreach ($quincenas as $q) {
        $registros = $controller->getLicenciasByQuincena($q['id'], $anio);
        if (!empty($registros)) {
            $quincenas_impresas[] = ['id' => $q['id'], 'nombre' => $q['nombre']];
        }
    }
    $total = count($quincenas_impresas);
    if ($total === 0) {
        echo '<div class="center"><b>No hay registros de licencias para ninguna quincena en el año seleccionado.</b></div>';
    } else {
        foreach ($quincenas_impresas as $idx => $q) {
            $add_break = ($idx < $total - 1);
            render_quincena_licencia($q['id'], $q['nombre'], $anio, $controller, $add_break);
        }
    }
} else {
    $q = $cat->getQuincenaByDate($quincena);
    $nombre = is_array($q) && isset($q['nombre']) ? $q['nombre'] : "QUINCENA $quincena";
    render_quincena_licencia($quincena, $nombre, $anio, $controller, false);
}
?>

</body>
</html>
