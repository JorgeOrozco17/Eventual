<?php 
require_once 'app/controllers/personalcontroller.php';
include 'header.php';

$controller = new PersonalController();

$id = $_GET['id'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->completeEmployee($id);
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
    body {
        background-color: #E9ECEF !important;
    }

    .card {
        border-radius: 12px;
        border: 1px solid #dee2e6;
    }

    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        padding: 1.5rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-header h2 {
        font-size: 1.3rem;
        font-weight: 600;
        margin: 0;
        color: #212529;
    }

    .employee-name {
        font-size: 1.05rem;
        color: #212529;
        font-weight: 500;
    }

    .form-label {
        font-weight: 500;
        color: #333;
        font-size: 1rem;
    }

    .form-control {
        border-radius: 8px;
        border: 1px solid #ced4da;
        font-size: 1rem;
        padding: 0.65rem 0.8rem;
        height: 46px;
    }

    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.15rem rgba(13,110,253,0.25);
    }

    .btn {
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 500;
        padding: 0.6rem 1.2rem;
    }

    .btn-success {
        background-color: #198754;
        border: none;
    }

    .btn-success:hover {
        background-color: #157347;
    }

    .btn-danger {
        background-color: #dc3545;
        border: none;
    }

    .btn-danger:hover {
        background-color: #bb2d3b;
    }

    .btn-info {
        background-color: #0d6efd;
        border: none;
    }

    .btn-info:hover {
        background-color: #0b5ed7;
    }

    .container {
        max-width: 1050px;
    }

    .card-body {
        padding: 2rem 2rem 2.5rem 2rem;
    }

    .mb-3 button {
        font-size: 0.95rem;
        padding: 0.5rem 1rem;
    }
</style>


<div class="container mt-5">
    <div class="mb-3">
        <button class="btn btn-sm btn-info" onclick="history.back()">
            <i class="bi bi-arrow-left"></i> Regresar
        </button>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h2>
                <?= $id ? 'Editar datos de personal' : 'Nuevo registro de personal' ?>
            </h2>
            <?php if ($personal): ?>
                <div class="employee-name">
                    <i class="bi bi-person-circle me-2"></i>
                    <?= htmlspecialchars($personal['nombre_alta'] ?? '') ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="card-body p-4">
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
                            
                            <?php
                            $estados = ['SOLTERO', 'CASADO', 'DIVORCIADO', 'VIUDO', 'UNION_LIBRE'];
                            foreach ($estados as $estado) {
                                $sel = (old('estado_civil', $personal) == $estado) ? 'selected' : ' Selecciona estado civil';
                                echo "<option value='$estado' $sel>$estado</option>";
                            }
                            ?>
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
                        <label class="form-label">Calle y Numero <span class="text-danger">*</span></label>
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
                    <button type="submit" class="btn btn-success" onclick="return confirm('¿Desea guardar los cambios?')">
                        <i class="bi bi-check2"></i> Guardar
                    </button>
                    <button onclick="history.back(); return false;" class="btn btn-danger ms-2">
                        <i class="bi bi-x"></i> Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
