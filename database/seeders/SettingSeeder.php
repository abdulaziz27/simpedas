<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tentang Kami Settings
        Setting::set('about_visi', '', 'textarea', 'Visi', 'Visi Dinas Pendidikan');
        Setting::set('about_misi', '', 'textarea', 'Misi', 'Misi Dinas Pendidikan');
        Setting::set('about_tugas_pokok', '', 'textarea', 'Tugas Pokok', 'Tugas Pokok Dinas Pendidikan');
        Setting::set('about_fungsi', '', 'textarea', 'Fungsi', 'Fungsi Dinas Pendidikan');

        // Kontak Settings
        Setting::set('contact_address', 'Jl. Merdeka No.228c, Dwikora, Kec. Siantar Bar., Kota Pematang Siantar, Sumatera Utara 21146', 'textarea', 'Alamat', 'Alamat lengkap Dinas Pendidikan');
        Setting::set('contact_phone', '(0622) 421123', 'text', 'Telepon', 'Nomor telepon Dinas Pendidikan');
        Setting::set('contact_email', 'disdik@pematangsiantar.go.id', 'text', 'Email', 'Alamat email Dinas Pendidikan');
        Setting::set('contact_hours', 'Senin - Jumat: 08:00 - 16:00 WIB
Sabtu: 08:00 - 12:00 WIB
Minggu: Tutup', 'textarea', 'Jam Operasional', 'Jam operasional Dinas Pendidikan');
        Setting::set('contact_map_url', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3984.494668834142!2d99.06709037543456!3d2.9601531542844213!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x303184429d8b212f%3A0x382944b91c345d9b!2sDinas%20Pendidikan%20Kota%20Pematangsiantar!5e0!3m2!1sid!2sid!4v1763474070417!5m2!1sid!2sid', 'text', 'URL Peta', 'URL embed Google Maps');

        $this->command->info('Settings default berhasil dibuat!');
    }
}
