<?php

namespace PAW\app\controllers;


class crearCuentaController
{
    public string $viewsDir;

    public function __construct()
    {
        $this->viewsDir = __DIR__ . '/../views/';

    }

    public function crearCuenta($procesado = false)
    {
        require $this->viewsDir . 'crearCuenta.view.php';
    }


    public function crearCuentaProcess()
    {
        $nombre_apellido = $_POST['nombre_apellido'];
        $email = $_POST['email'];
        $telefono = $_POST['telefono'];
        $contraseña = $_POST['contraseña'];
        $ccontraseña = $_POST['ccontraseña'];

        if ($contraseña !== $ccontraseña) {
            die('Las contraseñas no coinciden');
        }

        // lógica para guardar los datos en la base de datos

        $this->crearCuenta(true);
    }

}