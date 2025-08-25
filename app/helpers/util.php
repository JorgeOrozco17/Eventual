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


