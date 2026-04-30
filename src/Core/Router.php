<?php

namespace PAW\Core;

class Router {

    private $routes = [];

    //Registra las rutas en el ROUTER
    public function register($method, $path, $controller, $action){
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'controller' => $controller,
            'action' => $action
        ];
    }

    //Obtiene la url actual limpia (Sin parametros GET)
    public function getCurrentPath(){
        $uri = $_SERVER['REQUEST_URI'];
        //Extraigo solo el path sin query string
        $path = parse_url($uri, PHP_URL_PATH);

        $path = trim($path, '/');

        if(empty($path)){
            return '/';
        }
        return '/' . $path;
    }

    //Obtiene el metodo HTTP actual
    private function getCurrentMethod(){
        return $_SERVER['REQUEST_METHOD'];
    }

    //Busca y ejecuta la ruta que concida con la solicitud actual
    public function route(){
        $currentPath = $this->getCurrentPath();
        $currentMethod = $this->getCurrentMethod();

        //Recorro todas las rutas que se registraron
        foreach($this->routes as $route){
            if($route['method'] == $currentMethod && $route['path'] == $currentPath){
                return $this->executeController($route['controller'], $route['action']);
            }
        }


        http_response_code(404);
        echo "Error 404 - Página no encontrada";
        return false;
    }


    //Instancia el controlador y ejecuta la accion
    public function executeController($controllerName, $actionName){
    //Construyo el nombre completo de la case     
        $controllerClass = "\\PAW\\App\\Controllers\\" . $controllerName;

        //Verifico que la clase exista
        if(!class_exists($controllerClass)){
            http_response_code(404);
            echo "Error: el controlador $controllerName no existe";
            return false;
        }

        //Instancio la clase del controlador
        $controller = new $controllerClass();

        //Verifo que el metodo exista
        if(!method_exists($controller, $actionName)){
            http_response_code(500);
            echo "Error: el metodo $actionName no existe en $controllerName";
            return false;
        }

        //Ejecuta el metodo
        $controller->$actionName();
        return true;

    }

    //Muestra todas las rutas registradas
    public function showRoutes(){
        echo "<pre>";
        print_r($this->routes);
        echo "</pre>";
    }
        
    
}
