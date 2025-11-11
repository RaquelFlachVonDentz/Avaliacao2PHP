<?php

namespace App\Repositories;

use App\Core\Database;
use App\Models\Client;
use PDO;

class ClientRepository
{
    public function countAll(): int
    {
        $stmt = Database::getConnection()->query("SELECT COUNT(*) FROM clients");
        return (int)$stmt->fetchColumn();
    }

    public function paginate(int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        $stmt = Database::getConnection()->prepare("
            SELECT * FROM clients
            ORDER BY id DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = Database::getConnection()->prepare("SELECT * FROM clients WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = Database::getConnection()->prepare("SELECT * FROM clients WHERE email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findAll(): array
    {
        $stmt = Database::getConnection()->prepare("SELECT * FROM clients ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create(Client $client): int
    {
        $stmt = Database::getConnection()->prepare("
            INSERT INTO clients (name, email, phone, city, state)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $client->name,
            $client->email,
            $client->phone,
            $client->city,
            $client->state
        ]);
        return (int)Database::getConnection()->lastInsertId();
    }

    public function update(Client $client): bool
    {
        $stmt = Database::getConnection()->prepare("
            UPDATE clients
            SET name = ?, email = ?, phone = ?, city = ?, state = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $client->name,
            $client->email,
            $client->phone,
            $client->city,
            $client->state,
            $client->id
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = Database::getConnection()->prepare("DELETE FROM clients WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getArray(): array
    {
        $stmt = Database::getConnection()->prepare("SELECT * FROM clients ORDER BY id DESC");
        $stmt->execute();
        $clients = $stmt->fetchAll();
        $return = [];
        foreach ($clients as $client) {
            $return[$client['id']] = $client['name'];
        }
        return $return;
    }
}
