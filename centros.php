<?php
require_once 'app/controllers/catalogocontroller.php';

include 'header.php';

$controller = new CatalogoController();
$centros = $controller->model->getAllCentros();
?>
<style>
    body {
        background-color: #D9D9D9 !important;
    }
    h2 {
        color: #333333;
    }
</style>

<div class="container-fluid mt-5">
    <div class="regreso">
        <span class="menu-title"><a class="menu-link" href="catalogos.php">
        <span class="menu-tittle">Catalogos</span></a> 
        <span class="menu-tittle">/Centros</span></span>
    </div>
    <br>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="menu-title m-0">Administración de Centros</h2>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 1): ?>
            <a href="centrosform.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Registro
            </a>
            <?php endif; ?>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="tablaComun" class="table table-hover gy-5 dataTable">
                    <thead class="table-light">
                        <tr>
                            <th>Jurisdicción</th>
                            <th>Nombre</th>
                            <th>Clues</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($centros as $c): ?>
                        <tr>
                            <td><?= htmlspecialchars($c['jurisdiccion'])?></td>
                            <td><?= htmlspecialchars($c['nombre']) ?></td>
                            <td><?= htmlspecialchars($c['clues']) ?></td>
                            <td nowrap>
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 1): ?>
                                <a href="jurisform.php?id=<?= $c['id'] ?>" class="btn btn-icon btn-sm btn-warning me-2" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="?action=delete&id=<?= $c['id'] ?>" class="btn btn-icon btn-sm btn-danger" title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar este registro?')">
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
