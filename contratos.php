<?php
include 'app/controllers/catalogocontroller.php';
include 'app/controllers/contratocontroller.php';
include 'header.php';

$catalogio  = new CatalogoController();
$contratoCtrl = new ContratoController();
$jurisdicciones = $catalogio->getAllJurisdicciones();

$jurisgral = $_GET['jurisdiccion'] ?? 'todas';

// El admin (juris 9) puede filtrar, los demás sólo ven su jurisdicción
if ($user_juris == 10) {
    if (isset($_GET['jurisdiccion']) && $_GET['jurisdiccion'] != 'todas') {
        $juris = $_GET['jurisdiccion'];
        $empleados = $contratoCtrl->getEmpleadosByJurisdiccion($juris);
    } else {
        $juris = 'todas';
        $empleados = $contratoCtrl->getAllEmpleados();
    }
    $mostrar_filtro = true;
} else {
    $empleados = $contratoCtrl->getEmpleadosByJurisdiccion($user_juris);
    $juris = $user_juris;
    $mostrar_filtro = false;
}

// Función para validar si el empleado tiene todos los campos obligatorios para contrato
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
    body {
        background-color: #D9D9D9 !important;
    }
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
    .filtro-contratos {
        min-width: 200px;
    }
</style>

<div class="container-fluid px-4 pt-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <?php if ($mostrar_filtro): ?>
            <form method="get" class="d-flex align-items-center gap-2">
                <label for="jurisdiccion" class="form-label mb-0">Jurisdicción:</label>
                <select id="jurisdiccion" name="jurisdiccion" class="form-select filtro-contratos" onchange="this.form.submit()">
                    <option value="todas" <?= $juris == 'todas' ? 'selected' : '' ?>>Todas</option>
                    <?php foreach($jurisdicciones as $jurisdiccion): ?>
                        <option value="<?= htmlspecialchars($jurisdiccion['id']) ?>"
                            <?= $juris == $jurisdiccion['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($jurisdiccion['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        <?php else: ?>
            <div class="mb-2">
                Jurisdicción:
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
        <a href="generar_pdf_contratos.php?jurisdiccion=<?= $jurisgral ?>" class="btn btn-primary d-flex align-items-center gap-1" onclick="return confirm('Solamente se generaran los contratos de los empleados que tengan todos los datos requeridos. ¿Deseas continuar?')">
            <i class="bi bi-printer"></i>
            Imprimir contratos
        </a>
    </div>

    <div class="section-title">
        <i class="bi bi-people"></i> Lista de empleados
    </div>
    <div class="card mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tablaComun" class="table table-hover gy-5 dataTable">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Puesto</th>
                            <th>Jurisdicción</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($empleados as $i => $empleado): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($empleado['nombre_alta']) ?></td>
                                <td><?= htmlspecialchars($empleado['puesto']) ?></td>
                                <td>
                                    <?php
                                    // Mostrar nombre de jurisdicción
                                    foreach ($jurisdicciones as $j) {
                                        if ($j['id'] == $empleado['id_adscripcion']) {
                                            echo htmlspecialchars($j['ubicacion']);
                                            break;
                                        }
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php if (empleado_completo($empleado)): ?>
                                        <span class="badge bg-success">Completo</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Incompleto</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (empleado_completo($empleado)): ?>
                                        <a href="generar_pdf_contrato.php?id=<?= $empleado['id'] ?>" target="_blank" class="btn btn-sm btn-secondary">
                                            <i class="bi bi-file-earmark-pdf"></i> Contrato
                                        </a>
                                    <?php else: ?>
                                        <a href="completar_empleado.php?id=<?= $empleado['id'] ?>" class="btn btn-sm btn-warning">
                                            <i class="bi bi-exclamation-circle"></i> Completar
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
