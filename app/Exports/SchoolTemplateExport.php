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
            'B' => 15, // NPSN
            'C' => 30, // NAMA_SEKOLAH
            'D' => 20, // JENJANG_PENDIDIKAN
            'E' => 15, // STATUS
            'F' => 40, // ALAMAT
            'G' => 20, // TELEPON
            'H' => 30, // EMAIL
            'I' => 30, // WEBSITE
            'J' => 30, // KEPALA_SEKOLAH
            'K' => 20, // KECAMATAN
            'M' => 60, // PETUNJUK (kolom utama)
            'N' => 60, // PETUNJUK (merge, biar wrap text optimal)
            'O' => 60, // PETUNJUK (merge, biar wrap text optimal)
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

                // 2. JENJANG_PENDIDIKAN (Column D) - TK, SD, SMP, Non Formal
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
                    $validation->setPrompt('Pilih TK, SD, SMP, atau Non Formal');
                    $validation->setFormula1('"TK,SD,SMP,Non Formal"');
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

                // 4. KECAMATAN (Column K) - Daftar kecamatan
                for ($row = 2; $row <= $lastRow; $row++) {
                    $validation = $sheet->getCell('K' . $row)->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(false);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setErrorTitle('Input error');
                    $validation->setError('Nilai tidak valid. Pilih kecamatan.');
                    $validation->setPromptTitle('Pilih Kecamatan');
                    $validation->setPrompt('Pilih kecamatan yang sesuai');
                    $validation->setFormula1('"Siantar Utara,Siantar Selatan,Siantar Barat,Siantar Timur,Siantar Marihat,Siantar Martoba,Siantar Sitalasari,Siantar Marimbun"');
                }

                // Pindahkan petunjuk ke sisi kanan, dua kolom setelah kolom paling kanan (kolom N)
                $startCol = 'M'; // Dua kolom setelah K
                $row = 2; // Mulai di bawah header
                $petunjuk = [
                    'PETUNJUK PENGGUNAAN:',
                    '1. Kolom AKSI: Wajib diisi dengan CREATE, UPDATE, atau DELETE (dropdown)',
                    '2. Kolom NPSN: Wajib diisi dan harus unik. Untuk UPDATE/DELETE cukup isi NPSN',
                    '3. Kolom NAMA_SEKOLAH: Wajib diisi, minimal 3 karakter',
                    '4. Kolom JENJANG_PENDIDIKAN: Wajib diisi dengan nilai TK, SD, SMP, atau Non Formal (dropdown)',
                    '5. Kolom STATUS: Wajib diisi dengan nilai Negeri atau Swasta (dropdown)',
                    '6. Kolom ALAMAT: Wajib diisi',
                    '7. Kolom TELEPON, EMAIL, WEBSITE: Opsional',
                    '8. Kolom KEPALA_SEKOLAH: Wajib diisi',
                    '9. Kolom KECAMATAN: Wajib diisi dengan kecamatan yang valid (dropdown)',
                    '',
                    'CATATAN:',
                    '• Dropdown tersedia di semua baris (2-1000)',
                    '• NPSN harus unik, tidak boleh duplikat',
                    '• Format email: user@domain.com',
                    '• Format website: https://www.example.com',
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
