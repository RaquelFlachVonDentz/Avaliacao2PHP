<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\OrderItem;
use PDO;

class OrderItemRepository
{
    public function findByOrderId(int $orderId): array
    {
        $stmt = Database::getConnection()->prepare("
            SELECT oi.*, p.name as product_name, p.price as product_price
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
            ORDER BY oi.id
        ");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = Database::getConnection()->prepare("
            SELECT oi.*, p.name as product_name
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            WHERE oi.id = ?
        ");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(OrderItem $item): int
    {
        $stmt = Database::getConnection()->prepare("
            INSERT INTO order_items (order_id, product_id, quantity, unit_price)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $item->order_id,
            $item->product_id,
            $item->quantity,
            $item->unit_price
        ]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public function update(OrderItem $item): bool
    {
        $stmt = Database::getConnection()->prepare("
            UPDATE order_items
            SET product_id = ?, quantity = ?, unit_price = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $item->product_id,
            $item->quantity,
            $item->unit_price,
            $item->id
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM order_items WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function deleteByOrderId(int $orderId): bool
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM order_items WHERE order_id = ?");
        return $stmt->execute([$orderId]);
    }
}



