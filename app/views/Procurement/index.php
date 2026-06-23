<h2>Form Pengadaan Kendaraan</h2>
<p>Buat permintaan pengadaan untuk satu atau beberapa unit kendaraan sekaligus.</p>

<form method="POST" action="/procurement/store">
    <div id="vehicles-list">
        <!-- Dynamic rows will be inserted here -->
    </div>

    <div style="margin-top: 16px;">
        <button type="button" onclick="createRow()">
            + Tambah Kendaraan
        </button>
    </div>

    <div style="margin-top: 20px;">
        <a href="/">Kembali</a>
        <button type="submit">
            Kirim Permintaan
        </button>
    </div>
</form>

<script>
    // Data kendaraan dari PHP
    const vehiclesData = <?= json_encode($vehicles); ?>;

    function createRow() {
        const container = document.getElementById('vehicles-list');
        const row = document.createElement('div');
        row.style.marginBottom = '10px';
        
        // Select Kendaraan
        const select = document.createElement('select');
        select.name = 'vehicle_ids[]';
        select.required = true;
        
        // Default option
        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.textContent = '-- Pilih Kendaraan --';
        defaultOption.disabled = true;
        defaultOption.selected = true;
        select.appendChild(defaultOption);

        vehiclesData.forEach(vehicle => {
            const option = document.createElement('option');
            option.value = vehicle.id;
            const priceFormatted = Number(vehicle.price).toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 });
            option.textContent = `${vehicle.brand} ${vehicle.type} (${vehicle.color}) - ${priceFormatted}`;
            select.appendChild(option);
        });

        // Input Jumlah
        const qtyInput = document.createElement('input');
        qtyInput.type = 'number';
        qtyInput.name = 'quantities[]';
        qtyInput.min = '1';
        qtyInput.value = '1';
        qtyInput.required = true;
        qtyInput.style.marginLeft = '10px';

        // Button Hapus
        const btnDelete = document.createElement('button');
        btnDelete.type = 'button';
        btnDelete.textContent = 'Hapus';
        btnDelete.style.marginLeft = '10px';
        btnDelete.onclick = function() {
            if (container.children.length > 1) {
                row.remove();
            } else {
                alert('Minimal harus ada 1 baris pengadaan kendaraan.');
            }
        };

        row.appendChild(select);
        row.appendChild(qtyInput);
        row.appendChild(btnDelete);
        
        container.appendChild(row);
    }

    // Load first row automatically on page load
    document.addEventListener('DOMContentLoaded', () => {
        createRow();
    });
</script>

<h2>Daftar Pengadaan Kendaraan</h2>
<p>Berikut adalah seluruh data permintaan pengadaan kendaraan. Silakan pilih pengadaan yang berstatus <strong>sent</strong> untuk merekam penerimaan barang dari pabrik.</p>

<table border="1" cellpadding="8" cellspacing="0">
    <thead>
        <tr>
            <th>ID</th>
            <th>Kode Permintaan</th>
            <th>ID Pembuat</th>
            <th>Status</th>
            <th>Tanggal Dibuat</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($procurements)): ?>
            <tr>
                <td colspan="6" align="center">Tidak ada data pengadaan.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($procurements as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['id']) ?></td>
                    <td><?= htmlspecialchars($p['request_code']) ?></td>
                    <td><?= htmlspecialchars($p['requested_by']) ?></td>
                    <td><?= htmlspecialchars($p['status']) ?></td>
                    <td><?= htmlspecialchars($p['created_at']) ?></td>
                    <td>
                        <?php if ($p['status'] === 'sent'): ?>
                            <a href="/procurement/receipt/<?= htmlspecialchars($p['id']) ?>">
                                [ Lakukan Pengecekan ]
                            </a>
                        <?php elseif ($p['status'] === 'received'): ?>
                            <span>Sudah Diterima</span>
                        <?php else: ?>
                            <span>-</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<br>
<div>
    <a href="/">Kembali ke Beranda</a>
</div>
