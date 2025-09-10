
<?php
require_once 'app/controllers/personalController.php';
require_once 'app/controllers/catalogocontroller.php';
include 'header.php';
$controller = new PersonalController();
$catalogo = new CatalogoController(); 
$puestos = $catalogo->getAllPuestos();
$recursos = $catalogo->getAllRecursos();
$adscripciones = $catalogo->getAllJurisdicciones();
$quincena = $catalogo->getAllQuincenas();

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $controller->altaxbaja();
    exit;
}

$id = $_GET['id'] ?? '';

$personal = null;

if (isset($_GET['id'])){
   $personal = $controller->getPersonalById($id);
}
?>

<style>
    body {
        background-color: #D9D9D9 !important;
    }
    h2 {
        color: #333333;
    }
    input[type="date"] {
    background-color:rgb(233, 231, 233);
    color: #0e0e0e;
    border: 1px solid #ccc;
    }
</style>

<div class="container mt-5">

    <div style="margin: 10px;">
        <button class="btn btn-sm btn-info me-2" onclick="history.back()"><i class="fas fa-arrow-left-long"></i>Regresar</button>
    </div>
    
    <div class="card">
        <div class="card-header">
            <div class="menu-title">
                <h2>Registro de alta por baja</h2>
            </div>
        </div>

        <div class="card-body">
            <form method="POST" action="procesar_alta_por_baja.php" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

                <div class="row g-3">

                    <div class="col-md-5">
                        <label>Solicita</label>
                        <input type="text" name="solicita" class="form-control" value="<?= htmlspecialchars($personal['solicita'] ?? '') ?>" >
                    </div>

                    <div class="col-md-4">
                        <label>Movimiento:</label>
                        <input type="text" name="movimiento_fake" class="form-control" value="ALTA POR BAJA" placeholder="ALTA POR BAJA" readonly>
                    </div>

                    <div class="col-md-6">
                        <label>Oficio:</label>
                        <input type="text" name="oficio" class="form-control" value="<?= htmlspecialchars($personal['oficio'] ?? '') ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label>Puesto:</label>
                        <select name="puesto" class="form-select" required>
                            <?php foreach ($puestos as $p): ?>
                                <option value="<?= $p['nombre_puesto'] ?>" <?= (isset($personal['puesto']) && $personal['puesto'] == $p['nombre_puesto']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($p['nombre_puesto']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Programa:</label>
                        <select name="programa" class="form-select" required>
                            <?php foreach ($recursos as $r): ?>
                                <option value="<?= $r['nombre'] ?>" <?= (isset($personal['programa']) && $personal['programa'] == $r['nombre']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($r['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Rama:</label>
                        <select name="rama" id="" class="form-select" required>
                            <?php foreach (['RAMA ADMINISTRATIVA', 'RAMA MEDICA'] as $rama): ?>
                                <option value="<?= $rama ?>" <?= (isset($personal['rama']) && $personal['rama'] == $rama) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($rama) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Adscripción:</label>
                        <select name="adscripcion" id="adscripcion" class="form-select" required>
                            <?php foreach ($adscripciones as $a): ?>
                                <option value="<?= $a['id'] ?>" <?= (isset($personal['id_adscripcion']) && $personal['id_adscripcion'] == $a['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($a['nombre']) . '-' . htmlspecialchars($a['ubicacion']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Centro:</label>
                        <select name="centro" id="centro" class="form-select" required>
                            <option value="<?= htmlspecialchars($personal['centro'] ?? '') ?>">
                                <?= htmlspecialchars($personal['centro'] ?? 'Seleccione un centro') ?>
                            </option>
                        </select>
                    </div> 

                    <!-- Sueldo Bruto -->
                    <div class="col-md-6">
                        <label>Sueldo Bruto Mensual:</label>
                        <input type="number" step="0.01" name="sueldo_bruto" class="form-control" value="<?= htmlspecialchars($personal['sueldo_bruto'] ?? '') ?>">
                    </div>
 <!------------------------------------------------------------ datos de empleado actual --------------------------------------------->
                    <div class="card-header"></div>
                    <br>
                    <h2 for="" class="menu-title">Datos del empleado Actual</h2>

                    <div class="col-md-10">
                        <label>Nombre empleado actual:</label>
                        <input type="text" name="nombre_actual" class="form-control" value="<?= htmlspecialchars($personal['nombre_alta'] ?? '') ?>" readonly>
                    </div>

                    <div class="col-md-6">
                        <label>RFC:</label>
                        <input type="text" name="RFC_actual" class="form-control" value="<?= htmlspecialchars($personal['RFC'] ?? '') ?>" required readonly>
                    </div>

                    <div class="col-md-6">
                        <label>CURP:</label>
                        <input type="text" name="CURP_actual" class="form-control" value="<?= htmlspecialchars($personal['CURP'] ?? '') ?>" required readonly>
                    </div>

                        <div class="col-md-6">
                            <label>Quincena baja:</label>
                            <select name="quincena_baja" class="form-select" required>
                                <option value="">Seleccione una quincena</option>
                                <?php foreach ($quincena as $q): ?>
                                    <option value="<?= $q['nombre'] ?>" <?= (isset($personal['quincena_baja']) && $personal['quincena_baja'] == $q['nombre']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($q['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label>Fecha de Baja:</label>
                            <input type="date" name="fecha_baja" class="form-control" value="<?= htmlspecialchars($personal['fecha_baja'] ?? '') ?>" required>
                        </div>

                        <div class="col-md-12">
                        <label>Observaciones de la Baja:</label>
                        <textarea name="observaciones_baja" class="form-control"><?= htmlspecialchars($personal['observaciones_baja'] ?? '') ?></textarea>
                    </div>
    
    <!------------------------------------ empleado nuevo ------------------------------------->
                    <div class="card-header"></div>
                    <h2 class="menu-title">Datos del empleado Nuevo</h2>

                    <div class="col-md-10">
                            <label>Nombre empleado nuevo:</label>
                            <input type="text" name="nombre_alta" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                            <label>RFC:</label>
                            <input type="text" name="RFC" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                            <label>CURP:</label>
                            <input type="text" name="CURP" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                            <label>Quincena alta:</label>
                            <select name="quincena_alta" class="form-select" required>
                                <option value="">Seleccione una quincena</option>
                                <?php foreach ($quincena as $q): ?>
                                    <option value="<?= $q['nombre'] ?>" <?= (isset($personal['quincena_alta']) && $personal['quincena_alta'] == $q['nombre']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($q['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label>Inicio de Contratación:</label>
                            <input type="date" name="inicio_contratacion" class="form-control" value="">
                        </div>

                    <div class="col-md-6">
                        <label>Cuenta bancaria:</label>
                        <input type="number" name="cuenta" class="form-control">
                    </div>

                    <div class="col-md-12">
                        <label>Observaciones de la Alta:</label>
                        <textarea name="observaciones_alta" class="form-control"><?= htmlspecialchars($personal['observaciones_alta'] ?? '') ?></textarea>
                    </div>


                    <div class="col-md-12">
                        <label>Observaciones del Usuario:</label>
                        <textarea name="observaciones_usuario" class="form-control"><?= htmlspecialchars($personal['observaciones_usuario'] ?? '') ?></textarea>
                    </div>
                </div>

                <br>
                <div class="mt-3">
                    <button type="submit" class="btn btn-success">Guardar</button>
                    <a href="altapersonal.php" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
