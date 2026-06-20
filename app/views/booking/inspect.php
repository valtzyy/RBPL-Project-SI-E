<!-- app/views/booking/inspect.php -->
<div class="inspect-container">
    <div class="inspect-card">
        <a href="/booking/queue?date=<?= $booking['booking_date'] ?>" class="back-link">&larr; Kembali ke Antrean</a>
        
        <h2>Lembar Observasi Awal & Pemeriksaan Teknis</h2>
        <p class="subtitle">Catat kondisi kelayakan unit kendaraan sebelum dimasukkan ke antrean pengerjaan teknisi.</p>

        <div class="vehicle-info">
            <div class="info-group">
                <span class="info-label">Pelanggan</span>
                <span class="info-value"><?= htmlspecialchars($booking['customer_name']) ?></span>
            </div>
            <div class="info-group">
                <span class="info-label">Model Kendaraan</span>
                <span class="info-value"><?= htmlspecialchars($booking['vehicle_name']) ?></span>
            </div>
            <div class="info-group">
                <span class="info-label">Nomor Polisi</span>
                <span class="info-value badge badge-plate"><?= htmlspecialchars($booking['plate_number']) ?></span>
            </div>
        </div>

        <div id="alert-box" class="alert" style="display: none;"></div>

        <form id="inspect-form" method="POST" action="/booking/inspect/<?= $booking['id'] ?>/convert">
            
            <div class="form-group">
                <label for="notes">Catatan Observasi Tambahan / Detail Keluhan</label>
                <textarea name="notes" id="notes" rows="4" placeholder="Tuliskan detail keluhan pelanggan atau catatan teknis tambahan..." required></textarea>
            </div>

            <hr style="border: 0; border-top: 1px solid #eee; margin: 25px 0;">

            <div class="form-group">
                <label for="assigned_mechanic" style="color: #0056b3; font-size: 15px;">Tugaskan Mekanik (Teknisi)</label>
                <select name="assigned_mechanic" id="assigned_mechanic" required style="border-color: #0056b3;">
                    <option value="">-- Pilih Mekanik --</option>
                    <?php foreach ($mechanics as $mechanic): ?>
                        <option value="<?= $mechanic['id'] ?>"><?= htmlspecialchars($mechanic['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" id="submit-btn" class="btn btn-primary">Simpan & Buat Work Order Resmi</button>
        </form>
    </div>
</div>

<style>
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        background-color: #f4f6f9;
        color: #333;
        margin: 0;
        padding: 40px 20px;
    }
    .inspect-container {
        max-width: 750px;
        margin: 0 auto;
    }
    .inspect-card {
        background: #ffffff;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }
    .back-link {
        display: inline-block;
        color: #0056b3;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 20px;
    }
    .back-link:hover {
        text-decoration: underline;
    }
    h2 {
        margin-top: 0;
        font-size: 24px;
        color: #111;
        font-weight: 700;
    }
    .subtitle {
        color: #666;
        font-size: 14px;
        margin-bottom: 25px;
    }
    .vehicle-info {
        display: flex;
        gap: 30px;
        background-color: #f8f9fa;
        padding: 18px 24px;
        border-radius: 8px;
        margin-bottom: 30px;
        border: 1px solid #e9ecef;
    }
    .info-group {
        display: flex;
        flex-direction: column;
    }
    .info-label {
        font-size: 12px;
        color: #666;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 4px;
    }
    .info-value {
        font-weight: 700;
        font-size: 15px;
        color: #111;
    }
    .badge-plate {
        background-color: #e9ecef;
        color: #495057;
        font-family: monospace;
        padding: 3px 8px;
        border-radius: 4px;
        border: 1px solid #ced4da;
    }
    .section-title {
        font-weight: 700;
        font-size: 16px;
        color: #333;
        margin-bottom: 15px;
        border-left: 4px solid #0056b3;
        padding-left: 10px;
    }
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    .form-group {
        margin-bottom: 20px;
    }
    label {
        display: block;
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 8px;
        color: #444;
    }
    select, input, textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 15px;
        box-sizing: border-box;
        transition: border-color 0.2s;
    }
    select:focus, input:focus, textarea:focus {
        border-color: #0056b3;
        outline: none;
    }
    .btn {
        width: 100%;
        padding: 15px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 700;
        cursor: pointer;
        transition: background-color 0.2s;
        margin-top: 15px;
    }
    .btn-primary {
        background-color: #28a745;
        color: white;
    }
    .btn-primary:hover {
        background-color: #218838;
    }
    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
        font-weight: 500;
    }
    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('inspect-form');
        const alertBox = document.getElementById('alert-box');

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!confirm('Yakin ingin membuat Work Order resmi dari hasil observasi ini?')) {
                return;
            }

            const formData = new FormData(form);
            alertBox.style.display = 'none';

            fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alertBox.className = 'alert alert-success';
                    alertBox.innerHTML = data.message + ' Mengalihkan...';
                    alertBox.style.display = 'block';
                    setTimeout(() => {
                        window.location.href = `/booking/queue?date=<?= $booking['booking_date'] ?>`;
                    }, 2000);
                } else {
                    alertBox.className = 'alert alert-danger';
                    alertBox.innerHTML = data.message;
                    alertBox.style.display = 'block';
                }
            })
            .catch(err => {
                alertBox.className = 'alert alert-danger';
                alertBox.innerHTML = 'Terjadi kesalahan sistem saat membuat Work Order.';
                alertBox.style.display = 'block';
            });
        });
    });
</script>
