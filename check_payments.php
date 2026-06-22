<?php
define('ROOT_PATH', __DIR__);
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Database.php';

$db = Database::getInstance();

echo "=== DATA PAYMENTS ===\n";
$stmt = $db->query("SELECT p.id AS payment_id, p.transaction_id, p.amount, p.status, p.payment_date FROM payments p LIMIT 10");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (empty($rows)) {
    echo "KOSONG — belum ada data di tabel payments!\n";
} else {
    print_r($rows);
}

echo "\n=== DATA SALES TRANSACTIONS ===\n";
$stmt2 = $db->query("SELECT id, transaction_code, status, payment_type FROM sales_transactions LIMIT 10");
$rows2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
if (empty($rows2)) {
    echo "KOSONG — belum ada data di tabel sales_transactions!\n";
} else {
    print_r($rows2);
}
