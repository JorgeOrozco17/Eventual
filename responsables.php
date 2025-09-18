<?php
include 'app/controllers/personalcontroller.php';
include 'app/controllers/catalogocontroller.php';
include 'app/controllers/usercontroller.php';
include 'header.php';

$catalogo = new CatalogoController();
$personal = new PersonalController();
$usuario = new UserController();

if($rol == 5){
    $responsables = $usuario->getResponsablesByRH($_SESSION['user_id']);
} elseif($rol == 1) {
    $responsables = $usuario->getAllResponsables();
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
    <a href="jurisform.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Registro
            </a>
    <div class="card mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table id="tablaComun" class="table table-hover gy-5 dataTable">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Jurisdiccion</th>
                            <th>Centro</th>
                            <th>Responsable</th>
                            <th>Responsable RH</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($responsables as $i => $r): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($r['nombre_juris']) ?></td>
                                <td><?= htmlspecialchars($r['nombre_centro']) ?></td>
                                <td><?= htmlspecialchars($r['responsable']) ?></td>
                                <td><?= htmlspecialchars($r['rh_responsable']) ?></td>
                                <td nowrap>
                                <?php if ($_SESSION['user_id'] == $r['rh_responsable'] || $_SESSION['role'] === 1): ?>
                                <a href="responsableform.php?id=<?= $r['id'] ?>" class="btn btn-icon btn-sm btn-warning me-2" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="?action=delete&id=<?= $r['id'] ?>" class="btn btn-icon btn-sm btn-danger" title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar este registro?')">
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