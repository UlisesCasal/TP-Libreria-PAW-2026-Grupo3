<?php

namespace PAW\App\Controllers;

use PAW\Model\UsuarioModel;

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
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $error    = null;

        if (empty($email) || empty($password)) {
            $error = 'Por favor, complete todos los campos.';
        } else {
            $modelo  = new UsuarioModel();
            $usuario = $modelo->verificar($email, $password);

            if ($usuario !== null) {
                $_SESSION['usuario'] = $usuario;
                header('Location: /');
                exit;
            } else {
                $error = 'Email o contraseña incorrectos.';
            }
        }

        require $this->viewdir . 'inicio-sesion.view.php';
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        header('Location: /');
        exit;
    }
}
