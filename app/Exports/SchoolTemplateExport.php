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
            'ID',
            'NPSN',
            'NAMA_SEKOLAH',
            'JENJANG_PENDIDIKAN',
            'STATUS',
            'ALAMAT',
            'TELEPON',
            'EMAIL',
            'WEBSITE',
            'KEPALA_SEKOLAH',
            'KECAMATAN'
        ];
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 15, // AKSI
            'B' => 10, // ID
            'C' => 15, // NPSN
            'D' => 30, // NAMA_SEKOLAH
            'E' => 20, // JENJANG_PENDIDIKAN
            'F' => 15, // STATUS
            'G' => 40, // ALAMAT
            'H' => 20, // TELEPON
            'I' => 30, // EMAIL
            'J' => 30, // WEBSITE
            'K' => 30, // KEPALA_SEKOLAH
            'L' => 20, // KECAMATAN
            'N' => 60, // PETUNJUK (kolom utama)
            'O' => 60, // PETUNJUK (merge, biar wrap text optimal)
            'P' => 60, // PETUNJUK (merge, biar wrap text optimal)
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

                // Tambahkan validasi untuk kolom ACTION
                $actionValidation = $sheet->getCell('A2')->getDataValidation();
                $actionValidation->setType(DataValidation::TYPE_LIST);
                $actionValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $actionValidation->setAllowBlank(false);
                $actionValidation->setShowInputMessage(true);
                $actionValidation->setShowErrorMessage(true);
                $actionValidation->setShowDropDown(true);
                $actionValidation->setErrorTitle('Input error');
                $actionValidation->setError('Nilai tidak valid. Pilih dari daftar.');
                $actionValidation->setPromptTitle('Pilih dari daftar');
                $actionValidation->setPrompt('Pilih CREATE, UPDATE, atau DELETE');
                $actionValidation->setFormula1('"CREATE,UPDATE,DELETE"');

                // Tambahkan validasi untuk kolom EDUCATION_LEVEL
                $educationLevelValidation = $sheet->getCell('E2')->getDataValidation();
                $educationLevelValidation->setType(DataValidation::TYPE_LIST);
                $educationLevelValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $educationLevelValidation->setAllowBlank(false);
                $educationLevelValidation->setShowInputMessage(true);
                $educationLevelValidation->setShowErrorMessage(true);
                $educationLevelValidation->setShowDropDown(true);
                $educationLevelValidation->setErrorTitle('Input error');
                $educationLevelValidation->setError('Nilai tidak valid. Pilih dari daftar.');
                $educationLevelValidation->setPromptTitle('Pilih dari daftar');
                $educationLevelValidation->setPrompt('Pilih jenjang pendidikan');
                $educationLevelValidation->setFormula1('"TK,SD,SMP,SMA,SMK"');

                // Tambahkan validasi untuk kolom STATUS
                $statusValidation = $sheet->getCell('F2')->getDataValidation();
                $statusValidation->setType(DataValidation::TYPE_LIST);
                $statusValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $statusValidation->setAllowBlank(false);
                $statusValidation->setShowInputMessage(true);
                $statusValidation->setShowErrorMessage(true);
                $statusValidation->setShowDropDown(true);
                $statusValidation->setErrorTitle('Input error');
                $statusValidation->setError('Nilai tidak valid. Pilih dari daftar.');
                $statusValidation->setPromptTitle('Pilih dari daftar');
                $statusValidation->setPrompt('Pilih Negeri atau Swasta');
                $statusValidation->setFormula1('"Negeri,Swasta"');

                // Tambahkan validasi untuk kolom REGION
                $regionValidation = $sheet->getCell('L2')->getDataValidation();
                $regionValidation->setType(DataValidation::TYPE_LIST);
                $regionValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $regionValidation->setAllowBlank(false);
                $regionValidation->setShowInputMessage(true);
                $regionValidation->setShowErrorMessage(true);
                $regionValidation->setShowDropDown(true);
                $regionValidation->setErrorTitle('Input error');
                $regionValidation->setError('Nilai tidak valid. Pilih dari daftar.');
                $regionValidation->setPromptTitle('Pilih dari daftar');
                $regionValidation->setPrompt('Pilih kecamatan');
                $regionValidation->setFormula1('"Siantar Utara,Siantar Selatan,Siantar Barat,Siantar Timur,Siantar Marihat,Siantar Martoba,Siantar Sitalasari,Siantar Marimbun"');

                // Terapkan validasi ke seluruh kolom
                $lastRow = 100; // Jumlah baris yang akan memiliki validasi
                $sheet->duplicateStyle($sheet->getStyle('A2'), 'A2:A' . $lastRow);
                $sheet->duplicateStyle($sheet->getStyle('E2'), 'E2:E' . $lastRow);
                $sheet->duplicateStyle($sheet->getStyle('F2'), 'F2:F' . $lastRow);
                $sheet->duplicateStyle($sheet->getStyle('L2'), 'L2:L' . $lastRow);

                // Pindahkan petunjuk ke sisi kanan, dua kolom setelah kolom paling kanan (kolom N)
                $startCol = 'N'; // Dua kolom setelah L
                $row = 2; // Mulai di bawah header
                $petunjuk = [
                    'PETUNJUK PENGGUNAAN:',
                    '1. Kolom AKSI: Wajib diisi dengan CREATE, UPDATE, atau DELETE',
                    '2. Kolom ID: Wajib diisi dan harus unik. Untuk UPDATE/DELETE cukup isi ID, kolom NPSN boleh dikosongkan.',
                    '3. Kolom NPSN: (Opsional) Bisa diisi untuk operasi UPDATE/DELETE, tapi cukup isi ID saja sudah cukup.',
                    '4. Kolom NAMA_SEKOLAH: Wajib diisi, minimal 3 karakter',
                    '5. Kolom JENJANG_PENDIDIKAN: Wajib diisi dengan nilai TK, SD, SMP, SMA, atau SMK',
                    '6. Kolom STATUS: Wajib diisi dengan nilai Negeri atau Swasta',
                    '7. Kolom ALAMAT: Wajib diisi',
                    '8. Kolom TELEPON, EMAIL, WEBSITE: Opsional',
                    '9. Kolom KEPALA_SEKOLAH: Wajib diisi',
                    '10. Kolom KECAMATAN: Wajib diisi dengan kecamatan yang valid',
                ];
                foreach ($petunjuk as $text) {
                    $sheet->setCellValue($startCol . $row, $text);
                    // Merge cell petunjuk saja (N sampai P)
                    $sheet->mergeCells($startCol . $row . ':P' . $row);
                    $sheet->getStyle($startCol . $row)->getFont()->setBold($row === 2);
                    $row++;
                }
                // Format wrap text untuk petunjuk
                $sheet->getStyle('N2:P' . ($row - 1))->getAlignment()->setWrapText(true);
            },
        ];
    }
}
