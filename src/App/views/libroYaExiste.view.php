<!DOCTYPE html>
<html lang="es">
<head>
    <title>Libro ya existe</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/resultado-libro.css">
</head>
<body>
    <?php require __DIR__ . '/parts/header.view.php'; ?>
    <main>
        <div class="resultado-card resultado-error">
            <span class="resultado-icono">✕</span>
            <h2>El libro ya existe</h2>
            <p>Ya hay un libro registrado con ese ISBN en el catálogo.</p>
            <a href="/crear-libro" class="btn-resultado">Volver al formulario</a>
        </div>
    </main>
    <?php require __DIR__ . '/parts/footer.view.php'; ?>
</body>
</html>
