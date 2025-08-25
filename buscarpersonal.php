<?php 
require_once 'app/controllers/personalcontroller.php';

if (isset($_GET['rfc'])){
    $controller = new PersonalController();
    $controller->getByrfc($_GET['rfc']);
}

