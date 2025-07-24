<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentTemplateExport implements WithHeadings, WithEvents, WithStyles
{
    public function headings(): array
    {
        return [
            'action',
            'nisn',
            'full_name',
            'gender',
            'birth_place',
            'birth_date',
            'religion',
            'grade_level',
            'student_status',
            'academic_year',
            'school_id',
        ];
    }

    public function registerEvents(): array
    {
        return [
            \Maatwebsite\Excel\Events\AfterSheet::class => function (\Maatwebsite\Excel\Events\AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->setCellValue('M2', 'PETUNJUK PENGGUNAAN:');
                $sheet->setCellValue('M3', '1. Kolom ACTION: CREATE, UPDATE, DELETE');
                $sheet->setCellValue('M4', '2. Kolom NISN: Wajib dan unik');
                $sheet->setCellValue('M5', '3. Kolom FULL_NAME: Wajib');
                $sheet->setCellValue('M6', '4. Kolom GENDER: Laki-laki/Perempuan');
                $sheet->setCellValue('M7', '5. Kolom GRADE_LEVEL: Wajib');
                $sheet->setCellValue('M8', '6. Kolom STUDENT_STATUS: Aktif/Tamat');
                $sheet->setCellValue('M9', '7. Kolom ACADEMIC_YEAR: Wajib');
                $sheet->setCellValue('M10', '8. Kolom SCHOOL_ID: Wajib untuk admin dinas, otomatis untuk admin sekolah');
            }
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
