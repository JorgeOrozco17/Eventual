<?php 
require_once __DIR__ . '/../models/dbconexion.php';
require_once __DIR__ . '/../models/contratomodel.php';


class ContratoController {

    public $model;

    public function __construct() {
        $db = new DBConexion();
        $this->model = new ContratoModel();
    }

    public function getAllEmpleados() {
        return $this->model->getAllEmpleados();
    }

    public function getEmpleadoById($id) {
    return $this->model->getEmpleadoById($id);
    }

    public function getEmpleadosByJurisdiccion($jurisdiccionId) {
        return $this->model->getEmpleadosByJurisdiccion($jurisdiccionId);
    }

    public function getTrimestres(){
        return $this->model->getTrimestres();
    }
}

?>