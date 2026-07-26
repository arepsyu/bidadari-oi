<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'kategori',
        'opd_id',
        'organisasi',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function opd()
    {
        return $this->belongsTo(Opd::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isOpd(): bool
    {
        return $this->kategori === 'opd';
    }

    public function isKecamatan(): bool
    {
        return $this->kategori === 'kecamatan';
    }

    public function isDesa(): bool
    {
        return $this->kategori === 'desa';
    }

    public function kategoriLabel(): string
    {
        return match ($this->kategori) {
            'opd' => 'OPD/Dinas',
            'kecamatan' => 'Kecamatan',
            'desa' => 'Desa',
            default => '-',
        };
    }

    /**
     * Ambil semua pertanyaan yang relevan buat user ini sesuai kategori akunnya.
     */
    public function pertanyaanRelevan()
    {
        if ($this->kategori === 'kecamatan') {
            return Pertanyaan::where('untuk_kecamatan', true);
        }

        if ($this->kategori === 'desa') {
            return Pertanyaan::where('untuk_desa', true);
        }

        if ($this->kategori === 'opd' && $this->opd_id) {
            return Pertanyaan::whereHas('opds', function ($q) {
                $q->where('opds.id', $this->opd_id);
            });
        }

        return Pertanyaan::whereRaw('1 = 0');
    }
}
