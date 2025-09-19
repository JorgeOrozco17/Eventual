<?php
require_once 'app/controllers/PersonalController.php';
include 'header.php';
$controller = new PersonalController();

$personal = $controller->model->getNoAutorizados();
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
        <span class="menu-title"><a class="menu-link" href="personal.php">
        <span class="menu-tittle">Personal</span></a> <span class="menu-tittle">/No Autorizados</span></span>
    </div>
    <br>

    <div style="margin: 10px;">
        <button class="btn btn-sm btn-info me-2" onclick="history.back()"><i class="fas fa-arrow-left-long"></i>Regresar</button>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="menu-title">
                <h2>Administración de Personal</h2>
            </div>
            <div class="card-toolbar">
                
            </div>
        </div>

        <?php if (isset($_GET['duplicate'])): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                El RFC o CURP ya existe. Por favor, verifica los datos.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Usuario guardado correctamente.
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
                            <th>Movimiento</th>
                            <th>Nombre</th>
                            <th>Neto Mensual</th>
                            <th>Bruto Mensual</th>
                            <th>Perfil</th>
                            <th>Recurso</th>
                            <th>Motivo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($personal as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['movimiento']) ?></td>
                            <td><?= htmlspecialchars($p['nombre_alta']) ?></td>
                            <td><?= '$' . number_format($p['sueldo_neto'], 2, '.', ',') ?></td>
                            <td><?= '$' . number_format($p['sueldo_bruto'], 2, '.', ',') ?></td>
                            <td><?= htmlspecialchars($p['puesto']) ?></td>
                            <td><?= htmlspecialchars($p['programa']) ?></td>
                            <?php if ($p['movimiento'] === 'baja'): ?>
                                <td><?= htmlspecialchars($p['observaciones_baja']) ?></td>
                            <?php else: ?>
                                <td><?= htmlspecialchars($p['observaciones_alta']) ?></td>
                            <?php endif; ?>
                            <td nowrap>
                                <a href="autorizafrom.php?id=<?= $p['id'] ?>" title="continuar registro" class="btn btn-icon btn-sm btn-info me-2" onclick="return confirm('Para continuar con el registro debe contar con la autorizacion de alta')">
                                    <i class="fas fa-arrow-right-long"></i>
                                </a>
                                <a href="generar_pdf.php?id=<?= $p['id'] ?>" title="imprimir alta / baja" class="btn btn-icon btn-sm btn-info me-2" target="_blank">
                                    <i class="fas fa-print"></i>
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
