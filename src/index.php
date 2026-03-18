<?php
header('Content-Type: application/json');
include_once ("Url.php");
include_once("Controle.php");

$url = Url::getInstance();
$controle = new Controle();

if (!$url->authentification()){
    $controle->unauthorized();
}else{
    $methodeHTTP = $url->recupMethodeHTTP();
    $table = $url->recupVariable("table");
    $id = $url->recupVariable("id");
    $champs = $url->recupVariable("champs", "json");
    $controle->demande($methodeHTTP, $table, $id, $champs);
}