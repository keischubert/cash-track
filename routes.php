<?php

$router->get([
    "uri" => "",   
    "controller" => "home",
    "action" => "home"
]);

$router->get([
    "uri" => "/transactions",   
    "controller" => "transactions",
    "action" => "getTransactions"
]);

$router->get([
    "uri" => "/transactions?search=value",   
    "controller" => "transactions",
    "action" => "getTransactions"
]);

$router->post([
    "uri" => "/transactions",   
    "controller" => "transactions",
    "action" => "createTransaction"
]);

$router->get([
    "uri" => "/balance",   
    "controller" => "balance",
    "action" => "balance"
]);

$router->get([
    "uri" => "/balance?dateStart=value&dateEnd=value",   
    "controller" => "balance",
    "action" => "balance"
]);

$router->get([
    "uri" => "/error/{id}",   
    "controller" => "error",
    "action" => "errorResponse"
]);

