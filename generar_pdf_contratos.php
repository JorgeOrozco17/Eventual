<?php
session_start();
require 'vendor/autoload.php';
include 'app/controllers/contratocontroller.php';
include 'app/controllers/catalogocontroller.php';
include 'app/controllers/usercontroller.php';
use PhpZip\ZipFile;
use Dompdf\Dompdf;
use Dompdf\Options;

// --- CONFIGURACIÓN DE PERFORMANCE Y CACHÉ ---
$tmpDir = __DIR__ . '/tmp';
if (!is_dir($tmpDir)) {
    mkdir($tmpDir, 0777, true);
}

$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);
$options->set('isPhpEnabled', false);
$options->set('tempDir', $tmpDir);
$options->set('chroot', __DIR__);

// 1. Recibe la jurisdicción (puede ser "todas")
$jurisdiccion = $_GET['jurisdiccion'] ?? 'todas';


$catalogoCtrl = new CatalogoController();
$usuarios = new UserController();

if ($jurisdiccion == 'todas') {
    $juris_empleado = $catalogoCtrl->getAllJurisdicciones();
} else {
    $juris_empleado = $catalogoCtrl->getJurisdiccionById($jurisdiccion);
}

// 2. Consulta todos los empleados del filtro
$contratoCtrl = new ContratoController();
if ($jurisdiccion == 'todas') {
    $empleados = $contratoCtrl->getAllEmpleados();
} else {
    $empleados = $contratoCtrl->getEmpleadosByJurisdiccion($jurisdiccion);
}
if (!$empleados) exit('No hay empleados para el filtro.');

// --- TRIMESTRES ---
$trimestres = $contratoCtrl->getTrimestres();
$year_actual = date('Y');
$hoy = date('Y-m-d');

// Función para pasar dd/mm a Y-m-d
function trimestre_fecha($fecha, $year) {
    $partes = explode('/', $fecha);
    return $year . '-' . str_pad($partes[1], 2, '0', STR_PAD_LEFT) . '-' . str_pad($partes[0], 2, '0', STR_PAD_LEFT);
}

// Encuentra el trimestre actual
$trimestre_actual = null;
foreach ($trimestres as $t) {
    $inicio = trimestre_fecha($t['fecha_inicio'], $year_actual);
    $fin    = trimestre_fecha($t['fecha_fin'], $year_actual);
    if ($hoy >= $inicio && $hoy <= $fin) {
        $trimestre_actual = [
            'nombre' => $t['nombre'],
            'inicio' => $inicio,
            'fin'    => $fin
        ];
        break;
    }
}
if (!$trimestre_actual) {
    $ultimo = end($trimestres);
    $trimestre_actual = [
        'nombre' => $ultimo['nombre'],
        'inicio' => trimestre_fecha($ultimo['fecha_inicio'], $year_actual),
        'fin'    => trimestre_fecha($ultimo['fecha_fin'], $year_actual)
    ];
}

// 3. Prepara directorio temporal para PDFs
$tmpDir = sys_get_temp_dir() . '/contratos_' . uniqid();
mkdir($tmpDir);

// 4. Lee el template
$template = file_get_contents('contrato_template.html');
$archivos = [];

// Función para convertir sueldo a letras
function convertir_a_letras($numero) {
    $formatter = new NumeroALetras();
    $entero = floor($numero);
    $decimal = round(($numero - $entero) * 100);
    $letras = $formatter->convertir($entero);
    $decimalesTxt = str_pad($decimal, 2, '0', STR_PAD_LEFT);
    return strtoupper("$letras PESOS $decimalesTxt/100 M.N.");
}

