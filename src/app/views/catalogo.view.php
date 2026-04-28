<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/catalogo.css">
    <title>Catálogo — Libreria</title>
</head>
<body>
<?php require __DIR__ . '/parts/header.view.php'; ?>
    <main>
        <h2>Catalogo</h2>
        <section>
            <h2>Filtro y orden de catalogo</h2>
            <form action="/catalogo" method="get">
                <fieldset>
                    <legend>Orden</legend>
                    <label for="orden">Ordenar por:</label>
                    <select id="orden" name="orden">
                        <option value="az">A->Z</option>
                        <option value="za">Z->A</option>
                        <option value="precio-ascendente">Menor precio</option>
                        <option value="precio-descendente">Mayor precio</option>
                    </select>
                </fieldset>
                <label for="autor">Autor</label>
                <input type="text" id="autor" name="autor" placeholder="ej.:Borges">
                <label for="genero">Género</label>
                <select id="genero" name="genero">
                    <option value="">Todos</option>
                    <option value="novela">Novela</option>
                    <option value="poesia">Poesía</option>
                    <option value="fantasia">Fantasía</option>
                    <option value="policial">Policial</option>
                </select>
                <label for="precio_min">Precio mínimo</label>
                <input type="number" id="precio_min" name="precio_min" min="1" step="1">
                <label for="precio_max">Precio máximo</label>
                <input type="number" id="precio_max" name="precio_max" min="1" step="1">
                <button type="submit">Buscar</button>
            </form>
        </section>
        <section>
            <h2>Resultados de búsqueda</h2>
            <ul>
                <li>
                    <a href="/libro"><img src="/model/libro1L.jpg" alt="Libro 1"></a>
                    <p>Nombre libro 1</p>
                </li>
                <li>
                    <a href="/libro"><img src="/model/libro2L.webp" alt="Libro 2"></a>
                    <p>Nombre libro 2</p>
                </li>
                <li>
                    <a href="/libro"><img src="/model/Libro3L.webp" alt="Libro 3"></a>
                    <p>Nombre libro 3</p>
                </li>
            </ul>
        </section>
    </main>
<?php require __DIR__ . '/parts/footer.view.php'; ?>
</body>
</html>
