<?php
require_once 'app/controllers/capturacontroller.php';
require_once 'app/controllers/deduccioncontroller.php';
include 'header.php';
$controller = new Deduccioncontroller();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['id'])) {
        // Si hay id: es UPDATE
        $controller->UpdateTemporal();
    } else {
        // Si NO hay id: es INSERT
        $controller->SaveTemporal();
    }
    exit;
}


if(isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];
    $datos = $controller->getTemporalesbyId($id);
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
        <span class="menu-tittle">/Deducciones</span></span>
    </div>
    <br>
    <div class="card">
        <div class="card-header">
            <div class="menu-title">
                <h2>Agragar deduccion temporal</h2>
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
                        <input type="hidden" class="form-control" name="curp" id="curp" readonly>
                    </div>

                <!---------------------------------- fin del foreach ------------------------------------------------------->
                
                <div class="form-group mt-3">
                    <label>Nombre</label>
                    <input type="text" class="form-control" name="nombre" id="nombre"
                      value="<?= htmlspecialchars($datos['id_personal'] ?? '') ?>" readonly required>
                </div>

                <div class="form-group mt-3">
                    <input type="hidden" class="form-control" name="jurisdiccion" id="jurisdiccion" value="<?= htmlspecialchars($datos['jurisdiccion'] ?? '') ?>" required readonly>
                </div>

                <div class="form-group mt-3">
                    <label>Adscripción</label>
                    <input type="text" class="form-control" name="adscripcion" id="centro" value="<?= htmlspecialchars($datos['centro'] ?? '') ?>" required readonly>
                </div>

                <?php endif; ?>

                <?php foreach ($datos as $row): ?>
                    <strong><label for="namelabel"><?= htmlspecialchars($row['empleado']) ?></label></strong>
                <?php endforeach; ?>

                <div class="form-group mt-3">
                    <label for="concepto">Concepto</label>
                    <select name="concepto" id="concepto" class="form-control" required>
                        <option value="">Seleccione un concepto</option>
                        <option value="D_04"
                            <?= (isset($datos['concepto']) && $datos['concepto'] == 'D_04') ? 'selected' : '' ?>>
                            ANTICIPO DE VIATICOS
                        </option>
                        <option value="D_64"
                            <?= (isset($datos['concepto']) && $datos['concepto'] == 'D_64') ? 'selected' : '' ?>>
                            AMORTIZACION FOVISSSTE
                        </option>
                        <option value="D_65"
                            <?= (isset($datos['concepto']) && $datos['concepto'] == 'D_65') ? 'selected' : '' ?>>
                            SEGURO DE DAÑOS FOVISSSTE
                        </option>
                        <option value="D_AS"
                            <?= (isset($datos['concepto']) && $datos['concepto'] == 'D_AS') ? 'selected' : '' ?>>
                            PRESTAMO
                        </option>
                        <option value="D_O1"
                            <?= (isset($datos['concepto']) && $datos['concepto'] == 'D_O1') ? 'selected' : '' ?>>
                            ANTICIPO DE SUELDO
                        </option>
                    </select>
                </div>

                <div class="form-group mt-3">
                    <label for="monto_total">Monto Total</label>
                    <input type="number" step="0.01" name="monto_total" id="monto_total" placeholder="Ingrese el moto total del adeudo (si aplica)" value="<?= htmlspecialchars($datos['monto_total'] ?? '') ?>" class="form-control"></label>
                </div>
 
                <div class="form-group mt-3">
                    <label for="monto">Monto</label>
                    <input type="number" step="0.01" name="monto" id="monto" placeholder="Ingrese el monto que se descontará por quincena" value="<?= htmlspecialchars($datos['monto'] ?? '') ?>" class="form-control">
                </div>
                <br>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="fecha_inicio">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" id="fecha_inicio" value="<?= htmlspecialchars($datos['fecha_inicio'] ?? '') ?>" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label for="fecha_fin">Fecha Fin</label>
                    <input type="date" name="fecha_fin" id="fecha_inicio" value="<?= htmlspecialchars($datos['fecha_inicio'] ?? '') ?>" class="form-control" required>
                </div>

                <input type="hidden" name="id_usuario" value="<?= $_SESSION['user_id'] ?>">

                <div class="form-group mt-4">
                    <button type="submit" name="guardar" class="btn btn-success">Guardar</button>
                </div>
            </div>
            </form>
        </div>
    </div>
    </div>


<?php
include 'footer.php';
?>  