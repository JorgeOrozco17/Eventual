<?php
require_once 'vendor/autoload.php'; 

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;


// Conexión a la base de datos
require_once 'app/controllers/capturacontroller.php';
$controller = new Capturacontroller();

// Obtener todos los registros de la tabla captura
$qna = $_GET['qna'] ?? '';  // Puedes recibir el valor de la quincena desde la URL
$anio = $_GET['anio'] ?? ''; // También puedes recibir el año desde la URL
$capturaData = $controller->model->datosCaptura($qna, $anio); // Filtrar por quincena y año si lo necesitas

// Crear una nueva instancia de PhpSpreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('QNA ' . $qna . '-' . $anio);


// Títulos de las columnas
$sheet->setCellValue('A1', 'NOM');
$sheet->setCellValue('B1', 'RFC');
$sheet->setCellValue('C1', 'CURP');
$sheet->setCellValue('D1', 'NOMBRE');
$sheet->setCellValue('E1', 'CLUES');
$sheet->setCellValue('F1', 'AÑO');
$sheet->setCellValue('G1', 'JUR');
$sheet->setCellValue('H1', 'DESCRIPCION_DEL_CLUES');
$sheet->setCellValue('I1', 'CODIGO');
$sheet->setCellValue('J1', 'QNA');
$sheet->setCellValue('K1', 'STATUS');
$sheet->setCellValue('L1', 'PERCEPCIONES');
$sheet->setCellValue('M1', 'DEDUCCIONES');
$sheet->setCellValue('N1', 'TOTAL_NETO');
$sheet->setCellValue('O1', 'FECHA_INGRESO');
$sheet->setCellValue('P1', 'DESC_TNOMINA');
$sheet->setCellValue('Q1', 'RECURSO');
$sheet->setCellValue('R1', 'CVE_RECURSO');
$sheet->setCellValue('S1', 'DESC_CT_DEPTO');
$sheet->setCellValue('T1', 'DESC_CEN_ART74');
$sheet->setCellValue('U1', 'CT_ART_74');
$sheet->setCellValue('V1', 'JURIS');
$sheet->setCellValue('W1', 'DESC_CATEGORIAS');
$sheet->setCellValue('X1', 'RAMA');
$sheet->setCellValue('Y1', 'D_01');
$sheet->setCellValue('Z1', 'D_04');
$sheet->setCellValue('AA1', 'D_05');
$sheet->setCellValue('AB1', 'D_62');
$sheet->setCellValue('AC1', 'D_64');
$sheet->setCellValue('AD1', 'D_65');
$sheet->setCellValue('AE1', 'D_R1');
$sheet->setCellValue('AF1', 'D_R2');
$sheet->setCellValue('AG1', 'D_R3');
$sheet->setCellValue('AH1', 'D_R4');
$sheet->setCellValue('AI1', 'D_AS');
$sheet->setCellValue('AJ1', 'D_AM');
$sheet->setCellValue('AK1', 'D_S1');
$sheet->setCellValue('AL1', 'D_S2');
$sheet->setCellValue('AM1', 'D_S4');
$sheet->setCellValue('AN1', 'D_S5');
$sheet->setCellValue('AO1', 'D_S6');
$sheet->setCellValue('AP1', 'D_O1');
$sheet->setCellValue('AQ1', 'P_00');
$sheet->setCellValue('AR1', 'P_01');
$sheet->setCellValue('AS1', 'P_06');
$sheet->setCellValue('AT1', 'P_26');
$sheet->setCellValue('AU1', 'CUENTA');
$sheet->setCellValue('AV1', 'SD');
$sheet->setCellValue('AW1', 'DIAS');
$sheet->setCellValue('AX1', 'DSCTO');
$sheet->setCellValue('AY1', 'PENSION');
$sheet->setCellValue('AZ1', 'BENEFICIARIA');
$sheet->setCellValue('BA1', 'CUENTA_BENEFICIARIA');

$sheet->setAutoFilter('A1:BA1'); //Agrega el filtrado automatico a las celdas

