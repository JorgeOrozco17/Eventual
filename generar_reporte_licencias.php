<?php
require 'vendor/autoload.php';
require_once 'app/controllers/faltas_licenciascontroller.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$quincena = $_GET['quincena'] ?? 'all';
$anio = $_GET['anio'] ?? date('Y');

// Inicia el buffer de salida
ob_start();

// Asegura las variables estén en el scope del include
$_GET['quincena'] = $quincena;
$_GET['anio'] = $anio;

include 'imprimir_licencias.php';

$html = ob_get_clean();

$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape'); // Horizontal
$dompdf->render();
$dompdf->stream("reporte_licencias.pdf", ["Attachment" => false]);
?>
