<?php

class Customer extends Model
{
    protected string $table = 'customers';

    public function findByKtp(string $ktp): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE ktp_number = ? LIMIT 1");
        $stmt->execute([$ktp]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}