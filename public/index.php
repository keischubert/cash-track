<?php

require_once '../helpers/functions.php';

require fullPath('Router.php');

$router = new Router();

require fullPath("routes.php");

$requestMethod = $_SERVER["REQUEST_METHOD"];

$requestUri = $_SERVER["REQUEST_URI"];

$router->run($requestMethod, $requestUri);