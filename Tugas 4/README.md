# Portal Jurnal Ilmiah Multi-Kolom

Prototipe antarmuka portal jurnal ilmiah fakultas dengan navigasi dinamis
dan tata letak tiga kolom yang responsif.

## Fitur

- **Navbar** dibangun murni dengan Flexbox, merapat vertikal pada layar ponsel.
- **Layout utama** memakai CSS Grid kustom:
  - Sidebar kiri: 200px (kategori & filter tahun)
  - Daftar artikel: kolom fleksibel di tengah
  - Kolom kanan: 220px (artikel populer & pengumuman)
- **Kartu artikel** disusun dalam micro-grid menggunakan
  `repeat(auto-fit, minmax(240px, 1fr))` sehingga jumlah kolom kartu
  menyesuaikan lebar layar secara otomatis.
- **Responsif**:
  - Desktop (≥992px): 3 kolom penuh.
  - Tablet (768–991px): kolom kanan turun ke bawah sebagai strip horizontal.
  - Tablet kecil (577–767px): kolom kanan disembunyikan.
  - Ponsel (≤576px): seluruh layout bertumpuk satu kolom.

## Struktur Berkas