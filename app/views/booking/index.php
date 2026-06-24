<!-- app/views/booking/index.php -->
<div class="booking-container">
    <div class="booking-card">
        <h2>Pendaftaran Booking Servis</h2>
        <p class="subtitle">Daftarkan jadwal servis pelanggan dan periksa ketersediaan slot secara real-time.</p>

        <div id="alert-box" class="alert" style="display: none;"></div>

        <form id="booking-form" method="POST" action="/booking/store">
            <!-- Stepper Progress Header -->
            <div class="stepper-header">
                <span class="step-indicator active" id="indicator-1">1. Informasi Pelanggan</span>
                <span class="step-divider"></span>
                <span class="step-indicator" id="indicator-2">2. Kendaraan & Jadwal</span>
            </div>

            <!-- Step 1 Content -->
            <div id="step-1" class="step-content">
                <div class="form-group">
                    <label>Tipe Pelanggan</label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="customer_type" value="existing" checked> Pelanggan Lama
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="customer_type" value="new"> Pelanggan Baru
                        </label>
                    </div>
                </div>

                <div class="form-group" id="existing-customer-group">
                    <label for="customer_id">Pilih Pelanggan</label>
                    <select name="customer_id" id="customer_id">
                        <option value="">-- Pilih Pelanggan --</option>
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?= $customer['id'] ?>">
                                <?= htmlspecialchars($customer['name']) ?> (<?= htmlspecialchars($customer['phone'] ?? '-') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div id="new-customer-group" style="display: none;">
                    <div class="form-group">
                        <label for="new_customer_name">Nama Pelanggan Baru</label>
                        <input type="text" name="new_customer_name" id="new_customer_name" placeholder="Nama Lengkap">
                    </div>
                    <div class="form-group">
                        <label for="new_customer_phone">Nomor Telepon Baru</label>
                        <input type="text" name="new_customer_phone" id="new_customer_phone" placeholder="Contoh: 08123456789">
                    </div>
                </div>

                <button type="button" id="next-btn" class="btn btn-primary" style="margin-top: 10px;">Selanjutnya</button>
            </div>

            <!-- Step 2 Content -->
            <div id="step-2" class="step-content" style="display: none;">
                <div class="form-group">
                    <label for="plate_number">Nomor Polisi (Plat Nomor)</label>
                    <input type="text" name="plate_number" id="plate_number" placeholder="Contoh: B 1234 CD">
                </div>

                <div class="form-group">
                    <label for="vehicle_name">Nama / Model Kendaraan</label>
                    <input type="text" name="vehicle_name" id="vehicle_name" placeholder="Contoh: Toyota Avanza">
                </div>

                <div class="form-group">
                    <label for="booking_date">Tanggal Booking</label>
                    <input type="date" name="booking_date" id="booking_date" min="<?= date('Y-m-d') ?>">
                    <div id="slot-indicator" class="slot-indicator"></div>
                </div>

                <div class="btn-group">
                    <button type="button" id="back-btn" class="btn btn-secondary">Sebelumnya</button>
                    <button type="submit" id="submit-btn" class="btn btn-primary" disabled>Simpan Booking</button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    /* Stepper Progress Indicator Styles */
    .stepper-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 25px;
        background-color: #f8f9fa;
        padding: 12px 18px;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }

    .step-indicator {
        font-size: 13px;
        font-weight: 600;
        color: #868e96;
        transition: color 0.2s;
    }

    .step-indicator.active {
        color: #0056b3;
    }

    .step-divider {
        flex: 1;
        height: 2px;
        background-color: #e9ecef;
        margin: 0 12px;
    }

    .btn-group {
        display: flex;
        gap: 12px;
        margin-top: 15px;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        background-color: #f4f6f9;
        color: #333;
        margin: 0;
        padding: 40px 20px;
    }

    .booking-container {
        max-width: 600px;
        margin: 0 auto;
    }

    .booking-card {
        background: #ffffff;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
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
        margin-bottom: 30px;
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

    .radio-group {
        display: flex;
        gap: 20px;
        margin-top: 5px;
        margin-bottom: 10px;
    }

    .radio-label {
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        font-size: 14px;
        color: #555;
    }

    .radio-label input {
        width: auto;
        margin: 0;
    }

    select,
    input {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 15px;
        box-sizing: border-box;
        transition: border-color 0.2s;
    }

    select:focus,
    input:focus {
        border-color: #0056b3;
        outline: none;
    }

    .slot-indicator {
        margin-top: 8px;
        font-size: 13px;
        font-weight: 600;
    }

    .btn {
        width: 100%;
        padding: 14px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .btn-primary {
        background-color: #0056b3;
        color: white;
    }

    .btn-primary:hover {
        background-color: #004085;
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
        const dateInput = document.getElementById('booking_date');
        const slotIndicator = document.getElementById('slot-indicator');
        const submitBtn = document.getElementById('submit-btn');
        const alertBox = document.getElementById('alert-box');
        const form = document.getElementById('booking-form');

        const customerTypeRadios = document.querySelectorAll('input[name="customer_type"]');
        const existingGroup = document.getElementById('existing-customer-group');
        const newGroup = document.getElementById('new-customer-group');
        const customerSelect = document.getElementById('customer_id');
        const newNameInput = document.getElementById('new_customer_name');
        const newPhoneInput = document.getElementById('new_customer_phone');

        // Step navigation elements
        const step1 = document.getElementById('step-1');
        const step2 = document.getElementById('step-2');
        const indicator1 = document.getElementById('indicator-1');
        const indicator2 = document.getElementById('indicator-2');
        const nextBtn = document.getElementById('next-btn');
        const backBtn = document.getElementById('back-btn');

        // Variables to avoid recreating the same customer if user goes back and forth
        let lastCreatedCustomerId = null;
        let lastCreatedCustomerName = '';
        let lastCreatedCustomerPhone = '';

        // Toggle customer type fields inside Step 1
        customerTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'existing') {
                    existingGroup.style.display = 'block';
                    newGroup.style.display = 'none';
                } else {
                    existingGroup.style.display = 'none';
                    newGroup.style.display = 'block';
                }
            });
        });

        function goToStep2() {
            step1.style.display = 'none';
            step2.style.display = 'block';
            indicator1.classList.remove('active');
            indicator2.classList.add('active');
        }

        // Step 1 validation and navigation to Step 2
        nextBtn.addEventListener('click', function() {
            alertBox.style.display = 'none';
            const customerType = document.querySelector('input[name="customer_type"]:checked').value;

            if (customerType === 'existing') {
                if (!customerSelect.value) {
                    showAlert('Silakan pilih pelanggan terlebih dahulu.');
                    return;
                }
                goToStep2();
            } else {
                const name = newNameInput.value.trim();
                const phone = newPhoneInput.value.trim();
                if (!name || !phone) {
                    showAlert('Silakan isi nama dan nomor telepon pelanggan baru.');
                    return;
                }

                // If customer with same name/phone was already created, proceed without recreating
                if (lastCreatedCustomerName === name && lastCreatedCustomerPhone === phone && lastCreatedCustomerId) {
                    customerSelect.value = lastCreatedCustomerId;
                    goToStep2();
                    return;
                }

                // Call immediate customer creation API
                nextBtn.disabled = true;
                nextBtn.innerHTML = 'Mendaftarkan...';

                const customerFormData = new FormData();
                customerFormData.append('new_customer_name', name);
                customerFormData.append('new_customer_phone', phone);

                fetch('/booking/create-customer', {
                        method: 'POST',
                        body: customerFormData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {

                            showAlert(data.message, 'success');

                            setTimeout(() => {
                                window.location.href = data.redirect;
                            }, 1000);

                        } else {
                            showAlert(data.message, 'danger');
                        }
                    })
                    .catch(err => {
                        nextBtn.disabled = false;
                        nextBtn.innerHTML = 'Selanjutnya';
                        showAlert('Terjadi kesalahan koneksi saat mendaftarkan pelanggan.');
                    });
            }
        });

        // Navigation back to Step 1
        backBtn.addEventListener('click', function() {
            alertBox.style.display = 'none';
            step2.style.display = 'none';
            step1.style.display = 'block';
            indicator2.classList.remove('active');
            indicator1.classList.add('active');
        });

        function showAlert(message, type = 'danger') {
            alertBox.className = `alert alert-${type}`;
            alertBox.innerHTML = message;
            alertBox.style.display = 'block';
        }

        // Pengecekan slot otomatis ketika tanggal diubah
        dateInput.addEventListener('change', function() {
            const date = this.value;
            if (!date) return;

            slotIndicator.innerHTML = 'Memeriksa slot...';
            slotIndicator.style.color = '#666';

            fetch(`/booking/check-slot?date=${date}`)
                .then(res => res.json())
                .then(data => {
                    if (data.available) {
                        slotIndicator.innerHTML = `✓ ${data.message}`;
                        slotIndicator.style.color = '#28a745';
                        submitBtn.disabled = false;
                    } else {
                        slotIndicator.innerHTML = `✗ ${data.message}`;
                        slotIndicator.style.color = '#dc3545';
                        submitBtn.disabled = true;
                    }
                })
                .catch(err => {
                    slotIndicator.innerHTML = 'Gagal memeriksa slot.';
                    slotIndicator.style.color = '#dc3545';
                });
        });

        // Submit form via AJAX
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Step 2 validation
            const plateNumber = document.getElementById('plate_number').value.trim();
            const vehicleName = document.getElementById('vehicle_name').value.trim();
            const bookingDate = dateInput.value;

            if (!plateNumber || !vehicleName || !bookingDate) {
                showAlert('Semua data kendaraan dan tanggal booking wajib diisi.');
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
                        showAlert(data.message, 'success');
                        form.reset();
                        slotIndicator.innerHTML = '';

                        // Reset Step 1 & 2 states
                        step2.style.display = 'none';
                        step1.style.display = 'block';
                        indicator2.classList.remove('active');
                        indicator1.classList.add('active');
                        existingGroup.style.display = 'block';
                        newGroup.style.display = 'none';

                        lastCreatedCustomerId = null;
                        lastCreatedCustomerName = '';
                        lastCreatedCustomerPhone = '';

                        setTimeout(() => window.location.href = '/booking', 1500);
                    } else {
                        showAlert(data.message, 'danger');
                    }
                })
                .catch(err => {
                    showAlert('Terjadi kesalahan sistem.', 'danger');
                });
        });
    });
</script>