<?php
// Datos ya vienen desde generar_pdf.php
$nombre = $personal['nombre_alta'];
$fecha_contrato = $personal['inicio_contratacion'] ?? 'No aplica';
$rfc = $personal['RFC'];
$curp = $personal['CURP'];
$tipo_mov = strtoupper($personal['movimiento']);
$solicita = $personal['solicita'];
$puesto = $personal['puesto'];
$programa = $personal['programa'];
$descripcion = $personal['adscripcion'];
$sueldo_neto = "$" . number_format($personal['sueldo_neto'], 2);
$sueldo_bruto = "$" . number_format($personal['sueldo_bruto'], 2);
$quincena = $personal['quincena_alta'] ?? 'No aplica';
$oficio = $personal['numero_oficio'];
$adscripciones = $personal['adscripcion'];
$centro = $personal['centro'];
$ovservaciones = $personal['observaciones_alta'];
$qna_baja = $personal['quincena_baja'] ?? 'No aplica';
$fecha_baja = $personal['fecha_baja'] ?? 'No aplica';
$observaciones_baja = $personal['observaciones_baja'] ?? 'No aplica';
?>

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Oficio alta o baja</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }
        .logos{
            width: 100%;
            margin-top: 30px;
            margin-bottom: 20px;
        }
        .logo1{
            height: 80px;
            margin-right: 25%;
            border-radius: 7px;
        }
        .logo2{
            height: 80px;
            margin-left: 20%;
            border-radius: 7px;
        }
        .etiqueta {
            font-weight: bold;
            padding: 3px;
            width: 180px;
        }
        .valor {
            padding: 3px 6px;
            font-weight: bold;
            color: red;
        }
        .observacion {
            padding: 3px 6px;
        }
        .titulo {
            background-color: #0C4DA2;
            color: white;
            padding: 5px;
            text-align: center;
            font-weight: bold;
            font-size: 12px;
        }
        .seccion {
            margin: 8px 0px;
        }
        .seccion2{
            margin: 8px;
        }
        .campo {
            margin: 8px 0;
        }
        .camposub {
            background-color: rgb(156, 213, 252);
            width: 50%;
            border-radius: 2px;
            padding: 2px 50px;
        }
        .firma-table {
            width: 100%;
            margin-bottom: 40px;
            border-collapse: collapse;
        }
        .firma-cell {
            text-align: center;
            width: 45%;
            vertical-align: top;
            padding: 0 10px;
        }
        .firma-spacer {
            width: 10%;
        }
        .linea-firma {
            border-top: 1px solid #000;
            padding-top: 5px;
            margin-top: 20%;
        }
        .elabora-container {
            width: 50%;
            margin: 0 auto;
            text-align: center;
        }
    </style>
</head>
<body>


<?php if ($tipo_mov == 'ALTA'): ?>

<h2></h2>
<div class="logos">
    <img class="logo1" src="public/img/logo_coah.png" alt="Logo Coahuila">
    <img class="logo2" src="public/img/logoss.jpg" alt="Logo Secretaría">
</div>

<div class="pdfBody">
    <div class="seccion2">
        <strong>NÓMINA Eventual</strong>
    </div>

    <div class="titulo">DATOS DEL MOVIMIENTO</div>
<table style="width: 100%; font-size: 11px; margin: 5px 0px;">
    <tr>
        <td class="etiqueta">TIPO DE MOVIMIENTO:</td>
        <td class="valor"><?= $tipo_mov ?></td>
        <td class="etiqueta">SOLICITA:</td>
        <td class="valor"><?= $solicita ?>a</td>
    </tr>
    <tr>
        <td class="etiqueta">OFICIO:</td>
        <td class="valor"><?= $oficio ?></td>
        <td class="etiqueta">PROGRAMA:</td>
        <td class="valor"><?= $programa ?></td>
    </tr>
    <tr>
        <td class="etiqueta">ADSCRIPCIÓN:</td>
        <td class="valor"><?= $adscripciones . '-' . $centro ?></td>
        <td class="etiqueta">SUELDO NETO MENSUAL:</td>
        <td class="valor"><?= $sueldo_neto ?></td>
    </tr>
    <tr>
        <td class="etiqueta">PUESTO:</td>
        <td class="valor"><?= $puesto ?></td>
        <td class="etiqueta">SUELDO BRUTO MENSUAL:</td>
        <td class="valor"><?= $sueldo_bruto ?></td>
    </tr>
    <tr>
        <td class="etiqueta">QUINCENA DE APLICACIÓN:</td>
        <td class="valor"><?= $quincena ?></td>
        <td></td>
        <td></td>
    </tr>
