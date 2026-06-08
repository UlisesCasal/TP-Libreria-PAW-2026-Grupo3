<?php
// Maneja el acceso a los libros sobre la base de datos relacional (PostgreSQL).
// El listado y la búsqueda se resuelven con SQL (WHERE / ORDER BY) en el servidor.
namespace PAW\Model;

use PAW\Core\Database;
use PDO;

class LibroModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->pdo();
    }

    // Devuelve todos los libros como array de arrays asociativos
    public function getAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM libros ORDER BY titulo');
        return array_map([$this, 'normalizar'], $stmt->fetchAll());
    }

    // Devuelve un libro por su ID o null si no existe
    public function getById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM libros WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->normalizar($row) : null;
    }

    // Filtra y ordena libros según los parámetros recibidos.
    // La búsqueda se realiza SOBRE LA BASE DE DATOS (WHERE/ORDER BY con
    // parámetros enlazados para prevenir inyección SQL).
    public function filtrar(array $filtros): array
    {
        $where  = [];
        $params = [];

        if (!empty($filtros['q'])) {
            $where[] = '(titulo ILIKE :q OR autor ILIKE :q OR genero ILIKE :q)';
            $params[':q'] = '%' . $filtros['q'] . '%';
        }

        if (!empty($filtros['autor'])) {
            $where[] = 'autor ILIKE :autor';
            $params[':autor'] = '%' . $filtros['autor'] . '%';
        }

        if (!empty($filtros['genero'])) {
            $where[] = 'genero = :genero';
            $params[':genero'] = $filtros['genero'];
        }

        if (isset($filtros['precio_min']) && $filtros['precio_min'] !== '') {
            $where[] = 'precio >= :precio_min';
            $params[':precio_min'] = (float)$filtros['precio_min'];
        }

        if (isset($filtros['precio_max']) && $filtros['precio_max'] !== '') {
            $where[] = 'precio <= :precio_max';
            $params[':precio_max'] = (float)$filtros['precio_max'];
        }

        $orderBy = match ($filtros['orden'] ?? 'az') {
            'za'                 => 'titulo DESC',
            'precio-ascendente'  => 'precio ASC',
            'precio-descendente' => 'precio DESC',
            default              => 'titulo ASC',
        };

        $sql = 'SELECT * FROM libros';
        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY ' . $orderBy;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return array_map([$this, 'normalizar'], $stmt->fetchAll());
    }

    // Convierte los tipos de las columnas al mismo formato que esperaban
    // las vistas y los controladores (ints/floats nativos).
    private function normalizar(array $row): array
    {
        return [
            'id'          => (int)$row['id'],
            'titulo'      => $row['titulo'],
            'autor'       => $row['autor'],
            'genero'      => $row['genero'],
            'precio'      => (float)$row['precio'],
            'stock'       => (int)$row['stock'],
            'imagen'      => $row['imagen'],
            'isbn'        => $row['isbn'],
            'paginas'     => (int)$row['paginas'],
            'publicacion' => $row['publicacion'],
            'descripcion' => $row['descripcion'] ?? '',
        ];
    }

    // ─── Alta de libros ──────────────────────────────────────
    // Verifica si el ISBN del nuevo libro ya existe.
    public function existeIsbn(string $isbn_nuevo_libro): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM libros WHERE isbn = :isbn LIMIT 1');
        $stmt->execute([':isbn' => $isbn_nuevo_libro]);
        return $stmt->fetchColumn() !== false;
    }

    // Inserta el nuevo libro en la base de datos. El id lo genera el SERIAL.
    public function cargaLibro(array $datosLibro): void
    {
        $sql = 'INSERT INTO libros
                    (titulo, autor, genero, precio, stock, imagen, isbn, paginas, publicacion, descripcion)
                VALUES
                    (:titulo, :autor, :genero, :precio, :stock, :imagen, :isbn, :paginas, :publicacion, :descripcion)';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':titulo'      => $datosLibro['titulo'],
            ':autor'       => $datosLibro['autor'],
            ':genero'      => $datosLibro['genero'],
            ':precio'      => (float)$datosLibro['precio'],
            ':stock'       => (int)$datosLibro['stock'],
            ':imagen'      => $datosLibro['nombre_archi_tapa'],
            ':isbn'        => $datosLibro['isbn'],
            ':paginas'     => (int)$datosLibro['paginas'],
            ':publicacion' => $datosLibro['fecha-pub'],
            ':descripcion' => $datosLibro['descr'],
        ]);
    }
}
