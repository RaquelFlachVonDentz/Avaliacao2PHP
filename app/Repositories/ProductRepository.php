<?php
namespace App\Repositories;

use App\Core\Database;
use App\Models\Product;
use PDO;

class ProductRepository {
    public function countAll(): int {
        $stmt = Database::getConnection()->query("SELECT COUNT(*) FROM products");
        return (int)$stmt->fetchColumn();
    }
    public function paginate(int $page, int $perPage): array {
        $offset = ($page - 1) * $perPage;
        $stmt = Database::getConnection()->prepare("SELECT * FROM products ORDER BY id DESC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function find(int $id): ?array {
        $stmt = Database::getConnection()->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
    public function create(Product $p): int {
        $stmt = Database::getConnection()->prepare("INSERT INTO products (category_id, name, price, estoque, image_path) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$p->category_id, $p->name, $p->price, $p->estoque, $p->image_path]);
        return (int)Database::getConnection()->lastInsertId();
    }
    public function update(Product $p): bool {
        $stmt = Database::getConnection()->prepare("UPDATE products SET category_id = ?, name = ?, price = ?, estoque = ?, image_path = ? WHERE id = ?");
        return $stmt->execute([$p->category_id, $p->name, $p->price, $p->estoque, $p->image_path, $p->id]);
    }
    public function delete(int $id): bool {
        $stmt = Database::getConnection()->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }
    public function findAll(): array {
        $stmt = Database::getConnection()->prepare("SELECT * FROM products ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    public function findByCategoryId(int $id): ?array {
        $stmt = Database::getConnection()->prepare("SELECT * FROM products WHERE category_id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: [];
    }
    public function decreaseStock(int $productId, int $quantity): bool {
        $stmt = Database::getConnection()->prepare("UPDATE products SET estoque = estoque - ? WHERE id = ? AND estoque >= ?");
        return $stmt->execute([$quantity, $productId, $quantity]);
    }
}
