<?php
require_once 'app/controllers/UserController.php';
include 'header.php';
$controller = new UserController();

// Procesar eliminación
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $controller->delete($_GET['id']);
} 

// Obtener todos los usuarios
$usuarios = $controller->model->getAll();
?>
<style>
    body {
        background-color: #D9D9D9 !important;
    }
    h2 {
        color: #333333
    }
</style>

<div class="container-fluid mt-5">
    <!-- Breadcrumb -->
    <div class="regreso mb-4">
        <span class="menu-title">
            <a class="menu-link" href="catalogos.php">
                <span class="menu-tittle">Catalogos</span>
            </a>
            <span class="menu-tittle">/Usuarios</span>
        </span>
    </div>
    
    <div class="card card-flush shadow-sm mb-10">
        <!--begin::Card header-->
        <div class="card-header border-0 pb-0 d-flex flex-wrap flex-stack">
            <h2 class="card-title fw-bold mb-0">
                <i class="fas fa-users me-2 text-primary"></i>
                Administración de Usuarios
            </h2>
            <div class="card-toolbar">
                <a href="usuarioform.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Usuario
                </a>
            </div>
        </div>
        <!--end::Card header-->

        <?php if (isset($_GET['duplicate'])): ?>
            <div class="alert alert-warning alert-dismissible fade show mt-4" role="alert">
                El RFC o nombre de usuario ya existe. Por favor, verifica los datos.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show mt-4" role="alert">
                Usuario guardado correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show mt-4" role="alert">
                Ocurrió un error al guardar el usuario. Intenta nuevamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!--begin::Card body-->
        <div class="card-body pt-0">
            <div class="table-responsive">
                <table id="tablaComun" class="table align-middle table-row-dashed fs-6 gy-5">
                    <thead>
                        <tr class="text-start text-gray-700 fw-bold fs-6 text-uppercase">
                            <th>Nombre</th>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?= htmlspecialchars($usuario['Nombre']) ?></td>
                            <td><?= htmlspecialchars($usuario['usuario']) ?></td>
                            <td>
                                <span class="badge 
                                    <?php
                                        switch ($usuario['rol']) {
                                            case '1': echo 'badge-primary'; break;
                                            case '2': echo 'badge-light'; break;
                                            case '3': echo 'badge-success'; break;
                                            default:  echo 'badge-danger'; break;
                                        }
                                    ?>">
                                    <?= htmlspecialchars($usuario['rol_nombre'] ?? 'Desconocido') ?>
                                </span>
                            </td>
                            <td nowrap>
                                <a href="usuarioform.php?id=<?= $usuario['id'] ?>" class="btn btn-icon btn-sm btn-warning me-2" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="?action=delete&id=<?= $usuario['id'] ?>" class="btn btn-icon btn-sm btn-danger" title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar este usuario?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!--end::Card body-->
    </div>
</div>


<?php include 'footer.php'; ?>