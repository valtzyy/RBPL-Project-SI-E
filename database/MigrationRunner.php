<?php

class MigrationRunner {
    private PDO $db;
    private string $migrationsPath;

    public function __construct(PDO $db, string $migrationsPath) {
        $this->db = $db;
        $this->migrationsPath = $migrationsPath;

        // Pastikan tabel migrations ada di cloud DB
        $this->createMigrationsTable();
    }

    /** Buat tabel migrations jika belum ada */
    private function createMigrationsTable(): void {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS migrations (
                id         INT AUTO_INCREMENT PRIMARY KEY,
                filename   VARCHAR(255) NOT NULL UNIQUE,
                ran_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    /** Ambil daftar migrasi yang sudah jalan */
    private function getRanMigrations(): array {
        $stmt = $this->db->query("SELECT filename FROM migrations");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /** Jalankan semua migrasi yang belum dijalankan */
    public function run(): void {
        $ranMigrations = $this->getRanMigrations();

        // Ambil semua file .php di folder migrations, urutkan by nama
        $files = glob($this->migrationsPath . '/*.php');
        sort($files);

        $count = 0;

        foreach ($files as $file) {
            $filename = basename($file);

            // Lewati jika sudah pernah dijalankan
            if (in_array($filename, $ranMigrations)) {
                echo "  [SKIP] {$filename}\n";
                continue;
            }

            // Jalankan migrasi dalam transaction
            try {
                $this->db->beginTransaction();

                $migration = require $file;
                $migration->up($this->db);

                // Catat ke tabel migrations
                $stmt = $this->db->prepare(
                    "INSERT INTO migrations (filename) VALUES (?)"
                );
                $stmt->execute([$filename]);

                $this->db->commit();
                echo "  [OK]   {$filename}\n";
                $count++;

            } catch (Exception $e) {
                $this->db->rollBack();
                echo "  [FAIL] {$filename}: " . $e->getMessage() . "\n";
                break; // Hentikan jika ada yang gagal
            }
        }

        echo $count > 0
            ? "\n✅ {$count} migrasi berhasil dijalankan.\n"
            : "\n✅ Semua migrasi sudah up-to-date.\n";
    }

    /** Rollback migrasi terakhir */
    public function rollback(): void {
        $stmt = $this->db->query(
            "SELECT filename FROM migrations ORDER BY id DESC LIMIT 1"
        );
        $last = $stmt->fetchColumn();

        if (!$last) {
            echo "Tidak ada migrasi untuk di-rollback.\n";
            return;
        }

        $file = $this->migrationsPath . '/' . $last;
        $migration = require $file;
        $migration->down($this->db);

        $this->db->prepare("DELETE FROM migrations WHERE filename = ?")
                 ->execute([$last]);

        echo "  [ROLLBACK] {$last}\n";
    }
}