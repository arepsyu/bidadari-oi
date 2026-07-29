<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    use HasFactory;

    protected $fillable = ['nama'];

    public function desas()
    {
        return $this->hasMany(Desa::class)->orderBy('nama');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
