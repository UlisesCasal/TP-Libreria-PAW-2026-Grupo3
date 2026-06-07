# Tasks: Migración a Twig

## Review Workload Forecast

| Field | Value |
|-------|-------|
| Estimated changed lines | ~1300 (additions + deletions) |
| 400-line budget risk | High |
| Chained PRs recommended | Yes |
| Suggested split | PR 1 → PR 2 → PR 3 |
| Delivery strategy | ask-always |
| Chain strategy | pending |

Decision needed before apply: Yes
Chained PRs recommended: Yes
Chain strategy: stacked-to-main
400-line budget risk: High

### Suggested Work Units (Stacked to Main)

| Unit | Goal | Likely PR | Base | Notes |
|------|------|-----------|------|-------|
| 1 | Infraestructura + POC (home) | PR 1 | main | TwigEnvironment, base.twig, header/footer, index.twig + IndexController |
| 2 | Vistas + controladores principales | PR 2 | main | catalogo, libro, formulario, crearCuenta, inicio-sesion + sus controllers |
| 3 | Vistas secundarias + cleanup | PR 3 | main | carrito, nosotros, crear-libro, mis-compras + cleanup .view.php |

## Phase 1: Infraestructura Twig

- [x] 1.1 Agregar `"twig/twig": "^3.0"` a `composer.json` require y ejecutar `composer update`
- [x] 1.2 Crear `src/Core/TwigEnvironment.php` — singleton que inicializa `\Twig\Environment` con `FilesystemLoader` apuntando a `src/App/views/` y expone globals: `_session` (`$_SESSION`), `_server` (`$_SERVER`), `_get` (`$_GET`)
- [x] 1.3 Crear `src/App/views/layouts/base.twig` con estructura HTML (`<!DOCTYPE>`), bloques `title`, `stylesheets`, `header`, `main`, `footer`, `scripts`, y carga de `style.css` por defecto

## Phase 2: Partials + Vistas Principales

- [x] 2.1 Migrar `parts/header.view.php` → `parts/header.twig`: reemplazar `<?php if(isset($_SESSION['usuario']))` con `{% if _session.usuario %}`, usar `_session.usuario.nombre` y `raw` filter para HTML inyectado
- [x] 2.2 Migrar `parts/footer.view.php` → `parts/footer.twig`: convertir `parse_url($_SERVER['REQUEST_URI'])` a `_server.REQUEST_URI` con filter `|split`, mantener misma lógica condicional de navegación
- [x] 2.3 Migrar `index.view.php` → `index.twig` extendiendo `layouts/base.twig`, bloque `main` con carousel y foreach sobre `libros`, bloque `scripts` con swiffyslider.js
- [x] 2.4 Migrar `catalogo.view.php` → `catalogo.twig` con `{% extends "layouts/base.twig" %}`, inyectar `window.ALL_BOOKS` usando `json_encode(libros)|raw`, bloque `stylesheets` con catalogo.css, bloque `scripts` con catalogo.js
- [x] 2.5 Migrar `libro.view.php` → `libro.twig` extendiendo `layouts/base.twig`, pasar `libro` y `relacionados`, bloque `stylesheets` con libro.css, título dinámico con `libro.titulo`

## Phase 3: Vistas de Formularios y Autenticación

- [x] 3.1 Migrar `formulario.view.php` → `formulario.twig` extendiendo `layouts/base.twig`, iterar `errores`, condicional `exito`, bloque `stylesheets` con formulario.css
- [x] 3.2 Migrar `compra-exitosa.view.php` → `compra-exitosa.twig` con `mensajeExito` y `tipoOperacion`
- [x] 3.3 Migrar `crearCuenta.view.php` → `crearCuenta.twig` mostrando `error` condicional, bloque `stylesheets` con crear-cuenta.css
- [x] 3.4 Migrar `crearCuentaCreada.view.php` → `crearCuentaCreada.twig`
- [x] 3.5 Migrar `inicio-sesion.view.php` → `inicio-sesion.twig` con `error` condicional, bloque `stylesheets` con inicio-sesion.css

## Phase 4: Vistas Secundarias

- [x] 4.1 Migrar `carrito.view.php` → `carrito.twig`
- [x] 4.2 Migrar `nosotros.view.php` → `nosotros.twig`
- [x] 4.3 Migrar `crear-libro.view.php` → `crear-libro.twig`
- [x] 4.4 Migrar `crearLibroCreado.view.php` → `crearLibroCreado.twig`
- [x] 4.5 Migrar `libroYaExiste.view.php` → `libroYaExiste.twig`
- [x] 4.6 Migrar `mis-compras.view.php` → `mis-compras.twig` con variable `compras`

## Phase 5: Actualizar Controladores

- [x] 5.1 `IndexController::index()`: importar `TwigEnvironment`, llamar `TwigEnvironment::render('index.twig', ['libros' => $libros])`
- [x] 5.2 `CatalogoController::listar()`: renderizar `catalogo.twig` con `libros` + `filtros`
- [x] 5.3 `LibroController::mostrar_lib()` y `compra_lib()`: renderizar `libro.twig` y `formulario.twig`
- [x] 5.4 `FormularioController::index()`, `process()`: renderizar `formulario.twig`, `compra-exitosa.twig`
- [x] 5.4b `FormularioController::historial()`: renderizar `mis-compras.twig` (pendiente — vista mis-compras en Slice 3)
- [x] 5.5 `CrearCuentaController::crearCuenta()`, `cuentaCreada()`, `crearCuentaProcess()`: renderizar `crearCuenta.twig` y `crearCuentaCreada.twig`
- [x] 5.6 `InicioSesionController::index()` y `process()`: renderizar `inicio-sesion.twig`
- [x] 5.7 `CarritoController::ver()`: renderizar `carrito.twig`
- [x] 5.8 `NosotrosController::mostrar_nosotros()`: renderizar `nosotros.twig`
- [x] 5.9 `CrearLibroController::mostrarForm()`, `libroCreado()`, `elLibroYaExiste()`: renderizar `crear-libro.twig`, `crearLibroCreado.twig`, `libroYaExiste.twig`

## Phase 6: Limpieza

- [x] 6.1 Eliminar `parts/header.view.php` y `parts/footer.view.php`
- [x] 6.2 Eliminar 14 archivos `.view.php`: index, catalogo, libro, formulario, crearCuenta, inicio-sesion, carrito, nosotros, crear-libro, compra-exitosa, crearCuentaCreada, crearLibroCreado, libroYaExiste, mis-compras
- [x] 6.3 Verificar todas las rutas del sitio recorriendo cada página en navegador
