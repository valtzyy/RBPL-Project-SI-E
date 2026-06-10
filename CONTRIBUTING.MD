# Git Commit Convention Guideline

Panduan ringkas untuk menjaga konsistensi dan keterbacaan pesan commit di seluruh proyek.

---

## Struktur Commit Header

```bash
<type>(<scope>): <subject>
```

### Contoh

```bash
feat(auth): add login with Google
```

---

## 🔖 Tipe Commit

Gunakan tipe berikut sesuai konteks perubahan:

| Type         | Deskripsi                                                                           |
| ------------ | ----------------------------------------------------------------------------------- |
| **feat**     | Penambahan fitur baru                                                               |
| **fix**      | Perbaikan bug                                                                       |
| **docs**     | Perubahan dokumentasi saja                                                          |
| **style**    | Perubahan tampilan atau format kode tanpa mengubah logika (misal: indentasi, spasi) |
| **refactor** | Perubahan kode tanpa menambah fitur atau memperbaiki bug                            |
| **perf**     | Peningkatan performa                                                                |
| **test**     | Penambahan atau modifikasi tes                                                      |
| **build**    | Perubahan pada sistem build atau dependensi eksternal                               |
| **ci**       | Perubahan pada konfigurasi CI/CD                                                    |
| **chore**    | Perubahan kecil yang tidak mempengaruhi kode sumber aplikasi                        |
| **revert**   | Membatalkan commit sebelumnya                                                       |

---

## Scope (Opsional)

`<scope>` digunakan untuk menunjukkan **bagian atau modul spesifik** dari proyek yang terpengaruh oleh commit tersebut. Scope membantu orang lain memahami area yang berubah tanpa harus membaca seluruh diff.

Beberapa panduan dalam menentukan scope:

| Jenis Scope           | Contoh                                    | Deskripsi                                                            |
| --------------------- | ----------------------------------------- | -------------------------------------------------------------------- |
| **Fitur/Modul**       | `auth`, `payment`, `profile`, `dashboard` | Digunakan untuk menandai modul utama aplikasi yang berubah           |
| **Layer Aplikasi**    | `ui`, `api`, `db`, `core`, `utils`        | Menunjukkan lapisan logika atau komponen aplikasi yang terpengaruh   |
| **Konfigurasi/Build** | `config`, `deps`, `env`, `docker`         | Digunakan saat perubahan terkait pengaturan proyek atau dependensi   |
| **Dokumentasi/Test**  | `readme`, `test`, `spec`, `docs`          | Digunakan untuk area non-produksi seperti dokumentasi atau pengujian |

### Tips Menentukan Scope

* Gunakan satu kata pendek dan konsisten antar tim
* Hindari penggunaan path file (misal `src/components/Button`), cukup gunakan nama modulnya (`button`)
* Jika commit memengaruhi banyak area, gunakan scope umum seperti `core` atau abaikan scope sama sekali

Contoh:

```bash
fix(api): handle null response on user fetch
refactor(ui): simplify dashboard component logic
chore(config): update eslint rules
```

---

## Subject

* Gunakan kalimat singkat (maksimal 72 karakter)
* Gunakan gaya **imperative**, misal: *add*, *update*, *remove*, bukan *added* atau *removed*
* Jangan gunakan huruf kapital di awal kecuali untuk nama proper
* Jangan akhiri dengan titik