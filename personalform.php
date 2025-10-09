<?php
if (isset($_GET['adscrip_id'])) {
    require_once 'app/controllers/catalogocontroller.php';
    $catalogo = new CatalogoController();
    $centros = $catalogo->getCentrosByAdscripcion($_GET['adscrip_id']);

    header('Content-Type: application/json');
    echo json_encode($centros);
    exit;
}
?>

<?php
require_once 'app/controllers/personalController.php';
require_once 'app/controllers/catalogocontroller.php';
$id_pagina = 29; // ID de la página para el menú
include 'header.php';
$controller = new PersonalController();
$catalogo = new CatalogoModel(); 
$puestos = $catalogo->getAllPuestos();
$recursos = $catalogo->getAllRecursos();
$adscripciones = $catalogo->getAllJurisdicciones();
$quincena = $catalogo->getAllQuincenas();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_personal'] ?? null;
    if (!empty($id)) {
        $controller->updatePersonal();
    } else {
        $controller->save();
    }
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
        background-color: #e9ecef !important;
    }
    h2 {
        color: #1e293b;
        font-weight: 600;
    }
    .card {
        border: none;
        border-radius: 14px;
        box-shadow: 0 3px 12px rgba(0,0,0,0.08);
        background-color: #ffffff;
    }
    .card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        border-top-left-radius: 14px;
        border-top-right-radius: 14px;
    }
    label {
        font-weight: 500;
        color: #374151;
        margin-bottom: 4px;
    }
    input.form-control, select.form-select, textarea.form-control, date.form-control {
        border-radius: 10px;
        border: 1px solid #ced4da;
        transition: all 0.2s ease-in-out;
        background-color: #f9fafb;
        color: #060708ff;
    }
    input.form-control:focus, select.form-select:focus, textarea.form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.15rem rgba(13,110,253,0.25);
        background-color: #fff;
    }
    .btn-success {
        background-color: #198754;
        border: none;
        border-radius: 8px;
        padding: 0.5rem 1.5rem;
    }
    .btn-success:hover {
        background-color: #157347;
    }
    .btn-secondary {
        border-radius: 8px;
        padding: 0.5rem 1.5rem;
    }
    .section-divider {
        margin-top: 2rem;
        margin-bottom: 1rem;
        border-bottom: 2px solid #dee2e6;
        padding-bottom: 0.5rem;
        font-size: 1.05rem;
        color: #495057;
        font-weight: 600;
    }
    textarea.form-control {
        min-height: 80px;
        resize: vertical;
    }
