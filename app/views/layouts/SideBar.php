<?php
// app/views/layouts/SideBar.php

$user = Auth::user();
$activeRole = Auth::role();
$roleKey = $activeRole ? strtolower($activeRole) : 'guest';

$initials = 'GU';
if ($user && !empty($user['name'])) {
  $words = explode(' ', trim($user['name']));
  if (count($words) >= 2) {
    $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
  } else {
    $initials = strtoupper(substr($words[0], 0, 2));
  }
}

$menus = [
    'admin' => [
        ['label' => 'Manajemen Akun', 'url' => '/admin/users', 'icon' => '👥'],
        ['label' => 'Stok Kendaraan', 'url' => '/inventory', 'icon' => '🚗'],
        ['label' => 'Pengadaan', 'url' => '/procurement', 'icon' => '📥'],
        // ['label' => 'Pengadaan', 'url' => '/procurement/receipt', 'icon' => '📥'],
    ],
    'sales' => [
        ['label' => 'Manajemen Pelanggan', 'url' => '/customers', 'icon' => '👥'],
        ['label' => 'Penjualan Mobil', 'url' => '/transactions', 'icon' => '🚗'],
        ['label' => 'Pengajuan Kredit', 'icon' => '💳', 'submenu' => [
            ['label' => 'Buat Pengajuan', 'url' => '/credit/create-form'],
            ['label' => 'Upload Syarat', 'url' => '/credit/upload-search'],
            ['label' => 'Status Pengajuan', 'url' => '/credit/tracking'],
        ]],
        ['label' => 'Jadwal Serah Terima', 'url' => '/delivery', 'icon' => '🚚'],
        ['label' => 'Riwayat Transaksi', 'url' => '/history', 'icon' => '🗂️'],
    ],
    'finance' => [
        ['label' => 'Dashboard', 'url' => '/dashboard', 'icon' => '📊'],
        ['label' => 'Verifikasi Pembayaran', 'url' => '/finance/payments', 'icon' => '✅'],
        ['label' => 'Kredit & Leasing', 'icon' => '💳', 'submenu' => [
            ['label' => 'Verifikasi DP', 'url' => '/verifikasi-dp'],
            ['label' => 'Approval Kredit', 'url' => '/form-approval'],
            ['label' => 'Status Kredit', 'url' => '/credit/status'],
        ]],
        ['label' => 'Nota Penjualan', 'url' => '/service-billing', 'icon' => '💵'],
        ['label' => 'Kasir Nota', 'url' => '/kasir/nota', 'icon' => '🧾'],
    ],
  'service advisor' => [
    ['label' => 'Booking Servis', 'url' => '/booking', 'icon' => '📅'],
    ['label' => 'Work Order', 'url' => '/work-orders', 'icon' => '📝'],
    ['label' => 'Sparepart', 'url' => '/sparepart', 'icon' => '🔩'],
  ],
  'mekanik' => [
    ['label' => 'Work Order Saya', 'url' => '/work-orders', 'icon' => '🔧'],
    ['label' => 'Log Kerja Mekanik', 'url' => '/work-order/log', 'icon' => '📋'],
    ['label' => 'Sparepart', 'url' => '/sparepart', 'icon' => '🔩'],
  ],
  'manager' => [
    ['label' => 'Dashboard', 'url' => '/dashboard', 'icon' => '📊'],
    ['label' => 'Riwayat Transaksi', 'url' => '/history', 'icon' => '🗂️'],
    ['label' => 'Purchase Order', 'url' => '/purchase-orders', 'icon' => '🛒'],
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

$activeMenu = $menus[$roleKey] ?? [
  ['label' => 'Dashboard', 'url' => '/dashboard', 'icon' => '📊'],
  ['label' => 'Riwayat Transaksi', 'url' => '/history', 'icon' => '🗂️'],
];

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
              <span class="nav-ic"><?= htmlspecialchars($item['icon']) ?></span>
              <span><?= htmlspecialchars($item['label']) ?></span>
            </summary>
            <div class="sidebar-submenu">
              <?php foreach ($item['submenu'] as $subItem): ?>
                <?php $subActive = (strpos($currentUri, $subItem['url']) !== false) ? 'active' : ''; ?>
                <a class="sidebar-sublink <?= $subActive ?>" href="<?= htmlspecialchars($subItem['url']) ?>">
                  <?= htmlspecialchars($subItem['label']) ?>
                </a>
              <?php endforeach; ?>
            </div>
          </details>
        </li>
      <?php else: ?>
        <?php
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
            <span class="nav-ic"><?= htmlspecialchars($item['icon']) ?></span>
            <span class="lbl-text"><?= htmlspecialchars($item['label']) ?></span>
          </li>
        </a>
      <?php endif; ?>
    <?php endforeach; ?>
  </ul>
</aside>