<?php

require_once ROOT_PATH . '/core/Model.php';

class User extends Model
{
    protected string $table = 'users';

    public function findForLogin(string $identity): array|false
    {
        $stmt = $this->db->prepare("
            SELECT
                users.id,
                users.name,
                users.username,
                users.email,
                users.password,
                users.role_id,
                users.status,
                roles.name AS role_name
            FROM users
            INNER JOIN roles ON roles.id = users.role_id
            WHERE users.email = :identity_email OR users.username = :identity_username
            LIMIT 1
        ");

        $stmt->execute([
            'identity_email' => $identity,
            'identity_username' => $identity,
        ]);

        return $stmt->fetch();
    }

    public function allWithRoles(): array
    {
        $stmt = $this->db->query("
            SELECT
                users.id,
                users.name,
                users.username,
                users.email,
                users.status,
                users.created_at,
                roles.name AS role_name
            FROM users
            INNER JOIN roles ON roles.id = users.role_id
            ORDER BY users.created_at DESC
        ");

        return $stmt->fetchAll();
    }

    public function findWithRole(int $id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT
                users.id,
                users.name,
                users.username,
                users.email,
                users.role_id,
                users.status,
                roles.name AS role_name
            FROM users
            INNER JOIN roles ON roles.id = users.role_id
            WHERE users.id = :id
            LIMIT 1
        ");

        $stmt->execute([
            'id' => $id,
        ]);

        return $stmt->fetch();
    }

    public function createAccount(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO users (name, username, email, password, role_id, status)
            VALUES (:name, :username, :email, :password, :role_id, :status)
        ");

        $stmt->execute([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'role_id' => (int) $data['role_id'],
            'status' => $data['status'] ?? 'active',
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function updateAccount(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users
            SET
                name = :name,
                username = :username,
                email = :email,
                role_id = :role_id,
                status = :status
            WHERE id = :id
        ");

        return $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'role_id' => (int) $data['role_id'],
            'status' => $data['status'],
        ]);
    }

    public function deactivate(int $id): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users
            SET status = 'inactive'
            WHERE id = :id
        ");

        return $stmt->execute([
            'id' => $id,
        ]);
    }

    public function updatePassword(int $id, string $password): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users
            SET password = :password
            WHERE id = :id
        ");

        return $stmt->execute([
            'id' => $id,
            'password' => password_hash($password, PASSWORD_BCRYPT),
        ]);
    }
}
