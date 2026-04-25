<?php

require_once __DIR__ . '/vendor/autoload.php';

use \PAW\core\Router;


$router = new Router();
$router->get('/','indexController@index');
$router->get('/crearCuenta','crearCuentaController@crearCuenta');
$router->post('/crearCuenta','crearCuentaController@crearCuentaProcess');