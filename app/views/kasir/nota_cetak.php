<?php
// app/views/kasir/nota_cetak.php
// PBI-12.4 — Nota servis resmi yang dapat dicetak / disimpan sebagai PDF
// Dibuka via window.open di tab baru, kasir tekan Ctrl+P atau tombol Cetak

if (!isset($nota) || !$nota) {
    http_response_code(404);
    echo '<p style="font-family:sans-serif;padding:40px;">Nota tidak ditemukan.</p>';
    exit;
}

function rupiahC(float $n): string {
    return 'Rp ' . number_format($n, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota <?= htmlspecialchars($nota['nomor_nota']) ?></title>
    <style>
        /* ============================================================
           nota_cetak.php — print stylesheet
           Halaman ini TIDAK pakai service-billing.css supaya nota
           tetap rapi saat dicetak / disimpan PDF oleh browser.
           ============================================================ */

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 13px;
            color: #1e2a3b;
            background: #f0f2f7;
        }

        /* Tombol cetak — disembunyikan saat print */
        .no-print {
            background: #fff;
            padding: 14px 40px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-print {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 20px;
            background: #2dd4a7;
            color: #1a1f2e;
            font-weight: 700;
            font-size: 13.5px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        .btn-print:hover { filter: brightness(1.08); }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 9px 16px;
            background: transparent;
            color: #6b7a90;
            font-size: 13px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
        }

        /* Wrapper nota */
        .nota-wrapper {
            max-width: 720px;
            margin: 32px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(30,42,59,.1);
            overflow: hidden;
        }

        /* Header nota */
        .nota-header {
            background: #1a1f2e;
            color: #fff;
            padding: 28px 36px 24px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .nota-brand {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -.3px;
        }

        .nota-brand span { color: #2dd4a7; }

        .nota-brand-sub {
            font-size: 11px;
            color: #9aaaba;
            margin-top: 3px;
            letter-spacing: .5px;
            text-transform: uppercase;
        }

        .nota-meta {
            text-align: right;
        }

        .nota-meta__nomor {
            font-size: 15px;
            font-weight: 700;
            color: #2dd4a7;
            font-family: monospace;
        }

        .nota-meta__label {
            font-size: 11px;
            color: #9aaaba;
            margin-bottom: 4px;
        }

        .nota-meta__tanggal {
            font-size: 12px;
            color: #c8cfe0;
            margin-top: 4px;
        }

        /* Body nota */
        .nota-body {
            padding: 28px 36px;
        }

        /* Grid info 2 kolom */
        .nota-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px 32px;
            margin-bottom: 28px;
            padding-bottom: 24px;
            border-bottom: 1px solid #e2e8f0;
        }

        .nota-info-block__label {
            font-size: 10.5px;
            font-weight: 700;
            color: #9aaaba;
            text-transform: uppercase;
            letter-spacing: .6px;
            margin-bottom: 4px;
        }

        .nota-info-block__value {
            font-size: 13.5px;
            font-weight: 500;
            color: #1e2a3b;
        }

        /* Tabel sparepart */
        .nota-section-title {
            font-size: 11px;
            font-weight: 700;
            color: #9aaaba;
            text-transform: uppercase;
            letter-spacing: .6px;
            margin-bottom: 10px;
        }

        .nota-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        .nota-table th {
            font-size: 11px;
            font-weight: 700;
            color: #6b7a90;
            text-transform: uppercase;
            letter-spacing: .4px;
            padding: 9px 10px;
            border-bottom: 2px solid #e2e8f0;
            text-align: left;
        }

        .nota-table th.right { text-align: right; }
        .nota-table th.center { text-align: center; }

        .nota-table td {
            font-size: 13px;
            padding: 10px 10px;
            border-bottom: 1px solid #f1f4f8;
            color: #1e2a3b;
        }

        .nota-table td.right { text-align: right; }
        .nota-table td.center { text-align: center; }

        /* Ringkasan total */
        .nota-total-box {
            background: #1a1f2e;
            border-radius: 10px;
            padding: 16px 20px;
            margin-bottom: 28px;
        }

        .nota-total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 4px 0;
        }

        .nota-total-row__label { font-size: 13px; color: #c8cfe0; }
        .nota-total-row__value { font-size: 13px; font-weight: 600; color: #fff; }

        .nota-total-divider {
            border: none;
            border-top: 1px solid rgba(255,255,255,.12);
            margin: 10px 0;
        }

        .nota-total-grand-label { font-size: 14px; font-weight: 700; color: #2dd4a7; }
        .nota-total-grand-value { font-size: 20px; font-weight: 800; color: #2dd4a7; }

        /* Catatan & tanda tangan */
        .nota-footer-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .nota-note {
            font-size: 12px;
            color: #6b7a90;
            line-height: 1.6;
        }

        .nota-ttd {
            text-align: center;
        }

        .nota-ttd__label {
            font-size: 12px;
            color: #6b7a90;
            margin-bottom: 52px;
        }

        .nota-ttd__line {
            border-top: 1px solid #1e2a3b;
            width: 160px;
            margin: 0 auto 6px;
        }

        .nota-ttd__name {
            font-size: 12.5px;
            font-weight: 600;
            color: #1e2a3b;
        }

        /* ===== PRINT ===== */
        @media print {
            .no-print { display: none !important; }
            body { background: #fff; }
            .nota-wrapper {
                margin: 0;
                border-radius: 0;
                box-shadow: none;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

<!-- Tombol cetak (disembunyikan saat print) -->
<div class="no-print">
    <button class="btn-print" onclick="window.print()">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2.5">
            <polyline points="6 9 6 2 18 2 18 9"/>
            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
            <rect x="6" y="14" width="12" height="8"/>
        </svg>
        Cetak / Simpan PDF
    </button>
    <a href="/kasir/nota" class="btn-back">← Kembali</a>
    <span style="font-size:12px;color:#9aaaba;margin-left:8px;">
        Untuk simpan sebagai PDF: pilih "Save as PDF" di dialog cetak browser.
    </span>
</div>

<!-- Nota -->
<div class="nota-wrapper">

    <!-- Header -->
    <div class="nota-header">
        <div>
            <div class="nota-brand">Dealer<span>Link</span> DMS</div>
            <div class="nota-brand-sub">Nota Servis Resmi</div>
        </div>
        <div class="nota-meta">
            <div class="nota-meta__label">Nomor Nota</div>
            <div class="nota-meta__nomor"><?= htmlspecialchars($nota['nomor_nota']) ?></div>
            <div class="nota-meta__tanggal">
                Dicetak: <?= date('d M Y, H:i') ?>
            </div>
        </div>
    </div>

    <div class="nota-body">

        <!-- Info Pelanggan & Kendaraan -->
        <div class="nota-info-grid">
            <div>
                <div class="nota-info-block__label">Pelanggan</div>
                <div class="nota-info-block__value"><?= htmlspecialchars($nota['customer_name']) ?></div>
            </div>
            <div>
                <div class="nota-info-block__label">No. Telepon</div>
                <div class="nota-info-block__value"><?= htmlspecialchars($nota['customer_phone'] ?? '—') ?></div>
            </div>
            <div>
                <div class="nota-info-block__label">Alamat</div>
                <div class="nota-info-block__value"><?= htmlspecialchars($nota['customer_address'] ?? '—') ?></div>
            </div>
            <div>
                <div class="nota-info-block__label">Tanggal Servis</div>
                <div class="nota-info-block__value">
                    <?= date('d M Y', strtotime($nota['booking_date'] ?? $nota['wo_created_at'])) ?>
                </div>
            </div>
            <div>
                <div class="nota-info-block__label">Kendaraan</div>
                <div class="nota-info-block__value">
                    <?= htmlspecialchars($nota['brand'] . ' ' . $nota['vehicle_type']) ?>
                    — <?= htmlspecialchars($nota['color']) ?>
                </div>
            </div>
            <div>
                <div class="nota-info-block__label">No. Rangka</div>
                <div class="nota-info-block__value" style="font-family:monospace;font-size:12.5px;">
                    <?= htmlspecialchars($nota['chassis_number']) ?>
                </div>
            </div>
            <div>
                <div class="nota-info-block__label">No. Mesin</div>
                <div class="nota-info-block__value" style="font-family:monospace;font-size:12.5px;">
                    <?= htmlspecialchars($nota['engine_number']) ?>
                </div>
            </div>
            <div>
                <div class="nota-info-block__label">Mekanik</div>
                <div class="nota-info-block__value"><?= htmlspecialchars($nota['mechanic_name'] ?? '—') ?></div>
            </div>
        </div>

        <!-- Tabel Sparepart -->
        <div class="nota-section-title">Rincian Komponen & Jasa</div>
        <table class="nota-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Komponen</th>
                    <th>SKU</th>
                    <th class="right">Harga Satuan</th>
                    <th class="center">Qty</th>
                    <th class="right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($nota['spareparts'])): ?>
                    <?php foreach ($nota['spareparts'] as $i => $sp): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($sp['nama_sparepart']) ?></td>
                            <td style="color:#6b7a90;font-size:12px;font-family:monospace;">
                                <?= htmlspecialchars($sp['sku'] ?? '—') ?>
                            </td>
                            <td class="right"><?= rupiahC((float)$sp['harga_satuan']) ?></td>
                            <td class="center"><?= (int)$sp['quantity'] ?></td>
                            <td class="right"><strong><?= rupiahC((float)$sp['subtotal']) ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="color:#9aaaba;text-align:center;padding:16px 0;font-style:italic;">
                            Tidak ada komponen sparepart.
                        </td>
                    </tr>
                <?php endif; ?>

                <!-- Baris biaya jasa servis -->
                <tr style="background:#f8fafc;">
                    <td><?= count($nota['spareparts'] ?? []) + 1 ?></td>
                    <td><strong>Biaya Jasa Servis</strong></td>
                    <td style="color:#6b7a90;font-size:12px;">—</td>
                    <td class="right"><?= rupiahC((float)$nota['biaya_jasa']) ?></td>
                    <td class="center">1</td>
                    <td class="right"><strong><?= rupiahC((float)$nota['biaya_jasa']) ?></strong></td>
                </tr>
            </tbody>
        </table>

        <!-- Ringkasan Total -->
        <div class="nota-total-box">
            <div class="nota-total-row">
                <span class="nota-total-row__label">Total Komponen</span>
                <span class="nota-total-row__value"><?= rupiahC((float)$nota['total_komponen']) ?></span>
            </div>
            <div class="nota-total-row">
                <span class="nota-total-row__label">Biaya Jasa Servis</span>
                <span class="nota-total-row__value"><?= rupiahC((float)$nota['biaya_jasa']) ?></span>
            </div>
            <hr class="nota-total-divider">
            <div class="nota-total-row">
                <span class="nota-total-grand-label">TOTAL YANG DIBAYAR</span>
                <span class="nota-total-grand-value"><?= rupiahC((float)$nota['grand_total']) ?></span>
            </div>
        </div>

        <!-- Catatan & Tanda Tangan -->
        <div class="nota-footer-grid">
            <div class="nota-note">
                <strong style="display:block;margin-bottom:6px;color:#1e2a3b;">Catatan:</strong>
                Nota ini merupakan bukti resmi pembayaran layanan servis kendaraan.
                Simpan nota ini sebagai referensi garansi jasa selama <strong>30 hari</strong>
                sejak tanggal servis.
                <?php if (!empty($nota['wo_description'])): ?>
                    <br><br><em>Catatan mekanik: <?= htmlspecialchars($nota['wo_description']) ?></em>
                <?php endif; ?>
            </div>
            <div class="nota-ttd">
                <div class="nota-ttd__label">Kasir / Petugas</div>
                <div class="nota-ttd__line"></div>
                <div class="nota-ttd__name">(______________________)</div>
            </div>
        </div>

    </div>
</div>

</body>
</html>