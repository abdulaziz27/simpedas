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
            'ACTION',
            'NISN',
            'FULL_NAME',
            'GENDER',
            'BIRTH_PLACE',
            'BIRTH_DATE',
            'RELIGION',
            'GRADE_LEVEL',
            'STUDENT_STATUS',
            'ACADEMIC_YEAR',
            'SCHOOL_NPSN',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // ACTION
            'B' => 15, // NISN
            'C' => 30, // FULL_NAME
            'D' => 15, // GENDER
            'E' => 20, // BIRTH_PLACE
            'F' => 15, // BIRTH_DATE
            'G' => 20, // RELIGION
            'H' => 15, // GRADE_LEVEL
            'I' => 18, // STUDENT_STATUS
            'J' => 18, // ACADEMIC_YEAR
            'K' => 15, // SCHOOL_NPSN
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

                $genderValidation = $sheet->getCell('D2')->getDataValidation();
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

                $statusValidation = $sheet->getCell('I2')->getDataValidation();
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
                $sheet->duplicateStyle($sheet->getStyle('D2'), 'D2:D' . $lastRow);
                $sheet->duplicateStyle($sheet->getStyle('I2'), 'I2:I' . $lastRow);

                // Petunjuk penggunaan di kanan
                $startCol = 'M';
                $row = 2;
                $petunjuk = [
                    'PETUNJUK PENGGUNAAN:',
                    '1. Kolom ACTION: Wajib diisi dengan CREATE, UPDATE, atau DELETE',
                    '2. Kolom NISN: Wajib diisi dan harus unik. Untuk UPDATE/DELETE cukup isi NISN.',
                    '3. Kolom FULL_NAME: Wajib diisi',
                    '4. Kolom GENDER: Wajib diisi dengan Laki-laki atau Perempuan',
                    '5. Kolom GRADE_LEVEL: Wajib diisi',
                    '6. Kolom STUDENT_STATUS: Wajib diisi dengan Aktif atau Tamat',
                    '7. Kolom ACADEMIC_YEAR: Wajib diisi',
                    '8. Kolom SCHOOL_NPSN: Wajib diisi untuk admin dinas, otomatis untuk admin sekolah. Isi dengan NPSN sekolah (lihat menu sekolah)',
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
