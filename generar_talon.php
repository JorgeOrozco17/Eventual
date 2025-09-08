<?php
require 'vendor/autoload.php';
require_once 'app/controllers/capturacontroller.php';
require_once 'app/controllers/catalogocontroller.php';

use setasign\Fpdi\Fpdi;

$jurisdiccion = $_POST['jurisdiccion'] ?? 'todas';
$quincena = $_POST['quincena'] ?? '';
if (!$quincena) die('Quincena no especificada');
$anio = date('Y');

$captura = new Capturacontroller();
$catalogo = new CatalogoController();

$qna = $catalogo->getAllQuincenas();

$quincenaData = null;
foreach ($qna as $q) {
    // Si $q['nombre'] = 'QNA 11', entonces:
    if ($q['nombre'] == 'QNA ' . $quincena) {
        $quincenaData = $q;
        break;
    }
}

if ($jurisdiccion === 'todas') {
    $registros = $captura->getCapturaPorQuincenaSinJurisdiccion($quincena, $anio);
} else {
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
    if ($r['D_AM'] > 0) $deducciones[] = ['AM Adeudo Mercatil', $r['D_AM']];
    if ($r['D_O1'] > 0) $deducciones[] = ['O1 Anticipo de Sueldo', $r['D_O1']];

    $empleados[] = [
        'nombre' => $r['NOMBRE'],
        'rfc' => $r['RFC'],
        'puesto' => $r['DESC_CATEGORIAS'],
        'departamento' => $r['DESC_CT_DEPTO'],
        'distribuidora' => $r['JURIS'],
        'percepciones' => $percepciones,
        'deducciones' => $deducciones,
        'fecha_ingreso' => $r['FECHA_INGRESO'],
        'neto' => $r['TOTAL_NETO']
    ];
    
}

class ReciboPDF extends Fpdi {}

$pdf = new ReciboPDF('P', 'mm', 'Letter');
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(true, 15);
$pdf->SetFont('Arial', 'B', 8);

$colWidth = 70; // Ancho de cada columna
$xCol1 = 13; // dentro de la caja, deja más margen interno
$xCol2 = $xCol1 + 3 + $colWidth;
$xCol3 = $xCol2 + 2 + $colWidth;
$y_iniciales = [15, 140];    // Puedes ajustar 150 si quieres el segundo más arriba o más abajo
$altoCaja = 112.5;             // Altura de cada caja (ajusta según el contenido)
$anchoCaja = 197;            // 216 (Letter) - 2*16.5 (margen) ~183mm
$x_inicio = 8;              // margen izquierdo

