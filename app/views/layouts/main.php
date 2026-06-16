<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DealerLink DMS</title>

    <link rel="stylesheet" href="/css/style.css">

</head>

<body>

    <div class="main-content">
        <?= $content ?>
    </div>

    <script src="/js/sparepart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('form-sparepart');

            // Jika kita sedang tidak berada di halaman form sparepart, abaikan script ini
            if (!form) return;

            const inputs = form.querySelectorAll('.form-input');
            const overlay = document.getElementById('toast-overlay');
            const toastIcon = document.getElementById('toast-icon');
            const toastText = document.getElementById('toast-text');
            
            // Tangkap tombol submit-nya
            const submitBtn = form.querySelector('.btn-submit');

            // IDE 1: Validasi Real-Time (Saat kursor meninggalkan input / blur)
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (this.value.trim() === '') {
                        this.parentElement.classList.add('has-error');
                    } else {
                        this.parentElement.classList.remove('has-error');
                    }
                });

                // Hapus error saat mulai mengetik lagi
                input.addEventListener('input', function() {
                    this.parentElement.classList.remove('has-error');
                });
            });

            // INTEGRASI PBI-13.2: Mengirim data ke Database nyata via AJAX
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Tahan pengiriman form bawaan browser

                // Jalankan pengecekan validasi sebelum mengirim
                let isValid = true;
                inputs.forEach(input => {
                    if (input.value.trim() === '') {
                        input.parentElement.classList.add('has-error');
                        isValid = false;
                    }
                });

                if (!isValid) return; // Batalkan proses jika form kosong

                // --- TRIK PRO UX: Matikan tombol agar tidak bisa di-klik ganda ---
                if (submitBtn) submitBtn.disabled = true;

                // 1. Munculkan Animasi Spinner (Mulai Menyimpan)
                if (overlay) {
                    overlay.classList.add('active');
                    toastIcon.className = 'spinner'; 
                    toastIcon.innerHTML = '';
                    toastText.textContent = 'Menyimpan data...';
                    toastText.style.color = 'var(--text-main)';
                }

                // 2. Bungkus seluruh data inputan form
                const formData = new FormData(form);

                // 3. Tembak endpoint POST buatan Back-End
                fetch('/sparepart/store', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest' 
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        // JIKA SUKSES: Ubah Spinner menjadi Ceklis Hijau resmi Figma
                        toastIcon.className = 'checkmark'; 
                        toastIcon.innerHTML = '✓';
                        toastText.textContent = 'Berhasil disimpan!';
                        toastText.style.color = 'var(--success-text)';

                        // Bersihkan seluruh isi form agar siap menerima input baru
                        form.reset();

                        // Beri waktu 1.5 detik agar user bisa melihat ceklis sukses, lalu tutup overlay
                        setTimeout(() => {
                            overlay.classList.remove('active');
                        }, 1500);

                    } else {
                        // JIKA GAGAL (Validasi controller gagal / SKU duplikat)
                        toastIcon.className = ''; 
                        toastIcon.innerHTML = '❌';
                        toastText.textContent = data.message || 'Gagal menyimpan!';
                        toastText.style.color = 'var(--danger)';

                        setTimeout(() => {
                            overlay.classList.remove('active');
                        }, 2500);
                    }
                })
                .catch(err => {
                    // JIKA KONEKSI PUTUS / SERVER ERROR
                    console.error("Error storing sparepart:", err);
                    toastIcon.className = ''; 
                    toastIcon.innerHTML = '⚠️';
                    toastText.textContent = 'Terjadi kesalahan jaringan sistem.';
                    toastText.style.color = 'var(--danger)';

                    setTimeout(() => {
                        overlay.classList.remove('active');
                    }, 2500);
                })
                .finally(() => {
                    // --- TRIK PRO UX: Nyalakan kembali tombol submit setelah request selesai (sukses/gagal) ---
                    if (submitBtn) submitBtn.disabled = false;
                });
            });
        });
    </script>
</body>

</html>