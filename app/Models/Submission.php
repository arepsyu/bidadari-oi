<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pertanyaan_id',
        'desa_id',
        'value',
        'file_path',
        'file_original_name',
        'status',
        'catatan_admin',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pertanyaan()
    {
        return $this->belongsTo(Pertanyaan::class);
    }

    public function desa()
    {
        return $this->belongsTo(Desa::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function histories()
    {
        return $this->hasMany(SubmissionHistory::class)->orderByDesc('diganti_at');
    }

    public function isMenunggu(): bool
    {
        return $this->status === 'menunggu';
    }

    public function isDisetujui(): bool
    {
        return $this->status === 'disetujui';
    }

    public function isDitolak(): bool
    {
        return $this->status === 'ditolak';
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
            default => 'Menunggu Verifikasi',
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'disetujui' => 'bg-success',
            'ditolak' => 'bg-danger',
            default => 'bg-warning text-dark',
        };
    }
}
