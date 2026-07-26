# Update: Alur Verifikasi Admin, Riwayat Perubahan Data, Cari/Filter Pertanyaan

## 1. Alur Verifikasi Admin
- Tiap data yang diupload user sekarang punya status: **Menunggu Verifikasi** (kuning) →
  **Disetujui** (hijau) / **Ditolak** (merah, wajib disertai catatan alasan/revisi)
- Admin bisa Setujui/Tolak langsung dari halaman **Detail Akun** (tombol "Aksi" di tiap baris)
- Kalau ditolak, user bakal lihat catatan revisi dari admin langsung di dashboard mereka,
  dan begitu mereka upload ulang, status otomatis balik ke "Menunggu Verifikasi"
- Dashboard Monitoring nambah kartu statistik **"Menunggu Verifikasi"** + kolom jumlah
  pending per akun di tabel

## 2. Riwayat Perubahan Data
- Setiap kali user re-upload/ganti data yang udah ada, versi LAMA otomatis kesimpen
  (bukan ketimpa/ilang)
- Admin bisa lihat semua versi sebelumnya (link "Riwayat" di halaman Detail Akun,
  muncul kalau ada riwayatnya) — termasuk file lama masih bisa didownload

## 3. Cari & Filter Pertanyaan
- Halaman **Kelola Pertanyaan KLA** sekarang ada kotak pencarian teks (cari di judul
  pertanyaan & nama indikator), bisa dikombinasi sama filter Klaster yang udah ada

## ⚠️ WAJIB: Migrate database
Ada tabel & kolom baru (status verifikasi, riwayat), jadi HARUS migrate dulu sebelum
fitur ini bisa jalan:

```
php artisan migrate --force
```

(pakai `migrate` biasa, BUKAN `migrate:fresh` — biar data yang udah ada gak ke-reset)

Kalau di Railway, jalanin lewat:
```
railway run php artisan migrate --force
```
