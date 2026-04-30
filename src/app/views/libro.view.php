<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="stylesheet" href="/assets/css/libro.css">
  <title>PAWPrints - <?= $libro ? htmlspecialchars($libro['titulo']) : 'Detalle del libro' ?></title>
</head>
<body>
  <?php require __DIR__ . '/parts/header.view.php'; ?>
  <main>
  <?php if ($libro === null): ?>
    <p>Libro no encontrado.</p>
  <?php else: ?>

    <figure>
      <img src="/model/<?= htmlspecialchars($libro['imagen']) ?>"
           alt="Tapa de <?= htmlspecialchars($libro['titulo']) ?>">
      <figcaption>Tapa libro</figcaption>
    </figure>

    <section aria-label="Opciones de tapa">
      <button type="button">Tapa Realista</button>
      <button type="button">Tapa digital</button>
    </section>

    <section aria-label="Datos del libro">
      <h2><?= htmlspecialchars($libro['titulo']) ?></h2>
      <h3><?= htmlspecialchars($libro['autor']) ?></h3>
      <p><strong>$<?= number_format($libro['precio'], 0, ',', '.') ?></strong></p>
    </section>

    <section aria-label="Comprar">
      <form action="/carrito" method="post">
        <input type="hidden" name="libro_id" value="<?= $libro['id'] ?>">
        <label for="cantidad">Cantidad:</label>
        <button type="button" aria-label="Disminuir cantidad">&#8592;</button>
        <input type="number" id="cantidad" name="cantidad"
               value="1" min="1" max="<?= $libro['stock'] ?>">
        <button type="button" aria-label="Aumentar cantidad">&#8594;</button>
        <button type="submit">Comprar</button>
      </form>
    </section>

    <section aria-label="Descripción del libro">
      <h4>Descripción</h4>
      <p><?= htmlspecialchars($libro['descripcion']) ?></p>
    </section>

    <section aria-label="Ficha técnica del libro">
      <header><h5>Datos técnicos</h5></header>
      <ul>
        <li><strong>ISBN:</strong> <span><?= htmlspecialchars($libro['isbn']) ?></span></li>
        <li><strong>Género:</strong> <span><?= htmlspecialchars(ucfirst($libro['genero'])) ?></span></li>
        <li><strong>Idioma:</strong> <span>Español</span></li>
        <li><strong>Páginas:</strong> <span><?= $libro['paginas'] ?></span></li>
        <li><strong>Publicación:</strong>
          <time datetime="<?= htmlspecialchars($libro['publicacion']) ?>">
            <?= date('d/m/Y', strtotime($libro['publicacion'])) ?>
          </time>
        </li>
        <li><strong>Stock:</strong> <span><?= $libro['stock'] ?> unidades</span></li>
      </ul>
    </section>

    <?php if (!empty($relacionados)): ?>
    <section aria-label="Libros relacionados">
      <header>
        <h2>Libros relacionados</h2>
      </header>
      <?php foreach ($relacionados as $rel): ?>
      <article>
        <h3><?= htmlspecialchars($rel['titulo']) ?></h3>
        <figure>
          <img src="/model/<?= htmlspecialchars($rel['imagen']) ?>"
               alt="<?= htmlspecialchars($rel['titulo']) ?>">
          <footer>
            <p><a href="/libro?id=<?= $rel['id'] ?>">Ver libro</a></p>
          </footer>
        </figure>
      </article>
      <?php endforeach; ?>
    </section>
    <?php endif; ?>

  <?php endif; ?>
  </main>
<?php require __DIR__ . '/parts/footer.view.php'; ?>
</body>
</html>
