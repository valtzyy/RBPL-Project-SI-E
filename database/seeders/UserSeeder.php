<?php

class UserSeeder
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function run(): void
    {
        $roles = [
            'Admin',
            'Sales',
            'Finance',
            'Service Advisor',
            'Mekanik',
            'Manager',
        ];

        foreach ($roles as $roleName) {
            $this->insertRoleIfMissing($roleName);
            echo "  [SEED] Role: {$roleName}\n";
        }

        $usersToSeed = [
            [
                'name' => 'Admin User',
                'username' => 'admin',
                'email' => 'admin@dealer.test',
                'password' => 'password123',
                'role_name' => 'Admin',
            ],
            [
                'name' => 'Sales User',
                'username' => 'sales',
                'email' => 'sales@dealer.test',
                'password' => 'password123',
                'role_name' => 'Sales',
            ],
            [
                'name' => 'Finance User',
                'username' => 'finance',
                'email' => 'finance@dealer.test',
                'password' => 'password123',
                'role_name' => 'Finance',
            ],
            [
                'name' => 'Service Advisor User',
                'username' => 'advisor',
                'email' => 'advisor@dealer.test',
                'password' => 'password123',
                'role_name' => 'Service Advisor',
            ],
            [
                'name' => 'Mekanik User',
                'username' => 'mekanik',
                'email' => 'mekanik@dealer.test',
                'password' => 'password123',
                'role_name' => 'Mekanik',
            ],
            [
                'name' => 'Manager User',
                'username' => 'manager',
                'email' => 'manager@dealer.test',
                'password' => 'password123',
                'role_name' => 'Manager',
            ],
        ];

        foreach ($usersToSeed as $userData) {
            $roleId = $this->getRoleId($userData['role_name']);
            $this->insertUserIfMissing([
                'name' => $userData['name'],
                'username' => $userData['username'],
                'email' => $userData['email'],
                'password' => $userData['password'],
                'role_id' => $roleId,
                'status' => 'active',
            ]);
            echo "  [SEED] User: {$userData['email']} ({$userData['role_name']})\n";
        }

        echo "\n[OK] Seeder selesai.\n";
    }

    private function insertRoleIfMissing(string $name): void
    {
        $stmt = $this->db->prepare("
            SELECT id
            FROM roles
            WHERE name = :name
            LIMIT 1
        ");
        $stmt->execute([
            'name' => $name,
        ]);

        if ($stmt->fetch()) {
            return;
        }

        $insert = $this->db->prepare("
            INSERT INTO roles (name)
            VALUES (:name)
        ");
        $insert->execute([
            'name' => $name,
        ]);
    }

    private function getRoleId(string $name): int
    {
        $stmt = $this->db->prepare("
            SELECT id
            FROM roles
            WHERE name = :name
            LIMIT 1
        ");
        $stmt->execute([
            'name' => $name,
        ]);

        $id = $stmt->fetchColumn();

        if (!$id) {
            throw new RuntimeException("Role {$name} tidak ditemukan.");
        }

        return (int) $id;
    }

    private function insertUserIfMissing(array $user): void
    {
        $stmt = $this->db->prepare("
            SELECT id
            FROM users
            WHERE email = :email OR username = :username
            LIMIT 1
        ");
        $stmt->execute([
            'email' => $user['email'],
            'username' => $user['username'],
        ]);

        if ($stmt->fetch()) {
            return;
        }

        $insert = $this->db->prepare("
            INSERT INTO users (name, username, email, password, role_id, status)
            VALUES (:name, :username, :email, :password, :role_id, :status)
        ");
        $insert->execute([
            'name' => $user['name'],
            'username' => $user['username'],
            'email' => $user['email'],
            'password' => password_hash($user['password'], PASSWORD_BCRYPT),
            'role_id' => (int) $user['role_id'],
            'status' => $user['status'],
        ]);
    }
}
