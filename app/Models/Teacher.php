<?php
// app/Models/Teacher.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'full_name',
        'nuptk',
        'nip',
        'birth_place',
        'birth_date',
        'gender',
        'employment_status',
        'jenis_ptk',
        'keterangan',
        'gelar_depan',
        'gelar_belakang',
        'jenjang',
        'education_major', // jurusan/prodi
        'sertifikasi',
        'tmt',
        'tugas_tambahan',
        'mengajar',
        'jam_tugas_tambahan',
        'jjm',
        'total_jjm',
        'siswa',
        'kompetensi',
        'subjects', // keep for backward compatibility
        'photo'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'tmt' => 'date',
        'jam_tugas_tambahan' => 'integer',
        'jjm' => 'integer', 
        'total_jjm' => 'integer',
        'siswa' => 'integer'
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function user()
    {
        return $this->hasOne(User::class, 'teacher_id');
    }

    public function documents()
    {
        return $this->hasMany(TeacherDocument::class);
    }

    // Accessor untuk subjects (jika disimpan sebagai JSON)
    public function getSubjectsArrayAttribute()
    {
        return explode(',', $this->subjects);
    }

    // Scope untuk pencarian
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('full_name', 'like', '%' . $search . '%')
                ->orWhere('nuptk', 'like', '%' . $search . '%')
                ->orWhere('nip', 'like', '%' . $search . '%');
        });
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'Aktif');
    }

    public function scopeBySchool($query, $schoolId)
    {
        return $query->where('school_id', $schoolId);
    }
}
