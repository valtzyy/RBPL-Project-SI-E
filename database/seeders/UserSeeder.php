<?php

class UserSeeder {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function run(): void {
        $users = [
            ['Budi Santoso',  'budi@example.com',  password_hash('password123', PASSWORD_BCRYPT)],
            ['Siti Rahayu',   'siti@example.com',  password_hash('password123', PASSWORD_BCRYPT)],
            ['Agus Prasetyo', 'agus@example.com',  password_hash('password123', PASSWORD_BCRYPT)],
        ];

        $stmt = $this->db->prepare(
            "INSERT IGNORE INTO users (name, email, password) VALUES (?, ?, ?)"
        );

        foreach ($users as $user) {
            $stmt->execute($user);
            echo "  [SEED] User: {$user[1]}\n";
        }

        echo "\n✅ Seeder selesai.\n";
    }
}