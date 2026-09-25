# Tugas 5 — Sistem Manajemen Keuangan Sederhana

## Skenario

Prototipe modul pemrosesan transaksi keuangan berbasis web yang menangani
deposit dan penarikan (withdrawal). Sistem ini menerapkan tipe data ketat,
arsitektur OOP terenkapsulasi, validasi formulir, serta pertahanan terhadap
serangan XSS dan CSRF.

## Struktur Berkas

| Berkas | Deskripsi |
|---|---|
| `Transaction.php` | Kelas `Transaction` yang mengelola entitas transaksi dan logika perubahan saldo |
| `finance.php` | Entry point aplikasi — form transaksi, validasi, dan tampilan riwayat |

## Spesifikasi yang Diterapkan

1. **Kelas `Transaction`** dengan properti `id`, `type`, dan `amount` bersifat
   `private` dan `readonly`, dideklarasikan melalui *constructor property
   promotion*.
2. **Metode `process()`** menolak penarikan bila saldo dalam sesi tidak
   mencukupi (melempar `RuntimeException`), dan menambah saldo pada deposit.
3. **`finance.php`** menghubungkan kelas `Transaction` melalui `require_once`.
4. **Proteksi CSRF** — token disimpan di `$_SESSION`, dibuat dengan
   `random_bytes()`, divalidasi dengan `hash_equals()`, dan diregenerasi
   setelah setiap transaksi berhasil untuk mencegah *replay attack*.
5. **Validasi input:**
   - Jumlah transaksi divalidasi sebagai angka desimal positif (`is_numeric()`
     dan `> 0`).
   - Jenis transaksi dicocokkan melalui ekspresi `match` (whitelist
     `deposit` / `withdrawal`).
6. **Output aman** — saldo dan riwayat transaksi ditampilkan menggunakan
   `htmlspecialchars()` untuk mencegah XSS.