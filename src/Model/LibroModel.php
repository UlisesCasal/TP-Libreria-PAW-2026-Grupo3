<?php
//esta clase tiene dos responsabilidades marcadas, una es
//tratar los libros existentes y otra es tratar los libros
//nuevos (validaciones,etc) podrian hacer dos clases (AltaLibro.php y 
//otra ManejoLibro.php) y que LibroModel las use segun lo que requiera
//de esta forma se dividirian las responsabilidades.
//Tambien se me ocurre hacer una clase libro que tenga como atributos
//los atributos del mismo
namespace PAW\Model;

class LibroModel
{
    private string $dbPath;

    public function __construct()
    {
        $this->dbPath = __DIR__ . '/libros.txt';
    }

    // Devuelve todos los libros como array de arrays asociativos
    public function getAll(): array
    {
        if (!file_exists($this->dbPath)) {
            return [];
        }

        $libros = [];
        $lines = file($this->dbPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $libro = $this->parsearLinea($line);
            if ($libro !== null) {
                $libros[] = $libro;
            }
        }

        return $libros;
    }

    // Devuelve un libro por su ID o null si no existe
    public function getById(int $id): ?array
    {
        foreach ($this->getAll() as $libro) {
            if ((int)$libro['id'] === $id) {
                return $libro;
            }
        }
        return null;
    }

    // Filtra y ordena libros según los parámetros recibidos
    public function filtrar(array $filtros): array
    {
        $libros = $this->getAll();

        if (!empty($filtros['q'])) {
            $q = mb_strtolower($filtros['q']);
            $libros = array_filter($libros, fn($l) =>
                str_contains(mb_strtolower($l['titulo']), $q) ||
                str_contains(mb_strtolower($l['autor']), $q) ||
                str_contains(mb_strtolower($l['genero']), $q)
            );
        }

        if (!empty($filtros['autor'])) {
            $autor = mb_strtolower($filtros['autor']);
            $libros = array_filter($libros, fn($l) =>
                str_contains(mb_strtolower($l['autor']), $autor)
            );
        }

        if (!empty($filtros['genero'])) {
            $genero = $filtros['genero'];
            $libros = array_filter($libros, fn($l) => $l['genero'] === $genero);
        }

        if (!empty($filtros['precio_min'])) {
            $min = (float)$filtros['precio_min'];
            $libros = array_filter($libros, fn($l) => (float)$l['precio'] >= $min);
        }

        if (!empty($filtros['precio_max'])) {
            $max = (float)$filtros['precio_max'];
            $libros = array_filter($libros, fn($l) => (float)$l['precio'] <= $max);
        }

        $libros = array_values($libros);

        $orden = $filtros['orden'] ?? 'az';
        usort($libros, function ($a, $b) use ($orden) {
            return match ($orden) {
                'za'                 => strcmp($b['titulo'], $a['titulo']),
                'precio-ascendente'  => $a['precio'] <=> $b['precio'],
                'precio-descendente' => $b['precio'] <=> $a['precio'],
                default              => strcmp($a['titulo'], $b['titulo']),
            };
        });

        return $libros;
    }

    private function parsearLinea(string $line): ?array
    {
        // Formato: id|titulo|autor|genero|precio|stock|imagen|isbn|paginas|publicacion|descripcion
        $parts = explode('|', $line, 11);
        if (count($parts) < 10) {
            return null;
        }

        return [
            'id'          => (int)trim($parts[0]),
            'titulo'      => trim($parts[1]),
            'autor'       => trim($parts[2]),
            'genero'      => trim($parts[3]),
            'precio'      => (float)trim($parts[4]),
            'stock'       => (int)trim($parts[5]),
            'imagen'      => trim($parts[6]),
            'isbn'        => trim($parts[7]),
            'paginas'     => (int)trim($parts[8]),
            'publicacion' => trim($parts[9]),
            'descripcion' => isset($parts[10]) ? trim($parts[10]) : '',
        ];
    }
    //metodos de valen para alta de libro
    //se fija que el isbn del nuevo libro exista
    public function existeIsbn(string $isbn_nuevo_libro): bool{
        if (!file_exists($this->dbPath)) {
            return false;
        }
        $lines = file($this->dbPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);//esta funcion devuelve un Array con todas las lineas del .txt ignorando lineas vacias e ignorando los \n
        foreach ($lines as $line) {//itera cada linea
            $parts = explode('|', $line);//obtiene cada "parte" de la linea separada por un | y las pone en un Array
            if (isset($parts[7]) && trim($parts[7]) === $isbn_nuevo_libro) {//isset verifica que la parte [7] (isbn) no este vacia o mal formada
            //trim elimina los espacios en blanco al inicio y al final quedando el string del isbn limpio
                return true;//si el isbn existe retorna true
            }
        }
        return false;//si no existe retorna false
    }
    //hace la carga del nuevo libro en "libros.txt"
    public function cargaLibro(array $datosLibro){
                                                                                                                                                                                               // Formato: id|titulo|autor|genero|precio|stock|imagen|isbn|paginas|publicacion|descripcion
        //$id=siguienteId();
        $linea =  11 . '|' . $datosLibro['titulo']. '|' . $datosLibro['autor'] . '|' . $datosLibro['genero']. '|' . $datosLibro['precio']. '|' . $datosLibro['stock']. '|' . $datosLibro['nombre_archi_tapa']. '|' . $datosLibro['isbn']. '|' . $datosLibro['paginas']. '|' . $datosLibro['fecha-pub']. '|' . $datosLibro['descr']. PHP_EOL;
        //faltaria la contratapa
        file_put_contents($this->dbPath, $linea, FILE_APPEND | LOCK_EX);
    }
    
}
