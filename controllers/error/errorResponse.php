<?php

$statusCode = [
    "0" => "Ha ocurrido un error",
    "400" => "BadRequest",
    "404" => "NotFound",
    "500" => "ServerError"
];

$uriParts = explode("/", $_SERVER["REQUEST_URI"]);
$uriCode = $uriParts[2] ?? "0";

$data = [
    "responseCode" => $uriCode,
    "responseMessage" => $statusCode[$uriCode]
];

view("errorPage.view.php", $data);