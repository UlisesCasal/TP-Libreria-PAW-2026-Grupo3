# TP Librería — PAW 2026 Grupo 3

Aplicación web de una librería online desarrollada en PHP con arquitectura MVC para la materia Programación Web.

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

## Respuestas Teóricas (Microdata)

### 1. ¿Toda la microdata es estática?
No, no toda la microdata es estática. Si bien ciertos datos como el nombre de la organización o su dirección suelen ser fijos, la mayor parte de la microdata en un sitio dinámico es precisamente **dinámica**. Por ejemplo, en la página de un libro, los valores de `name`, `author`, `price`, `availability` (stock) e `isbn` se cargan desde el modelo de datos y cambian según el libro seleccionado. La microdata debe reflejar fielmente el contenido actual de la página para que los motores de búsqueda indexen información veraz.

### 2. ¿Cómo decidimos en qué página es importante la microdata de ciertos objetos?
La decisión se basa en la **jerarquía semántica** y el **propósito** de cada página:
- **Global (Footer/Header):** Se incluye la microdata de la organización (`BookStore`) para que esté presente en todo el sitio, permitiendo que buscadores identifiquen siempre quién es el responsable del contenido y su información de contacto.
- **Página de Detalle (`libro.twig`):** El objeto central es el `Book`. Aquí es crítico incluir el esquema completo con ofertas (`Offer`) para que el libro pueda aparecer en resultados de búsqueda enriquecidos (Rich Snippets) con su precio y stock.
- **Listados (`catalogo.twig`):** Se utiliza `ItemList` y `ListItem`. Esto indica a los buscadores que la página es una colección de objetos relacionados. Los objetos individuales se marcan como `Book` para que se entienda que son productos en venta y no meros enlaces o publicidades.
- **Página de Institucional (`nosotros.twig`):** Aquí se refuerza el objeto `BookStore` con una descripción más detallada de la misión e historia de la librería.

En resumen, la microdata debe acompañar lo que el usuario está viendo: si el usuario ve un producto, el buscador debe "ver" un objeto de tipo producto (Book/Product). Si ve una lista, el buscador debe "ver" un listado.

