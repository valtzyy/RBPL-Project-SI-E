<?php

/**
 * Seeder: SalesTransactionSeeder
 * Mengisi data transaksi penjualan tunai dummy untuk kebutuhan pengujian PBI-5.8.
 *
 * PRASYARAT — pastikan tabel & data berikut sudah ada:
 *   - customers  (id: 1, 2)
 *   - vehicles   (id: 1, 2)
 *   - users      (id: 1, 2) — minimal 1 user sebagai sales
 *   - payment_types (name: 'Tunai' / 'Cash') — jalankan PaymentTypeSeeder dulu
 */
class SalesTransactionSeeder {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function run(): void {
        // Cari id payment_type 'Tunai'
        $stmt = $this->db->query("SELECT id FROM payment_types WHERE LOWER(name) LIKE '%tunai%' OR LOWER(name) LIKE '%cash%' LIMIT 1");
        $pt   = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pt) {
            echo "  [SKIP] SalesTransactionSeeder: payment_type 'Tunai' tidak ditemukan. Jalankan PaymentTypeSeeder terlebih dahulu.\n";
            return;
        }
        $paymentTypeId = (int) $pt['id'];

        // Cari customer & vehicle yang tersedia
        $customers = $this->db->query("SELECT id FROM customers LIMIT 2")->fetchAll(PDO::FETCH_COLUMN);
        $vehicles  = $this->db->query("SELECT id FROM vehicles  WHERE status = 'available' LIMIT 2")->fetchAll(PDO::FETCH_COLUMN);
        $salesUser = $this->db->query("SELECT id FROM users     LIMIT 1")->fetchColumn();

        if (!$customers || !$vehicles || !$salesUser) {
            echo "  [SKIP] SalesTransactionSeeder: data customers / vehicles / users tidak cukup.\n";
            return;
        }

        $buyerId1 = $customers[0] ?? null;
        if ($buyerId1) {
            echo "  [SEED] Menggunakan Customer 1 (ID: {$buyerId1}) untuk transaksi pertama\n";
        }

        $buyerId2 = count($customers) > 1 ? $customers[1] : null;
        if ($buyerId2) {
            echo "  [SEED] Menggunakan Customer 2 (ID: {$buyerId2}) untuk transaksi kedua\n";
        }

        $transactions = [
            [
                'transaction_code' => 'TRX-TEST-' . date('Ymd') . '-001',
                'customer_id'      => $buyerId1,
                'vehicle_id'       => $vehicles[0],
                'sales_user_id'    => $salesUser,
                'payment_type'     => $paymentTypeId,
                'status'           => 'process',
            ],
        ];

        if ($buyerId2 && count($vehicles) > 1) {
            $transactions[] = [
                'transaction_code' => 'TRX-TEST-' . date('Ymd') . '-002',
                'customer_id'      => $buyerId2,
                'vehicle_id'       => $vehicles[1],
                'sales_user_id'    => $salesUser,
                'payment_type'     => $paymentTypeId,
                'status'           => 'process',
            ];
        }

        $insert = $this->db->prepare(
            "INSERT IGNORE INTO sales_transactions
                (transaction_code, customer_id, vehicle_id, sales_user_id, payment_type, status)
             VALUES (?, ?, ?, ?, ?, ?)"
        );

        foreach ($transactions as $trx) {
            $insert->execute([
                $trx['transaction_code'],
                $trx['customer_id'],
                $trx['vehicle_id'],
                $trx['sales_user_id'],
                $trx['payment_type'],
                $trx['status'],
            ]);
            echo "  [SEED] Transaksi: {$trx['transaction_code']} (BuyerCustomerID: {$trx['customer_id']})\n";
        }

        echo "\n✅ SalesTransactionSeeder selesai.\n";
    }
}
