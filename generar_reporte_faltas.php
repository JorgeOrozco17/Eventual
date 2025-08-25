<?php
require 'vendor/autoload.php';
require_once 'app/controllers/Faltas_licenciascontroller.php';

$controller = new Faltas_licenciascontroller();

use Dompdf\Dompdf;
use Dompdf\Options;

$quincena = $_GET['quincena'] ?? '0';
$anio = $_GET['anio'] ?? date('Y');

// Inicia el buffer de salida
ob_start();

// Antes de incluir la plantilla, define las variables necesarias:
$_GET['quincena'] = $quincena;
$_GET['anio'] = $anio;

// Incluye la plantilla PHP (NO uses ?query, solo incluye el archivo)
include 'imprimir_incidencias.php';

// Captura el HTML
$html = ob_get_clean();

$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream("nomina_eventual.pdf", ["Attachment" => false]);
?>
