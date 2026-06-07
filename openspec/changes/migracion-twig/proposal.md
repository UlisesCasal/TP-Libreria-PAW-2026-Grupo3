# Proposal: Migración a Twig

## Intent

Las 14 vistas actuales duplican la estructura HTML completa (`<!DOCTYPE html>` a `</html>`) incluyendo manualmente header y footer con `require`. Esto dificulta el mantenimiento: cambiar la estructura base requiere editar 14 archivos. Migrar a Twig permite herencia de templates (layout base), bloques reutilizables, escape automático, y sintaxis más limpia que PHP embebido.

## Scope

### In Scope
- Instalar `twig/twig` vía Composer
- Crear `src/Core/TwigEnvironment.php` — servicio que configura Twig con el directorio de vistas y expone globals (`_session`, `_server`, `_get`)
- Crear `src/App/views/layouts/base.twig` — layout base con bloques: `title`, `stylesheets`, `header`, `main`, `footer`, `scripts`
- Migrar `parts/header.view.php` → `parts/header.twig`
- Migrar `parts/footer.view.php` → `parts/footer.twig`
- Migrar las 14 vistas `.view.php` → `.twig` usando herencia del layout base
- Actualizar los 9 controladores activos para usar `TwigEnvironment::render()` en vez de `require`

### Out of Scope
- Refactorizar lógica de negocio en controladores
- Agregar contenedor de dependencias / inyección
- Cambiar el sistema de routing
- Eliminar archivos `.view.php` antiguos (se eliminan tras verificación)

## Capabilities

### New Capabilities
None — refactor técnico sin cambios en requisitos funcionales.

### Modified Capabilities
None — no cambia comportamiento observable por el usuario.

## Approach

1. `composer require twig/twig` — agregar dependencia
2. Crear `TwigEnvironment` en `src/Core/` que inicializa `\Twig\Environment` con `src/App/views/` como `LoaderInterface` y expone `$_SESSION`, `$_SERVER`, y `$_GET` como Twig globals
3. Agregar método `render(string $template, array $data): void` que renderiza y hace `echo`
4. Crear `layouts/base.twig` con la estructura HTML común y bloques para que cada vista extienda
5. Convertir `header.view.php` y `footer.view.php` a Twig parciales
6. Cada vista `.view.php` → `.twig`: reemplaza `require header/footer` + HTML repetido por `{% extends "layouts/base.twig" %}` + contenido en bloques
7. Cada controlador: inyecta/referencia `TwigEnvironment` y llama `$twig->render('vista.twig', compact('var1', 'var2'))` en lugar de `require`
8. Verificar navegación completa del sitio (todas las rutas)

## Affected Areas

| Area | Impact | Description |
|------|--------|-------------|
| `composer.json` | Modified | Agregar `twig/twig` |
| `src/Core/TwigEnvironment.php` | New | Servicio Twig singleton |
| `src/App/views/layouts/base.twig` | New | Layout base con bloques |
| `src/App/views/parts/header.twig` | New | Migración de header |
| `src/App/views/parts/footer.twig` | New | Migración de footer |
| `src/App/views/*.twig` (14 archivos) | New | Migración de vistas |
| `src/App/Controllers/*Controller.php` (9) | Modified | Usar `$twig->render()` |
| `src/App/views/*.view.php` (16 archivos) | Removed | Eliminar tras verificar |

## Risks

| Risk | Likelihood | Mitigation |
|------|------------|------------|
| Escape automático de Twig rompe HTML inyectado por JS (`json_encode($libros)`) | Medium | Usar `raw` filter explícito donde sea necesario |
| Variables PHP globales (`$_SESSION`, `$_SERVER`) no accesibles en Twig | Low | Exponer como Twig globals en `TwigEnvironment` |
| Alguna variable no se pasa del controlador a la vista | Low | Revisar cada controlador vs. template migrado |

## Rollback Plan

1. `composer remove twig/twig` + `composer install`
2. `git checkout -- src/App/Controllers/` (restaurar controladores)
3. `git checkout -- src/App/views/` (restaurar vistas)
4. Verificar rutas en navegador

## Dependencies

- `twig/twig` ^3.0

## Success Criteria

- [ ] `composer install` exitoso con Twig
- [ ] Cada ruta del sitio renderiza correctamente (recorrido visual completo)
- [ ] Header y footer se ven idénticos al estado pre-migración
- [ ] Variables de sesión (usuario logueado) se muestran correctamente en header
- [ ] Catálogo con filtros JS funciona (datos inyectados via `json_encode`)
- [ ] Formulario de compra muestra errores y éxito correctamente
- [ ] No hay errores PHP/Watchdog en ninguna ruta
