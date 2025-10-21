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
            'NAMA',
            'NUPTK',
            'JK',
            'TEMPAT_LAHIR',
            'TANGGAL_LAHIR',
            'NIP',
            'STATUS_KEPEGAWAIAN',
            'JENIS_PTK',
            'GELAR_DEPAN',
            'GELAR_BELAKANG',
            'JENJANG',
            'JURUSAN_PRODI',
            'SERTIFIKASI',
            'TMT_KERJA',
            'TUGAS_TAMBAHAN',
            'MENGAJAR',
            'JAM_TUGAS_TAMBAHAN',
            'JJM',
            'TOTAL_JJM',
            'SISWA',
            'KOMPETENSI',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // AKSI
            'B' => 15, // NPSN_SEKOLAH
            'C' => 30, // NAMA
            'D' => 20, // NUPTK
            'E' => 5,  // JK
            'F' => 20, // TEMPAT_LAHIR
            'G' => 15, // TANGGAL_LAHIR
            'H' => 20, // NIP
            'I' => 15, // STATUS_KEPEGAWAIAN
            'J' => 15, // JENIS_PTK
            'L' => 10, // GELAR_DEPAN
            'M' => 10, // GELAR_BELAKANG
            'N' => 10, // JENJANG
            'O' => 25, // JURUSAN_PRODI
            'P' => 25, // SERTIFIKASI
            'Q' => 15, // TMT_KERJA
            'R' => 30, // TUGAS_TAMBAHAN
            'S' => 25, // MENGAJAR
            'T' => 10, // JAM_TUGAS_TAMBAHAN
            'U' => 10, // JJM
            'V' => 10, // TOTAL_JJM
            'W' => 10, // SISWA
            'X' => 30, // KOMPETENSI
            'Y' => 80, // PETUNJUK
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

                // 2. JK (Column E) - L, P
                for ($row = 2; $row <= $lastRow; $row++) {
                    $validation = $sheet->getCell('E' . $row)->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(false);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setErrorTitle('Input error');
                    $validation->setError('Nilai tidak valid. Pilih L atau P.');
                    $validation->setPromptTitle('Pilih Jenis Kelamin');
                    $validation->setPrompt('Pilih L untuk Laki-laki atau P untuk Perempuan');
                    $validation->setFormula1('"L,P"');
                }

                // Remove strict validations - let users input freely
                // Only keep basic validations for critical fields if needed

                // Petunjuk penggunaan di kolom Y
                $startCol = 'Y';
                $row = 2;
                $petunjuk = [
                    'PETUNJUK PENGGUNAAN TEMPLATE DAPODIK:',
                    '1. AKSI: Wajib diisi dengan CREATE, UPDATE, atau DELETE (dropdown)',
                    '2. NPSN_SEKOLAH: Wajib untuk admin dinas, otomatis untuk admin sekolah',
                    '3. NAMA: Wajib diisi - nama lengkap guru',
                    '4. NUPTK: Opsional untuk CREATE (wajib untuk UPDATE/DELETE sebagai identifier)',
                    '5. JK: Wajib diisi dengan L atau P (dropdown)',
                    '6. TEMPAT_LAHIR: Wajib diisi',
                    '7. TANGGAL_LAHIR: Format YYYY-MM-DD (contoh: 1985-07-20)',
                    '8. NIP: Opsional - Nomor Induk Pegawai',
                    '9. STATUS_KEPEGAWAIAN: Contoh: PNS, PPPK, GTY, PTY',
                    '10. JENIS_PTK: Contoh: Guru, Kepala Sekolah, Wakil Kepala Sekolah',
                    '12. GELAR_DEPAN: Contoh: Drs., Dr., Prof.',
                    '13. GELAR_BELAKANG: Contoh: S.Pd., M.Pd., S.Mers',
                    '14. JENJANG: Contoh: S1, S2, S3, D3, D4',
                    '15. JURUSAN_PRODI: Jurusan/Program Studi pendidikan',
                    '16. SERTIFIKASI: Mata pelajaran yang disertifikasi',
                    '17. TMT_KERJA: Tanggal Mulai Tugas (YYYY-MM-DD)',
                    '18. TUGAS_TAMBAHAN: Text bebas tugas tambahan',
                    '19. MENGAJAR: Mata pelajaran yang diajar',
                    '20. JAM_TUGAS_TAMBAHAN: Angka jam tugas tambahan',
                    '21. JJM: Jam Jaminan Mengajar (angka)',
                    '22. TOTAL_JJM: Total Jam Jaminan Mengajar (angka)',
                    '23. SISWA: Jumlah siswa yang diajar (angka)',
                    '24. KOMPETENSI: Text bebas kompetensi guru',
                    '',
                    'CATATAN PENTING:',
                    '• Field wajib: NAMA saja (NUPTK opsional untuk CREATE, wajib untuk UPDATE/DELETE)',
                    '• Semua field lain opsional dan bisa diisi bebas',
                    '• Tidak ada batasan enum - input sesuai data Dapodik',
                    '• Format tanggal: YYYY-MM-DD',
                    '• NUPTK harus unik per guru',
                ];
                
                foreach ($petunjuk as $text) {
                    $sheet->setCellValue($startCol . $row, $text);
                    $sheet->getStyle($startCol . $row)->getFont()->setBold($row === 2);
                    $row++;
                }
                
                // Set wrap text untuk kolom petunjuk
                $sheet->getStyle('Y2:Y' . ($row - 1))->getAlignment()->setWrapText(true);
                $sheet->getStyle('Y2:Y' . ($row - 1))->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
            }
        ];
    }
}
