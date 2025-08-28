<?php
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

}
