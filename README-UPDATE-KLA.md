# Update: Import Data KLA 2024 + Kategori Akun (OPD / Kecamatan / Desa)

## Apa yang berubah
- Struktur data lama (flat "Jenis Data") diganti total jadi **Klaster > Indikator > Pertanyaan**,
  sesuai struktur asli dokumen Rekap Penilaian Mandiri KLA 2024 yang lo upload.
- Total **274 pertanyaan**, **31 indikator**, **7 klaster** (Kelembagaan, Klaster I-V, Kelana Dekela)
  sudah otomatis ke-import lewat seeder.
- **32 OPD/instansi** otomatis dibuat sebagai master data, hasil normalisasi dari kolom "Sumber Data"
  di file Excel (banyak variasi penulisan kayak "Capil"/"CAPIL"/"Dukcapil" digabung jadi satu: "Disdukcapil").
- Akun sekarang punya **Kategori**: `OPD/Dinas`, `Kecamatan`, atau `Desa`.
  - Kategori **OPD** → di-assign ke OPD spesifik, cuma lihat pertanyaan yang di-assign ke OPD itu.
  - Kategori **Kecamatan** → otomatis lihat SEMUA pertanyaan yang ditandai "untuk semua Kecamatan".
  - Kategori **Desa** → otomatis lihat SEMUA pertanyaan yang ditandai "untuk semua Desa".
- **16 akun Kecamatan se-Ogan Ilir** udah dibuat otomatis lewat seeder.
- Menu admin baru: **Kelola Pertanyaan KLA** dan **Kelola Master OPD**.

## ⚠️ Yang perlu dicek manual
- Ada **1 pertanyaan** (Klaster Kelembagaan > Indikator 2 > Pertanyaan 2, soal Gugus Tugas KLA terlatih KHA)
  yang kolom "Sumber Data"-nya kosong di file Excel asli. Gua default-in ke **DPPPAPPKB**, tapi cek lagi
  kebenarannya lewat menu **Kelola Pertanyaan KLA**.
- Nama OPD hasil normalisasi ("Dinas PU", "Dinas PU Perkim", "Dinas PUPR") kemungkinan sebenarnya OPD yang
  sama tapi beda penulisan di sumbernya — silakan cek & rapikan lewat **Kelola Master OPD** kalau perlu digabung.

## Cara install
1. **Copy semua isi folder ini**, timpa ke `C:\laragon\www\bidadari-oi`
2. Clear cache & **reset database** (karena struktur tabel berubah total, paling aman migrate ulang dari nol —
   aman karena masih tahap demo, belum ada data asli):
```
php artisan config:clear
php artisan migrate:fresh --seed
```
3. Buka lagi websitenya. Login masih pakai akun admin yang sama:
   `admin@bidadarioi.test` / `password123`

## Akun baru buat testing
- **16 akun Kecamatan**: `kecamatan.indralaya@bidadarioi.test` s/d `kecamatan.sungai-pinang@bidadarioi.test`,
  password semua: `kecamatan123`
- **Contoh akun OPD**: `opd.contoh@bidadarioi.test` / `password123`
- **Contoh akun Desa**: `desa.contoh@bidadarioi.test` / `password123`

## Cara nambah akun Desa baru
Buka **Kelola Akun** > **Tambah Akun** > pilih Role "User" > Kategori "Desa" > isi nama desanya.
Otomatis langsung lihat semua pertanyaan yang ditandai "untuk Desa", tanpa perlu setting tambahan.
