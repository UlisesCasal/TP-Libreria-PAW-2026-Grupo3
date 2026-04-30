<?php

namespace PAW\App\Controllers;

use PAW\Model\UsuarioModel;

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
        $nombre     = trim($_POST['nombre_apellido'] ?? '');
        $email      = trim($_POST['email'] ?? '');
        $password   = $_POST['contraseña'] ?? '';
        $cpassword  = $_POST['ccontraseña'] ?? '';
        $error      = null;

        if (empty($nombre) || empty($email) || empty($password)) {
            $error = 'Por favor, complete todos los campos.';
            require $this->viewsDir . 'crearCuenta.view.php';
            return;
        }

        if ($password !== $cpassword) {
            $error = 'Las contraseñas no coinciden.';
            require $this->viewsDir . 'crearCuenta.view.php';
            return;
        }

        $modelo = new UsuarioModel();

        if ($modelo->existeEmail($email)) {
            $error = 'El email ya está registrado.';
            require $this->viewsDir . 'crearCuenta.view.php';
            return;
        }

        $modelo->registrar($nombre, $email, $password);
        $this->cuentaCreada();
    }
}
