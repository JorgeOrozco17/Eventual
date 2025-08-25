<?php

require_once __DIR__ . '/../models/dbconexion.php';
require_once __DIR__ . '/../models/personalmodel.php';
require_once __DIR__ . '/../models/catalogomodel.php';

class PersonalController {
    public $model;

    public function __construct() {
        $this->model = new PersonalModel();

        // Validar si se pidió una acción
        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'save':
                    $this->save();
                    break;
                case 'delete':
                    if (isset($_GET['id'])) {
                        $this->delete($_GET['id']);
                    }
                    break;
                case 'get':
                    if (isset($_GET['id'])) {
                        $this->get($_GET['id']);
                    }
                    break;
                    case 'getByCurp':
                    if (isset($_GET['curp'])) {
                        $this->getByrfc($_GET['curp']);
                    }
                    break;
                case 'baja':
                    $this->BajaPersonal();
                    break; 
            }
        }
    }

    public function getAll() {
        return $this->model->getAll();
    }

    public function getEmpleadosByJurisdiccion($jurisdiccion) {
        return $this->model->getEmpleadosByJurisdiccion($jurisdiccion);
    }

    public function getAutorizados(){
        return $this->model->getAutorizados();
    }

    public function getNoAutorizados(){
        return $this->model->getNoAutorizados();
    }

    public function edit($id) {
        return $this->model->getById($id);
    }

    public function save() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /eventual/personalform.php?error=1");
            exit();
        }

        try {
            $data = $_POST;

            if ($this->model->existsRFC($data['RFC'], $data['movimiento'])) {
                header("Location: /eventual/altapersonal.php?duplicate=1");
                exit();
            }

            if ($this->model->existsCURP($data['CURP'], $data['movimiento'])) { // Corregido CRUP => CURP
                header("Location: /eventual/altapersonal.php?duplicate=1");
                exit();
            }

            $id_personal = $this->model->save($data);

            if ($id_personal) {
                $this->model->CalculoPersonal($id_personal);
                header("Location: /eventual/autorizapersonal.php?success=1");
            } else {
                header("Location: /eventual/personal.php");
            }
        } catch (Exception $e) {
            error_log("Error al guardar personal: " . $e->getMessage());
            header("Location: /eventual/personal.php?error=1");
        }

        exit();
    }

    public function delete($id) {
        $this->model->delete($id);
        header("Location: /eventual/altapersonal.php?deleted=1");
        exit();
    }

    public function get($id) {
        $data = $this->model->obtenerPorId($id);
        echo json_encode($data);
        exit;
    }

    public function getPersonalById($id) {
        return $this->model->getById($id);
    }

    public function getComentsById($id) {
        return $this->model->getComentsById($id);
    }

    public function getByrfc($rfc) {
    $data = $this->model->getAllbyrfc($rfc);

    if (!$data) {
        echo json_encode(null);
        exit;
    }

    $catalogo = new CatalogoModel();
    $juris = $catalogo->getJurisdiccionById($data['id_adscripcion']);

    echo json_encode([
        'id' => $data['id'],
        'nombre' => $data['nombre_alta'],
        'curp' => $data['CURP'],
        'centro' => $data['centro'],
        'jurisdiccion' => $data['adscripcion'] . ' - ' . $juris['ubicacion'],
    ]);
    exit;
}


public function BajaPersonal() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: /eventual/bajaform.php?error=1");
        exit();
    }
    try {
            $data = $_POST;

            $ok = $this->model->updateBaja($data);

            if ($ok) {
                header("Location: /eventual/autorizapersonal.php?success=1");
            } else {
                header("Location: /eventual/personal.php");
            }
        } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
        exit();
    }   

        exit();
    }


    public function completeEmployee($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /eventual/completar_empleado.php?error=1");
            exit();
        }

        try {
            $data = $_POST;

            $id_personal = $this->model->completeEmployee($id, $data);

            if ($id_personal) {
                header("Location: /eventual/contratos.php");
            } else {
                header("Location: /eventual/contratos.php");
            }
        } catch (Exception $e) {
            error_log("Error al completar empleado: " . $e->getMessage());
            header("Location: /eventual/completar_empleado.php?error=1");
        }

        exit();
    }
}
