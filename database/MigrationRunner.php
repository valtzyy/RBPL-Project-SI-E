<?php

class MigrationRunner
{
    private PDO $db;
    private string $migrationsPath;

    public function __construct(PDO $db, string $migrationsPath)
    {
        $this->db = $db;
        $this->migrationsPath = $migrationsPath;

        $this->createMigrationsTable();
    }

    private function createMigrationsTable(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                filename VARCHAR(255) NOT NULL UNIQUE,
                ran_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    private function getRanMigrations(): array
    {
        $stmt = $this->db->query("SELECT filename FROM migrations");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function run(): void
    {
        $ranMigrations = $this->getRanMigrations();
        $files = glob($this->migrationsPath . '/*.php') ?: [];
        sort($files);

        $count = 0;
        $failed = false;

        foreach ($files as $file) {
            $filename = basename($file);

            if (in_array($filename, $ranMigrations, true)) {
                echo "  [SKIP] {$filename}\n";
                continue;
            }

            try {

                $migration = require $file;
                $this->validateMigration($migration, $filename, 'up');
                $migration->up($this->db);

                $stmt = $this->db->prepare(
                    "INSERT INTO migrations (filename) VALUES (?)"
                );
                $stmt->execute([$filename]);

                echo "  [OK] {$filename}\n";
                $count++;
            } catch (Throwable $e) {

                echo "\n====================\n";
                echo "FILE : {$filename}\n";
                echo "ERROR: " . $e->getMessage() . "\n";
                echo "====================\n";

                $failed = true;
                break;
            }
        }

        if ($failed) {
            echo "\n[FAILED] Migrasi berhenti karena ada error.\n";
            return;
        }

        echo $count > 0
            ? "\n[OK] {$count} migrasi berhasil dijalankan.\n"
            : "\n[OK] Semua migrasi sudah up-to-date.\n";
    }

    public function rollback(int $steps = 1): void
    {
        $stmt = $this->db->query(
            "SELECT filename FROM migrations ORDER BY id DESC LIMIT {$steps}"
        );
        $files = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (!$files) {
            echo "Tidak ada migrasi untuk di-rollback.\n";
            return;
        }

        foreach ($files as $last) {
            $file = $this->migrationsPath . '/' . $last;

            if (!file_exists($file)) {
                echo "File migrasi tidak ditemukan: {$last}\n";
                continue;
            }

            try {
                $migration = (function (string $path): mixed {
                    return require $path;
                })($file);

                $this->validateMigration($migration, $last, 'down');
                $migration->down($this->db);

                $this->db->prepare("DELETE FROM migrations WHERE filename = ?")
                    ->execute([$last]);

                echo "  [ROLLBACK] {$last}\n";
            } catch (Throwable $e) {
                echo "Rollback gagal: " . $e->getMessage() . "\n";
                break;
            }
        }
    }
    private function validateMigration(mixed $migration, string $filename, string $method): void
    {
        if (!is_object($migration) || !method_exists($migration, $method)) {
            throw new RuntimeException("Migration {$filename} wajib memiliki method {$method}().");
        }
    }
}
