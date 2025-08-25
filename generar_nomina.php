<?php
require_once 'app/controllers/capturacontroller.php';
require_once 'app/controllers/catalogocontroller.php';
include 'header.php';

$catalogo = new CatalogoController();
$captura = new Capturacontroller();


$quincena = $catalogo->getAllQuincenas();
?>
<?php if (isset($_GET['error'])): ?>
    <script>
        const errorType = "<?php echo $_GET['error']; ?>";

        if (errorType === "1") {
            // Error de que ya existe una nómina para esa quincena y año
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: 'Ya se ha creado una nómina para esta quincena y año.',
                confirmButtonText: 'Cerrar'
            });
        } else if (errorType === "2") {
            // Error genérico en caso de otros problemas al generar la nómina
            Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: 'Hubo un error al generar la nómina. Intenta nuevamente.',
                confirmButtonText: 'Cerrar'
            });
        }
    </script>
<?php endif; ?>
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
                <h2>Generar Recibos de Nómina</h2>
            </div>
        </div>
       
        <div class="card-body">
            <form action="procesar_nomina.php" method="POST">
                <div class="row g-3">
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