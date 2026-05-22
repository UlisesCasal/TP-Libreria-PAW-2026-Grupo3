<!DOCTYPE html>
<html lang="es">
<head>
    <title>Crear libro</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/crear-libro.css">
</head>
<body>
    <?php require __DIR__ . '/parts/header.view.php'; ?>
     <main>
        <h2>Crear libro</h2> 
        <form action="/crear-libro" method="post" enctype="multipart/form-data">
            <fieldset>
            <legend>Datos del libro:</legend>
                <label for="titulo">Titulo</label>
                <input type="text" id="titulo" name="titulo" placeholder="ej.: El principito" required>
                <label for="autor">Autor</label>
                <input type="text" id="autor" name="autor" placeholder="ej.:Borges" required>
                <label for="genero">Genero</label>
                <input type="text" id="genero" name="genero" placeholder="ej.:Romantico" required>
                <label for="fecha-pub">Fecha de publicacion</label>
                <input type="date" id="fecha-pub" name="fecha-pub" placeholder="ej.:02/11/2022" required>
                <label for="precio">Precio</label>
                <input type="number" id="precio" name="precio" min="0" step="0.01" placeholder="ej.:120.00" required>
                <label for="stock">Stock</label>
                <input type="number" id="stock" name="stock" min="0" placeholder="ej.:5" required>
                <label for="isbn">ISBN</label>
                <input type="text" id="isbn" name="isbn" placeholder="ej.:978-84-376-0494-7" required>
                <label for="paginas">Paginas</label>
                <input type="number" id="paginas" name="paginas" min="1" placeholder="ej.:978" required>
                <label for="descr">Descripcion</label>
                <textarea id="descr" name="descr" rows="4" placeholder="ej.: Novela romántica que se basa en Romeo y Julieta" required></textarea>  
                <label for="tapa">Tapa</label>
                <input type="file" id="tapa" name="tapa" required>     
                <label for="contratapa">Contratapa</label>
                <input type="file" id="contratapa" name="contratapa" required>                    
            </fieldset>
            <button type="submit">Crear libro</button>
        </form>
    </main>
    <?php require __DIR__ . '/parts/footer.view.php'; ?>
</body>
</html>
