<!DOCTYPE html>
<html lang="es">
<head>
    <title>Libro creado</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/resultado-libro.css">
</head>
<body>
    <?php require __DIR__ . '/parts/header.view.php'; ?>
    <main>
        <div class="resultado-card resultado-exito">
            <span class="resultado-icono">✓</span>
            <h2>Libro creado exitosamente</h2>
            <p>El libro fue agregado al catálogo correctamente.</p>
            <a href="/" class="btn-resultado">Volver al inicio</a>
        </div>
    </main>
    <?php require __DIR__ . '/parts/footer.view.php'; ?>
</body>
</html>
