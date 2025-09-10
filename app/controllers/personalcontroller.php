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
        if (!($_SERVER['REQUEST_METHOD'] === 'POST')) {
            header("Location:/personalform.php");
            exit();
        }
        $data = $_POST;

        try {
            // aquí NO sobrescribas $data con $_POST otra vez!
            if ($this->model->existsRFC($data['RFC'], $data['movimiento'])) {
                return false; // mejor regresas false en vez de redirigir
            }

            if ($this->model->existsCURP($data['CURP'], $data['movimiento'])) {
                return false;
            }

            $id_personal = $this->model->save($data);

            if ($id_personal) {
                $this->model->CalculoPersonal($id_personal);
                header("Location:/autorizapersonal.php?success=1");
                exit();
            } else {
                return false;
            }
        } catch (Exception $e) {
            error_log("Error al guardar personal: " . $e->getMessage());
            return [ 'success' => false, 'error' => $e->getMessage() ];
        }
        
    }

    public function AltaBajaPersonalSave($data) {
        try {
            // aquí NO sobrescribas $data con $_POST otra vez!
            if ($this->model->existsRFC($data['RFC'], $data['movimiento'])) {
                return false; // mejor regresas false en vez de redirigir
            }

            if ($this->model->existsCURP($data['CURP'], $data['movimiento'])) {
                return false;
            }

            $id_personal = $this->model->saveAltaBaja($data);

            if ($id_personal) {
                $this->model->CalculoPersonal($id_personal);
                return $id_personal;
            } else {
                return false;
            }
        } catch (Exception $e) {
            error_log("Error al guardar personal: " . $e->getMessage());
            return [ 'success' => false, 'error' => $e->getMessage() ];
        }
    }


    public function delete($id) {
        $this->model->delete($id);

        $referer = $_SERVER['HTTP_REFERER'] ?? '/altapersonal.php';
        header("Location: $referer");
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
        header("Location:/bajaform.php");
        exit();
    }
    try {
            $data = $_POST;

            $ok = $this->model->updateBaja($data);

            if ($ok) {
                header("Location:/autorizapersonal.php");
            } else {
                header("Location:/personal.php");
            }
        } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
        exit();
    }   

        exit();
    }

    public function AltaBajaPersonal($data) {
        try {
            $ok = $this->model->updateBaja($data);
            return $ok; // devolvemos true/false en lugar de cortar el flujo
        } catch (Exception $e) {
            error_log("Error en AltaBajaPersonal: " . $e->getMessage());
            return false;
        }
    }



    public function completeEmployee($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location:/completar_empleado.php");
            exit();
        }

        try {
            $data = $_POST;

            $id_personal = $this->model->completeEmployee($id, $data);

            if ($id_personal) {
                header("Location:/contratos.php");
            } else {
                header("Location:/contratos.php");
            }
        } catch (Exception $e) {
            error_log("Error al completar empleado: " . $e->getMessage());
            header("Location:/completar_empleado.php");
        }

        exit();
    }

    public function altaxbaja(){
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location:/altaxbaja.php");
            exit();
        }

        try{

            $data = $_POST;

            $ok = $this->model->altaxbaja($data);

            if ($ok) {
                $this->model->save($data);
            } else {
                header("Location:/personal.php");
            }
        } catch (Exception $e) {
            error_log("Error al dar de alta o baja: " . $e->getMessage());
            header("Location:/altaxbaja.php");
        }
        
    }

    public function getPersonalNew($qna, $anio) {
    $data = $this->model->getPersonalNew($qna, $anio);

    // Depuración: imprime en log
    error_log("getPersonalNew en Controller (qna=$qna, anio=$anio): " . print_r($data, true));

    return $data;
}
}
