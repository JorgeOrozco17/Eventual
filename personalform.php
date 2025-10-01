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

    <div class="regreso">
        <span class="menu-title"><a class="menu-link" href="personal.php">
        <span class="menu-tittle">Personal</span></a> <span class="menu-tittle">/Registrar personal</span></span>
    </div>

    <div style="margin: 10px;">
        <button class="btn btn-sm btn-info me-2" onclick="history.back()"><i class="fas fa-arrow-left-long"></i>Regresar</button>
    </div>
    
    <div class="card">
        <div class="card-header">
            <div class="menu-title">
                <h2><?= $id ? 'Editar Registro de Personal' : 'Nuevo Registro de Personal' ?></h2>
            </div>
        </div>

        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">

                <div class="row g-3">

                    <div class="col-md-10">
                        <label>Nombre Alta:</label>
                        <input type="text" name="nombre_alta" class="form-control" value="<?= htmlspecialchars($personal['nombre_alta'] ?? '') ?>">
                    </div>

                    <?php if (!isset($_GET['id'])): ?>
                    <div class="col-md-6">
                        <label>Solicita</label>
                        <input type="text" name="solicita" class="form-control" value="<?= htmlspecialchars($personal['solicita'] ?? '') ?>" >
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

                    <div>
                        <input type="hidden" name="estatus" id="estatus" >
                        <input type="hidden" name="id_personal" id="id_personal" value="<?= htmlspecialchars($personal['id'] ?? '') ?>" id="">
                    </div>

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

                    <div class="col-md-6">
                        <label>Programa:</label>
                        <select name="programa" class="form-select" required>
                            <?php foreach ($recursos as $r): ?>
                                <option value="<?= $r['nombre'] ?>" 
                                    <?= (isset($personal['programa']) && $personal['programa'] == $r['nombre']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($r['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Rama:</label>
                        <select name="rama" id="" class="form-select" required>
                            <option value="RAMA ADMINISTRATIVA">RAMA ADMINISTRATIVA</option>
                            <option value="RAMA MEDICA"> RAMA MEDICA</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Adscripción:</label>
                        <select name="adscripcion" id="adscripcion" class="form-select" required>
                            <?php foreach ($adscripciones as $a): ?>
                                <option value="<?= $a['id'] ?>"
                                    <?= (isset($personal['id_adscripcion']) && $personal['id_adscripcion'] == $a['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($a['nombre']) . '-' . htmlspecialchars($a['ubicacion'])?>
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
                        <label>Selecciona el tipo de sueldo a ingresar:</label>
                        <select id="tipo_sueldo" class="form-select" onchange="toggleSueldoFields()">
                            <option value="">Seleccione</option>
                            <!---- <option value="neto">Sueldo Neto</option> --->
                            <option value="bruto">Sueldo Bruto</option>
                        </select>
                    </div>

                    <!-- Sueldo Neto -->
                    <div class="col-md-6" id="sueldo_neto_field" style="display:none;">
                        <label>Sueldo Neto Mensual:</label>
                        <input type="number" step="0.01" name="sueldo_neto" class="form-control" id="sueldo_neto">
                    </div>

                    <!-- Sueldo Bruto -->
                    <div class="col-md-6">
                        <label>Sueldo Bruto Mensual:</label>
                        <input type="number" step="0.01" name="sueldo_bruto" class="form-control" 
                            value="<?= htmlspecialchars($personal['sueldo_bruto'] ?? '') ?>" required>
                    </div>

                    <!-- Mensaje de error si no se elige un sueldo -->
                    <div id="error-message" style="color: red; display: none;">
                        Debes ingresar solo uno de los dos sueldos (neto o bruto).
                    </div>

                    <?php if (!isset($_GET['id'])): ?>
                        <!-- Mostrar campos de alta solo si no hay id -->
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
                            <div class="col-md-4">
                                <label>Inicio de Contratación:</label>
                                <input type="date" name="inicio_contratacion" class="form-control" value="<?= htmlspecialchars($personal['inicio_contratacion'] ?? '') ?>">
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="col-md-6">
                        <label>Cuenta bancaria:</label>
                        <input type="number" name="cuenta" class="form-control" value="<?= htmlspecialchars($personal['cuenta'] ?? '') ?>">
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
                    <button type="submit" class="btn btn-success" >Guardar</button>
                    <a href="altapersonal.php" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
document.querySelector("form").addEventListener("submit", function(e){
    console.log("Datos enviados:", Object.fromEntries(new FormData(this)));
});
</script>

<?php include 'footer.php'; ?>
