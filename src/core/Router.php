<?php

namespace PAW\Core;

use PAW\Core\Exceptions\RouteNotFoundException;

class Router {

    public array $routes = [
        "GET" => [],
        "POST" => [],
    ];

    public function route()
    {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];
        $this->direct($path, $method);
    }

    public function loadRoutes($path, $action, $method = 'GET')
    {
        $this->routes[$method][$path] = $action; // Almacena la ruta y su acción asociada en el arreglo de rutas
    }

    public function get($path, $action)
    {
        $this->loadRoutes($path, $action, 'GET'); // Carga una ruta para el método GET
    }

    public function post($path, $action)
    {
        $this->loadRoutes($path, $action, 'POST'); // Carga una ruta para el método POST
    }

    public function exists($path, $method)
    {
        return array_key_exists($path, $this->routes[$method]); // Verifica si una ruta existe para un método específico
    }

    public function getController($path, $http_method)
    {
        return explode('@', $this->routes[$http_method][$path]); // Obtiene el controlador y el método asociados a una ruta específica
    }

    public function direct($path, $http_method = 'GET')
    {
        if (!$this->exists($path, $http_method)) { // Verifica si la ruta solicitada existe en el arreglo de rutas
            throw new RouteNotFoundException("Ruta no encontrada: {$path}"); // Si no existe, lanza una excepción indicando que la ruta no fue encontrada
        }
        //list($controller, $method) = explode('@', $this->routes[][$path]); // Obtiene el nombre del controlador y el método de la ruta solicitada
        list($controller, $method) = $this->getController($path, $http_method); // Obtiene el nombre del controlador y el método de la ruta solicitada utilizando el método getController
        $controller_name = "PAW\\App\\Controllers\\{$controller}"; // Construye el nombre completo del controlador con su espacio de nombres
        $objController = new $controller_name; // Crea una instancia del controlador correspondiente
        $objController->$method(); // Llama al método del controlador para manejar la solicitud
    }
        
    
}