// Clase auxiliar para letras
class NumeroALetras {
    private $UNIDADES = [
        '', 'uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve',
        'diez', 'once', 'doce', 'trece', 'catorce', 'quince',
        'dieciséis', 'diecisiete', 'dieciocho', 'diecinueve', 'veinte'
    ];
    private $DECENAS = [
        '', '', 'veinte', 'treinta', 'cuarenta', 'cincuenta', 'sesenta',
        'setenta', 'ochenta', 'noventa'
    ];
    private $CENTENAS = [
        '', 'ciento', 'doscientos', 'trescientos', 'cuatrocientos',
        'quinientos', 'seiscientos', 'setecientos', 'ochocientos', 'novecientos'
    ];
    public function convertir($num) {
        if ($num == 0) {
            return 'cero';
        }
        if ($num < 0) {
            return 'menos ' . $this->convertir(-$num);
        }
        $out = '';
        if ($num >= 1000000) {
            $out .= ($num >= 2000000 ? $this->convertir(intval($num / 1000000)) . ' ' : '') .
                    'millón' . ($num >= 2000000 ? 'es' : '') .
                    ($num % 1000000 == 0 ? '' : ' ' . $this->convertir($num % 1000000));
        } elseif ($num >= 1000) {
            $out .= ($num >= 2000 ? $this->convertir(intval($num / 1000)) . ' ' : ($num >= 1000 ? 'mil ' : '')) .
                    ($num % 1000 == 0 ? '' : $this->convertir($num % 1000));
        } elseif ($num >= 100) {
            if ($num == 100) {
                $out .= 'cien';
            } else {
                $out .= $this->CENTENAS[intval($num / 100)] . ' ' . $this->convertir($num % 100);
            }
        } elseif ($num > 20) {
            $out .= $this->DECENAS[intval($num / 10)];
            if (($num % 10) > 0) {
                $out .= ' y ' . $this->UNIDADES[$num % 10];
            }
        } else {
            $out .= $this->UNIDADES[$num];
        }
        return trim($out);
    }
}

// Función para obtener edad desde el RFC
function edad_desde_rfc($rfc) {
    $fecha_str = substr($rfc, 4, 6);
    if (!preg_match('/^\d{6}$/', $fecha_str)) {
        return '';
    }
    $anio = intval(substr($fecha_str, 0, 2));
    $mes  = intval(substr($fecha_str, 2, 2));
    $dia  = intval(substr($fecha_str, 4, 2));
    $anio_full = ($anio <= intval(date('y'))) ? 2000 + $anio : 1900 + $anio;
    $fecha_nac = DateTime::createFromFormat('Y-m-d', "$anio_full-$mes-$dia");
    $hoy = new DateTime();
    if (!$fecha_nac) return '';
    $edad = $hoy->diff($fecha_nac)->y;
    return $edad;
}

// Validación: sólo procesar empleados con todos los datos requeridos
function empleado_valido($empleado) {
    $campos = [
        'nombre_alta', 'nacionalidad', 'estado_civil', 'profesion', 'originario',
        'RFC', 'calle', 'colonia', 'ciudad', 'estado', 'puesto', 'adscripcion', 'sueldo_bruto'
    ];
    foreach ($campos as $campo) {
        if (empty($empleado[$campo])) return false;
    }
    return true;
}

$responsableRH = $contratoCtrl->getResponsableByUser($_SESSION['user_id']);
$cargo_responsable = $contratoCtrl->getCargoById($_SESSION['user_id']);

