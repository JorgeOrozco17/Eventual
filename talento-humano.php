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
</style>
<div class="regreso">
    <span class="menu-title"><a class="menu-link" href="catalogos.php">
    <span class="menu-title">Catalogos /</span>
    </a>Talento humano</span>
</div>

<?php include 'footer.php'; ?>