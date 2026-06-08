<?php

namespace PAW\Core;

use PDO;

// Conexión a la base de datos relacional (PostgreSQL).
// Singleton, al igual que TwigEnvironment, para reutilizar la misma
// conexión PDO durante toda la petición.
class Database
{
    private static ?self $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $this->pdo = new PDO(
            self::buildDsn(),
            self::$user,
            self::$password,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );
    }

    private static ?string $user = null;
    private static ?string $password = null;

    // Construye el DSN de PostgreSQL a partir de DATABASE_URL
    // (postgres://user:pass@host:port/db) o, en su defecto, de las
    // variables de entorno PG* que expone Render.
    private static function buildDsn(): string
    {
        $url = getenv('DATABASE_URL');

        if ($url !== false && $url !== '') {
            $parts = parse_url($url);
            $host = $parts['host'] ?? 'localhost';
            $port = $parts['port'] ?? 5432;
            $db   = isset($parts['path']) ? ltrim($parts['path'], '/') : '';
            self::$user     = isset($parts['user']) ? urldecode($parts['user']) : null;
            self::$password = isset($parts['pass']) ? urldecode($parts['pass']) : null;
        } else {
            $host = getenv('PGHOST') ?: 'localhost';
            $port = getenv('PGPORT') ?: 5432;
            $db   = getenv('PGDATABASE') ?: 'libreria';
            self::$user     = getenv('PGUSER') ?: 'postgres';
            self::$password = getenv('PGPASSWORD') ?: '';
        }

        return sprintf('pgsql:host=%s;port=%s;dbname=%s', $host, $port, $db);
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function pdo(): PDO
    {
        return $this->pdo;
    }
}
