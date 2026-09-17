# Tugas Mandiri Modul 3 — Pemutar Media Kuliah Responsif

## Deskripsi
Komponen kartu multimedia untuk platform e-learning kampus. Kartu responsif
memuat pemutar video (poster + dua format cadangan), pemutar audio (dua
format cadangan), dan foto dosen dengan picture art direction.

## Berkas
- 03-tugas-media-dan-css.html — markup dua kartu media (Python & SQL)
- 03-tugas-style.css — reset global, styling BEM, dan bukti spesifisitas
- assets/ — poster video, foto dosen (WebP art direction), file video/audio

## Spesifikasi yang dipenuhi
1. Reset global box-sizing: border-box pada seluruh elemen
2. picture dengan dua source WebP dibedakan lewat media (art direction foto dosen)
3. video dengan poster valid dan dua source format cadangan (webm, mp4)
4. Kartu memakai pola BEM dengan margin terpusat, bayangan, sudut melengkung, transisi hover
5. Spesifisitas CSS: modifier media-card--penting menimpa gaya deskripsi tanpa !important

## Cara membuka
Buka 03-tugas-media-dan-css.html di browser. Pastikan folder assets/
berada satu tingkat dengan berkas HTML dan CSS.