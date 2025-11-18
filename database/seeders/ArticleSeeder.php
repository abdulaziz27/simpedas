<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\User;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin dinas as author
        $adminDinas = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin_dinas');
        })->first();

        if (!$adminDinas) {
            $this->command->warn('Admin Dinas tidak ditemukan. Membuat artikel dengan user pertama...');
            $adminDinas = User::first();
        }

        if (!$adminDinas) {
            $this->command->error('Tidak ada user ditemukan. Silakan jalankan UserSeeder terlebih dahulu.');
            return;
        }

        $articles = [
            [
                'title' => 'Program Peningkatan Mutu Pendidikan di Kota Pematang Siantar',
                'slug' => 'program-peningkatan-mutu-pendidikan-kota-pematang-siantar',
                'excerpt' => 'Dinas Pendidikan Kota Pematang Siantar meluncurkan program komprehensif untuk meningkatkan mutu pendidikan di seluruh jenjang sekolah.',
                'content' => '<h2>Program Peningkatan Mutu Pendidikan</h2><p>Dinas Pendidikan Kota Pematang Siantar dengan bangga mengumumkan peluncuran program komprehensif untuk meningkatkan mutu pendidikan di seluruh jenjang sekolah. Program ini mencakup berbagai aspek penting dalam dunia pendidikan.</p><h3>Fokus Utama Program</h3><ul><li>Peningkatan kualitas pembelajaran di kelas</li><li>Pelatihan dan pengembangan kompetensi guru</li><li>Penyediaan sarana dan prasarana pendidikan yang memadai</li><li>Peningkatan partisipasi masyarakat dalam pendidikan</li></ul><p>Program ini diharapkan dapat meningkatkan kualitas pendidikan di Kota Pematang Siantar secara signifikan dalam beberapa tahun ke depan.</p>',
                'status' => 'published',
                'published_at' => now()->subDays(5),
                'meta_title' => 'Program Peningkatan Mutu Pendidikan - Dinas Pendidikan Pematang Siantar',
                'meta_description' => 'Program komprehensif untuk meningkatkan mutu pendidikan di seluruh jenjang sekolah di Kota Pematang Siantar.',
            ],
            [
                'title' => 'Pelatihan Guru Berkelanjutan: Investasi untuk Masa Depan Pendidikan',
                'slug' => 'pelatihan-guru-berkelanjutan-investasi-masa-depan-pendidikan',
                'excerpt' => 'Dinas Pendidikan menyelenggarakan program pelatihan berkelanjutan untuk meningkatkan kompetensi dan profesionalisme guru di seluruh wilayah.',
                'content' => '<h2>Pelatihan Guru Berkelanjutan</h2><p>Sebagai bagian dari komitmen untuk meningkatkan kualitas pendidikan, Dinas Pendidikan Kota Pematang Siantar menyelenggarakan program pelatihan berkelanjutan bagi para guru.</p><h3>Manfaat Program</h3><p>Program ini dirancang untuk memberikan guru-guru di wilayah kami kesempatan untuk mengembangkan keterampilan mengajar mereka, mempelajari metode pembelajaran terbaru, dan meningkatkan pemahaman mereka tentang kurikulum yang berlaku.</p><h3>Format Pelatihan</h3><ul><li>Workshop interaktif</li><li>Seminar dan konferensi pendidikan</li><li>Program mentoring</li><li>Pelatihan online dan offline</li></ul><p>Dengan investasi yang tepat pada pengembangan profesional guru, kita dapat memastikan bahwa generasi mendatang mendapatkan pendidikan terbaik.</p>',
                'status' => 'published',
                'published_at' => now()->subDays(3),
                'meta_title' => 'Pelatihan Guru Berkelanjutan - Dinas Pendidikan Pematang Siantar',
                'meta_description' => 'Program pelatihan berkelanjutan untuk meningkatkan kompetensi dan profesionalisme guru di Kota Pematang Siantar.',
            ],
            [
                'title' => 'Penerapan Teknologi Digital dalam Pembelajaran di Era Modern',
                'slug' => 'penerapan-teknologi-digital-pembelajaran-era-modern',
                'excerpt' => 'Sekolah-sekolah di Kota Pematang Siantar mulai mengadopsi teknologi digital untuk meningkatkan efektivitas pembelajaran dan mempersiapkan siswa menghadapi tantangan masa depan.',
                'content' => '<h2>Teknologi Digital dalam Pembelajaran</h2><p>Di era digital yang terus berkembang, Dinas Pendidikan Kota Pematang Siantar mendorong sekolah-sekolah untuk mengintegrasikan teknologi digital dalam proses pembelajaran.</p><h3>Inisiatif yang Dilakukan</h3><p>Beberapa inisiatif yang telah dilakukan meliputi:</p><ul><li>Penyediaan perangkat teknologi di sekolah</li><li>Pelatihan guru dalam penggunaan teknologi pendidikan</li><li>Pengembangan platform pembelajaran digital</li><li>Program literasi digital untuk siswa</li></ul><h3>Manfaat</h3><p>Penerapan teknologi digital dalam pembelajaran tidak hanya membuat proses belajar lebih menarik, tetapi juga membantu siswa mengembangkan keterampilan yang dibutuhkan di abad 21. Dengan teknologi, siswa dapat mengakses sumber belajar yang lebih luas dan belajar dengan cara yang lebih interaktif.</p>',
                'status' => 'published',
                'published_at' => now()->subDays(1),
                'meta_title' => 'Penerapan Teknologi Digital dalam Pembelajaran - Dinas Pendidikan Pematang Siantar',
                'meta_description' => 'Inisiatif Dinas Pendidikan dalam mengintegrasikan teknologi digital untuk meningkatkan kualitas pembelajaran di sekolah.',
            ],
            [
                'title' => 'Pentingnya Partisipasi Orang Tua dalam Pendidikan Anak',
                'slug' => 'pentingnya-partisipasi-orang-tua-pendidikan-anak',
                'excerpt' => 'Dinas Pendidikan mengajak orang tua untuk lebih aktif terlibat dalam pendidikan anak-anak mereka, karena kolaborasi antara sekolah dan keluarga sangat penting untuk kesuksesan belajar siswa.',
                'content' => '<h2>Partisipasi Orang Tua dalam Pendidikan</h2><p>Pendidikan yang sukses memerlukan kolaborasi yang erat antara sekolah dan keluarga. Dinas Pendidikan Kota Pematang Siantar mengajak semua orang tua untuk lebih aktif terlibat dalam pendidikan anak-anak mereka.</p><h3>Mengapa Partisipasi Orang Tua Penting?</h3><ul><li>Meningkatkan motivasi belajar siswa</li><li>Meningkatkan prestasi akademik</li><li>Membangun komunikasi yang lebih baik antara sekolah dan keluarga</li><li>Menciptakan lingkungan belajar yang mendukung di rumah</li></ul><h3>Cara Orang Tua Dapat Terlibat</h3><p>Ada banyak cara bagi orang tua untuk terlibat dalam pendidikan anak mereka:</p><ul><li>Menghadiri pertemuan orang tua dan guru</li><li>Membantu anak dengan pekerjaan rumah</li><li>Membaca bersama anak</li><li>Berpartisipasi dalam kegiatan sekolah</li><li>Berkomunikasi secara teratur dengan guru</li></ul><p>Dengan bekerja sama, kita dapat memastikan bahwa setiap anak mendapatkan pendidikan terbaik dan mencapai potensi penuh mereka.</p>',
                'status' => 'published',
                'published_at' => now(),
                'meta_title' => 'Pentingnya Partisipasi Orang Tua dalam Pendidikan - Dinas Pendidikan Pematang Siantar',
                'meta_description' => 'Ajakan Dinas Pendidikan kepada orang tua untuk lebih aktif terlibat dalam pendidikan anak-anak mereka.',
            ],
        ];

        foreach ($articles as $articleData) {
            Article::firstOrCreate(
                ['slug' => $articleData['slug']],
                array_merge($articleData, [
                    'author_id' => $adminDinas->id,
                    'views' => rand(50, 500),
                ])
            );
        }

        $this->command->info('4 artikel berhasil dibuat!');
    }
}
