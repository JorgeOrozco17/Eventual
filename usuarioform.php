<?php
require_once 'app/controllers/UserController.php';
include 'header.php';
$controller = new UserController();

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $controller->save();
    exit;
}

$id = $_GET['id'] ?? '';
$usuario = [
    'id' => '',
    'Nombre' => '',
    'usuario' => '',
    'rol' => '',
    'archivo' => ''
];

if ($id) {
    $usuarioEncontrado = $controller->edit($id);
    if ($usuarioEncontrado) {
        $usuario = $usuarioEncontrado;
    } else {
        echo "<div class='alert alert-danger'>Usuario no encontrado.</div>";
    }
}
?>
<style>
    body {
        background-color: #D9D9D9 !important;
    }
    h2 {
        color: #333333;
    }
    .card-custom {
        border-radius: 1.2rem;
        box-shadow: 0 4px 18px 0 rgba(0,0,0,0.08), 0 1.5px 3px 0 rgba(0,0,0,0.10);
        border: none;
    }
    .card-header-custom {
        background: linear-gradient(90deg, #9c25ebff, #9a06d4ff);
        color: #fff;
        border-radius: 1.2rem 1.2rem 0 0;
        padding: 2rem 2rem 1rem 2rem;
        border-bottom: none;
    }
    .form-label {
        font-weight: 500;
        color: #0e2237;
    }
    .btn-success {
        border-radius: 2rem;
        padding-left: 2rem;
        padding-right: 2rem;
    }
    .btn-secondary {
        border-radius: 2rem;
        padding-left: 2rem;
        padding-right: 2rem;
    }
    .mb-4 {
        margin-bottom: 1.7rem !important;
    }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card card-custom">
                <div class="card-header d-flex align-items-center">
                    <i class="fas fa-user-plus me-3 fa-2x"></i>
                    <h2 class="mb-0"><?= $id ? 'Editar Usuario' : 'Nuevo Usuario' ?></h2>
                </div>
                <div class="card-body px-5 py-4">
                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($usuario['id']) ?>">

                        <div class="mb-4">
                            <label class="form-label">Nombre:</label>
                            <input type="text" name="Nombre" class="form-control" value="<?= htmlspecialchars($usuario['Nombre']) ?>" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Usuario:</label>
                            <input type="text" name="usuario" class="form-control" value="<?= htmlspecialchars($usuario['usuario']) ?>" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Rol:</label>
                            <select name="rol" class="form-select" required>
                                <option value="">Seleccione un rol</option>
                                <option value="1" <?= $usuario['rol'] == 'admin' ? 'selected' : '' ?>>Administrador</option>
                                <option value="2" <?= $usuario['rol'] == 'supervisor' ? 'selected' : '' ?>>Supervisor</option>
                                <option value="3" <?= $usuario['rol'] == 'reclutamiento' ? 'selected' : '' ?>>Reclutamiento</option>
                                <option value="4" <?= $usuario['rol'] == 'sistematizacion' ? 'selected' : '' ?>>Sistematización</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Contraseña:</label>
                            <input type="password" name="contraseña" class="form-control" <?= $id ? '' : 'required' ?>>
                            <?php if ($id): ?>
                                <small class="text-muted">Dejar en blanco si no deseas cambiar la contraseña.</small>
                            <?php endif; ?>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Archivo:</label>
                            <input type="file" name="archivo" class="form-control">
                            <?php if (!empty($usuario['archivo'])): ?>
                                <small class="text-muted">Archivo actual: <?= htmlspecialchars($usuario['archivo']) ?></small>
                                <input type="hidden" name="archivo_actual" value="<?= htmlspecialchars($usuario['archivo']) ?>">
                            <?php endif; ?>
                        </div>

                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-success flex-fill">
                                <i class="fas fa-save me-2"></i>Guardar
                            </button>
                            <a href="nuevousuario.php" class="btn btn-secondary flex-fill">
                                <i class="fas fa-arrow-left me-2"></i>Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
