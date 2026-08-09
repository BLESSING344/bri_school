<?php

namespace App\Core;

use PDO;
use PDOStatement;

abstract class Model
{
    protected string $table;
    protected string $primaryKey = 'id';
    protected PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connection();
    }

    public function all(string $orderBy = ''): array
    {
        $sql = "SELECT * FROM {$this->table}" . ($orderBy !== '' ? " ORDER BY {$orderBy}" : '');
        return $this->pdo->query($sql)->fetchAll();
    }

    public function find($id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function insert(array $data)
    {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ') VALUES ('
            . implode(', ', $placeholders) . ") RETURNING {$this->primaryKey}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(array_values($data));
        return $stmt->fetchColumn();
    }

    public function update($id, array $data): bool
    {
        $set = implode(', ', array_map(fn($col) => "{$col} = ?", array_keys($data)));
        $sql = "UPDATE {$this->table} SET {$set} WHERE {$this->primaryKey} = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([...array_values($data), $id]);
    }

    public function delete($id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        return $stmt->execute([$id]);
    }

    public function count(): int
    {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();
    }

    protected function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}
