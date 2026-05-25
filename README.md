# TP Librería - PAW 2026 - Grupo 3

Este proyecto es una aplicación web de librería desarrollada como parte de la cátedra de Programación en Ambiente Web (PAW).

## Cambios Recientes (Mayo 2026)

### 1. Sistema de Catálogo Dinámico (JS)
Se ha migrado la lógica de filtrado, ordenamiento y paginación del lado del servidor (PHP) al lado del cliente (Vanilla JS).

*   **Componente `CatalogManager`**: Implementado en `public/assets/js/catalogo.js` utilizando ES6+.
*   **Filtrado Inteligente**: Los usuarios pueden filtrar libros por autor (búsqueda parcial), género y rango de precio en tiempo real.
*   **Ordenamiento Dinámico**: Soporte para orden ascendente/descendente por título y precio.
*   **Paginación Dual (Responsive)**:
    *   **Desktop**: Paginación tradicional por botones.
    *   **Mobile**: Scroll infinito (Infinite Scroll) para una mejor experiencia táctil.
*   **Inyección de Datos**: El backend inyecta el dataset inicial mediante `window.ALL_BOOKS` para evitar peticiones AJAX adicionales en la carga inicial.

### 2. Refactorización de Arquitectura y Estándares
*   **Corrección PSR-4**: Se renombraron directorios y se actualizaron los namespaces para cumplir estrictamente con el estándar PSR-4 de Composer. Los directorios dentro de `src/` ahora usan PascalCase (`App`, `Core`, `Model`) para coincidir con los namespaces definidos en `composer.json`.
*   **Controladores**: El `CatalogoController` se simplificó para delegar la lógica de presentación al cliente, manteniendo la responsabilidad de provisión de datos.

### 3. Mejoras en UI/UX y Estilos
*   **Layout Adaptativo**: Ajustes en `catalogo.css` y `style.css` para soportar el nuevo sistema de filtros y asegurar la responsividad en dispositivos móviles.
*   **Header y Footer**: Actualización de componentes compartidos para mayor consistencia visual.

## Tecnologías Utilizadas
*   **Backend**: PHP 8.x (MVC personalizado)
*   **Frontend**: HTML5, CSS3 (Vanilla), JavaScript (ES6+)
*   **Dependencias**: Composer (Autoloading PSR-4)
*   **Base de Datos**: Sistema de archivos (archivos de texto plano)

## Estructura del Proyecto
*   `public/`: Punto de entrada de la aplicación y activos estáticos (CSS, JS, Imágenes).
*   `src/App/`: Lógica de la aplicación (Controladores y Vistas).
*   `src/Core/`: Clases base del framework (Router, Excepciones).
*   `src/Model/`: Modelos de datos y acceso a archivos.
*   `vendor/`: Dependencias gestionadas por Composer.
