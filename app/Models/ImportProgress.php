<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportProgress extends Model
{
    use HasFactory;

    protected $table = 'import_progress';

    protected $fillable = [
        'import_id',
        'user_id',
        'status',
        'total',
        'processed',
        'success',
        'failed',
        'errors',
        'warnings',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'errors' => 'array',
        'warnings' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getProgressPercentageAttribute()
    {
        return $this->total > 0 ? round(($this->processed / $this->total) * 100, 1) : 0;
    }

    public function getElapsedTimeAttribute()
    {
        if (!$this->started_at) return 0;

        $endTime = $this->completed_at ?? now();
        return $this->started_at->diffInSeconds($endTime);
    }
}
