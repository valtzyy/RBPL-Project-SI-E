<?php

class LeasingService
{
    private string $logFile;

    // Setup path log + auto-buat folder storage/logs kalau belum ada
    public function __construct()
    {
        $this->logFile = ROOT_PATH . '/storage/logs/leasing.log';
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }
    }

    // Simulasi kirim bundle: generate ref + catat ke log
    public function simulateSend(int $applicationId, string $leasingName): string
    {
        $ref = $this->generateRef();
        $this->log("Application #{$applicationId} → {$leasingName} (ref: {$ref})");
        return $ref;
    }

    // Format ref: LSG-<tahun>-<6 digit random>
    private function generateRef(): string
    {
        return 'LSG-' . date('Y') . '-' . str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    // Format entry log: [timestamp] [SIMULASI] <pesan>
    private function log(string $message): void
    {
        $line = '[' . date('Y-m-d H:i:s') . '] [SIMULASI] ' . $message . PHP_EOL;
        @file_put_contents($this->logFile, $line, FILE_APPEND);
    }
}
