<?php 
require_once 'app/controllers/PersonalController.php';
include 'header.php'; 


$controller = new PersonalController();

$personal = $controller->model->getAll();
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
</style>
    <div class="regreso">
        <span class="menu-title"><a class="menu-link" href="catalogos.php">
        <span class="menu-tittle">Catalogos</span></a> 
        <span class="menu-tittle">/Expediente Digital</span></span>
    </div>


<div class="card">
        <div class="card-header">
            <div class="menu-title">
                <h2>Expediente digital del Personal</h2>
            </div>
        </div>
<div class="card-body">
            <div class="table-responsive">
                <table id="tablaComun" class="table table-hover gy-5 dataTable">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>RFC</th>
                            <th>CURP</th>
                            <th>Adscripcion</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($personal as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['id']) ?></td>
                            <td><?= htmlspecialchars($p['nombre_alta']) ?></td>
                            <td><?= htmlspecialchars($p['RFC']) ?></td>
                            <td><?= htmlspecialchars($p['CURP']) ?></td>
                            <td><?= htmlspecialchars($p['adscripcion']) ?></td>
                            <td nowrap>
                                <a href="archivodetalle.php?id=<?= $p['id'] ?>" title="Archivo digital" class="btn btn-icon btn-sm btn-info me-2">
                                    <i class="fas fa-folder-open"></i>
                                </a>
                                <a href="" title="Imprimir Archivo Unico" class="btn btn-icon btn-sm btn-warning me-2">
                                    <i class="fas fa-print"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

<?php include 'footer.php'; ?>