// Escribir los datos de la tabla captura
$row = 2; // Empezamos desde la fila 2 (después de los títulos)
foreach ($capturaData as $rowData) {
    $sheet->setCellValue('A' . $row, $rowData['NOM']);
    $sheet->setCellValue('B' . $row, $rowData['RFC']);
    $sheet->setCellValue('C' . $row, $rowData['CURP']);
    $sheet->setCellValue('D' . $row, $rowData['NOMBRE']);
    $sheet->setCellValue('E' . $row, $rowData['CLUES']);
    $sheet->setCellValue('F' . $row, $rowData['AÑO']);
    $sheet->setCellValue('G' . $row, $rowData['JUR']);
    $sheet->setCellValue('H' . $row, $rowData['DESCRIPCION_CLUES']);
    $sheet->setCellValue('I' . $row, $rowData['CODIGO']);
    $sheet->setCellValue('J' . $row, $rowData['QNA']);
    $sheet->setCellValue('K' . $row, $rowData['STATUS']);
    $sheet->setCellValue('L' . $row, $rowData['PERCEPCIONES']);
    $sheet->setCellValue('M' . $row, $rowData['DEDUCCIONES']);
    $sheet->setCellValue('N' . $row, $rowData['TOTAL_NETO']);
    $sheet->setCellValue('O' . $row, $rowData['FECHA_INGRESO']);
    $sheet->setCellValue('P' . $row, $rowData['DESC_TNOMINA']);
    $sheet->setCellValue('Q' . $row, $rowData['RECURSO']);
    $sheet->setCellValue('R' . $row, $rowData['CVE_RECURSO']);
    $sheet->setCellValue('S' . $row, $rowData['DESC_CT_DEPTO']);
    $sheet->setCellValue('T' . $row, $rowData['DESC_CEN_ART74']);
    $sheet->setCellValue('U' . $row, $rowData['CT_ART_74']);
    $sheet->setCellValue('V' . $row, $rowData['JURIS']);
    $sheet->setCellValue('W' . $row, $rowData['DESC_CATEGORIAS']);
    $sheet->setCellValue('X' . $row, $rowData['RAMA']);
    $sheet->setCellValue('Y' . $row, empty($rowData['D_01']) ? 0 : $rowData['D_01']);
    $sheet->setCellValue('Z' . $row, empty($rowData['D_04']) ? 0 : $rowData['D_04']);
    $sheet->setCellValue('AA' . $row, empty($rowData['D_05']) ? 0 : $rowData['D_05']);
    $sheet->setCellValue('AB' . $row, empty($rowData['D_62']) ? 0 : $rowData['D_62']);
    $sheet->setCellValue('AC' . $row, empty($rowData['D_64']) ? 0 : $rowData['D_64']);
    $sheet->setCellValue('AD' . $row, empty($rowData['D_65']) ? 0 : $rowData['D_65']);
    $sheet->setCellValue('AE' . $row, empty($rowData['D_R1']) ? 0 : $rowData['D_R1']);
    $sheet->setCellValue('AF' . $row, empty($rowData['D_R2']) ? 0 : $rowData['D_R2']);
    $sheet->setCellValue('AG' . $row, empty($rowData['D_R3']) ? 0 : $rowData['D_R3']);
    $sheet->setCellValue('AH' . $row, empty($rowData['D_R4']) ? 0 : $rowData['D_R4']);
    $sheet->setCellValue('AI' . $row, empty($rowData['D_AS']) ? 0 : $rowData['D_AS']);
    $sheet->setCellValue('AJ' . $row, empty($rowData['D_AM']) ? 0 : $rowData['D_AM']);
    $sheet->setCellValue('AK' . $row, empty($rowData['D_S1']) ? 0 : $rowData['D_S1']);
    $sheet->setCellValue('AL' . $row, empty($rowData['D_S2']) ? 0 : $rowData['D_S2']);
    $sheet->setCellValue('AM' . $row, empty($rowData['D_S4']) ? 0 : $rowData['D_S4']);
    $sheet->setCellValue('AN' . $row, empty($rowData['D_S5']) ? 0 : $rowData['D_S5']);
    $sheet->setCellValue('AO' . $row, empty($rowData['D_S6']) ? 0 : $rowData['D_S6']);
    $sheet->setCellValue('AP' . $row, empty($rowData['D_O1']) ? 0 : $rowData['D_O1']);
    $sheet->setCellValue('AQ' . $row, empty($rowData['P_00']) ? 0 : $rowData['P_00']);
    $sheet->setCellValue('AR' . $row, empty($rowData['P_01']) ? 0 : $rowData['P_01']);
    $sheet->setCellValue('AS' . $row, empty($rowData['P_06']) ? 0 : $rowData['P_06']);
    $sheet->setCellValue('AT' . $row, empty($rowData['P_26']) ? 0 : $rowData['P_26']);
    $sheet->getStyle('AU' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT); // Asignar el Formato de la celda como texto
    $sheet->setCellValue('AU' . $row, $rowData['CUENTA'] . " ");
    $sheet->setCellValue('AV' . $row, $rowData['SD']);
    $sheet->setCellValue('AW' . $row, $rowData['DIAS']);
    $sheet->setCellValue('AX' . $row, $rowData['DSCTO']);
    $sheet->setCellValue('AY' . $row, $rowData['PENSION']);
    $sheet->setCellValue('AZ' . $row, $rowData['BENEFICIARIA']);
    $sheet->setCellValue('BA' . $row, $rowData['CUENTA_BENEFICIARIA'] . " ");

    $row++;
}

foreach (range('A', 'BA') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

$spreadsheet->getActiveSheet()->calculateColumnWidths();  // Calcular los anchos de columna


// PROTEGER LA HOJA CON CONTRASEÑA:
//$sheet->getProtection()->setPassword('SSalud2024..//');
//$sheet->getProtection()->setSheet(true);  // Protege la hoja (no permite modificar)
//$sheet->getProtection()->setSort(false);   // Permitir ordenar (opcional)
//$sheet->getProtection()->setInsertRows(false); // No permite insertar filas
//$sheet->getProtection()->setFormatCells(false); // No permite cambiar formato de celdas

// Crear y descargar el archivo
$writer = new Xlsx($spreadsheet);
$fileName = 'QNA ' . $qna . ' - ' . $anio . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $fileName . '"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
exit;

