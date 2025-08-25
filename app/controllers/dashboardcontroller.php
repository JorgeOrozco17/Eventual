<?php
require_once __DIR__ . '/../models/dashboardmodel.php';
require_once __DIR__ . '/../controllers/catalogocontroller.php';

class DashboardController {
    private $model;
    private $catalogo;

    public function __construct() {
        $this->model = new DashboardModel();
        $this->catalogo = new CatalogoController();
    }

    public function getData() {
        // Quincena actual
        $today = new DateTime('now');
        $month = (int)$today->format('n');
        $day   = (int)$today->format('j');
        $qnaActual  = ($day <= 15) ? ($month * 2 - 1) : ($month * 2); 
        $anioActual = (int)$today->format('Y');

        return [
            'empleadosActivos' => $this->model->getEmpleadosActivos(),
            'licenciasActuales' => $this->model->getLicenciasActuales($qnaActual, $anioActual),
            'serieNomina' => $this->model->getSerieNomina(),
            'serieFaltasLic' => $this->model->getSerieFaltasLic(),
            'pieAdscripciones' => $this->model->getPieAdscripciones(),
            'ultimosMovs' => $this->model->getUltimosMovs(),
            'quincenas' => $this->catalogo->getAllQuincenas(),
            'jurisdicciones' => $this->catalogo->getAllJurisdicciones(),
            'anios' => range($anioActual - 1, $anioActual + 1),
            'qnaActual' => $qnaActual,
            'anioActual' => $anioActual,
        ];
    }
    
}
