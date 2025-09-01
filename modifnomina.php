<?php
require_once 'app/controllers/catalogocontroller.php';
require_once 'app/controllers/capturacontroller.php';
include 'header.php';
$controller = new Capturacontroller();
$catalogo = new CatalogoModel(); 
$puestos = $catalogo->getAllPuestos();
$recursos = $catalogo->getAllRecursos();
$adscripciones = $catalogo->getAllJurisdicciones();
$quincena = $catalogo->getAllQuincenas();

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $controller->save();
    exit;
}

$id = $_GET['id'] ?? '';

$personal = null;

if (isset($_GET['id'])){
   $personal = $controller->getNominaById($id);
}
?>

<style>
    body {
        background-color: #D9D9D9 !important;
    }
    h2 {
        color: #333333;
    }
    h3 {
        color: #333333;
    }
    p {
        font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
        font-size: medium;
        color: rgb(0, 0, 0);
    }
    input[type="date"] {
    background-color:rgb(73, 73, 73);
    color: #D9D9D9;
    border: 1px solid #ccc;
    }

    label {
        color:#333333
    }
</style>

<div class="container mt-5">
    <div style="margin-bottom: 10px;">
        <button class="btn btn-sm btn-info me-2" onclick="history.back()"><i class="fas fa-arrow-left-long"></i>Regresar</button>
    </div>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= htmlspecialchars( ($id)) ?>">

        <div class="row g-3">
            <div class="card mb-4 p-3 shadow-md">
                <div class="card-header">
                    <div class="menu-title">
                        <h2 class="mb-4"><?= $id ? 'Editar Registro de Personal' : 'Nuevo Registro de Personal' ?></h2>
                    </div>
                </div>
                <div class="card-body row g-3">
                    <div class="col-md-6">
                        <label>Nomina:</label>
                        <p class="form-control-plaintext">EVE</p>
                    </div>

                    <div class="col-md-6">
                        <label>RFC</label>
                        <p class="form-control-plaintext"><?= htmlspecialchars($personal['RFC'] ?? '') ?></p>
                    </div>

                    <div class="col-md-6">
                        <label>CURP:</label>
                        <p class="form-control-plaintext"><?= htmlspecialchars($personal['CURP'] ?? '') ?></p>
                    </div>

                    <div class="col-md-6">
                        <label>Nombre:</label>
                        <p class="form-control-plaintext"><?= htmlspecialchars($personal['NOMBRE'] ?? '') ?></p>
                    </div>
                </div>
            </div>

            <div class="card mb-4 p-3">
                <div class="row g-3">
                    <div class="menu-title">
                        <h3 class="mb-3">Deducciones</h3>
                    </div>
                    <div class="col-md-6">
                        <label>D01: (ISR)</label>
                        <input type="number" step="0.01" name="D_01" class="form-control" value="<?= htmlspecialchars($personal['D_01'] ?? '0.0') ?>" readonly>
                    </div>

                    <div class="col-md-6">
                        <label>D04: Anticipo de viaticos</label>
                        <input type="number" step="0.01" name="D_04" class="form-control" value="<?= htmlspecialchars($personal['D_04'] ?? '0.0') ?>" >
                    </div>

                    <div class="col-md-6">
                        <label>D05: Faltas</label>
                        <input type="number" step="0.01" name="D_05" class="form-control" value="<?= htmlspecialchars($personal['D_05'] ?? '0.0') ?>" >
                    </div>

                    <div class="col-md-6">
                        <label>D62: Pension Alimenticia</label>
                        <input type="number" step="0.01" name="D_62" class="form-control" value="<?= htmlspecialchars($personal['D_62'] ?? '0.0') ?>" >
                    </div>

                    <div class="col-md-6">
                        <label>D64: Amortizacion Fovissste</label>
                        <input type="number" step="0.01" name="D_64" class="form-control" value="<?= htmlspecialchars($personal['D_64'] ?? '0.0') ?>" >
                    </div>

                    <div class="col-md-6">
                        <label>D65: Seguro de daños Fovissste</label>
                        <input type="number" step="0.01" name="D_65" class="form-control" value="<?= htmlspecialchars($personal['D_65'] ?? '0.0') ?>" >
                    </div>

                    <div class="col-md-6">
                        <label>DR1: Retro Seguro de salud</label>
                        <input type="number" step="0.01" name="D_R1" class="form-control" value="<?= htmlspecialchars($personal['D_R1'] ?? '0.0') ?>" >
                    </div>

                    <div class="col-md-6">
                        <label>DR2: invalidez y vida</label>
                        <input type="number" step="0.01" name="D_R2" class="form-control" value="<?= htmlspecialchars($personal['D_R2'] ?? '0.0') ?>" >
                    </div>

                    <div class="col-md-6">
                        <label>DR3: Servicios Sociales y culturales</label>
                        <input type="number" step="0.01" name="D_R3" class="form-control" value="<?= htmlspecialchars($personal['D_R3'] ?? '0.0') ?>" >
                    </div>

                    <div class="col-md-6">
                        <label>D_AS: Prestamo</label>
                        <input type="number" step="0.01" name="D_AS" class="form-control" value="<?= htmlspecialchars($personal['D_AS'] ?? '0.0') ?>" >
                    </div>

                    <div class="col-md-6">
                        <label>D_AM: </label>
                        <input type="number" step="0.01" name="D_AM" class="form-control" value="<?= htmlspecialchars($personal['D_AM'] ?? '0.0') ?>" >
                    </div>

                    <div class="col-md-6">
                        <label>D_S1: Responsabilidades</label>
                        <input type="number" step="0.01" name="D_S1" class="form-control" value="<?= htmlspecialchars($personal['D_S1'] ?? '0.0') ?>" >
                    </div>

                    <div class="col-md-6">
                        <label>D_S2: Responsabilidades</label>
                        <input type="number" step="0.01" name="D_S2" class="form-control" value="<?= htmlspecialchars($personal['D_S2'] ?? '0.0') ?>" readonly>
                    </div>

                    <div class="col-md-6">
                        <label>D_S4: Invalidez y Vida</label>
                        <input type="number" step="0.01" name="D_S4" class="form-control" value="<?= htmlspecialchars($personal['D_S4'] ?? '0.0') ?>" readonly>
                    </div>

                    <div class="col-md-6">
                        <label>D_S5: Servicios sociales y culturales</label>
                        <input type="number" step="0.01" name="D_S5" class="form-control" value="<?= htmlspecialchars($personal['D_S5'] ?? '0.0') ?>" readonly>
                    </div>

                    <div class="col-md-6">
                        <label>D_S6: Cesantia y vejez</label>
                        <input type="number" step="0.01" name="D_S6" class="form-control" value="<?= htmlspecialchars($personal['D_S6'] ?? '0.0') ?>" readonly>
                    </div>

                    <div class="col-md-6">
                        <label>D_O1: Anticipo de Sueldo</label>
                        <input type="number" step="0.01" name="D_O1" class="form-control" value="<?= htmlspecialchars($personal['D_O1'] ?? '0.0') ?>" >
                    </div>
                </div>
            </div>

            <div class="card mb-4 p-3">
                <div class="row g-3">
                    <div class="menu-title">
                        <h3>Prestaciones</h3>
                    </div>

                    <div class="col-md-6">
                        <label>P_00: Subsidio para el empleado</label>
                        <input type="number" step="0.01" name="P_00" class="form-control" value="<?= htmlspecialchars($personal['P_00'] ?? '0.0') ?>">
                    </div>

                    <div class="col-md-6">
                        <label>P_01: Sueldo</label>
                        <input type="number" step="0.01" name="P_01" class="form-control" value="<?= htmlspecialchars($personal['P_01'] ?? '0.0') ?>" readonly>
                    </div>

                    <div class="col-md-6">
                        <label>P_06: Otras prestaciones</label>
                        <input type="number" step="0.01" name="P_06" class="form-control" value="<?= htmlspecialchars($personal['P_06'] ?? '0.0') ?>">
                    </div>

                    <div class="col-md-6">
                        <label>P_26: Gratificacion Extraordinaria</label>
                        <input type="number" step="0.01" name="P_26" class="form-control" value="<?= htmlspecialchars($personal['P_26'] ?? '0.0') ?>" readonly>
                    </div>
                </div>
            </div>

        </div>

        <br>
        <div class="mt-3">
            <button type="submit" class="btn btn-success">Guardar</button>
            <a href="altapersonal.php" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>
