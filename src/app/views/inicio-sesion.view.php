<!DOCTYPE html>
<html lang="es">

<head>
    <title>Inicio de Sesión - PAWPrints</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/inicio-sesion.css">
</head>

<body>
    <?php require __DIR__ . '/parts/header.view.php'; ?>
    <main>
        <h2>Iniciar sesión</h2>
        <form action="/inicio-sesion" method="post">
            <fieldset>
                <legend>Datos personales:</legend>
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="ej.:tuemail@email.com" required>
                <label for="contra">Contraseña</label>
                <input type="password" name="password" id="contra" placeholder="ej.:tucontraseña" required>
            </fieldset>
            <button type="submit">Iniciar sesión</button>
        </form>
    </main>
    <?php require __DIR__ . '/parts/footer.view.php'; ?>
</body>

</html>
