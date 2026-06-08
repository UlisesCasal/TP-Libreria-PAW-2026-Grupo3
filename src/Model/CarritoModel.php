<?php

namespace PAW\Model;

use PAW\Core\Database;
use PDO;

class CarritoModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->pdo();
    }

    // Devuelve todos los items del carrito del usuario con datos del libro
    public function obtenerItems(int $usuarioId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT ci.id, ci.cantidad,
                    l.id AS libro_id, l.titulo, l.autor, l.precio, l.imagen
             FROM carrito_items ci
             JOIN libros l ON l.id = ci.libro_id
             WHERE ci.usuario_id = :uid
             ORDER BY ci.id'
        );
        $stmt->execute([':uid' => $usuarioId]);
        return $stmt->fetchAll();
    }

    // Agrega un libro al carrito. Si ya existe, incrementa la cantidad.
    public function agregarItem(int $usuarioId, int $libroId, int $cantidad = 1): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO carrito_items (usuario_id, libro_id, cantidad)
             VALUES (:uid, :lid, :cantidad)
             ON CONFLICT (usuario_id, libro_id)
             DO UPDATE SET cantidad = carrito_items.cantidad + :cantidad'
        );
        $stmt->execute([':uid' => $usuarioId, ':lid' => $libroId, ':cantidad' => $cantidad]);
    }

    // Elimina un libro específico del carrito del usuario
    public function eliminarItem(int $usuarioId, int $libroId): void
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM carrito_items WHERE usuario_id = :uid AND libro_id = :lid'
        );
        $stmt->execute([':uid' => $usuarioId, ':lid' => $libroId]);
    }

    // Elimina todos los items del carrito del usuario (se llama al finalizar la compra)
    public function vaciarCarrito(int $usuarioId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM carrito_items WHERE usuario_id = :uid');
        $stmt->execute([':uid' => $usuarioId]);
    }
}
