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
            'NIP_NIK',
            'NUPTK',
            'NAMA_LENGKAP',
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
            'NPSN_SEKOLAH',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // AKSI
            'B' => 15, // NIP_NIK
            'C' => 15, // NUPTK
            'D' => 30, // NAMA_LENGKAP
            'E' => 15, // JENIS_KELAMIN
            'F' => 20, // TEMPAT_LAHIR
            'G' => 15, // TANGGAL_LAHIR
            'H' => 20, // AGAMA
            'I' => 40, // ALAMAT
            'J' => 25, // JABATAN
            'K' => 25, // TINGKAT_PENDIDIKAN
            'L' => 25, // JURUSAN_PENDIDIKAN
            'M' => 20, // STATUS_KE_PEGAWAIAN
            'N' => 20, // PANGKAT
            'O' => 15, // TMT
            'P' => 15, // STATUS
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

                $statusValidation = $sheet->getCell('M2')->getDataValidation();
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

                $staffStatusValidation = $sheet->getCell('P2')->getDataValidation();
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
                $sheet->duplicateStyle($sheet->getStyle('E2'), 'E2:E' . $lastRow);
                $sheet->duplicateStyle($sheet->getStyle('H2'), 'H2:H' . $lastRow);
                $sheet->duplicateStyle($sheet->getStyle('M2'), 'M2:M' . $lastRow);
                $sheet->duplicateStyle($sheet->getStyle('P2'), 'P2:P' . $lastRow);

                // Petunjuk penggunaan di kanan
                $startCol = 'S';
                $row = 2;
                $petunjuk = [
                    'PETUNJUK PENGGUNAAN:',
                    '1. Kolom AKSI: Wajib diisi dengan CREATE, UPDATE, atau DELETE',
                    '2. Kolom NIP_NIK: Wajib diisi dan harus unik. Untuk UPDATE/DELETE cukup isi NIP_NIK.',
                    '3. Kolom NUPTK: Opsional',
                    '4. Kolom NAMA_LENGKAP: Wajib diisi',
                    '5. Kolom JENIS_KELAMIN: Wajib diisi dengan Laki-laki atau Perempuan',
                    '6. Kolom AGAMA: Wajib diisi dengan agama yang valid',
                    '7. Kolom JABATAN: Wajib diisi',
                    '8. Kolom STATUS_KE_PEGAWAIAN: Wajib diisi dengan status kepegawaian',
                    '9. Kolom STATUS: Wajib diisi dengan Aktif atau Tidak Aktif',
                    '10. Kolom NPSN_SEKOLAH: Wajib diisi untuk admin dinas, otomatis untuk admin sekolah. Isi dengan NPSN sekolah',
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
