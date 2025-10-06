<?php
require_once 'app/controllers/capturacontroller.php';
require_once 'app/controllers/catalogocontroller.php';
include 'header.php'; 
$catalogo = new CatalogoController();
$controller = new Capturacontroller();

$id_nomina = $_GET['id'] ?? '';


if ($id_nomina) {
    $data = $controller->model->getByPeriodo($id_nomina);
} else {
    $data = []; // o null si prefieres no mostrar nada
}


if (isset($_GET['action']) && $_GET['action'] == 'savenomina' && isset($_GET['id'])){
    $controller->insertartotales($id_nomina, $data[0]['QNA'], $data[0]['AÑO']);
    header("Location: nomina.php");
    exit;
}

$quincenas = $catalogo->getAllQuincenas();

?>
<style>
    body {
        background-color: #D9D9D9 !important;
    }
    h2 {
        color: #333333
    }
</style>


<div class="container-fluid mt-5">
    <div class="regreso">
        <span class="menu-tittle"><a class="menu-link" href="nomina.php"  onclick="return confirm('Asegurese de dar clik en Finalizar si quiere guardar los cambios realizados')">
        <span class="menu-tittle">Nominas /</span>
        </a>Captura</span>
    </div>
    <br>
    <div class="card">
        <div class="card-header">
            <div class="menu-title">
                <h2>Administracion de Percepciones/Decucciones</h2>
            </div>
            <div class="card-toolbar">
                <a href="?action=savenomina&id=<?= htmlspecialchars($id_nomina) ?>" class="btn btn-primary">
                    Finalizar o Guardar Cambios
                </a>
            </div>
        </div>

        <?php if (isset($_GET['duplicate'])): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                El RFC o CURP ya existe. Por favor, verifica los datos.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                La captura del empleado se guardo correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                Ocurrió un error al capturar los conceptos.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card-body">
            <div class="table-responsive">
                <table id="tablaComun" class="table table-hover gy-5 dataTable">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>RFC</th>
                            <th>NOMBRE</th>
                            <th>JUR</th>
                            <th>QNA</th>
                            <th>AÑO</th>
                            <th>PER</th>
                            <th>DEDU</th>
                            <th>TOTAL NETO</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                         $contador = 1;
                         foreach ($data as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($contador) ?></td>
                            <td><?= htmlspecialchars($p['RFC']) ?></td>
                            <td><?= htmlspecialchars($p['NOMBRE']) ?></td>
                            <td><?= htmlspecialchars($p['JUR']) ?></td>
                            <td><?= htmlspecialchars($p['QNA']) ?></td>
                            <td><?= htmlspecialchars($p['AÑO']) ?></td>
                            <td><?= "$" . number_format($p['PERCEPCIONES'], 2, '.', ',') ?></td>
                            <td><?= "$" . number_format($p['DEDUCCIONES'], 2, '.', ',') ?></td>
                            <td><?= "$" . number_format($p['TOTAL_NETO'], 2, '.', ',') ?></td>
                            <td nowrap>
                                <a href="modifnomina.php?id=<?= $p['id'] ?>&id_n=<?= $id_nomina ?>&qna=<?=$p['QNA'] ?>&anio=<?= $p['AÑO'] ?>" class="btn btn-icon btn-sm me-2" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="?action=delete&id=<?= $p['id'] ?>" class="btn btn-icon btn-sm btn-danger" title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar este registro?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php 
                    $contador ++;
                    endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>