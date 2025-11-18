<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Gallery;
use Illuminate\Support\Facades\Storage;

class GallerySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Note: Seeder ini akan membuat data gallery, tapi image-nya perlu diupload manual
        // atau menggunakan placeholder image
        
        $galleries = [
            [
                'title' => 'Kegiatan Workshop Guru',
                'description' => 'Kegiatan workshop peningkatan kompetensi guru di Dinas Pendidikan Kota Pematang Siantar',
                'image' => 'gallery-images/workshop-guru.jpg', // Path relatif dari storage/app/public
                'category' => 'kegiatan',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Acara Peresmian Sekolah Baru',
                'description' => 'Peresmian sekolah baru di wilayah Kota Pematang Siantar untuk meningkatkan akses pendidikan',
                'image' => 'gallery-images/peresmian-sekolah.jpg',
                'category' => 'acara',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Prestasi Siswa Olimpiade',
                'description' => 'Penghargaan untuk siswa berprestasi dalam olimpiade sains tingkat nasional',
                'image' => 'gallery-images/prestasi-siswa.jpg',
                'category' => 'prestasi',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Kunjungan Kerja Dinas Pendidikan',
                'description' => 'Kunjungan kerja ke sekolah-sekolah untuk monitoring dan evaluasi program pendidikan',
                'image' => 'gallery-images/kunjungan-kerja.jpg',
                'category' => 'kegiatan',
                'order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($galleries as $galleryData) {
            // Check if gallery with same title exists
            $existing = Gallery::where('title', $galleryData['title'])->first();
            
            if (!$existing) {
                // Create the gallery record
                // Note: Image file needs to be uploaded manually via admin panel
                // The image path is set but file should be uploaded separately
                Gallery::create($galleryData);
            }
        }

        $this->command->info('4 gallery items berhasil dibuat!');
        $this->command->warn('Catatan: Pastikan file gambar sudah diupload ke storage/app/public/gallery-images/');
    }
}

