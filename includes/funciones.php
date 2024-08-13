<?php

function debuguear($variable, $die = true)   {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    if(!$die)
        exit;
}

// Escapa / Sanitizar el HTML
function s($html) : string {
    $s = htmlspecialchars($html);
    return $s;
}

// Funcion que revisa si el usuario está autenticado
function isAuth() : void{
    if(!isset($_SESSION["login"])){
        header("Location: /");
    }
}
function isAdmin() : void {
    if(!isset($_SESSION["admin"])){
        // debuguear($_SESSION,false);
        header("Location: /");
    }
}

function esUltimo(string|int $actual, string $proximo): bool {
    if($actual != $proximo){
        return true;
    } else {
        return false;
    }
}