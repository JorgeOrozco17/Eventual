<?php
require 'vendor/autoload.php';
require_once 'app/controllers/PersonalController.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Validar que venga el ID
if (!isset($_GET['id'])) {
    die('ID no especificado');
}

$controller = new PersonalController();
$personal = $controller->getPersonalById($_GET['id']);

if (!$personal) {
    die('Registro no encontrado');
}

// Pasar datos a la plantilla
ob_start();
include 'plantilla_nomina.php';  // Aquí usaremos $personal
$html = ob_get_clean();

$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("nomina_eventual.pdf", ["Attachment" => false]);
?>