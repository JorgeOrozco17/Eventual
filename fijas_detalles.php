<?php
require_once 'app/controllers/catalogocontroller.php';
include 'header.php';

$controller = new CatalogoController();

$id = $_GET['id'] ?? '';
$fijo = [
    'id' => '',
    'nombre_concepto' => '',
    'concepto' => '',
    'cantidad' => ''
];

// Si hay ID, es edición. Si no, es nuevo.
if ($id) {
    $fijo = $controller->getFijosById($id);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['id'])) {
        $controller->updateFijo();
    } else {
        $controller->saveFijo();
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
    <h2><?= $id ? 'Editar Concepo Fijo' : 'Nueva Concepto Fijo' ?></h2>

    <form method="POST" action="" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= htmlspecialchars($fijo['id']) ?>">

        <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" class="form-control" name="nombre" id="nombre" required value="<?= htmlspecialchars($fijo['nombre_concepto'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="concepto">Concepto:</label>
            <input type="text" class="form-control" name="concepto" id="concepto" required value="<?= htmlspecialchars($fijo['concepto'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="cantidad">Total:</label>
            <input type="text" class="form-control" name="cantidad" id="cantidad" required value="<?= htmlspecialchars($fijo['cantidad'] ?? '') ?>">
        </div>

        <button type="submit" class="btn btn-primary mt-3">
            <?= $id ? 'Actualizar' : 'Guardar' ?>
        </button>
        <a href="juris.php" class="btn btn-danger mt-3"> Cancelar</a>
    </form>
</div>
