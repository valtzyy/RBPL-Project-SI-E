<?php


return new class {

    public function up(PDO $db): void
    {
        $db->exec("
           CREATE TABLE IF NOT EXISTS sparepart_usages (
                id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                work_order_id  INT UNSIGNED NOT NULL,    -- FK work_orders
                sparepart_id   INT UNSIGNED NOT NULL,    -- FK spareparts
                quantity       INT NOT NULL DEFAULT 1,
                CONSTRAINT fk_su_wo         FOREIGN KEY (work_order_id) REFERENCES work_orders(id),
                CONSTRAINT fk_su_sparepart  FOREIGN KEY (sparepart_id)  REFERENCES spareparts(id)
            )
        ");
    }

    public function down(PDO $db): void
    {
        $db->exec("DROP TABLE IF EXISTS sparepart_usages");
    }
};
