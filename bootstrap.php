<?php

// ============================================
// BOOTSTRAP - Configuración inicial de la app
// ============================================

// Cargar el autoload de Composer para manejar las clases automáticamente
require __DIR__ . '/vendor/autoload.php';

// Cargar la configuración de la aplicación (credenciales, constantes, etc.)
require __DIR__ . '/src/config.php'; 

// Configurar el enrutamiento de la aplicación
use PAW\Core\Router;
use PAW\Core\Exceptions\RouteNotFoundException;

// Crear una instancia del enrutador
$router = new Router();
$router->get('/', 'PageController@index'); // Ruta para la página de inicio
$router->get('/inicio-sesion', 'InicioSesionController@index'); // Ruta para mostrar el formulario de inicio de sesión
$router->post('/inicio-sesion', 'InicioSesionController@process'); // Ruta para procesar el formulario de inicio de sesión
$router->get('/formulario', 'FormularioController@index'); // Ruta para mostrar el formulario de pago
$router->post('/formulario', 'FormularioController@process'); // Ruta para procesar el formulario de pago

// Manejar la solicitud entrante y dirigirla a la acción correspondiente
try {
    $router->route();
} catch (RouteNotFoundException $e) {
    echo $e->getMessage();
}