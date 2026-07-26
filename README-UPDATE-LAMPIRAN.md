# Update: Pertanyaan dengan 2 Jawaban (Teks/Angka + Lampiran)

## Kenapa ini dibutuhin
Beberapa pertanyaan KLA emang butuh 2 hal sekaligus, contoh:
> "Berapa persentase kecamatan yang memiliki Forum Anak Kecamatan?
> (Lampirkan MATRIKS dan dokumen pendukung)"

Ini butuh jawaban ANGKA/TEKS (persentasenya) DITAMBAH upload dokumen (matriks + pendukung).

## Cara pakai
1. Buka **Kelola Pertanyaan KLA**, edit pertanyaan yang butuh 2 jawaban itu
2. Set **Tipe Input** sesuai jawaban utamanya (misal "Angka" buat persentase, atau
   "Teks Panjang" kalau jawabannya berupa narasi)
3. Centang **"Butuh lampiran dokumen tambahan juga"**
4. Simpan

Setelah itu, di halaman user bakal muncul 2 kolom sekaligus: kolom jawaban (sesuai
tipe yang dipilih) DAN kolom upload file — dua-duanya wajib diisi bareng buat submit.

## Yang perlu di-migrate
Ada kolom baru (`wajib_lampiran`), jalanin migrate biasa (BUKAN migrate:fresh):
```
railway run php artisan migrate --force
```

## Catatan
- Kalau Tipe Input pertanyaan itu udah "Upload File", opsi ini gak relevan (karena
  udah upload file aja, gak butuh checkbox tambahan)
- Dashboard admin (Detail Akun), Export Excel & PDF semua udah disesuaikan buat
  nampilin dua-duanya (isi teks + link file) kalau ada
