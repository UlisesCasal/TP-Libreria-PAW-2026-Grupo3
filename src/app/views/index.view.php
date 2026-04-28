<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/style.css">
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
        <section>
            <header>
                <h2>Libros destacados</h2>
            </header>
            <article>
                <h3>Libro 1</h3>
                <figure>
                    <img src="/model/libro1L.jpg" alt="Libro 1">
                    <footer>
                        <p><a href="/libro">Comprar</a></p>
                    </footer>
                </figure>
            </article>
            <article>
                <h3>Libro 2</h3>
                <figure>
                    <img src="/model/libro2L.webp" alt="Libro 2">
                    <footer>
                        <p><a href="/libro">Comprar</a></p>
                    </footer>
                </figure>
            </article>
            <article>
                <h3>Libro 3</h3>
                <figure>
                    <img src="/model/Libro3L.webp" alt="Libro 3">
                    <footer>
                        <p><a href="/libro">Comprar</a></p>
                    </footer>
                </figure>
            </article>
        </section>
    </main>

    <?php require __DIR__ . '/parts/footer.view.php'; ?>
</body>

</html>
