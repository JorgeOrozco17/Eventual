<?php
require 'vendor/autoload.php';
require_once 'app/models/dbconexion.php';
require_once 'app/controllers/capturacontroller.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// --- CONFIGURACIÓN DOMPDF ---
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', false);
$dompdf = new Dompdf($options);

// --- PARAMETROS DEL POST ---
$qna     = $_POST['quincena'] ?? '';
$anio    = $_POST['anio'] ?? '';
$programa= $_POST['programa'] ?? '';

// --- OBTENER DATOS ---
$controller = new CapturaController();
$data  = $controller->getTablaTotales($qna, $programa);
$dataqna = $controller->getDataNominaById($qna);

$totales  = $data['totales'] ?? [];
$programa = $data['programa'] ?? [];
$percepcion = $data['percepcion'] ?? 0;
$deduccion = $data['deduccion'] ?? 0;   
$neto = $data['neto'] ?? 0;

// --- MAPEO DE CONCEPTOS ---
$percepciones = [
    'p_00' => 'SUELDOS (P00)',
    'p_01' => 'SUELDO BASE (P01)',
    'p_06' => 'COMP. GARANTIZADA (P06)',
    'p_26' => 'GRATIFICACIÓN EXTRAORDINARIA (P26)'
];

$deducciones = [
    'd_01' => 'ISR (D01)',
    'd_04' => 'ANTICIPO DE VIÁTICOS (D04)',
    'd_05' => 'FALTAS (D05)',
    'd_62' => 'PENSIÓN ALIMENTICIA (D62)',
    'd_64' => 'AMORTIZACIÓN FOVISSSTE (D64)',
    'd_65' => 'SEGURO DE DAÑOS FOVISSSTE (D65)',

    'd_r1' => 'RETRO SEGURO DE SALUD (DR1)',
    'd_r2' => 'RETRO INVALIDEZ Y VIDA (DR2)',
    'd_r3' => 'RETRO SERVICIOS SOCIALES Y CULTURALES (DR3)',
    'd_r4' => 'RETRO CESANTÍA Y VEJEZ (DR4)',

    'd_s1' => 'RESPONSABILIDADES (DS1)',
    'd_s2' => 'SEGURO DE SALUD (DS2)',
    'd_s4' => 'INVALIDEZ Y VIDA (DS4)',
    'd_s5' => 'SERVICIOS SOCIALES Y CULTURALES (DS5)',
    'd_s6' => 'CESANTÍA Y VEJEZ (DS6)',

    'd_as' => 'PRESTAMO (DAS)',
    'd_am' => 'ADEUDO MERCANTIL (DAM)',
    'd_o1' => 'ANTICIPO DE SUELDO (DO1)'
];

