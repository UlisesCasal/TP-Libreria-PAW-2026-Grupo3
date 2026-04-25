<?php

namespace PAW\App\Controllers;

class CrearCuentaController
{
    public string $viewsDir;

    public function __construct()
    {
        $this->viewsDir = __DIR__ . '/../views/';
    }

    public function crearCuenta()
    {
        require $this->viewsDir . 'crearCuenta.view.php';
    }

    public function cuentaCreada()
    {
        require $this->viewsDir . 'crearCuentaCreada.view.php';
    }

    public function crearCuentaProcess()
    {
        $nombre_apellido = $_POST['nombre_apellido'] ?? '';
        $email = $_POST['email'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $contraseña = $_POST['contraseña'] ?? '';
        $ccontraseña = $_POST['ccontraseña'] ?? '';

        if ($contraseña !== $ccontraseña) {
            die('Las contraseñas no coinciden');
        }

        // lógica para guardar los datos en la base de datos

        $this->cuentaCreada();
    }
}
