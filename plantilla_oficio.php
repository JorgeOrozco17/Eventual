<?php
// Valores que recibes desde generar_oficio.php
$oficio = $oficio ?? ($_POST['oficio'] ?? '');
$anio   = $anio   ?? ($_POST['anio'] ?? date('Y'));

// Fecha “22 de agosto de 2025” en español sin depender del locale del servidor:
$meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
$fecha_larga = date('j') . ' de ' . $meses[(int)date('n')-1] . ' de ' . date('Y');

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <base href="https://nomina.soed.mx/Eventual/">
    <title>Oficio alta o baja</title>
    <style>
        /* 1) Reserva espacio para header y footer en TODAS las páginas */
        @page {
            margin: 120px 50px 110px 50px; /* top, right, bottom, left */
        }

        /* 2) Header fijo que se repite en todas las páginas */
        #pdf-header {
            position: fixed;
            top: -100px;    /* ≈ - (alto header + padding). Ajusta fino si hace falta */
            left: 0;
            right: 0;
            height: 90px;   /* debe concordar con el espacio reservado arriba (120px aprox.) */
            z-index: 10;
        }

        /* 3) Footer fijo que se repite en todas las páginas */
        #pdf-footer {
            position: fixed;
            bottom: -90px;  /* ≈ - (alto footer + padding) */
            left: 0;
            right: 0;
            height: 80px;   /* debe concordar con el espacio reservado abajo (110px aprox.) */
            z-index: 10;
            font-size: 9px;
        }

        /* 4) Tablas largas: cabecera repetible y sin cortes feos */
        table { page-break-inside: auto; }
        tr    { page-break-inside: avoid; page-break-after: auto; }
        thead { display: table-header-group; }
        tfoot { display: table-row-group; }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 0;
        }
        .logos{
            width: 100%;
            margin-top: 10px;
            margin-bottom: 10px;
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
        .encabezado { display: flex; justify-content: space-between; align-items: flex-start; margin: 5px 0 5px; }
        .enc-izq p { margin: 0; line-height: 1.3; }
        .enc-der { text-align: right; }
        .enc-der p { margin: 0; line-height: 1.3; }
        .enc-dest { font-weight: bold; }
        .conocimientos {
            font-size: 9px;        /* más chico que el resto */
            line-height: 1.1;      /* menos espacio vertical entre líneas */
            margin-top: 1px;      /* separa un poco de la tabla de firmas */
        }
        .conocimientos p {
            margin: 2px 0;         /* reduce márgenes de cada párrafo */
        }

        /* Header fijo que se repite en todas las páginas */
        #pdf-header {
            position: fixed;
            left: 0;
            right: 0;
            height: 90px;
            margin-bottom: 5%;
        }

        /* Footer fijo que se repite en todas las páginas */
        #pdf-footer {
            position: fixed;
            bottom: -90px; /* ajusta según la altura real del footer */
            left: 0;
            right: 0;
            height: 90px;
            font-size: 9px;
        }

        /* Tabla: evitar cortes raros dentro de filas */
        table { page-break-inside: auto; }
        tr    { page-break-inside: avoid; page-break-after: auto; }
        thead { display: table-header-group; } /* Dompdf lo respeta */
        tfoot { display: table-row-group; }
    </style>
</head>
<body>
    
<header id="pdf-header">
  <div style="display:flex; align-items:center; justify-content:space-between; padding:8px 10px;">
    <img src="/public/img/logo_coah.png" class="logo1" alt="Logo Coahuila" style="height:70px; border-radius:7px;">
    <img src="/public/img/logoss.jpg" class="logo2" alt="Logo Secretaría" style="height:70px; border-radius:7px;">
  </div>
</header>

