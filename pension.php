<?php
require_once 'app/controllers/deduccioncontroller.php';
include 'header.php'; 

$controller = new Deduccioncontroller();

$data = $controller->getAllPensiones();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $controller->delete($_POST['delete_id']);
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
        <span class="menu-tittle">/Pensiones</span></span>
    </div>
    <br>
    <div class="card">
        <div class="card-header">
            <div class="menu-title">
                <h2>Administracion de pensiones</h2>
            </div>
            <div class="card-toolbar">
                <a href="pensionesform.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Registro
                </a>
            </div>
        </div>
       
        <div class="card-body">
            <div class="table-responsive">
                <table id="tablaComun" class="table table-hover gy-5 dataTable">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Empleado</th>
                            <th>Porcentaje</th>
                            <th>Beneficiaria/o</th>
                            <th>Cuenta Beneficiaria</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['id']) ?></td>
                            <td><?= htmlspecialchars($p['id_personal']) ?></td>
                            <td><?= htmlspecialchars($p['porcentaje']) ?></td>
                            <td><?= htmlspecialchars($p['beneficiaria']) ?></td>
                            <td><?= htmlspecialchars($p['cuenta_beneficiaria']) ?></td>
                            <td nowrap>
                                <a href="pensionesform.php?id=<?= $p['id'] ?>" class="btn btn-icon btn-sm btn-info me-2">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="?action=delete&id=<?= $p['id'] ?>" class="btn btn-icon btn-sm btn-danger" title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar este registro?')">
                                    <i class="fas fa-trash"></i>
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

<?php include 'footer.php'; ?>