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
            <h2>Filtro y orden de catálogo</h2>
            <form action="/catalogo" method="get">
                <fieldset>
                    <legend>Orden</legend>
                    <label for="orden">Ordenar por:</label>
                    <select id="orden" name="orden">
                        <option value="az"                 <?= (($filtros['orden'] ?? '') === 'az')                 ? 'selected' : '' ?>>A→Z</option>
                        <option value="za"                 <?= (($filtros['orden'] ?? '') === 'za')                 ? 'selected' : '' ?>>Z→A</option>
                        <option value="precio-ascendente"  <?= (($filtros['orden'] ?? '') === 'precio-ascendente')  ? 'selected' : '' ?>>Menor precio</option>
                        <option value="precio-descendente" <?= (($filtros['orden'] ?? '') === 'precio-descendente') ? 'selected' : '' ?>>Mayor precio</option>
                    </select>
                </fieldset>
                <label for="autor">Autor</label>
                <input type="text" id="autor" name="autor" placeholder="ej.: Borges"
                       value="<?= htmlspecialchars($filtros['autor'] ?? '') ?>">
                <label for="genero">Género</label>
                <select id="genero" name="genero">
                    <option value="">Todos</option>
                    <option value="novela"   <?= (($filtros['genero'] ?? '') === 'novela')   ? 'selected' : '' ?>>Novela</option>
                    <option value="poesia"   <?= (($filtros['genero'] ?? '') === 'poesia')   ? 'selected' : '' ?>>Poesía</option>
                    <option value="fantasia" <?= (($filtros['genero'] ?? '') === 'fantasia') ? 'selected' : '' ?>>Fantasía</option>
                    <option value="policial" <?= (($filtros['genero'] ?? '') === 'policial') ? 'selected' : '' ?>>Policial</option>
                    <option value="cuento"   <?= (($filtros['genero'] ?? '') === 'cuento')   ? 'selected' : '' ?>>Cuento</option>
                </select>
                <label for="precio_min">Precio mínimo</label>
                <input type="number" id="precio_min" name="precio_min" min="1" step="1"
                       value="<?= htmlspecialchars($filtros['precio_min'] ?? '') ?>">
                <label for="precio_max">Precio máximo</label>
                <input type="number" id="precio_max" name="precio_max" min="1" step="1"
                       value="<?= htmlspecialchars($filtros['precio_max'] ?? '') ?>">
                <button type="submit">Buscar</button>
            </form>
        </section>
        <section>
            <h2>Resultados de búsqueda</h2>
            <?php if (empty($libros)): ?>
                <p>No se encontraron libros con los filtros aplicados.</p>
            <?php else: ?>
            <ul>
                <?php foreach ($libros as $libro): ?>
                <li>
                    <a href="/libro?id=<?= $libro['id'] ?>">
                        <img src="/model/<?= htmlspecialchars($libro['imagen']) ?>"
                             alt="<?= htmlspecialchars($libro['titulo']) ?>">
                    </a>
                    <p><?= htmlspecialchars($libro['titulo']) ?></p>
                    <p><?= htmlspecialchars($libro['autor']) ?></p>
                    <p>$<?= number_format($libro['precio'], 0, ',', '.') ?></p>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </section>
    </main>
<?php require __DIR__ . '/parts/footer.view.php'; ?>
</body>
</html>
