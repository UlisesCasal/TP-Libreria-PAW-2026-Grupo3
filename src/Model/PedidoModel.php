<?php

namespace PAW\Model;

use PAW\Core\Database;
use PDO;

class PedidoModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->pdo();
    }

    // Crea un pedido con sus items a partir del carrito. Usa transacción para garantizar consistencia.
    // $datos: tipo_envio, metodo_pago, nombre_destinatario, direccion, ciudad, provincia, pais, codigo_postal, telefono
    // $items: array de ['libro_id', 'cantidad', 'precio_unitario']
    // Devuelve el id del pedido creado.
    public function crear(int $usuarioId, array $datos, array $items): int
    {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare(
                'INSERT INTO pedidos
                    (usuario_id, fecha, tipo_envio, metodo_pago, estado,
                     nombre_destinatario, direccion, ciudad, provincia, pais, codigo_postal, telefono)
                 VALUES
                    (:uid, NOW(), :tipo_envio, :metodo_pago, :estado,
                     :nombre_dest, :direccion, :ciudad, :provincia, :pais, :codigo_postal, :telefono)
                 RETURNING id'
            );
            $stmt->execute([
                ':uid'           => $usuarioId,
                ':tipo_envio'    => $datos['tipo_envio'],
                ':metodo_pago'   => $datos['metodo_pago'],
                ':estado'        => 'en_proceso',
                ':nombre_dest'   => $datos['nombre_destinatario'] ?? '',
                ':direccion'     => $datos['direccion'] ?? '',
                ':ciudad'        => $datos['ciudad'] ?? '',
                ':provincia'     => $datos['provincia'] ?? '',
                ':pais'          => $datos['pais'] ?? '',
                ':codigo_postal' => $datos['codigo_postal'] ?? '',
                ':telefono'      => $datos['telefono'] ?? '',
            ]);
            $pedidoId = (int)$stmt->fetchColumn();

            $stmtItem = $this->pdo->prepare(
                'INSERT INTO pedido_items (pedido_id, libro_id, cantidad, precio_unitario)
                 VALUES (:pedido_id, :libro_id, :cantidad, :precio)'
            );
            foreach ($items as $item) {
                $stmtItem->execute([
                    ':pedido_id' => $pedidoId,
                    ':libro_id'  => $item['libro_id'],
                    ':cantidad'  => $item['cantidad'],
                    ':precio'    => $item['precio_unitario'],
                ]);
            }

            $this->pdo->commit();
            return $pedidoId;
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    // Devuelve todos los pedidos con datos del usuario y total (para el panel del personal)
    public function obtenerTodos(): array
    {
        $stmt = $this->pdo->query(
            'SELECT p.*, u.nombre, u.apellido, u.email,
                    COALESCE(SUM(pi.cantidad * pi.precio_unitario), 0) AS total
             FROM pedidos p
             JOIN usuarios u ON u.id = p.usuario_id
             LEFT JOIN pedido_items pi ON pi.pedido_id = p.id
             GROUP BY p.id, u.nombre, u.apellido, u.email
             ORDER BY p.fecha DESC'
        );
        return $stmt->fetchAll();
    }

    // Devuelve los pedidos de un usuario específico (para el historial del cliente)
    public function obtenerPorUsuario(int $usuarioId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM pedidos WHERE usuario_id = :uid ORDER BY fecha DESC'
        );
        $stmt->execute([':uid' => $usuarioId]);
        return $stmt->fetchAll();
    }

    // Devuelve los items (libros) de un pedido específico
    public function obtenerItems(int $pedidoId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT pi.cantidad, pi.precio_unitario, l.titulo, l.autor, l.imagen
             FROM pedido_items pi
             JOIN libros l ON l.id = pi.libro_id
             WHERE pi.pedido_id = :pid'
        );
        $stmt->execute([':pid' => $pedidoId]);
        return $stmt->fetchAll();
    }

    // Cambia el estado de un pedido ('en_proceso' | 'entregada')
    public function cambiarEstado(int $pedidoId, string $estado): void
    {
        $stmt = $this->pdo->prepare('UPDATE pedidos SET estado = :estado WHERE id = :id');
        $stmt->execute([':estado' => $estado, ':id' => $pedidoId]);
    }
}
