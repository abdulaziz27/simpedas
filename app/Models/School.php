<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'npsn',
        'education_level',
        'status',
        'address',
        'phone',
        'email',
        'website',
        'headmaster',
        'region',
        'logo'
    ];

    public function teachers()
    {
        return $this->hasMany(Teacher::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function nonTeachingStaff()
    {
        return $this->hasMany(NonTeachingStaff::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Scope untuk filter
    public function scopeByEducationLevel($query, $level)
    {
        return $query->where('education_level', $level);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
