<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class KecamatanSeeder extends Seeder
{
    public function run(): void
    {
        $kecamatanList = [
            'Indralaya',
            'Indralaya Utara',
            'Indralaya Selatan',
            'Tanjung Raja',
            'Tanjung Batu',
            'Pemulutan',
            'Pemulutan Selatan',
            'Pemulutan Barat',
            'Muara Kuang',
            'Rambang Kuang',
            'Lubuk Keliat',
            'Payaraman',
            'Rantau Alai',
            'Rantau Panjang',
            'Kandis',
            'Sungai Pinang',
        ];

        foreach ($kecamatanList as $nama) {
            $slug = Str::slug($nama);
            $username = "kecamatan.{$slug}";

            User::firstOrCreate(
                ['username' => $username],
                [
                    'name' => 'Kecamatan ' . $nama,
                    'email' => "{$username}@bidadarioi.local",
                    'password' => Hash::make('kecamatan123'),
                    'role' => 'user',
                    'kategori' => 'kecamatan',
                    'organisasi' => 'Kecamatan ' . $nama,
                    'is_active' => true,
                ]
            );
        }
    }
}
