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
            'NISN',
            'NIPD',
            'NAMA_LENGKAP',
            'JENIS_KELAMIN',
            'TEMPAT_LAHIR',
            'TANGGAL_LAHIR',
            'AGAMA',
            'ROMBEL',
            'STATUS_SISWA',
            'ALAMAT',
            'KELURAHAN',
            'KECAMATAN',
            'KODE_POS',
            'NAMA_AYAH',
            'PEKERJAAN_AYAH',
            'NAMA_IBU',
            'PEKERJAAN_IBU',
            'ANAK_KE',
            'JUMLAH_SAUDARA',
            'NO_HP',
            'KIP',
            'TRANSPORTASI',
            'JARAK_RUMAH_SEKOLAH',
            'TINGGI_BADAN',
            'BERAT_BADAN',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15, // AKSI
            'B' => 15, // NPSN_SEKOLAH
            'C' => 15, // NISN
            'D' => 15, // NIPD
            'E' => 30, // NAMA_LENGKAP
            'F' => 15, // JENIS_KELAMIN
            'G' => 20, // TEMPAT_LAHIR
            'H' => 15, // TANGGAL_LAHIR
            'I' => 20, // AGAMA
            'J' => 15, // ROMBEL
            'K' => 18, // STATUS_SISWA
            'L' => 30, // ALAMAT
            'M' => 20, // KELURAHAN
            'N' => 20, // KECAMATAN
            'O' => 12, // KODE_POS
            'P' => 25, // NAMA_AYAH
            'Q' => 20, // PEKERJAAN_AYAH
            'R' => 25, // NAMA_IBU
            'S' => 20, // PEKERJAAN_IBU
            'T' => 10, // ANAK_KE
            'U' => 15, // JUMLAH_SAUDARA
            'V' => 15, // NO_HP
            'W' => 10, // KIP
            'X' => 15, // TRANSPORTASI
            'Y' => 20, // JARAK_RUMAH_SEKOLAH
            'Z' => 12, // TINGGI_BADAN
            'AA' => 12, // BERAT_BADAN
            'AB' => 60, // PETUNJUK
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

                // 2. JENIS_KELAMIN (Column F) - L, P
                for ($row = 2; $row <= $lastRow; $row++) {
                    $validation = $sheet->getCell('F' . $row)->getDataValidation();
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

                // 4. STATUS_SISWA (Column K) - aktif, tamat, pindah
                for ($row = 2; $row <= $lastRow; $row++) {
                    $validation = $sheet->getCell('K' . $row)->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(true);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setErrorTitle('Input error');
                    $validation->setError('Nilai tidak valid. Pilih aktif, tamat, atau pindah.');
                    $validation->setPromptTitle('Pilih Status Siswa');
                    $validation->setPrompt('Pilih aktif, tamat, atau pindah (opsional, default: aktif)');
                    $validation->setFormula1('"aktif,tamat,pindah"');
                }

                // 5. KIP (Column W) - Ya, Tidak
                for ($row = 2; $row <= $lastRow; $row++) {
                    $validation = $sheet->getCell('W' . $row)->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(true);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setErrorTitle('Input error');
                    $validation->setError('Nilai tidak valid. Pilih Ya atau Tidak.');
                    $validation->setPromptTitle('Pilih KIP');
                    $validation->setPrompt('Pilih Ya atau Tidak (opsional)');
                    $validation->setFormula1('"Ya,Tidak"');
                }

                // Petunjuk penggunaan di kanan
                $startCol = 'AB';
                $row = 2;
                $petunjuk = [
                    'PETUNJUK PENGGUNAAN:',
                    '1. Kolom AKSI: Wajib diisi dengan CREATE, UPDATE, atau DELETE (dropdown)',
                    '2. Kolom NISN: Wajib diisi dan harus unik. Untuk UPDATE/DELETE cukup isi NISN.',
                    '3. Kolom NAMA_LENGKAP: Wajib diisi',
                    '4. Kolom JENIS_KELAMIN: Wajib diisi dengan L atau P (dropdown)',
                    '5. Kolom TEMPAT_LAHIR: Wajib diisi',
                    '6. Kolom TANGGAL_LAHIR: Wajib diisi (format: YYYY-MM-DD)',
                    '7. Kolom AGAMA: Wajib diisi (dropdown: Islam, Kristen, Katolik/Katholik, Hindu, Buddha, Konghucu)',
                    '8. Kolom ROMBEL: Wajib diisi (contoh: 6A, 9B, 12 IPA 1)',
                    '9. Kolom STATUS_SISWA: Opsional (dropdown: aktif, tamat, pindah)',
                    '10. Kolom NPSN_SEKOLAH: Wajib diisi untuk admin dinas, otomatis untuk admin sekolah',
                    '11. Kolom opsional: NIPD, ALAMAT, KELURAHAN, KECAMATAN, KODE_POS',
                    '12. Data keluarga: NAMA_AYAH, PEKERJAAN_AYAH, NAMA_IBU, PEKERJAAN_IBU, ANAK_KE, JUMLAH_SAUDARA',
                    '13. Kontak: NO_HP, KIP (dropdown: Ya/Tidak), TRANSPORTASI, JARAK_RUMAH_SEKOLAH',
                    '14. Kesehatan: TINGGI_BADAN (cm), BERAT_BADAN (kg)',
                    '',
                    'CATATAN:',
                    '• Dropdown tersedia di semua baris (2-1000)',
                    '• Format tanggal: YYYY-MM-DD (contoh: 2014-01-15)',
                    '• NISN harus unik, tidak boleh duplikat',
                    '• Admin sekolah tidak perlu isi NPSN_SEKOLAH',
                    '• Agama: Bisa tulis "Katolik" atau "Katholik", sistem akan otomatis normalisasi',
                ];
                foreach ($petunjuk as $text) {
                    $sheet->setCellValue($startCol . $row, $text);
                    $sheet->mergeCells($startCol . $row . ':AE' . $row);
                    $sheet->getStyle($startCol . $row)->getFont()->setBold($row === 2);
                    $row++;
                }
                $sheet->getStyle('AB2:AE' . ($row - 1))->getAlignment()->setWrapText(true);
            }
        ];
    }
}
