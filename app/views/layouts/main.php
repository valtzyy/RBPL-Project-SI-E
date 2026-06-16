<!-- app/views/layouts/main.php -->
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

            // IDE 2: Mencegah submit langsung & memunculkan animasi
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Tahan pengiriman data asli ke server

                // Pastikan tidak ada kolom yang kosong
                let isValid = true;
                inputs.forEach(input => {
                    if (input.value.trim() === '') {
                        input.parentElement.classList.add('has-error');
                        isValid = false;
                    }
                });

                if (!isValid) return; // Jika ada error, batalkan animasi

                // 1. Munculkan kotak dengan Spinner
                overlay.classList.add('active');
                toastIcon.className = 'spinner'; // Pastikan ikonnya spinner
                toastIcon.innerHTML = '';
                toastText.textContent = 'Menyimpan data...';
                toastText.style.color = 'var(--text-main)';

                // 2. Simulasi loading (1.5 detik), lalu berubah jadi Ceklis Hijau
                setTimeout(() => {
                    toastIcon.className = 'checkmark'; // Ganti kelas CSS ke ceklis
                    toastIcon.innerHTML = '✓';
                    toastText.textContent = 'Berhasil disimpan!';
                    toastText.style.color = '#10b981';

                    // 3. Tunggu sebentar agar user bisa melihat ceklis, lalu tutup dan submit
                    setTimeout(() => {
                        overlay.classList.remove('active');

                        // CATATAN: Jika backend sudah siap, gunakan baris di bawah ini:
                        form.submit(); 
                    }, 1200);

                }, 1500); // Waktu putaran spinner
            });
        });
    </script>
</body>

</html>