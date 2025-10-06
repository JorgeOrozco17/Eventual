<?php
require_once 'app/controllers/capturacontroller.php';
require_once 'app/controllers/catalogocontroller.php';
include 'header.php';

$catalogo = new CatalogoController();
$captura = new Capturacontroller();


$quincena = $catalogo->getAllQuincenas();

if (isset($_GET['error']) && $_GET['error'] == 1) {
    echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Error al registrar',
                text: 'Ya existe una nómina en ese periodo.',
                confirmButtonText: 'Aceptar',
                background: '#fdfaf6',
                color: '#333',
                customClass: {
                    popup: 'rounded-4 shadow-lg'
                }
            });
        });
    </script>
    ";
}

if (isset($_GET['error']) && $_GET['error'] == 2) {
    echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Error al registrar',
                text: 'No existe nomina ordinaria para este periodo. Genere la nomina ordinaria primero.',
                confirmButtonText: 'Aceptar',
                background: '#fdfaf6',
                color: '#333',
                customClass: {
                    popup: 'rounded-4 shadow-lg'
                }
            });
        });
    </script>
    ";
}

?>
<style>
    body {
        background-color: #D9D9D9 !important;
    }
</style>


<div class="container-fluid mt-5">
    <div class="regreso">
        <span class="menu-title"></span>
    </div>
    <br>
    <div class="card">
        <div class="card-header">
            <div class="menu-title">
                <h2>Generar Concentrado de Nómina</h2>
            </div>
        </div>
       
        <div class="card-body">
            <form action="procesar_nomina.php" method="POST">
                <div class="row g-3">

                    <div>
                        <input type="hidden" name="tipo" id="" value="Ordinaria">
                    </div>
                    <div class="col-md-6">
                        <label>Quincena</label>
                        <select name="quincena" class="form-select" required>
                            <?php foreach ($quincena as $q): ?>
                                <option value="<?= htmlspecialchars($q['id']) ?>">
                                    <?= htmlspecialchars($q['nombre']) ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Año</label>
                        <input type="text" name="año" class="form-control" maxlength="4" pattern="\d{4}" placeholder="Ingrese un año de 4 dígitos" required>
                    </div>
                </div>

                <br>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Generar Nómina</button>
                </div>
            </form>
        </div>
    </div>

    <br><br>

    <div class="card">
        <div class="card-header">
            <div class="menu-title">
                <h2>Generar Nómina Extraordinaria</h2>
            </div>
        </div>
       
        <div class="card-body">
            <form action="procesar_nomina.php" method="POST">
                <div class="row g-3">
                    <div>
                        <input type="hidden" name="tipo" id="" value="EXT">
                    </div>
                    <div class="col-md-6">
                        <label>Quincena</label>
                        <select name="quincena" class="form-select" required>
                            <?php foreach ($quincena as $q): ?>
                                <option value="<?= htmlspecialchars($q['id']) ?>">
                                    <?= htmlspecialchars($q['nombre']) ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Año</label>
                        <input type="text" name="año" class="form-control" maxlength="4" pattern="\d{4}" placeholder="Ingrese un año de 4 dígitos" required>
                    </div>
                </div>

                <br>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Generar Nómina</button>
                </div>
            </form>
        </div>
    </div>

</div>

<?php include 'footer.php'; ?>