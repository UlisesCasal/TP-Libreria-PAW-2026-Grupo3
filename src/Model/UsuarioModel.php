<?php

namespace PAW\Model;

class UsuarioModel
{
    private string $dbPath;

    public function __construct()
    {
        $this->dbPath = __DIR__ . '/db.txt';
    }

    // Verifica credenciales. Devuelve datos del usuario o null si falla.
    public function verificar(string $email, string $password): ?array
    {
        if (!file_exists($this->dbPath)) {
            return null;
        }

        $lines = file($this->dbPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $parts = explode('|', $line);
            if (count($parts) < 2) {
                continue;
            }

            $storedEmail = trim($parts[0]);
            $storedHash  = trim($parts[1]);
            $nombre      = isset($parts[2]) ? trim($parts[2]) : 'Usuario';

            if ($storedEmail === $email && password_verify($password, $storedHash)) {
                return ['email' => $storedEmail, 'nombre' => $nombre];
            }
        }

        return null;
    }

    // Registra un nuevo usuario. Devuelve false si el email ya existe.
    public function registrar(string $nombre, string $email, string $password): bool
    {
        if ($this->existeEmail($email)) {
            return false;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $linea = $email . '|' . $hash . '|' . $nombre . PHP_EOL;

        file_put_contents($this->dbPath, $linea, FILE_APPEND | LOCK_EX);
        return true;
    }

    // Verifica si un email ya está registrado.
    public function existeEmail(string $email): bool
    {
        if (!file_exists($this->dbPath)) {
            return false;
        }

        $lines = file($this->dbPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $parts = explode('|', $line);
            if (isset($parts[0]) && trim($parts[0]) === $email) {
                return true;
            }
        }

        return false;
    }
}
