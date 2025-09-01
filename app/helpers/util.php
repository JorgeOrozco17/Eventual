<?php 
function esc($v){ 
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); 
}

function pick(array $row, array $keys, $default=null){ 
    foreach($keys as $k){ 
        if(isset($row[$k]) && $row[$k] !== '') return $row[$k]; 
    } 
    return $default; 
}

/** Ruta raíz del proyecto (sube un nivel desde /helpers) */
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// Carpeta public
if (!defined('PUBLIC_PATH')) {
    define('PUBLIC_PATH', BASE_PATH . '/public');
}

// Carpeta de imágenes
if (!defined('IMG_PATH')) {
    define('IMG_PATH', PUBLIC_PATH . '/img');
}
