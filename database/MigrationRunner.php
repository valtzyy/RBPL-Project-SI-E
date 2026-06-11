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
                $this->db->beginTransaction();

                $migration = require $file;
                $this->validateMigration($migration, $filename, 'up');
                $migration->up($this->db);

                $stmt = $this->db->prepare(
                    "INSERT INTO migrations (filename) VALUES (?)"
                );
                $stmt->execute([$filename]);

                $this->db->commit();
                echo "  [OK] {$filename}\n";
                $count++;
            } catch (Throwable $e) {
                if ($this->db->inTransaction()) {
                    $this->db->rollBack();
                }

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

    public function rollback(): void
    {
        $stmt = $this->db->query(
            "SELECT filename FROM migrations ORDER BY id DESC LIMIT 1"
        );
        $last = $stmt->fetchColumn();

        if (!$last) {
            echo "Tidak ada migrasi untuk di-rollback.\n";
            return;
        }

        $file = $this->migrationsPath . '/' . $last;

        if (!file_exists($file)) {
            echo "File migrasi tidak ditemukan: {$last}\n";
            return;
        }

        try {
            $this->db->beginTransaction();

            $migration = require $file;
            $this->validateMigration($migration, $last, 'down');
            $migration->down($this->db);

            $this->db->prepare("DELETE FROM migrations WHERE filename = ?")
                     ->execute([$last]);

            $this->db->commit();
            echo "  [ROLLBACK] {$last}\n";
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            echo "Rollback gagal: " . $e->getMessage() . "\n";
        }
    }

    private function validateMigration(mixed $migration, string $filename, string $method): void
    {
        if (!is_object($migration) || !method_exists($migration, $method)) {
            throw new RuntimeException("Migration {$filename} wajib memiliki method {$method}().");
        }
    }
}
