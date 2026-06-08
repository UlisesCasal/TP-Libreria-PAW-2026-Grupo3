<?php

// ============================================
// BOOTSTRAP - Configuración inicial de la app
// ============================================

// 1. Cargar el autoload de Composer
require __DIR__ . '/vendor/autoload.php';

// 2. Cargar configuración (SMTP, constantes)
require __DIR__ . '/src/config.php';

// Auto-seed: ejecuta el seed si las tablas no existen (Render deployment)
// Usa Database::getInstance() que parsea correctamente DATABASE_URL,
// a diferencia de new PDO() directo que no acepta postgres:// como DSN.
if (getenv('DATABASE_URL')) {
    try {
        $pdo = Database::getInstance()->pdo();
        // Cuenta las 5 tablas requeridas; si falta alguna re-aplica el schema.
        // schema.sql usa IF NOT EXISTS, por lo que es idempotente.
        $stmt = $pdo->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='public' AND table_name IN ('usuarios','libros','carrito_items','pedidos','pedido_items')");
        if ((int)$stmt->fetchColumn() < 5) {
            $sql = file_get_contents(__DIR__ . '/db/schema.sql');
            if ($sql !== false) {
                $pdo->exec($sql);
            }
        }
    } catch (\Throwable $e) {
        // Silenciosamente falla si DATABASE_URL no está configurada
        // o la base de datos no está disponible aún
    }
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

// 5. Importar clases del core
use PAW\Core\Database;
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
$router->register('GET', '/alta-personal', 'CrearCuentaController', 'altaPersonal');
$router->register('POST', '/alta-personal', 'CrearCuentaController', 'altaPersonalProcess');

// Inicio de sesión
$router->register('GET', '/inicio-sesion', 'InicioSesionController', 'index');
$router->register('POST', '/inicio-sesion', 'InicioSesionController', 'process');
$router->register('GET', '/cerrar-sesion', 'InicioSesionController', 'logout');

// Formulario de compra e Historial
/*$router->register('GET', '/formulario', 'FormularioController', 'index');
$router->register('POST', '/formulario', 'FormularioController', 'process');
$router->register('GET', '/mis-compras', 'FormularioController', 'historial');*/

// Libro
$router->register('GET', '/libro', 'LibroController', 'mostrar_lib');

// Nosotros
$router->register('GET', '/nosotros', 'NosotrosController', 'mostrar_nosotros');

// Crear Libro 
$router->register('POST', '/crear-libro', 'CrearLibroController', 'altaLibro');
$router->register('GET', '/crear-libro', 'CrearLibroController', 'mostrarForm');

// API: búsqueda de libros (Open Library)
$router->register('GET', '/api/buscar-isbn', 'ApiController', 'buscarPorIsbn');
$router->register('GET', '/api/buscar-libro', 'ApiController', 'buscarLibro');
$router->register('GET', '/api/detalle-libro', 'ApiController', 'detalleLibro');

$router->register('POST', '/compra', 'ReservasController', 'processCompra');
$router->register('GET', '/compra', 'ReservasController', 'mostrarFormulario');
$router->register('GET', '/pedidos', 'ReservasController', 'getAll');
$router->register('GET', '/pedidos/items', 'ReservasController', 'verDetalle');
$router->register('GET', '/mis-compras', 'ReservasController', 'historial');

// Seed: inicializar base de datos (Render/deployment)
$router->register('GET', '/seed', 'SeedController', 'execute');
// 8. Ejecutar la aplicación
$router->route();
