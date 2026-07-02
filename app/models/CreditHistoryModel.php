<?php

class CreditHistoryModel
{
    public function getCustomerProfile(int $customerId): array
    {
        return [
            'id' => $customerId,
            'nama' => 'Budi Santoso',
            'no_hp' => '0812-3456-7890',
            'email' => 'budi.santoso@email.com',
        ];
    }

    public function getCreditApplicationsByCustomer(int $customerId): array
    {
        return [
            [
                'id' => 1,
                'kode_pengajuan' => 'KRD-2026-0001',
                'tanggal_ajuan' => '2026-06-10',
                'kendaraan' => 'Toyota Avanza 1.5 G MT',
                'harga_kendaraan' => 235000000,
                'tenor_bulan' => 35,
                'dp' => 47000000,
                'status' => 'disetujui',
                'leasing' => 'PT Astra Sedaya Finance',
                'sales' => 'Andi Prasetyo',
                'catatan_keputusan' => 'Pengajuan disetujui penuh. Kontrak kredit telah ditandatangani pada 18 Jun 2026.',
                'dokumen' => [
                    ['id' => 1, 'nama' => 'KTP Pemohon', 'status' => 'lengkap', 'catatan' => 'Terverifikasi 11 Jun 2026', 'file_path' => '/uploads/ktp-budi.pdf'],
                    ['id' => 2, 'nama' => 'Kartu Keluarga', 'status' => 'lengkap', 'catatan' => 'Terverifikasi 11 Jun 2026', 'file_path' => '/uploads/kk-budi.pdf'],
                    ['id' => 3, 'nama' => 'Slip Gaji 3 Bulan', 'status' => 'lengkap', 'catatan' => 'Terverifikasi 12 Jun 2026', 'file_path' => '/uploads/slip-gaji-budi.pdf'],
                    ['id' => 4, 'nama' => 'Rekening Koran 3 Bulan', 'status' => 'lengkap', 'catatan' => 'Terverifikasi 12 Jun 2026', 'file_path' => '/uploads/rekening-budi.pdf'],
                    ['id' => 5, 'nama' => 'NPWP', 'status' => 'lengkap', 'catatan' => 'Terverifikasi 12 Jun 2026', 'file_path' => '/uploads/npwp-budi.pdf'],
                    ['id' => 6, 'nama' => 'Bukti Domisili', 'status' => 'lengkap', 'catatan' => 'Terverifikasi 13 Jun 2026', 'file_path' => '/uploads/domisili-budi.pdf'],
                ],
            ],
            [
                'id' => 2,
                'kode_pengajuan' => 'KRD-2026-0002',
                'tanggal_ajuan' => '2026-03-02',
                'kendaraan' => 'Honda Brio RS CVT',
                'harga_kendaraan' => 198000000,
                'tenor_bulan' => 24,
                'dp' => 30000000,
                'status' => 'ditolak',
                'leasing' => 'PT BCA Finance',
                'sales' => 'Andi Prasetyo',
                'catatan_keputusan' => 'Pengajuan ditolak. Rasio pendapatan terhadap pengajuan tidak memenuhi syarat minimum leasing (DTI > 40%).',
                'dokumen' => [
                    ['id' => 1, 'nama' => 'KTP Pemohon', 'status' => 'lengkap', 'catatan' => 'Terverifikasi 3 Mar 2026', 'file_path' => '/uploads/ktp-honda.pdf'],
                    ['id' => 2, 'nama' => 'Kartu Keluarga', 'status' => 'lengkap', 'catatan' => 'Terverifikasi 3 Mar 2026', 'file_path' => '/uploads/kk-honda.pdf'],
                    ['id' => 3, 'nama' => 'Slip Gaji 3 Bulan', 'status' => 'lengkap', 'catatan' => 'Terverifikasi 4 Mar 2026', 'file_path' => '/uploads/slip-honda.pdf'],
                    ['id' => 4, 'nama' => 'Rekening Koran 3 Bulan', 'status' => 'kurang', 'catatan' => 'Hanya 2 bulan terakhir diserahkan', 'file_path' => null],
                    ['id' => 5, 'nama' => 'NPWP', 'status' => 'lengkap', 'catatan' => 'Terverifikasi 4 Mar 2026', 'file_path' => '/uploads/npwp-honda.pdf'],
                ],
            ],
            [
                'id' => 3,
                'kode_pengajuan' => 'KRD-2025-0014',
                'tanggal_ajuan' => '2025-11-18',
                'kendaraan' => 'Mitsubishi Xpander Ultimate',
                'harga_kendaraan' => 287500000,
                'tenor_bulan' => 0,
                'dp' => 0,
                'status' => 'diverifikasi',
                'leasing' => 'PT Mandiri Tunas Finance',
                'sales' => 'Andi Prasetyo',
                'catatan_keputusan' => '',
                'dokumen' => [
                    ['id' => 1, 'nama' => 'KTP Pemohon', 'status' => 'lengkap', 'catatan' => 'Terverifikasi 19 Nov 2025', 'file_path' => '/uploads/ktp-xpander.pdf'],
                    ['id' => 2, 'nama' => 'Kartu Keluarga', 'status' => 'lengkap', 'catatan' => 'Terverifikasi 19 Nov 2025', 'file_path' => '/uploads/kk-xpander.pdf'],
                    ['id' => 3, 'nama' => 'Slip Gaji 3 Bulan', 'status' => 'menunggu', 'catatan' => 'Sedang diverifikasi leasing', 'file_path' => null],
                    ['id' => 4, 'nama' => 'Rekening Koran 3 Bulan', 'status' => 'menunggu', 'catatan' => 'Sedang diverifikasi leasing', 'file_path' => null],
                ],
            ],
        ];
    }

    public function findSelectedApplication(array $applications, ?int $applicationId): ?array
    {
        foreach ($applications as $application) {
            if ($applicationId !== null && $application['id'] === $applicationId) {
                return $application;
            }
        }

        return $applications[0] ?? null;
    }

    public function getApplicationStats(array $applications): array
    {
        $totalMenunggu = count(array_filter(
            $applications,
            fn($application) => in_array($application['status'], ['diverifikasi', 'diajukan'], true)
        ));
        $totalDisetujui = count(array_filter($applications, fn($application) => $application['status'] === 'disetujui'));
        $totalNilaiAktif = array_sum(array_map(
            fn($application) => $application['harga_kendaraan'],
            array_filter($applications, fn($application) => $application['status'] === 'disetujui')
        ));

        return [
            'total_menunggu' => $totalMenunggu,
            'total_disetujui' => $totalDisetujui,
            'total_nilai_aktif' => $totalNilaiAktif,
            'total_pengajuan' => count($applications),
        ];
    }
}
