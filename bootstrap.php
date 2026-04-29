<?php

// ============================================
// BOOTSTRAP - Configuración inicial de la app
// ============================================

// 1. Cargar el autoload de Composer
require __DIR__ . '/vendor/autoload.php';

// 2. Cargar configuración (SMTP, constantes)
require __DIR__ . '/src/config.php';

// 3. Iniciar sesiones
session_start();

// 4. Configurar manejo de errores (Whoops)
try {
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
} catch (Exception $e) {
    // Si Whoops no está instalado, continuamos sin él
}

// 5. Importar el Router
use PAW\Core\Router;

// 6. Crear instancia del Router
$router = new Router();

use PAW\Core\Request;
$request = new Request();//creo el objeto request

$router->cargar_rutas();//cargo las rutas de la aplicacion hardcodeadas
$router->direct($request);//le paso la request al router, que la va a rutear a donde corresponda


