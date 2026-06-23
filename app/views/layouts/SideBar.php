<?php
// app/views/layouts/SideBar.php

$user = Auth::user();
$activeRole = Auth::role();
$roleKey = $activeRole ? strtolower($activeRole) : 'guest';

// Initials logic
$initials = 'AD';
if ($user && !empty($user['name'])) {
    $words = explode(' ', trim($user['name']));
    if (count($words) >= 2) {
        $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
    } else {
        $initials = strtoupper(substr($words[0], 0, 2));
    }
}

// Menu structure definition per role
$menus = [
    // 1. Admin Sistem: Mengelola pengguna, stok kendaraan, dan pengadaan unit
    'admin sistem' => [
        ['label' => 'Dashboard', 'url' => '/dashboard', 'icon' => '📊'],
        ['label' => 'Manajemen Akun', 'url' => '/admin/users', 'icon' => '👥'],
        ['label' => 'Stok Kendaraan', 'url' => '/inventory', 'icon' => '🚗'],
        ['label' => 'Pengadaan', 'url' => '/procurement', 'icon' => '📥'],
    ],
    // 2. Admin Dealer: Mengelola transaksi penjualan, pelanggan, dan pengiriman unit mobil
    'admin dealer' => [
        ['label' => 'Dashboard', 'url' => '/dashboard', 'icon' => '📊'],
        ['label' => 'Manajemen Pelanggan', 'url' => '/customers', 'icon' => '👥'],
        ['label' => 'Riwayat Transaksi', 'url' => '/history', 'icon' => '🗂️'],
        ['label' => 'Penjualan Mobil', 'url' => '/transactions', 'icon' => '🚗'],
        ['label' => 'Jadwal Serah Terima', 'url' => '/delivery', 'icon' => '🚚'],
    ],
    // 3. Finance: Mengelola invoicing, nota servis, kasir, dan peninjauan kredit leasing
    'finance' => [
        ['label' => 'Dashboard', 'url' => '/dashboard', 'icon' => '📊'],
        ['label' => 'Nota Penjualan', 'url' => '/service-billing', 'icon' => '💵'],
        ['label' => 'Status Kredit', 'url' => '/credit/status', 'icon' => '💳'],
        ['label' => 'Kasir Nota', 'url' => '/kasir/nota', 'icon' => '🧾'],
    ],
    // 4. Service Advisor: Mengelola penjadwalan & antrean servis berkala pelanggan
    'service advisor' => [
        ['label' => 'Dashboard', 'url' => '/dashboard', 'icon' => '📊'],
        ['label' => 'Booking Servis', 'url' => '/booking', 'icon' => '📅'],
        ['label' => 'Antrean Booking', 'url' => '/booking/queue', 'icon' => '⏳'],
    ],
    // 5. Mekanik: Mengerjakan work order servis kendaraan di bengkel
    'mekanik' => [
        ['label' => 'Panel Mekanik', 'url' => '/mechanic/panel', 'icon' => '🔧'],
        ['label' => 'Log Kerja Mekanik', 'url' => '/mechanic/work-order/log', 'icon' => '📋'],
    ],
    // 6. Manager: Memantau visualisasi rapor KPI finansial dealer secara keseluruhan
    'manager' => [
        ['label' => 'Dashboard', 'url' => '/dashboard', 'icon' => '📊'],
        ['label' => 'Riwayat Transaksi', 'url' => '/history', 'icon' => '🗂️'],
        ['label' => 'Reports', 'icon' => '📈', 'submenu' => [
            ['label' => 'Sales Report', 'url' => '/reports#sales-report'],
            ['label' => 'Stock Report', 'url' => '/reports#stock-report'],
            ['label' => 'Credit Report', 'url' => '/reports#credit-report'],
            ['label' => 'Service Report', 'url' => '/reports#service-report'],
            ['label' => 'Sparepart Report', 'url' => '/reports#sparepart-report'],
        ]],
        ['label' => 'Audit Log', 'url' => '/reports/audit-log', 'icon' => '📝'],
    ],
];

// Fallback role menu if not recognized
$activeMenu = $menus[$roleKey] ?? [
    ['label' => 'Dashboard', 'url' => '/dashboard', 'icon' => '📊'],
    ['label' => 'Riwayat Transaksi', 'url' => '/history', 'icon' => '🗂️']
];

// Get current request URI to detect active menu items
$currentUri = $_SERVER['REQUEST_URI'] ?? '/';
?>
<aside class="sidebar">
  <div class="profile">
    <div class="avatar"><?= htmlspecialchars($initials) ?></div>
    <div>
      <div class="profile-name"><?= htmlspecialchars($user['name'] ?? 'Guest') ?></div>
      <div class="profile-role"><?= htmlspecialchars($activeRole ?? 'Pengunjung') ?></div>
    </div>
  </div>
  
  <ul class="nav">
    <?php foreach ($activeMenu as $item): ?>
      <?php if (isset($item['submenu'])): ?>
        <?php 
          // Detect if any submenu link is active to open the details tag by default
          $isOpen = false;
          foreach ($item['submenu'] as $subItem) {
              if (strpos($currentUri, $subItem['url']) !== false) {
                  $isOpen = true;
                  break;
              }
          }
        ?>
        <li style="padding: 0; display: block;">
          <details class="sidebar-details" <?= $isOpen ? 'open' : '' ?>>
            <summary class="sidebar-summary">
              <span class="nav-ic"><?= $item['icon'] ?></span>
              <span><?= htmlspecialchars($item['label']) ?></span>
            </summary>
            <div class="sidebar-submenu">
              <?php foreach ($item['submenu'] as $subItem): ?>
                <?php 
                  $subActive = (strpos($currentUri, $subItem['url']) !== false) ? 'active' : '';
                ?>
                <a class="sidebar-sublink <?= $subActive ?>" href="<?= htmlspecialchars($subItem['url']) ?>">
                  <?= htmlspecialchars($subItem['label']) ?>
                </a>
              <?php endforeach; ?>
            </div>
          </details>
        </li>
      <?php else: ?>
        <?php 
          // Check if link is active
          $isActive = false;
          if ($item['url'] === '/dashboard' || $item['url'] === '/') {
              $isActive = ($currentUri === '/' || strpos($currentUri, '/dashboard') !== false);
          } else {
              $isActive = (strpos($currentUri, $item['url']) !== false);
          }
          $activeClass = $isActive ? 'active' : '';
        ?>
        <a href="<?= htmlspecialchars($item['url']) ?>" style="text-decoration: none; color: inherit; display: block;">
          <li class="<?= $activeClass ?>">
            <span class="nav-ic"><?= $item['icon'] ?></span>
            <span class="lbl-text"><?= htmlspecialchars($item['label']) ?></span>
          </li>
        </a>
      <?php endif; ?>
    <?php endforeach; ?>
  </ul>
</aside>
