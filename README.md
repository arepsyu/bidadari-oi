# BIDADARI OI - Bank Informasi Data Kabupaten Layak Anak Terintegrasi Ogan Ilir

Aplikasi berbasis **Laravel 10** untuk mengelola pengumpulan data dari OPD/organisasi
(user) yang dipantau oleh 1 akun Admin.

## Fitur
- Login (admin & user), tanpa registrasi mandiri
- Admin: CRUD akun user (tambah/edit/hapus/nonaktifkan)
- Admin: kelola "Jenis Data" secara dinamis (bisa tambah/edit/hapus kapan saja) -
  tiap jenis data punya tipe: Teks, Teks Panjang, Angka, Tanggal, atau Upload File
- Admin: Dashboard monitoring - progres kelengkapan data tiap user + detail per user
- User: hanya bisa mengisi/upload data sesuai jenis data yang dibuat admin
- Tema warna disesuaikan dengan logo BIDADARI OI (biru & hijau)

## Cara Install di Laragon

### 1. Buat project Laravel baru
Buka terminal (Laragon > Terminal atau cmder), arahkan ke folder `laragon/www`, lalu jalankan:

```
composer create-project laravel/laravel bidadari-oi "^10.0"
```

> PENTING: pakai versi Laravel 10 (`^10.0`) supaya struktur file cocok dengan yang di paket ini
> (Laravel 11/12 punya struktur folder yang berbeda).

### 2. Copy file dari paket ini
Salin **semua isi folder paket ini** (bidadari-oi-app) ke dalam folder project
`laragon/www/bidadari-oi` yang baru dibuat, **timpa/replace** file yang sudah ada
(composer.json JANGAN ditimpa, biarkan yang asli dari create-project).

File/folder yang perlu disalin & menimpa punya Laravel:
- `app/Http/Kernel.php`
- `app/Http/Middleware/EnsureUserIsAdmin.php` (baru)
- `app/Http/Controllers/**` (baru)
- `app/Models/**`
- `app/Providers/AppServiceProvider.php`
- `database/migrations/**` (tambahan, jangan hapus migration bawaan Laravel)
- `database/seeders/DatabaseSeeder.php`
- `routes/web.php`
- `resources/views/**`
- `public/images/logo.png`

### 3. Buat database
Buka HeidiSQL / phpMyAdmin dari Laragon, buat database baru misalnya `bidadari_oi`.

### 4. Konfigurasi .env
Copy `.env.example` menjadi `.env` (jika belum ada), lalu sesuaikan:

```
APP_NAME="BIDADARI OI"
APP_URL=http://bidadari-oi.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bidadari_oi
DB_USERNAME=root
DB_PASSWORD=

FILESYSTEM_DISK=public
```

### 5. Install dependency & generate key
Jalankan di terminal dalam folder project:

```
composer install
php artisan key:generate
```

### 6. Migrasi database + seed data awal
```
php artisan migrate --seed
```

Perintah ini otomatis membuat:
- Akun **Admin**: `admin@bidadarioi.test` / password: `password123`
- Akun contoh **User**: `user@bidadarioi.test` / password: `password123`
- 3 contoh Jenis Data (Nama Organisasi, Upload SK Organisasi, Upload Data Pendukung)

> Segera ganti password admin setelah login pertama kali lewat menu Kelola Akun User.

### 7. Buat symbolic link storage (WAJIB, untuk file upload)
```
php artisan storage:link
```

### 8. Jalankan aplikasi
Cara termudah lewat Laragon: klik kanan folder project di Laragon > Auto Create Virtual Hosts,
lalu akses `http://bidadari-oi.test` di browser.

Atau jalankan manual:
```
php artisan serve
```
lalu buka `http://127.0.0.1:8000`

## Struktur Data
- **users**: name, email, password, role (admin/user), organisasi, is_active
- **data_requirements**: judul, deskripsi, tipe (text/textarea/number/date/file), wajib, urutan
- **submissions**: user_id, data_requirement_id, value, file_path, file_original_name

## Alur Penggunaan
1. Admin login, buka **Kelola Jenis Data** untuk menambahkan daftar data yang wajib
   diisi seluruh user (misal: Nama Organisasi, Upload SK, dst).
2. Admin buka **Kelola Akun User** untuk membuat akun tiap OPD/organisasi.
3. User login, otomatis diarahkan ke halaman **Data Saya**, mengisi/upload data
   sesuai daftar yang dibuat admin.
4. Admin memantau progres semua user lewat **Dashboard Monitoring**, bisa klik
   "Lihat" untuk detail data per user (termasuk download file yang diupload).
