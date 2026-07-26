<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'user_id',
        'value',
        'file_path',
        'file_original_name',
        'status_saat_itu',
        'diganti_at',
    ];

    protected $casts = [
        'diganti_at' => 'datetime',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
