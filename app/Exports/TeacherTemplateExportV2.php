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

class TeacherTemplateExportV2 implements WithHeadings, WithEvents, WithStyles, WithColumnWidths
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
            'TMT',
            'STATUS',
            'EMAIL',
            'PASSWORD_GURU',
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
            'R' => 15, // TMT
            'S' => 15, // STATUS
            'T' => 30, // EMAIL
            'U' => 25, // PASSWORD_GURU
            'V' => 80, // PETUNJUK - Lebar untuk petunjuk
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

                // 2. JENIS_KELAMIN (Column F) - Laki-laki, Perempuan
                for ($row = 2; $row <= $lastRow; $row++) {
                    $validation = $sheet->getCell('F' . $row)->getDataValidation();
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

                // 3. AGAMA (Column I) - Islam, Kristen, Katolik, Hindu, Buddha, Konghucu
                for ($row = 2; $row <= $lastRow; $row++) {
                    $validation = $sheet->getCell('I' . $row)->getDataValidation();
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

                // 4. STATUS_KE_PEGAWAIAN (Column O) - PNS, PPPK, GTY, PTY
                for ($row = 2; $row <= $lastRow; $row++) {
                    $validation = $sheet->getCell('O' . $row)->getDataValidation();
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

                // 5. STATUS (Column S) - Aktif, Tidak Aktif
                for ($row = 2; $row <= $lastRow; $row++) {
                    $validation = $sheet->getCell('S' . $row)->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(false);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setErrorTitle('Input error');
                    $validation->setError('Nilai tidak valid. Pilih status aktif.');
                    $validation->setPromptTitle('Pilih Status');
                    $validation->setPrompt('Pilih Aktif atau Tidak Aktif');
                    $validation->setFormula1('"Aktif,Tidak Aktif"');
                }

                // Petunjuk penggunaan di kolom V (setelah PASSWORD_GURU di kolom U)
                $startCol = 'V';
                $row = 2;
                $petunjuk = [
                    'PETUNJUK PENGGUNAAN:',
                    '1. Kolom AKSI: Wajib diisi dengan CREATE, UPDATE, atau DELETE (dropdown)',
                    '2. Kolom NPSN_SEKOLAH: Wajib diisi untuk admin dinas, otomatis untuk admin sekolah',
                    '3. Kolom NAMA_LENGKAP: Wajib diisi',
                    '4. Kolom NUPTK: Wajib diisi (unik) untuk identifikasi',
                    '5. Kolom JENIS_KELAMIN: Wajib diisi dengan Laki-laki atau Perempuan (dropdown)',
                    '6. Kolom TEMPAT_LAHIR: Wajib diisi',
                    '7. Kolom TANGGAL_LAHIR: Wajib diisi (format: YYYY-MM-DD)',
                    '8. Kolom AGAMA: Wajib diisi dengan agama yang valid (dropdown)',
                    '9. Kolom STATUS_KE_PEGAWAIAN: Wajib diisi dengan status kepegawaian (dropdown)',
                    '10. Kolom STATUS: Wajib diisi dengan Aktif atau Tidak Aktif (dropdown)',
                    '11. Kolom EMAIL: Opsional, untuk akun login guru',
                    '12. Kolom PASSWORD_GURU: Opsional, untuk akun login guru',
                    '',
                    'CATATAN:',
                    '• Dropdown tersedia di semua baris (2-1000)',
                    '• Format tanggal: YYYY-MM-DD (contoh: 1990-05-15)',
                    '• NUPTK harus unik, tidak boleh duplikat',
                    '• Admin sekolah tidak perlu isi NPSN_SEKOLAH',
                    '• Jika EMAIL dan PASSWORD_GURU diisi, akan otomatis buat akun login',
                ];
                
                foreach ($petunjuk as $text) {
                    $sheet->setCellValue($startCol . $row, $text);
                    $sheet->getStyle($startCol . $row)->getFont()->setBold($row === 2);
                    $row++;
                }
                
                // Set wrap text untuk kolom petunjuk
                $sheet->getStyle('V2:V' . ($row - 1))->getAlignment()->setWrapText(true);
                $sheet->getStyle('V2:V' . ($row - 1))->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
            }
        ];
    }
}