// --- Ciclo principal de generación de PDFs ---
foreach ($empleados as $empleado) {
    // Edad desde RFC
    $edad_trabajador = edad_desde_rfc($empleado['RFC']);

    // Validar datos completos
    if (!empleado_valido($empleado)) {
        continue;
    }

    // === FECHAS Y VIGENCIA POR TRIMESTRE ===
    $fecha_contratacion = $empleado['inicio_contratacion']; // Cambia si tu campo tiene otro nombre
    $fecha_inicio_tri = $trimestre_actual['inicio'];
    $fecha_fin_tri    = $trimestre_actual['fin'];

    // Determinar inicio
    if ($fecha_contratacion && $fecha_contratacion >= $fecha_inicio_tri && $fecha_contratacion <= $fecha_fin_tri) {
        $inicio_contrato = $fecha_contratacion;
    } else {
        $inicio_contrato = $fecha_inicio_tri;
    }
    $fin_contrato = $fecha_fin_tri;

    // Calcular vigencia meses y días
    $dt_inicio = new DateTime($inicio_contrato);
    $dt_fin = new DateTime($fin_contrato);
    $intervalo = $dt_inicio->diff($dt_fin);
    $vigencia_meses = $intervalo->m + ($intervalo->y * 12);
    $vigencia_dias = $intervalo->d;

    // Si es menos de un mes, solo días
    if ($vigencia_meses < 1) {
        $vigencia_dias = $dt_inicio->diff($dt_fin)->days;
    }

    // Obtén la jurisdicción usando el ID preferentemente
    $adscripcion_nombre = '';
    $adscripcion_ubicacion = '';
    if (!empty($empleado['id_adscripcion'])) {
        $juris = $catalogoCtrl->getJurisdiccionById($empleado['id_adscripcion']);
        if ($juris) {
            $adscripcion_nombre = $juris['nombre'] ?? '';
            $adscripcion_ubicacion = $juris['ubicacion'] ?? '';
        }
    }

    // Construye la variable combinada
    $adscripcion_full = trim($adscripcion_nombre . ' - ' . ($adscripcion_ubicacion ?  $adscripcion_ubicacion : ''));

    $sueldo_mensual = $empleado['sueldo_bruto']; // Sueldo mensual

    $responsable = $_SESSION['name'];


// --- Arma el array de reemplazo ---
$vars = [
        '{{NOMBRE_TRABAJADOR}}'   => '<span class="underline">' . htmlspecialchars($empleado['nombre_alta']) . '</span>',
        '{{EDAD}}'                => '<span class="underline">' . htmlspecialchars($edad_trabajador) . '</span>',
        '{{NACIONALIDAD}}'        => '<span class="underline">' . htmlspecialchars($empleado['nacionalidad']) . '</span>',
        '{{ESTADO_CIVIL}}'        => '<span class="underline">' . htmlspecialchars($empleado['estado_civil']) . '</span>',
        '{{PROFESION}}'           => '<span class="underline">' . htmlspecialchars($empleado['profesion']) . '</span>',
        '{{ORIGINARIO}}'          => '<span class="underline">' . htmlspecialchars($empleado['originario']) . '</span>',
        '{{RFC_TRABAJADOR}}'      => '<span class="underline">' . htmlspecialchars($empleado['RFC']) . '</span>',
        '{{CALLE}}'               => '<span class="underline">' . htmlspecialchars($empleado['calle']) . '</span>',
        '{{COLONIA}}'             => '<span class="underline">' . htmlspecialchars($empleado['colonia']) . '</span>',
        '{{CIUDAD}}'              => '<span class="underline">' . htmlspecialchars($empleado['ciudad']) . '</span>',
        '{{ESTADO}}'              => '<span class="underline">' . htmlspecialchars($empleado['estado']) . '</span>',
        '{{PUESTO_TRABAJADOR}}'   => '<span class="underline">' . htmlspecialchars($empleado['puesto']) . '</span>',
        '{{FECHA_INICIO}}'        => '<span class="underline">' . date('d/m/Y', strtotime($inicio_contrato)) . '</span>',
        '{{FECHA_FIN}}'           => '<span class="underline">' . date('d/m/Y', strtotime($fin_contrato)) . '</span>',
        '{{ADSCRIPCION}}'         => '<span class="underline">' . htmlspecialchars($adscripcion_full) . '</span>',
        '{{VIGENCIA_MESES}}'      => '<span class="underline">' . htmlspecialchars($vigencia_meses) . '</span>',
        '{{VIGENCIA_DIAS}}'       => '<span class="underline">' . htmlspecialchars($vigencia_dias) . '</span>',
        '{{SALARIO}}'             => '<span class="underline">' . number_format($sueldo_mensual, 2) . '</span>',
        '{{SALARIO_LETRAS}}'      => '<span class="underline">' . convertir_a_letras($sueldo_mensual) . '</span>',
        '{{RESPONSABLE}}'         => '<span >' . htmlspecialchars($responsable) . '</span>',
        '{{RESPONSABLERH}}'       => '<span >' . htmlspecialchars($responsableRH) . '</span>',
        '{{CARGO_RESPONSABLE}}'   => '<span >' . htmlspecialchars($cargo_responsable) . '</span>',
    ];


    $html = strtr($template, $vars);

    // Generar PDF
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('Letter', 'portrait');
    $dompdf->render();

    // Guarda PDF en archivo temporal
    $pdfOutput = $dompdf->output();
    $filename = $tmpDir . '/' . preg_replace('/[^a-zA-Z0-9_]/', '_', $empleado['nombre_alta']) . '.pdf';
    file_put_contents($filename, $pdfOutput);

    $archivos[] = $filename;
}

// 5. Crear el ZIP con PhpZip
$zipname = sys_get_temp_dir() . '/Contratos_' . date('Ymd_His') . '.zip';
$zipFile = new ZipFile();

try {
    foreach ($archivos as $file) {
        $zipFile->addFile($file, basename($file));
    }
    $zipFile->saveAsFile($zipname);
} finally {
    $zipFile->close();
}

// --- Limpia buffer de salida para que no meta basura en el zip
if (ob_get_length()) ob_end_clean();

// 6. Descarga el ZIP
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . basename($zipname) . '"');
header('Content-Length: ' . filesize($zipname));
flush();
readfile($zipname);
exit;


?>
