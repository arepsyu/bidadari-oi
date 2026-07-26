<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pertanyaan extends Model
{
    use HasFactory;

    protected $fillable = [
        'indikator_id', 'kode', 'teks', 'tipe', 'nilai_max', 'nilai_evaluasi',
        'wajib', 'untuk_kecamatan', 'untuk_desa', 'urutan',
    ];

    protected $casts = [
        'wajib' => 'boolean',
        'untuk_kecamatan' => 'boolean',
        'untuk_desa' => 'boolean',
    ];

    public function indikator()
    {
        return $this->belongsTo(Indikator::class);
    }

    public function opds()
    {
        return $this->belongsToMany(Opd::class, 'pertanyaan_opd');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function isFile(): bool
    {
        return $this->tipe === 'file';
    }

    /**
     * Cek apakah pertanyaan ini relevan/terlihat buat user tertentu,
     * berdasarkan kategori akun (opd/kecamatan/desa).
     */
    public function isVisibleFor(User $user): bool
    {
        if ($user->kategori === 'kecamatan') {
            return $this->untuk_kecamatan;
        }

        if ($user->kategori === 'desa') {
            return $this->untuk_desa;
        }

        if ($user->kategori === 'opd' && $user->opd_id) {
            return $this->opds->contains('id', $user->opd_id);
        }

        return false;
    }
}
