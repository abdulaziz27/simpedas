<?php
// app/Models/Student.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'full_name',
        'nisn',
        'nis',
        'birth_place',
        'birth_date',
        'gender',
        'religion',
        'grade_level',
        'parent_name',
        'major',
        'achievements',
        'student_status',
        'graduation_status',
        'academic_year'
    ];

    protected $casts = [
        'birth_date' => 'date'
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function reports()
    {
        return $this->hasMany(StudentReport::class);
    }

    public function certificates()
    {
        return $this->hasMany(StudentCertificate::class);
    }

    // Scope untuk pencarian
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('full_name', 'like', '%' . $search . '%')
                ->orWhere('nisn', 'like', '%' . $search . '%')
                ->orWhere('nis', 'like', '%' . $search . '%');
        });
    }

    public function scopeActive($query)
    {
        return $query->where('student_status', 'Aktif');
    }

    public function scopeBySchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }

    /**
     * Calculate student age from birth date
     */
    public function getAgeAttribute()
    {
        if (!$this->birth_date) {
            return null;
        }

        return $this->birth_date->age;
    }
}
