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

class TeacherTemplateExport implements WithHeadings, WithEvents, WithStyles, WithColumnWidths
{
    public function headings(): array
    {
        return [
            'AKSI',
            'NPSN_SEKOLAH',
            'NAMA_LENGKAP',
            'NUPTK',
            'NIP',
            'JENIS_KELAMIN',
            'TEMPAT_LAHIR',
            'TANGGAL_LAHIR',
            'AGAMA',
            'ALAMAT',
            'TELEPON',
            'TINGKAT_PENDIDIKAN',
            'JURUSAN_PENDIDIKAN',
            'MATA_PELAJARAN',
            'STATUS_KE_PEGAWAIAN',
            'PANGKAT',
            'JABATAN',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // AKSI
            'B' => 15, // NPSN_SEKOLAH
            'C' => 30, // NAMA_LENGKAP
            'D' => 15, // NUPTK
            'E' => 15, // NIP
            'F' => 15, // JENIS_KELAMIN
            'G' => 20, // TEMPAT_LAHIR
            'H' => 15, // TANGGAL_LAHIR
            'I' => 20, // AGAMA
            'J' => 40, // ALAMAT
            'K' => 20, // TELEPON
            'L' => 25, // TINGKAT_PENDIDIKAN
            'M' => 25, // JURUSAN_PENDIDIKAN
            'N' => 30, // MATA_PELAJARAN
            'O' => 20, // STATUS_KE_PEGAWAIAN
            'P' => 20, // PANGKAT
            'Q' => 25, // JABATAN
            'S' => 60, // PETUNJUK
            'T' => 60,
            'U' => 60,
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

                $genderValidation = $sheet->getCell('F2')->getDataValidation();
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

                $religionValidation = $sheet->getCell('I2')->getDataValidation();
                $religionValidation->setType(DataValidation::TYPE_LIST);
                $religionValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $religionValidation->setAllowBlank(false);
                $religionValidation->setShowInputMessage(true);
                $religionValidation->setShowErrorMessage(true);
                $religionValidation->setShowDropDown(true);
                $religionValidation->setErrorTitle('Input error');
                $religionValidation->setError('Nilai tidak valid. Pilih dari daftar.');
                $religionValidation->setPromptTitle('Pilih dari daftar');
                $religionValidation->setPrompt('Pilih agama');
                $religionValidation->setFormula1('"Islam,Kristen,Katolik,Hindu,Buddha,Konghucu"');

                $statusValidation = $sheet->getCell('O2')->getDataValidation();
                $statusValidation->setType(DataValidation::TYPE_LIST);
                $statusValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $statusValidation->setAllowBlank(false);
                $statusValidation->setShowInputMessage(true);
                $statusValidation->setShowErrorMessage(true);
                $statusValidation->setShowDropDown(true);
                $statusValidation->setErrorTitle('Input error');
                $statusValidation->setError('Nilai tidak valid. Pilih dari daftar.');
                $statusValidation->setPromptTitle('Pilih dari daftar');
                $statusValidation->setPrompt('Pilih status kepegawaian');
                $statusValidation->setFormula1('"PNS,PPPK,GTY,PTY"');

                // Terapkan validasi ke seluruh kolom (baris 2-100)
                $lastRow = 100;
                $sheet->duplicateStyle($sheet->getStyle('A2'), 'A2:A' . $lastRow);
                $sheet->duplicateStyle($sheet->getStyle('F2'), 'F2:F' . $lastRow);
                $sheet->duplicateStyle($sheet->getStyle('I2'), 'I2:I' . $lastRow);
                $sheet->duplicateStyle($sheet->getStyle('O2'), 'O2:O' . $lastRow);

                // Petunjuk penggunaan di kanan
                $startCol = 'S';
                $row = 2;
                $petunjuk = [
                    'PETUNJUK PENGGUNAAN:',
                    '1. Kolom AKSI: Wajib diisi dengan CREATE, UPDATE, atau DELETE',
                    '2. Kolom NPSN_SEKOLAH: Wajib diisi untuk admin dinas, otomatis untuk admin sekolah.',
                    '3. Kolom NAMA_LENGKAP: Wajib diisi',
                    '4. Kolom NUPTK: Wajib diisi dan harus unik. Untuk UPDATE/DELETE cukup isi NUPTK.',
                    '5. Kolom JENIS_KELAMIN: Wajib diisi dengan Laki-laki atau Perempuan',
                    '6. Kolom AGAMA: Wajib diisi dengan agama yang valid',
                    '7. Kolom STATUS_KE_PEGAWAIAN: Wajib diisi dengan status kepegawaian',
                ];
                foreach ($petunjuk as $text) {
                    $sheet->setCellValue($startCol . $row, $text);
                    $sheet->mergeCells($startCol . $row . ':U' . $row);
                    $sheet->getStyle($startCol . $row)->getFont()->setBold($row === 2);
                    $row++;
                }
                $sheet->getStyle('S2:U' . ($row - 1))->getAlignment()->setWrapText(true);
            }
        ];
    }
}
