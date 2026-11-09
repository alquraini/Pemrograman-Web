# 📚 Perpustakaan Mini (Daftar Buku)

Aplikasi web sederhana untuk mengelola data buku, dibangun dengan **Laravel**, **Blade**, dan **Tailwind CSS**. Mendukung operasi CRUD lengkap, pencarian, validasi form, dan pembagian halaman (pagination).

Dibuat sebagai tugas mata kuliah **Pemrograman Web**.

## Fitur

- Menampilkan daftar buku dengan pagination (5 buku per halaman)
- Mencari buku berdasarkan judul atau penulis
- Menambah buku baru dengan validasi form
- Melihat detail buku
- Mengubah data buku
- Menghapus buku (dengan konfirmasi)
- Pesan sukses (flash message) setelah setiap aksi
- Data contoh lewat seeder

## Teknologi

| Teknologi | Keterangan |
|---|---|
| Laravel 12 | Framework PHP (pola MVC) |
| Blade | Template engine untuk tampilan |
| Tailwind CSS 4 | Styling dengan utility class |
| Vite | Build tool untuk aset CSS/JS |
| SQLite | Database bawaan Laravel |

## Prasyarat

- PHP 8.2 atau lebih baru
- Composer
- Node.js dan npm
- Git

## Instalasi

1. Clone repository:

   ```bash
   git clone https://github.com/USERNAME/NAMA-REPO.git
   cd NAMA-REPO/daftar-buku
   ```

2. Pasang dependensi PHP dan Node.js:

   ```bash
   composer install
   npm install
   ```

3. Salin file environment dan buat application key:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Di Windows PowerShell, ganti `cp` dengan `copy`:

   ```powershell
   copy .env.example .env
   ```

4. Pastikan `.env` memakai SQLite:

   ```env
   DB_CONNECTION=sqlite
   ```

   Jika file `database/database.sqlite` belum ada, buat file kosong dengan nama tersebut.

5. Jalankan migration dan seeder:

   ```bash
   php artisan migrate
   php artisan db:seed --class=BukuSeeder
   ```

## Menjalankan Aplikasi

Buka dua terminal:

```bash
# Terminal 1: server Laravel
php artisan serve

# Terminal 2: Vite + Tailwind
npm run dev
```

Lalu buka <http://127.0.0.1:8000> di browser. Alamat `/` otomatis diarahkan ke `/buku`.

Alternatif satu terminal: `composer run dev`.

## Struktur Penting

```
daftar-buku/
├── app/
│   ├── Http/Controllers/BukuController.php   # Logika CRUD dan pencarian
│   └── Models/Buku.php                       # Model Buku
├── database/
│   ├── migrations/…_create_buku_table.php    # Struktur tabel buku
│   └── seeders/BukuSeeder.php                # Data contoh
├── resources/views/
│   ├── layouts/app.blade.php                 # Kerangka halaman
│   └── buku/
│       ├── index.blade.php                   # Daftar buku
│       ├── create.blade.php                  # Form tambah
│       ├── edit.blade.php                    # Form ubah
│       ├── show.blade.php                    # Detail buku
│       └── _form.blade.php                   # Isian form bersama
└── routes/web.php                            # Daftar route
```

## Route

| Method | URL | Nama Route | Fungsi |
|---|---|---|---|
| GET | `/buku` | `buku.index` | Daftar buku |
| GET | `/buku/create` | `buku.create` | Form tambah |
| POST | `/buku` | `buku.store` | Simpan buku baru |
| GET | `/buku/{buku}` | `buku.show` | Detail buku |
| GET | `/buku/{buku}/edit` | `buku.edit` | Form ubah |
| PUT/PATCH | `/buku/{buku}` | `buku.update` | Simpan perubahan |
| DELETE | `/buku/{buku}` | `buku.destroy` | Hapus buku |

## Struktur Tabel `buku`

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | bigint | Primary key |
| `judul` | string | Wajib diisi |
| `penulis` | string | Wajib diisi |
| `tahun_terbit` | smallint | Wajib, minimal 1000 dan tidak melebihi tahun ini |
| `sinopsis` | text | Opsional |
| `created_at`, `updated_at` | timestamp | Diisi otomatis |

## Perintah Berguna

| Perintah | Fungsi |
|---|---|
| `php artisan route:list --path=buku` | Melihat semua route buku |
| `php artisan migrate:fresh --seed` | Mengulang database dari nol beserta data contoh |
| `php artisan migrate:status` | Mengecek status migration |
| `npm run build` | Membuat CSS/JS versi produksi |

> **Catatan:** `migrate:fresh` menghapus seluruh data. Gunakan hanya saat pengembangan. Agar `--seed` menjalankan `BukuSeeder`, tambahkan `$this->call(BukuSeeder::class);` di `DatabaseSeeder.php`.
