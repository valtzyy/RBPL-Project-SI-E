<?php

define('ROOT_PATH', __DIR__);

require_once ROOT_PATH . '/core/Database.php';
require_once ROOT_PATH . '/core/Model.php';
require_once ROOT_PATH . '/app/services/VehicleInventoryService.php';
require_once ROOT_PATH . '/app/services/ProcurementReceiptService.php';
require_once ROOT_PATH . '/app/services/SalesTransactionService.php';

$db = Database::getInstance();
$inventoryService = new VehicleInventoryService();
$receiptService = new ProcurementReceiptService();
$salesService = new SalesTransactionService();
$checks = [];

function sitAssert(bool $condition, string $message, array &$checks): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }

    $checks[] = $message;
}

try {
    $detailsTable = $db->query("SHOW TABLES LIKE 'procurement_details'")->fetch();
    if ($detailsTable === false) {
        throw new RuntimeException('SIT tidak bisa dijalankan: tabel procurement_details tidak ditemukan.');
    }

    $db->beginTransaction();

    $suffix = time();

    $stmt = $db->prepare('INSERT INTO roles (name) VALUES (?)');
    $stmt->execute(['Sales']);
    $salesRoleId = (int) $db->lastInsertId();

    $stmt = $db->prepare('
        INSERT INTO users (name, username, email, password, role_id, status)
        VALUES (?, ?, ?, ?, ?, ?)
    ');
    $stmt->execute(['SIT Sales', 'sit_sales_' . $suffix, 'sit_sales_' . $suffix . '@example.test', password_hash('secret', PASSWORD_DEFAULT), $salesRoleId, 'active']);
    $salesUserId = (int) $db->lastInsertId();

    $vehicleId = $inventoryService->create([
        'brand' => 'SIT Brand',
        'type' => 'SIT Type',
        'color' => 'SIT Color',
        'chassis_number' => 'SIT-CH-' . $suffix,
        'engine_number' => 'SIT-EN-' . $suffix,
        'price' => 250000000,
        'status' => 'available',
        'quantity' => 0,
        'min_stock' => 0,
    ]);

    $stmt = $db->prepare('INSERT INTO procurements (request_code, requested_by, status) VALUES (?, ?, ?)');
    $stmt->execute(['SIT-PROC-' . $suffix, $salesUserId, 'sent']);
    $procurementId = (int) $db->lastInsertId();

    $stmt = $db->prepare('INSERT INTO procurement_details (procurement_id, vehicle_id, quantity) VALUES (?, ?, ?)');
    $stmt->execute([$procurementId, $vehicleId, 2]);

    $receiptId = $receiptService->createReceipt([
        'procurement_id' => $procurementId,
        'received_by' => 'SIT QA',
        'inspection_result' => 'OK',
    ]);
    sitAssert($receiptId > 0, 'Procurement receipt berhasil dibuat.', $checks);

    $vehicle = $inventoryService->find($vehicleId);
    sitAssert((int) $vehicle['stock_quantity'] === 2, 'Stock bertambah dari procurement receipt menjadi 2.', $checks);

    $list = $inventoryService->list(['keyword' => 'SIT-CH-' . $suffix], 1, 10);
    sitAssert((int) $list['total'] === 1, 'Inventory list menampilkan kendaraan hasil procurement.', $checks);

    $stmt = $db->prepare('UPDATE vehicles_stock SET min_stock = 1 WHERE vehicle_id = ?');
    $stmt->execute([$vehicleId]);

    $customerColumns = array_column($db->query('SHOW COLUMNS FROM customers')->fetchAll(), 'Field');
    $customerData = [
        'name' => 'SIT Customer',
        'phone' => '0800000000',
        'address' => 'SIT Address',
        'ktp_number' => 'SITKTP' . $suffix,
    ];
    $customerData = array_intersect_key($customerData, array_flip($customerColumns));
    $customerSql = sprintf(
        'INSERT INTO customers (%s) VALUES (%s)',
        implode(', ', array_map(fn($column) => "`{$column}`", array_keys($customerData))),
        implode(', ', array_fill(0, count($customerData), '?'))
    );
    $stmt = $db->prepare($customerSql);
    $stmt->execute(array_values($customerData));
    $customerId = (int) $db->lastInsertId();
    $salesCustomerId = $customerId;

    $buyerCustomerTable = $db->query("SHOW TABLES LIKE 'buyer_customers'")->fetch();
    if ($buyerCustomerTable !== false) {
        $buyerColumns = array_column($db->query('SHOW COLUMNS FROM buyer_customers')->fetchAll(), 'Field');
        $buyerData = [
            'customer_id' => $customerId,
            'address' => 'SIT Address',
            'ktp_number' => 'SITKTP' . $suffix,
            'vehicle_id' => $vehicleId,
        ];
        $buyerData = array_intersect_key($buyerData, array_flip($buyerColumns));
        $buyerSql = sprintf(
            'INSERT INTO buyer_customers (%s) VALUES (%s)',
            implode(', ', array_map(fn($column) => "`{$column}`", array_keys($buyerData))),
            implode(', ', array_fill(0, count($buyerData), '?'))
        );
        $stmt = $db->prepare($buyerSql);
        $stmt->execute(array_values($buyerData));
        $salesCustomerId = (int) $db->lastInsertId();
    }

    $stmt = $db->prepare('INSERT INTO payment_types (name) VALUES (?)');
    $stmt->execute(['SIT Cash ' . $suffix]);
    $paymentTypeId = (int) $db->lastInsertId();

    $stmt = $db->prepare('
        INSERT INTO sales_transactions (transaction_code, customer_id, vehicle_id, sales_user_id, payment_type, status)
        VALUES (?, ?, ?, ?, ?, ?)
    ');
    $stmt->execute(['SIT-SALES-' . $suffix, $salesCustomerId, $vehicleId, $salesUserId, $paymentTypeId, 'process']);
    $transactionId = (int) $db->lastInsertId();

    $salesService->updateStatus($transactionId, 'lunas');

    $vehicle = $inventoryService->find($vehicleId);
    sitAssert((int) $vehicle['stock_quantity'] === 1, 'Stock berkurang setelah sales transaction lunas menjadi 1.', $checks);

    $db->rollBack();

    echo "SIT Sprint 3 berhasil. Semua perubahan data sudah di-rollback." . PHP_EOL;
    foreach ($checks as $check) {
        echo "- {$check}" . PHP_EOL;
    }
} catch (Throwable $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }

    fwrite(STDERR, 'SIT Sprint 3 gagal: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
