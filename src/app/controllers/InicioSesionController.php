<?php

namespace PAW\App\Controllers;

class InicioSesionController
{
    private string $viewdir;

    public function __construct()
    {
        $this->viewdir = __DIR__ . '/../views/';
    }

    public function index()
    {
        require $this->viewdir . 'inicio-sesion.view.php';
    }

    public function process()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        // Aquí iría la lógica de autenticación contra la base de datos

        require $this->viewdir . 'inicio-sesion.view.php';
    }
}
