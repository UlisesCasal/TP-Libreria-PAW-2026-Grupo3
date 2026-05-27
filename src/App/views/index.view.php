<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/swiffyslider.css">
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

        <!-- Sección carousel de libros destacados -->
        <section class="seccion-carousel">
            <header>
                <h2>Libros destacados</h2>
            </header>

            <div
                class="swiffy-slider"
                data-swiffy
                data-items="3"
                data-effect="slide"
                data-loop="true"
            >
                <!-- Track de slides -->
                <div class="swiffy-track">
                    <?php foreach ($libros as $libro): ?>
                    <div class="swiffy-slide">
                        <a href="/libro?id=<?= htmlspecialchars($libro['id']) ?>" class="swiffy-book-card">
                            <div class="swiffy-book-cover">
                                <img
                                    src="/assets/tapas/<?= htmlspecialchars($libro['imagen']) ?>"
                                    alt="<?= htmlspecialchars($libro['titulo']) ?>"
                                    onerror="this.onerror=null;this.src='/assets/tapas/libro1L.jpg'"
                                >
                            </div>
                            <div class="swiffy-book-info">
                                <span class="swiffy-book-title"><?= htmlspecialchars($libro['titulo']) ?></span>
                                <span class="swiffy-book-author"><?= htmlspecialchars($libro['autor']) ?></span>
                                <span class="swiffy-book-genre"><?= htmlspecialchars($libro['genero']) ?></span>
                            </div>
                            <div class="swiffy-book-footer">
                                <span class="swiffy-book-price">$<?= number_format($libro['precio'], 0, ',', '.') ?></span>
                                <span class="swiffy-book-btn">Ver libro</span>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Botones prev/next -->
                <button type="button" class="swiffy-btn swiffy-btn-prev" aria-label="Slide anterior">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6"/>
                    </svg>
                </button>
                <button type="button" class="swiffy-btn swiffy-btn-next" aria-label="Slide siguiente">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"/>
                    </svg>
                </button>

                <!-- Dots de navegación -->
                <div class="swiffy-dots" role="tablist" aria-label="Navegación de slides"></div>

                <!-- Thumbs -->
                <div class="swiffy-thumbs" role="group" aria-label="Miniaturas"></div>

                <!-- Selector de efecto de transición -->
                <div class="swiffy-effect-switcher" role="group" aria-label="Efectos de transición"></div>
            </div>
        </section>
    </main>

    <?php require __DIR__ . '/parts/footer.view.php'; ?>

    <script src="/assets/js/constructorElementos.js"></script>
    <script src="/assets/js/swiffyslider.js"></script>
</body>

</html>
