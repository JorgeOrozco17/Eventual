<?php
require_once 'app/controllers/capturacontroller.php';
require_once 'app/controllers/catalogocontroller.php';
include 'header.php'; 
$catalogo = new CatalogoController();
$controller = new Capturacontroller();

$qna = $_GET['qna'] ?? '';
$anio = $_GET['anio'] ?? '';

$data = $controller->getAllNomina();
$quincenas = $catalogo->getAllQuincenas();

if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $qna = isset($_GET['qna']) ? $_GET['qna'] : null;
    $anio = isset($_GET['anio']) ? $_GET['anio'] : null;

    // Si quieres que deleteNomina reciba qna y anio:
    $controller->deleteNomina($id, $qna, $anio);

    header("Location: nomina.php");
    exit;
}


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
        <span class="menu-title"><a class="menu-link" href="menu_captura.php">
        <span class="menu-tittle">Captura</span></a> 
        <span class="menu-tittle">/Gestion de nominas</span></span>
    </div>
    <br>
    <div class="card">
        <div class="card-header">
            <div class="menu-title">
                <h2>Administracion de Percepciones/Decucciones</h2>
            </div>
            <div class="card-toolbar">
                <a href="generar_nomina.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Registro
                </a>
            </div>
        </div>
       
        <div class="card-body">
            <div class="table-responsive">
                <table id="tablaComun" class="table table-hover gy-5 dataTable">
                    <thead class="table-light">
                        <tr>
                            <th>QNA</th>
                            <th>AÑO</th>
                            <th>Total Registros</th>
                            <th>Total Percepciones</th>
                            <th>Total Deducciones</th>
                            <th>Total Neto</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['qna']) ?></td>
                            <td><?= htmlspecialchars($p['año']) ?></td>
                            <td><?= htmlspecialchars($p['total_registros']) ?></td>
                            <td><?= '$' . number_format($p['total_percepciones'], 2, '.', ',') ?></td>
                            <td><?= '$' . number_format($p['total_deducciones'], 2, '.', ',') ?></td>
                            <td><?= '$' . number_format($p['total_neto'], 2, '.', ',') ?></td>
                            <td nowrap>
                                <?php if (!empty($p['total_percepciones']) && $p['total_percepciones'] != '0.0'): ?>
                                    <a href="generar_excel.php?qna=<?= $p['qna'] ?>&anio=<?= $p['año'] ?>" class="btn btn-icon btn-sm btn-info me-2">
                                        <i class="fas fa-file-lines"></i>
                                    </a>
                                
                                <?php else: ?>
                                <a href="captura.php?qna=<?= $p['qna'] ?>&anio=<?= $p['año'] ?>" class="btn btn-icon btn-sm btn-info me-2">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php endif; ?>
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 1): ?>
                                <a href="?action=delete&id=<?= $p['id'] ?>&qna=<?= $p['qna'] ?>&anio=<?= $p['año'] ?>" class="btn btn-icon btn-sm btn-danger" title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar este registro?')">
                                    <i class="fas fa-trash"></i>
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