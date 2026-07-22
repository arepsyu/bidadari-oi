<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'data_requirement_id',
        'value',
        'file_path',
        'file_original_name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dataRequirement()
    {
        return $this->belongsTo(DataRequirement::class);
    }
}
