<?php 
include 'header.php'; 


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
    .card{
        padding: 0%;
    }
    .card:hover {
        transform: scale(1.05);
    }
</style>
<div class="regreso">
        <span class="menu-title"><a class="menu-link" href="menu_captura.php">
        <span class="menu-tittle">Captura</span></a> 
    </div>

<div class="container mt-10">
    <!--------------- SECCION 1 cards --------------->
    <div class="row g-5">
        <!-- Card 1: Usuarios -->
        <div class="col-md-4">
            <div class="card card-flush h-md-100">
                <div class="card-header bg-lila">
                    <h3 class="card-title text-white">Registros de nominas</h3>
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="mb-5">
                        <span class="menu-icon"><i class="fas fa-users fa-3x text-lila mb-4"></i></span>
                        <p class="text-muted">Control de nominas</p>
                    </div>
                    <a href="nomina.php" class="btn bg-morado-suave align-self-start">Acceder</a>
                </div>
            </div>
        </div>

        <!-- Card 2: Archivo -->
        <div class="col-md-4">
            <div class="card card-flush h-md-100">
                <div class="card-header bg-lila">
                    <h3 class="card-title text-white">Pensiones</h3>
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="mb-5">
                        <span class="menu-icon"><i class="fas fa-folder-open fa-3x text-lila mb-4"></i></span>
                        <p class="text-muted">Modulo de pensiones</p>
                    </div>
                    <a href="pension.php" class="btn bg-morado-suave align-self-start">Acceder</a>
                </div>
            </div>
        </div>

        <!-- Card 3: Talento Humano -->
        <div class="col-md-4">
            <div class="card card-flush h-md-100">
                <div class="card-header bg-lila">
                    <h3 class="card-title text-white">Generar hojas de recibos y archivos</h3>
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="mb-5">
                        <span class="menu-icon"><i class="fas fa-user-tie fa-10x text-lila mb-4"></i></span>
                        <p class="text-muted">Archivos generados</p>
                    </div>
                    <a href="recibos.php" class="btn bg-morado-suave align-self-start">Acceder</a>
                </div>
            </div>
        </div>
    </div>
    <br><br>
    </div>

</div>

<?php include 'footer.php'; ?>