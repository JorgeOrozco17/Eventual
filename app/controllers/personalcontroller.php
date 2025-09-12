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

    public function getAutorizados($responsable){
        return $this->model->getAutorizados($responsable);
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
                echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                <script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Registro exitoso',
                        text: 'El empleado ha sido registrado correctamente.',
                        showConfirmButton: false,
                        timer: 900,
                        background: '#fdfaf6',
                        color: '#333',
                        customClass: {
                            popup: 'rounded-4 shadow-lg'
                        }
                    }).then(() => {
                        window.location.href = 'autorizapersonal.php';
                    });
                </script>";
                exit();
            } else {
                return false;
            }
        } catch (Exception $e) {
            error_log("Error al guardar personal: " . $e->getMessage());
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                <script>
                    Swal.fire({
                        icon: 'danger',
                        title: 'Error al registrar',
                        text: 'Ocurrió un error al registrar al empleado.',
                        showConfirmButton: false,
                        timer: 900,
                        background: '#fdfaf6',
                        color: '#333',
                        customClass: {
                            popup: 'rounded-4 shadow-lg'
                        }
                    }).then(() => {
                        window.location.href = 'personalform.php';
                    });
                </script>";
                exit();
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
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                <script>
                    Swal.fire({
                        icon: 'danger',
                        title: 'Error al registrar',
                        text: 'Ocurrió un error al registrar al empleado.',
                        showConfirmButton: false,
                        timer: 900,
                        background: '#fdfaf6',
                        color: '#333',
                        customClass: {
                            popup: 'rounded-4 shadow-lg'
                        }
                    }).then(() => {
                        window.location.href = 'personalform.php';
                    });
                </script>";
                exit();
        }
    }


    public function delete($id) {
        $ok = $this->model->delete($id);

        if ($ok) {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Eliminado',
                    text: 'El empleado fue eliminado correctamente.',
                    showConfirmButton: false,
                    timer: 1200,
                    background: '#fdfaf6',
                    color: '#333',
                    customClass: { popup: 'rounded-4 shadow-lg' }
                }).then(() => {
                    window.location.href = 'altapersonal.php';
                });
            </script>";
        } else {
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo eliminar el empleado.',
                    showConfirmButton: true,
                    background: '#fdfaf6',
                    color: '#333',
                    customClass: { popup: 'rounded-4 shadow-lg' }
                }).then(() => {
                    window.location.href = 'altapersonal.php';
                });
            </script>";
        }
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
