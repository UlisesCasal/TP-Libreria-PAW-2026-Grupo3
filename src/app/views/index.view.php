<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Libreria</title>
</head>

<body>
    <?php require 'parts/header.view.php'; ?>
    
    <main>
        <picture>
            <source media="(min-width: 600px)" srcset="../media/Promo1.webp">
            <source media="(max-width: 599px)" srcset="../media/promo1-chica.webp">
            <img src="../media/Promo1.webp" alt="Promo">
        </picture>
        <!-- Sección de categorías -->
        <section class="seccion-circulos">
            <header>
                <h2>Categorias</h2>
            </header>
            <article>
                <h3>ciencia ficción</h3>
                <figure>
                    <picture>
                        <img src="./media/categorias1L.jpg" alt="">
                        <source srcset="./media/categorias1L.jpg" media="( min-width: 1024px )">
                        <source srcset="./media/categorias1S.jpg" media="( max-width: 480px )">
                        <source srcset="./media/categorias1C.jpg" media="( 480px <= width <= 1024px )">
                    </picture>
                    
                </figure>
            </article>
            <article>
                <h3>romance</h3>
                <figure>
                    <picture>
                        <img src="./media/categorias2L.jpg" alt="">
                        <source srcset="./media/categorias2L.jpg" media="( min-width: 1024px )">
                        <source srcset="./media/categorias2S.jpg" media="( max-width: 480px )">
                        <source srcset="./media/categorias2C.jpg" media="( 480px <= width <= 1024px )">
                    </picture>
                </figure>
            </article>
            <article>
                <h3>terror</h3>
                <figure>
                    <picture>
                        <img src="./media/categorias3L.jpg" alt="">
                        <source srcset="./media/categorias3L.jpg" media="( min-width: 1024px )">
                        <source srcset="./media/categorias3S.jpg" media="( max-width: 480px )">
                        <source srcset="./media/categorias3C.jpg" media="( 480px <= width <= 1024px )">
                    </picture>

                </figure>
            </article>
            <article>
                <h3>Acción</h3>
                <figure>
                    <picture>
                        <img src="./media/categorias3L.jpg" alt="">
                        <source srcset="./media/categorias3L.jpg" media="( min-width: 1024px )">
                        <source srcset="./media/categorias3S.jpg" media="( max-width: 480px )">
                        <source srcset="./media/categorias3C.jpg" media="( 480px <= width <= 1024px )">
                    </picture>

                </figure>
            </article>
        </section>
        <!-- Sección de libros destacados -->
        <section>
            <header>
                <h2>libros destacados</h2>
            </header>
            <article>
                <h3>libro 1</h3>
                <figure>
                    <picture>
                        <source srcset="libro1L.jpg" media="( min-width: 1024px )">
                        <img src="libro1L.jpg" alt="Libro 1">
                    </picture>
                    <footer>
                        <p><a href="libro.html">Comprar</a></p>
                    </footer>
                </figure>
            </article>
            <article>
                <h3>libro 2</h3>
                <figure>
                    <picture>
                        <source srcset="libro2L.webp" media="( min-width: 1024px )">
                        <img src="libro2L.webp" alt="Libro 2">
                    </picture>
                    <footer>
                        <p><a href="libro.html">Comprar</a></p>
                    </footer>
                </figure>
            </article>
            <article>
                <h3>libro 3</h3>
                <figure>
                    <picture>
                        <source srcset="Libro3L.webp" media="( min-width: 1024px )">
                        <img src="Libro3L.webp" alt="Libro 3">
                    </picture>
                    <footer>
                        <p><a href="libro.html">Comprar</a></p>
                    </footer>
                </figure>
            </article>
        </section>
        <!-- Sección de autores destacados -->
        <section class="seccion-circulos">
            <header>
                <h2>autores destacados</h2>
            </header>
            <article>
                <h3>autor 1</h3>
                <figure>
                    <picture>
                        <img src="./media/autor1L.jpg" alt="">
                        <source srcset="./media/autor1L.jpg" media="( min-width: 1024px )">
                        <source srcset="./media/autor1S.jpg" media="( max-width: 480px )">
                        <source srcset="./media/autor1C.jpg" media="( 480px <= width <= 1024px )">
                    </picture>
                    <footer>
                        <p><a href="productos.html">Comprar</a></p>
                    </footer>
                </figure>
            </article>
            <article>
                <h3>autor 2</h3>
                <figure>
                    <picture>
                        <img src="./media/autor2L.jpg" alt="">
                        <source srcset="./media/autor2L.jpg" media="( min-width: 1024px )">
                        <source srcset="./media/autor2S.jpg" media="( max-width: 480px )">
                        <source srcset="./media/autor2C.jpg" media="( 480px <= width <= 1024px )">
                    </picture>
                    <footer>
                        <p><a href="productos.html">Comprar</a></p>
                    </footer>
                </figure>
            </article>
            <article>
                <h3>autor 3</h3>
                <figure>
                    <picture>
                        <img src="./media/autor3L.jpg" alt="">
                        <source srcset="./media/autor3L.jpg" media="( min-width: 1024px )">
                        <source srcset="./media/autor3S.jpg" media="( max-width: 480px )">
                        <source srcset="./media/autor3C.jpg" media="( 480px <= width <= 1024px )">
                    </picture>
                    <footer>
                        <p><a href="productos.html">Comprar</a></p>
                    </footer>
                </figure>
            </article>
        </section>

    </main>

    <!-- Footer de página -->
    <?php require 'parts/footer.view.php'; ?>


</body>

</html>