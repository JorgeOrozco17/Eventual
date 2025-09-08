<?php
require 'vendor/autoload.php';
require_once 'app/controllers/PersonalController.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Validar que venga el ID
$qna    = $_POST['qna']    ?? null;         // ejemplo: "QNA 17"
$anio   = isset($_POST['anio']) ? (int)$_POST['anio'] : (int)date('Y'); // ejemplo: 2025
$oficio = $_POST['oficio'] ?? null;

$controller = new PersonalController();
$personal = $controller->getPersonalNew($qna, $anio);

if (!$personal) {
    die('No se encontraron datos para la quincena y año especificados.');
}   

// Pasar datos a la plantilla
ob_start();
include 'plantilla_oficio.php';  // Aquí usaremos $personal
$html = ob_get_clean();

$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("nomina_eventual.pdf", ["Attachment" => false]);
?>