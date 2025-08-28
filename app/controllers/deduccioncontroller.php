<?php

require_once __DIR__ . '/../models/dbconexion.php';
require_once __DIR__ . '/../models/deduccionmodel.php';

class Deduccioncontroller{
    public $model;

    public function __construct(){
        $this->model = new deduccionmodel();

        if (isset($_GET['action'])) {
            switch ($_GET['action']) {
            
                case 'delete':
                    if (isset($_GET['id'])) {
                        $this->delete($_GET['id']);
                    }
                    break;
            }
        }
    }

    function log_accion($id_usuario, $accion, $id_acciones) {
        return $this->model->log_accion($id_usuario, $accion, $id_acciones);
    }

    public function getAllPensiones(){
        return $this->model->getAllPensiones();
    }

    public function delete($id) {
        $ok = $this->model->deletePension($id);

        $usuario = $_SESSION['user_id'] ?? 0;
        $id_acciones = "ID Pensión= {$id}";
        $this->log_accion($usuario, "Eliminó pensión.", $id_acciones);

        if ($ok) {
            header("Location: pension.php");
        } else {
            header("Location: pension.php");
        }
        exit;
    }
    
}