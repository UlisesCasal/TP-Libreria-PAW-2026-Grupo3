<?php

namespace PAW\App\Controllers;

class LibroController{
    private string $viewdir;

    public function __construct()
    {
        $this->viewdir = __DIR__ . '/../views/';//el string viewdir queda con el valor-> /src/views/
    }
    public function mostrar_lib(){
        require $this->viewdir . 'libro.view.php';
    }
    public function compra_lib(){
        $cantidad= $_POST['cantidad'] ?? '';

        //chequear stock para la cantidad deseada del libro
        //chequear que el usuario este registrado
        require $this->viewdir . 'formulario.view.php';
    }
}