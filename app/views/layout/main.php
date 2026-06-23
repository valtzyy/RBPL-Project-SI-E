<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'MyApp') ?></title>
    <link rel="stylesheet" href="/css/down_payment.css">
</head>
<body>
    <div class="admin-shell">
        <?php require ROOT_PATH . '/app/views/layout/SideBar.php'; ?>

        <div class="admin-workspace">
            <?php require ROOT_PATH . '/app/views/layout/TopBar.php'; ?>

            <main class="admin-main">
                <?= $content ?>
            </main>
        </div>
    </div>
</body>
</html>
