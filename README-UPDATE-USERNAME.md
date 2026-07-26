# Update: Login pakai Username (bukan Email lagi)

## Apa yang berubah
- Login sekarang pakai **Username** biasa, gak perlu format email/domain `@bidadarioi.test`
- Kolom `email` masih ada di database (bawaan Laravel), tapi cuma diisi otomatis di
  belakang layar (`username@bidadarioi.local`) — gak dipakai buat login, gak perlu diisi
  manual sama admin pas bikin akun baru
- Semua akun yang **udah ada sekarang** otomatis dapat username hasil migrasi:
  - Admin → `admin`
  - Kecamatan → `kecamatan.indralaya`, `kecamatan.tanjung-raja`, dst (persis kayak sebelum
    tanda `@`, cuma dibuang bagian domainnya)
  - Contoh OPD → `opd.contoh`
  - Contoh Desa → `desa.contoh`

Jadi kalau sebelumnya login pakai `kecamatan.indralaya@bidadarioi.test`, sekarang
tinggal `kecamatan.indralaya` doang. **Password gak berubah.**

## WAJIB: Migrate dulu
Migration ini nambah kolom `username` + otomatis isi dari email yang udah ada,
jadi HARUS dijalanin (pakai `migrate` biasa, BUKAN `migrate:fresh` — data yang
ada sekarang gak boleh ke-reset):

```
railway run php artisan migrate --force
```

## Form Tambah/Edit Akun
Field "Email" di form admin diganti jadi "Username" — bisa pakai huruf, angka, titik,
atau strip (gak perlu format email). Contoh: `dinkes`, `bappeda`, `desa.sukajadi`.
