<?php
// app/Models/NonTeachingStaff.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NonTeachingStaff extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'full_name',
        'nip_nik',
        'nuptk',
        'birth_place',
        'birth_date',
        'gender',
        'religion',
        'address',
        'staff_type',
        'position',
        'education_level',
        'education_major',
        'employment_status',
        'rank',
        'tmt',
        'status'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'tmt' => 'date'
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'Aktif');
    }
}
