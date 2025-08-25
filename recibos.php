<?php
require_once 'app/controllers/capturacontroller.php';
require_once 'app/controllers/catalogocontroller.php';
include 'header.php';

$catalogo = new CatalogoController();
$captura = new Capturacontroller();


$quincena = $catalogo->getAllQuincenas();
$recursos = $catalogo->getAllRecursos();
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
        <span class="menu-tittle">/Generador de Recibos</span></span>
    </div>
    <br>

    <!-------------------------------------- card para recibos -------------------------------------->
    <div class="card">
        <div class="card-header">
            <div class="menu-title">
                <h2>Generar hoja de firmas</h2>
            </div>
        </div>
       
        <div class="card-body">
            <form action="generar_recibos.php" method="POST" target="_blank">
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
                        <label>Jurisdicción</label>
                        <select name="jurisdiccion" class="form-select" required>
                            <option value="todas">Todas</option>
                            <option value="JUR 0- OFICINA CENTRAL">JUR 0- OFICINA CENTRAL</option>
                            <option value="JUR 1- PIEDRAS NEGRAS">JUR 1- PIEDRAS NEGRAS</option>
                            <option value="JUR 2- ACUÑA">JUR 2- ACUÑA</option>
                            <option value="JUR 3- SABINAS">JUR 3- SABINAS</option>
                            <option value="JUR 4- MONCLOVA">JUR 4- MONCLOVA</option>
                            <option value="JUR 5- CUATROCIENEGAS">JUR 5- CUATROCIENEGAS</option>
                            <option value="JUR 6- TORREON">JUR 6- TORREON</option>
                            <option value="JUR 7- FCO. I. MADERO">JUR 7- FCO. I. MADERO</option>
                            <option value="JUR 8- SALTILLO">JUR 8- SALTILLO</option>
                        </select>
                    </div>
                </div>
                <br>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Generar Nómina</button>
                </div>
            </form>
        </div>
    </div>

    <!-------------------------------------- card para recibos -------------------------------------->
    <div class="card mt-4">
        <div class="card-header">
            <div class="menu-title  ">
                <h2>Recibos de nómina</h2>
            </div>
        </div>

        <div class="card-body">
            <form action="generar_talon.php" method="POST" target="_blank">
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
                        <label>Jurisdicción</label>
                        <select name="jurisdiccion" class="form-select" required>
                            <option value="todas">Todas</option>
                            <option value="JUR 0- OFICINA CENTRAL">JUR 0- OFICINA CENTRAL</option>
                            <option value="JUR 1- PIEDRAS NEGRAS">JUR 1- PIEDRAS NEGRAS</option>
                            <option value="JUR 2- ACUÑA">JUR 2- ACUÑA</option>
                            <option value="JUR 3- SABINAS">JUR 3- SABINAS</option>
                            <option value="JUR 4- MONCLOVA">JUR 4- MONCLOVA</option>
                            <option value="JUR 5- CUATROCIENEGAS">JUR 5- CUATROCIENEGAS</option>
                            <option value="JUR 6- TORREON">JUR 6- TORREON</option>
                            <option value="JUR 7- FCO. I. MADERO">JUR 7- FCO. I. MADERO</option>
                            <option value="JUR 8- SALTILLO">JUR 8- SALTILLO</option>
                        </select>
                    </div>
                </div>
                <br>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Generar Nómina</button>
                </div>
            </form>
        </div>
    </div>


    
    <div class="card mt-4">
        <div class="card-header">
            <div class="menu-title  ">
                <h2>Tabla de totales</h2>
            </div>
        </div>

        <div class="card-body">
            <form action="generar_tabla_totales.php" method="POST" target="_blank">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label>Quincena</label>
                        <select name="quincena" class="form-select" required>
                            <?php  foreach ($quincena as $q): ?>
                                <option value="<?= htmlspecialchars($q['id']) ?>">
                                    <?= htmlspecialchars($q['nombre']) ?>
                                </option>
                            <?php  endforeach ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Año</label>
                        <input type="number" name="anio" placeholder="Ingrese un año de 4 digitos" class="form-control" min="2020" max="2099" maxlength="4" required>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label>Recurso</label>
                        <select name="programa" class="form-select" required>
                            <?php  foreach ($recursos as $r): ?>
                                <option value="<?= htmlspecialchars($r['nombre']) ?>">
                                    <?= htmlspecialchars($r['nombre']) ?>
                                </option>
                            <?php  endforeach ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Rama</label>
                        <select name="rama" class="form-select" required>
                            <option value="todos">Seleccionar rama</option>
                            <option value="RAMA ADMINISTRATIVA">RAMA ADMINISTRATIVA</option>
                            <option value="RAMA MEDICA">RAMA MEDICA</option>
                        </select>
                    </div>
                </div>

                <br>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Generar Nómina</button>
                </div>
            </form>
        </div>
    </div>
   

<?php include 'footer.php'; ?>