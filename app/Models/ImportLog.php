<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'filename',
        'total_rows',
        'successful_rows',
        'errors',
        'warnings',
        'type',
    ];

    protected $casts = [
        'errors' => 'array',
        'warnings' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}