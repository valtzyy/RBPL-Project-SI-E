<?php
/**
 * @var string $title
 * @var string $message
 */
$user = Auth::user();
$role = Auth::role();
?>

<style>
  /* ── Home Dashboard specific styling ── */
  .page-header {
    margin-bottom: 28px;
  }
  
  .page-title {
    font-size: 26px;
    font-weight: 800;
    letter-spacing: -.025em;
    margin-bottom: 6px;
    color: #111827;
  }
  
  .page-sub {
    font-size: 14px;
    color: #6b7280;
  }

  .portal-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 24px;
  }

  @media (max-width: 992px) {
    .portal-grid {
      grid-template-columns: 1fr;
    }
  }

  .portal-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    padding: 28px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.01);
  }

  .card-section-title {
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 20px;
    color: #1f2937;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  /* Profile Widget */
  .profile-box {
    display: flex;
    align-items: center;
    gap: 16px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 24px;
  }

  .profile-box-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #e8eafb;
    color: #4f5bd5;
    font-size: 20px;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 10px rgba(79, 91, 213, 0.1);
  }

  .profile-box-details h4 {
    font-size: 16px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 4px;
  }

  .role-tag {
    display: inline-block;
    background: #e8eafb;
    color: #4f5bd5;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    padding: 4px 10px;
    border-radius: 99px;
  }

  /* Shortcut links styling */
  .shortcut-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
  }

  .shortcut-card {
    display: flex;
    flex-direction: column;
    padding: 20px;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    background: #ffffff;
    text-decoration: none;
    color: inherit;
    transition: all 0.2s ease;
  }

  .shortcut-card:hover {
    border-color: #4f5bd5;
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(79, 91, 213, 0.05);
  }

  .shortcut-icon {
    font-size: 24px;
    margin-bottom: 12px;
  }

  .shortcut-title {
    font-size: 14.5px;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 4px;
  }

  .shortcut-desc {
    font-size: 12px;
    color: #64748b;
  }

  /* Info list styles */
  .info-list {
    list-style: none;
    display: flex;
    flex-direction: column;
    gap: 14px;
  }

  .info-item {
    display: flex;
    justify-content: space-between;
    font-size: 13.5px;
    padding-bottom: 10px;
    border-bottom: 1px solid #f1f5f9;
  }

  .info-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
  }

  .info-label {
    color: #64748b;
  }

  .info-value {
    font-weight: 600;
    color: #334155;
  }
</style>

<div class="page-header">
  <h1 class="page-title">Selamat Datang, <?= htmlspecialchars($user['name'] ?? 'Pengguna') ?></h1>
  <p class="page-sub">Panel kontrol pusat DealerLink DMS. Akses cepat dan laporan operasional harian Anda.</p>
</div>

