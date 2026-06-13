<?php

class Service_Summary extends Model {
    // 1. Mengunci manipulasi data khusus untuk tabel utama kamu
    protected string $table = 'service_summary';

    /**
     * 2. Method untuk menghitung total antrean pada tanggal & jam tertentu
     * Digunakan oleh UI Form Pelanggan nanti untuk cek apakah kuota di jam tersebut penuh atau belum
     */
    public function countBookingsByDateTime(string $tanggal, string $jam): int {
        // Menggunakan Prepared Statement PDO demi keamanan dari SQL Injection
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM {$this->table} WHERE tanggal_servis = ? AND jam_servis = ?");
        $stmt->execute([$tanggal, $jam]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return (int) ($result['total'] ?? 0);
    }
}