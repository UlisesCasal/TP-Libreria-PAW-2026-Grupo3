<header>
    <img src="/model/Logo_Violeta2.JPG" alt="Logo Violeta">
    <h1>Libreria</h1>
    <nav>
        <ul>
            <li><a href="/">Inicio</a></li>
            <li><a href="/catalogo">Catálogo</a></li>
            <li><a href="/mis-compras" style="white-space: nowrap;">Mis compras</a></li>
        </ul>
        <form action="/libro" method="get" role="search">
            <label for="busqueda-libros">Buscar libro:</label>
            <input type="search" id="busqueda-libros" name="q" placeholder="Título, autor o género">
            <button type="submit">Buscar</button>
        </form>
        <div class="nav-acciones">
            <?php if (isset($_SESSION['usuario'])): ?>
                <span class="user-greeting">Hola, <strong><?= htmlspecialchars(explode(' ', $_SESSION['usuario']['nombre'])[0]) ?></strong></span>
                <a href="/cerrar-sesion" class="btn-logout">Salir</a>
            <?php else: ?>
                <a href="/crearCuenta">Creá tu cuenta</a>
                <a href="/inicio-sesion">Ingresá</a>
            <?php endif; ?>
            <a href="/carrito" class="btn-carrito-nav">Carrito</a>
        </div>
    </nav>
</header>
