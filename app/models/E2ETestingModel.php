<?php

class E2ETestingModel
{
    public function getCreditPurchaseScenario(): array
    {
        return [
            'title' => 'End-to-End Testing Pembelian Kredit',
            'subtitle' => 'Rute pembelian kredit dari pengajuan customer sampai mobil siap diserahkan.',
            'actor' => 'Customer, Sales, Finance, Leasing, Admin Delivery',
            'precondition' => 'Customer sudah memilih kendaraan dan data customer tersedia untuk proses pengajuan kredit.',
            'expected_result' => 'Pengajuan kredit approved, DP lunas, kontrak tersimpan, dan unit masuk status siap diserahkan.',
            'test_data' => [
                'customer' => 'Budi Santoso',
                'phone' => '0812-3456-7890',
                'vehicle' => 'Toyota Avanza 1.5 G MT',
                'leasing' => 'PT Astra Sedaya Finance',
                'transaction_code' => 'TRX-E2E-0001',
                'credit_code' => 'KRD-E2E-0001',
                'down_payment' => 47000000,
            ],
            'steps' => [
                [
                    'id' => 'lead_created',
                    'phase' => 'Awal Pembelian',
                    'name' => 'Customer memilih kendaraan dan dibuatkan transaksi',
                    'route' => '/sales',
                    'action' => 'Buat transaksi penjualan untuk customer yang memilih kendaraan kredit.',
                    'expected' => 'Transaksi penjualan terbentuk dengan kode transaksi dan status awal tercatat.',
                    'status' => 'pending',
                ],
                [
                    'id' => 'credit_application_created',
                    'phase' => 'Pengajuan Kredit',
                    'name' => 'Sales membuat pengajuan kredit',
                    'route' => '/credit-application',
                    'action' => 'Input data leasing, tenor, DP awal, dan kendaraan pada form pengajuan kredit.',
                    'expected' => 'Pengajuan kredit tersimpan dan memiliki nomor pengajuan.',
                    'status' => 'pending',
                ],
                [
                    'id' => 'documents_uploaded',
                    'phase' => 'Kelengkapan Dokumen',
                    'name' => 'Dokumen customer diupload dan diverifikasi',
                    'route' => '/document',
                    'action' => 'Upload dokumen identitas, bukti penghasilan, rekening koran, dan dokumen pendukung.',
                    'expected' => 'Semua dokumen wajib tampil sebagai lengkap pada histori kelengkapan.',
                    'status' => 'pending',
                ],
                [
                    'id' => 'leasing_approval_received',
                    'phase' => 'Approval Leasing',
                    'name' => 'Keputusan approval leasing diterima',
                    'route' => '/test-notification',
                    'action' => 'Simulasikan keputusan approved dari leasing untuk pengajuan kredit.',
                    'expected' => 'Status pengajuan berubah menjadi approved dan notifikasi berhasil tampil.',
                    'status' => 'pending',
                ],
                [
                    'id' => 'down_payment_paid',
                    'phase' => 'Uang Muka',
                    'name' => 'Finance mencatat pembayaran DP',
                    'route' => '/down-payment',
                    'action' => 'Pilih pengajuan approved lalu simpan nominal dan tanggal pembayaran DP.',
                    'expected' => 'DP tersimpan, terlihat pada riwayat customer, dan tidak dapat diproses untuk pengajuan non-approved.',
                    'status' => 'pending',
                ],
                [
                    'id' => 'contract_uploaded',
                    'phase' => 'Kontrak Kredit',
                    'name' => 'Kontrak kredit digital disimpan',
                    'route' => '/down-payment',
                    'action' => 'Upload file kontrak kredit yang sudah ditandatangani customer.',
                    'expected' => 'File kontrak tersimpan dan link kontrak muncul pada riwayat pengajuan.',
                    'status' => 'pending',
                ],
                [
                    'id' => 'credit_history_checked',
                    'phase' => 'Histori Customer',
                    'name' => 'Histori kelengkapan kredit dicek',
                    'route' => '/histori-kelengkapan',
                    'action' => 'Buka detail histori customer dan pastikan status dokumen, DP, dan approval sesuai.',
                    'expected' => 'Halaman histori menampilkan pengajuan approved, dokumen lengkap, dan progress customer benar.',
                    'status' => 'pending',
                ],
                [
                    'id' => 'vehicle_ready_for_delivery',
                    'phase' => 'Serah Terima',
                    'name' => 'Mobil masuk status siap diserahkan',
                    'route' => '/delivery',
                    'action' => 'Pastikan unit yang kreditnya selesai masuk daftar siap serah terima.',
                    'expected' => 'Mobil tampil sebagai siap diserahkan kepada customer.',
                    'status' => 'pending',
                ],
            ],
        ];
    }

    public function mergeStatuses(array $steps, array $statuses): array
    {
        return array_map(function (array $step) use ($statuses): array {
            if (isset($statuses[$step['id']])) {
                $step['status'] = $statuses[$step['id']];
            }

            return $step;
        }, $steps);
    }

    public function calculateSummary(array $steps): array
    {
        $summary = [
            'total' => count($steps),
            'passed' => 0,
            'failed' => 0,
            'blocked' => 0,
            'pending' => 0,
        ];

        foreach ($steps as $step) {
            $status = $step['status'] ?? 'pending';

            if (!array_key_exists($status, $summary)) {
                $status = 'pending';
            }

            $summary[$status]++;
        }

        $summary['progress'] = $summary['total'] > 0
            ? (int) round(($summary['passed'] / $summary['total']) * 100)
            : 0;

        return $summary;
    }

    public function isValidStep(string $stepId): bool
    {
        foreach ($this->getCreditPurchaseScenario()['steps'] as $step) {
            if ($step['id'] === $stepId) {
                return true;
            }
        }

        return false;
    }
}
