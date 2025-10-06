<?php 
require_once __DIR__ . '/../models/dbconexion.php';
require_once __DIR__ . '/../models/capturamodel.php';
require_once __Dir__ . '/../models/catalogomodel.php';

class Capturacontroller{
    public $model;

    public function __construct(){
        if (session_status() == PHP_SESSION_NONE) session_start();
        $this->model  = new Capturamodel();
    }

    public function getAll(){
        // Solo consulta, no log necesario
        return $this->model->getAll();
    }

    public function getNominaById($id){
        // Solo consulta, no log necesario
        return $this->model->getNominaById($id);
    }

    public function save() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_GET['id'];
            $qna = $_GET['qna'];
            $anio = $_GET['anio'];
            $id_nomina = $_GET['id_n'];

            $fields = [
                'D_01', 'D_04', 'D_05', 'D_62', 'D_64', 'D_65', 'D_R1', 'D_R2', 'D_R3', 'D_R4',
                'D_AS', 'D_AM', 'D_S1', 'D_S2', 'D_S4', 'D_S5', 'D_S6', 'D_O1', 'P_00', 'P_01', 
                'P_06', 'P_26'
            ];

            $data = [];
            foreach ($fields as $field) {
                $data[$field] = ($_POST[$field] !== '' ? $_POST[$field] : null);
            }

            $ok = $this->model->updateCapturaManual($id, $data);

            $usuario = $_SESSION['user_id'] ?? 0;
            $id_acciones = "ID= $id, QNA= $qna, Año= $anio";
            $this->log_accion($usuario, "Actualizó captura manual.", $id_acciones);

            if ($ok) {
                $this->model->calcularTotales($id_nomina, $qna, $anio);

                $this->log_accion($usuario, "Calculó totales de captura.", $id_acciones);

                header("Location: captura.php?&id=" . urlencode($id_nomina));
            } else {
                header("Location: captura.php?&id=" . urlencode($id_nomina));
            }
            exit;
        }
    }

    //////////////////// Funciones para nomina.php ////////////////////////////

    public function getAllNomina(){
        // Solo consulta, no log necesario
        return $this->model->getAllNomina();
    }

    public function deleteNomina($id, $qna, $anio){
        $ok = $this->model->deleteNomina($id);

        $usuario = $_SESSION['user_id'] ?? 0;
        $id_acciones = "ID_NOMINA= $id, QNA= $qna, Año= $anio";
        $this->log_accion($usuario, "Eliminó nómina.", $id_acciones);

        if ($ok) {
            $this->model->deleteCaptura($qna, $anio);

            $this->log_accion($usuario, "Eliminó captura asociada a la nómina.", $id_acciones);
        }

        return $ok;
    }

    ////////////////////////////////// Generar nomina ////////////////////////////////

    public function saveNomina($tipo, $quincena, $anio){
        $usuario = $_SESSION['user_id'];
        $id_acciones = "QNA= $quincena, Año= $anio";

        if ($this->model->nominaExistente($quincena, $anio)) {
            $this->log_accion($usuario, "Intentó crear nómina pero ya existe.", $id_acciones);
            return 'existe';
        }

        $id_nomina = $this->model->insertNomina($tipo, $quincena, $anio);
        

        if ($id_nomina) {
            $this->model->generarCaptura($quincena, $anio, $id_nomina);

            }
        $this->log_accion($usuario, "Insertó nueva nómina.", $id_acciones);
        return true ? 'exito' : 'error';    
    }

    public function saveNominaExtra($tipo, $quincena, $anio){
       $usuario = $_SESSION['user_id'];
        $id_acciones = "QNA= $quincena, Año= $anio";

        if (!$this->model->nominaExistente($quincena, $anio)) {
            $this->log_accion($usuario, "Intentó crear nómina Extraordinaria pero no existe una nómina ordinaria", $id_acciones);
            return 'no existe';
        }

        $id_nomina = $this->model->insertNomina($tipo, $quincena, $anio);
        

        if ($id_nomina) {
            $this->model->generarCapturaExtra($quincena, $anio, $id_nomina);

            }
        $this->log_accion($usuario, "Insertó nueva nómina.", $id_acciones);
        return true ? 'exito' : 'error'; 
    }

    public function insertartotales($id_nomina, $qna, $anio){
         $ok = $this->model->calcularTotales($id_nomina, $qna, $anio);
         
        if ($ok){
            $this->model->insertartotales($id_nomina);
        }
        $usuario = $_SESSION['user_id'] ?? 0;
        $id_acciones = "ID NÓMINA= $id_nomina";
        $this->log_accion($usuario, "Insertó totales de nómina.", $id_acciones);
        return $ok;
    }

    //////////////////////////////// Generar excel ///////////////////////////////////////

    public function datosCaptura($id_nomina){
        return $this->model->datosCaptura($id_nomina);
    }

    /////////////////////////// generar recibos ///////////////////////////////

    public function getCapturaPorQuincena($quincena, $anio, $jurisdiccion){
        return $this->model->getCapturaPorQuincena($quincena, $anio, $jurisdiccion);
    }

    public function getCapturaPorQuincenaSinJurisdiccion($quincena, $anio){
        return $this->model->getCapturaPorQuincenaSinJurisdiccion($quincena, $anio);
    }

    ////////////////////////////////////////// logs///////////////////////////////////////////////////
    function log_accion($id_usuario, $accion, $id_acciones) {
        return $this->model->log_accion($id_usuario, $accion, $id_acciones);
    }




 /////////////////////////////////////////////// pension //////////////////////////////////////

    public function SavePension() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'id_personal' => $_POST['id_personal'] ?? '',
                'beneficiaria' => $_POST['beneficiaria'] ?? 0,
                'cuenta_beneficiaria' => $_POST['cuenta_beneficiaria'] ?? '',
                'porcentaje' => $_POST['porcentaje'] ?? 0,
                'id_usuario' => $_POST['id_usuario'] ?? 0
            ];

            $ok = $this->model->savePension($datos);

            $usuario = $_SESSION['user_id'] ?? 0;
            $id_acciones = "ID Empleado= {$datos['id_personal']}";
            $this->log_accion($usuario, "Guardó pensión.", $id_acciones);

            if ($ok) {
                header("Location: pension.php?");
            } else {
                header("Location: pension.php");
            }
            exit;
        }
    }
    
    public function getPensionById($id) {
        return $this->model->getPensionById($id);
    }

    public function UpdatePension() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {
            $id = $_POST['id'];
            $datos = [
                'beneficiaria' => $_POST['beneficiaria'] ?? '',
                'cuenta_beneficiaria' => $_POST['cuenta_beneficiaria'] ?? '',
                'porcentaje' => $_POST['porcentaje'] ?? 0,
                'id_usuario' => $_SESSION['user_id'] ?? 0
            ];
            $ok = $this->model->updatePension($id, $datos);

            $usuario = $_SESSION['user_id'] ?? 0;
            $id_acciones = "ID Pensión= {$id}";
            $this->log_accion($usuario, "Actualizó pensión.", $id_acciones);

            if ($ok) {
                header("Location: pension.php");
            } else {
                header("Location: pension.php");
            }
            exit;
        }
    }

    
////////////////////////////////////////////////////////////////// tabla totales

    public function getTablaTotales($qna, $anio, $programa, $rama){
        return $this->model->getTablaTotales($qna, $anio, $programa, $rama);
    }

}
