<!-- app/views/layouts/main.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title ?? 'MyApp') ?></title>
</head>
<body>
    <?= $content ?>
</body>
</html>