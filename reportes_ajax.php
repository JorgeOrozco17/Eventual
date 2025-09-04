<?php
require_once 'app/controllers/reportescontroller.php';

$controller = new ReportesController();

$action = $_POST['action'] ?? '';

if ($action === 'quincena') {
    $qna  = $_POST['quincena'] ?? null;
    $anio = $_POST['anio'] ?? null;
    $tipo = $_POST['tipo_reporte'] ?? 'all';

    $resultados = $controller->getAltasBajasByQuincena($qna, $anio, $tipo);

} elseif ($action === 'periodo') {
    $qnaInicio = $_POST['quincena_inicio'] ?? null;
    $qnaFin    = $_POST['quincena_fin'] ?? null;
    $tipo      = $_POST['tipo_reporte'] ?? 'all';

    $resultados = $controller->getAltasBajasByPeriodo($qnaInicio, $qnaFin, $tipo);
}

// imprimir resultados
if (!empty($resultados)) {
    echo "<table id=\"tablaComun\" class=\"table align-middle table-row-bordered table-row-solid gy-4 gs-7\">";
    echo "<thead>
    <tr>
        <th>Nombre</th>
        <th>Movimiento</th>
        <th>Neto Mensual</th>
        <th>Bruto Mensual</th>
        <th>Recurso</th>
        <th>Perfil</th>
        <th>Adscripcion</th>
        <th>Motivo</th>
    </tr>
</thead>
<tbody>";
    foreach ($resultados as $row) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['nombre_alta']) . "</td>";
        echo "<td>" . htmlspecialchars($row['movimiento']) . "</td>";
        echo "<td>" . htmlspecialchars($row['sueldo_neto']) . "</td>";
        echo "<td>" . htmlspecialchars($row['sueldo_bruto']) . "</td>";
        echo "<td>" . htmlspecialchars($row['programa']) . "</td>";
        echo "<td>" . htmlspecialchars($row['puesto']) . "</td>";
        echo "<td>" . htmlspecialchars($row['centro']) . "</td>";
        if ($row['movimiento'] === 'alta') {
            echo "<td>" . htmlspecialchars($row['observaciones_alta']) . "</td>";
        }else{
            echo "<td>" . htmlspecialchars($row['observaciones_baja']) . "</td>";
        }
        echo "</tr>";
    }
    echo "</tbody></table>";
} else {
    echo "<p class='text-muted'>No se encontraron resultados</p>";
}
