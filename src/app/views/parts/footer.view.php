<footer>
    <section aria-label="Redes sociales">
        <h2>Seguinos</h2>
        <ul>
            <li><a href="https://www.instagram.com" target="_blank" rel="noopener noreferrer">Instagram</a></li>
            <li><a href="https://www.twitter.com" target="_blank" rel="noopener noreferrer">Twitter</a></li>
            <li><a href="https://www.facebook.com" target="_blank" rel="noopener noreferrer">Facebook</a></li>
        </ul>
    </section>

    <nav aria-label="Enlaces del sitio">
        <h2>Paw Print</h2>
        <ul>
            <?php
                // Obtiene la ruta actual
                $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                $currentPath = trim($currentPath, '/');
            ?>
            <?php if($currentPath !== 'nosotros'): ?>
                <li><a href="/nosotros">Nosotros</a></li>
            <?php endif; ?>
            <?php if($currentPath !== 'carrito'): ?>
                <li><a href="/carrito">Carrito</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <section>
        <h2>Atención al cliente</h2>
        <address>
            +54 9 11 5555-5555<br>
            <a href="mailto:atencion@pawprint.com">atencion@pawprint.com</a><br>
            Calle Falsa 123
        </address>
    </section>

    <section aria-label="Información legal">
        <p><small>© 2026 Paw Print. Todos los derechos reservados.</small></p>
    </section>
</footer>
