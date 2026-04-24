<header>
    <img src="Logo_Violeta2.JPG" alt="Logo Violeta">
    <h1>Libreria</h1>    
    <nav>
        <ul>
            <li><a href="/">Inicio</a></li>
            <li><a href="/libros">Libros</a></li>
            <li><a href="/autores">Autores</a></li>
            <li><a href="/contacto">Contacto</a></li>
        </ul>
        <form action="/libros" method="get" role="search">
            <label for="busqueda-libros">Buscar libro:</label>
            <input type="search" id="busqueda-libros" name="q" placeholder="Título, autor o género">
            <button type="submit">Buscar</button>
        </form>
        <div class="nav-acciones">
            <a href="/crearCuenta">Registro</a>
            <a href="/carrito">Carrito</a>
        </div>
    </nav>
</header>