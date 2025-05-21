<?php

declare(strict_types=1);

//funcion para debuggear un valor
function varInfo($value){
    echo "<pre>";
    var_dump($value);
    echo "</pre>";
}


function fullPath(string $path){
    $pathExists = realpath(__DIR__ . "/../$path");

    if($pathExists !== false){
        return $pathExists;
    }

    echo "La ruta {'$path'} no existe";
}

function view($path, $data = null){

    require fullPath("views/" . $path);
}

function sendResponse(int $statusCode, string $message){
    header('Content-Type: application/json');

    $response = [
        'statusCode' => $statusCode,
        'message' => $message
    ];

    echo json_encode($response);
}

