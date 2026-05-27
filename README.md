# TP Librería — PAW 2026 Grupo 3

Aplicación web de una librería online desarrollada en PHP con arquitectura MVC para la materia Programación de Aplicaciones Web.

## Integrantes

- Mariano Cavallo
- Ulises Casal
- Cristian Anito
- Valen Aimale


## Funcionalidades

- Catálogo de libros con filtros por título, autor, género y precio
- Detalle de libro individual
- Carrito de compras
- Formulario de compra
- Alta de libros con validación de formulario y drag & drop de imágenes
- Registro e inicio de sesión de usuarios
- Carousel de libros destacados (SwiffySlider) con efectos Slide, Fade, Zoom y Flip
- Historial de últimas 5 búsquedas

## Tecnologías

- PHP 8+ (MVC sin framework)
- HTML5 / CSS3 / JavaScript (vanilla)
- Almacenamiento en archivos `.txt`
- Servidor de desarrollo: PHP built-in server

## Instalación y ejecución

**Requisitos:** PHP 8.0 o superior y Composer instalados.

```bash
# Clonar el repositorio
git clone https://github.com/UlisesCasal/TP-Libreria-PAW-2026-Grupo3.git
cd TP-Libreria-PAW-2026-Grupo3

# Instalar dependencias
composer install

# Iniciar el servidor de desarrollo
php -S localhost:8000 -t public
```

Luego abrí `http://localhost:8000` en el navegador.

## Estructura del proyecto

```
├── public/
│   ├── index.php          # Entry point
│   └── assets/
│       ├── css/           # Hojas de estilo por sección
│       ├── js/            # Scripts (carousel, catálogo, validaciones)
│       └── tapas/         # Imágenes de portadas de libros
├── src/
│   ├── App/
│   │   ├── Controllers/   # Controladores de cada página
│   │   └── views/         # Vistas PHP + parciales (header, footer)
│   ├── Core/              # Router y excepciones
│   └── Model/
│       ├── LibroModel.php # Modelo de libros
│       └── libros.txt     # Base de datos de libros
├── bootstrap.php          # Configuración e inicialización
└── composer.json
```