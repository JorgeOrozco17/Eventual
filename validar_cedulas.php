<?php
// Habilitar errores de PHP para diagnóstico
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Función para consultar la API de APIMarket (versión corregida según documentación).
 * @param string $nombres
 * @param string $paterno
 * @param string $materno
 * @return array Lista de cédulas encontradas.
 */
function consultarCedulaAPIMarket($nombres, $paterno, $materno = '') {
    $apiKey = '59b34521-b7da-42f7-b0b5-48c5846a7add'; // Tu token
    $baseUrl = 'https://apimarket.mx/api/sep/grupo/obtener-cedula';

    $queryParams = ['nombres' => $nombres, 'paterno' => $paterno];
    if (!empty($materno)) {
        $queryParams['materno'] = $materno;
    }
    
    $fullUrl = $baseUrl . '?' . http_build_query($queryParams);

    $ch = curl_init($fullUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Accept: application/json',
            'Authorization: Bearer ' . $apiKey
        ],
        CURLOPT_TIMEOUT => 20
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    // --- PUNTO DE DEPURACIÓN (Si sigue fallando, descomenta estas líneas) ---
    // var_dump(['http_code' => $httpCode, 'curl_error' => $curlError, 'response_body' => $response]);
    // exit;

    if ($httpCode == 200) {
        $data = json_decode($response, true);
        // --- CORRECCIÓN CLAVE ---
        // La documentación confirma que los resultados están DENTRO de la clave "data".
        // Si "data" existe, lo devolvemos; si no, devolvemos un arreglo vacío.
        return $data['data'] ?? [];
    }
    
    return []; // Si hay un error HTTP, devolvemos un arreglo vacío
}


// === Procesamiento del Archivo (lógica principal) ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo'])) {
    $fileTmp = $_FILES['archivo']['tmp_name'];
    $spreadsheet = IOFactory::load($fileTmp);
    $rows = $spreadsheet->getActiveSheet()->toArray();

    $result = new Spreadsheet();
    $sheet = $result->getActiveSheet();
    $sheet->setTitle('Resultados Cédulas');

    $headers = ['CURP', 'NOMBRE(S)', 'APELLIDO PATERNO', 'APELLIDO MATERNO', 'ID CÉDULA', 'NOMBRE COMPLETO (Resultado)', 'TÍTULO', 'INSTITUCIÓN', 'AÑO REGISTRO'];
    $sheet->fromArray($headers, null, 'A1');

    $fila = 2;
    foreach (array_slice($rows, 1) as $r) {
        $curp    = trim($r[0] ?? '');
        $nombres = strtoupper(trim($r[1] ?? ''));
        $paterno = strtoupper(trim($r[2] ?? ''));
        $materno = strtoupper(trim($r[3] ?? ''));

        if (empty($nombres) || empty($paterno)) continue;

        $resultados = consultarCedulaAPIMarket($nombres, $paterno, $materno);

        if (!empty($resultados) && is_array($resultados)) {
            foreach ($resultados as $item) {
                // --- CORRECCIÓN DE NOMBRES DE CAMPOS ---
                // Mapeamos los campos exactamente como en la documentación.
                $cedula = $item['idCedula'] ?? '';
                $nombreRes = $item['nombre'] ?? '';
                $paternoRes = $item['paterno'] ?? '';
                $maternoRes = $item['materno'] ?? '';
                $nombreCompletoResultado = trim("$nombreRes $paternoRes $maternoRes");
                $titulo = $item['titulo'] ?? '';
                $inst = $item['desins'] ?? ''; // El campo es "desins"
                $anio = $item['anioreg'] ?? ''; // El campo es "anioreg"

                $sheet->fromArray([$curp, $nombres, $paterno, $materno, $cedula, $nombreCompletoResultado, $titulo, $inst, $anio], null, "A$fila");
                $fila++;
            }
        } else {
            $sheet->fromArray([$curp, $nombres, $paterno, $materno, '—', 'No se encontraron resultados', '—', '—', '—'], null, "A$fila");
            $fila++;
        }

        usleep(300000);
    }

    $fileName = 'Resultados_Cedulas_' . date('Ymd_His') . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"$fileName\"");
    $writer = new Xlsx($result);
    $writer->save('php://output');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Validar Cédulas Profesionales (v2 Corregido)</title>
    <style>body{font-family:Arial,sans-serif;background-color:#f8fafc;text-align:center;padding-top:60px}.card{display:inline-block;background:white;padding:30px;border-radius:10px;box-shadow:0 3px 10px rgba(0,0,0,0.15)}input,button{margin-top:15px;padding:10px}button{cursor:pointer;background-color:#007bff;color:white;border:none;border-radius:5px;}</style>
</head>
<body>
<div class="card">
    <h2>📘 Validar Cédulas Profesionales (APIMarket)</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Sube tu Excel con 4 columnas: <br> CURP | NOMBRE(S) | APELLIDO PATERNO | APELLIDO MATERNO</label><br>
        <input type="file" name="archivo" accept=".xlsx,.csv" required><br>
        <button type="submit">Procesar y Descargar Resultados</button>
    </form>
</div>
</body>
</html>