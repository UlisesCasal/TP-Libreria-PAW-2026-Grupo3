<?php

namespace PAW\App\Controllers;

class InicioSesionController
{
    private string $viewdir;

    public function __construct()
    {
        $this->viewdir = __DIR__ . '/../views/'; // Directorio donde se encuentran las vistas
    }

    public function index()
    {
        //echo "CONTROLLER FUNCIONANDO";
        require $this->viewdir . 'inicio-sesion.view.php'; // Mostrar el formulario de inicio de sesión
    }

    public function process()
    {
        $email = $_POST['email'] ?? ''; // Obtener el email del formulario, o asignar una cadena vacía si no se envió
        $password = $_POST['password'] ?? ''; // Obtener la contraseña del formulario, o asignar una cadena vacía si no se envió

        // Aquí iría la lógica de autenticación (verificar email y contraseña) contra db.txt por ahora.

        require $this->viewdir . 'inicio-sesion.view.php'; // Volver a mostrar el formulario (en un caso real, redirigirías a otra página)
    }

}