foreach (array_chunk($empleados, 2) as $grupo) {
    $pdf->AddPage();

    foreach ($grupo as $i => $emp) {
        $y_inicio = $y_iniciales[$i];

        // === Caja/borde para el recibo ===
        $pdf->Rect($x_inicio, $y_inicio, $anchoCaja, $altoCaja);

        // === LOGO Y ENCABEZADO DENTRO DE LA CAJA ===
        $logo_path = __DIR__ . '/public/img/escudo.png';
        $logo_y = $y_inicio + 4;
        $pdf->Image($logo_path, $x_inicio + 3, $logo_y, 18, 18);

        // Encabezado
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetXY($x_inicio + 40, $y_inicio + 5);
        $pdf->Cell($anchoCaja - 70, 3, utf8_decode("SERVICIOS DE SALUD DE COAHUILA"), 0, 2, 'C');
        $pdf->Cell($anchoCaja - 70, 5, utf8_decode("NOMINAS EventualES"), 0, 2, 'C');

        // --- DATOS GENERALES ---
        $left = $x_inicio + 3;
        $right = $x_inicio + $anchoCaja - 3;
        $baseX_izq = $x_inicio + 40;                    // columna izquierda dentro de la caja
        $baseX_der = $x_inicio + $anchoCaja - 95;       // columna derecha cerca del borde derecho
        $colDerWidth = 80;                              // ancho columna derecha
        $lineaY = $y_inicio + 16;                       // posición vertical inicial (ajusta si lo necesitas)
        $salto = 4;   

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetXY($baseX_izq, $lineaY);
        $pdf->Cell(80, 2, utf8_decode("NOMBRE: {$emp['nombre']}"), 0, 0, 'L');
        $pdf->SetXY($baseX_der, $lineaY);
        $pdf->Cell($colDerWidth, 2, utf8_decode("RECIBO DE NOMINA"), 0, 0, 'R');

        // Segunda línea: Categoria - RFC
        $lineaY += $salto;
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetXY($baseX_izq, $lineaY);
        $pdf->Cell(80, 2.5, utf8_decode("CATEGORIA: {$emp['puesto']}"), 0, 0, 'L');
        $pdf->SetXY($baseX_der, $lineaY);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell($colDerWidth, 2.5, utf8_decode("RFC: {$emp['rfc']}"), 0, 0, 'R');

        // Tercera línea: Nomina
        $lineaY += $salto;
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetXY($baseX_izq, $lineaY);
        $pdf->Cell(80, 3, utf8_decode("NOMINA: EventualES"), 0, 0, 'L');

        // ---------- CUADRO DE DATOS GENERALES ----------

        $y_cuadro = $lineaY + $salto + 2;  // Un poco abajo del último dato
        $altoCuadro = 17;                  // Ajusta el alto del cuadro
        $anchoCuadro = $anchoCaja - 10;
        $x_cuadro = $x_inicio + 5;

        $pdf->Rect($x_cuadro, $y_cuadro, $anchoCuadro, $altoCuadro);

        // Primera línea: Forma de pago, jurisdicción, periodo de pago
        $pdf->SetFont('Arial', '', 6.7);
        $pdf->SetXY($x_cuadro + 2, $y_cuadro + 2);
        $pdf->Cell(45, 4, utf8_decode("FORMA DE PAGO: TARJETA"), 0, 0, 'L');
        $pdf->Cell(60, 4, utf8_decode(" {$emp['distribuidora']}"), 0, 0, 'L');
        $pdf->Cell(78, 4, utf8_decode("PERIODO DE PAGO: " . $quincenaData['inicio'] . "/$anio - " . $quincenaData['fin'] . "/$anio"), 0, 1, 'R');

        // Segunda línea: Banco, lugar de trabajo, fecha de pago
        $pdf->SetX($x_cuadro + 2);
        $pdf->Cell(45, 4, utf8_decode("BANCO:"), 0, 0, 'L');
        $pdf->Cell(60, 4, "", 0, 0, 'L'); // Si tienes el dato, aquí va el banco
        $pdf->Cell(68, 4, utf8_decode("FECHA DE PAGO: " . $quincenaData['fin'] . "/$anio"), 0, 1, 'R');

        // Tercera línea: Lugar de trabajo, fecha de ingreso
        $pdf->SetX($x_cuadro + 2);
        $pdf->Cell(80, 4, utf8_decode("LUGAR DE TRABAJO: {$emp['departamento']}"), 0, 0, 'L');
        $pdf->Cell(40, 4, "", 0, 0, 'L');
        $pdf->Cell(60, 4, utf8_decode("FECHA DE INGRESO: {$emp['fecha_ingreso']}"), 0, 1, 'R');

        // ----------- FIN DEL CUADRO ----------


        // Máximo número de filas
        $maxRows = max(count($emp['percepciones']), count($emp['deducciones']));

        // 1ra columna: Percepciones
        $pdf->SetXY($xCol1, $y_inicio + 48);
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell($colWidth, 5, utf8_decode("PERCEPCIONES:"), 0, 2, 'C');
        $pdf->Cell($colWidth / 2, 4, utf8_decode("Concepto"), 0, 0, 'L');
        $pdf->Cell($colWidth / 2, 4, utf8_decode("Importe"), 0, 1, 'R');
        $totalPer = 0;
        for ($j = 0; $j < $maxRows; $j++) {
            $pdf->SetX($xCol1);
            if (isset($emp['percepciones'][$j])) {
                $pdf->Cell($colWidth / 2, 3, utf8_decode($emp['percepciones'][$j][0]), 0, 0, 'L');
                $pdf->Cell($colWidth / 2, 3, '$' . number_format($emp['percepciones'][$j][1], 2), 0, 1, 'R');
                $totalPer += $emp['percepciones'][$j][1];
            } else {
                $pdf->Cell($colWidth / 2, 3, '', 0, 0, 'L');
                $pdf->Cell($colWidth / 2, 3, '', 0, 1, 'R');
            }
        }
        $pdf->Ln(.5);
        $pdf->SetX($xCol1);
        $pdf->SetFont('Arial', '', 5);
        $pdf->MultiCell($colWidth + $xCol2 - 40, 2.2, utf8_decode(
            "EventualES\nRECIBÍ DE CONFORMIDAD LA LIQUIDACION MOSTRADA EN ESTE RECIBO POR SUELDO Y PRESTACIONES QUE ME CORRESPONDEN POR LOS TRABAJOS QUE HE DESEMPEÑADO DECLARO QUE ME CONSIDERO LEGAL Y SATISFACTORIAMENTE PAGADO HASTA LA FECHA EN VIRTUD DE HABERSEME CUMPLIDO EN TODAS SUS PARTES CON LA LEY FEDERAL DEL TRABAJO."
        ), 0, 'L');


        // 2da columna: Deducciones
        $pdf->SetXY($xCol2, $y_inicio + 48);
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell($colWidth, 5, utf8_decode("DEDUCCIONES:"), 0, 2, 'C');
        $pdf->Cell($colWidth / 2, 4, utf8_decode("Concepto"), 0, 0, 'L');
        $pdf->Cell($colWidth / 2, 4, utf8_decode("Importe"), 0, 1, 'R');
        $totalDed = 0;
        for ($j = 0; $j < $maxRows; $j++) {
            $pdf->SetX($xCol2);
            if (isset($emp['deducciones'][$j])) {
                $pdf->Cell($colWidth / 2, 3, utf8_decode($emp['deducciones'][$j][0]), 0, 0, 'L');
                $pdf->Cell($colWidth / 2, 3, '$' . number_format($emp['deducciones'][$j][1], 2), 0, 1, 'R');
                $totalDed += $emp['deducciones'][$j][1];
            } else {
                $pdf->Cell($colWidth / 2, 3, '', 0, 0, 'L');
                $pdf->Cell($colWidth / 2, 3, '', 0, 1, 'R');
            }
        }

        // 3ra columna: Total a pagar + Recibi
        $pdf->SetXY($xCol3 + 5, $y_inicio + 48);
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell($colWidth, 5, utf8_decode("Saldos:"), 0, 2, 'L');
        $pdf->Ln(30);
        $pdf->SetX($xCol3);
        $pdf->Cell($colWidth, 0, utf8_decode('________________________'), 0, 2, 'L');
        $pdf->SetX($xCol3 + 20);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell($colWidth, 12, utf8_decode('Recibí'), 0, 2, 'L');

        // === CUADRO DE TOTALES ===
        $ancho_totales = $anchoCaja - 10;
        $x_totales = $x_inicio + 5;
        $alto_totales = 8; // altura del cuadro
        $y_totales = $y_inicio + $altoCaja - $alto_totales - 3; // deja un margen de 6 abajo

        $pdf->Rect($x_totales, $y_totales, $ancho_totales, $alto_totales);

        // Calcula el ancho de cada bloque
        $bloque = intval($ancho_totales / 3);

        $pdf->SetFont('Arial', 'B', 8);

        // --- Total percepciones ---
        $pdf->SetXY($x_totales, $y_totales + 2);
        $pdf->Cell($bloque, 6, utf8_decode("Total Percepciones: $" . number_format($totalPer, 2)), 0, 0, 'C');

        // --- Total deducciones ---
        $pdf->SetXY($x_totales + $bloque, $y_totales + 2);
        $pdf->Cell($bloque, 6, utf8_decode("Total Deducciones: $" . number_format($totalDed, 2)), 0, 0, 'C');

        // --- Total a pagar ---
        $pdf->SetXY($x_totales + 2 * $bloque, $y_totales + 2);
        $pdf->Cell($bloque, 6, utf8_decode("Total Pagado: $" . number_format($totalPer - $totalDed, 2)), 0, 0, 'C');

    }
}


$pdf->Output('I', 'recibos_nomina_QNA' . $quincena . '_' . $anio . '.pdf');

