<?php

namespace PAW\App\Controllers;

class NosotrosController{
    private string $viewdir;

    public function __construct()
    {
        $this->viewdir = __DIR__ . '/../views/';//el string viewdir queda con el valor-> /src/views/
    }
    public function mostrar_nostros(){
        require $this->viewdir . 'nosotros.view.php';
    }
    
}