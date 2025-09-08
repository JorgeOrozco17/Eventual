<?php
require_once 'app/controllers/catalogocontroller.php';
require_once 'app/controllers/personalController.php';
require_once 'app/controllers/reportescontroller.php';
include 'header.php';

$catalogio = new CatalogoController();
$personal = new PersonalController();
$controller = new ReportesController();

$quincenas = $catalogio->getAllQuincenas();

?>

<style>
    body {
        background-color: #D9D9D9 !important;
    }
    h2{
        color: #333333;
    }
    .hidden {
        display: none;
    }
</style>


    <h2>Reportes</h2>

    <div class="mb-4">
        <button class="btn btn-primary me-2" id="btn_quincena">
            Altas y bajas por quincena
        </button>
        <button class="btn btn-secondary" id="btn_periodo">
            Altas y bajas por periodo
        </button>
        <?php if ($_SESSION['role'] === 1 || $_SESSION['role'] === 2): ?>
        <button class="btn btn-secondary" id="btn_oficio">
            Oficio de altas y bajas por quincena
        </button>
        <?php endif; ?>
    </div>

<div class="container mt-5">
    
        <h2>Filtros</h2>
        <div class="card mb-4 hidden"  id="filtros_quincena">
            <!---------- reporte de altas y bajas por quincena ---------->
            <div class="card-body">
                <p>Reporte de altas y bajas por quincena</p>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="quincena">Quincena</label>
                        <select name="quincena" id="quincena" class="form-select">
                            <?php foreach($quincenas as $qna): ?>
                                <option value="<?= $qna['nombre']; ?>"><?= $qna['nombre']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="anio">Año</label>
                        <input type="number" id="anio" class="form-control"
                            value="<?= date('Y'); ?>" min="2000" max="2099">
                    </div>

                    <div class="col-md-3">
                        <label for="tipo_reporte">Tipo de reporte</label>
                        <select id="tipo_reporte" class="form-select">
                            <option value="all">Todas</option>
                            <option value="alta">Altas</option>
                            <option value="baja">Bajas</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <button style="margin-top: 1rem;" class="btn btn-primary" id="generar_reporte">
                            Generar Reporte <i class="fas fa-arrow-right-long"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 hidden" id="filtros_periodo">
            <!---------- reporte de altas y bajas por periodo ---------->
            <div class="card-body">
                <p>Reporte de altas y bajas por periodo</p>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="quincena_inicio">Fecha Inicio</label>
                        <input type="date" id="quincena_inicio" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label for="quincenafin">Fecha Fin</label>
                        <input type="date" id="quincena_fin" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label for="tipo_reporte">Tipo de reporte</label>
                        <select id="tipo_reporte" class="form-select">
                            <option value="all">Todas</option>
                            <option value="alta">Altas</option>
                            <option value="baja">Bajas</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <button style="margin-top: 1rem;" class="btn btn-primary" id="generar_reporte_periodo">
                            Generar Reporte <i class="fas fa-arrow-right-long"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4 hidden" id="filtros_oficio">
            <!---------- oficio de altas y bajas por periodo ---------->
            <div class="card-body">
                <form action="generar_oficio.php" method="POST" target="_blank" enctype="multipart/form-data">
                    <p>Oficio Altas y Bajas por quincena</p>
                    <div class="row g-3"> 
                        <div class="col-md-3">
                            <label for="quincena">Quincena</label>
                            <select name="qna" id="quincena" class="form-select">
                                <?php foreach($quincenas as $qna): ?>
                                    <option value="<?= $qna['nombre']; ?>"><?= $qna['nombre']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="anio">AÑO</label>
                            <input type="number" name="anio" id="anio" class="form-control">
                        </div>

                        <div class="col-md-3">
                            <label for="oficio">Numero de oficio</label>
                            <input type="text" name="oficio" id="oficio" class="form-control">
                        </div>

                        <div class="col-md-3">
                            <button type="submit" style="margin-top: 1rem;" class="btn btn-success">Generar oficio</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
</div>
<div class="container mt-5">
    <h2>Resultados</h2>

    <!-- Aquí se mostrarán los resultados -->
    <div class="card hidden" id="resultados">
        <div class="card-body">
             <button id="btn_imprimir" class="btn btn-success">
                <i class="fas fa-print"></i> Imprimir informe
            </button>
            <div id="tabla_resultados"></div>
        </div>
    </div>

</div>


<script>

