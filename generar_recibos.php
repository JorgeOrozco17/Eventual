<?php
require 'vendor/autoload.php';
require_once 'app/controllers/capturacontroller.php';

use setasign\Fpdi\Fpdi;

$jurisdiccion = $_POST['jurisdiccion'] ?? 'todas';
$quincena = $_POST['quincena'] ?? '';
if (!$quincena) die('Quincena no especificada');
$anio = date('Y');

$captura = new Capturacontroller();

if ($jurisdiccion === 'todas') {
    // Sin filtro de jurisdicción
    $registros = $captura->getCapturaPorQuincenaSinJurisdiccion($quincena, $anio);
} else {
    // Solo esa jurisdicción
    $registros = $captura->getCapturaPorQuincena($quincena, $anio, $jurisdiccion);
}

$empleados = [];
foreach ($registros as $r) {
    $percepciones = [];
    $percepciones[] = ['00 Subsidio', $r['P_00']];
    $percepciones[] = ['01 Sueldo', $r['P_01']];
    if ($r['P_06'] > 0) $percepciones[] = ['06 Despensa', $r['P_06']];
    $percepciones[] = ['26 Gratificación extraordinaria', $r['P_26']];


    $deducciones = [];
    $deducciones[] = ['01 ISR', $r['D_01']];
    if ($r['D_04'] > 0) $deducciones[] = ['04 Anticipo de viaticos', $r['D_04']];
    $deducciones[] = ['05 Faltas', $r['D_05']];
    $deducciones[] = ['62 Pension alimenticia', $r['D_62']];
    $deducciones[] = ['64 Amortizacion Fovissste', $r['D_64']];
    $deducciones[] = ['65 Seguro de daños Fovissste', $r['D_65']];
    $deducciones[] = ['AS Prestamo', $r['D_AS']];
    $deducciones[] = ['S2 Seguro de Salud', $r['D_S2']];
    $deducciones[] = ['S4 Invalidez y Vida', $r['D_S4']];
    $deducciones[] = ['S5 Servicios sociales y culturales', $r['D_S5']];
    $deducciones[] = ['S6 Cesantía y Vejez', $r['D_S6']];
    $deducciones[] = ['AM Adeudo Mercatil', $r['D_AM']];
    if ($r['D_O1'] > 0) $deducciones[] = ['O1 Anticipo de Sueldo', $r['D_O1']];


    $empleados[] = [
        'nombre' => $r['NOMBRE'],
        'rfc' => $r['RFC'],
        'puesto' => $r['DESC_CATEGORIAS'],
        'departamento' => $r['DESC_CT_DEPTO'],
        'distribuidora' => $r['JURIS'],
        'percepciones' => $percepciones,      // <--- así
        'deducciones' => $deducciones,        // <--- así
        'neto' => $r['TOTAL_NETO']
    ];
}

class ReciboPDF extends Fpdi {}

$pdf = new ReciboPDF('P', 'mm', 'Letter');
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(true, 15);
$pdf->SetFont('Arial', 'B', 8);

// Columnas sin separación ni bordes
$colWidth = 70; // 3 columnas de 70mm (70*3=210mm, área útil de Letter=216mm, así tienes margen)
$xCol1 = 10;
$xCol2 = $xCol1 + 3 + $colWidth;
$xCol3 = $xCol2 + 3 + $colWidth;

