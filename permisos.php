<?php
include 'header.php';
?>

<style>
    body {
        background-color: #f8f9fa !important; /* Lighter background for a fresh feel */
    }

    /* Custom styles for the card, mimicking Metronic's clean separation */
    .permissions-card-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: calc(100vh - 100px); /* Adjust based on header/footer height */
        padding: 40px 15px; /* Add some padding for smaller screens */
    }

    .permissions-card {
        max-width: 480px; /* Slightly wider for a more substantial feel */
        width: 100%;
        border: none; /* Remove default card border */
        border-radius: 12px; /* Soften corners */
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); /* Subtle, modern shadow */
        background-color: #ffffff; /* Explicitly white background */
    }

    /* Title styling */
    .permissions-title {
        font-size: 2.2rem; /* Larger, more impactful title */
        font-weight: 700; /* Bold */
        color: #343a40; /* Darker text for professionalism */
        line-height: 1.2;
    }

    /* Button specific styles */
    .permissions-btn {
        padding: 1.5rem 1rem; /* Generous padding for large, touch-friendly buttons */
        font-size: 1.3rem; /* Larger font size for button text */
        font-weight: 600; /* Semi-bold */
        border-radius: 10px; /* Match card border-radius */
        display: flex; /* Enable flexbox for icon/text alignment */
        align-items: center;
        justify-content: center;
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    .permissions-btn i {
        font-size: 1.8rem; /* Larger icons */
        margin-right: 15px; /* Spacing between icon and text */
    }

    .permissions-btn:hover {
        transform: translateY(-3px); /* Subtle lift effect on hover */
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15); /* More pronounced shadow on hover */
    }

    .permissions-btn b {
        font-weight: 800; /* Make the bold part even bolder */
        margin-left: 5px; /* Small space for emphasis */
    }

    /* Specific button colors for a sober look */
    .btn-custom-primary {
        background-color: #007bff; /* Bootstrap primary blue */
        border-color: #007bff;
        color: #fff;
    }
    .btn-custom-primary:hover {
        background-color: #0069d9;
        border-color: #0062cc;
    }

    .btn-custom-secondary {
        background-color: #6c757d; /* Bootstrap secondary gray */
        border-color: #6c757d;
        color: #fff;
    }
    .btn-custom-secondary:hover {
        background-color: #5a6268;
        border-color: #545b62;
    }

    /* Alternative button style for contrast, if primary/secondary feels too dark */
    /* You can choose between btn-custom-secondary or btn-custom-outline */
    .btn-custom-outline {
        background-color: transparent;
        border: 2px solid #007bff;
        color: #007bff;
    }
    .btn-custom-outline:hover {
        background-color: #007bff;
        color: #fff;
    }

</style>

<div class="permissions-card-container">
    <div class="card permissions-card">
        <div class="card-body p-5">
            <div class="text-center mb-5 mt-3">
                <h1 class="permissions-title">
                    <i class="fas fa-user-shield me-2"></i>
                    Gestión de Permisos
                </h1>
            </div>

            <div class="d-grid gap-3 mb-3"> <a href="permisos_por_rol.php" class="btn permissions-btn btn-custom-primary">
                    <i class="fas fa-users-cog"></i>
                    Permisos por <b>Rol</b>
                </a>
                <a href="permisos_por_usuario.php" class="btn permissions-btn btn-custom-secondary">
                    <i class="fas fa-user"></i>
                    Permisos por <b>Usuario</b>
                </a>
            </div>
        </div>
    </div>
</div>

<?php
include 'footer.php';
?>