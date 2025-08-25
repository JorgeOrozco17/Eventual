<?php
// reportes.php
// ------------------------------------------------------------
// Página de reportes con exportación a PDF y Excel/CSV.
// Depende de: Dompdf (dompdf/dompdf) y opcionalmente PhpSpreadsheet (phpoffice/phpspreadsheet)
// ------------------------------------------------------------

require_once 'app/controllers/capturacontroller.php';
require_once 'app/controllers/faltas_licenciascontroller.php';
require_once 'app/controllers/personalcontroller.php';
require_once 'app/controllers/catalogocontroller.php';

include 'header.php';

// --------- Helpers ---------
function val($arr, $key, $default = null) {
    return isset($arr[$key]) ? $arr[$key] : $default;
}
function h($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}
function toIntOrAll($v) {
    if ($v === 'all' || $v === null || $v === '') return 'all';
    return (int)$v;
}

// --------- Controladores ---------
$catalogoCtrl = new CatalogoController();
$capturaCtrl  = new Capturacontroller();
$falylicCtrl  = new Faltas_licenciascontroller();
$personalCtrl = new PersonalController();

// --------- Filtros ---------
$tipo       = val($_GET, 'tipo', 'nomina');                 // nomina | faltas | licencias | personal
$quincena   = toIntOrAll(val($_GET, 'quincena', 'all'));    // número o 'all'
$anio       = (int)val($_GET, 'anio', date('Y'));           // año
$juris      = toIntOrAll(val($_GET, 'juris', 'all'));       // id jurisdicción o 'all'
$export     = val($_GET, 'export', null);                   // pdf | excel

// Para iterar quincenas si piden 'all'
$quincenasCat = $catalogoCtrl->getAllQuincenas(); // Debe devolver arreglo con al menos 'qna' o 'numero'
$quincenasLista = [];
foreach ($quincenasCat as $q) {
    // Intentar mapear nombre de campo típico
    $qna = $q['qna'] ?? $q['numero'] ?? $q['QNA'] ?? null;
    if ($qna !== null) $quincenasLista[] = (int)$qna;
}
$quincenasLista = array_values(array_unique(array_filter($quincenasLista)));

// --------- Obtención de datos según filtros ----------
function fetchData(string $tipo, $quincena, int $anio, $juris,
                   Capturacontroller $cap, Faltas_licenciascontroller $fl, PersonalController $per,
                   array $quincenasLista) : array {

    // Utilidad para filtrar por jurisdicción si el arreglo tiene la llave
    $filtrarJuris = function(array $rows) use ($juris) {
        if ($juris === 'all') return $rows;
        return array_values(array_filter($rows, function($r) use ($juris) {
            // Intenta con llaves comunes
            foreach (['id_adscripcion', 'id_juris', 'jurisdiccion_id', 'juris_id'] as $k) {
                if (isset($r[$k]) && (int)$r[$k] === (int)$juris) return true;
            }
            return false;
        }));
    };

    switch ($tipo) {
        case 'nomina':
            if ($quincena === 'all') {
                $res = [];
                foreach ($quincenasLista as $q) {
                    $res = array_merge($res, $cap->model->getByPeriodo($q, $anio));
                }
                return $filtrarJuris($res);
            } else {
                $rows = $cap->model->getByPeriodo((int)$quincena, $anio);
                return $filtrarJuris($rows);
            }

        case 'faltas':
            if ($quincena === 'all') {
                // Si existe método por año, úsalo; si no, iteramos quincenas
                if (method_exists($fl->model, 'getFaltasByAnio')) {
                    $rows = $fl->model->getFaltasByAnio($anio);
                } else {
                    $rows = [];
                    foreach ($quincenasLista as $q) {
                        if (method_exists($fl, 'getFaltasByQuincena')) {
                            $rows = array_merge($rows, $fl->getFaltasByQuincena($q, $anio));
                        }
                    }
                }
                return $filtrarJuris($rows);
            } else {
                $rows = $fl->getFaltasByQuincena((int)$quincena, $anio);
                return $filtrarJuris($rows);
            }

        case 'licencias':
            if ($quincena === 'all') {
                if (method_exists($fl->model, 'getLicenciasByAnio')) {
                    $rows = $fl->model->getLicenciasByAnio($anio);
                } else {
                    $rows = [];
                    foreach ($quincenasLista as $q) {
                        if (method_exists($fl, 'getLicenciasByQuincena')) {
                            $rows = array_merge($rows, $fl->getLicenciasByQuincena($q, $anio));
                        }
                    }
                }
                return $filtrarJuris($rows);
            } else {
                $rows = method_exists($fl, 'getLicenciasByQuincena')
                    ? $fl->getLicenciasByQuincena((int)$quincena, $anio)
                    : [];
                return $filtrarJuris($rows);
            }

        case 'personal':
            // Si hay métodos específicos por jurisdicción, úsalos; si no, getAll + filtro
            if ($juris !== 'all' && method_exists($per, 'getEmpleadosByJurisdiccion')) {
                $rows = $per->getEmpleadosByJurisdiccion((int)$juris);
            } else if (method_exists($per->model, 'getAutorizados')) {
                $rows = $per->model->getAutorizados();
            } else {
                $rows = $per->model->getAll();
            }
            return $filtrarJuris($rows);

        default:
            return [];
    }
}

