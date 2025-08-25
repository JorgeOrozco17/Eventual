<?php
require_once 'app/controllers/capturacontroller.php';
include 'header.php';
$controller = new CapturaController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['id'])) {
        // Si hay id: es UPDATE
        $controller->UpdatePension();
    } else {
        // Si NO hay id: es INSERT
        $controller->SavePension();
    }
    exit;
}


if(isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];
    $datos = $controller->getPensionById($id);
} else {
    $datos = [];
}

?>
<style>
    body {
        background-color: #D9D9D9 !important;
    }

</style>

<div class="container-fluid mt-5">
    <div class="regreso">
        <span class="menu-title"><a class="menu-link" href="menu_captura.php">
        <span class="menu-tittle">Captura</span></a> 
        <span class="menu-tittle">/Modulo de pensiones</span></span>
    </div>
    <br>
    <div class="card">
        <div class="card-header">
            <div class="menu-title">
                <h2>Agragar beneficiaria de pension</h2>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <?php $id = $_GET['id'] ?? ''; ?>
                <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
                <input type="hidden" name="id_personal" id="id_personal">

                <!---------------------- Si NO hay id (modo "nuevo registro"), se muestra RFC + CURP. --------------------->    
                <?php if (!$id): ?>
                    <div class="form-group mt-3">
                        <label for="rfc">RFC</label>
                        <input type="text" id="rfc" class="form-control" required>
                        <button type="button" id="validarRFC" class="btn btn-primary mt-2">Validar RFC</button>
                        <small class="form-text text-muted">Haz clic en validar para buscar los datos del empleado.</small>
                    </div>

                    <div class="form-group mt-3">
                        <label>CURP</label>
                        <input type="text" class="form-control" name="curp" id="curp" readonly>
                    </div>
                

                <!---------------------------------- fin del foreach ------------------------------------------------------->


                <div class="form-group mt-3">
                    <label>Nombre</label>
                    <input type="text" class="form-control" name="nombre" id="nombre"  value="<?= htmlspecialchars($datos['nombre'] ?? '') ?>" required readonly>
                </div>

                <div class="form-group mt-3">
                    <label>Jurisdicción</label>
                    <input type="text" class="form-control" name="jurisdiccion" id="jurisdiccion" value="<?= htmlspecialchars($datos['jurisdiccion'] ?? '') ?>" required readonly>
                </div>

                <div class="form-group mt-3">
                    <label>Adscripción</label>
                    <input type="text" class="form-control" name="adscripcion" id="centro" value="<?= htmlspecialchars($datos['centro'] ?? '') ?>" required readonly>
                </div>

                <?php endif; ?>

                <div class="form-group mt-3">
                    <label for="dias">Beneficiaria</label>
                    <input type="text" name="beneficiaria" id="beneficiaria" value="<?= htmlspecialchars($datos['beneficiaria'] ?? '') ?>" class="form-control" required>
                </div>

                <div class="form-group mt-3">
                    <label for="observaciones">Cuenta de la Beneficiaria</label>
                    <input type="number" name="cuenta_beneficiaria" id="cuenta_beneficiaria" value="<?= htmlspecialchars($datos['cuenta_beneficiaria'] ?? '') ?>" class="form-control">
                </div>

                <div class="form-group mt-3">
                    <label for="quincena">Porcentaje de descuento</label>
                    <input type="number" name="porcentaje" id="porcentaje" value=" " placeholder="Ingrese el porcentaje en numeros enteros (De 1% a 100%) No es necesario colocar el simbolo %. Ejemplo: 10"  class="form-control" required>
                </div>

                <input type="hidden" name="id_usuario" value="<?= $_SESSION['user_id'] ?>">

                <div class="form-group mt-4">
                    <button type="submit" name="guardar" class="btn btn-success">Guardar</button>
                </div>
            </form>
        </div>
    </div>
    </div>


<?php
include 'footer.php';
?>  