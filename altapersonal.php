<?php
require_once 'app/controllers/PersonalController.php';
include 'app/controllers/catalogocontroller.php';

include 'header.php';
$catalogo = new CatalogoController();
$controller = new PersonalController();     

$responsable = $_SESSION['juris'];
$user_rol = $_SESSION['role'];
$jurisdicciones = $catalogo->getAllJurisdicciones();

$jurisdiccion_inicial = 'J9';
$filtro = $_GET['jurisdiccion'] ?? $jurisdiccion_inicial;

// 👇 Carga condicional según filtro
if ($filtro === 'todas') {
    $personal = $controller->getAutorizados(10); // usa 10 si es el código general o modifica según tu lógica
} else {
    $personal = $controller->getAutorizados($filtro);
}

if ($user_rol == 1 || $user_rol == 2) {
    $mostrar_filtro = true;
} else {
    $mostrar_filtro = false;
}
?>
<style>
    body {
        background-color: #D9D9D9 !important;
    }
    h2 {
        color: #333333
    }
    .filtro-contratos { min-width: 200px; border-radius: 10px; }
    .card-filtros {
        border-left: 4px solid #0d6efd;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.5rem;
    }
    .card-filtros:hover {
        box-shadow: 0 3px 12px rgba(0,0,0,0.08);
    }
    .filtro-label i {
        font-size: 1rem;
        color: #0d6efd;
        margin-right: 4px;
    }
</style>


<div class="container-fluid mt-5">
    <div class="regreso">
        <span class="menu-title"><a class="menu-link" href="personal.php">
        <span class="menu-tittle">Personal</span></a> <span class="menu-tittle">/Gestionar personal</span></span>
    </div>
    <br>
    <div style="margin-bottom: 10px;">
        <button class="btn btn-sm btn-info me-2" onclick="history.back()"><i class="fas fa-arrow-left-long"></i>Regresar</button>
    </div>

    <div class="card shadow-sm border-0">
        <!-- Card Header estilo Metronic -->
        <div class="card-header d-flex justify-content-between align-items-center" style="border-top-left-radius:.5rem;border-top-right-radius:.5rem;">
            <h2 class="mb-0"><i class="fas fa-users me-2"></i>Administración de Personal</h2>
            <div>
                <?php if ($mostrar_filtro): ?>
                <div class="filtro-group">
                    <label for="filtro-jurisdiccion" class="form-label filtro-label mb-1 fw-semibold text-secondary">
                        <i class="bi bi-geo-alt-fill"></i> Jurisdicción
                    </label>
                    <select id="filtro-jurisdiccion" class="form-select filtro-contratos">
                        <option value="todas" <?= ($filtro === 'todas') ? 'selected' : '' ?>>Todas</option>
                        <?php foreach($jurisdicciones as $jurisdiccion): ?>
                            <option value="<?= htmlspecialchars($jurisdiccion['nombre']) ?>"
                                <?= ($filtro === $jurisdiccion['nombre']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($jurisdiccion['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
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

<script>
document.getElementById('filtro-jurisdiccion').addEventListener('change', function() {
    const jurisdiccion = this.value;
    const url = new URL(window.location.href);
    url.searchParams.set('jurisdiccion', jurisdiccion);
    window.location.href = url; // recarga la página con el nuevo filtro
});
</script>

<?php include 'footer.php'; ?>