</style>

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="regreso">
        <span class="menu-title"><a class="menu-link" href="personal.php">
        <span class="menu-tittle">Personal</span></a> <span class="menu-tittle">/Registrar personal</span></span>
        </div>
    </div>

    <div style="margin: 10px;">
        <button class="btn btn-sm btn-info me-2" onclick="history.back()"><i class="fas fa-arrow-left-long"></i>Regresar</button>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0"><?= $id ? 'Editar Registro de Personal' : 'Nuevo Registro de Personal' ?></h2>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data" class="mt-3">
                <div class="section-divider">Datos Generales</div>
                <div class="row g-3">
                    <div class="col-md-10">
                        <label>Nombre Alta:</label>
                        <input type="text" name="nombre_alta" class="form-control" value="<?= htmlspecialchars($personal['nombre_alta'] ?? '') ?>">
                    </div>

                    <?php if (!isset($_GET['id'])): ?>
                    <div class="col-md-6">
                        <label>Solicita</label>
                        <input type="text" name="solicita" class="form-control" value="<?= htmlspecialchars($personal['solicita'] ?? '') ?>">
                    </div>

                    <div class="col-md-4">
                        <label>Movimiento:</label>
                        <select name="movimiento" class="form-select" required>
                            <option value="">Seleccione</option>
                            <option value="alta" <?= (isset($personal['movimiento']) && $personal['movimiento'] == 'alta') ? 'selected' : '' ?>>ALTA</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Oficio:</label>
                        <input type="text" name="oficio" class="form-control" value="<?= htmlspecialchars($personal['oficio'] ?? '') ?>" required>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="section-divider">Identificación y Puesto</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label>RFC:</label>
                        <input type="text" name="RFC" class="form-control" value="<?= htmlspecialchars($personal['RFC'] ?? '') ?>" maxlength="13" required>
                    </div>

                    <div class="col-md-6">
                        <label>CURP:</label>
                        <input type="text" name="CURP" class="form-control" value="<?= htmlspecialchars($personal['CURP'] ?? '') ?>" maxlength="18" required>
                    </div>

                    <div class="col-md-6">
                        <label>Puesto:</label>
                        <select name="puesto" class="form-select" required>
                            <?php foreach ($puestos as $p): ?>
                                <option value="<?= $p['nombre_puesto'] ?>" 
                                    <?= (isset($personal['puesto']) && $personal['puesto'] == $p['nombre_puesto']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($p['nombre_puesto']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Nuevo campo: Descripción de tipo de nómina -->
                    <div class="col-md-6">
                        <label>Descripción del tipo de nómina:</label>
                        <select name="desc_tnomina" class="form-select" required>
                            <option value="">Seleccione</option>
                            <option value="EVENTUALES" <?= (isset($personal['desc_tnomina']) && $personal['desc_tnomina'] == 'EVENTUALES') ? 'selected' : '' ?>>EVENTUALES</option>
                            <option value="PROMOTORES INSABI" <?= (isset($personal['desc_tnomina']) && $personal['desc_tnomina'] == 'PROMOTORES INSABI') ? 'selected' : '' ?>>PROMOTORES INSABI</option>
                            <option value="INSABI ANEXO IV" <?= (isset($personal['desc_tnomina']) && $personal['desc_tnomina'] == 'INSABI ANEXO IV') ? 'selected' : '' ?>>INSABI ANEXO IV</option>
                            <option value="PROGRAMA DE CANCER DE LA MUJER INSABI" <?= (isset($personal['desc_tnomina']) && $personal['desc_tnomina'] == 'PROGRAMA DE CANCER DE LA MUJER INSABI') ? 'selected' : '' ?>>PROGRAMA DE CÁNCER DE LA MUJER INSABI</option>
                            <option value="VACUNACION FEDERAL" <?= (isset($personal['desc_tnomina']) && $personal['desc_tnomina'] == 'VACUNACION FEDERAL') ? 'selected' : '' ?>>VACUNACIÓN FEDERAL</option>
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
                    <select name="rama" class="form-select" required>
                        <option value="RAMA ADMINISTRATIVA" <?= (isset($personal['rama']) && $personal['rama'] == 'RAMA ADMINISTRATIVA') ? 'selected' : '' ?>>RAMA ADMINISTRATIVA</option>
                        <option value="RAMA MEDICA" <?= (isset($personal['rama']) && $personal['rama'] == 'RAMA MEDICA') ? 'selected' : '' ?>>RAMA MÉDICA</option>
                    </select>
                </div>

                <div class="section-divider">Adscripción y Sueldo</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label>Adscripción:</label>
                        <select name="adscripcion" id="adscripcion" class="form-select" required>
                            <?php foreach ($adscripciones as $a): ?>
                                <option value="<?= $a['id'] ?>" <?= (isset($personal['id_adscripcion']) && $personal['id_adscripcion'] == $a['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($a['nombre']) . ' - ' . htmlspecialchars($a['ubicacion']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Centro:</label>
                        <select name="centro" id="centro" class="form-select" required>
                            <option value="<?= htmlspecialchars($personal['id_centro'] ?? '') ?>">
                                <?= htmlspecialchars($personal['centro'] ?? 'Seleccione un centro') ?>
                            </option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Sueldo Bruto Mensual:</label>
                        <input type="number" step="0.01" name="sueldo_bruto" class="form-control" 
                            value="<?= htmlspecialchars($personal['sueldo_bruto'] ?? '') ?>" required>
                    </div>

                    <div class="col-md-6">
                        <label>Cuenta bancaria:</label>
                        <input type="number" name="cuenta" class="form-control" value="<?= htmlspecialchars($personal['cuenta'] ?? '') ?>">
                    </div>
                </div>

                <?php if (!isset($_GET['id'])): ?>
                <div class="section-divider">Inicio de Contrato</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label>Quincena alta:</label>
                        <select name="quincena_alta" class="form-select" required>
                            <?php foreach ($quincena as $q): ?>
                                <option value="<?= $q['nombre'] ?>" <?= (isset($personal['quincena_alta']) && $personal['quincena_alta'] == $q['nombre']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($q['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Inicio de Contratación:</label>
                        <input type="date" name="inicio_contratacion" class="form-control" value="<?= htmlspecialchars($personal['inicio_contratacion'] ?? '') ?>">
                    </div>
                </div>
                <?php endif; ?>

                <div class="section-divider">Observaciones</div>
                <div class="row g-3">
                    <div class="col-md-12">
                        <label>Observaciones de la Alta:</label>
                        <textarea name="observaciones_alta" class="form-control"><?= htmlspecialchars($personal['observaciones_alta'] ?? '') ?></textarea>
                    </div>

                    <div class="col-md-12">
                        <label>Observaciones del Usuario:</label>
                        <textarea name="observaciones_usuario" class="form-control"><?= htmlspecialchars($personal['observaciones_usuario'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Guardar
                    </button>
                    <a href="altapersonal.php" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
</div>


<script>
document.querySelector("form").addEventListener("submit", function(e){
    console.log("Datos enviados:", Object.fromEntries(new FormData(this)));
});
</script>

<?php include 'footer.php'; ?>
