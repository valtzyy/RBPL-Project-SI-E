<!-- <?php

echo "<pre>";
var_dump($detailLog ?? null);
echo "</pre>"; -->

<?php
$title = 'Riwayat Servis';

if (empty($detailLog)) :
?>
    <div class="dl-card">
        <h2>Data Riwayat Tidak Ditemukan</h2>
        <p>Work Order tidak ditemukan atau belum memiliki riwayat servis.</p>
    </div>
<?php
    return;
endif;
?>

<div class="dl-page">

    <div class="dl-card">
        <h1>Riwayat Servis</h1>

        <div class="history-header">
            <div class="history-item">
                <strong>Work Order ID</strong>
                <span>#<?= htmlspecialchars($detailLog['work_order_id']) ?></span>
            </div>

            <div class="history-item">
                <strong>Status</strong>
                <span><?= htmlspecialchars($detailLog['wo_status']) ?></span>
            </div>

            <div class="history-item">
                <strong>Tanggal Dibuat</strong>
                <span><?= date('d M Y H:i', strtotime($detailLog['wo_created_at'])) ?></span>
            </div>
        </div>

        <div class="history-description">
            <strong>Deskripsi Servis</strong>
            <p><?= nl2br(htmlspecialchars($detailLog['wo_description'] ?? '-')) ?></p>
        </div>
    </div>

    <div class="dl-card">
        <h2>Riwayat Pekerjaan</h2>

        <?php if (!empty($detailLog['logs'])) : ?>

            <div class="timeline">

                <?php foreach ($detailLog['logs'] as $log) : ?>

                    <div class="timeline-item">
                        <div class="timeline-content">

                            <div class="timeline-header">
                                <span class="timeline-status">
                                    <?= htmlspecialchars($log['log_status']) ?>
                                </span>

                                <span class="timeline-date">
                                    <?= date('d M Y H:i', strtotime($log['log_created'])) ?>
                                </span>
                            </div>

                            <div class="timeline-notes">
                                <?= nl2br(htmlspecialchars($log['log_notes'] ?? '-')) ?>
                            </div>

                        </div>
                    </div>

                <?php endforeach; ?>

            </div>

        <?php else : ?>

            <p>Tidak ada log pekerjaan.</p>

        <?php endif; ?>
    </div>

    <div class="dl-card">
        <h2>Sparepart yang Digunakan</h2>

        <?php if (!empty($detailLog['spareparts'])) : ?>

            <table class="dl-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Sparepart</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($detailLog['spareparts'] as $index => $part) : ?>

                        <tr>
                            <td><?= $index + 1 ?></td>

                            <td>
                                <?= htmlspecialchars($part['sparepart_name']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($part['quantity']) ?>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                </tbody>
            </table>

        <?php else : ?>

            <p>Tidak ada sparepart yang tercatat.</p>

        <?php endif; ?>
    </div>

</div>