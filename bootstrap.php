<?php

// ============================================
// BOOTSTRAP - Configuración inicial de la app
// ============================================

// 1. Cargar el autoload de Composer
require __DIR__ . '/vendor/autoload.php';

// 2. Iniciar sesiones
session_start();

// 3. Configurar manejo de errores (Whoops)
try {
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
} catch (Exception $e) {
    // Si Whoops no está instalado, continuamos sin él
}

// 4. Importar el Router
use PAW\Core\Router;

// 5. Crear instancia del Router
$router = new Router();

// 6. Registrar todas las rutas de la aplicación
$router->register('GET', '/', 'CatalogoController', 'listar');
$router->register('GET', '/catalogo', 'CatalogoController', 'listar');
$router->register('GET', '/carrito', 'CarritoController', 'ver');
$router->register('POST', '/carrito/agregar', 'CarritoController', 'agregar');
$router->register('POST', '/carrito/eliminar', 'CarritoController', 'eliminar');

// 7. Ejecutar la aplicación
$router->route();
