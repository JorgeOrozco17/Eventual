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
    .card{
        padding: 0%;
    }
    .card:hover {
        transform: scale(1.05);
    }
</style>
<div class="regreso">
    <a class="menu-link" href="index.php">
    <span class="menu-tittle">Catalogos</span>
    </a>
</div>

<div class="container mt-10">

    <!--------------- SECCION 2 cards --------------->
    <div class="row g-5">
        <!-- Card 4: Jurisdicciones -->
        <div class="col-md-4">
            <div class="card card-flush h-md-100">
                <div class="card-header bg-lila">
                    <h3 class="card-title text-white">Jurisdicciones</h3>
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="mb-5">
                        <span class="menu-icon"><i class="fas fa-hospital fa-3x text-lila mb-4"></i></span>
                        <p class="text-muted">Listado de Jurisdicciones</p>
                    </div>
                    <a href="juris.php" class="btn bg-morado-suave align-self-start">Acceder</a>
                </div>
            </div>
        </div>

        <!-- Card 5: Centros -->
        <div class="col-md-4">
            <div class="card card-flush h-md-100">
                <div class="card-header bg-lila">
                    <h3 class="card-title text-white">Centros</h3>
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="mb-5">
                        <span class="menu-icon"><i class="fas fa-house-medical fa-3x text-lila mb-4"></i></span>
                        <p class="text-muted">Listado de Centros</p>
                    </div>
                    <a href="centros.php" class="btn bg-morado-suave align-self-start">Acceder</a>
                </div>
            </div>
        </div>

        <!-- Card 6: Programas -->
        <div class="col-md-4">
            <div class="card card-flush h-md-100">
                <div class="card-header bg-lila">
                    <h3 class="card-title text-white">Programas</h3>
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="mb-5">
                        <span class="menu-icon"><i class="fas fa-file-invoice-dollar fa-3x text-lila mb-4"></i></span>
                        <p class="text-muted">Listado de progamas de recurso</p>
                    </div>
                    <a href="programas.php" class="btn bg-morado-suave align-self-start">Acceder</a>
                </div>
            </div>
        </div>
    </div>

    <br><br>
    <!--------------- SECCION 3 cards --------------->
    <div class="row g-5">
        <!-- Card 7: Puestos -->
        <div class="col-md-4">
            <div class="card card-flush h-md-100">
                <div class="card-header bg-lila">
                    <h3 class="card-title text-white">Puestos</h3>
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="mb-5">
                        <span class="menu-icon"><i class="fas fa-address-card fa-3x text-lila mb-4"></i></span>
                        <p class="text-muted">Listado de Puestos disponibles</p>
                    </div>
                    <a href="puestos.php" class="btn bg-morado-suave align-self-start">Acceder</a>
                </div>
            </div>
        </div>

        <!-- Card 8: Quincenas -->
        <div class="col-md-4">
            <div class="card card-flush h-md-100">
                <div class="card-header bg-lila">
                    <h3 class="card-title text-white">Quincenas</h3>
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="mb-5">
                        <span class="menu-icon"><i class="fas fa-file-invoice-dollar fa-3x text-lila mb-4"></i></span>
                        <p class="text-muted">Listado de Quincenas</p>
                    </div>
                    <a href="quincenas.php" class="btn bg-morado-suave align-self-start">Acceder</a>
                </div>
            </div>
        </div>

        <!-- Card 9: Puestos  -->
        <div class="col-md-4">
            <div class="card card-flush h-md-100">
                <div class="card-header bg-lila">
                    <h3 class="card-title text-white">Conceptos Fijos</h3>
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="mb-5">
                        <span class="menu-icon"><i class="fas fa-dollar-sign fa-3x text-lila mb-4"></i></span>
                        <p class="text-muted">Capturas de percepciones y deducciones automiaticas/fijas</p>
                    </div>
                    <a href="fijos.php" class="btn bg-morado-suave align-self-start">Acceder</a>
                </div>
            </div>
        </div>
    </div>
    <br><br>

    <div class="row g-5">
        <!-- Card 10: Articulo 74 -->
        <div class="col-md-4">
            <div class="card card-flush h-md-100">
                <div class="card-header bg-lila">
                    <h3 class="card-title text-white">Articulo 74</h3>
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="mb-5">
                        <span class="menu-icon"><i class="fas fa-address-card fa-3x text-lila mb-4"></i></span>
                        <p class="text-muted">Captura de datos articulo 74</p>
                    </div>
                    <a href="articulo74.php" class="btn bg-morado-suave align-self-start">Acceder</a>
                </div>
            </div>
        </div>

</div>

<?php include 'footer.php'; ?>