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
            'EMAIL',
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
            'D' => 30, // EMAIL
            'E' => 15, // NUPTK
            'F' => 15, // NIP
            'G' => 15, // JENIS_KELAMIN
            'H' => 20, // TEMPAT_LAHIR
            'I' => 15, // TANGGAL_LAHIR
            'J' => 20, // AGAMA
            'K' => 40, // ALAMAT
            'L' => 20, // TELEPON
            'M' => 25, // TINGKAT_PENDIDIKAN
            'N' => 25, // JURUSAN_PENDIDIKAN
            'O' => 30, // MATA_PELAJARAN
            'P' => 20, // STATUS_KE_PEGAWAIAN
            'Q' => 20, // PANGKAT
            'R' => 25, // JABATAN
            'T' => 60, // PETUNJUK
            'U' => 60,
            'V' => 60,
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

                // 2. JENIS_KELAMIN (Column G) - Laki-laki, Perempuan
                for ($row = 2; $row <= $lastRow; $row++) {
                    $validation = $sheet->getCell('G' . $row)->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(false);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setErrorTitle('Input error');
                    $validation->setError('Nilai tidak valid. Pilih Laki-laki atau Perempuan.');
                    $validation->setPromptTitle('Pilih Jenis Kelamin');
                    $validation->setPrompt('Pilih Laki-laki atau Perempuan');
                    $validation->setFormula1('"Laki-laki,Perempuan"');
                }

                // 3. AGAMA (Column J) - Islam, Kristen, Katolik, Hindu, Buddha, Konghucu
                for ($row = 2; $row <= $lastRow; $row++) {
                    $validation = $sheet->getCell('J' . $row)->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(false);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setErrorTitle('Input error');
                    $validation->setError('Nilai tidak valid. Pilih dari daftar agama.');
                    $validation->setPromptTitle('Pilih Agama');
                    $validation->setPrompt('Pilih agama yang sesuai');
                    $validation->setFormula1('"Islam,Kristen,Katolik,Hindu,Buddha,Konghucu"');
                }

                // 4. STATUS_KE_PEGAWAIAN (Column P) - PNS, PPPK, GTY, PTY
                for ($row = 2; $row <= $lastRow; $row++) {
                    $validation = $sheet->getCell('P' . $row)->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(false);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setErrorTitle('Input error');
                    $validation->setError('Nilai tidak valid. Pilih status kepegawaian.');
                    $validation->setPromptTitle('Pilih Status Kepegawaian');
                    $validation->setPrompt('Pilih PNS, PPPK, GTY, atau PTY');
                    $validation->setFormula1('"PNS,PPPK,GTY,PTY"');
                }

                // Petunjuk penggunaan di kanan
                $startCol = 'T';
                $row = 2;
                $petunjuk = [
                    'PETUNJUK PENGGUNAAN:',
                    '1. Kolom AKSI: Wajib diisi dengan CREATE, UPDATE, atau DELETE (dropdown)',
                    '2. Kolom NPSN_SEKOLAH: Wajib diisi untuk admin dinas, otomatis untuk admin sekolah',
                    '3. Kolom NAMA_LENGKAP: Wajib diisi',
                    '4. Kolom EMAIL: Wajib diisi dan valid untuk akun login guru',
                    '5. Kolom NUPTK: Wajib diisi (unik) untuk identifikasi',
                    '6. Kolom JENIS_KELAMIN: Wajib diisi dengan Laki-laki atau Perempuan (dropdown)',
                    '7. Kolom AGAMA: Wajib diisi dengan agama yang valid (dropdown)',
                    '8. Kolom STATUS_KE_PEGAWAIAN: Wajib diisi dengan status kepegawaian (dropdown)',
                    '',
                    'CATATAN:',
                    '• Dropdown tersedia di semua baris (2-1000)',
                    '• Format tanggal: YYYY-MM-DD (contoh: 1990-05-15)',
                    '• NUPTK harus unik, tidak boleh duplikat',
                    '• Admin sekolah tidak perlu isi NPSN_SEKOLAH',
                ];
                foreach ($petunjuk as $text) {
                    $sheet->setCellValue($startCol . $row, $text);
                    $sheet->mergeCells($startCol . $row . ':V' . $row);
                    $sheet->getStyle($startCol . $row)->getFont()->setBold($row === 2);
                    $row++;
                }
                $sheet->getStyle('T2:V' . ($row - 1))->getAlignment()->setWrapText(true);
            }
        ];
    }
}