</table>

    <div class="titulo">DATOS PERSONALES DE LA ALTA</div>
    <div class="seccion">
        <div class="campo"><span>NOMBRE:</span> <span class="valor"><?= $nombre ?></span></div>
        <div class="camposub"><span>Apellido Paterno /  Apellido Materno / Nombre</span></div>
    </div>
    <table style="width: 100%; font-size: 11px; margin: 10px 0px 20px 0px">
        <tr>
            <td class="etiqueta">RFC: </td>
            <td class="etiqueta">CRUP: </td>
        </tr>
        <tr>
            <td class="valor"> <?= $rfc ?> </td>
            <td class="valor"> <?= $curp ?></td>
        </tr>
        <tr>
            <td class="etiqueta"> Inicio de contratación</td>
            <td class="valor"> <?= $fecha_contrato ?></td>
        </tr>
        <tr>
            <td class="etiqueta"> Observaciones</td>
            <td></td>
        </tr>
        <tr>
            <td class="observacion" colspan="2"><?= $ovservaciones ?></td>
        </tr>
    </table>

    <div class="text-center mt-5" style="font-size: 10px;">
        <table class="firma-table">
            <tr>
                <td class="firma-cell">
                    <div><strong>AUTORIZA</strong></div>
                    <div class="linea-firma">
                        <strong>C.P. MARIA DE JESUS RAMIREZ HERNANDEZ</strong><br>
                        DIRECTORA DE ADMINISTRACIÓN
                    </div>
                </td>
                <td class="firma-spacer"></td>
                <td class="firma-cell">
                    <div><strong>AUTORIZA</strong></div>
                    <div class="linea-firma">
                        <strong>LIC. JANNET ALEJANDRA GONZALEZ HIDROGO</strong><br>
                        SUBDIRECTORA DE RECURSOS HUMANOS
                    </div>
                </td>
            </tr>
        </table>

        <div class="elabora-container">
            <div><strong>ELABORA</strong></div>
            <div class="linea-firma">
                <strong>LIC. JENDY NAYELI GONZALEZ CASTAÑEDA</strong><br>
                ENCARGADA DE RECLUTAMIENTO Y SELECCIÓN DE PERSONAL
            </div>
        </div>
    </div>
</div>

<!------------------------------------ Baja ------------------------------------>
<?php elseif ($tipo_mov == 'BAJA'): ?>

<h2></h2>
<div class="logos">
    <img class="logo1" src="public/img/logo_coah.png" alt="Logo Coahuila">
    <img class="logo2" src="public/img/logoss.jpg" alt="Logo Secretaría">
</div>

<div class="pdfBody">
    <div class="seccion2">
        <strong>NÓMINA Eventual</strong>
    </div>

    <div class="titulo">DATOS DEL MOVIMIENTO</div>
<table style="width: 100%; font-size: 11px; margin: 5px 0px;">
    <tr>
        <td class="etiqueta">TIPO DE MOVIMIENTO:</td>
        <td class="valor"><?= $tipo_mov ?></td>
        <td class="etiqueta">SOLICITA:</td>
        <td class="valor"><?= $solicita ?>a</td>
    </tr>
    <tr>
        <td class="etiqueta">OFICIO:</td>
        <td class="valor"><?= $oficio ?></td>
        <td class="etiqueta">PROGRAMA:</td>
        <td class="valor"><?= $programa ?></td>
    </tr>
    <tr>
        <td class="etiqueta">ADSCRIPCIÓN:</td>
        <td class="valor"><?= $adscripciones . '-' . $centro ?></td>
        <td class="etiqueta">SUELDO NETO MENSUAL:</td>
        <td class="valor"><?= $sueldo_neto ?></td>
    </tr>
    <tr>
        <td class="etiqueta">PUESTO:</td>
        <td class="valor"><?= $puesto ?></td>
        <td class="etiqueta">QUINCENA DE APLICACIÓN:</td>
        <td class="valor"><?= $quincena ?></td>
    </tr>
</table>

<br>
<br>

    <div class="titulo">DATOS PERSONALES DE LA BAJA</div>
    <div class="seccion">
        <div class="campo"><span>NOMBRE:</span> <span class="valor"><?= $nombre ?></span></div>
        <div class="camposub"><span>Apellido Paterno /  Apellido Materno / Nombre</span></div>
    </div>
    <table style="width: 100%; font-size: 11px; margin: 10px 0px 20px 0px">
        <tr>
            <td class="etiqueta">Fecha: </td>
        </tr>
        <tr>
            <td class="valor"> <?= $fecha_baja ?> </td>
        </tr>
        <tr>
            <td class="etiqueta"> Observaciones</td>
            <td></td>
        </tr>
        <tr>
            <td class="observacion" colspan="2"><?= $ovservaciones ?></td>
        </tr>
    </table>

    <div class="text-center mt-5" style="font-size: 10px;">
        <table class="firma-table">
            <tr>
                <td class="firma-cell">
                    <div><strong>ELABORA</strong></div>
                    <div class="linea-firma">
                        <strong>LIC. JENDY NAYELI GONZALEZ CASTAÑEDA</strong><br>
                        ENCARGADA DE RECLUTAMIENTO Y SELECCIÓN DE PERSONAL
                    </div>
                </td>
                <td class="firma-spacer"></td>
                <td class="firma-cell">
                    <div><strong>AUTORIZA</strong></div>
                    <div class="linea-firma">
                        <strong>LIC. JANNET ALEJANDRA GONZALEZ HIDROGO</strong><br>
                        SUBDIRECTORA DE RECURSOS HUMANOS
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>

<?php endif; ?>

</body>
</html>