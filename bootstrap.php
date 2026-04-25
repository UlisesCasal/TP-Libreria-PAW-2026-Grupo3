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

// 7. Registrar todas las rutas de la aplicación

// Inicio y catálogo
$router->register('GET', '/', 'IndexController', 'index');
$router->register('GET', '/catalogo', 'CatalogoController', 'listar');

// Carrito
$router->register('GET', '/carrito', 'CarritoController', 'ver');
$router->register('POST', '/carrito/agregar', 'CarritoController', 'agregar');
$router->register('POST', '/carrito/eliminar', 'CarritoController', 'eliminar');

// Crear cuenta
$router->register('GET', '/crearCuenta', 'CrearCuentaController', 'crearCuenta');
$router->register('POST', '/crearCuenta', 'CrearCuentaController', 'crearCuentaProcess');

// Inicio de sesión
$router->register('GET', '/inicio-sesion', 'InicioSesionController', 'index');
$router->register('POST', '/inicio-sesion', 'InicioSesionController', 'process');

// Formulario de compra
$router->register('GET', '/formulario', 'FormularioController', 'index');
$router->register('POST', '/formulario', 'FormularioController', 'process');

// 8. Ejecutar la aplicación
$router->route();
