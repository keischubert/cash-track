<?php
require_once fullPath("Repository.php");

$repository = new Repository();

//accediendo a los query parameters si este ejecuto ajax. De lo contrario los parametros seran vacios.
$dateStart = $_GET["dateStart"] ?? "";
$dateEnd = $_GET["dateEnd"] ?? "";
 
$data = [
    "income" => $repository->getTotalAmount($dateStart . " 00:00:00", $dateEnd . " 23:59:59", 1),
    "expense" => $repository->getTotalAmount($dateStart . " 00:00:00", $dateEnd . " 23:59:59", 2)
];

//manejando solicitudes http y ajax. Estos se diferencian por los parametros de rutas.
if($dateStart === "" && $dateEnd === ""){
    view("balance/balance.view.php");
}

else{
    header('Content-Type: application/json');
    echo json_encode($data);
}