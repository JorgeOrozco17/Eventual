<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php';
include 'app/controllers/contratocontroller.php';
include 'app/controllers/catalogocontroller.php';
include 'app/controllers/usercontroller.php';
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

$id = $_GET['id'] ?? null;
if (!$id) exit('Empleado no especificado.');

$contratoCtrl = new ContratoController();
$catalogoCtrl = new CatalogoController();
$usuarios = new UserController();
$empleado = $contratoCtrl->getEmpleadoById($id);
if (!$empleado) exit('Empleado no encontrado.');

// --- TRIMESTRES ---
$trimestres = $contratoCtrl->getTrimestres();
$year_actual = date('Y');
$hoy = date('Y-m-d');

function trimestre_fecha($fecha, $year) {
    $partes = explode('/', $fecha);
    return $year . '-' . str_pad($partes[1], 2, '0', STR_PAD_LEFT) . '-' . str_pad($partes[0], 2, '0', STR_PAD_LEFT);
}

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

// -------- Funciones utilitarias --------
function convertir_a_letras($numero) {
    $formatter = new NumeroALetras();
    $entero = floor($numero);
    $decimal = round(($numero - $entero) * 100);
    $letras = $formatter->convertir($entero);
    $decimalesTxt = str_pad($decimal, 2, '0', STR_PAD_LEFT);
    return strtoupper("$letras PESOS $decimalesTxt/100 M.N.");
}

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
        if ($num == 0) return 'cero';
        if ($num < 0) return 'menos ' . $this->convertir(-$num);

        $out = '';

        if ($num >= 1000000) {
            $millones = intval($num / 1000000);
            $resto = $num % 1000000;
            if ($millones == 1) {
                $out .= 'un millón';
            } else {
                $out .= $this->convertir($millones) . ' millones';
            }
            if ($resto > 0) {
                $out .= ' ' . $this->convertir($resto);
            }
        } elseif ($num >= 1000) {
            $miles = intval($num / 1000);
            $resto = $num % 1000;
            if ($miles == 1) {
                $out .= 'mil';
            } else {
                $out .= $this->convertir($miles) . ' mil';
            }
            if ($resto > 0) {
                $out .= ' ' . $this->convertir($resto);
            }
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


// Edad desde RFC (igual que en el masivo)
function edad_desde_rfc($rfc) {
    $fecha_str = substr($rfc, 4, 6);
    if (!preg_match('/^\d{6}$/', $fecha_str)) return '';
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

// --- CALCULAR FECHAS Y VIGENCIA IGUAL QUE EL MASIVO ---
$edad_trabajador = edad_desde_rfc($empleado['RFC']);

if (!empleado_valido($empleado)) {
    exit('El empleado no tiene todos los datos requeridos.');
}

$fecha_contratacion = $empleado['inicio_contratacion'];
$fecha_inicio_tri = $trimestre_actual['inicio'];
$fecha_fin_tri    = $trimestre_actual['fin'];

if ($fecha_contratacion && $fecha_contratacion >= $fecha_inicio_tri && $fecha_contratacion <= $fecha_fin_tri) {
    $inicio_contrato = $fecha_contratacion;
} else {
    $inicio_contrato = $fecha_inicio_tri;
}
$fin_contrato = $fecha_fin_tri;

// Calcular vigencia meses y días
// --- CALCULAR VIGENCIA EN MESES Y DÍAS (corrigiendo cálculo) ---
$dt_inicio = new DateTime($inicio_contrato);
$dt_fin = new DateTime($fin_contrato);
$intervalo = $dt_inicio->diff($dt_fin);

$vigencia_meses = $intervalo->m + ($intervalo->y * 12);
$vigencia_dias = $intervalo->d;

// Si abarca el trimestre completo (3 meses exactos)
$inicio_tri_dt = new DateTime($trimestre_actual['inicio']);
$fin_tri_dt = new DateTime($trimestre_actual['fin']);

// Si el empleado inició en el mismo día que el trimestre y termina el mismo día que el trimestre
if ($dt_inicio->format('Y-m-d') == $inicio_tri_dt->format('Y-m-d') &&
    $dt_fin->format('Y-m-d') == $fin_tri_dt->format('Y-m-d')) {
    $vigencia_meses = 3;
    $vigencia_dias = 0;
}
// Si la diferencia total de días es >= 85 (promedio de 3 meses)
elseif ($dt_inicio->diff($dt_fin)->days >= 85) {
    $vigencia_meses = 3;
    $vigencia_dias = 0;
}
// Si no llega al mes completo
elseif ($vigencia_meses < 1) {
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

$sueldo_mensual = $empleado['sueldo_bruto'];

$responsable = $_SESSION['name'];
$responsableRH = $usuarios->getRespobsableByJurisdiccion($_SESSION['user_id']);
$cargo_responsable = $contratoCtrl->getCargoById($_SESSION['user_id']);


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
        '{{CENTRO}}'              => '<span class="underline">' . htmlspecialchars($empleado['centro']) . '</span>',
        '{{VIGENCIA_MESES}}'      => '<span class="underline">' . htmlspecialchars($vigencia_meses) . '</span>',
        '{{VIGENCIA_DIAS}}'       => '<span class="underline">' . htmlspecialchars($vigencia_dias) . '</span>',
        '{{SALARIO}}'             => '<span class="underline">' . number_format($sueldo_mensual, 2) . '</span>',
        '{{SALARIO_LETRAS}}'      => '<span class="underline">' . convertir_a_letras($sueldo_mensual) . '</span>',
        '{{RESPONSABLE}}'         => '<span>' . htmlspecialchars($responsable) . '</span>',
        '{{RESPONSABLERH}}'       => '<span>' . htmlspecialchars($responsableRH) . '</span>',
        '{{CARGO_RESPONSABLE}}'   => '<span>' . htmlspecialchars($cargo_responsable) . '</span>',
    ];

// --- LEE el template, reemplaza y genera PDF ---
$template = file_get_contents('contrato_template.html');
$html = strtr($template, $vars);

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('Letter', 'portrait');
$dompdf->render();

$filename = 'Contrato_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $empleado['nombre_alta']) . '.pdf';
$dompdf->stream($filename, ['Attachment'=>false]);
exit;
?>
