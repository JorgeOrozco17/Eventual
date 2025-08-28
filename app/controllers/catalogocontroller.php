<?php

require_once __DIR__ . '/../models/dbconexion.php';
require_once __DIR__ . '/../models/catalogomodel.php';

class CatalogoController {
    public $model;

    public function __construct() {
        $this->model = new CatalogoModel();
    }

    //              Jurisdicciones
    public function getAllJurisdicciones() {
        return $this->model->getAllJurisdicciones();
    }

    public function getJurisdiccionById($id) {
        return $this->model->getJurisdiccionById($id);
    }

    public function saveJuris() {
        try{
            $data = $this->sanitizeInput($_POST);

        $ok = $this->model->saveJuris($data);

        if ($ok) {
                header("Location:/juris.php");
            } else {
                header("Location:/juris.php");
            }
        } catch (Exception $e) {
            header("Location:/juris.php");
        }

        exit();
    }

    private function sanitizeInput($input) {
        return [
            'id' => $input['id'] ?? null,
            'nombre' => trim(htmlspecialchars($input['nombre'] ?? '')),
            'ubicacion' => trim(htmlspecialchars($input['ubicacion'] ?? '')),
        ];
    }    


    public function updateJuris() {
        try {
            $data = $this->sanitizeInput($_POST);
            $ok = $this->model->updateJuris($data['id'], $data['nombre'], $data['ubicacion']);
    
            if ($ok) {
                header("Location:/juris.php?updated=1");
            } else {
                header("Location:/juris.php");
            }
        } catch (Exception $e) {
            header("Location:/juris.php");
        }
        exit();
    }
    

    public function deleteJurisdiccion($id) {
        return $this->model->deleteJurisdiccion($id);
    }

    //              Fin Jurisdiccciones

    //              Centros
    public function getAllCentros() {
        return $this->model->getAllCentros();
    }

    public function getCentrosByAdscripcion($adscrip_id){
        return $this->model->getCentrosByAdscripcion($adscrip_id);
    }

    //             Fin Centros

    //             Recursos
    public function getAllRecursos(){
        return $this->model->getAllRecursos();
    }

    //

    //             puestos
    public function getAllPuestos(){
        return $this->model->getAllPuestos();
    }

    //           fin puestos

    //          Quincenas
    public function getAllQuincenas() {
        return $this->model->getAllQuincenas();
    }

    public function getQuincenaByDate($date) {
        return $this->model->getQuincenaByDate($date);
    }

    //         fin quincenas

    //              archivos

    public function getArchivosById($id) {
        return $this->model->getArchivosById($id);
    }

    //              fin archivos

    //               conceptos fijos
    public function getAllFijas(){
        return $this->model->getAllFijas();
    }

    public function getFijosById($id){
        return $this->model->getFijosById($id);
    }
    
    public function saveFijo() {
        try {
            $data = $this->sanitizeFijo($_POST);
            $ok = $this->model->insertFijo($data);

            if ($ok) {
                header("Location:/fijos.php?");
            } else {
                header("Location:/fijos.php?");
            }
        } catch (Exception $e) {
            header("Location:/fijos.php?");
        }
        exit;
    }

    public function updateFijo() {
        try {
            $data = $this->sanitizeFijo($_POST);
            $ok = $this->model->updateFijo($data);

            if ($ok) {
                header("Location:/fijos.php?");
            } else {
                header("Location:/fijos.php?");
            }
        } catch (Exception $e) {
            header("Location:/fijos.php?");
        }
        exit;
    }

    private function sanitizeFijo($input) {
        return [
            'id' => $input['id'] ?? null,
            'concepto' => trim(htmlspecialchars($input['concepto'] ?? '')),
            'nombre_concepto' => trim(htmlspecialchars($input['nombre'] ?? '')),
            'cantidad' => floatval($input['cantidad'] ?? 0)
        ];
    }

    public function deleteFijo($id){
        return $this->model->deleteFijo($id);
    }

}
