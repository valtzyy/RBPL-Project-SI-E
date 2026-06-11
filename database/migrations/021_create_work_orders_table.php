<?php


return new class {

    public function up(PDO $db): void
    {
        $db->exec("
           CREATE TABLE IF NOT EXISTS work_orders (
                id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                assigned_mechanic INT UNSIGNED NOT NULL,   -- FK users
                booking_id        INT UNSIGNED NOT NULL,   -- FK service_bookings
                status            ENUM('in_progress','done','ready') NOT NULL DEFAULT 'in_progress',
                description       TEXT,
                created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT fk_wo_mechanic FOREIGN KEY (assigned_mechanic) REFERENCES users(id),
                CONSTRAINT fk_wo_booking  FOREIGN KEY (booking_id)        REFERENCES service_bookings(id)
        )
        ");
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS work_orders");
    }
};
