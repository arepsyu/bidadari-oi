<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Klaster extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'urutan', 'nilai_max', 'nilai_evaluasi'];

    public function indikators()
    {
        return $this->hasMany(Indikator::class)->orderBy('urutan');
    }
}
