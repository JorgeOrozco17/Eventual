<?php
include 'app/controllers/catalogocontroller.php';
include 'app/controllers/contratocontroller.php';
include 'header.php';

$catalogio  = new CatalogoController();
$contratoCtrl = new ContratoController();
$jurisdicciones = $catalogio->getAllJurisdicciones();

$jurisgral = $_GET['jurisdiccion'] ?? 'todas';
$centroSel = $_GET['centro'] ?? 'todos';

if ($user_juris == 10) {
    $empleados = $contratoCtrl->getAllEmpleados();
    $mostrar_filtro = true;
} elseif ($_SESSION['role'] == 6) {
    $empleados = $contratoCtrl->getEmpleadosByJurisdiccion($user_juris);
    $mostrar_filtro = true;
    $juris = $user_juris;
} else {
    $empleados = $contratoCtrl->getEmpleadosByCentro($_SESSION['user_id']);
    $mostrar_filtro = false;
    $juris = $user_juris;
}

// === Validación de datos completos ===
function empleado_completo($empleado) {
    $campos = [
        'nombre_alta', 'nacionalidad', 'estado_civil', 'profesion', 'originario',
        'RFC', 'calle', 'colonia', 'ciudad', 'estado', 'puesto', 'adscripcion', 'sueldo_bruto'
    ];
    foreach ($campos as $campo) {
        if (empty($empleado[$campo])) return false;
    }
    return true;
}
?>

