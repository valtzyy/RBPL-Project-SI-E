<?php
// app/views/layout/main.php

$content = $content ?? '';

// Jika view yang dirender sudah merupakan halaman utuh HTML (memiliki tag <html> atau doctype),
// maka langsung keluarkan isinya tanpa dibungkus layout global.
if (stripos($content, '<!doctype') !== false || stripos($content, '<html') !== false || isset($hide_layout)) {
    echo $content;
    return;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? 'DealerLink DMS') ?></title>
  <!-- Load layout styles from central CSS folder -->
  <link rel="stylesheet" href="/CSS/layout.css">
</head>
<body>

<div class="shell">

  <!-- Topbar Header -->
  <?php require ROOT_PATH . '/app/views/layouts/TopBar.php'; ?>

  <div class="body-row">
    <!-- Sidebar Navigation -->
    <?php require ROOT_PATH . '/app/views/layouts/SideBar.php'; ?>

    <!-- Main Dynamic Content -->
    <main class="main">
      <?= $content ?>
    </main>
  </div>

</div>

</body>
</html>