<div class="pdfBody">

    <div class="encabezado">

    <!-- Derecha: número de oficio + lugar/fecha -->
        <div class="enc-der">
            <p><strong>Oficio RH/<?= htmlspecialchars($oficio) ?>/<?= htmlspecialchars($anio) ?></strong></p>
            <p>Saltillo, Coahuila de Zaragoza a <?= htmlspecialchars($fecha_larga) ?></p>
        </div>

        <!-- Izquierda: destinatario -->
        <div class="enc-izq">
            <p class="enc-dest">C.P. MIGUEL ANGEL FLORES LUIS</p>
            <p>DIRECTOR ADJUNTO DE ADMINISTRACIÓN</p>
            <p>P R E S E N T E .-</p>
        </div>

    </div>

    <div class="texto-intro" style="margin-top:20px; width: 80%; text-align:justify; line-height:1.5;">
        <p>
            Sirva el presente para hacer llegar a usted los expedientes de bajas y altas 
            que se afectaron en la nómina eventual de la <strong><?= htmlspecialchars($qna ?? '') ?></strong> <strong>año <?= htmlspecialchars($anio) ?></strong></strong>, 
            a fin de solicitar su firma y concluir el expediente respectivo.
        </p>
    </div>

    <!-- ================= TABLA DE BAJAS ================= -->
    <div class="seccion">
        <div class="titulo">BAJAS</div>
        <table class="table table-sm table-bordered" style="font-size:11px; margin-top:10px;">
            <thead class="text-center">
                <tr>
                    <th>NOMBRE</th>
                    <th>NETO MENSUAL</th>
                    <th>BRUTO MENSUAL</th>
                    <th>RECURSO</th>
                    <th>PERFIL</th>
                    <th>ADSCRIPCIÓN</th>
                    <th>MOTIVO</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $totalNetoBajas  = 0;
                $totalBrutoBajas = 0;

                foreach ($personal as $p) {
                    if (strtoupper($p['movimiento'] ?? '') !== 'BAJA') continue;

                    $totalNetoBajas  += (float)($p['sueldo_neto_mensual'] ?? 0);
                    $totalBrutoBajas += (float)($p['sueldo_bruto_mensual'] ?? 0);
                ?>
                <tr>
                    <td><?= htmlspecialchars($p['nombre_alta'] ?? '') ?></td>
                    <td class="text-right">$<?= number_format((float)($p['sueldo_neto_mensual'] ?? 0),2) ?></td>
                    <td class="text-right">$<?= number_format((float)($p['sueldo_bruto_mensual'] ?? 0),2) ?></td>
                    <td><?= htmlspecialchars($p['programa'] ?? '') ?></td>
                    <td><?= htmlspecialchars($p['puesto'] ?? '') ?></td>
                    <td><?= htmlspecialchars($p['centro'] ?? '') ?></td>
                    <td><?= htmlspecialchars($p['observaciones_baja'] ?? '') ?></td>
                </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <th class="text-right">TOTAL</th>
                    <th class="text-right">$<?= number_format($totalNetoBajas,2) ?></th>
                    <th class="text-right">$<?= number_format($totalBrutoBajas,2) ?></th>
                    <th colspan="4"></th>
                </tr>
            </tfoot>
        </table>
    </div>


    <!-- ================= TABLA DE ALTAS ================= -->
    <div class="seccion">
        <div class="titulo">ALTAS</div>
        <table class="table table-sm table-bordered" style="font-size:11px; margin-top:10px;">
            <thead class="text-center">
                <tr>
                    <th>NOMBRE</th>
                    <th>NETO MENSUAL</th>
                    <th>BRUTO MENSUAL</th>
                    <th>RECURSO</th>
                    <th>PERFIL</th>
                    <th>ADSCRIPCIÓN</th>
                    <th>MOTIVO</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $totalNetoAltas  = 0;
                $totalBrutoAltas = 0;

                foreach ($personal as $p) {
                    if (strtoupper($p['movimiento'] ?? '') !== 'ALTA') continue;

                    $totalNetoAltas  += (float)($p['sueldo_neto_mensual'] ?? 0);
                    $totalBrutoAltas += (float)($p['sueldo_bruto_mensual'] ?? 0);
                ?>
                <tr>
                    <td><?= htmlspecialchars($p['nombre_alta'] ?? '') ?></td>
                    <td class="text-right">$<?= number_format((float)($p['sueldo_neto_mensual'] ?? 0),2) ?></td>
                    <td class="text-right">$<?= number_format((float)($p['sueldo_bruto_mensual'] ?? 0),2) ?></td>
                    <td><?= htmlspecialchars($p['programa'] ?? '') ?></td>
                    <td><?= htmlspecialchars($p['puesto'] ?? '') ?></td>
                    <td><?= htmlspecialchars($p['centro'] ?? '') ?></td>
                    <td><?= htmlspecialchars($p['observaciones_alta'] ?? '') ?></td>
                </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <th class="text-right">TOTAL</th>
                    <th class="text-right">$<?= number_format($totalNetoAltas,2) ?></th>
                    <th class="text-right">$<?= number_format($totalBrutoAltas,2) ?></th>
                    <th colspan="4"></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="seccion" style="margin-top:18px;">
    <table class="firma-table">
        <tr>
        <!-- Izquierda: SOLICITA + línea para firma -->
        <td class="firma-cell" style="text-align:center; width:70%;">
            <div style="font-weight:bold; margin-bottom:40px;">SOLICITA</div>
            <div style="margin-top:6px; font-weight:bold;">
            LIC. JANNET ALEJANDRA GONZÁLEZ HIDROGO
            </div>
            <div style="margin-top:1px;">
            SUBDIRECTORA DE RECURSOS HUMANOS SERVICIOS DE SALUD DE COAHUILA
            </div>
        </td>
        </tr>
    </table>
    <div class="conocimientos">
        <p>c.p.p. C.P. Maria de Jesús Ramírez Hernández - Directora de administracion - de conocimiento</p>
        <p>c.c.p. C.P. Javier Gutiérrez Carrillo - Subdirector de Finanzas y Presupuestos - de conocimiento</p>
    </div>
    </div>
</div>

<footer id="pdf-footer">
  <div style="display:flex; align-items:center; justify-content:flex-start; gap:12px; padding:6px 10px;">
    <img src="/public/img/logo_C_colores.png" alt="Logo C" style="height:80px;">
    <img src="/public/img/datos_ss.png"      alt="Datos SS" style="height:80px;">
  </div>
</footer>
</body>
</html>