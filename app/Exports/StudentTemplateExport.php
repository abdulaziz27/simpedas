<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class StudentTemplateExport implements WithHeadings, WithEvents, WithStyles, WithColumnWidths
{
    public function headings(): array
    {
        return [
            'AKSI',
            'NPSN_SEKOLAH',
            'NAMA_LENGKAP',
            'NISN',
            'JENIS_KELAMIN',
            'TEMPAT_LAHIR',
            'TANGGAL_LAHIR',
            'AGAMA',
            'TINGKAT_KELAS',
            'STATUS_SISWA',
            'TAHUN_AJARAN',
            'NAMA_ORANG_TUA',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // AKSI
            'B' => 15, // NPSN_SEKOLAH
            'C' => 30, // NAMA_LENGKAP
            'D' => 15, // NISN
            'E' => 15, // JENIS_KELAMIN
            'F' => 20, // TEMPAT_LAHIR
            'G' => 15, // TANGGAL_LAHIR
            'H' => 20, // AGAMA
            'I' => 15, // TINGKAT_KELAS
            'J' => 18, // STATUS_SISWA
            'K' => 18, // TAHUN_AJARAN
            'L' => 25, // NAMA_ORANG_TUA
            'M' => 60, // PETUNJUK
            'N' => 60,
            'O' => 60,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '125047'],
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

    public function registerEvents(): array
    {
        return [
            \Maatwebsite\Excel\Events\AfterSheet::class => function (\Maatwebsite\Excel\Events\AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Data validation dropdown
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

                $genderValidation = $sheet->getCell('E2')->getDataValidation();
                $genderValidation->setType(DataValidation::TYPE_LIST);
                $genderValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $genderValidation->setAllowBlank(false);
                $genderValidation->setShowInputMessage(true);
                $genderValidation->setShowErrorMessage(true);
                $genderValidation->setShowDropDown(true);
                $genderValidation->setErrorTitle('Input error');
                $genderValidation->setError('Nilai tidak valid. Pilih dari daftar.');
                $genderValidation->setPromptTitle('Pilih dari daftar');
                $genderValidation->setPrompt('Pilih Laki-laki atau Perempuan');
                $genderValidation->setFormula1('"Laki-laki,Perempuan"');

                $statusValidation = $sheet->getCell('J2')->getDataValidation();
                $statusValidation->setType(DataValidation::TYPE_LIST);
                $statusValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $statusValidation->setAllowBlank(false);
                $statusValidation->setShowInputMessage(true);
                $statusValidation->setShowErrorMessage(true);
                $statusValidation->setShowDropDown(true);
                $statusValidation->setErrorTitle('Input error');
                $statusValidation->setError('Nilai tidak valid. Pilih dari daftar.');
                $statusValidation->setPromptTitle('Pilih dari daftar');
                $statusValidation->setPrompt('Pilih Aktif atau Tamat');
                $statusValidation->setFormula1('"Aktif,Tamat"');

                // Terapkan validasi ke seluruh kolom (baris 2-100)
                $lastRow = 100;
                $sheet->duplicateStyle($sheet->getStyle('A2'), 'A2:A' . $lastRow);
                $sheet->duplicateStyle($sheet->getStyle('E2'), 'E2:E' . $lastRow);
                $sheet->duplicateStyle($sheet->getStyle('J2'), 'J2:J' . $lastRow);

                // Petunjuk penggunaan di kanan
                $startCol = 'M';
                $row = 2;
                $petunjuk = [
                    'PETUNJUK PENGGUNAAN:',
                    '1. Kolom AKSI: Wajib diisi dengan CREATE, UPDATE, atau DELETE',
                    '2. Kolom NISN: Wajib diisi dan harus unik. Untuk UPDATE/DELETE cukup isi NISN.',
                    '3. Kolom NAMA_LENGKAP: Wajib diisi',
                    '4. Kolom JENIS_KELAMIN: Wajib diisi dengan Laki-laki atau Perempuan',
                    '5. Kolom TINGKAT_KELAS: Wajib diisi',
                    '6. Kolom STATUS_SISWA: Wajib diisi dengan Aktif atau Tamat',
                    '7. Kolom TAHUN_AJARAN: Wajib diisi',
                    '8. Kolom NPSN_SEKOLAH: Wajib diisi untuk admin dinas, otomatis untuk admin sekolah. Isi dengan NPSN sekolah (lihat menu sekolah)',
                ];
                foreach ($petunjuk as $text) {
                    $sheet->setCellValue($startCol . $row, $text);
                    $sheet->mergeCells($startCol . $row . ':O' . $row);
                    $sheet->getStyle($startCol . $row)->getFont()->setBold($row === 2);
                    $row++;
                }
                $sheet->getStyle('M2:O' . ($row - 1))->getAlignment()->setWrapText(true);
            }
        ];
    }
}
