<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/catalogo.css">
    <script>
        // Inyectamos los datos de los libros procesados por el backend para tu JS dinámico
        window.ALL_BOOKS = <?= json_encode($libros) ?>;
    </script>
    <script src="/assets/js/catalogo.js" defer></script>
    <title>Catálogo — Libreria</title>
</head>
<body>
<?php require __DIR__ . '/parts/header.view.php'; ?>
    <main>
        <h1 class="catalogo-titulo">Catálogo</h1>
        
        <div class="catalogo-layout">
            <aside class="catalogo-filtros">
                <h2>Filtro y orden de catálogo</h2>
                <form id="filter-form">
                    <label for="q">Buscar</label>
                    <div class="campo-busqueda-wrapper">
                        <input type="text" id="q" name="q" placeholder="Título, autor o género">
                        <div id="sugerencias-busqueda" class="sugerencias"></div>
                    </div>

                    <fieldset>
                        <legend>Orden</legend>
                        <label for="orden">Ordenar por:</label>
                        <select id="orden" name="orden">
                            <option value="az">A→Z</option>
                            <option value="za">Z→A</option>
                            <option value="precio-ascendente">Menor precio</option>
                            <option value="precio-descendente">Mayor precio</option>
                        </select>
                    </fieldset>
                    
                    <fieldset>
                        <legend>Filtros</legend>
                        <label for="autor">Autor</label>
                        <input type="text" id="autor" name="autor" placeholder="ej.: Borges">
                        
                        <label for="genero">Género</label>
                        <select id="genero" name="genero">
                            <option value="">Todos</option>
                            <option value="novela">Novela</option>
                            <option value="poesia">Poesía</option>
                            <option value="fantasia">Fantasía</option>
                            <option value="policial">Policial</option>
                            <option value="cuento">Cuento</option>
                        </select>
                        
                        <label for="precio_min">Precio mínimo</label>
                        <input type="number" id="precio_min" name="precio_min" min="0" step="1">
                        
                        <label for="precio_max">Precio maximum</label>
                        <input type="number" id="precio_max" name="precio_max" min="0" step="1">
                    </fieldset>
                    
                    <button type="submit">Limpiar Filtros</button>
                </form>
            </aside>
            
            <section class="catalogo-resultados">
                <h2 class="visually-hidden">Resultados de búsqueda</h2>
                
                <div id="historial-busquedas" style="display:none">
                    <h2>Últimas búsquedas</h2>
                    <ul></ul>
                </div>

                <div id="catalog-results">
                    <ul id="book-list">
                        <?php foreach ($libros as $libro): ?>
                        <li>
                            <a href="/libro?id=<?= $libro['id'] ?>">
                                <img src="/assets/img/<?= htmlspecialchars($libro['imagen']) ?>"
                                     alt="<?= htmlspecialchars($libro['titulo']) ?>">
                            </a>
                            <p><?= htmlspecialchars($libro['titulo']) ?></p>
                            <p><?= htmlspecialchars($libro['autor']) ?></p>
                            <p>$<?= number_format($libro['precio'], 0, ',', '.') ?></p>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <div id="pagination-controls" class="pagination">
                    </div>
                
                <div id="scroll-anchor" style="height: 20px;"></div>
            </section>
        </div>
    </main>
<?php require __DIR__ . '/parts/footer.view.php'; ?>
</body>
</html>
