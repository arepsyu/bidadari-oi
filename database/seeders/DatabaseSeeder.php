<?php

namespace Database\Seeders;

use App\Models\Opd;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin
        User::firstOrCreate(
            ['email' => 'admin@bidadarioi.test'],
            [
                'name' => 'Administrator BIDADARI OI',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        // 2. Import data KLA (Klaster > Indikator > Pertanyaan) + master OPD
        $this->call(KlaSeeder::class);

        // 3. 16 akun Kecamatan se-Ogan Ilir
        $this->call(KecamatanSeeder::class);

        // 4. Contoh 1 akun OPD & 1 akun Desa buat demo
        $contohOpd = Opd::first();
        if ($contohOpd) {
            User::firstOrCreate(
                ['email' => 'opd.contoh@bidadarioi.test'],
                [
                    'name' => 'User ' . $contohOpd->nama,
                    'password' => Hash::make('password123'),
                    'role' => 'user',
                    'kategori' => 'opd',
                    'opd_id' => $contohOpd->id,
                    'organisasi' => $contohOpd->nama,
                    'is_active' => true,
                ]
            );
        }

        User::firstOrCreate(
            ['email' => 'desa.contoh@bidadarioi.test'],
            [
                'name' => 'Desa Contoh',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'kategori' => 'desa',
                'organisasi' => 'Desa Contoh',
                'is_active' => true,
            ]
        );
    }
}
