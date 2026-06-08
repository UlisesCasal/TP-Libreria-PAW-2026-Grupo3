<?php

// ============================================
// BOOTSTRAP - Configuración inicial de la app
// ============================================

// 1. Cargar el autoload de Composer
require __DIR__ . '/vendor/autoload.php';

// 2. Cargar configuración (SMTP, constantes)
require __DIR__ . '/src/config.php';

// Auto-seed: ejecuta el seed si las tablas no existen (Render deployment)
try {
    $pdo = new \PDO(getenv('DATABASE_URL') ?: 'sqlite::memory:');
    $stmt = $pdo->query("SELECT 1 FROM information_schema.tables WHERE table_name = 'libros' LIMIT 1");
    if (!$stmt->fetch()) {
        $sql = file_get_contents(__DIR__ . '/db/schema.sql');
        if ($sql !== false) {
            $pdo->exec($sql);
        }
    }
} catch (\Throwable $e) {
    // Silenciosamente falla si DATABASE_URL no está configurada
}

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
$router->register('GET', '/cerrar-sesion', 'InicioSesionController', 'logout');

// Formulario de compra e Historial
$router->register('GET', '/formulario', 'FormularioController', 'index');
$router->register('POST', '/formulario', 'FormularioController', 'process');
$router->register('GET', '/mis-compras', 'FormularioController', 'historial');

// Libro 
$router->register('GET', '/libro', 'LibroController', 'mostrar_lib');
$router->register('POST', '/libro', 'LibroController', 'compra_lib');

// Nosotros
$router->register('GET', '/nosotros', 'NosotrosController', 'mostrar_nosotros');

// Crear Libro 
$router->register('POST', '/crear-libro', 'CrearLibroController', 'altaLibro');
$router->register('GET', '/crear-libro', 'CrearLibroController', 'mostrarForm');

// API: búsqueda de libros (Open Library)
$router->register('GET', '/api/buscar-isbn', 'ApiController', 'buscarPorIsbn');
$router->register('GET', '/api/buscar-libro', 'ApiController', 'buscarLibro');
$router->register('GET', '/api/detalle-libro', 'ApiController', 'detalleLibro');

// Seed: inicializar base de datos (Render/deployment)
$router->register('GET', '/seed', 'SeedController', 'execute');

// 8. Ejecutar la aplicación
$router->route();
