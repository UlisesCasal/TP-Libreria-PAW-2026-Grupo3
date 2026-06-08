<?php

namespace PAW\Model;

use PAW\Core\Database;
use PDO;

class UsuarioModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->pdo();
    }

    public function verificar(string $email, string $password): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM usuarios WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            return [
                'id'       => (int)$user['id'],
                'nombre'   => $user['nombre'],
                'apellido' => $user['apellido'],
                'email'    => $user['email'],
                'rol'      => $user['rol'],
            ];
        }

        return null;
    }

    public function registrar(string $nombre, string $apellido, string $email, string $password, string $rol = 'cliente'): bool
    {
        if ($this->existeEmail($email)) {
            return false;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->pdo->prepare(
            'INSERT INTO usuarios (nombre, apellido, email, password, rol)
             VALUES (:nombre, :apellido, :email, :password, :rol)'
        );
        $stmt->execute([
            ':nombre'   => $nombre,
            ':apellido' => $apellido,
            ':email'    => $email,
            ':password' => $hash,
            ':rol'      => $rol,
        ]);

        return true;
    }

    public function existeEmail(string $email): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM usuarios WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        return $stmt->fetchColumn() !== false;
    }
}
