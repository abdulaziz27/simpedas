<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class SchoolTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithEvents
{
    /**
     * @return array
     */
    public function array(): array
    {
        // Template dengan contoh data di baris pertama
        return [
            [
                'CREATE',
                '12345678',
                'Contoh: SD Negeri 101 Pematang Siantar',
                'SD',
                'Negeri',
                'Jl. Contoh No. 123',
                'Desa Contoh',
                'Siantar Utara',
                'Pematang Siantar',
                'Sumatera Utara',
                '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!..." width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>',
                '2.9707',
                '99.0674',
                '0622-123456',
                'sekolah@example.com',
                'https://www.example.com',
                'Nama Kepala Sekolah',
                'password123',
            ],
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'AKSI',
            'NPSN',
            'NAMA_SEKOLAH',
            'JENJANG_PENDIDIKAN',
            'STATUS',
            'ALAMAT',
            'DESA',
            'KECAMATAN',
            'KABUPATEN_KOTA',
            'PROVINSI',
            'GOOGLE_MAPS_LINK',
            'LATITUDE',
            'LONGITUDE',
            'TELEPON',
            'EMAIL',
            'WEBSITE',
            'KEPALA_SEKOLAH',
            'PASSWORD_ADMIN'
        ];
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 15, // AKSI
            'B' => 15, // NPSN
            'C' => 30, // NAMA_SEKOLAH
            'D' => 20, // JENJANG_PENDIDIKAN
            'E' => 15, // STATUS
            'F' => 40, // ALAMAT
            'G' => 20, // DESA
            'H' => 20, // KECAMATAN
            'I' => 20, // KABUPATEN_KOTA
            'J' => 20, // PROVINSI
            'K' => 60, // GOOGLE_MAPS_LINK (lebih lebar untuk iframe code)
            'L' => 15, // LATITUDE
            'M' => 15, // LONGITUDE
            'N' => 20, // TELEPON
            'O' => 30, // EMAIL
            'P' => 30, // WEBSITE
            'Q' => 30, // KEPALA_SEKOLAH
            'R' => 20, // PASSWORD_ADMIN
            'S' => 60, // PETUNJUK (kolom utama)
            'T' => 60, // PETUNJUK (merge, biar wrap text optimal)
            'U' => 60, // PETUNJUK (merge, biar wrap text optimal)
            'V' => 60, // PETUNJUK (merge, biar wrap text optimal)
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '125047'], // Warna hijau tua sesuai tema
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Data validation dropdown untuk semua baris (2-1000)
                $lastRow = 1000;

                // 1. AKSI (Column A) - CREATE, UPDATE, DELETE
                for ($row = 2; $row <= $lastRow; $row++) {
                    $validation = $sheet->getCell('A' . $row)->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(false);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setErrorTitle('Input error');
                    $validation->setError('Nilai tidak valid. Pilih dari daftar.');
                    $validation->setPromptTitle('Pilih Aksi');
                    $validation->setPrompt('Pilih CREATE, UPDATE, atau DELETE');
                    $validation->setFormula1('"CREATE,UPDATE,DELETE"');
                }

                // 2. JENJANG_PENDIDIKAN (Column D) - TK, SD, SMP, KB, PKBM
                for ($row = 2; $row <= $lastRow; $row++) {
                    $validation = $sheet->getCell('D' . $row)->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(false);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setErrorTitle('Input error');
                    $validation->setError('Nilai tidak valid. Pilih jenjang pendidikan.');
                    $validation->setPromptTitle('Pilih Jenjang Pendidikan');
                    $validation->setPrompt('Pilih TK, SD, SMP, KB, atau PKBM');
                    $validation->setFormula1('"TK,SD,SMP,KB,PKBM"');
                }

                // 3. STATUS (Column E) - Negeri, Swasta
                for ($row = 2; $row <= $lastRow; $row++) {
                    $validation = $sheet->getCell('E' . $row)->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(false);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setErrorTitle('Input error');
                    $validation->setError('Nilai tidak valid. Pilih status sekolah.');
                    $validation->setPromptTitle('Pilih Status');
                    $validation->setPrompt('Pilih Negeri atau Swasta');
                    $validation->setFormula1('"Negeri,Swasta"');
                }

                // Pindahkan petunjuk ke sisi kanan, dua kolom setelah kolom paling kanan (kolom S)
                $startCol = 'S'; // Dua kolom setelah Q
                $row = 2; // Mulai di bawah header
                $petunjuk = [
                    'PETUNJUK PENGGUNAAN:',
                    'Baris 2 adalah CONTOH data. Hapus atau edit sesuai kebutuhan.',
                    '',
                    '1. Kolom AKSI: Wajib diisi dengan CREATE, UPDATE, atau DELETE (dropdown)',
                    '2. Kolom NPSN: Wajib diisi dan harus unik. Untuk UPDATE/DELETE cukup isi NPSN',
                    '3. Kolom NAMA_SEKOLAH: Wajib diisi, minimal 3 karakter',
                    '4. Kolom JENJANG_PENDIDIKAN: Wajib diisi dengan nilai TK, SD, SMP, KB, atau PKBM (dropdown)',
                    '5. Kolom STATUS: Wajib diisi dengan nilai Negeri atau Swasta (dropdown)',
                    '6. Kolom ALAMAT: Wajib diisi',
                    '7. Kolom DESA, KECAMATAN, KABUPATEN_KOTA, PROVINSI: Opsional',
                    '8. Kolom GOOGLE_MAPS_LINK: Opsional (copy full iframe code dari Google Maps - text panjang akan otomatis wrap)',
                    '9. Kolom LATITUDE, LONGITUDE: Opsional (untuk lokasi di peta)',
                    '10. Kolom TELEPON, EMAIL, WEBSITE: Opsional',
                    '11. Kolom KEPALA_SEKOLAH: Wajib diisi',
                    '12. Kolom PASSWORD_ADMIN: Opsional (minimal 8 karakter - akan otomatis buat akun admin sekolah)',
                    '',
                    'PANDUAN PENAMAAN SEKOLAH:',
                    '• TK: Taman Kanak-kanak [Nama] atau TK [Nama]',
                    '• SD: SD [Nama] atau SD Negeri/Swasta [Nama]',
                    '• SMP: SMP [Nama] atau SMP Negeri/Swasta [Nama]',
                    '• KB: Kelompok Bermain [Nama] atau KB [Nama]',
                    '• PKBM: Pusat Kegiatan Belajar Masyarakat [Nama] atau PKBM [Nama]',
                    '• Contoh: "SD Negeri 101 Pematang Siantar", "TK Al-Hidayah", "KB Tunas Bangsa"',
                    '',
                    'CATATAN TEKNIS:',
                    '• Dropdown tersedia di semua baris (2-1000)',
                    '• NPSN harus unik, tidak boleh duplikat',
                    '• Format email: user@domain.com',
                    '• Format website: https://www.example.com',
                    '• Format koordinat: Latitude (-90 hingga 90), Longitude (-180 hingga 180)',
                    '• Format Google Maps: Copy FULL iframe code dari "Embed a map" di Google Maps',
                    '• Password admin: Jika diisi, akan otomatis membuat akun admin sekolah dengan email yang sama',
                    '• Kolom GOOGLE_MAPS_LINK akan otomatis wrap text untuk iframe code panjang',
                ];
                foreach ($petunjuk as $text) {
                    $sheet->setCellValue($startCol . $row, $text);
                    // Merge cell petunjuk saja (S sampai V)
                    $sheet->mergeCells($startCol . $row . ':V' . $row);
                    $sheet->getStyle($startCol . $row)->getFont()->setBold($row === 2);
                    $row++;
                }
                // Format wrap text untuk petunjuk
                $sheet->getStyle('S2:V' . ($row - 1))->getAlignment()->setWrapText(true);

                // Format wrap text untuk kolom GOOGLE_MAPS_LINK (Column K)
                // Set wrap text untuk semua baris data
                for ($row = 2; $row <= $lastRow; $row++) {
                    $sheet->getStyle('K' . $row)->getAlignment()->setWrapText(true);
                    $sheet->getStyle('K' . $row)->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
                }

                // Set row height auto untuk baris data
                for ($row = 2; $row <= $lastRow; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(-1); // -1 = auto height
                }
            },
        ];
    }
}
