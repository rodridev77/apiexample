<?php
header('Content-Type: application/json; charset=UTF-8');
require_once '../vendor/autoload.php';

use App\Routes\Router;

if (isset($_REQUEST) && !empty($_REQUEST)) {
    $router = new Router($_SERVER['REQUEST_METHOD'], $_REQUEST);
    echo $router->run();
}