foreach (array_chunk($empleados, 3) as $grupo) {
    $pdf->AddPage();

    $logo_path = '/public/img/escudo.png'; // Usa PNG/JPG para compatibilidad
    $pdf->Image($logo_path, 15, 14, 18, 18); // x=15 para margen izquierdo

    // Encabezado una sola vez por página
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetXY(15, 15);
    $pdf->Cell(0, 6, utf8_decode("SERVICIOS DE SALUD DE COAHUILA"), 0, 1, 'C');
    $pdf->SetXY(15, 21);
    $pdf->Cell(0, 6, utf8_decode("NÓMINASEventualES"), 0, 1, 'C');
    $pdf->Cell(0, 6, utf8_decode("PERIODO {$anio} - Q{$quincena}"), 0, 1, 'C');

    $y_inicio = 35;
    $y_cuerpo = 25; // Espacio entre encabezado y cuerpo
    
    foreach ($grupo as $emp) {
        $left = 10;
        $right = 206; // 216 - 15
        $y_top = $y_inicio - 1;        // Un poco arriba del texto
        $y_bottom = $y_inicio + 10;    // Un poco abajo del texto
        $pdf->SetFont('Arial', 'B', 8);

         // Datos generales (puedes ajustar estos si lo deseas)
        $pdf->Line($left, $y_top, $right, $y_top);
        $pdf->SetXY($xCol1, $y_inicio);
        $pdf->Cell($colWidth * 2, 5, utf8_decode("DEPARTAMENTO:  {$emp['departamento']}"), 0, 1);
        $pdf->SetX($xCol1);
        $pdf->Cell($colWidth * 2, 5, utf8_decode("U. DISTRIBUIDORA: {$emp['distribuidora']}"), 0, 1);
        $pdf->Line($left, $y_bottom, $right, $y_bottom);

        // NOMBRE y RFC en columna 1, espacio después de RFC
        $pdf->SetX($xCol1);
        $pdf->Cell($colWidth, 7, utf8_decode("NOMBRE: {$emp['nombre']}"), 0, 1, 'L');
        $pdf->SetX($xCol1);
        $pdf->Cell($colWidth, 2, utf8_decode("RFC: {$emp['rfc']}"), 0, 1, 'L');


        // Deja espacio extra entre RFC y percepciones
        $espacio_extra = 3; // mm extra de espacio
        $y_percepciones = $pdf->GetY() + $espacio_extra;

        // PUESTO alineado a la derecha de columna 2 (justo arriba de deducciones)
        $pdf->SetXY($xCol2, $y_inicio + 11); // puedes ajustar el +10 para más/menos altura
        $pdf->Cell($colWidth, 5, utf8_decode("PUESTO: {$emp['puesto']}"), 0, 2, 'R'); // 'R' lo carga a la derecha

        // Máximo número de filas
        $maxRows = max(count($emp['percepciones']), count($emp['deducciones']));

        // 1ra columna: Percepciones
        $pdf->SetXY($xCol1, $y_inicio + 20);
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell($colWidth, 5, utf8_decode("PERCEPCIONES:"), 0, 2, 'C');
        $pdf->Cell($colWidth / 2, 4, utf8_decode("Concepto"), 0, 0, 'L');
        $pdf->Cell($colWidth / 2, 4, utf8_decode("Importe"), 0, 1, 'R');
        $totalPer = 0;
        for ($i = 0; $i < $maxRows; $i++) {
            $pdf->SetX($xCol1);
            if (isset($emp['percepciones'][$i])) {
                $pdf->Cell($colWidth / 2, 3, utf8_decode($emp['percepciones'][$i][0]), 0, 0, 'L');
                $pdf->Cell($colWidth / 2, 3, '$' . number_format($emp['percepciones'][$i][1], 2), 0, 1, 'R');
                $totalPer += $emp['percepciones'][$i][1];
            } else {
                $pdf->Cell($colWidth / 2, 3, '', 0, 0, 'L');
                $pdf->Cell($colWidth / 2, 3, '', 0, 1, 'R');
            }
        }
        $pdf->Ln(5);
        $pdf->SetX($xCol1);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell($colWidth / 2, 0, utf8_decode("Total percepciones"), 0, 0, 'L');
        $pdf->Cell($colWidth / 2, 0, '$' . number_format($totalPer, 2), 0, 1, 'R');

        // 2da columna: Deducciones
        $pdf->SetXY($xCol2, $y_inicio + 20);
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell($colWidth, 5, utf8_decode("DEDUCCIONES:"), 0, 2, 'C');
        $pdf->Cell($colWidth / 2, 4, utf8_decode("Concepto"), 0, 0, 'L');
        $pdf->Cell($colWidth / 2, 4, utf8_decode("Importe"), 0, 1, 'R');
        $totalDed = 0;
        for ($i = 0; $i < $maxRows; $i++) {
            $pdf->SetX($xCol2);
            if (isset($emp['deducciones'][$i])) {
                $pdf->Cell($colWidth / 2, 3, utf8_decode($emp['deducciones'][$i][0]), 0, 0, 'L');
                $pdf->Cell($colWidth / 2, 3, '$' . number_format($emp['deducciones'][$i][1], 2), 0, 1, 'R');
                $totalDed += $emp['deducciones'][$i][1];
            } else {
                $pdf->Cell($colWidth / 2, 3, '', 0, 0, 'L');
                $pdf->Cell($colWidth / 2, 3, '', 0, 1, 'R');
            }
        }
        $pdf->Ln();
        $pdf->SetX($xCol2);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell($colWidth / 2, 4, utf8_decode("Total deducciones"), 0, 0, 'L');
        $pdf->Cell($colWidth / 2, 4, '$' . number_format($totalDed, 2), 0, 1, 'R');

        // 3ra columna: Total a pagar + Recibi
        $pdf->SetXY($xCol3 + 5, $y_inicio + 20);
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell($colWidth, 5, utf8_decode("TOTAL A PAGAR:"), 0, 2, 'L');
        $pdf->Cell($colWidth, 5, '$' . number_format($totalPer - $totalDed, 2), 0, 2, 'L');
        $pdf->Ln(30);
        $pdf->SetX($xCol3);
        $pdf->Cell($colWidth, 0, utf8_decode('____________________________________'), 0, 2, 'L');
        $pdf->SetX($xCol3 + 20);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell($colWidth, 12, utf8_decode('Recibí'), 0, 2, 'L');


        $y_inicio += 16 + ($maxRows * 5);
    }
}

$pdf->Output('I', 'hoja_firmas_QNA' . $quincena . '_' . $anio . '.pdf');

