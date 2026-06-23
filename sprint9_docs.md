# Dokumentasi Teknis Sprint 9: Kredit & Leasing (Approval & Uang Muka)

Dokumentasi ini dibuat untuk mempermudah anggota tim lain dan dosen dalam memahami arsitektur, struktur kode, aliran data, serta keamanan yang telah dibangun untuk modul **Kredit & Leasing** pada aplikasi ini.

Seluruh fitur telah selesai diimplementasikan menggunakan arsitektur **MVC (Model-View-Controller)** berbasis **PDO Native**, serta terhubung secara aman ke **Database Cloud Aiven MySQL**.

---

## 📂 1. Struktur Folder & Berkas Sprint 9

Seluruh kode terorganisir dengan rapi dalam folder MVC sebagai berikut:

```text
RBPL-Project-SI-E/
│
├── app/
│   ├── controllers/
│   │   ├── WebhookApprovalController.php   # Menangani API persetujuan leasing (pihak ketiga)
│   │   ├── VerifikasiDpController.php      # Menangani pencatatan pelunasan DP oleh Finance
│   │   ├── DebugController.php             # [TEST ONLY] Merender halaman pengujian (Testing Panel)
│   │   └── DebugResetController.php        # [TEST ONLY] Mereset status data uji coba di database
│   │
│   ├── models/
│   │   ├── CreditApplication.php           # Model untuk tabel credit_applications
│   │   ├── CreditDecision.php              # Model untuk tabel credit_decisions
│   │   ├── DownPayment.php                 # Model untuk tabel down_payments
│   │   └── SalesTransaction.php            # Model untuk tabel sales_transactions
│   │
│   └── views/
│       └── debug_panel.php                 # View uji coba plain HTML untuk tim FE & QA
│
└── routes/
    └── web.php                             # Registrasi rute URL aplikasi
```

---

## ⚡ 2. Alur Kerja Rute (Routes)

Rute-rute berikut telah didaftarkan pada file `routes/web.php` dan terhubung otomatis ke Controller yang relevan:

| URL Endpoint | Method | Controller & Method | Deskripsi |
| :--- | :---: | :--- | :--- |
| `/webhook-approval` | `POST` | `WebhookApprovalController@process` | Memproses respon persetujuan kredit dari leasing luar |
| `/verifikasi-dp` | `POST` | `VerifikasiDpController@process` | Memverifikasi pembayaran uang muka oleh Finance |
| `/debug-panel` | `GET` | `DebugController@index` | [Dev Only] Membuka panel UI testing |
| `/debug-reset` | `POST` | `DebugResetController@process` | [Dev Only] Mereset data pengujian di database |

---

## ⚙️ 3. Deskripsi Fungsionalitas File

### A. Controllers (Logika Bisnis)
1. **`WebhookApprovalController.php`**
   * Menerima payload JSON dari leasing luar berisi `id_kredit`, `status_approval` ('disetujui'/'ditolak'), dan `catatan`.
   * Mengupdate status kelayakan kredit di database ke status `'approved'` atau `'rejected'`.
   * Mencatat data riwayat keputusan ke tabel `credit_decisions`.
   * Memanggil logika **PBI-9.6** (jika kredit disetujui, periksa apakah DP sudah lunas. Jika ya, ubah status transaksi utama menjadi `'lunas'`).
2. **`VerifikasiDpController.php`**
   * Menerima input data `id_kredit`, `nominal_dibayar`, dan `verified_by` (ID staf Finance).
   * Mencatat tanggal dan nominal pelunasan ke tabel `down_payments`.
   * Memanggil logika **PBI-9.6** (jika DP dilunasi, periksa apakah status kredit sudah disetujui. Jika ya, ubah status transaksi utama menjadi `'lunas'`).

### B. Models (Manipulasi Database)
* Seluruh model mewarisi kelas dasar `Model.php` yang mengemas kueri standar PDO (`find`, `create`, `update`, `delete`).
* **`CreditApplication`** memiliki method custom `findWithTransactionStatus` untuk menggabungkan data status transaksi penjualan utama secara dinamis menggunakan perintah SQL `LEFT JOIN`.

### C. View Uji Coba (`debug_panel.php`)
* Didesain dengan **Plain HTML** tanpa gaya CSS berlebihan agar tim Frontend (FE) dapat mendesain ulang secara modular.
* Menyediakan form interaktif berbasis Javascript `Fetch API` untuk menguji `/webhook-approval` dan `/verifikasi-dp`, serta tombol reset database `/debug-reset`.

---

## 🔄 4. Logika Alur Sekuensial PBI-9.6 (Gerbang Serah Terima)

Modul delivery/serah terima hanya mendeteksi unit mobil yang siap dikirim apabila status transaksi penjualan di tabel `sales_transactions` telah bernilai `'lunas'`.

Alur proses modul ini dibuat secara berurutan (sekuensial) dengan tahapan berikut:

### Langkah 1: Persetujuan Kredit oleh Pihak Leasing (`/webhook-approval`)
1. Pihak leasing mengirimkan keputusan persetujuan kredit.
2. Status kredit pada tabel `credit_applications` diupdate menjadi `'approved'` (atau `'rejected'`).
3. Pada tahap ini, status transaksi utama (`sales_transactions.status`) tetap `'process'` karena pembayaran uang muka (DP) belum diverifikasi.

### Langkah 2: Verifikasi Pelunasan Uang Muka oleh Finance (`/verifikasi-dp`)
1. Staf Finance mengunggah bukti/nominal pembayaran uang muka (DP).
2. **Validasi Alur**: Sistem secara otomatis memeriksa status pengajuan kredit terkait:
   - **Jika kredit belum disetujui** (status bukan `'approved'`), permintaan verifikasi DP ditolak dengan pesan error (mencegah pembayaran DP untuk kredit yang ditolak/tertunda).
   - **Jika kredit sudah disetujui** (status `'approved'`), data pelunasan DP dicatat di tabel `down_payments`.
3. Setelah DP diverifikasi dan kredit telah disetujui, sistem otomatis mengupdate status transaksi utama (`sales_transactions`) menjadi **`'lunas'`** sehingga unit mobil siap masuk ke antrean serah terima.

---

## 🔒 5. Fitur Keamanan yang Diterapkan

1. **SQL Injection Prevention:**
   Seluruh kueri SQL di dalam model menggunakan **PDO Prepared Statements** dengan *named placeholders* (contoh: `:id_kredit`, `:status`). Ini menjamin input data dari user disaring secara aman sebelum dieksekusi oleh MySQL.
2. **Database Transactions:**
   Untuk mencegah terjadinya kegagalan parsial (misal data riwayat keputusan tersimpan tetapi status pengajuan gagal diupdate), controller dibungkus dalam blok transaksi:
   ```php
   $db->beginTransaction();
   // ... kueri update 1
   // ... kueri insert 2
   $db->commit();
   ```
   Jika salah satu kueri gagal, perintah `rollBack()` akan otomatis mengembalikan database ke status semula secara aman.
