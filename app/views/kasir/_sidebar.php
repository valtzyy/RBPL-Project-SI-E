<?php
// app/views/kasir/_sidebar.php
// Sidebar khusus role Kasir Bengkel — 4 menu sesuai Sprint 12
// Cara pakai: include di setiap view kasir
// $activePage: 'dashboard' | 'tagihan' | 'nota' | 'riwayat'

$activePage = $activePage ?? '';
?>
<aside class="dl-sidebar">
    <div class="dl-sidebar__logo">Dealer<span>Link</span> DMS</div>

    <nav class="dl-sidebar__nav">

        <div class="dl-sidebar__section-label">KASIR BENGKEL</div>

        <a href="/kasir/dashboard" class="dl-sidebar__item <?= $activePage === 'dashboard' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="7" rx="1" />
                <rect x="14" y="3" width="7" height="7" rx="1" />
                <rect x="3" y="14" width="7" height="7" rx="1" />
                <rect x="14" y="14" width="7" height="7" rx="1" />
            </svg>
            Dashboard
        </a>

        <a href="/service-billing" class="dl-sidebar__item <?= $activePage === 'tagihan' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M4 2v20l3-2 3 2 3-2 3 2 3-2V2" />
                <path d="M8 7h8M8 11h8M8 15h4" />
            </svg>
            Tagihan Servis
            <?php if (!empty($pendingCount) && $activePage !== 'tagihan'): ?>
                <span class="dl-sidebar__badge"><?= (int)$pendingCount ?></span>
            <?php endif; ?>
        </a>

        <a href="/kasir/nota" class="dl-sidebar__item <?= $activePage === 'nota' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="6 9 6 2 18 2 18 9" />
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                <rect x="6" y="14" width="12" height="8" />
            </svg>
            Nota Servis
        </a>

        <a href="/kasir/riwayat" class="dl-sidebar__item <?= $activePage === 'riwayat' ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8" />
                <line x1="21" y1="21" x2="16.65" y2="16.65" />
            </svg>
            Riwayat Servis
        </a>

    </nav>

    <div class="dl-sidebar__user">
        <div class="dl-sidebar__avatar">KS</div>
        <div class="dl-sidebar__user-info">
            <div class="dl-sidebar__user-name"><?= htmlspecialchars($_SESSION['user_name'] ?? 'Kasir') ?></div>
            <div class="dl-sidebar__user-role">Kasir Bengkel</div>
        </div>
    </div>
</aside>