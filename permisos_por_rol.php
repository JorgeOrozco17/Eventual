<?php
require_once 'app/controllers/usercontroller.php';

$userController = new UserController();

$paginas = $userController->getPaginas();

$roles = $userController->getAllRoles();

// Rol seleccionado
$rol_id = isset($_GET['rol']) ? intval($_GET['rol']) : ($roles[0]['id'] ?? 0);

// Permisos actuales del rol
$permisos = [];
if ($rol_id) {
    $permisos = $userController->getPermisosPorRol($rol_id);
}

// Agrupar páginas por categoría (New: Added category grouping for pages)
$paginas_por_categoria = [];
foreach ($paginas as $pagina) {
    // Assuming 'categoria' key exists in your $pagina array.
    // If not, all pages will fall under 'Sin categoría'.
    $categoria = $pagina['categoria'] ?? 'Sin categoría'; // Use null coalescing for safety
    $paginas_por_categoria[$categoria][] = $pagina;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rol_id'])) {
    $rol_id = intval($_GET['rol']);
    $paginas_seleccionadas = isset($_POST['paginas']) ? $_POST['paginas'] : [];
    $userController->guardarPermisosPorRol($rol_id, $paginas_seleccionadas);
    header("Location: permisos_por_rol.php");
    exit;
}
?>

<?php include 'header.php'; ?>

<style>
    body {
        background-color: #D9D9D9 !important;
    }
</style>

<div class="regreso">
    <span class="menu-tittle"><a class="menu-link" href="permisos.php">
    <span class="menu-tittle">Permisos /</span>
    </a>Por Rol</span>
</div>

<div class="d-flex flex-column flex-root">
    <div class="page d-flex flex-row flex-column-fluid">
        <div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
            <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                <div id="kt_content_container" class="container-xxl pt-10">
                    <div class="card card-flush h-lg-auto mb-6 mb-xl-9">
                        <div class="card-header border-0 pt-6">
                            <div class="card-title">
                                <h2 class="d-flex align-items-center">
                                    <i class="fas fa-users-cog fs-3 me-2"></i>Permisos por Rol
                                </h2>
                            </div>
                        </div>
                        <div class="card-body pt-0">
                            <?php if (isset($_GET['success'])): ?>
                                <div class="alert alert-success d-flex align-items-center p-5 mb-10">
                                    <i class="ki-duotone ki-check-circle fs-2hx text-success me-4">
                                        <span class="path1"></span><span class="path2"></span>
                                    </i>
                                    <div class="d-flex flex-column">
                                        <h4 class="mb-1 text-success">¡Éxito!</h4>
                                        <span>Permisos actualizados correctamente.</span>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <form method="get" class="mb-8">
                                <div class="fv-row mb-5">
                                    <label for="rol" class="form-label fs-6 fw-bold mb-3">Selecciona un Rol:</label>
                                    <select name="rol" id="rol" class="form-select form-select-solid" data-control="select2" data-hide-search="true" onchange="this.form.submit()">
                                        <?php foreach ($roles as $rol): ?>
                                            <option value="<?= $rol['id'] ?>" <?= $rol['id'] == $rol_id ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($rol['nombre']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </form>

                            <form method="post">
                                <input type="hidden" name="rol_id" value="<?= $rol_id ?>">
                                <div class="mb-8">
                                    <h4 class="mb-5 text-gray-700">Páginas disponibles:</h4>
                                    <?php foreach ($paginas_por_categoria as $categoria => $paginas_cat): ?>
                                        <div class="card card-flush shadow-6 mb-5">
                                            <div class="card-header min-h-auto ps-4">
                                                <h5 class="card-title fs-5 fw-bold text-gray-800 me-3">
                                                    <?= htmlspecialchars($categoria) ?>
                                                </h5>
                                                <div class="form-check form-check-custom form-check-solid">
                                                    <input class="form-check-input select-all-category" type="checkbox"
                                                        id="select_all_<?= str_replace(' ', '_', $categoria) ?>">
                                                    <label class="form-check-label fw-semibold text-muted" for="select_all_<?= str_replace(' ', '_', $categoria) ?>">
                                                        Seleccionar todo
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="card-body py-3 px-4">
                                                <div class="row g-5 g-xl-8">
                                                    <?php foreach ($paginas_cat as $pagina): ?>
                                                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                                            <div class="form-check form-check-custom form-check-solid">
                                                                <input class="form-check-input page-checkbox category-<?= str_replace(' ', '_', $categoria) ?>" type="checkbox"
                                                                    name="paginas[]"
                                                                    value="<?= $pagina['id'] ?>"
                                                                    id="pag<?= $pagina['id'] ?>"
                                                                    <?= in_array($pagina['id'], $permisos) ? 'checked' : '' ?>>
                                                                <label class="form-check-label fw-semibold text-gray-700" for="pag<?= $pagina['id'] ?>">
                                                                    <?= htmlspecialchars($pagina['nombre']) ?>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <button type="submit" class="btn btn-primary d-flex align-items-center">
                                    <i class="ki-duotone ki-plus fs-2 me-2"></i>Guardar Permisos
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Obtener todos los checkboxes de "seleccionar todo" de cada categoría
        const selectAllCheckboxes = document.querySelectorAll('.select-all-category');

        selectAllCheckboxes.forEach(function(selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                // Obtener el ID de la categoría del ID del checkbox "seleccionar todo"
                const categoryId = this.id.replace('select_all_', 'category-');
                // Obtener todos los checkboxes de página de esta categoría
                const pageCheckboxes = document.querySelectorAll('.' + categoryId);

                pageCheckboxes.forEach(function(checkbox) {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            });
        });

        // Opcional: Si un usuario deselecciona una página individual, el "seleccionar todo" de la categoría se desmarca
        const pageCheckboxes = document.querySelectorAll('.page-checkbox');
        pageCheckboxes.forEach(function(pageCheckbox) {
            pageCheckbox.addEventListener('change', function() {
                // Extraer la clase de categoría del checkbox de página
                const categoryClass = Array.from(this.classList).find(cls => cls.startsWith('category-'));
                if (categoryClass) {
                    const categoryCheckboxes = document.querySelectorAll('.' + categoryClass);
                    const allChecked = Array.from(categoryCheckboxes).every(cb => cb.checked);
                    const selectAllCategoryCheckbox = document.getElementById('select_all_' + categoryClass.replace('category-', ''));
                    if (selectAllCategoryCheckbox) {
                        selectAllCategoryCheckbox.checked = allChecked;
                    }
                }
            });
        });

        // Inicializar el estado de los checkboxes "seleccionar todo" al cargar la página
        selectAllCheckboxes.forEach(function(selectAllCheckbox) {
            const categoryId = selectAllCheckbox.id.replace('select_all_', 'category-');
            const pageCheckboxes = document.querySelectorAll('.' + categoryId);
            const allChecked = Array.from(pageCheckboxes).every(cb => cb.checked);
            selectAllCheckbox.checked = allChecked;
        });
    });
</script>