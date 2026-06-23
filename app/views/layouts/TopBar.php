<?php
// app/views/layouts/TopBar.php
?>
<header class="topbar">
  <div class="topbar-left">
    <div class="hamburger">
      <span></span><span></span><span></span>
    </div>
    <div class="brand">DealerLink <span>DMS</span></div>
  </div>
  <div class="topbar-right">
    <?php if (Auth::check()): ?>
      <span class="user-greeting">Halo, <strong><?= htmlspecialchars(Auth::user()['name']) ?></strong></span>
      <form method="POST" action="/logout" style="display: inline;">
        <button type="submit" class="logout-btn">Keluar</button>
      </form>
    <?php else: ?>
      <a href="/login" class="login-btn">Masuk</a>
    <?php endif; ?>
    <div class="bell">🔔</div>
  </div>
</header>
