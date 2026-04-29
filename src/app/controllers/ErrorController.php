<?php

namespace PAW\App\Controllers;

class ErrorController{
    private string $viewdir;

    public function __construct()
    {
        $this->viewdir = __DIR__ . '/../views/';//el string viewdir queda con el valor-> /src/views/
    }
    public function notFound(){
        $mensaje_error = "Page Not Found: Error 404";
        http_response_code(404);
        require $this->viewdir . 'error.view.php';
    }
    public function internalError(){
        $mensaje_error = "Internal Error: 500";
        http_response_code(500);
        require $this->viewdir . 'error.view.php';
    }
    
}