<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/style.css">
    <title>Mis Compras - Paw Prints</title>
    <style>
        .historial-contenedor {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            min-height: 50vh;
        }
        .sin-compras {
            text-align: center;
            background: #f9f9f9;
            padding: 50px;
            border-radius: 8px;
            border: 2px dashed #ddd;
        }
        .sin-compras h3 {
            color: #6a1b9a;
            margin-bottom: 20px;
        }
        .btn-comprar {
            display: inline-block;
            padding: 10px 20px;
            background: #6a1b9a;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <?php require __DIR__ . '/parts/header.view.php'; ?>

    <main class="historial-contenedor">
        <h2>Mis compras</h2>

        <?php if (empty($compras)): ?>
            <div class="sin-compras">
                <h3>Aún no has realizado ninguna compra</h3>
                <p>¡Explora nuestro catálogo y encuentra tu próximo libro favorito!</p>
                <br>
                <a href="/catalogo" class="btn-comprar">Ver catálogo</a>
            </div>
        <?php else: ?>
            <!-- Aquí iría la tabla o lista de compras si hubiera datos -->
            <p>Tienes compras realizadas.</p>
        <?php endif; ?>
    </main>

    <?php require __DIR__ . '/parts/footer.view.php'; ?>
</body>

</html>
