<?php

namespace PAW\App\Controllers;

use PAW\Core\Database;

class SeedController
{
    public function execute()
    {
        try {
            $sql = file_get_contents(__DIR__ . '/../../db/schema.sql');
            if ($sql === false) {
                return ['error' => 'No se pudo leer db/schema.sql'];
            }

            $pdo = Database::getInstance()->pdo();
            $pdo->exec($sql);

            return ['success' => 'Base de datos inicializada correctamente'];
        } catch (\Throwable $e) {
            return ['error' => 'Error: ' . $e->getMessage()];
        }
    }
}
