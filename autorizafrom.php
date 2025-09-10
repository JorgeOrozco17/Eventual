
<?php
require_once 'app/controllers/personalController.php';
require_once 'app/controllers/catalogocontroller.php';
include 'header.php';
$controller = new PersonalController();
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
        <button class="btn btn-sm btn-info me-2" onclick="history.back()" > <i class="fas fa-arrow-left-long"></i>Regresar</button>
    </div>
    
    <div class="card">
        <div class="card-header">
            <div class="menu-title">
                <h2><?= $id ? 'Editar Registro de Personal' : 'Nuevo Registro de Personal' ?></h2>
            </div>
        </div>

        <div class="card-body">
            <form method="POST" enctype="multipart/form-data" action="procesar_autorizacion.php">
                <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

                <div class="row g-3">
                    
                    <div class="col-md-5">
                        <label>Solicita</label>
                        <input type="text" name="solicita" class="form-control" value="<?= htmlspecialchars($personal['solicita'] ?? '') ?>" readonly>
                    </div>

                    <div class="col-md-4">
                        <label>Movimiento:</label>
                        <select name="movimiento" class="form-select" required disabled>
                            <option value="">Seleccione</option>
                            <option value="alta" <?= (isset($personal['movimiento']) && $personal['movimiento'] == 'alta') ? 'selected' : '' ?>>ALTA</option>
                            <option value="baja" <?= (isset($personal['movimiento']) && $personal['movimiento'] == 'baja') ? 'selected' : '' ?>>BAJA</option>
                        </select>
                    </div>

                    <div>
                        <input type="hidden" name="estatus" id="estatus" readonly>
                    </div>

                    <div class="col-md-6">
                        <label>Oficio:</label>
                        <input type="text" name="oficio" class="form-control" value="<?= htmlspecialchars($personal['oficio'] ?? '') ?>" required readonly>
                    </div>

                    <div class="col-md-6">
                        <label>Puesto:</label>
                        <select name="puesto" class="form-select" required disabled>
                            <option value="">Seleccione un puesto</option>
                            <?php foreach ($puestos as $p): ?>
                                <option value="<?= $p['nombre_puesto'] ?>" <?= (isset($personal['puesto']) && $personal['puesto'] == $p['nombre_puesto']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($p['nombre_puesto']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Programa:</label>
                        <select name="programa" class="form-select" required disabled>
                            <option value="">Seleccione un programa</option>
                            <?php foreach ($recursos as $r): ?>
                                <option value="<?= $r['nombre'] ?>" <?= (isset($personal['programa']) && $personal['programa'] == $r['nombre']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($r['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Adscripción:</label>
                        <select name="adscripcion" id="adscripcion" class="form-select" required disabled>
                            <?php foreach ($adscripciones as $a): ?>
                                <option <?= htmlspecialchars($personal['adscripcion'] ?? '') ?>>
                                    <?= htmlspecialchars($personal['adscripcion'] ?? '') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Centro:</label>
                        <select name="centro" id="centro" class="form-select" required disabled>
                            <option value="<?= htmlspecialchars($personal['centro'] ?? '') ?>">
                                <?= htmlspecialchars($personal['centro'] ?? 'Seleccione un centro') ?>
                            </option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>RFC:</label>
                        <input type="text" name="RFC" class="form-control" value="<?= htmlspecialchars($personal['RFC'] ?? '') ?>" required readonly>
                    </div>

                    <div class="col-md-6">
                        <label>CURP:</label>
                        <input type="text" name="CURP" class="form-control" value="<?= htmlspecialchars($personal['CURP'] ?? '') ?>" required readonly>
                    </div>

                    <div class="col-md-6">
                        <label>Sueldo Neto Mensual:</label>
                        <input type="number" step="0.01" name="sueldo_neto" class="form-control" value="<?= htmlspecialchars($personal['sueldo_neto'] ?? '') ?>" required readonly>
                    </div>

                    <div class="col-md-6">
                        <label>Sueldo Bruto Mensual:</label>
                        <input type="number" step="0.01" name="sueldo_bruto" class="form-control" value="<?= htmlspecialchars($personal['sueldo_bruto'] ?? '') ?>" required readonly>
                    </div>

                    <div class="col-md-6">
                        <label>Nombre Empleado:</label>
                        <input type="text" name="nombre_alta" class="form-control" value="<?= htmlspecialchars($personal['nombre_alta'] ?? '') ?>" readonly>
                    </div>

                    <?php if ($personal && $personal['movimiento'] == 'alta'): ?>
                    <div class="col-md-3">
                        <label>Quincena Alta:</label>
                        <input type="text" name="inicio_contratacion" class="form-control" value="<?= htmlspecialchars($personal['quincena_alta'] ?? '') ?>" required>
                    </div>

                    <div class="col-md-3">
                        <label>Inicio de Contratación:</label>
                        <input type="date" name="inicio_contratacion" class="form-control" value="<?= htmlspecialchars($personal['inicio_contratacion'] ?? '') ?>" required>
                    </div>

                    <?php endif; ?>
                   
                    <?php if ($personal && $personal['movimiento'] == 'baja'): ?>
                    <div class="col-md-3">
                        <label>Quincena baja:</label>
                        <input type="text" name="quincena_baja" class="form-control" value="<?= htmlspecialchars($personal['quincena_baja'] ?? '') ?>" required>
                    </div>

                    <div class="col-md-3">
                        <label>Fecha de Baja:</label>
                        <input type="date" name="fecha_baja" class="form-control" value="<?= htmlspecialchars($personal['fecha_baja'] ?? '') ?>" required>
                    </div>

                    <?php endif; ?>

                    <div class="col-md-6">
                        <label>Cuenta bancaria:</label>
                        <input type="text" name="cuenta" maxlength="18" class="form-control" value="<?= htmlspecialchars($personal['cuenta'] ?? '') ?>">
                    </div>

                    <div class="col-md-12">
                        <label>Oficio de autoizacion:</label>
                        <input type="file" name="archivo" class="form-control" accept=".pdf,.doc,.docx,.jpg,.png,.jpeg" required>
                    </div>
                <br>
                <div class="mt-3">
                    <button type="submit" class="btn btn-success" onclick="return confirm('Una vez guardado el archivo de alta este NO se podra modificar, ¿Desea continuar?')">Guardar</button>
                    <button onclick="history.back()" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
