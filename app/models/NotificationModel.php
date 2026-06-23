<?php

class NotificationModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getLatestDecision()
    {
        $query = "
            SELECT
                cd.decision,
                ca.leasing_name,
                cd.decided_at

            FROM credit_decisions cd

            INNER JOIN credit_applications ca

            ON ca.id = cd.credit_application_id

            ORDER BY cd.decided_at DESC

            LIMIT 1
        ";

        $stmt = $this->db->prepare($query);

        $stmt->execute();

        return $stmt->fetch();
    }
}