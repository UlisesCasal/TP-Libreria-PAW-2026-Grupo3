<?php

namespace PAW\App\Controllers;

use PAW\Model\LibroModel;

class IndexController
{
    private string $viewsDir;

    public function __construct()
    {
        $this->viewsDir = __DIR__ . '/../views/';
    }

    public function index()
    {
        $libroModel = new LibroModel();
        $libros = $libroModel->getAll();
        require $this->viewsDir . 'index.view.php';
    }
}
