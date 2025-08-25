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
</style>
    <h2>prueba config</h2>

<?php include 'footer.php'; ?>