$data = fetchData($tipo, $quincena, $anio, $juris, $capturaCtrl, $falylicCtrl, $personalCtrl, $quincenasLista);

// --------- Exportación ----------
function normalizeData(array $data): array {
    // Garantiza que todos los renglones tengan las mismas llaves (unión de todas)
    $allKeys = [];
    foreach ($data as $row) $allKeys = array_unique(array_merge($allKeys, array_keys($row)));
    $norm = [];
    foreach ($data as $row) {
        $r = [];
        foreach ($allKeys as $k) $r[$k] = array_key_exists($k, $row) ? $row[$k] : '';
        $norm[] = $r;
    }
    return [$norm, $allKeys];
}

function exportPDF(array $data, string $filenameBase = 'reporte') {
    if (empty($data)) {
        header('Content-Type: text/plain; charset=UTF-8');
        echo "Sin datos para exportar.";
        exit;
    }

    // Cargar Dompdf sólo si se exporta
    if (!class_exists(\Dompdf\Dompdf::class)) {
        // Carga perezosa de vendor/autoload.php
        $vendor = __DIR__ . '/vendor/autoload.php';
        if (file_exists($vendor)) require_once $vendor;
    }
    if (!class_exists(\Dompdf\Dompdf::class)) {
        header('Content-Type: text/plain; charset=UTF-8');
        echo "Dompdf no está instalado. Instálalo con: composer require dompdf/dompdf";
        exit;
    }

    [$norm, $cols] = normalizeData($data);

    ob_start();
    ?>
    <html>
    <head>
        <meta charset="utf-8">
        <style>
            body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 11px; }
            h2 { text-align:center; margin: 4px 0 10px; }
            table { width:100%; border-collapse:collapse; }
            th, td { border:1px solid #666; padding:6px 8px; }
            th { background:#efefef; }
        </style>
    </head>
    <body>
        <h2><?= h(strtoupper($filenameBase)) ?></h2>
        <table>
            <thead>
                <tr>
                    <?php foreach ($cols as $c): ?>
                        <th><?= h($c) ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($norm as $r): ?>
                    <tr>
                        <?php foreach ($cols as $c): ?>
                            <td><?= h($r[$c]) ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </body>
    </html>
    <?php
    $html = ob_get_clean();

    $dompdf = new \Dompdf\Dompdf();
    $dompdf->setPaper('letter', 'landscape');
    $dompdf->loadHtml($html, 'UTF-8');
    $dompdf->render();

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="'.$filenameBase.'.pdf"');
    echo $dompdf->output();
    exit;
}

function exportExcelOrCsv(array $data, string $filenameBase = 'reporte') {
    if (empty($data)) {
        header('Content-Type: text/plain; charset=UTF-8');
        echo "Sin datos para exportar.";
        exit;
    }

    // Intentar XLSX con PhpSpreadsheet; si no existe, hacer CSV
    if (!class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
        $vendor = __DIR__ . '/vendor/autoload.php';
        if (file_exists($vendor)) require_once $vendor;
    }

    if (class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
        // XLSX
        [$norm, $cols] = normalizeData($data);
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados
        $colIndex = 1;
        foreach ($cols as $c) {
            $sheet->setCellValueByColumnAndRow($colIndex, 1, $c);
            $sheet->getColumnDimensionByColumn($colIndex)->setAutoSize(true);
            $colIndex++;
        }

        // Datos
        $rowIndex = 2;
        foreach ($norm as $row) {
            $colIndex = 1;
            foreach ($cols as $c) {
                $sheet->setCellValueByColumnAndRow($colIndex, $rowIndex, (string)$row[$c]);
                $colIndex++;
            }
            $rowIndex++;
        }

        // Salida
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$filenameBase.'.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    } else {
        // CSV (compat. Excel)
        [$norm, $cols] = normalizeData($data);
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="'.$filenameBase.'.csv"');
        $out = fopen('php://output', 'w');
        // BOM UTF-8 para acentos en Excel
        fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($out, $cols);
        foreach ($norm as $row) {
            $line = [];
            foreach ($cols as $c) $line[] = (string)$row[$c];
            fputcsv($out, $line);
        }
        fclose($out);
        exit;
    }
}

// Si hay export, procesar y salir
if ($export === 'pdf') {
    $filename = "reporte_{$tipo}_" . ($quincena === 'all' ? 'todas' : $quincena) . "_{$anio}";
    exportPDF($data, $filename);
} else if ($export === 'excel') {
    $filename = "reporte_{$tipo}_" . ($quincena === 'all' ? 'todas' : $quincena) . "_{$anio}";
    exportExcelOrCsv($data, $filename);
}

?>
<style>
    body { background-color: #D9D9D9 !important; }
    h2 { color: #333333; }
    .card { border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    .table-responsive { max-height: 65vh; overflow: auto; }
    .sticky th { position: sticky; top: 0; background: #fff; z-index: 1; }
</style>

<div class="container-fluid mt-5">
    <div class="regreso">
        <span class="menu-title">
            <a class="menu-link" href="reportes.php">
                <span class="menu-tittle">Reportes</span>
            </a>
        </span>
    </div>
    <br>

    <div class="card">
        <div class="card-header">
            <h2>Reportes e históricos</h2>
        </div>

        <div class="card-body">
            <form class="row g-3" method="get" action="reportes.php">
                <div class="col-md-3">
                    <label class="form-label">Tipo de reporte</label>
                    <select name="tipo" class="form-select" required>
                        <?php
                        $tipos = [
                            'nomina'   => 'Nómina (captura)',
                            'faltas'   => 'Faltas',
                            'licencias'=> 'Licencias',
                            'personal' => 'Personal'
                        ];
                        foreach ($tipos as $k=>$v):
                        ?>
                        <option value="<?= h($k) ?>" <?= $tipo===$k?'selected':'' ?>><?= h($v) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Quincena</label>
                    <select name="quincena" class="form-select">
                        <option value="all" <?= $quincena==='all'?'selected':'' ?>>Todas</option>
                        <?php foreach ($quincenasLista as $q): ?>
                            <option value="<?= h($q) ?>" <?= (string)$quincena===(string)$q?'selected':'' ?>><?= h($q) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label">Año</label>
                    <input type="number" class="form-control" name="anio" value="<?= h($anio) ?>" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Jurisdicción (opcional)</label>
                    <select name="juris" class="form-select">
                        <option value="all" <?= $juris==='all'?'selected':'' ?>>Todas</option>
                        <?php
                        $jurisdicciones = $catalogoCtrl->getAllJurisdicciones();
                        foreach ($jurisdicciones as $j):
                            $id = $j['id'] ?? null; $nombre = $j['nombre'] ?? ('Juris '.$id);
                            if ($id===null) continue;
                        ?>
                            <option value="<?= h($id) ?>" <?= (string)$juris===(string)$id?'selected':'' ?>><?= h($nombre) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter"></i> Filtrar</button>
                </div>

                <div class="col-12 d-flex gap-2">
                    <a class="btn btn-danger"
                       href="?<?= http_build_query(['tipo'=>$tipo,'quincena'=>$quincena,'anio'=>$anio,'juris'=>$juris,'export'=>'pdf']) ?>">
                        <i class="fas fa-file-pdf"></i> Exportar PDF
                    </a>
                    <a class="btn btn-success"
                       href="?<?= http_build_query(['tipo'=>$tipo,'quincena'=>$quincena,'anio'=>$anio,'juris'=>$juris,'export'=>'excel']) ?>">
                        <i class="fas fa-file-excel"></i> Exportar Excel
                    </a>
                </div>
            </form>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <?php if (empty($data)): ?>
                    <div class="alert alert-warning">Sin resultados para los filtros seleccionados.</div>
                <?php else:
                    // Columnas dinámicas
                    $allKeys = [];
                    foreach ($data as $row) $allKeys = array_unique(array_merge($allKeys, array_keys($row)));
                ?>
                <table class="table table-sm table-striped table-hover sticky">
                    <thead>
                        <tr>
                            <?php foreach ($allKeys as $k): ?>
                                <th><?= h($k) ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $r): ?>
                            <tr>
                                <?php foreach ($allKeys as $k): ?>
                                    <td><?= h($r[$k] ?? '') ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
