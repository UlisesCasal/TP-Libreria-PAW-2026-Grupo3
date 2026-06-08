<?php

// ============================================================
// Inicializa la base de datos: crea las tablas y carga los
// libros semilla ejecutando db/schema.sql.
//
// Uso (una sola vez, local o contra la BD de Render):
//   DATABASE_URL=postgres://user:pass@host:port/db php db/seed.php
// ============================================================

require __DIR__ . '/../vendor/autoload.php';

use PAW\Core\Database;

$sql = file_get_contents(__DIR__ . '/schema.sql');
if ($sql === false) {
    fwrite(STDERR, "No se pudo leer db/schema.sql\n");
    exit(1);
}

try {
    $pdo = Database::getInstance()->pdo();
    $pdo->exec($sql);
    echo "Esquema y datos semilla aplicados correctamente.\n";
} catch (\Throwable $e) {
    fwrite(STDERR, "Error al aplicar el esquema: " . $e->getMessage() . "\n");
    exit(1);
}
