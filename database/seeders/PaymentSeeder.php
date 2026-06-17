<?php

/**
 * Seeder: PaymentSeeder
 * Membuat data pembayaran dummy (pending & verified) untuk pengujian alur PBI-5.8.
 *
 * PRASYARAT — jalankan terlebih dahulu:
 *   1. PaymentTypeSeeder
 *   2. SalesTransactionSeeder
 *
 * Skenario yang dihasilkan:
 *   - Transaksi pertama: 1 pembayaran PENDING  → Finance bisa verifikasi
 *   - Transaksi kedua  : 1 pembayaran VERIFIED → Finance bisa cetak kwitansi
 */
class PaymentSeeder {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function run(): void {
        // Ambil transaksi yang ada (urut terlama)
        $stmt = $this->db->query(
            "SELECT id FROM sales_transactions ORDER BY id ASC LIMIT 2"
        );
        $transactions = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (!$transactions) {
            echo "  [SKIP] PaymentSeeder: tidak ada sales_transactions. Jalankan SalesTransactionSeeder dulu.\n";
            return;
        }

        // Ambil user Finance (untuk verified_by) — gunakan user pertama sebagai fallback
        $financeUserId = (int) $this->db->query("SELECT id FROM users LIMIT 1")->fetchColumn();

        $today     = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));

        $payments = [];

        // Transaksi 1: Pembayaran PENDING → dapat diverifikasi lewat UI
        $payments[] = [
            'transaction_id' => $transactions[0],
            'amount'         => 15000000.00,
            'payment_date'   => $today,
            'verified_by'    => null,
            'status'         => 'pending',
        ];

        // Transaksi 2 (jika ada): Pembayaran VERIFIED → dapat langsung cetak kwitansi
        if (isset($transactions[1])) {
            $payments[] = [
                'transaction_id' => $transactions[1],
                'amount'         => 25000000.00,
                'payment_date'   => $yesterday,
                'verified_by'    => $financeUserId,
                'status'         => 'verified',
            ];
        }

        $insert = $this->db->prepare(
            "INSERT INTO payments (transaction_id, amount, payment_date, verified_by, status)
             VALUES (?, ?, ?, ?, ?)"
        );

        foreach ($payments as $pay) {
            $insert->execute([
                $pay['transaction_id'],
                $pay['amount'],
                $pay['payment_date'],
                $pay['verified_by'],
                $pay['status'],
            ]);

            $label = strtoupper($pay['status']);
            echo "  [SEED] Payment [{$label}] → transaksi ID {$pay['transaction_id']}, "
               . "Rp " . number_format($pay['amount'], 0, ',', '.') . "\n";
        }

        echo "\n✅ PaymentSeeder selesai.\n";
        echo "   Skenario uji:\n";
        echo "   • Buka /finance/queue — lihat antrean tagihan\n";
        echo "   • Klik 'Rincian' → /finance/transactions/{ID} — lihat detail & verifikasi pembayaran\n";
        echo "   • Klik 'Kwitansi' pada pembayaran verified → /finance/payments/{ID}/receipt — unduh PDF\n\n";
    }
}
