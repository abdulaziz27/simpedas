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
            'NUPTK',
            'NIP',
            'NAMA_LENGKAP',
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
            'NPSN_SEKOLAH',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // AKSI
            'B' => 15, // NUPTK
            'C' => 15, // NIP
            'D' => 30, // NAMA_LENGKAP
            'E' => 15, // JENIS_KELAMIN
            'F' => 20, // TEMPAT_LAHIR
            'G' => 15, // TANGGAL_LAHIR
            'H' => 20, // AGAMA
            'I' => 40, // ALAMAT
            'J' => 20, // TELEPON
            'K' => 25, // TINGKAT_PENDIDIKAN
            'L' => 25, // JURUSAN_PENDIDIKAN
            'M' => 30, // MATA_PELAJARAN
            'N' => 20, // STATUS_KE_PEGAWAIAN
            'O' => 20, // PANGKAT
            'P' => 25, // JABATAN
            'Q' => 15, // NPSN_SEKOLAH
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

                $religionValidation = $sheet->getCell('H2')->getDataValidation();
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

                $statusValidation = $sheet->getCell('N2')->getDataValidation();
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
                $sheet->duplicateStyle($sheet->getStyle('E2'), 'E2:E' . $lastRow);
                $sheet->duplicateStyle($sheet->getStyle('H2'), 'H2:H' . $lastRow);
                $sheet->duplicateStyle($sheet->getStyle('N2'), 'N2:N' . $lastRow);

                // Petunjuk penggunaan di kanan
                $startCol = 'S';
                $row = 2;
                $petunjuk = [
                    'PETUNJUK PENGGUNAAN:',
                    '1. Kolom AKSI: Wajib diisi dengan CREATE, UPDATE, atau DELETE',
                    '2. Kolom NUPTK: Wajib diisi dan harus unik. Untuk UPDATE/DELETE cukup isi NUPTK.',
                    '3. Kolom NIP: Opsional',
                    '4. Kolom NAMA_LENGKAP: Wajib diisi',
                    '5. Kolom JENIS_KELAMIN: Wajib diisi dengan Laki-laki atau Perempuan',
                    '6. Kolom AGAMA: Wajib diisi dengan agama yang valid',
                    '7. Kolom STATUS_KE_PEGAWAIAN: Wajib diisi dengan status kepegawaian',
                    '8. Kolom NPSN_SEKOLAH: Wajib diisi untuk admin dinas, otomatis untuk admin sekolah. Isi dengan NPSN sekolah',
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
