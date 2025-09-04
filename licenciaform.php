<?php
require_once 'app/controllers/faltas_licenciascontroller.php';
require_once 'app/controllers/catalogocontroller.php';
require_once 'app/controllers/personalcontroller.php';
include 'header.php';

$controller = new Faltas_licenciascontroller();
$catalogo = new CatalogoModel();
$personal = new PersonalController();

$adscripciones = $catalogo->getAllJurisdicciones();
$quincena = $catalogo->getAllQuincenas();


if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $controller->save();
    exit;
}

$id = $_GET['id'] ?? '';

$datos = null;

if (isset($_GET['id'])){
   $datos = $controller->getDatosById($id);
}

$tipofalta = isset($_GET['tipo']) ? $_GET['tipo'] : 'licencia';
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
    <div class="card">
        <div class="card-header">
            <div class="menu-title">
                <h2>Nuevo Registro de Ausencia o Licencia</h2>
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
                <?php endif; ?>

                <!---------------------------------- fin del foreach ------------------------------------------------------->


                <div class="form-group mt-3">
                    <label>Nombre</label>
                    <input type="text" class="form-control" name="nombre" id="nombre"  value="<?= htmlspecialchars($datos['nombre'] ?? '') ?>" required>
                </div>

                <div class="form-group mt-3">
                    <input type="hidden" class="form-control" name="jurisdiccion" id="jurisdiccion" value="<?= htmlspecialchars($datos['jurisdiccion'] ?? '') ?>" required>
                </div>

                <div class="form-group mt-3">
                    <label>Adscripción</label>
                    <input type="text" class="form-control" name="adscripcion" id="centro" value="<?= htmlspecialchars($datos['centro'] ?? '') ?>" required>
                </div>

                <div class="form-group mt-3">
                    <label for="dias">Días</label>
                    <input type="number" name="dias" id="dias" value="<?= htmlspecialchars($datos['dias'] ?? '') ?>" class="form-control" required>
                </div>

                <?php if ($tipofalta === 'falta'): ?>
                    <div class="form-group mt-3">
                        <label for="fechas">Fechas</label>
                        <input type="text" name="fechas" placeholder="Ejemplo: 19, 20, 21 de junio 2025" class="form-control mb-2" multiple>
                        <!-- Puedes añadir dinámicamente más fechas vía JS con base en el número de días -->
                    </div>
                <?php else: ?>
                    <div class="form-group mt-3">
                        <label for="periodo_1">Periodo Inicio</label>
                        <input type="date" name="periodo_1" value="<?= htmlspecialchars($datos['periodo_1'] ?? '') ?>" class="form-control">
                    </div>
                    <div class="form-group mt-3">
                        <label for="periodo_2">Periodo Fin</label>
                        <input type="date" name="periodo_2" value="<?= htmlspecialchars($datos['periodo_2'] ?? '') ?>" class="form-control">
                    </div>
                <?php endif; ?>

                <div class="form-group mt-3">
                    <label for="observaciones">Observaciones</label>
                    <input type="text" name="observaciones" value="<?= htmlspecialchars($datos['observaciones'] ?? '') ?>" class="form-control">
                </div>

                <div class="form-group mt-3">
                    <label for="quincena">Quincena de aplicacion del descuento (Si aplica)</label>
                    <select name="quincena" class="form-control" required>
                        <?php foreach ($quincena as $q): 
                            $selected = (isset($datos['quincena']) && $datos['quincena'] == $q['id']) ? 'selected' : '';
                        ?>
                            <option value="<?= $q['id'] ?>" <?= $selected ?>><?= $q['nombre'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <input type="hidden" name="id_usuario" value="<?= $_SESSION['user_id'] ?>">
                <input type="hidden" name="tipo" value="<?= $tipofalta ?>">

                <div class="form-group mt-4">
                    <button type="submit" name="guardar" class="btn btn-success" onclick="return confirm('Si la quincena de aplicacion es anterior a la actual se aplicara en la quincena seleccionada del proximo año')">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>