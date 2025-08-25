<?php
require_once 'app/controllers/catalogocontroller.php';
include 'header.php';

$controller = new CatalogoController();

$id = $_GET['id'] ?? '';
$juris = [
    'id' => '',
    'nombre' => '',
    'ubicacion' => ''
];

// Si hay ID, es edición. Si no, es nuevo.
if ($id) {
    $juris = $controller->getJurisdiccionById($id);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['id'])) {
        $controller->updateJuris();
    } else {
        $controller->saveJuris();
    }
    exit;
}
?>

<style>
    body {
        background-color: #D9D9D9 !important;
    } 
    h2 {
        color: #333333;
    }
</style>

<div class="container mt-5">
    <h2><?= $id ? 'Editar Jurisdicción' : 'Nueva Jurisdicción' ?></h2>

    <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= htmlspecialchars($juris['id']) ?>">

        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" class="form-control" name="nombre" id="nombre" required value="<?= htmlspecialchars($juris['nombre']) ?>">
        </div>

        <div class="form-group">
            <label for="ubicacion">Ubicación:</label>
            <input type="text" class="form-control" name="ubicacion" id="ubicacion" required value="<?= htmlspecialchars($juris['ubicacion']) ?>">
        </div>

        <button type="submit" class="btn btn-primary mt-3">
            <?= $id ? 'Actualizar' : 'Guardar' ?>
        </button>
        <a href="juris.php" class="btn btn-danger mt-3"> Cancelar</a>
    </form>
</div>
