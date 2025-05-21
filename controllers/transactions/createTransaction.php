<?php
require fullPath("Repository.php");
$repository = new Repository();

$response = [];

//extraccion de los datos del form.
$formData = [
    "transactionTypeId" => (int)$_POST["transactionTypeId"],
    "amount" => (float)$_POST["amount"],
    "dateTime" => $_POST["dateTime"],
    "description" => $_POST["description"],
    "moneyAccountId" => (int)$_POST["moneyAccountId"]
];

//validaciones del servidor
//validar existencia del transactionType
if(!$repository->transactionTypeExists($formData["transactionTypeId"])){
    return sendResponse(400, "Ha ocurrido un error con el tipo de transaccion");
}

//validacion que el monto sea de tipo numerico
if(!is_numeric($formData["amount"])){
    return sendResponse(400, "Ha ocurrido un error con el monto. No es valido.");
}

//validacion montos negativos
if($formData["amount"] <= 0){
    return sendResponse(400, "Ha ocurrido un error con el monto. No puede ser negativo");
}

// validar fecha vacia
if($formData["dateTime"] === ''){
    return sendResponse(400, "Ha ocurrido un error con la fecha. No puede ser vacio.");
}

//validar formato de fecha y los valores de la fecha (2025-45-54)
$dateFormated = DateTime::createFromFormat("Y-m-d", $formData["dateTime"]);
if(!$dateFormated){
    return sendResponse(400, "Ha ocurrido un error con la fecha. Fecha no valida");
}

// validar existencia del moneyAccount
if(!$repository->moneyAccountExists($formData["moneyAccountId"])){
    return sendResponse(400, "Ha ocurrido un error con la cuenta monetaria");
}

//validar longitud de la descripcion
if(strlen($formData["description"]) > 255){
    return sendResponse(400, "Ha ocurrido un error con la descripcion. Este no puede exceder los 255 caracteres");
}

//actualizar el dinero de las cuentas monetarias segun el tipo de transaccion
$updateMoneyAccount =  $repository->updateMoney($formData["amount"], $formData["moneyAccountId"], $formData["transactionTypeId"]);

if(!$updateMoneyAccount){
    view("errorPage.view.php");
}

//Guardar la nueva transaccion en la db
$insertResult = $repository->insertTransaction($formData);
if(!$insertResult){
    view("errorPage.view.php");
}

return sendResponse(200, "La transacción se registro correctamente");