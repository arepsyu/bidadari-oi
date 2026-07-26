<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Indikator extends Model
{
    use HasFactory;

    protected $fillable = ['klaster_id', 'kode', 'nama', 'urutan', 'nilai_max', 'nilai_evaluasi'];

    public function klaster()
    {
        return $this->belongsTo(Klaster::class);
    }

    public function pertanyaans()
    {
        return $this->hasMany(Pertanyaan::class)->orderBy('urutan');
    }
}