// --- HTML DEL PDF ---
$html = '
<html>
<title>Tabla de totales</title>
<head>
<link rel="icon" type="image/jpeg" href="public/img/ss.jpg">
<style>
body { font-family: Arial, sans-serif; font-size: 12px; padding: 20px; }
h2 { text-align: center; margin: 0; }
.title-box {
    border: 1px solid #000;
    padding: 10px;
    text-align: center;
    font-size: 20px;
    font-weight: bold;
    margin-bottom: 15px;
}
table { border-collapse: collapse; width: 100%; margin-top: 10px; }
th, td { border: 1px solid #000; padding: 5px; }
th { background: #eee; text-align: center; }
</style>
</head>
<body>

<div class="title-box">
    SECRETARÍA DE SALUD DE COAHUILA DE ZARAGOZA
</div>

<!-- SEGUNDO CUADRO -->
<table class="info-table" style="width:100%; border: 1px solid #000; border-collapse:collapse; margin-bottom:15px;">
    <tr>
        <td style="width:20%; text-align:center; border:none; padding:6px; font-size:10px;">
            <b>NÓMINA</b>
        </td>
        <td style="width:80%; text-align:center; border:none; font-weight:bold; font-size:18px;">
            Eventual
        </td>
    </tr>
    <tr>
        <td style="width:20%; border:none; padding:6px; text-align:center; font-size:10px;">
            <b>QUINCENA</b>
        </td>
        <td style="width:80%; border:none; text-align:center; font-weight:bold; font-size:18px;">
            '.$dataqna['qna'].' &nbsp;&nbsp;&nbsp;&nbsp; <b>'.$dataqna['tipo'].'</b> &nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp; <b>AÑO '.$anio.'</b>
        </td>
    </tr>
    <tr>
        <td style="width:20%; border:none; padding:6px; text-align:center; font-size:10px;">
            <b>PROGRAMA</b>
        </td>
        <td style="width:80%; border:none; text-align:center; font-style:italic; font-weight:bold; font-size:18px; border-bottom:1px solid #000;">
            '.$programa['desc_tnomina'].'
        </td>
    </tr>
    <tr>
        <td style="width:20%; border:none; padding:6px; text-align:center; font-size:10px;">
            <b>RECURSO</b>
        </td>
        <td style="width:80%; border:none; text-align:center; font-style:italic; font-weight:bold; font-size:18px; border-bottom:1px solid #000;">
            '.$programa['nombre'].'
        </td>
    </tr>
    <tr>
        <td style="width:20%; border:none; padding:6px; text-align:center; font-size:10px;">
            <b>RAMA</b>
        </td>
        <td style="width:80%; border:none; text-align:center; font-style:italic; font-weight:bold; font-size:18px; border-bottom:1px solid #000;">
            '.$programa['rama'].'
        </td>
    </tr>
</table>';

// --- TABLA PERCEPCIONES ---
$html .= "<table>
<tr>
  <th>CONCEPTO DE PAGO</th>
  <th>DESCRIPCIÓN DEL PAGO</th>
  <th>IMPORTE</th>
</tr>";

foreach ($percepciones as $campo => $label) {
    if (!empty($totales[$campo]) && $totales[$campo] > 0) {
        $html .= "<tr>
                    <td style='text-align:center;'>".strtoupper($campo)."</td>
                    <td style='text-align:left;'>$label</td>
                    <td style='text-align:right;'>".number_format($totales[$campo],2)."</td>
                  </tr>";
    }
}
$html .= "</table>";

// TOTAL PERCEPCIONES FUERA
$html .= "<table>
<tr>
    <td colspan='4' style='text-align:right; font-weight:bold; background:#eee;'>
        TOTAL DE PERCEPCIONES: ".number_format($percepcion ?? 0,2)."
    </td>
</tr>
</table>";

// --- TABLA DEDUCCIONES ---
$html .= "<br><table>
<tr>
  <th>CONCEPTO DE PAGO</th>
  <th>DESCRIPCIÓN DEL PAGO</th>
  <th>IMPORTE</th>
</tr>";

foreach ($deducciones as $campo => $label) {
    if (!empty($totales[$campo]) && $totales[$campo] > 0) {
        $html .= "<tr>
                    <td style='text-align:center;'>".strtoupper($campo)."</td>
                    <td style='text-align:left;'>$label</td>
                    <td style='text-align:right;'>".number_format($totales[$campo],2)."</td>
                  </tr>";
    }
}
$html .= "</table>";

// TOTAL DEDUCCIONES Y NETO FUERA
$html .= "<table>
<tr>
    <td colspan='4' style='text-align:right; font-weight:bold; background:#eee;'>
        TOTAL DE DEDUCCIONES: ".number_format($deduccion ?? 0,2)."
    </td>
</tr>
<tr>
    <td colspan='4' style='text-align:right; font-weight:bold; background:#eee;'>
        TOTAL NETO: ".number_format($neto ?? 0,2)."
    </td>
</tr>
</table>";

$html .= "</body></html>";

// --- RENDER PDF EN HORIZONTAL ---
$dompdf->setPaper('A4', 'landscape');
$dompdf->loadHtml($html);
$dompdf->render();
$dompdf->stream("tabla_totales.pdf", ["Attachment" => false]);
