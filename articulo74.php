<?php
include 'app/controllers/personalcontroller.php';
include 'app/controllers/catalogocontroller.php';
include 'header.php';

$catalogo = new CatalogoController();
$personal = new PersonalController();
$jurisdicciones = $catalogo->getAllJurisdicciones();

function empleado_completo($empleado) {
    $campos = [
        'desc_ct_dpto', 'desc_cen_art74', 'ct_art74', 'juris'
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
    h2 {
        color: #333333
    }
    .regreso {
        margin: 30px 0;
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
                            <th>RFC</th>
                            <th>Nombre</th>
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