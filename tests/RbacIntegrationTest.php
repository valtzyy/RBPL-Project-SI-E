<?php
// tests/RbacIntegrationTest.php

define('ROOT_PATH', dirname(__DIR__));

// Enable mock session start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once ROOT_PATH . '/config/env.php';
require_once ROOT_PATH . '/core/Database.php';
require_once ROOT_PATH . '/core/Model.php';
require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/core/Auth.php';

// Helper to run individual test case in a separate CLI process
if (isset($argv[1]) && str_starts_with($argv[1], '--case=')) {
    ob_start();
    $case = substr($argv[1], 7);
    runTestCase($case);
    ob_end_flush();
    exit(0);
}

// Main Test Runner
echo "\n============================================\n";
echo "    RBAC (ROLE-BASED ACCESS CONTROL) TEST    \n";
echo "============================================\n\n";

$testCases = [
    'guest_redirect_to_login' => 'Guest accessing protected home should redirect to /login (exit process)',
    'mekanik_forbidden_finance' => 'Mekanik accessing Finance controller should be forbidden (403 Akses ditolak)',
    'mekanik_allowed_panel' => 'Mekanik accessing Mekanik panel should be allowed (200 OK)',
    'admin_allowed_inventory' => 'Admin accessing VehicleController should be allowed (200 OK)',
    'sales_allowed_customers' => 'Sales accessing CustomerController should be allowed (200 OK)',
    'webhook_allowed_unauthenticated' => 'Webhook approval endpoint should be public/unauthenticated (200 OK)',
];

$passedCount = 0;
foreach ($testCases as $caseName => $description) {
    echo "Running: {$description}...\n";
    
    // Launch subprocess
    $cmd = 'php "' . __FILE__ . '" --case=' . $caseName;
    $output = [];
    $retval = 0;
    exec($cmd, $output, $retval);
    
    $outputText = implode("\n", $output);
    
    if ($caseName === 'guest_redirect_to_login') {
        // Since the process redirects and exits, it should exit with 0 and not reach [ALLOWED]
        if ($retval === 0 && !str_contains($outputText, '[ALLOWED]')) {
            echo "  -> [PASSED] Guest redirected successfully.\n";
            $passedCount++;
        } else {
            echo "  -> [FAILED] Expected redirect, got output: {$outputText} (code: {$retval})\n";
        }
    } elseif ($caseName === 'mekanik_forbidden_finance') {
        if (str_contains($outputText, 'Akses ditolak.')) {
            echo "  -> [PASSED] Access forbidden successfully.\n";
            $passedCount++;
        } else {
            echo "  -> [FAILED] Expected 'Akses ditolak.', got: {$outputText}\n";
        }
    } elseif ($caseName === 'mekanik_allowed_panel') {
        if (str_contains($outputText, '[ALLOWED]')) {
            echo "  -> [PASSED] Access allowed successfully.\n";
            $passedCount++;
        } else {
            echo "  -> [FAILED] Expected '[ALLOWED]', got: {$outputText}\n";
        }
    } elseif ($caseName === 'admin_allowed_inventory' || $caseName === 'sales_allowed_customers' || $caseName === 'webhook_allowed_unauthenticated') {
        if (str_contains($outputText, '[ALLOWED]')) {
            echo "  -> [PASSED] Access allowed successfully.\n";
            $passedCount++;
        } else {
            echo "  -> [FAILED] Expected '[ALLOWED]', got: {$outputText}\n";
        }
    }
}

echo "\n============================================\n";
if ($passedCount === count($testCases)) {
    echo " STATUS UJI RBAC: [SUKSES / PASSED] 🎉 \n";
    echo "============================================\n";
    exit(0);
} else {
    echo " STATUS UJI RBAC: [GAGAL / FAILED] ❌ \n";
    echo "============================================\n";
    exit(1);
}

// Case Executions
function runTestCase(string $case) {
    if ($case === 'guest_redirect_to_login') {
        $_SESSION = [];
        echo "[START] Guest accessing HomeController...\n";
        
        // Mock redirect check
        require_once ROOT_PATH . '/app/controllers/HomeController.php';
        
        $controller = new HomeController();
        echo "[ALLOWED]\n"; // This shouldn't be reached
    } 
    elseif ($case === 'mekanik_forbidden_finance') {
        // Mock Login as Mekanik
        Auth::login([
            'id' => 5,
            'name' => 'Mekanik User',
            'username' => 'mekanik',
            'email' => 'mekanik@dealer.test',
            'role_id' => 5,
            'role_name' => 'Mekanik',
        ]);
        
        $_SERVER['REQUEST_URI'] = '/finance/payments';
        echo "[START] Mekanik accessing FinanceController...\n";
        
        require_once ROOT_PATH . '/app/controllers/FinanceController.php';
        $controller = new FinanceController();
        echo "[ALLOWED]\n"; // This shouldn't be reached
    } 
    elseif ($case === 'mekanik_allowed_panel') {
        // Mock Login as Mekanik
        Auth::login([
            'id' => 5,
            'name' => 'Mekanik User',
            'username' => 'mekanik',
            'email' => 'mekanik@dealer.test',
            'role_id' => 5,
            'role_name' => 'Mekanik',
        ]);
        
        $_SERVER['REQUEST_URI'] = '/mechanic/panel';
        echo "[START] Mekanik accessing WorkOrderController...\n";
        
        require_once ROOT_PATH . '/app/controllers/WorkOrderController.php';
        $controller = new WorkOrderController();
        echo "[ALLOWED]\n";
    } 
    elseif ($case === 'admin_allowed_inventory') {
        // Mock Login as Admin
        Auth::login([
            'id' => 17,
            'name' => 'Admin User',
            'username' => 'admin',
            'email' => 'admin@dealer.test',
            'role_id' => 1,
            'role_name' => 'Admin',
        ]);
        
        $_SERVER['REQUEST_URI'] = '/inventory';
        echo "[START] Admin accessing VehicleController...\n";
        
        require_once ROOT_PATH . '/app/controllers/VehicleController.php';
        $controller = new VehicleController();
        echo "[ALLOWED]\n";
    }
    elseif ($case === 'sales_allowed_customers') {
        // Mock Login as Sales
        Auth::login([
            'id' => 2,
            'name' => 'Sales User',
            'username' => 'sales',
            'email' => 'sales@dealer.test',
            'role_id' => 2,
            'role_name' => 'Sales',
        ]);
        
        $_SERVER['REQUEST_URI'] = '/customers';
        echo "[START] Sales accessing CustomerController...\n";
        
        require_once ROOT_PATH . '/app/controllers/CustomerController.php';
        $controller = new CustomerController();
        echo "[ALLOWED]\n";
    }
    elseif ($case === 'webhook_allowed_unauthenticated') {
        $_SESSION = [];
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['REQUEST_URI'] = '/webhook-approval';
        echo "[START] WebhookApprovalController...\n";
        
        require_once ROOT_PATH . '/app/controllers/WebhookApprovalController.php';
        $controller = new WebhookApprovalController();
        echo "[ALLOWED]\n";
    }
}
