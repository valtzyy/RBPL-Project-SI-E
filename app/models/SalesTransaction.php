<?php
require_once ROOT_PATH . '/core/Model.php';

class SalesTransaction extends Model
{
    protected string $table = 'sales_transactions';

    public function getAllWithDetails(): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                st.*,
                COALESCE(c.name, '-') AS customer_name,
                COALESCE(c.phone, '-') AS customer_phone,
                COALESCE(v.brand, '-') AS brand,
                COALESCE(v.type, '-') AS type,
                COALESCE(v.color, '-') AS color,                   
                COALESCE(v.price, 0) AS price,
                COALESCE(u.name, '-') AS sales_name
            FROM {$this->table} st
            LEFT JOIN buyer_customers bc ON st.customer_id = bc.customer_id
            LEFT JOIN customers c ON bc.customer_id = c.id
            LEFT JOIN vehicles  v ON st.vehicle_id  = v.id
            LEFT JOIN users     u ON st.sales_user_id = u.id
            ORDER BY st.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function store(): void
    {
        $customerId  = (int) $this->input('customer_id');
        $ktpNumber   = $this->input('ktp_number');
        $address     = $this->input('address');
        $vehicleId   = $this->input('vehicle_id');
        $paymentType = (int) $this->input('payment_type');
        $salesUserId = $_SESSION['user_id'] ?? 2;

        try {
            $transaction = new SalesTransaction();
            $transactionId = $transaction->create([
                'transaction_code' => $transaction->generateCode(),
                'customer_id'      => $customerId,
                'vehicle_id'       => $vehicleId,
                'sales_user_id'    => $salesUserId,
                'payment_type'     => $paymentType,
                'status'           => 'process',
                'ktp_number'       => $ktpNumber,
                'address'          => $address,
            ]);

            (new Vehicle())->setHeld((int) $vehicleId);

            if ($paymentType !== 1) {
                require_once ROOT_PATH . '/app/models/Payment.php';
                $vehicle = (new Vehicle())->find((int) $vehicleId);
                $vehiclePrice = $vehicle['price'] ?? 0;

                (new Payment())->create([
                    'transaction_id' => $transactionId,
                    'amount'         => $vehiclePrice,
                    'payment_date'   => date('Y-m-d'),
                    'status'         => 'pending'
                ]);

                $this->redirect('/transactions');
            } else {
                $this->redirect('/credit-applications/create?vehicle_id=' . $vehicleId);
            }

        } catch (Exception $e) {
            echo '<pre>ERROR: ' . $e->getMessage() . '</pre>';
        }
    }

    public function generateCode(): string
    {
        return 'TRX-' . strtoupper(uniqid());
    }

    public function getAllWithPaymentDetails(): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                st.*,
                COALESCE(c.name, '-') AS customer_name,
                COALESCE(c.phone, '-') AS customer_phone,
                COALESCE(v.brand, '-') AS brand,
                COALESCE(v.type, '-') AS type,
                COALESCE(v.color, '-') AS color,
                COALESCE(v.price, 0) AS price,
                COALESCE(v.price, 0) AS total_amount,
                p.id AS payment_id,
                p.amount AS payment_amount,
                p.payment_date,
                p.status AS payment_status
            FROM {$this->table} st
            LEFT JOIN buyer_customers bc ON st.customer_id = bc.id
            LEFT JOIN customers c ON bc.customer_id = c.id
            LEFT JOIN vehicles  v ON st.vehicle_id  = v.id
            LEFT JOIN payments  p ON st.id = p.transaction_id
            ORDER BY st.created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }
}