<style>
body { background-color: #D9D9D9 !important; }
.section-title {
    color: #222;
    margin-top: 16px;
    margin-bottom: 10px;
    font-size: 1.12rem;
    font-weight: 600;
    letter-spacing: 0.01em;
}
.table {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 1px 8px 0 rgba(0,0,0,0.04);
    overflow: hidden;
}
.filtro-contratos { min-width: 200px; border-radius: 10px; }
.card-filtros {
    border-left: 4px solid #0d6efd;
    border-radius: 14px;
    background: #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    padding: 1.25rem 1.5rem;
    margin-bottom: 1.5rem;
}
.card-filtros:hover {
    box-shadow: 0 3px 12px rgba(0,0,0,0.08);
}
.filtro-label i {
    font-size: 1rem;
    color: #0d6efd;
    margin-right: 4px;
}
</style>

<!-- === Importación DataTables === -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>

<div class="container-fluid px-4 pt-4">

    <!-- === Card de Filtros === -->
    <?php if ($mostrar_filtro): ?>
    <div class="card-filtros">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <!-- Filtro Jurisdicción -->
                <div class="filtro-group">
                    <label for="filtro-jurisdiccion" class="form-label filtro-label mb-1 fw-semibold text-secondary">
                        <i class="bi bi-geo-alt-fill"></i> Jurisdicción
                    </label>
                    <select id="filtro-jurisdiccion" class="form-select filtro-contratos"
                        <?= ($user_juris != 10) ? 'disabled' : '' ?>>
                        <option value="todas">Todas</option>
                        <?php foreach($jurisdicciones as $jurisdiccion): ?>
                            <option value="<?= htmlspecialchars($jurisdiccion['nombre']) ?>">
                                <?= htmlspecialchars($jurisdiccion['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Filtro Centro -->
                <?php if ($_SESSION['role'] == 6 || $user_juris == 10): ?>
                <div class="filtro-group">
                    <label for="filtro-centro" class="form-label filtro-label mb-1 fw-semibold text-secondary">
                        <i class="bi bi-building"></i> Centro
                    </label>
                    <select id="filtro-centro" class="form-select filtro-contratos">
                        <option value="todos">Todos</option>
                        <?php
                        $centrosUnicos = [];
                        foreach ($empleados as $e) {
                            if (!in_array($e['centro'], $centrosUnicos)) {
                                $centrosUnicos[] = $e['centro'];
                            }
                        }
                        foreach ($centrosUnicos as $c):
                        ?>
                            <option value="<?= htmlspecialchars($c) ?>"><?= htmlspecialchars($c) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>

                <a href="#" 
               class="btn btn-primary d-flex align-items-center gap-1"
               onclick="confirmarImpresion()">
                <i class="bi bi-printer"></i> Imprimir contratos
            </a>

            </div>

            
        </div>
    </div>
    <?php else: ?>
        <div class="alert alert-light border-start border-primary shadow-sm">
            <strong>Jurisdicción:</strong>
            <?php
            foreach ($jurisdicciones as $j) {
                if ($j['id'] == $juris) {
                    echo htmlspecialchars($j['nombre']);
                    break;
                }
            }
            ?>
        </div>
    <?php endif; ?>

    <!-- === Tabla === -->
    <div class="section-title">
        <i class="bi bi-people"></i> Lista de empleados
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <!-- 🔹 Quitamos la clase "table-striped" -->
                <table id="tablaEmpleados" class="table table-hover gy-5 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Puesto</th>
                            <th>Jurisdicción</th>
                            <th>Centro</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($empleados as $i => $empleado): ?>
                            <tr data-juris="<?= htmlspecialchars($empleado['adscripcion'] ?? '') ?>" 
                                data-centro="<?= htmlspecialchars($empleado['centro'] ?? '') ?>">
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($empleado['nombre_alta']) ?></td>
                                <td><?= htmlspecialchars($empleado['puesto']) ?></td>
                                <td><?= htmlspecialchars($empleado['adscripcion']) ?></td>
                                <td><?= htmlspecialchars($empleado['centro']) ?></td>
                                <td>
                                    <?php if (empleado_completo($empleado)): ?>
                                        <span class="badge bg-success">Completo</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Incompleto</span>
                                    <?php endif; ?>
                                </td>
                                <td class="d-flex gap-2">
                                    <?php if (empleado_completo($empleado)): ?>
                                        <a href="generar_pdf_contrato.php?id=<?= $empleado['id'] ?>" target="_blank" class="btn btn-sm btn-secondary">
                                            <i class="bi bi-file-earmark-pdf"></i> Contrato
                                        </a>
                                    <?php else: ?>
                                        <a href="completar_empleado.php?id=<?= $empleado['id'] ?>" class="btn btn-sm btn-warning">
                                            <i class="bi bi-exclamation-circle"></i> Completar
                                        </a>
                                    <?php endif; ?>
                                    <a href="completar_empleado.php?id=<?= $empleado['id'] ?>" class="btn btn-sm btn-info text-white">
                                        <i class="bi bi-pencil-square"></i> Editar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- === Scripts === -->
<script>
function confirmarImpresion() {
    if (confirm('Solo se generarán los contratos de los empleados con todos los datos requeridos. ¿Deseas continuar?')) {
        const juris = document.getElementById('filtro-jurisdiccion')?.value || 'todas';
        const centro = document.getElementById('filtro-centro')?.value || 'todos';
        window.location.href = `generar_pdf_contratos.php?jurisdiccion=${juris}`;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const filtroJuris = document.getElementById('filtro-jurisdiccion');
    const filtroCentro = document.getElementById('filtro-centro');

    // Inicializamos DataTable
    const tabla = $('#tablaEmpleados').DataTable({
        responsive: true,
        pageLength: 10,
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/es-MX.json"
        }
    });

    // 🔹 Custom filtering en DataTables
    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        const jurisSel = filtroJuris ? filtroJuris.value : 'todas';
        const centroSel = filtroCentro ? filtroCentro.value : 'todos';
        const juris = tabla.row(dataIndex).node().getAttribute('data-juris');
        const centro = tabla.row(dataIndex).node().getAttribute('data-centro');

        const coincideJuris = (jurisSel === 'todas' || juris === jurisSel);
        const coincideCentro = (centroSel === 'todos' || centro === centroSel);

        return coincideJuris && coincideCentro;
    });

    // 🔹 Escucha cambios y redibuja tabla
    if (filtroJuris) filtroJuris.addEventListener('change', () => tabla.draw());
    if (filtroCentro) filtroCentro.addEventListener('change', () => tabla.draw());
});
</script>

<?php include 'footer.php'; ?>
