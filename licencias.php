<?php
require_once 'app/controllers/faltas_licenciascontroller.php';
include 'header.php';
$controller = new Faltas_licenciascontroller();

// Filtros
$quincena = $_GET['quincena'] ?? 'all';
$anio = $_GET['anio'] ?? date('Y'); 

if ($quincena === 'all') {
    $licencias = $controller->getAllLicencias();
} else {
    $licencias = $controller->getLicenciasByQuincena($quincena, $anio);
}

?>
<style>
    body { background-color: #D9D9D9 !important; }
    h2 { color: #333333 }
</style>
<div class="container-fluid mt-5">
    <div class="regreso">
        <span class="menu-title"><a class="menu-link" href="licencias.php">
        <span class="menu-tittle">Licencias de personal</span></a></span>
    </div>
    <br>
    <div class="card">
        <div class="card-header">
            <div class="menu-title">
                <h2>Registro de licencias del personal</h2>
            </div>
            <div class="card-toolbar">
                <a href="licenciaform.php?tipo=licencia" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Registro
                </a>
                <a href="generar_reporte_licencias.php?quincena=<?= urlencode($_GET['quincena'] ?? 'all') ?>&anio=<?= urlencode($_GET['anio'] ?? date('Y')) ?>" target="_blank" class="btn btn-outline-secondary ms-2">
                    <i class="fas fa-print"></i> Imprimir reporte
                </a>
            </div>
        </div>

        <!-- Filtros -->
        <form method="get" class="row g-2 align-items-center mb-3">
            <div class="col-auto">
                <select name="quincena" class="form-select">
                    <option value="all" <?= ($quincena === 'all') ? 'selected' : '' ?>>Todas las quincenas</option>
                    <?php
                    for ($q = 1; $q <= 24; $q++) {
                        $selected = ($quincena == $q) ? 'selected' : '';
                        echo "<option value=\"$q\" $selected>Quincena $q</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-auto">
                <input type="number" name="anio" class="form-control" placeholder="Año" value="<?= htmlspecialchars($anio) ?>" min="2000" max="2100" required>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-secondary">Filtrar</button>
                <a href="licencias.php" class="btn btn-outline-secondary">Limpiar</a>
            </div>
        </form>

        <?php if (isset($_GET['duplicate'])): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                El RFC o CURP ya existe. Por favor, verifica los datos.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Registro creado correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                Ocurrió un error al guardar el registro. Intenta nuevamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card-body">
            <div class="table-responsive">
                <table id="tablaComun" class="table table-hover gy-5 dataTable">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre</th>
                            <th>Adscripción</th>
                            <th>Jurisdicción</th>
                            <th>Días</th>
                            <th>Quincena</th>
                            <th>Periodo</th>
                            <th>Observaciones</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($licencias as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['nombre']) ?></td>
                            <td><?= htmlspecialchars($p['adscripcion']) ?></td>
                            <td><?= htmlspecialchars($p['jurisdiccion']) ?></td>
                            <td><?= htmlspecialchars($p['dias']) ?></td>
                            <td><?= htmlspecialchars($p['quincena']) ?></td>
                            <td>
                                <?php
                                    $ini = !empty($p['periodo_1']) ? date('d/m/Y', strtotime($p['periodo_1'])) : '';
                                    $fin = !empty($p['periodo_2']) ? date('d/m/Y', strtotime($p['periodo_2'])) : '';
                                    echo "$ini al $fin";
                                ?>
                            </td>
                            <td><?= htmlspecialchars($p['observaciones']) ?></td>
                            <td nowrap>
                                <a href="licenciaform.php?id=<?= $p['id'] ?>&tipo=licencia" class="btn btn-icon btn-sm me-2" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="?action=delete&id=<?= $p['id'] ?>&tipo=licencia" class="btn btn-icon btn-sm btn-danger" title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar este registro?')">
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
