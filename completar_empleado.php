<?php 
require_once 'app/controllers/personalcontroller.php';
include 'header.php';

$controller = new PersonalController();

$id = $_GET['id'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->completeEmployee($id); // Asegúrate de recibir los datos correctos en el controlador
    exit;
}

$personal = null;
if ($id){
   $personal = $controller->getPersonalById($id);
}

// Helper para valores antiguos
function old($key, $personal) {
    return htmlspecialchars($personal[$key] ?? '');
}
?>
<style>
    body { background-color: #D9D9D9 !important; }
    .form-label { font-weight: 500; }
</style>

<div class="container mt-5">
    <div style="margin: 10px;">
        <button class="btn btn-sm btn-info me-2" onclick="history.back()" > <i class="fas fa-arrow-left-long"></i>Regresar</button>
    </div>
    
    <div class="card shadow">
        <div class="card-header text-white">
            <div class="menu-title">
                <h2 class="mb-0"><?= $id ? 'Editar datos de personal' : 'Nuevo registro de personal' ?></h2>
            </div>
        </div>

        <div class="card-body">
            <form method="POST" autocomplete="off">
                <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Nacionalidad <span class="text-danger">*</span></label>
                        <input type="text" name="nacionalidad" class="form-control" required value="<?= old('nacionalidad', $personal) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Estado civil <span class="text-danger">*</span></label>
                        <select name="estado_civil" class="form-control" required>
                            <option value="">Selecciona</option>
                            <option value="SOLTERO" <?= (old('estado_civil', $personal) == 'SOLTERO') ? 'selected' : '' ?>>Soltero</option>
                            <option value="CASADO" <?= (old('estado_civil', $personal) == 'CASADO') ? 'selected' : '' ?>>Casado</option>
                            <option value="DIVORCIADO" <?= (old('estado_civil', $personal) == 'DIVORCIADO') ? 'selected' : '' ?>>Divorciado</option>
                            <option value="VIUDO" <?= (old('estado_civil', $personal) == 'VIUDO') ? 'selected' : '' ?>>Viudo</option>
                            <option value="UNION_LIBRE" <?= (old('estado_civil', $personal) == 'UNION_LIBRE') ? 'selected' : '' ?>>Unión Libre</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Profesión <span class="text-danger">*</span></label>
                        <input type="text" name="profesion" class="form-control" required value="<?= old('profesion', $personal) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Originario de <span class="text-danger">*</span></label>
                        <input type="text" name="originario" class="form-control" required value="<?= old('originario', $personal) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Calle <span class="text-danger">*</span></label>
                        <input type="text" name="calle" class="form-control" required value="<?= old('calle', $personal) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Colonia <span class="text-danger">*</span></label>
                        <input type="text" name="colonia" class="form-control" required value="<?= old('colonia', $personal) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Ciudad <span class="text-danger">*</span></label>
                        <input type="text" name="ciudad" class="form-control" required value="<?= old('ciudad', $personal) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Estado <span class="text-danger">*</span></label>
                        <input type="text" name="estado" class="form-control" required value="<?= old('estado', $personal) ?>">
                    </div>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-success" onclick="return confirm('¿Desea guardar los cambios?')">Guardar</button>
                    <button onclick="history.back(); return false;" class="btn btn-secondary ms-2">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
include 'footer.php';
?>
