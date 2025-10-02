<?php
// app/Models/Student.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        // ==== W A J I B ====
        'nisn',
        'nipd',
        'nama_lengkap',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'agama',
        'rombel',
        'sekolah_id',
        'foto',

        // ==== O P S I O N A L ====
        // Domisili
        'alamat',
        'kelurahan',
        'kecamatan',
        'kode_pos',

        // Status
        'status_siswa',

        // Data keluarga
        'nama_ayah',
        'pekerjaan_ayah',
        'nama_ibu',
        'pekerjaan_ibu',
        'anak_ke',
        'jumlah_saudara',

        // Kontak
        'no_hp',

        // Sosial-ekonomi
        'kip',
        'transportasi',
        'jarak_rumah_sekolah',

        // Kesehatan
        'tinggi_badan',
        'berat_badan',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'kip' => 'boolean',
        'anak_ke' => 'integer',
        'jumlah_saudara' => 'integer',
        'tinggi_badan' => 'integer',
        'berat_badan' => 'integer',
        'jarak_rumah_sekolah' => 'decimal:2',
    ];

    public function school()
    {
        return $this->belongsTo(School::class, 'sekolah_id');
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
            $q->where('nama_lengkap', 'like', '%' . $search . '%')
                ->orWhere('nisn', 'like', '%' . $search . '%')
                ->orWhere('nipd', 'like', '%' . $search . '%');
        });
    }

    public function scopeActive($query)
    {
        return $query->where('status_siswa', 'aktif');
    }

    public function scopeBySchool($query, $schoolId)
    {
        return $query->where('sekolah_id', $schoolId);
    }

    public function scopeByRombel($query, $rombel)
    {
        return $query->where('rombel', $rombel);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status_siswa', $status);
    }

    /**
     * Calculate student age from birth date
     */
    public function getAgeAttribute()
    {
        if (!$this->tanggal_lahir) {
            return null;
        }

        return $this->tanggal_lahir->age;
    }

    /**
     * Get gender label
     */
    public function getJenisKelaminLabelAttribute()
    {
        return $this->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    }

    /**
     * Get status label
     */
    public function getStatusSiswaLabelAttribute()
    {
        return ucfirst($this->status_siswa);
    }

    /**
     * Get KIP status label
     */
    public function getKipLabelAttribute()
    {
        return $this->kip ? 'Ya' : 'Tidak';
    }
}
