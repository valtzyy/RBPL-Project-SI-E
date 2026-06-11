<?php

class Model
{
    protected PDO $db;
    protected string $table = '';

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->validateIdentifier($this->table);
    }

    public function all(): array
    {
        $table = $this->quoteIdentifier($this->table);
        $stmt = $this->db->query("SELECT * FROM {$table}");
        return $stmt->fetchAll();
    }

    public function find(int $id): array|false
    {
        $table = $this->quoteIdentifier($this->table);
        $stmt = $this->db->prepare("SELECT * FROM {$table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(array $data): int
    {
        if ($data === []) {
            throw new InvalidArgumentException('Data insert tidak boleh kosong.');
        }

        $table = $this->quoteIdentifier($this->table);
        $columns = implode(', ', array_map([$this, 'quoteIdentifier'], array_keys($data)));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $stmt = $this->db->prepare(
            "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})"
        );
        $stmt->execute(array_values($data));

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        if ($data === []) {
            throw new InvalidArgumentException('Data update tidak boleh kosong.');
        }

        $table = $this->quoteIdentifier($this->table);
        $set = implode(', ', array_map(
            fn($key) => $this->quoteIdentifier($key) . ' = ?',
            array_keys($data)
        ));

        $stmt = $this->db->prepare(
            "UPDATE {$table} SET {$set} WHERE id = ?"
        );

        return $stmt->execute([...array_values($data), $id]);
    }

    public function delete(int $id): bool
    {
        $table = $this->quoteIdentifier($this->table);
        $stmt = $this->db->prepare("DELETE FROM {$table} WHERE id = ?");
        return $stmt->execute([$id]);
    }

    protected function validateIdentifier(string $identifier): void
    {
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $identifier)) {
            throw new InvalidArgumentException("Nama identifier database tidak valid: {$identifier}");
        }
    }

    protected function quoteIdentifier(string $identifier): string
    {
        $this->validateIdentifier($identifier);
        return "`{$identifier}`";
    }
}
