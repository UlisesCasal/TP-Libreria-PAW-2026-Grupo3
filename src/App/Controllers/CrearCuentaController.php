<?php

namespace PAW\App\Controllers;

use PAW\Core\TwigEnvironment;
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
        $error = $_POST['error'] ?? null;
        TwigEnvironment::getInstance()->render('crearCuenta.twig', [
            'error' => $error
        ]);
    }

    public function cuentaCreada()
    {
        TwigEnvironment::getInstance()->render('crearCuentaCreada.twig', []);
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
            TwigEnvironment::getInstance()->render('crearCuenta.twig', [
                'error' => $error
            ]);
            return;
        }

        if ($password !== $cpassword) {
            $error = 'Las contraseñas no coinciden.';
            TwigEnvironment::getInstance()->render('crearCuenta.twig', [
                'error' => $error
            ]);
            return;
        }

        $modelo = new UsuarioModel();

        if ($modelo->existeEmail($email)) {
            $error = 'El email ya está registrado.';
            TwigEnvironment::getInstance()->render('crearCuenta.twig', [
                'error' => $error
            ]);
            return;
        }

        // Separar nombre y apellido del campo combinado "nombre_apellido"
        $partes = explode(' ', $nombre, 2);
        $nombre   = $partes[0];
        $apellido = $partes[1] ?? '';

        $modelo->registrar($nombre, $apellido, $email, $password);
        $this->cuentaCreada();
    }
}
