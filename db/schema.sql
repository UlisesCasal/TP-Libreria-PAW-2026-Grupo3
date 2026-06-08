-- ============================================================
-- Esquema de la base de datos relacional (PostgreSQL)
-- Librería PAW - Grupo 3
-- Idempotente: se puede ejecutar varias veces sin error.
-- ============================================================

-- ------------------------------------------------------------
-- Tabla USUARIOS
-- Reemplaza al antiguo db.txt (email|hash|nombre).
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
    id        SERIAL PRIMARY KEY,
    nombre    TEXT NOT NULL,
    apellido  TEXT NOT NULL DEFAULT '',
    email     TEXT NOT NULL UNIQUE,
    password  TEXT NOT NULL,
    rol       TEXT NOT NULL DEFAULT 'cliente'
);

-- ------------------------------------------------------------
-- Tabla LIBROS
-- Mismas columnas que antes se parseaban desde libros.txt
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS libros (
    id          SERIAL PRIMARY KEY,
    titulo      TEXT          NOT NULL,
    autor       TEXT          NOT NULL,
    genero      TEXT          NOT NULL,
    precio      NUMERIC(12,2) NOT NULL DEFAULT 0,
    stock       INTEGER       NOT NULL DEFAULT 0,
    imagen      TEXT          NOT NULL DEFAULT '',
    isbn        TEXT          UNIQUE,
    paginas     INTEGER       NOT NULL DEFAULT 0,
    publicacion TEXT          NOT NULL DEFAULT '',
    descripcion TEXT          NOT NULL DEFAULT ''
);

-- ------------------------------------------------------------
-- Tabla CARRITO_ITEMS (DDL corregido respecto del diagrama)
-- El carrito debe poder contener VARIOS libros distintos por
-- usuario: un renglón por (usuario, libro). El UNIQUE evita
-- duplicar el mismo libro y permite acumular cantidad.
-- (Modelo documentado; el carrito persistente queda fuera de
--  alcance de esta entrega.)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS carrito_items (
    id          SERIAL PRIMARY KEY,
    usuario_id  INTEGER NOT NULL,
    libro_id    INTEGER NOT NULL REFERENCES libros(id),
    cantidad    INTEGER NOT NULL DEFAULT 1 CHECK (cantidad > 0),
    UNIQUE (usuario_id, libro_id)
);

-- ------------------------------------------------------------
-- Tabla PEDIDOS (permanente)
-- Cabecera de cada compra/reserva: datos de envío, pago y
-- destinatario. Un pedido pertenece a un usuario.
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS pedidos (
    id            SERIAL PRIMARY KEY,
    usuario_id    INTEGER NOT NULL REFERENCES usuarios(id),
    fecha         TIMESTAMP NOT NULL DEFAULT NOW(),
    tipo_envio    TEXT NOT NULL,
    metodo_pago   TEXT NOT NULL,
    estado        TEXT NOT NULL DEFAULT 'pendiente',
    nombre_dest   TEXT NOT NULL,
    direccion     TEXT NOT NULL DEFAULT '',
    ciudad        TEXT NOT NULL DEFAULT '',
    provincia     TEXT NOT NULL DEFAULT '',
    pais          TEXT NOT NULL DEFAULT '',
    codigo_postal TEXT NOT NULL DEFAULT '',
    telefono      TEXT NOT NULL DEFAULT ''
);

-- ------------------------------------------------------------
-- Tabla PEDIDO_ITEMS (permanente)
-- Detalle de cada pedido: un renglón por libro, con la cantidad
-- y el precio_unitario congelado al momento de la compra.
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS pedido_items (
    id              SERIAL PRIMARY KEY,
    pedido_id       INTEGER NOT NULL REFERENCES pedidos(id) ON DELETE CASCADE,
    libro_id        INTEGER NOT NULL REFERENCES libros(id),
    cantidad        INTEGER NOT NULL DEFAULT 1 CHECK (cantidad > 0),
    precio_unitario NUMERIC(12,2) NOT NULL DEFAULT 0
);

