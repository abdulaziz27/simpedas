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
        // Template kosong, hanya berisi header
        return [];
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
            'KEPALA_SEKOLAH'
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
            'K' => 50, // GOOGLE_MAPS_LINK
            'L' => 15, // LATITUDE
            'M' => 15, // LONGITUDE
            'N' => 20, // TELEPON
            'O' => 30, // EMAIL
            'P' => 30, // WEBSITE
            'Q' => 30, // KEPALA_SEKOLAH
            'S' => 60, // PETUNJUK (kolom utama)
            'T' => 60, // PETUNJUK (merge, biar wrap text optimal)
            'U' => 60, // PETUNJUK (merge, biar wrap text optimal)
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
                    '1. Kolom AKSI: Wajib diisi dengan CREATE, UPDATE, atau DELETE (dropdown)',
                    '2. Kolom NPSN: Wajib diisi dan harus unik. Untuk UPDATE/DELETE cukup isi NPSN',
                    '3. Kolom NAMA_SEKOLAH: Wajib diisi, minimal 3 karakter',
                    '4. Kolom JENJANG_PENDIDIKAN: Wajib diisi dengan nilai TK, SD, SMP, KB, atau PKBM (dropdown)',
                    '5. Kolom STATUS: Wajib diisi dengan nilai Negeri atau Swasta (dropdown)',
                    '6. Kolom ALAMAT: Wajib diisi',
                    '7. Kolom DESA, KECAMATAN, KABUPATEN_KOTA, PROVINSI: Opsional',
                    '8. Kolom GOOGLE_MAPS_LINK: Opsional (iframe code dari Google Maps - akan tampil embedded)',
                    '9. Kolom LATITUDE, LONGITUDE: Opsional (untuk lokasi di peta)',
                    '10. Kolom TELEPON, EMAIL, WEBSITE: Opsional',
                    '11. Kolom KEPALA_SEKOLAH: Wajib diisi',
                    '',
                    'CATATAN:',
                    '• Dropdown tersedia di semua baris (2-1000)',
                    '• NPSN harus unik, tidak boleh duplikat',
                    '• Format email: user@domain.com',
                    '• Format website: https://www.example.com',
                    '• Format koordinat: Latitude (-90 hingga 90), Longitude (-180 hingga 180)',
                    '• Format Google Maps: Copy iframe code dari "Embed a map" di Google Maps',
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
            },
        ];
    }
}
