<?php
// app/models/WorkOrders.php

class WorkOrders extends Model {
    protected string $table = 'work_orders';

    // Insert ke work_orders dengan booking_id dari parameter $id, status = ready
    public function insert(int $bookingId): bool {
        // Cari ID mekanik pertama dari database secara dinamis agar aman dari foreign key constraint error
        $stmt = $this->db->query("
            SELECT u.id 
            FROM   users u
            JOIN   roles r ON r.id = u.role_id
            WHERE  r.name LIKE '%mechanic%' 
               OR  r.name LIKE '%mekanik%' 
               OR  r.name LIKE '%Mechanic%'
            LIMIT 1
        ");
        $mechanicId = (int) ($stmt->fetchColumn() ?: 5);
         

        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (booking_id, assigned_mechanic, status)
            VALUES (?, ?, 'ready')
        ");
        return $stmt->execute([$bookingId, $mechanicId]);
    }
}