-- ------------------------------------------------------------
-- Datos semilla (libros válidos provenientes de libros.txt).
-- Se omiten las líneas de prueba corruptas/duplicadas.
-- ON CONFLICT (isbn) DO NOTHING -> no duplica si ya existen.
-- ------------------------------------------------------------
INSERT INTO libros (titulo, autor, genero, precio, stock, imagen, isbn, paginas, publicacion, descripcion) VALUES
('La Rueda del Tiempo: El Gran Cacique', 'Robert Jordan', 'fantasia', 20000, 15, 'libro1L.jpg', '9789505472413', 688, '2022-03-03', 'Rand, acosado por inquietantes sueños sobre una espada de cristal, decide abandonar a sus compañeros tras un ataque de Engendros de la Sombra y se encamina hacia Tear para lograr descubrir quién es realmente.'),
('Ficciones', 'Jorge Luis Borges', 'cuento', 8500, 30, 'libro2L.webp', '9788420633466', 192, '1993-01-01', 'Colección de cuentos magistrales que incluyen El jardín de senderos que se bifurcan y Artificios, obras fundamentales de la literatura latinoamericana.'),
('El nombre de la rosa', 'Umberto Eco', 'policial', 15000, 20, 'Libro3L.webp', '9788408163435', 574, '2012-05-01', 'Una novela histórica situada en una abadía medieval donde un monje franciscano investiga una serie de muertes misteriosas entre manuscritos y secretos.'),
('Cien años de soledad', 'Gabriel García Márquez', 'novela', 12000, 25, 'libro1L.jpg', '9780060883287', 432, '2003-01-01', 'La historia de la familia Buendía a lo largo de siete generaciones en el pueblo ficticio de Macondo, obra cumbre del realismo mágico.'),
('El Aleph', 'Jorge Luis Borges', 'cuento', 7500, 18, 'libro2L.webp', '9789500703840', 224, '2011-01-01', 'Recopilación de cuentos fantásticos que exploran temas como el tiempo, el infinito y los laberintos, con el célebre cuento El Aleph.'),
('1984', 'George Orwell', 'novela', 9800, 40, 'Libro3L.webp', '9780451524935', 328, '1961-01-01', 'Novela distópica que narra la vida en una sociedad totalitaria controlada por el Gran Hermano, donde el pensamiento libre está prohibido.'),
('El Señor de los Anillos', 'J.R.R. Tolkien', 'fantasia', 25000, 10, 'libro1L.jpg', '9780618640157', 1178, '2004-01-01', 'La épica historia de la Comunidad del Anillo en su búsqueda para destruir el Anillo Único y derrotar al Señor Oscuro Sauron.'),
('Crimen y Castigo', 'Fiódor Dostoyevski', 'novela', 11000, 12, 'libro2L.webp', '9780143058144', 671, '2003-01-01', 'El estudiante Raskolnikov comete un asesinato y lucha con su conciencia, en una profunda exploración de la culpa y la redención.'),
('El código Da Vinci', 'Dan Brown', 'policial', 8900, 35, 'Libro3L.webp', '9780307474278', 454, '2009-01-01', 'El profesor Robert Langdon investiga un misterio que involucra obras de Leonardo Da Vinci y secretos de la Iglesia Católica.'),
('Harry Potter y la Piedra Filosofal', 'J.K. Rowling', 'fantasia', 13500, 50, 'libro1L.jpg', '9788478884452', 264, '1999-01-01', 'El joven Harry Potter descubre en su undécimo cumpleaños que es un mago y comienza sus estudios en el Colegio Hogwarts de Magia y Hechicería.'),
('El principito', 'Antoine de Saint-Exupéry', 'Narrativo', 30000, 9, 'principito tapa.png', '8675673-35646-335635', 92, '1943-04-06', 'La historia comienza cuando un aviador se queda varado en el desierto del Sahara debido a un desperfecto en su avión. Allí conoce a un pequeño niño extraterrestre, el Principito, quien le pide que le dibuje un cordero.')
ON CONFLICT (isbn) DO NOTHING;