const btnQna = document.getElementById("btn_quincena");
const btnPer = document.getElementById("btn_periodo");
const btnOfi = document.getElementById("btn_oficio");

const filtrosQna = document.getElementById("filtros_quincena");
const filtrosPer = document.getElementById("filtros_periodo");
const filtrosOfi = document.getElementById("filtros_oficio");
const resultados = document.getElementById("resultados");

// Al hacer clic en quincena
function mostrarSeccion(activa) {
    // Ocultar todas
    filtrosQna.classList.add("hidden");
    filtrosPer.classList.add("hidden");
    filtrosOfi.classList.add("hidden");

    // Quitar estilos activos
    btnQna.classList.remove("btn-primary"); btnQna.classList.add("btn-secondary");
    btnPer.classList.remove("btn-primary"); btnPer.classList.add("btn-secondary");
    btnOfi.classList.remove("btn-primary"); btnOfi.classList.add("btn-secondary");

    // Mostrar la activa
    if (activa === "qna") {
        filtrosQna.classList.remove("hidden");
        btnQna.classList.add("btn-primary");
        btnQna.classList.remove("btn-secondary");
    } else if (activa === "per") {
        filtrosPer.classList.remove("hidden");
        btnPer.classList.add("btn-primary");
        btnPer.classList.remove("btn-secondary");
    } else if (activa === "ofi") {
        filtrosOfi.classList.remove("hidden");
        btnOfi.classList.add("btn-primary");
        btnOfi.classList.remove("btn-secondary");
    }

    resultados.classList.remove("hidden");
    tabla_resultados.innerHTML = ""; // limpiar resultados
}

// Listeners
btnQna.addEventListener("click", () => mostrarSeccion("qna"));
btnPer.addEventListener("click", () => mostrarSeccion("per"));
btnOfi.addEventListener("click", () => mostrarSeccion("ofi"));

document.getElementById("generar_reporte").addEventListener("click", function() {
    const qna  = document.getElementById("quincena").value;
    const anio = document.getElementById("anio").value;
    const tipo = document.getElementById("tipo_reporte").value;

    const formData = new FormData();
    formData.append("action", "quincena");
    formData.append("quincena", qna);
    formData.append("anio", anio);
    formData.append("tipo_reporte", tipo);

    fetch("reportes_ajax.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        document.getElementById("tabla_resultados").innerHTML = data;

         if (document.getElementById("tablaComun")) {
            $("#tablaComun").DataTable({
                pageLength: 10,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                responsive: true
            });
        }
    })
    .catch(err => {
        console.error("Error:", err);
        document.getElementById("tabla_resultados").innerHTML = 
            "<p class='text-danger'>Error al generar el reporte</p>";
    });
});

document.getElementById("generar_reporte_periodo").addEventListener("click", function() {
    const qnaInicio = document.getElementById("quincena_inicio").value;
    const qnaFin    = document.getElementById("quincena_fin").value;
    const tipo      = document.getElementById("tipo_reporte").value;

    const formData = new FormData();
    formData.append("action", "periodo");
    formData.append("quincena_inicio", qnaInicio);
    formData.append("quincena_fin", qnaFin);
    formData.append("tipo_reporte", tipo);

    fetch("reportes_ajax.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        document.getElementById("tabla_resultados").innerHTML = data;

        if (document.getElementById("tablaComun")) {
            $("#tablaComun").DataTable({
                pageLength: 10,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                },
                responsive: true
            });
        }
    })
    .catch(err => {
        console.error("Error:", err);
        document.getElementById("tabla_resultados").innerHTML = 
            "<p class='text-danger'>Error al generar el reporte</p>";
    });
});
document.getElementById("btn_imprimir").addEventListener("click", function() {
    const contenido = document.getElementById("tabla_resultados").innerHTML;
    const ventana = window.open('', '', 'height=600,width=800');
    ventana.document.write('<html><head><title>Informe</title>');
    ventana.document.write('<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">');
    ventana.document.write('<style>table{width:100%;border-collapse:collapse;}th,td{border:1px solid #ddd;padding:8px;text-align:left;}</style>');
    ventana.document.write('</head><body>');
    ventana.document.write('<h2 style="text-align:center;">Informe generado</h2>');
    ventana.document.write(contenido);
    ventana.document.write('</body></html>');
    ventana.document.close();
    ventana.print();
});
</script>

<?php 
include 'footer.php'; 
?>
