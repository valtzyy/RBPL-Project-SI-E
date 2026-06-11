<?php


return new class {

    public function up(PDO $db): void
    {
        $db->exec("
           CREATE TABLE IF NOT EXISTS sparepart_requests (
                id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                requested_by   INT UNSIGNED NOT NULL,    -- FK users
                work_order_id  INT UNSIGNED NOT NULL,    -- FK work_orders
                status         ENUM('pending','approved') NOT NULL DEFAULT 'pending',
                CONSTRAINT fk_sr_user FOREIGN KEY (requested_by)  REFERENCES users(id),
                CONSTRAINT fk_sr_wo   FOREIGN KEY (work_order_id) REFERENCES work_orders(id)
            )
        ");
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS sparepart_requests");
    }
};
