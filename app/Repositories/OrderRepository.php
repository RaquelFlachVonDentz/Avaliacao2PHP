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
        $stmtItems = Database::getConnection()->prepare("DELETE FROM order_items WHERE order_id = ?");
        $stmtItems->execute([$id]);
        
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

    public function countByClientId(int $clientId, ?array $excludeStatuses = null): int
    {
        $sql = "SELECT COUNT(*) FROM orders WHERE client_id = ?";
        $params = [$clientId];
        
        if ($excludeStatuses !== null && !empty($excludeStatuses)) {
            $placeholders = implode(',', array_fill(0, count($excludeStatuses), '?'));
            $sql .= " AND status NOT IN ($placeholders)";
            $params = array_merge($params, $excludeStatuses);
        }
        
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function findByClientId(int $clientId, ?array $excludeStatuses = null): array
    {
        $sql = "SELECT * FROM orders WHERE client_id = ?";
        $params = [$clientId];
        
        if ($excludeStatuses !== null && !empty($excludeStatuses)) {
            $placeholders = implode(',', array_fill(0, count($excludeStatuses), '?'));
            $sql .= " AND status NOT IN ($placeholders)";
            $params = array_merge($params, $excludeStatuses);
        }
        
        $sql .= " ORDER BY id DESC";
        
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}



