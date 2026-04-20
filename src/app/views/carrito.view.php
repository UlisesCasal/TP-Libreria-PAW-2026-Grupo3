<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css"> <link rel="stylesheet" href="css/carrito.css">
    <title>Carrito — Libreria</title>
</head>

<body>
    <header>
        <img src="Logo_Violeta2.JPG" alt="Logo Violeta">
        <h1>Libreria</h1>

        <nav>
            <ul>
                <li><a href="index.html">Inicio</a></li>
                <li><a href="libros.html">Libros</a></li>
                <li><a href="autores.html">Autores</a></li>
                <li><a href="contacto.html">Contacto</a></li>
            </ul>
            <form action="libros.html" method="get" role="search">
                <label for="busqueda-libros">Buscar libro:</label>
                <input type="search" id="busqueda-libros" name="q" placeholder="Título, autor o género">
                <button type="submit">Buscar</button>
            </form>
            <div class="nav-acciones">
                <a href="login.html">Registro</a>
                <a href="carrito.html">Carrito</a>
            </div>
        </nav>
    </header>

    <main>
        <h2>Carrito de compras</h2>
        <form action="analizar_carrito.PHP" class="carrito-form">
            <fieldset class="carrito-items">
                <legend>Libros seleccionados:</legend>
                <ul>
                    <li class="carrito-item">
                        <img src="../media/tapa_libro.jpg" alt="Portada libro">
                        <p class="item-titulo">Titulo + autor</p>
                        <p class="item-precio">$0</p>
                        <input type="number" name="cantidad_libro" min="1" value="1">
                        <button type="submit" class="btn-borrar" name="accion" value="borrar">
                            Quitar
                        </button>
                    </li>
                    <li class="carrito-item">
                        <img src="../media/Promo1.webp" alt="Portada libro">
                        <p class="item-titulo">Titulo + autor</p>
                        <p class="item-precio">$0</p>
                        <input type="number" name="cantidad_libro" min="1" value="1">
                        <button type="submit" class="btn-borrar" name="accion" value="borrar">
                            Quitar
                        </button>
                    </li>
                    <li class="carrito-item">
                        <img src="../media/promo1-chica.webp" alt="Portada libro">
                        <p class="item-titulo">Titulo + autor</p>
                        <p class="item-precio">$0</p>
                        <input type="number" name="cantidad_libro" min="1" value="1">
                        <button type="submit" class="btn-borrar" name="accion" value="borrar">
                            Quitar
                        </button>
                    </li>
                </ul>
            </fieldset>

            <button type="submit" class="btn-actualizar" name="accion" value="actualizar">
                Actualizar carrito
            </button>

            <section class="resumen-compra">
                <h2>Resumen de compra</h2>
                <ul>
                    <li><p>Subtotal: $0</p></li>
                    <li><p>Envío: $0</p></li>
                    <li><p>Total: $0</p></li>
                </ul>
                <p>Fecha de entrega estimada: 06/04/2006</p>
                <button type="submit" name="accion" value="finalizar">Finalizar compra</button>
            </section>
        </form>
    </main>

    <footer>
        <section aria-label="Redes sociales">
            <h2>Seguinos</h2>
            <ul>
                <li><a href="https://www.instagram.com" target="_blank" rel="noopener noreferrer">Instagram</a></li>
                <li><a href="https://www.twitter.com" target="_blank" rel="noopener noreferrer">Twitter</a></li>
                <li><a href="https://www.facebook.com" target="_blank" rel="noopener noreferrer">Facebook</a></li>
            </ul>
        </section>

        <nav aria-label="Enlaces del sitio">
            <h2>Paw Print</h2>
            <ul>
                <li><a href="nosotros.html">Nosotros</a></li>
                <li><a href="sucursales.html">Sucursales</a></li>
                <li><a href="contacto.html">Contacto</a></li>
                <li><a href="help.html">Ayuda</a></li>
            </ul>
        </nav>

        <section>
            <h2>Atención al cliente</h2>
            <address>
                +54 9 11 5555-5555<br>
                <a href="mailto:atencion@pawprint.com">atencion@pawprint.com</a><br>
                Calle Falsa 123
            </address>
        </section>

        <section aria-label="Información legal">
            <img src="img/logo.png" alt="Data fiscal">
            <p><small>© 2026 Paw Print. Todos los derechos reservados.</small></p>
        </section>
    </footer>

</body>
</html>