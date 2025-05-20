<?php
require_once fullPath("Repository.php");

$repository = new Repository();

$inputSearch = $_GET["search"] ?? "";

$data = [
    "transactions" => $repository->getFilteredTransactions(["inputSearch" => $inputSearch])
];

//Manejar solicitudes http y ajax diferenciandolos con quey parameters.
if($inputSearch === ""){
    view("transactions/getTransactions.view.php", $data);
}
else{
    header('Content-Type: application/json');
    echo json_encode($data);
}