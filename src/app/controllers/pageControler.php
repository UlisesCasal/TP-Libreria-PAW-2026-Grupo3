<?php

namespace PAW\App\Controllers;

class PageController
{
    private string $viewdir;

    public function __construct()
    {
        $this->viewdir = __DIR__ . '/../views/';
    }

}