<?php 
require_once __DIR__ . '/../models/dbconexion.php';
require_once __DIR__ . '/../models/reportesmodel.php';

class ReportesController{
    public $model;

    public function __construct(){
        $this->model = new ReportesModel();
    }

    public function getAltasBajasByQuincena($qna, $anio, $tipo){
        return $this->model->getAltasBajasByQuincena($qna, $anio, $tipo);
    }

    public function getAltasBajasByQuincenaEstatus($qna, $anio, $estatus, $tipo){
        return $this->model->getAltasBajasByQuincenaEstatus($qna, $anio, $estatus, $tipo);
    }

    public function getAltasBajasByPeriodo($qnaInicio, $qnaFin, $tipo) {
        return $this->model->getAltasBajasByPeriodo($qnaInicio, $qnaFin, $tipo);
    }

    public function getAltasBajasByPeriodoEstatus($qnaInicio, $qnaFin, $estatus, $tipo) {
        return $this->model->getAltasBajasByPeriodoEstatus($qnaInicio, $qnaFin, $estatus, $tipo);
    }

}

?>