<?php
require_once 'vendor/autoload.php';
require_once 'app/controllers/catalogocontroller.php';
require_once 'app/controllers/PersonalController.php';

use setasign\Fpdi\Fpdi;

if (!isset($_GET['id'])) {
    die("ID no proporcionado.");
}

$id = $_GET['id'];
$catalogo = new CatalogoController();
$personal = (new PersonalController())->getPersonalById($id);
$archivos = $catalogo->getArchivosById($id);

if (isset($archivos[0])) {
    $archivos = $archivos[0];
}

if (!$personal || !$archivos) {
    die("Datos no encontrados.");
}

// Lista de campos de archivos PDF
$nombres_campos = [
    'solicitud',
    'curriculum',
    'acta_nacimiento',
    'cartilla_militar',
    'certificado_estudios',
    'titulo_profesional',
    'cedula_profesional',
    'certificacion_cedula',
    'titulo_cedula_especialidad',
    'cursos_capacitacion',
    'ine',
    'cartas_recomendacion',
    'carta_protesta',
    'compatibilidad_horario',
    'carta_compromiso',
    'certificado_medico',
    'no_antecedentes',
    'no_inhabilitado',
    'situacion_fiscal',
    'acuse_declaracion',
    'comprobante_banco'
];

// Crear el PDF final
$pdf = new FPDI();

foreach ($nombres_campos as $campo) {
    if (!empty($archivos[$campo])) {
        $ruta = 'uploads/' . $archivos[$campo];
        if (file_exists($ruta) && strtolower(pathinfo($ruta, PATHINFO_EXTENSION)) === 'pdf') {
            $pageCount = $pdf->setSourceFile($ruta);
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $tplIdx = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($tplIdx);

                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($tplIdx);
            }
        }
    }
}

if ($pdf->PageNo() === 0) {
    die("No hay archivos PDF válidos para unir.");
}

$nombre = preg_replace('/[^a-zA-Z0-9_-]/', '_', $personal['nombre_alta']);
$pdf->Output('I', "archivo_completo_{$nombre}.pdf");
exit;
