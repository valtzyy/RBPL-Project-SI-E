<?php
if (!isset($scenario, $steps, $summary)) {
    throw new RuntimeException('View credit_purchase.php harus dipanggil melalui E2ETestingController::index().');
}

$flash = $_SESSION['e2e_credit_purchase_flash'] ?? null;
unset($_SESSION['e2e_credit_purchase_flash']);

if (!function_exists('e2eStatusLabel')) {
    function e2eStatusLabel(string $status): string
    {
        return match ($status) {
            'passed' => 'Passed',
            'failed' => 'Failed',
            'blocked' => 'Blocked',
            default => 'Pending',
        };
    }
}

if (!function_exists('e2eStatusClass')) {
    function e2eStatusClass(string $status): string
    {
        $allowed = ['pending', 'passed', 'failed', 'blocked'];

        return in_array($status, $allowed, true) ? $status : 'pending';
    }
}

if (!function_exists('e2eFormatRupiah')) {
    function e2eFormatRupiah($amount): string
    {
        return 'Rp ' . number_format((float) $amount, 0, ',', '.');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($scenario['title']) ?></title>
    <link rel="stylesheet" href="css/e2e_testing.css">
</head>
<body>
    <main class="e2e-page">
        <section class="e2e-header">
            <div>
                <h1><?= htmlspecialchars($scenario['title']) ?></h1>
                <p><?= htmlspecialchars($scenario['subtitle']) ?></p>
            </div>

            <form method="POST" action="/e2e-testing/reset">
                <button class="ghost-button" type="submit">Reset Checklist</button>
            </form>
        </section>

        <?php if ($flash): ?>
            <div class="flash-message"><?= htmlspecialchars($flash) ?></div>
        <?php endif; ?>

        <section class="summary-grid">
            <article class="summary-card">
                <span>Total Step</span>
                <strong><?= (int) $summary['total'] ?></strong>
            </article>
            <article class="summary-card success">
                <span>Passed</span>
                <strong><?= (int) $summary['passed'] ?></strong>
            </article>
            <article class="summary-card danger">
                <span>Failed</span>
                <strong><?= (int) $summary['failed'] ?></strong>
            </article>
            <article class="summary-card warning">
                <span>Blocked</span>
                <strong><?= (int) $summary['blocked'] ?></strong>
            </article>
        </section>

        <section class="progress-panel">
            <div class="progress-copy">
                <h2>Progress Testing</h2>
                <p><?= (int) $summary['progress'] ?>% step sudah passed.</p>
            </div>
            <div class="progress-track" aria-label="Progress testing">
                <div class="progress-fill" style="width: <?= (int) $summary['progress'] ?>%"></div>
            </div>
        </section>

        <section class="scenario-grid">
            <article class="info-panel">
                <h2>Data Test</h2>
                <dl>
                    <div>
                        <dt>Customer</dt>
                        <dd><?= htmlspecialchars($scenario['test_data']['customer']) ?></dd>
                    </div>
                    <div>
                        <dt>No. HP</dt>
                        <dd><?= htmlspecialchars($scenario['test_data']['phone']) ?></dd>
                    </div>
                    <div>
                        <dt>Kendaraan</dt>
                        <dd><?= htmlspecialchars($scenario['test_data']['vehicle']) ?></dd>
                    </div>
                    <div>
                        <dt>Leasing</dt>
                        <dd><?= htmlspecialchars($scenario['test_data']['leasing']) ?></dd>
                    </div>
                    <div>
                        <dt>Kode Transaksi</dt>
                        <dd><?= htmlspecialchars($scenario['test_data']['transaction_code']) ?></dd>
                    </div>
                    <div>
                        <dt>Kode Kredit</dt>
                        <dd><?= htmlspecialchars($scenario['test_data']['credit_code']) ?></dd>
                    </div>
                    <div>
                        <dt>Nominal DP</dt>
                        <dd><?= e2eFormatRupiah($scenario['test_data']['down_payment']) ?></dd>
                    </div>
                </dl>
            </article>

            <article class="info-panel">
                <h2>Kriteria Lulus</h2>
                <p><strong>Aktor:</strong> <?= htmlspecialchars($scenario['actor']) ?></p>
                <p><strong>Precondition:</strong> <?= htmlspecialchars($scenario['precondition']) ?></p>
                <p><strong>Expected result:</strong> <?= htmlspecialchars($scenario['expected_result']) ?></p>
            </article>
        </section>

        <section class="steps-panel">
            <div class="section-title">
                <h2>Checklist Rute End-to-End</h2>
            </div>

            <div class="steps-list">
                <?php foreach ($steps as $index => $step): ?>
                    <?php $statusClass = e2eStatusClass($step['status'] ?? 'pending'); ?>
                    <article class="step-card <?= $statusClass ?>">
                        <div class="step-number"><?= $index + 1 ?></div>

                        <div class="step-content">
                            <div class="step-heading">
                                <div>
                                    <span class="phase"><?= htmlspecialchars($step['phase']) ?></span>
                                    <h3><?= htmlspecialchars($step['name']) ?></h3>
                                </div>
                                <span class="status-pill <?= $statusClass ?>">
                                    <?= e2eStatusLabel($statusClass) ?>
                                </span>
                            </div>

                            <div class="step-details">
                                <p><strong>Route:</strong> <code><?= htmlspecialchars($step['route']) ?></code></p>
                                <p><strong>Aksi:</strong> <?= htmlspecialchars($step['action']) ?></p>
                                <p><strong>Ekspektasi:</strong> <?= htmlspecialchars($step['expected']) ?></p>
                            </div>

                            <form class="status-form" method="POST" action="/e2e-testing/update">
                                <input type="hidden" name="step_id" value="<?= htmlspecialchars($step['id']) ?>">
                                <button type="submit" name="status" value="passed" class="btn-passed">Passed</button>
                                <button type="submit" name="status" value="failed" class="btn-failed">Failed</button>
                                <button type="submit" name="status" value="blocked" class="btn-blocked">Blocked</button>
                                <button type="submit" name="status" value="pending" class="btn-pending">Pending</button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    </main>
</body>
</html>