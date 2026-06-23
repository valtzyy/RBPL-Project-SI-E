<?php $title = $title ?? 'Jadwal Serah Terima'; ?>

<div style="
    padding:24px;
    background:#f5f6fa;
    min-height:100vh;
    font-family:'Segoe UI', Arial, sans-serif;
">

    <div style="
        display:flex;
        justify-content:space-between;
        align-items:center;
        margin-bottom:24px;
    ">
        <div>
            <h1 style="
                margin:0;
                font-size:34px;
                font-weight:700;
                color:#1e293b;
            ">
                Delivery Schedule
            </h1>

            <p style="
                margin:4px 0 0;
                color:#64748b;
                font-size:14px;
            ">
                Riwayat delivery schedule
            </p>
        </div>

        <a href="/delivery/create"
           style="
                background:#000;
                color:#fff;
                text-decoration:none;
                padding:12px 20px;
                border-radius:10px;
                font-size:14px;
                font-weight:600;
                display:inline-flex;
                align-items:center;
                box-shadow:0 2px 8px rgba(0,0,0,.15);
           ">
            + Buat Agenda Pengiriman
        </a>
    </div>

    <div style="
        background:#fff;
        border-radius:12px;
        border:1px solid #e5e7eb;
        overflow:hidden;
        box-shadow:0 1px 3px rgba(0,0,0,.05);
    ">

        <div style="
            display:flex;
            justify-content:space-between;
            align-items:center;
            padding:20px 24px;
            border-bottom:1px solid #edf2f7;
        ">
            <div>
                <h2 style="
                    margin:0;
                    font-size:26px;
                    font-weight:700;
                    color:#1f2937;
                ">
                    Data Delivery
                </h2>

                <p style="
                    margin:4px 0 0;
                    font-size:13px;
                    color:#6b7280;
                ">
                    Status keberhasilan pengiriman
                </p>
            </div>

            <input
                id="searchInput"
                type="text"
                placeholder="Cari Nama Customer..."
                style="
                    width:280px;
                    padding:10px 14px;
                    border:none;
                    background:#f1f5f9;
                    border-radius:8px;
                    outline:none;
                    font-size:14px;
                "
            >
        </div>

        <table style="
            width:100%;
            border-collapse:collapse;
        ">
            <thead>
                <tr style="background:#eef2f7;">

                    <th style="
                        text-align:left;
                        padding:16px 24px;
                        font-size:11px;
                        font-weight:700;
                        color:#64748b;
                        text-transform:uppercase;
                        letter-spacing:.5px;
                    ">
                        No
                    </th>

                    <th style="
                        text-align:left;
                        padding:16px 24px;
                        font-size:11px;
                        font-weight:700;
                        color:#64748b;
                        text-transform:uppercase;
                        letter-spacing:.5px;
                    ">
                        Nama Customer
                    </th>

                    <th style="
                        text-align:left;
                        padding:16px 24px;
                        font-size:11px;
                        font-weight:700;
                        color:#64748b;
                        text-transform:uppercase;
                        letter-spacing:.5px;
                    ">
                        Kendaraan
                    </th>

                    <th style="
                        text-align:left;
                        padding:16px 24px;
                        font-size:11px;
                        font-weight:700;
                        color:#64748b;
                        text-transform:uppercase;
                        letter-spacing:.5px;
                    ">
                        Tanggal
                    </th>

                    <th style="
                        text-align:left;
                        padding:16px 24px;
                        font-size:11px;
                        font-weight:700;
                        color:#64748b;
                        text-transform:uppercase;
                        letter-spacing:.5px;
                    ">
                        Status
                    </th>

                </tr>
            </thead>

            <tbody>

            <?php if (empty($schedules)): ?>

                <tr>
                    <td colspan="5"
                        style="
                            text-align:center;
                            padding:40px;
                            color:#94a3b8;
                            font-size:14px;
                        ">
                        Belum ada jadwal serah terima.
                    </td>
                </tr>

            <?php else: ?>

                <?php $no = 1; ?>

                <?php foreach ($schedules as $s): ?>

                    <?php if (empty($s['customer_name'])) continue; ?>

                    <tr
                        onclick="window.location.href='/delivery/<?= $s['id'] ?>'"
                        style="cursor:pointer;"
                        onmouseover="this.style.background='#fafafa'"
                        onmouseout="this.style.background='transparent'"
                    >

                        <td style="
                            padding:18px 24px;
                            border-bottom:1px solid #f1f5f9;
                            color:#334155;
                            font-size:14px;
                        ">
                            <?= $no++ ?>
                        </td>

                        <td
                            class="customer-name"
                            style="
                                padding:18px 24px;
                                border-bottom:1px solid #f1f5f9;
                                color:#111827;
                                font-size:14px;
                                font-weight:600;
                            "
                        >
                            <?= htmlspecialchars($s['customer_name']) ?>
                        </td>

                        <td style="
                            padding:18px 24px;
                            border-bottom:1px solid #f1f5f9;
                            color:#334155;
                            font-size:14px;
                        ">
                            <?= htmlspecialchars($s['brand'] . ' ' . $s['type'] . ' - ' . $s['color']) ?>
                        </td>

                        <td style="
                            padding:18px 24px;
                            border-bottom:1px solid #f1f5f9;
                            color:#334155;
                            font-size:14px;
                        ">
                            <?= htmlspecialchars($s['scheduled_date']) ?>
                        </td>

                        <td style="
                            padding:18px 24px;
                            border-bottom:1px solid #f1f5f9;
                        ">

                            <?php if ($s['status'] === 'confirmed'): ?>

                                <span style="
                                    display:inline-block;
                                    background:#dff4ea;
                                    color:#0f7b50;
                                    padding:6px 18px;
                                    border-radius:999px;
                                    font-size:11px;
                                    font-weight:700;
                                ">
                                    SELESAI
                                </span>

                            <?php elseif ($s['status'] === 'failed'): ?>

                                <span style="
                                    display:inline-block;
                                    background:#fde2e2;
                                    color:#dc2626;
                                    padding:6px 18px;
                                    border-radius:999px;
                                    font-size:11px;
                                    font-weight:700;
                                ">
                                    GAGAL
                                </span>

                            <?php else: ?>

                                <span style="
                                    display:inline-block;
                                    background:#fff4d6;
                                    color:#d97706;
                                    padding:6px 18px;
                                    border-radius:999px;
                                    font-size:11px;
                                    font-weight:700;
                                ">
                                    TERJADWAL
                                </span>

                            <?php endif; ?>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const searchInput = document.getElementById('searchInput');

    searchInput.addEventListener('keyup', function () {

        const keyword = this.value.toLowerCase().trim();

        document.querySelectorAll('tbody tr').forEach(row => {

            const customerCell = row.querySelector('.customer-name');

            if (!customerCell) return;

            const customerName = customerCell.textContent.toLowerCase();

            row.style.display = customerName.includes(keyword)
                ? ''
                : 'none';

        });

    });

});
</script>