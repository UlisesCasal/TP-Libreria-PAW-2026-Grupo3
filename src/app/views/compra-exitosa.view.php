<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/formulario.css">
    <title>¡Gracias por tu compra! - Paw Prints</title>
    <style>
        /* Estilos para empujar el footer al fondo */
        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1 0 auto; /* Esto hace que el main crezca para ocupar el espacio disponible */
        }

        footer {
            flex-shrink: 0;
        }

        .exito-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .mensaje-exito-contenedor {
            background: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            max-width: 550px;
            width: 100%;
            text-align: center;
            border-top: 5px solid #6a1b9a;
        }

        .icono-exito {
            width: 70px;
            height: 70px;
            background-color: #e8f5e9;
            color: #2e7d32;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            margin: 0 auto 20px;
            font-weight: bold;
        }

        .mensaje-exito-contenedor h2 {
            color: #4a148c;
            font-size: 1.8rem;
            margin-bottom: 15px;
        }

        .mensaje-exito-contenedor p {
            color: #555;
            font-size: 1.05rem;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        .detalles-email {
            background-color: #f3e5f5;
            padding: 10px 15px;
            border-radius: 6px;
            color: #6a1b9a;
            display: inline-block;
            margin-top: 10px;
            font-weight: 500;
        }

        .btn-volver {
            margin-top: 30px;
            padding: 10px 25px;
            background-color: #6a1b9a;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(106, 27, 154, 0.2);
        }

        .btn-volver:hover {
            background-color: #4a148c;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(106, 27, 154, 0.3);
        }
    </style>
</head>

<body>
    <?php require __DIR__ . '/parts/header.view.php'; ?>

    <main>
        <div class="exito-wrapper">
            <section class="mensaje-exito-contenedor">
                <div class="icono-exito">✓</div>
                <h2>¡Pedido confirmado!</h2>
                <p>Gracias por tu <?= htmlspecialchars($tipoOperacion) ?>. Hemos procesado tu solicitud con éxito.</p>
                <p>Te enviamos un correo con el resumen y los pasos a seguir.</p>
                <p class="detalles-email">Entrega estimada: 5 a 7 días hábiles</p>
            </section>

            <a href="/" class="btn-volver">Volver a la tienda</a>
        </div>
    </main>

    <?php require __DIR__ . '/parts/footer.view.php'; ?>
</body>

</html>
