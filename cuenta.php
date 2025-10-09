<?php
require_once 'app/controllers/usercontroller.php';
include 'header.php';

$userCtrl = new UserController();
$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: login.php");
    exit;
}

$usuario = $userCtrl->getById($user_id);
$mensaje = null;

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Es crucial que el input 'foto' esté dentro del <form> para que $_FILES lo reciba.
    $response = $userCtrl->actualizarCuenta($user_id, $_POST, $_FILES);
    $mensaje = $response;
    // Recargar los datos del usuario para mostrar la info actualizada inmediatamente
    $usuario = $userCtrl->getById($user_id);
}
?>

<style>
    body {
        background-color: #f8f9fa !important; /* Un gris muy claro, estándar en diseños modernos */
    }

    .account-card {
        border: 1px solid #e9ecef; /* Borde sutil en lugar de sombra fuerte */
        border-radius: 12px; /* Un radio más estándar y profesional */
        background-color: #fff;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); /* Sombra mucho más suave */
    }

    .account-avatar {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #dee2e6; /* Borde gris claro para integrarse mejor */
        margin: 0 auto;
        transition: transform 0.3s ease;
    }
    
    .account-avatar:hover {
        transform: scale(1.05);
    }

    .avatar-upload-wrapper {
        position: relative;
        display: inline-block;
    }

    .btn-camera {
        position: absolute;
        bottom: 5px;
        right: 5px;
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .btn-camera:hover {
        background-color: #f1f3f5;
    }

    .form-control, .input-group .btn {
        border-color: #ced4da; /* Color de borde estándar de Bootstrap */
    }

    .form-control:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    
    .form-section-title {
        color: #495057;
        font-weight: 500;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #e9ecef;
        margin-bottom: 1.5rem;
        margin-top: 2rem;
    }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9">
            
            <div class="text-center mb-4">
                <h2 class="fw-bold"><i class="fas fa-user-cog me-2"></i>Mi Cuenta</h2>
                <p class="text-muted">Gestiona tu información personal y de seguridad.</p>
            </div>

            <div class="card account-card">
                <div class="card-body p-4 p-md-5">

                    <form method="POST" enctype="multipart/form-data">
                        
                        <div class="text-center mb-4">
                            <div class="avatar-upload-wrapper">
                                <img id="previewFoto"
                                     src="<?= !empty($usuario['archivo']) ? 'uploads/perfiles/' . htmlspecialchars($usuario['archivo']) : 'public/img/ss.jpg' ?>"
                                     alt="Foto de perfil" class="account-avatar">
                                <label for="foto" class="btn-camera shadow-sm">
                                    <i class="fas fa-camera text-primary"></i>
                                </label>
                            </div>
                            <input type="file" id="foto" name="foto" class="d-none" accept="image/*" onchange="previewImagen(event)">
                        </div>

                        <h5 class="form-section-title">Información Personal</h5>

                        <div class="mb-3">
                            <label for="usuario" class="form-label fw-medium">Nombre de usuario</label>
                            <input type="text" class="form-control" id="usuario" name="usuario" value="<?= htmlspecialchars($usuario['usuario']) ?>" required>
                        </div>
                        
                        <h5 class="form-section-title">Cambiar Contraseña</h5>
                        <p class="text-muted small mb-3">Deja los campos en blanco si no deseas cambiar tu contraseña.</p>

                        <div class="mb-3">
                            <label for="password_actual" class="form-label fw-medium">Contraseña actual</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password_actual" name="password_actual" placeholder="Ingrese su contraseña actual">
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password_actual', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password_nueva" class="form-label fw-medium">Nueva contraseña</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password_nueva" name="password_nueva" placeholder="Ingresa nueva contraseña">
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password_nueva', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmar" class="form-label fw-medium">Confirmar nueva contraseña</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password_confirmar" name="password_confirmar" placeholder="Ingresa nuevamente la nueva contraseña">
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password_confirmar', this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg px-5 rounded-3 shadow-sm">
                                <i class="fas fa-save me-2"></i>Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($mensaje)): ?>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: '<?= $mensaje['success'] ? 'success' : 'error' ?>',
            title: '<?= $mensaje['success'] ? '¡Éxito!' : 'Error' ?>',
            text: '<?= addslashes($mensaje['message']) ?>',
            timer: 3000,
            timerProgressBar: true,
            showConfirmButton: false
        });
    });
</script>
<?php endif; ?>


<script>
function previewImagen(event) {
    const file = event.target.files[0];
    if (!file) {
        return;
    }
    const reader = new FileReader();
    reader.onload = (e) => {
        document.getElementById('previewFoto').src = e.target.result;
    };
    reader.readAsDataURL(file);
}

function togglePassword(id, button) {
    const input = document.getElementById(id);
    const icon = button.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

<?php include 'footer.php'; ?>