<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Dokumen Rahasia</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 90%;
        }

        h2 {
            color: #202124;
            margin-top: 0;
            text-align: center;
        }

        .info-text {
            color: #5f6368;
            font-size: 14px;
            text-align: center;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #3c4043;
        }

        /* Input File Styling */
        input[type="file"] {
            display: block;
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border: 2px dashed #dadce0;
            border-radius: 8px;
            background-color: #f8f9fa;
            cursor: pointer;
        }

        input[type="file"]:hover {
            border-color: #1a73e8;
            background-color: #f1f3f4;
        }

        .btn-submit {
            width: 100%;
            background-color: #1a73e8;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-submit:hover {
            background-color: #1557b0;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Upload Dokumen</h2>
        <p class="info-text">Dokumen yang Anda unggah akan disimpan secara privat dan aman di Cloudinary.</p>
        <form action="/document" method="POST" id="formDokumen">
            <div>
                <label for="pilih_file">Pilih Dokumen (Bebas Ukuran > 2MB):</label>
                <input type="file" id="pilih_file" required>

                <input type="hidden" name="file_base64" id="file_base64">
            </div>

            <button type="button" id="btnSubmit" style="margin-top: 10px;">Simpan Dokumen</button>

            <span id="statusProses" style="color: blue; margin-left: 10px; display: none;">Mengonversi file...</span>
        </form>
    </div>
    <script>
        // Ambil semua elemen yang kita butuhin
        const pilihFile = document.getElementById('pilih_file');
        const inputHidden = document.getElementById('file_base64');
        const btnSubmit = document.getElementById('btnSubmit');
        const statusProses = document.getElementById('statusProses');
        const formDokumen = document.getElementById('formDokumen');

        // Ketika user memilih file, langsung konversi ke Base64 saat itu juga
        pilihFile.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            // Tampilkan loading & matikan tombol biar ga di-klik dwi-kali
            btnSubmit.disabled = true;
            statusProses.style.display = "inline";
            statusProses.innerText = "Mengonversi file ke Base64...";

            const reader = new FileReader();

            reader.onloadend = function() {
                // Pas konversi selesai, masukkan hasilnya ke input hidden
                inputHidden.value = reader.result;

                // Hidupkan tombol lagi
                btnSubmit.disabled = false;
                statusProses.innerText = "Konversi selesai! Siap kirim.";
                statusProses.style.color = "green";
            };

            reader.onerror = function() {
                alert("Gagal membaca file di browser!");
            };

            // Jalankan proses pembacaan file fisik
            reader.readAsDataURL(file);
        });

        // KUNCI UTAMA: Ketika tombol diklik, kita cek dulu isinya via JS
        btnSubmit.addEventListener('click', function() {
            if (!pilihFile.value) {
                alert("Pilih filenya dulu dong, bro!");
                return;
            }

            if (!inputHidden.value) {
                alert("Mohon tunggu sebentar, file masih diproses menjadi Base64...");
                return;
            }

            // Kalau semua validasi lolos, baru kita submit form-nya ke PHP secara paksa!
            formDokumen.submit();
        });
    </script>
</body>

</html>