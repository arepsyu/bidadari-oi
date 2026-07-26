# Panduan Deploy BIDADARI OI ke InfinityFree

⚠️ Bacaan dulu: InfinityFree gak ada SSH/artisan/composer di server, jadi banyak
langkah manual. Panduan ini asumsinya buat **testing/demo ringan**, bukan produksi
beneran (baca lagi diskusi soal ini di chat kalau lupa kenapa).

## Perubahan kode yang udah disiapin
- Upload file sekarang gak pakai symlink `storage:link` lagi (langsung nulis ke
  `public/uploads/submissions/`), jadi aman di hosting yang gak dukung symlink.
- Ada route rahasia `/deploy/*` buat jalanin migrate/seed dari browser (karena gak
  ada SSH). **WAJIB DIHAPUS setelah selesai deploy** (lihat Langkah 8).
- Batas ukuran upload file bisa diatur lewat `.env` (`UPLOAD_MAX_KB`), defaultnya
  10240 (10MB) — turunin ke 8192 (8MB) kalau limit hosting cuma segitu.

## Langkah 1: Daftar & buat website di InfinityFree
1. Daftar akun di infinityfree.com
2. Buat website baru, pilih subdomain gratis (misal `bidadarioi.infinityfreeapp.com`)
   atau connect domain sendiri kalau punya
3. Tunggu sampai statusnya "Active" (biasanya beberapa menit)

## Langkah 2: Buat database MySQL
1. Di control panel (klik website kamu), buka **MySQL Databases**
2. Buat database baru, catat baik-baik: **hostname**, **nama database**,
   **username**, **password** (formatnya biasanya `epiz_xxxxxxx_bidadarioi` dst)

## Langkah 3: Siapin project di laptop
Di folder project lo (`bidadari-oi`), jalanin di Terminal Laragon:
```
composer install --optimize-autoloader --no-dev
```
Ini bikin folder `vendor` lebih ringkas (skip package development-only).

Edit `.env`, isi sesuai kredensial dari Langkah 2:
```
APP_NAME="BIDADARI OI"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://bidadarioi.infinityfreeapp.com

DB_CONNECTION=mysql
DB_HOST=sqlXXX.infinityfree.com
DB_PORT=3306
DB_DATABASE=epiz_xxxxxxx_bidadarioi
DB_USERNAME=epiz_xxxxxxx
DB_PASSWORD=passwordnya

UPLOAD_MAX_KB=8192
DEPLOY_TOKEN=bikin-random-panjang-yang-susah-ditebak-123xyz
```
> `DEPLOY_TOKEN` itu kayak password buat route `/deploy/*`, bikin yang panjang & acak.

Generate APP_KEY dulu kalau belum ada:
```
php artisan key:generate
```

## Langkah 4: Susun ulang folder buat upload
InfinityFree cuma punya 1 folder public (`htdocs`), jadi kita perlu pisahin:

```
htdocs/                        ← ini nanti isinya dari folder public/ project lo
├── index.php                  (perlu diedit, lihat bawah)
├── .htaccess
├── images/
├── uploads/
laravel-core/                  ← ini folder BARU, isinya SEMUA file/folder lain
├── app/
├── bootstrap/
├── config/
├── database/
├── resources/
├── routes/
├── storage/
├── vendor/
├── artisan
├── .env
└── composer.json
```

Caranya di laptop:
1. Bikin folder baru namanya `laravel-core`
2. Pindahin SEMUA isi folder project **kecuali folder `public`** ke `laravel-core`
   (jadi `app`, `bootstrap`, `config`, `database`, `resources`, `routes`, `storage`,
   `vendor`, `artisan`, `.env`, `composer.json`, dll)
3. Folder `public` yang tersisa, **isinya** (bukan foldernya) itu yang nanti jadi isi `htdocs`

## Langkah 5: Edit `index.php`
Buka `public/index.php` (yang nanti jadi `htdocs/index.php`), cari baris yang
require `vendor/autoload.php` dan `bootstrap/app.php`, ubah path-nya nunjuk ke
`laravel-core`:
```php
require __DIR__.'/../laravel-core/vendor/autoload.php';
// ...
$app = require_once __DIR__.'/../laravel-core/bootstrap/app.php';
```

## Langkah 6: Upload lewat FTP
1. Install **FileZilla** (gratis)
2. Ambil kredensial FTP dari control panel InfinityFree (menu **FTP Accounts**)
3. Connect, lalu upload:
   - Isi folder `public` (yang udah diedit index.php-nya) → ke `htdocs/`
   - Folder `laravel-core` → ke root (sejajar dengan `htdocs`, BUKAN di dalamnya)
4. Upload `vendor` paling lama (ribuan file kecil) — sabar, bisa 30-60 menit.
   Kalau sering putus, coba upload di jam sepi atau kompres jadi .zip dulu dan
   extract pakai File Manager bawaan InfinityFree.
5. Pastiin folder `htdocs/uploads/submissions` ada dan writable (klik kanan →
   permission → biasanya 755 udah cukup)

## Langkah 7: Jalanin migrate & seed
Buka browser, akses (ganti `xxx` dengan `DEPLOY_TOKEN` yang lo isi di `.env`):
```
http://bidadarioi.infinityfreeapp.com/deploy/migrate?token=xxx
http://bidadarioi.infinityfreeapp.com/deploy/seed?token=xxx
```
Kalau muncul output hijau tanpa error, database udah keisi (274 pertanyaan,
16 akun kecamatan, dll).

## Langkah 8: 🔒 WAJIB — kunci/hapus route deploy
Setelah sukses, JANGAN dibiarin route `/deploy/*` aktif. Pilih salah satu:
- **Paling aman**: hapus baris `DEPLOY_TOKEN=...` dari `.env` di server (lewat File
  Manager InfinityFree), jadi token-nya kosong dan semua akses ke `/deploy/*` otomatis
  ke-block (404)
- **Lebih aman lagi**: hapus file `app/Http/Controllers/DeployController.php` dan
  6 baris route `/deploy/...` di `routes/web.php`, lalu upload ulang 2 file itu

## Langkah 9: Testing
Buka websitenya, login pakai:
- Admin: `admin@bidadarioi.test` / `password123`
- Contoh Kecamatan: `kecamatan.indralaya@bidadarioi.test` / `kecamatan123`

## Troubleshooting umum
- **500 error blank putih** → cek `APP_DEBUG=true` sementara di `.env` buat lihat
  error detailnya, atau cek folder `storage/logs/laravel.log` lewat File Manager
- **Upload file gagal** → cek `UPLOAD_MAX_KB` di `.env` udah sesuai limit PHP
  hosting (cek di menu PHP Config InfinityFree)
- **Gambar/logo gak muncul** → pastiin folder `htdocs/images` ke-upload lengkap
