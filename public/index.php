<?php


use \PAW\core\Router;

require_once __DIR__ . '/../bootstrap.php';


$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

try {
    $router->direct($path, $method);
} catch (Exception $e) {
    echo $e->getMessage();
}   







