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

    public function getAllTemporales(){
        return $this->model->getAllTemporales();
    }
    

    public function getTemporalesbyId($id){
        return $this->model->getAllTemporalesbyId($id);
    }

    public function SaveTemporal() {
        try {
            $data = [
                'id_personal' => $_POST['id_personal'] ?? null,
                'concepto'    => $_POST['concepto'] ?? null,
                'monto_total' => $_POST['monto_total'] ?? 0,
                'monto'       => $_POST['monto'] ?? 0,
                'fecha_inicio'=> $_POST['fecha_inicio'] ?? null,
                'fecha_fin'   => $_POST['fecha_fin'] ?? null,
                'id_usuario'  => $_SESSION['user_id'] ?? 0
            ];

            $id = $this->model->SaveTemporal($data);

            // registro en bitácora
            $this->log_accion($data['id_usuario'], "Agregó deducción temporal.", "ID temporal={$id}");

            header("Location: temporales.php");
            exit;
        } catch (Exception $e) {
            die("Error al guardar: " . $e->getMessage());
        }
    }

    public function UpdateTemporal() {
        try {
            $data = [
                'id'          => $_POST['id'] ?? null,
                'id_personal' => $_POST['id_personal'] ?? null,
                'concepto'    => $_POST['concepto'] ?? null,
                'monto_total' => $_POST['monto_total'] ?? 0,
                'monto'       => $_POST['monto'] ?? 0,
                'fecha_inicio'=> $_POST['fecha_inicio'] ?? null,
                'fecha_fin'   => $_POST['fecha_fin'] ?? null,
                'id_usuario'  => $_SESSION['user_id'] ?? 0
            ];

            $ok = $this->model->UpdateTemporal($data);

            $this->log_accion($data['id_usuario'], "Actualizó deducción temporal.", "ID temporal={$data['id']}");

            header("Location: temporales.php");
            exit;
        } catch (Exception $e) {
            die("Error al actualizar: " . $e->getMessage());
        }
    }

}

?>