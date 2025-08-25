<?php
require_once 'app/controllers/catalogocontroller.php';
include 'header.php';

$controller = new CatalogoController();
$jurisdicciones = $controller->model->getAllJurisdicciones();
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
        <span class="menu-tittle">/Jurisdicciones</span></span>
    </div>
    <br>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="menu-title m-0">Administración de Jurisdicción</h2>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 1): ?>
            <a href="jurisform.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Registro
            </a>
            <?php endif; ?>
        </div>

        <?php if (isset($_GET['duplicate'])): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                La jurisdiccion ya existe. Por favor, verifica los datos.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Usuario guardado correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif (isset($_GET['updated'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Usuario actualizado correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                Ocurrió un error al guardar el usuario. Intenta nuevamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card-body">
            <div class="table-responsive">
                <table id="tablaComun" class="table table-hover gy-5 dataTable">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Ubicación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jurisdicciones as $j): ?>
                        <tr>
                            <td><?= htmlspecialchars($j['id']) ?></td>
                            <td><?= htmlspecialchars($j['nombre']) ?></td>
                            <td><?= htmlspecialchars($j['ubicacion']) ?></td>
                            <td nowrap>
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 1): ?>
                                <a href="jurisform.php?id=<?= $j['id'] ?>" class="btn btn-icon btn-sm btn-warning me-2" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="?action=delete&id=<?= $j['id'] ?>" class="btn btn-icon btn-sm btn-danger" title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar este registro?')">
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
