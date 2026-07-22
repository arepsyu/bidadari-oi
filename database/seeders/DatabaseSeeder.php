<?php

namespace Database\Seeders;

use App\Models\DataRequirement;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrator BIDADARI OI',
            'email' => 'admin@bidadarioi.test',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'organisasi' => 'Dinas Pemberdayaan Perempuan dan Perlindungan Anak',
            'is_active' => true,
        ]);

        $contohUser = User::create([
            'name' => 'Contoh User OPD',
            'email' => 'user@bidadarioi.test',
            'password' => Hash::make('password123'),
            'role' => 'user',
            'organisasi' => 'Contoh OPD / Kecamatan',
            'is_active' => true,
        ]);

        $requirements = [
            ['judul' => 'Nama Organisasi', 'deskripsi' => 'Isi nama lengkap organisasi/OPD Anda.', 'tipe' => 'text', 'wajib' => true, 'urutan' => 1],
            ['judul' => 'Upload SK Organisasi', 'deskripsi' => 'Upload dokumen SK pembentukan organisasi (PDF).', 'tipe' => 'file', 'wajib' => true, 'urutan' => 2],
            ['judul' => 'Upload Data Pendukung Lainnya', 'deskripsi' => 'Upload dokumen pendukung lain terkait Kabupaten Layak Anak.', 'tipe' => 'file', 'wajib' => false, 'urutan' => 3],
        ];

        foreach ($requirements as $req) {
            DataRequirement::create($req);
        }
    }
}