<?php if (Auth::check()): ?>
  <div class="portal-grid">
    
    <!-- Portal Left Content -->
    <div class="portal-card">
      <h3 class="card-section-title">
        <span>🚀</span> Pintasan Cepat Menu Operasional
      </h3>
      
      <div class="shortcut-grid">
        
        <?php if ($role === 'Manager'): ?>
          <a href="/dashboard" class="shortcut-card">
            <span class="shortcut-icon">📊</span>
            <span class="shortcut-title">Dashboard Managerial</span>
            <span class="shortcut-desc">Pantau statistik penjualan dan servis bulanan secara langsung.</span>
          </a>
          <a href="/history" class="shortcut-card">
            <span class="shortcut-icon">🗂️</span>
            <span class="shortcut-title">Riwayat Transaksi</span>
            <span class="shortcut-desc">Tinjau seluruh riwayat SPK dan Work Order yang masuk.</span>
          </a>
        <?php elseif ($role === 'Admin'): ?>
          <a href="/dashboard" class="shortcut-card">
            <span class="shortcut-icon">📊</span>
            <span class="shortcut-title">Dashboard Admin</span>
            <span class="shortcut-desc">Pantau KPI dan status sistem.</span>
          </a>
          <a href="/admin/users" class="shortcut-card">
            <span class="shortcut-icon">👥</span>
            <span class="shortcut-title">Manajemen Akun</span>
            <span class="shortcut-desc">Kelola otorisasi akun pengguna sistem.</span>
          </a>
          <a href="/inventory" class="shortcut-card">
            <span class="shortcut-icon">🚗</span>
            <span class="shortcut-title">Stok Kendaraan</span>
            <span class="shortcut-desc">Pantau master data inventaris unit mobil.</span>
          </a>
          <a href="/procurement" class="shortcut-card">
            <span class="shortcut-icon">📥</span>
            <span class="shortcut-title">Pengadaan</span>
            <span class="shortcut-desc">Kelola pengadaan unit mobil masuk.</span>
          </a>
        <?php elseif ($role === 'Sales'): ?>
          <a href="/dashboard" class="shortcut-card">
            <span class="shortcut-icon">📊</span>
            <span class="shortcut-title">Dashboard Dealer</span>
            <span class="shortcut-desc">Pantau visualisasi grafik penjualan bulanan.</span>
          </a>
          <a href="/customers" class="shortcut-card">
            <span class="shortcut-icon">👥</span>
            <span class="shortcut-title">Manajemen Pelanggan</span>
            <span class="shortcut-desc">Kelola master data pembeli kendaraan.</span>
          </a>
          <a href="/history" class="shortcut-card">
            <span class="shortcut-icon">🗂️</span>
            <span class="shortcut-title">Riwayat Transaksi</span>
            <span class="shortcut-desc">Tinjau seluruh riwayat SPK.</span>
          </a>
          <a href="/transactions" class="shortcut-card">
            <span class="shortcut-icon">🚗</span>
            <span class="shortcut-title">Penjualan Mobil</span>
            <span class="shortcut-desc">Kelola proses transaksi penjualan armada mobil.</span>
          </a>
          <a href="/delivery" class="shortcut-card">
            <span class="shortcut-icon">🚚</span>
            <span class="shortcut-title">Jadwal Serah Terima</span>
            <span class="shortcut-desc">Pantau jadwal pengiriman unit ke pembeli.</span>
          </a>
        <?php elseif ($role === 'Finance'): ?>
          <a href="/service-billing" class="shortcut-card">
            <span class="shortcut-icon">💵</span>
            <span class="shortcut-title">Nota Penjualan</span>
            <span class="shortcut-desc">Proses penagihan nota servis pelanggan.</span>
          </a>
          <a href="/credit/status" class="shortcut-card">
            <span class="shortcut-icon">💳</span>
            <span class="shortcut-title">Status Kredit</span>
            <span class="shortcut-desc">Lihat riwayat persetujuan aplikasi kredit.</span>
          </a>
        <?php elseif ($role === 'Service Advisor'): ?>
          <a href="/booking" class="shortcut-card">
            <span class="shortcut-icon">📅</span>
            <span class="shortcut-title">Booking Servis</span>
            <span class="shortcut-desc">Input dan kelola pendaftaran servis berkala pelanggan.</span>
          </a>
        <?php elseif ($role === 'Mekanik'): ?>
          <a href="/mechanic/panel" class="shortcut-card">
            <span class="shortcut-icon">🔧</span>
            <span class="shortcut-title">Panel Mekanik</span>
            <span class="shortcut-desc">Lihat antrean kerja dan kerjakan work order.</span>
          </a>
        <?php endif; ?>

        <a href="/change-password" class="shortcut-card">
          <span class="shortcut-icon">🔒</span>
          <span class="shortcut-title">Ganti Kata Sandi</span>
          <span class="shortcut-desc">Ubah kredensial masuk akun Anda demi keamanan.</span>
        </a>

      </div>
    </div>

    <!-- Portal Right Sidebar Info -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
      <div class="portal-card">
        <h3 class="card-section-title">👤 Identitas Akun</h3>
        
        <div class="profile-box">
          <?php
            $initials = 'AD';
            if (!empty($user['name'])) {
                $words = explode(' ', trim($user['name']));
                $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
            }
          ?>
          <div class="profile-box-avatar"><?= htmlspecialchars($initials) ?></div>
          <div class="profile-box-details">
            <h4><?= htmlspecialchars($user['name']) ?></h4>
            <span class="role-tag"><?= htmlspecialchars($role) ?></span>
          </div>
        </div>

        <ul class="info-list">
          <li class="info-item">
            <span class="info-label">Username:</span>
            <span class="info-value"><?= htmlspecialchars($user['username']) ?></span>
          </li>
          <li class="info-item">
            <span class="info-label">Surel / Email:</span>
            <span class="info-value"><?= htmlspecialchars($user['email']) ?></span>
          </li>
          <li class="info-item">
            <span class="info-label">Status Sesi:</span>
            <span class="info-value" style="color: #16a34a;">AKTIF (Mocked)</span>
          </li>
        </ul>
      </div>
    </div>

  </div>
<?php else: ?>
  <div class="portal-card" style="background: #fffbeb; border-color: #fef3c7; text-align: center; padding: 40px;">
    <span style="font-size: 40px; display: block; margin-bottom: 16px;">🔒</span>
    <h3 style="font-size: 18px; font-weight: 700; color: #92400e; margin-bottom: 8px;">Akses Dibatasi</h3>
    <p style="color: #b45309; margin-bottom: 24px;">Silakan masuk ke akun DealerLink DMS Anda terlebih dahulu untuk mengakses sistem.</p>
    <a href="/login" class="btn btn-dark" style="text-decoration: none; padding: 12px 24px;">Ke Halaman Login</a>
  </div>
<?php endif; ?>
