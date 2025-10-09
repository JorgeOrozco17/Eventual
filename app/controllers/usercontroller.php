<?php
require_once __DIR__ . '/../helpers/util.php';
require_once __DIR__ . '/../models/dbconexion.php';
require_once __DIR__ . '/../models/usermodel.php';


class UserController {
    public $model;

    public function __construct() {
        $this->model = new UserModel();

        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'save':
                    $this->save();
                    break;
            }
        }
    }

    public function getAll() {
        return $this->model->getAll();
    }

    public function getById($id) {
        return $this->model->getById($id);
    }

    public function edit($id) {
        return $this->model->getById($id);
    }

    public function save() {
        try {
            $data = $this->sanitizeInput($_POST);
            $data['archivo'] = $this->handleFileUpload($_FILES, $_POST);

            if (empty($data['id'])) {
                if( $this->model->existByUsuario($data['usuario'])) {
                    header("Location:/nuevousuario.php?duplicate");
                    exit();
                }
            }

            $ok = $this->model->save($data);

            if ($ok) {
                header("Location:/nuevousuario.php?");
            } else {
                header("Location:/nuevousuario.php");
            }
        } catch (Exception $e) {
            header("Location:/nuevousuario.php?error=");
        }

        exit();
    }

    public function delete($id) {
        $this->model->delete($id);
        header("Location:/nuevousuario.php");
        exit();
    }

    private function sanitizeInput($input) {
        return [
            'id' => $input['id'] ?? null,
            'Nombre' => trim(htmlspecialchars($input['Nombre'] ?? '')),
            'RFC' => trim(htmlspecialchars($input['RFC'] ?? '')),
            'usuario' => trim(htmlspecialchars($input['usuario'] ?? '')),
            'contraseña' => $input['contraseña'] ?? '',
            'juris' => $input['juris'] ?? '',
            'rol' => $input['rol'] ?? '',
        ];
    }

    private function handleFileUpload($files, $post) {
        if (isset($files['archivo']) && $files['archivo']['error'] === UPLOAD_ERR_OK) {
            $permitidos = ['application/pdf', 'image/jpeg', 'image/png'];
            if (!in_array($files['archivo']['type'], $permitidos)) {
                return '';
            }
            $nombre = uniqid() . '_' . basename($files['archivo']['name']);
            $ruta = __DIR__ . '/../fotos/' . $nombre;
            if (!move_uploaded_file($files['archivo']['tmp_name'], $ruta)) {
                return '';
            }
            return $nombre;
        } elseif (!empty($post['archivo_actual'])) {
            return $post['archivo_actual'];
        }
        return '';
    }

    public function getRolbyId($id){
        return $this->model->getRolbyId($id);
    }

    public function getPaginas() {
        return $this->model->getPaginas();
    }

    public function getAllRoles() {
        return $this->model->getAllRoles();
    }   

     public function getPermisosPorRol($rol_id) {
        return $this->model->getPermisosPorRol($rol_id);
    }

    public function getPermisosPorUsuario($id_usuario) {
        return $this->model->getPermisosPorUsuario($id_usuario);
    }

    public function guardarPermisosPorRol($rol_id, $paginas) {
        return $this->model->guardarPermisosRol($rol_id, $paginas);
    }

    public function guardarPermisosPorUsuario($id_usuario, $paginas) {
        return $this->model->guardarPermisosUsuario($id_usuario, $paginas);
    }

    public function getPaginaIdByArchivo($archivo) {
        return $this->model->getPaginaIdByArchivo($archivo);
    }

    public function usuarioTienePermiso($user_id, $id_pagina) {
        return $this->model->usuarioTienePermiso($user_id, $id_pagina);
    }

    public function rolTienePermiso($rol_id, $id_pagina) {
        return $this->model->rolTienePermiso($rol_id, $id_pagina);
    }


    public function getResponsablesByRH($responsable){
        return $this->model->getResponsablesByRH($responsable);
    }

    public function getAllResponsables(){
        return $this->model->getAllResponsables();
    }   

    public function getRespobsableByJurisdiccion($user_id){
        return $this->model->getRespobsableByJurisdiccion($user_id);
    }


    //////////////////////////////////// actualizar usuario //////////////////////


    public function actualizarCuenta($id, $data, $files) {
        $usuario = trim($data['usuario'] ?? '');
        $password_actual = $data['password_actual'] ?? '';
        $password_nueva = $data['password_nueva'] ?? '';
        $password_confirmar = $data['password_confirmar'] ?? '';

        // Validar nombre de usuario
        if (empty($usuario)) {
            return ['success' => false, 'message' => 'El nombre de usuario no puede estar vacío.'];
        }

        // Obtener datos actuales
        $actual = $this->model->getById($id);
        if (!$actual) {
            return ['success' => false, 'message' => 'Usuario no encontrado.'];
        }

        $passwordActual = $actual['contraseña'];
        $nuevaPassword = $passwordActual; // Por defecto se conserva la actual

        // === Cambio de contraseña ===
        if (!empty($password_actual) || !empty($password_nueva) || !empty($password_confirmar)) {

            if (empty($password_actual) || empty($password_nueva) || empty($password_confirmar)) {
                return ['success' => false, 'message' => 'Debes llenar todos los campos de contraseña.'];
            }
            if ($password_actual !== $passwordActual) {
                return ['success' => false, 'message' => 'La contraseña actual no coincide.'];
            }
            if ($password_nueva !== $password_confirmar) {
                return ['success' => false, 'message' => 'Las contraseñas nuevas no coinciden.'];
            }
            $nuevaPassword = $password_nueva;
        }

        // === Manejo de foto de perfil ===
        $fotoActual = $actual['archivo'];
        $fotoFinal = $fotoActual;

        if (!empty($files['foto']['name'])) {
            $ext = strtolower(pathinfo($files['foto']['name'], PATHINFO_EXTENSION));
            $permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (!in_array($ext, $permitidas)) {
                return ['success' => false, 'message' => 'Formato de imagen no permitido. Usa JPG, PNG o WEBP.'];
            }

            $nuevoNombre = 'perfil_' . $id . '_' . time() . '.' . $ext;
            $rutaDestino = 'uploads/perfiles/' . $nuevoNombre;

            if (!is_dir('uploads/perfiles/')) {
                mkdir('uploads/perfiles/', 0777, true);
            }

            if (move_uploaded_file($files['foto']['tmp_name'], $rutaDestino)) {
                // Eliminar foto anterior
                if (!empty($fotoActual) && file_exists('uploads/perfiles/' . $fotoActual)) {
                    unlink('uploads/perfiles/' . $fotoActual);
                }
                $fotoFinal = $nuevoNombre;
            } else {
                return ['success' => false, 'message' => 'No se pudo subir la foto.'];
            }
        }

        // === Actualizar datos en la base de datos ===
        $ok = $this->model->updateCuenta($id, $usuario, $nuevaPassword, $fotoFinal);

        if ($ok) {
            $_SESSION['usuario'] = $usuario;
            $_SESSION['foto'] = $fotoFinal;
            return ['success' => true, 'message' => 'Datos actualizados correctamente.'];
        } else {
            return ['success' => false, 'message' => 'No se pudo actualizar la cuenta.'];
        }
    }


}
