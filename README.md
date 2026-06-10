# 📦 MyApp — Dokumentasi Proyek PHP Native

> Framework mini berbasis PHP Native dengan arsitektur MVC, koneksi Cloud Database, dan sistem migrasi otomatis.

---

## 📋 Daftar Isi

- [Gambaran Umum](#gambaran-umum)
- [Prasyarat](#prasyarat)
- [Instalasi & Cara Mulai](#instalasi--cara-mulai)
- [Struktur Folder Lengkap](#struktur-folder-lengkap)
- [Penjelasan Tiap Folder & File](#penjelasan-tiap-folder--file)
  - [📁 app/](#-app)
  - [📁 config/](#-config)
  - [📁 core/](#-core)
  - [📁 database/](#-database)
  - [📁 public/](#-public)
  - [📁 routes/](#-routes)
  - [📁 storage/](#-storage)
  - [📄 File Root](#-file-root)
- [Sistem Migrasi](#sistem-migrasi)
- [Alur Request (Flow)](#alur-request-flow)
- [Panduan Pengembangan](#panduan-pengembangan)
- [Cloud Database yang Didukung](#cloud-database-yang-didukung)

---

## Gambaran Umum

MyApp adalah kerangka kerja PHP Native yang dirancang agar **mudah dipahami pemula** namun tetap terstruktur seperti framework modern. Dibangun tanpa dependensi eksternal (tidak perlu Composer), semua komponen ditulis dari nol.

**Fitur utama:**

- ✅ Arsitektur **MVC** (Model–View–Controller)
- ✅ Koneksi ke **Cloud Database** via PDO
- ✅ Sistem **migrasi otomatis** dengan perintah CLI
- ✅ Router sederhana dengan dukungan parameter URL
- ✅ Konfigurasi aman via file `.env`

---

## Prasyarat

| Kebutuhan  | Versi Minimum               |
| ---------- | --------------------------- |
| PHP        | 8.1+                        |
| MySQL      | 5.7+ / 8.0+                 |
| Web Server | Apache / Nginx / PHP CLI    |

---

## Instalasi & Cara Mulai

### 1. Clone atau download proyek

```bash
git clone https://github.com/username/my-app.git
cd my-app
```

### 2. Salin dan isi file `.env`

```bash
cp .env.example .env
```

Buka `.env` lalu isi dengan kredensial cloud database kamu:

```env
DB_HOST=your-cloud-host.db.example.com
DB_PORT=3306
DB_NAME=myapp_db
DB_USER=cloud_user
DB_PASS=password_rahasia
```

### 3. Jalankan migrasi ke cloud

```bash
php migrate.php
```

### 4. (Opsional) Isi data awal

```bash
php migrate.php seed
```

### 5. Jalankan server lokal

```bash
php -S localhost:8000 -t public
```

Buka browser di `http://localhost:8000` 🎉

---

## Struktur Folder Lengkap

```
my-app/
│
├── app/                            # Logic utama aplikasi (MVC)
│   ├── controllers/                # Menerima request, memanggil model & view
│   │   ├── HomeController.php
│   │   └── UserController.php
│   ├── models/                     # Berinteraksi langsung dengan database
│   │   └── User.php
│   └── views/                      # Tampilan HTML yang dikirim ke browser
│       ├── layouts/
│       │   └── main.php            # Template utama (wrapper HTML)
│       ├── home.php
│       └── users.php
│
├── config/                         # Konfigurasi aplikasi
│   ├── database.php                # Konfigurasi koneksi cloud DB
│   └── app.php                     # Konfigurasi umum
│
├── core/                           # Engine framework mini
│   ├── Database.php                # Wrapper PDO (singleton)
│   ├── Router.php                  # Mengarahkan URL ke controller
│   ├── Controller.php              # Base class semua controller
│   └── Model.php                   # Base class semua model
│
├── database/                       # Semua keperluan database
│   ├── migrations/                 # File migrasi berurutan
│   │   ├── 001_create_users_table.php
│   │   ├── 002_create_posts_table.php
│   │   └── 003_add_phone_to_users.php
│   ├── seeders/                    # Data awal untuk pengembangan
│   │   └── UserSeeder.php
│   └── MigrationRunner.php         # Engine yang menjalankan migrasi
│
├── public/                         # Satu-satunya folder yang bisa diakses publik
│   ├── index.php                   # Entry point — semua request masuk sini
│   ├── css/                        # File stylesheet
│   ├── js/                         # File JavaScript
│   └── img/                        # Gambar statis
│
├── routes/
│   └── web.php                     # Daftar semua route URL aplikasi
│
├── storage/
│   ├── logs/                       # Log error & aktivitas aplikasi
│   └── uploads/                    # File yang diupload pengguna
│
├── .env                            # ⚠️ Kredensial rahasia (jangan di-commit!)
├── .env.example                    # Template .env untuk anggota tim
├── migrate.php                     # CLI tool untuk menjalankan migrasi
└── README.md                       # Dokumentasi ini
```

---

## Penjelasan Tiap Folder & File

---

### 📁 `app/`

Folder inti yang berisi seluruh logika aplikasi menggunakan pola **MVC**.

---

#### 📁 `app/controllers/`

**Apa itu Controller?**
Controller adalah "otak" yang menerima permintaan dari pengguna, mengambil data dari Model, lalu mengirimkan hasilnya ke View.

| File | Tanggung Jawab |
|------|----------------|
| `HomeController.php` | Menangani halaman utama (`/`) |
| `UserController.php` | Menangani CRUD pengguna (`/users`) |

**Cara membuat controller baru:**

```php
// app/controllers/ProductController.php
class ProductController extends Controller {

    public function index(): void {
        // 1. Ambil data dari model
        $products = (new Product())->all();

        // 2. Kirim ke view
        $this->view('products', ['products' => $products]);
    }
}
```

Lalu daftarkan di `routes/web.php`:

```php
$router->get('/products', 'ProductController@index');
```

---

#### 📁 `app/models/`

**Apa itu Model?**
Model adalah lapisan yang bertugas berkomunikasi dengan database. Setiap tabel di database idealnya punya satu Model.

| File | Tabel DB | Method Tambahan |
|------|----------|-----------------|
| `User.php` | `users` | `findByEmail()`, `latest()` |

Semua model mewarisi dari `core/Model.php` yang sudah menyediakan operasi dasar:

| Method | Fungsi |
|--------|--------|
| `all()` | Ambil semua baris |
| `find($id)` | Cari berdasarkan ID |
| `create($data)` | Simpan data baru |
| `update($id, $data)` | Perbarui data |
| `delete($id)` | Hapus data |

**Cara membuat model baru:**

```php
// app/models/Product.php
class Product extends Model {
    protected string $table = 'products'; // nama tabel di DB

    // Tambahkan method khusus di sini
    public function findByCategory(string $category): array {
        $stmt = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE category = ?"
        );
        $stmt->execute([$category]);
        return $stmt->fetchAll();
    }
}
```

---

#### 📁 `app/views/`

**Apa itu View?**
View adalah file PHP yang menghasilkan tampilan HTML yang dilihat pengguna di browser.

| File | Halaman |
|------|---------|
| `layouts/main.php` | Kerangka HTML utama (nav, footer, dll) |
| `home.php` | Konten halaman utama |
| `users.php` | Tabel daftar pengguna + form |

**Cara kerja layout:**

`main.php` bertindak sebagai wrapper. Variabel `$content` di dalamnya diisi otomatis oleh controller dengan hasil render view yang dipanggil.

```
Controller::view('home', $data)
    │
    ├── Render app/views/home.php  →  hasilnya jadi $content
    └── Render app/views/layouts/main.php  →  $content disisipkan di sini
```

---

### 📁 `config/`

Folder konfigurasi aplikasi. Semua nilai sensitif dibaca dari file `.env`.

---

#### 📄 `config/database.php`

Membaca kredensial dari `.env` dan mengembalikan array konfigurasi PDO.

```php
// Nilai yang dikembalikan:
[
    'host'     => 'cloud-host.example.com',
    'port'     => '3306',
    'dbname'   => 'myapp_db',
    'username' => 'cloud_user',
    'password' => '***',
    'charset'  => 'utf8mb4',
    'options'  => [ ... opsi PDO ... ]
]
```

> ⚠️ **Jangan pernah** menulis kredensial langsung di file ini. Selalu gunakan `.env`.

---

#### 📄 `config/app.php`

Konfigurasi umum seperti nama aplikasi, mode environment, dan URL dasar.

---

### 📁 `core/`

Mesin internal framework. File-file di sini **tidak perlu diubah** kecuali ingin memperluas kemampuan framework.

---

#### 📄 `core/Database.php` — Singleton PDO

Memastikan hanya **satu koneksi** database yang dibuat selama satu request, lalu dipakai bersama oleh semua model.

```
Pertama kali dipanggil  →  Buat koneksi PDO baru
Dipanggil lagi          →  Kembalikan koneksi yang sama
```

Cara menggunakan di mana saja:

```php
$pdo = Database::getInstance();
```

---

#### 📄 `core/Router.php` — Routing

Mencocokkan URL yang diminta browser dengan daftar route di `routes/web.php`, lalu memanggil controller yang sesuai.

**Dukungan parameter URL:**

```php
// Definisi route
$router->get('/users/:id', 'UserController@show');

// URL: /users/42
// → UserController::show('42') dipanggil otomatis
```

---

#### 📄 `core/Controller.php` — Base Controller

Menyediakan method pembantu yang bisa dipakai semua controller:

| Method | Contoh Penggunaan |
|--------|-------------------|
| `view($name, $data)` | `$this->view('home', ['title' => 'Home'])` |
| `redirect($url)` | `$this->redirect('/users')` |
| `input($key)` | `$this->input('email')` — ambil dari POST/GET |

---

#### 📄 `core/Model.php` — Base Model

Menyediakan operasi CRUD dasar yang diwarisi semua model. Menggunakan **prepared statements** untuk keamanan dari SQL Injection.

---

### 📁 `database/`

Semua keperluan manajemen struktur database.

---

#### 📁 `database/migrations/`

Berisi file-file migrasi yang mendefinisikan perubahan struktur database secara berurutan.

**Aturan penamaan:**

```
[nomor urut]_[deskripsi_singkat].php

001_create_users_table.php
002_create_posts_table.php
003_add_phone_to_users.php   ← contoh update kolom
```

**Struktur setiap file migrasi:**

```php
<?php
return new class {

    // Dijalankan saat: php migrate.php
    public function up(PDO $db): void {
        $db->exec("CREATE TABLE IF NOT EXISTS ...");
    }

    // Dijalankan saat: php migrate.php rollback
    public function down(PDO $db): void {
        $db->exec("DROP TABLE IF EXISTS ...");
    }
};
```

> 💡 Setiap kali mengubah struktur database, **buat file migrasi baru** (jangan edit yang lama). Ini menjaga riwayat perubahan tetap aman.

---

#### 📄 `database/MigrationRunner.php` — Engine Migrasi

Otak dari sistem migrasi. Cara kerjanya:

```
1. Cek tabel `migrations` di cloud DB (buat jika belum ada)
2. Baca semua file di folder migrations/ (diurutkan by nama)
3. Bandingkan dengan daftar di tabel `migrations`
4. Jalankan HANYA file yang belum pernah dijalankan
5. Catat file yang berhasil ke tabel `migrations`
```

Karena menggunakan **database transaction**, jika satu migrasi gagal, perubahannya otomatis dibatalkan dan migrasi berikutnya tidak akan dijalankan.

---

#### 📁 `database/seeders/`

Berisi data awal untuk keperluan pengembangan atau demo.

```bash
php migrate.php seed   # jalankan semua seeder
```

> ⚠️ Seeder sebaiknya hanya dijalankan di environment **development**, bukan production.

---

### 📁 `public/`

Satu-satunya folder yang **boleh diakses langsung** oleh browser/web server. File-file di luar folder ini tidak dapat diakses dari URL.

---

#### 📄 `public/index.php` — Entry Point

Semua request HTTP masuk melalui file ini. Tugasnya:

```
1. Definisikan ROOT_PATH
2. Load konfigurasi (.env terbaca di sini)
3. Load semua core class
4. Inisialisasi Router
5. Load routes/web.php
6. Jalankan router → teruskan ke controller yang tepat
```

**Konfigurasi web server:**

Untuk Apache, buat file `.htaccess` di folder `public/`:

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]
```

Untuk Nginx:

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

---

#### 📁 `public/css/`, `public/js/`, `public/img/`

Folder untuk aset statis yang bisa diakses langsung via URL:

```
/css/style.css   →  public/css/style.css
/js/app.js       →  public/js/app.js
/img/logo.png    →  public/img/logo.png
```

---

### 📁 `routes/`

#### 📄 `routes/web.php`

Pusat pendaftaran seluruh URL aplikasi. Semua route yang ingin diakses **harus didaftarkan** di sini.

```php
// Format:
$router->get('/url',        'NamaController@namaMethod');
$router->post('/url',       'NamaController@namaMethod');

// Dengan parameter:
$router->get('/users/:id',  'UserController@show');
```

| Method HTTP | Digunakan Untuk |
|-------------|-----------------|
| `GET`       | Menampilkan halaman / mengambil data |
| `POST`      | Menyimpan, mengubah, atau menghapus data |

---

### 📁 `storage/`

Folder untuk menyimpan file yang dihasilkan oleh aplikasi saat berjalan.

| Sub-folder | Isi |
|------------|-----|
| `storage/logs/` | File log error dan aktivitas aplikasi |
| `storage/uploads/` | File yang diunggah oleh pengguna |

> ⚠️ Folder ini **tidak boleh** diakses langsung dari URL. Pastikan web server tidak meng-expose folder ini.

---

### 📄 File Root

| File | Fungsi |
|------|--------|
| `.env` | Menyimpan semua variabel rahasia (DB credentials, API keys). **Jangan di-commit ke Git!** |
| `.env.example` | Template `.env` kosong yang aman di-commit. Digunakan anggota tim sebagai referensi. |
| `migrate.php` | CLI tool untuk mengelola migrasi database. |
| `README.md` | Dokumentasi ini. |

---

## Sistem Migrasi

### Perintah CLI yang tersedia

```bash
# Jalankan semua migrasi yang belum dijalankan
php migrate.php

# Rollback (batalkan) migrasi terakhir
php migrate.php rollback

# Isi data awal (seeder)
php migrate.php seed

# Reset total: hapus semua tabel, lalu migrate ulang dari awal
php migrate.php fresh
```

### Cara menambah perubahan database baru

**Langkah 1** — Buat file migrasi baru (nomor harus lanjut dari yang terakhir):

```php
// database/migrations/004_create_categories_table.php
<?php
return new class {
    public function up(PDO $db): void {
        $db->exec("
            CREATE TABLE IF NOT EXISTS categories (
                id   INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL
            )
        ");
    }

    public function down(PDO $db): void {
        $db->exec("DROP TABLE IF EXISTS categories");
    }
};
```

**Langkah 2** — Jalankan migrasi, otomatis terupdate ke cloud:

```bash
php migrate.php
```

### Tabel `migrations` di database

MigrationRunner secara otomatis membuat tabel ini di cloud database untuk melacak riwayat:

```
+----+----------------------------------+---------------------+
| id | filename                         | ran_at              |
+----+----------------------------------+---------------------+
|  1 | 001_create_users_table.php       | 2024-01-15 10:00:00 |
|  2 | 002_create_posts_table.php       | 2024-01-15 10:00:01 |
|  3 | 003_add_phone_to_users.php       | 2024-01-20 14:30:00 |
+----+----------------------------------+---------------------+
```

---

## Alur Request (Flow)

```
Browser                   public/
  │                       index.php
  │  GET /users    ──────────────────►  Load config, core, routes
  │                                              │
  │                                         Router.php
  │                                    (cocokkan URL + method)
  │                                              │
  │                                    UserController.php
  │                                       index() method
  │                                              │
  │                                         User.php
  │                                          all()
  │                                              │
  │                                       Database.php
  │                                     (PDO ke Cloud DB)
  │                                              │
  │                                    Cloud Database ☁️
  │                                              │
  │                                    users.php (view)
  │  ◄──────────────────────────────  layouts/main.php
  │         HTML Response
```

---

## Panduan Pengembangan

### Menambah Fitur Baru (Contoh: Produk)

| Langkah | Aksi | Lokasi File |
|---------|------|-------------|
| 1 | Buat migrasi | `database/migrations/004_create_products_table.php` |
| 2 | Buat model | `app/models/Product.php` |
| 3 | Buat controller | `app/controllers/ProductController.php` |
| 4 | Buat view | `app/views/products.php` |
| 5 | Daftarkan route | `routes/web.php` |
| 6 | Jalankan migrasi | `php migrate.php` |

---

## Cloud Database yang Didukung

Aplikasi ini kompatibel dengan semua layanan MySQL/MariaDB cloud:

| Layanan | Keterangan |
|---------|------------|
| **PlanetScale** | MySQL serverless, gratis tier tersedia |
| **Railway** | Deploy cepat, cocok untuk development |
| **Supabase** | PostgreSQL & MySQL, gratis tier tersedia |
| **TiDB Cloud** | MySQL-compatible, scalable |
| **Aiven** | Multi-cloud, berbagai engine DB |
| **Amazon RDS** | Enterprise, sangat scalable |
| **Google Cloud SQL** | Terintegrasi ekosistem Google |

Cukup isi nilai `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, dan `DB_PASS` di file `.env` sesuai kredensial dari layanan yang dipilih.

---

*Dokumentasi ini dibuat untuk MyApp PHP Native Framework Mini.*