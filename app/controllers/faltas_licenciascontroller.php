<?php
require_once __DIR__ . '/../models/dbconexion.php';
require_once __DIR__ . '/../models/faltas_licenciasmodel.php';

class Faltas_licenciascontroller {

    public $model;

    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $this->model = new Faltas_licenciasmodel();

        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
                case 'save':
                    $this->save();
                    break;
                case 'delete':
                    if (isset($_GET['id'])) {
                        $this->delete((int)$_GET['id']);
                    }
                    break;
            }
        }
    }

    public function getAllFaltas(){
        return $this->model->getAllFaltas();
    }

    public function getAllLicencias(){
        return $this->model->getAllLicencias();
    }

    public function getDatosById($id){
        return $this->model->getById($id);
    }

    public function getFaltasByQuincena($quincena, $anio){
        return $this->model->getFaltasByQuincena($quincena, $anio);
    }

    public function getFaltasByAnio($anio){
        return $this->model->getFaltasByAnio($anio);
    }

    public function getLicenciasByQuincena($quincena, $anio){
        return $this->model->getLicenciasByQuincena($quincena, $anio);
    }

    public function save() {
        $redir_tipo = $_GET['tipo'] ?? 'licencia';
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $diasPost = isset($_POST['dias']) ? (int)$_POST['dias'] : 0;

        $data = [
            'id'           => isset($_POST['id']) ? (int)$_POST['id'] : null,
            'id_personal'  => (int)$_POST['id_personal'],
            'nombre'       => trim($_POST['nombre']),
            'adscripcion'  => trim($_POST['adscripcion']),
            'jurisdiccion' => trim($_POST['jurisdiccion']),
            'dias'         => $diasPost,
            'fechas'       => $_POST['fechas'] ?? '',
            'periodo_1'    => $_POST['periodo_1'] ?? null,
            'periodo_2'    => $_POST['periodo_2'] ?? null,
            'observaciones'=> $_POST['observaciones'] ?? '',
            'id_usuario'   => (int)$_POST['id_usuario'],
            'quincena'     => (int)$_POST['quincena'],
            'tipo'         => $_POST['tipo'] === 'falta' ? 'falta' : 'licencia',
            'faltas'       => 0 // se calculará
        ];

        // Guardar/actualizar
        if ($data['id']) {
            $okSave = $this->model->update($data);
            $recordId = $data['id'];
        } else {
            $recordId = $this->model->save($data); // devuelve ID
            $okSave = $recordId > 0;
        }

        $faltasGeneradasEnteras = 0;
        $okCalc = true;

        if ($okSave && $data['tipo'] === 'licencia' && $data['dias'] > 0) {
            $calc = $this->model->calculodias($data['dias'], $data['id_personal']);
            $okCalc = $calc['ok'];

            if ($okCalc) {
                // 1) Guardar responsabilidades (faltas netas) en la columna `responsabilidades`
                $this->model->updateResponsabilidades($recordId, (float)$calc['responsabilidades']);
            }
        }

        // Si el usuario registró directamente una FALTA → actualizar columna `faltas`
        if ($okSave && $data['tipo'] === 'falta') {
            $this->model->updateFaltasColumn($recordId, (float)$data['dias']);
        }

        // Log y salida
        $usuario = $_SESSION['user_id'] ?? 0;
        $anio = date('Y');
        $id_acciones = "ID={$recordId}, QNA={$data['quincena']}, Año={$anio}";
        $this->log_accion($usuario, "Se registró {$data['tipo']} (faltas enteras auto: {$faltasGeneradasEnteras}).", $id_acciones);

        $okAll = $okSave && $okCalc;
        if ($redir_tipo === 'falta') {
            header("Location: /eventual/incidencias.php?tipo=falta&" . ($okAll ? "success=1" : "error=1"));
        } else {
            header("Location: /eventual/licencias.php?tipo=licencia&" . ($okAll ? "success=1" : "error=1"));
        }
        exit();
    }


    public function delete($id){
        $tipo = $_GET['tipo'] ?? 'licencia';

        $ok = $this->model->delete($id);

        if (!$ok) {
            $dest = ($tipo === 'falta') ? 'incidencias' : 'licencias';
            header("Location: /eventual/{$dest}.php?tipo={$tipo}&error=1");
            exit();
        }

        $dest = ($tipo === 'falta') ? 'incidencias' : 'licencias';
        header("Location: /eventual/{$dest}.php?tipo={$tipo}&deleted=1");
        exit();
    }

    /////////////////////// logs ///////////////////////

    public function log_accion($id_usuario, $accion, $id_acciones) {
        return $this->model->log_accion($id_usuario, $accion, $id_acciones);
    }
}
