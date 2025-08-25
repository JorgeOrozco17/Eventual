<?php include 'header.php';


// Datos de la sesión
$usuario = $_SESSION["s_usuario"];
$rol = $_SESSION["role"];
?>
<style>
    body {
        background-color: #D9D9D9 !important;
    }
    h2 {
        color: #333333
    }
    .regreso{
        margin-top: 2rem;
        margin-bottom: 5rem;
    }
</style>
<div class="regreso">
    <a class="menu-link" href="">
    <span class="menu-tittle">Gestion de personal</span>
    </a>
</div>

<div class="container mt-10">
    <!--------------- SECCION 1 cards --------------->
    <div class="row g-5">
        <!-- Card 1: Usuarios -->
        <div class="col-md-4">
            <div class="card card-flush h-md-100">
                <div class="card-header bg-lila">
                    <h3 class="card-title text-white">Usuarios Autorizados y Activos</h3>
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="mb-5">
                        <span class="menu-icon"><i class="fas fa-users fa-3x text-lila mb-4"></i></span>
                        <p class="text-muted">Gestionar y modificar datos de los usuarios activos</p>
                    </div>
                    <a href="altapersonal.php" class="btn bg-morado-suave align-self-start">Acceder</a>
                </div>
            </div>
        </div>

        <!-- Card 2: Archivo -->
        <div class="col-md-4">
            <div class="card card-flush h-md-100">
                <div class="card-header bg-lila">
                    <h3 class="card-title text-white">usuarios no autorizados</h3>
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="mb-5">
                        <span class="menu-icon"><i class="fas fa-folder-open fa-3x text-lila mb-4"></i></span>
                        <p class="text-muted">Completar datos y registro del personal nuevo</p>
                    </div>
                    <a href="autorizapersonal.php" class="btn bg-morado-suave align-self-start">Acceder</a>
                </div>
            </div>
        </div>

        <!-- Card 3: Talento Humano -->
        <div class="col-md-4">
            <div class="card card-flush h-md-100">
                <div class="card-header bg-lila">
                    <h3 class="card-title text-white">Registrar nuevo usuario</h3>
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="mb-5">
                        <span class="menu-icon"><i class="fas fa-user-tie fa-10x text-lila mb-4"></i></span>
                        <p class="text-muted">Alta de personal nuevo por autorizar</p>
                    </div>
                    <a href="personalform.php" class="btn bg-morado-suave align-self-start">Acceder</a>
                </div>
            </div>
        </div>
    </div>
    <br><br>
    </div>

</div>

<?php include 'footer.php'; ?>