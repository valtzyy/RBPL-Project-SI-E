<?php

class InitialInspection extends Model {
    // 1. Menentukan tabel yang akan dimanipulasi oleh model ini
    protected string $table = 'initial_inspections';

    /**
     * 2. Method untuk menyimpan data log pemeriksaan awal (Inspeksi SA)
     * Menerima parameter array data yang dikirimkan dari UI form Advisor
     */
    public function storeInspection(array $data): bool {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} 
            (service_summary_id, service_advisor_id, kondisi_fisik_awal, catatan_sa, estimasi_waktu_menit) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['service_summary_id'],
            $data['service_advisor_id'],
            $data['kondisi_fisik_awal'],
            $data['catatan_sa'],
            $data['estimasi_waktu_menit']
        ]);
    }
}