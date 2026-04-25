<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/carrito.css">
    <title>Carrito — Libreria</title>
</head>

<body>
    <?php require __DIR__ . '/parts/header.view.php'; ?>
    <main>
        <h2>Carrito de compras</h2>
        <form action="/formulario" class="carrito-form">
            <fieldset class="carrito-items">
                <legend>Libros seleccionados:</legend>
                <ul>
                    <li class="carrito-item">
                        <img src="/model/libro1L.jpg" alt="Portada libro">
                        <p class="item-titulo">Titulo + autor</p>
                        <p class="item-precio">$0</p>
                        <input type="number" name="cantidad_libro" min="1" value="1">
                        <button type="button" class="btn-borrar">Quitar</button>
                    </li>
                </ul>
            </fieldset>

            <section class="resumen-compra">
                <h2>Resumen de compra</h2>
                <ul>
                    <li><p>Subtotal: $0</p></li>
                    <li><p>Envío: $0</p></li>
                    <li><p>Total: $0</p></li>
                </ul>
                <a href="/formulario" class="btn-finalizar">Finalizar compra</a>
            </section>
        </form>
    </main>

    <?php require __DIR__ . '/parts/footer.view.php'; ?>
</body>

</html>
