<?php

namespace PAW\App\Controllers;

use PAW\Core\TwigEnvironment;
use PAW\Model\LibroModel;

class IndexController
{
    public function index()
    {
        $libroModel = new LibroModel();
        $libros = $libroModel->getAll();
        TwigEnvironment::getInstance()->render('index.twig', ['libros' => $libros]);
    }
}
