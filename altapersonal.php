<?php
require_once 'app/controllers/PersonalController.php';

include 'header.php';
$controller = new PersonalController();

$responsable = $_SESSION['juris'];

$personal = $controller->getAutorizados($responsable);
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
        <span class="menu-tittle">Personal</span></a> <span class="menu-tittle">/Gestionar personal</span></span>
    </div>
    <br>
    <div style="margin: 10px;">
        <button class="btn btn-sm btn-info me-2" onclick="history.back()"><i class="fas fa-arrow-left-long"></i>Regresar</button>
    </div>

    <div class="card shadow-sm border-0">
        <!-- Card Header estilo Metronic -->
        <div class="card-header d-flex justify-content-between align-items-center" style="border-top-left-radius:.5rem;border-top-right-radius:.5rem;">
            <h2 class="mb-0"><i class="fas fa-users me-2"></i>Administración de Personal</h2>
            <div>
                <!-- Puedes agregar aquí algún botón, menú, o badge si quieres -->
            </div>
        </div>
        
        <!-- Alerts -->
        <?php if (isset($_GET['duplicate'])): ?>
            <div class="alert alert-warning alert-dismissible fade show rounded-0 mb-0" role="alert">
                El RFC o CURP ya existe. Por favor, verifica los datos.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-0 mb-0" role="alert">
                Usuario guardado correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-0 mb-0" role="alert">
                Ocurrió un error al guardar el usuario. Intenta nuevamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Card Body -->
        <div class="card-body bg-white rounded-bottom">
            <div class="table-responsive">
                <table id="tablaComun" class="table table-hover gy-5 dataTable">
                    <thead class="table-light">
                        <tr>
                            <th class="min-w-100px">Movimiento</th>
                            <th class="min-w-180px">Nombre</th>
                            <th class="min-w-100px">RFC</th>
                            <th class="min-w-150px">Neto Mensual</th>
                            <th class="min-w-120px">Bruto Mensual</th>
                            <th class="min-w-120px">Perfil</th>
                            <th class="min-w-120px">Recurso</th>
                            <th class="min-w-120px">Motivo</th>
                            <th class="min-w-150px text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($personal as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['movimiento']) ?></td>
                            <td><?= htmlspecialchars($p['nombre_alta']) ?></td>
                            <td><?= htmlspecialchars($p['RFC']) ?></td>
                            <td><?= "$" . number_format($p['sueldo_neto'],2,'.', ",") ?></td>
                            <td><?= "$" . number_format($p['sueldo_bruto'],2,'.', ",") ?></td>
                            <td><?= htmlspecialchars($p['puesto']) ?></td>
                            <td><?= htmlspecialchars($p['programa']) ?></td>
                            <?php if ($p['movimiento'] === 'alta'): ?>
                            <td><?= htmlspecialchars($p['observaciones_alta']) ?></td>
                            <?php elseif ($p['movimiento'] === 'baja'): ?>
                            <td><?= htmlspecialchars($p['observaciones_baja']) ?></td>
                            <?php endif; ?>
                            <td class="text-center">
                                <a href="personaldetalles.php?id=<?= $p['id'] ?>" class="btn btn-icon btn-sm btn-light-info me-1" title="Información">
                                    <i class="fas fa-circle-info"></i>
                                </a>
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 1): ?>
                                <a href="?action=delete&id=<?= $p['id'] ?>" class="btn btn-icon btn-sm btn-light-danger" title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar este registro?')">
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
