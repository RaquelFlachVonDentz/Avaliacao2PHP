<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Order;
use PDO;

class OrderRepository
{
    public function countAll(): int
    {
        $stmt = Database::getConnection()->query("SELECT COUNT(*) FROM orders");
        return (int)$stmt->fetchColumn();
    }

    public function paginate(int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        $stmt = Database::getConnection()->prepare("
            SELECT o.*, c.name as client_name, c.email as client_email
            FROM orders o
            LEFT JOIN clients c ON o.client_id = c.id
            ORDER BY o.id DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = Database::getConnection()->prepare("
            SELECT o.*, c.name as client_name, c.email as client_email, c.phone as client_phone
            FROM orders o
            LEFT JOIN clients c ON o.client_id = c.id
            WHERE o.id = ?
        ");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findAll(): array
    {
        $stmt = Database::getConnection()->prepare("
            SELECT o.*, c.name as client_name
            FROM orders o
            LEFT JOIN clients c ON o.client_id = c.id
            ORDER BY o.id DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create(Order $order): int
    {
        $stmt = Database::getConnection()->prepare("
            INSERT INTO orders (client_id, order_date, status)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([
            $order->client_id,
            $order->order_date,
            $order->status
        ]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public function update(Order $order): bool
    {
        $stmt = Database::getConnection()->prepare("
            UPDATE orders
            SET client_id = ?, order_date = ?, status = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $order->client_id,
            $order->order_date,
            $order->status,
            $order->id
        ]);
    }

    public function delete(int $id): bool
    {
        // Primeiro, deleta os itens do pedido
        $stmtItems = Database::getConnection()->prepare("DELETE FROM order_items WHERE order_id = ?");
        $stmtItems->execute([$id]);
        
        // Depois, deleta o pedido
        $stmt = Database::getConnection()->prepare("DELETE FROM orders WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getArray(): array
    {
        $stmt = Database::getConnection()->prepare("
            SELECT o.id, CONCAT('Pedido #', o.id, ' - ', c.name) as name
            FROM orders o
            LEFT JOIN clients c ON o.client_id = c.id
            ORDER BY o.id DESC
        ");
        $stmt->execute();
        $orders = $stmt->fetchAll();
        $return = [];
        foreach ($orders as $order) {
            $return[$order['id']] = $order['name'];
        }
        return $return;
    }
}



