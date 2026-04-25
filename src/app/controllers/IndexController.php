<?php

namespace PAW\App\Controllers;

class IndexController
{
    private string $viewsDir;

    public function __construct()
    {
        $this->viewsDir = __DIR__ . '/../views/';
    }

    public function index()
    {
        require $this->viewsDir . 'index.view.php';
    }
}
