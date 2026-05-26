<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/carousel.css">
    <title>Inicio — Libreria</title>
</head>

<body>
    <?php require __DIR__ . '/parts/header.view.php'; ?>
        <main>
        <!-- Sección de categorías -->
        <section class="seccion-circulos">
            <header>
                <h2>Categorias</h2>
            </header>
            <article>
                <h3>Ciencia ficción</h3>
            </article>
            <article>
                <h3>Romance</h3>
            </article>
            <article>
                <h3>Terror</h3>
            </article>
            <article>
                <h3>Acción</h3>
            </article>
        </section>

        <!-- Sección de libros destacados -->
        <section class="seccion-libros-destacados">
            <header>
                <h2>Libros destacados</h2>
            </header>
            <div id="carousel-libros">
                <img src="/assets/tapas/libro1L.jpg"  alt="Libro 1">
                <img src="/assets/tapas/libro2L.webp" alt="Libro 2">
                <img src="/assets/tapas/libro3L.webp" alt="Libro 3">
                <img src="/assets/tapas/libro4L.jpg" alt="Libro 4">
                <img src="/assets/tapas/libro.jpg" alt="Libro 5">
                <img src="/assets/tapas/principito tapa.png" alt="Libro 6">


            </div>
        </section>
    </main>

    <script src="/assets/js/carousel.js"></script>
    <script>
        new Carousel(document.getElementById('carousel-libros'), {
            effect  : 'slide',
            autoplay: true,
            interval: 4000,
        });
    </script>

    <?php require __DIR__ . '/parts/footer.view.php'; ?>
</body>

</html>
