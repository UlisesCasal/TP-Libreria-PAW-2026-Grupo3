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
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $error = null;

        if (empty($email) || empty($password)) {
            $error = 'Por favor, complete todos los campos.';
        } else {
            $dbPath = __DIR__ . '/../../model/db.txt';
            $authenticated = false;
            $userData = null;

            if (file_exists($dbPath)) {
                $users = file($dbPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($users as $line) {
                    $parts = explode('|', $line);
                    if (count($parts) >= 2) {
                        $storedEmail = trim($parts[0]);
                        $storedHash  = trim($parts[1]);
                        $userName    = isset($parts[2]) ? trim($parts[2]) : 'Usuario';

                        if ($storedEmail === $email && password_verify($password, $storedHash)) {
                            $authenticated = true;
                            $userData = [
                                'email' => $storedEmail,
                                'nombre' => $userName
                            ];
                            break;
                        }
                    }
                }
            }

            if ($authenticated) {
                $_SESSION['usuario'] = $userData;
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
