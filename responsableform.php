<?php
include 'app/controllers/personalcontroller.php';
include 'app/controllers/catalogocontroller.php';
include 'app/controllers/usercontroller.php';
include 'header.php';

$catalogo = new CatalogoController();
$personal = new PersonalController();
$usuario = new UserController();

?>

<style>
    body {
        background-color: #D9D9D9 !important;
    }
    h2 {
        color: #333333
    }
    .regreso {
        margin: 30px 0;
    }
    .section-title {
        color: #222;
        margin-top: 16px;
        margin-bottom: 10px;
        font-size: 1.12rem;
        font-weight: 600;
        letter-spacing: 0.01em;
    }
    .table {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 1px 8px 0 rgba(0,0,0,0.04);
        overflow: hidden;
    }
    .filtro-contratos {
        min-width: 200px;
    }
</style>

<div class="container-fluid px-4 pt-4">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Registro de Responsables</h5>
        </div>
        <div class="card-body">
            <form action="responsable_save.php" method="POST">

                <div class="mb-3">
                    <label for="tipo" class="form-label">Tipo de Responsabilidad</label>
                    <select name="tipo" class="form-select" id="">
                        <option value="">Seleccione un tipo</option>
                        <option value="Juris">Responsable de jurisdiccion</option>
                        <option value="centro">Responsable de centro</option>
                    </select>
                </div>
                
                <!-- Nombre del Responsable -->
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre del Responsable</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Escribe el nombre completo" required>
                </div>

                <!-- Usuario -->
                <div class="mb-3">
                    <label for="usuario" class="form-label">Usuario</label>
                    <select class="form-select" id="usuario" name="usuario" required>
                        <option value="">Seleccione...</option>
                        <?php 
                        $usuarios = $usuario->getAll();
                        foreach ($usuarios as $u): ?>
                            <option value="<?= $u['id'] ?>"><?= $u['Nombre'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Jurisdicción -->
                <div class="mb-3">
                    <label for="jurisdiccion" class="form-label">Jurisdicción</label>
                    <select class="form-select" id="jurisdiccion" name="jurisdiccion" required>
                        <option value="">Seleccione...</option>
                        <?php 
                        $juris = $catalogo->getAllJurisdicciones();
                        foreach ($juris as $j): ?>
                            <option value="<?= $j['id'] ?>"><?= $j['nombre'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Centro -->
                <div class="mb-3">
                    <label for="centro" class="form-label">Centro</label>
                    <select class="form-select" id="centro" name="centro" required>
                        <option value="">Seleccione...</option>
                        <?php 
                        $centros = $catalogo->getAllCentros();
                        foreach ($centros as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= $c['nombre'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Botones -->
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <a href="responsables.php" class="btn btn-secondary">Cancelar</a>
                </div>

            </form>
        </div>
    </div>
</div>

