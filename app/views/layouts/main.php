<!-- app/views/layouts/main.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'DealerLink DMS') ?></title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS (Diperlukan untuk test_sparepart & pendukung layout) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom Style DealerLink -->
    <link rel="stylesheet" href="/CSS/style.css">
</head>
<body>
    <div class="main-content">
        <?= $content ?>
    </div>
    <!-- Main Script DealerLink -->
    <script src="/js/sparepart.js"></script>
</body>
</html>