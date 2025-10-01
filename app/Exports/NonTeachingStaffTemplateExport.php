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

class NonTeachingStaffTemplateExport implements WithHeadings, WithEvents, WithStyles, WithColumnWidths
{
    public function headings(): array
    {
        return [
            'AKSI',
            'NPSN_SEKOLAH',
            'NAMA_LENGKAP',
            'NIP_NIK',
            'NUPTK',
            'JENIS_KELAMIN',
            'TEMPAT_LAHIR',
            'TANGGAL_LAHIR',
            'AGAMA',
            'ALAMAT',
            'JABATAN',
            'TINGKAT_PENDIDIKAN',
            'JURUSAN_PENDIDIKAN',
            'STATUS_KE_PEGAWAIAN',
            'PANGKAT',
            'TMT',
            'STATUS',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // AKSI
            'B' => 15, // NPSN_SEKOLAH
            'C' => 30, // NAMA_LENGKAP
            'D' => 20, // NIP_NIK
            'E' => 20, // NUPTK
            'F' => 15, // JENIS_KELAMIN
            'G' => 20, // TEMPAT_LAHIR
            'H' => 15, // TANGGAL_LAHIR
            'I' => 20, // AGAMA
            'J' => 40, // ALAMAT
            'K' => 25, // JABATAN
            'L' => 25, // TINGKAT_PENDIDIKAN
            'M' => 25, // JURUSAN_PENDIDIKAN
            'N' => 20, // STATUS_KE_PEGAWAIAN
            'O' => 20, // PANGKAT
            'P' => 15, // TMT
            'Q' => 15, // STATUS
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

                // Data validation dropdown (positions updated)
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

                $statusEmploymentValidation = $sheet->getCell('N2')->getDataValidation();
                $statusEmploymentValidation->setType(DataValidation::TYPE_LIST);
                $statusEmploymentValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $statusEmploymentValidation->setAllowBlank(false);
                $statusEmploymentValidation->setShowInputMessage(true);
                $statusEmploymentValidation->setShowErrorMessage(true);
                $statusEmploymentValidation->setShowDropDown(true);
                $statusEmploymentValidation->setErrorTitle('Input error');
                $statusEmploymentValidation->setError('Nilai tidak valid. Pilih dari daftar.');
                $statusEmploymentValidation->setPromptTitle('Pilih dari daftar');
                $statusEmploymentValidation->setPrompt('Pilih status kepegawaian');
                $statusEmploymentValidation->setFormula1('"PNS,PPPK,GTY,PTY"');

                $staffStatusValidation = $sheet->getCell('Q2')->getDataValidation();
                $staffStatusValidation->setType(DataValidation::TYPE_LIST);
                $staffStatusValidation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $staffStatusValidation->setAllowBlank(false);
                $staffStatusValidation->setShowInputMessage(true);
                $staffStatusValidation->setShowErrorMessage(true);
                $staffStatusValidation->setShowDropDown(true);
                $staffStatusValidation->setErrorTitle('Input error');
                $staffStatusValidation->setError('Nilai tidak valid. Pilih dari daftar.');
                $staffStatusValidation->setPromptTitle('Pilih dari daftar');
                $staffStatusValidation->setPrompt('Pilih status aktif');
                $staffStatusValidation->setFormula1('"Aktif,Tidak Aktif"');

                // Terapkan validasi ke seluruh kolom (baris 2-100)
                $lastRow = 100;
                $sheet->duplicateStyle($sheet->getStyle('A2'), 'A2:A' . $lastRow);
                $sheet->duplicateStyle($sheet->getStyle('F2'), 'F2:F' . $lastRow);
                $sheet->duplicateStyle($sheet->getStyle('I2'), 'I2:I' . $lastRow);
                $sheet->duplicateStyle($sheet->getStyle('N2'), 'N2:N' . $lastRow);
                $sheet->duplicateStyle($sheet->getStyle('Q2'), 'Q2:Q' . $lastRow);

                // Petunjuk penggunaan di kanan
                $startCol = 'S';
                $row = 2;
                $petunjuk = [
                    'PETUNJUK PENGGUNAAN:',
                    '1. Kolom AKSI: Wajib diisi dengan CREATE, UPDATE, atau DELETE',
                    '2. Kolom NPSN_SEKOLAH: Wajib diisi untuk admin dinas, otomatis untuk admin sekolah.',
                    '3. Kolom NAMA_LENGKAP: Wajib diisi',
                    '4. Kolom NIP_NIK: Wajib diisi dan harus unik. Untuk UPDATE/DELETE cukup isi NIP_NIK.',
                    '5. Kolom JENIS_KELAMIN: Wajib diisi',
                    '6. Kolom AGAMA: Wajib diisi',
                    '7. Kolom STATUS_KE_PEGAWAIAN: Wajib diisi',
                    '8. Kolom STATUS: Wajib diisi (Aktif/Tidak Aktif)',
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
