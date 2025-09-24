<?php
require_once 'app/controllers/personalController.php';
require_once 'app/controllers/catalogocontroller.php';
include 'header.php';
$controller = new PersonalController();
$catalogo = new CatalogoController();

$id = $_GET['id'];

$date = date('d/m');
$quincenas = $catalogo->getAllQuincenas();
$coments = $controller->getComentsById($id);
$quincena_actual = $catalogo->getQuincenaByDate($date);
$qna_actual = $quincena_actual['nombre'] ?? 'No disponible';


$id = $_GET['id'] ?? '';
$personal = null;

if (isset($_GET['id'])) {
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
        .info-value {
        display: block;
        padding: 8px 2px 4px 0;
        font-size: 1.07rem;
        color: #222;
        background: transparent;
        border: none;
        border-bottom: 1.5px solid #e4e7eb;
        margin-bottom: 12px;
        font-family: inherit;
        letter-spacing: 0.02em;
    }
    .info-label {
        font-weight: 500;
        color: #888ea8;
        margin-bottom: 1px;
        font-size: 0.95rem;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        display: block;
    }
    .card-body {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 1px 6px rgba(100,105,140,.10);
        padding: 36px 32px;
    }
</style>

<div class="container mt-5">
    <div style="margin: 10px;">
        <button class="btn btn-sm btn-info me-2" onclick="history.back()">
            <i class="fas fa-arrow-left-long"></i> Regresar
        </button>
    </div>
    <div class="card">
        <div class="card-header">
            <div class="menu-title">
                <h2>Detalles del Personal</h2>
            </div>
        </div>
        <div class="card-body">
            <form method="POST" action="procesar_baja.php">
                <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
                <?php if ($personal): ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <span class="info-label">Nombre Alta:</span>
                            <span class="info-value"><?= htmlspecialchars($personal['nombre_alta'] ?? '') ?></span>
                        </div>
                        <div class="col-md-3">
                            <span class="info-label">Número de Oficio:</span>
                            <span class="info-value"><?= htmlspecialchars($personal['numero_oficio'] ?? '') ?></span>
                        </div>
                        <div class="col-md-6">
                            <span class="info-label">Solicita:</span>
                            <input type="text" name="solicita" id="solicita" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <span class="info-label">Movimiento:</span>
                            <span name="movimiento" value="BAJA" class="info-value">Baja</span>
                            <input type="text" name="movimiento" id="movimiento" value="baja" hidden readonly>
                        </div>
                        <div class="col-md-6">
                            <span class="info-label">Oficio:</span>
                            <span class="info-value"><?= htmlspecialchars($personal['oficio'] ?? '') ?></span>
                        </div>
                        <div class="col-md-6">
                            <span class="info-label">Puesto:</span>
                            <span class="info-value"><?= htmlspecialchars($personal['puesto'] ?? '') ?></span>
                        </div>
                        <div class="col-md-6">
                            <span class="info-label">Programa:</span>
                            <span class="info-value"><?= htmlspecialchars($personal['programa'] ?? '') ?></span>
                        </div>
                        <div class="col-md-6">
                            <span class="info-label">Rama:</span>
                            <span class="info-value"><?= htmlspecialchars($personal['rama'] ?? '') ?></span>
                        </div>
                        <div class="col-md-6">
                            <span class="info-label">Adscripción:</span>
                            <span class="info-value"><?= htmlspecialchars($personal['adscripcion'] ?? '') ?></span>
                        </div>
                        <div class="col-md-6">
                            <span class="info-label">Centro:</span>
                            <span class="info-value"><?= htmlspecialchars($personal['centro'] ?? '') ?></span>
                        </div>
                        <div class="col-md-6">
                            <span class="info-label">RFC:</span>
                            <span class="info-value"><?= htmlspecialchars($personal['RFC'] ?? '') ?></span>
                        </div>
                        <div class="col-md-6">
                            <span class="info-label">CURP:</span>
                            <span class="info-value"><?= htmlspecialchars($personal['CURP'] ?? '') ?></span>
                        </div>
                        <div class="col-md-6">
                            <span class="info-label">Sueldo Neto Mensual:</span>
                            <span class="info-value"><?= htmlspecialchars($personal['sueldo_neto'] ?? '-') ?></span>
                        </div>
                        <div class="col-md-6">
                            <span class="info-label">Sueldo Bruto Mensual:</span>
                            <span class="info-value"><?= htmlspecialchars($personal['sueldo_bruto'] ?? '-') ?></span>
                        </div>
                        <div class="col-md-3">
                            <span class="info-label">Inicio de Contratación:</span>
                            <span class="info-value"><?= htmlspecialchars($personal['inicio_contratacion'] ?? '') ?></span>
                        </div>
                        <!-- Quincena baja -->
                        <div class="col-md-6">
                            <span class="info-label">Quincena baja:</span>
                            <select name="quincena_baja" id="quincena_baja" class="form-select">
                                <?php foreach ($quincenas as $qna): ?>
                                    <option value="<?= $qna['id'] ?>" <?= (isset($personal['quincena_baja']) && $personal['quincena_baja'] == $qna['nombre']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($qna['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!-- Fecha de Baja -->
                        <div class="col-md-3">
                            <span class="info-label">Fecha de Baja:</span>
                            <input type="date" name="fecha_baja" class="form-control" value="<?= htmlspecialchars($personal['fecha_baja'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <span class="info-label">Cuenta bancaria:</span>
                            <span class="info-value"><?= htmlspecialchars($personal['cuenta'] ?? '') ?></span>
                        </div>
                        <div class="col-md-12">
                            <span class="info-label">Observaciones de la Alta:</span>
                            <span class="info-value"><?= htmlspecialchars($personal['observaciones_alta'] ?? '') ?></span>
                        </div>
                        <!-- CAMPO EDITABLE: Observaciones de la Baja -->
                        <div class="col-md-12">
                            <label class="info-label" for="observaciones_baja">Observaciones de la Baja:</label>
                            <textarea name="observaciones_baja" class="form-control" id="observaciones_baja"><?= htmlspecialchars($personal['observaciones_baja'] ?? '') ?></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="info-label" for="observaciones_usuario">Observaciones del Usuario:</label>
                            <textarea name="observaciones_usuario" class="form-control" id="observaciones_usuario"><?= htmlspecialchars($coments['comentario'] ?? '') ?></textarea>
                        </div>
                    </div>
                    <br>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-success">Guardar</button>
                        <a href="altapersonal.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">No se encontró información de la persona.</div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
