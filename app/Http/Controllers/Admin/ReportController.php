<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\NonTeachingStaff;
use App\Models\StudentReport;
use App\Exports\TeacherReportExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ReportController extends Controller
{
    /**
     * Display reports dashboard
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('admin_dinas')) {
            return view('admin.reports.index-dinas');
        } elseif ($user->hasRole('admin_sekolah')) {
            return view('admin.reports.index-sekolah');
        }

        return redirect()->route('home');
    }

    /**
     * Laporan Rekap Sekolah (Admin Dinas)
     */
    public function schoolsReport()
    {
        $this->authorizeRole('admin_dinas');

        // Data sekolah dengan pagination
        $schools = School::withCount(['teachers', 'students', 'nonTeachingStaff'])
            ->with(['students' => function ($query) {
                $query->select('sekolah_id', 'status_siswa', DB::raw('count(*) as total'))
                    ->groupBy('sekolah_id', 'status_siswa');
            }])
            ->paginate(10);

        // Statistik terpisah untuk setiap kategori
        $totalSchools = School::count();
        $negeriSchools = School::where('status', 'Negeri')->count();
        $swastaSchools = School::where('status', 'Swasta')->count();

        // Statistik berdasarkan jenjang
        $tkSchools = School::where('education_level', 'TK')->count();
        $sdSchools = School::where('education_level', 'SD')->count();
        $smpSchools = School::where('education_level', 'SMP')->count();
        $nonFormalSchools = School::where('education_level', 'Non Formal')->count();

        return view('admin.reports.schools', compact(
            'schools',
            'totalSchools',
            'negeriSchools',
            'swastaSchools',
            'tkSchools',
            'sdSchools',
            'smpSchools',
            'nonFormalSchools'
        ));
    }

    /**
     * Laporan Statistik Guru (Admin Dinas)
     */
    public function teachersReport()
    {
        $this->authorizeRole('admin_dinas');

        // Data guru per sekolah dengan detail status kepegawaian
        $schoolsWithTeachers = School::withCount([
            'teachers as total_pns' => function ($query) {
                $query->where('employment_status', 'PNS');
            },
            'teachers as total_pppk' => function ($query) {
                $query->where('employment_status', 'PPPK');
            },
            'teachers as total_honorer' => function ($query) {
                $query->where('employment_status', 'Honorer');
            },
            'teachers as total_pty' => function ($query) {
                $query->where('employment_status', 'PTY');
            },
            'teachers as total_kontrak' => function ($query) {
                $query->where('employment_status', 'Kontrak');
            }
        ])
            ->having('total_pns', '>', 0)
            ->orHaving('total_pppk', '>', 0)
            ->orHaving('total_honorer', '>', 0)
            ->orHaving('total_pty', '>', 0)
            ->orHaving('total_kontrak', '>', 0)
            ->orderBy('name')
            ->paginate(10);

        // Statistik total guru
        $totalTeachers = Teacher::count();
        $totalPNS = Teacher::where('employment_status', 'PNS')->count();
        $totalPPPK = Teacher::where('employment_status', 'PPPK')->count();
        $totalHonorer = Teacher::where('employment_status', 'Honorer')->count();
        $totalPTY = Teacher::where('employment_status', 'PTY')->count();
        $totalKontrak = Teacher::where('employment_status', 'Kontrak')->count();

        return view('admin.reports.teachers', compact(
            'schoolsWithTeachers',
            'totalTeachers',
            'totalPNS',
            'totalPPPK',
            'totalHonorer',
            'totalPTY',
            'totalKontrak'
        ));
    }

    /**
     * Export Laporan Guru ke Excel
     */
    public function exportTeachers()
    {
        try {
            $this->authorizeRole('admin_dinas');

            // Set memory limit and timeout for exports
            ini_set('memory_limit', '512M');
            set_time_limit(300); // 5 minutes

            $filename = 'Laporan_Guru_' . date('Y-m-d_H-i-s') . '.xlsx';

            \Log::info('Exporting teachers report to Excel');

            // Get teachers data
            $teachers = Teacher::with('school')
                ->select('school_id', 'employment_status', 'education_level', 'full_name', 'nip', 'phone')
                ->orderBy('school_id')
                ->orderBy('employment_status')
                ->orderBy('education_level')
                ->orderBy('full_name')
                ->get();

            \Log::info('Teachers loaded: ' . $teachers->count());

            // Create new Spreadsheet
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set metadata spreadsheet
            $spreadsheet->getProperties()
                ->setCreator('SIMPeDAS')
                ->setLastModifiedBy('SIMPeDAS')
                ->setTitle('Laporan Guru')
                ->setSubject('Data Guru')
                ->setDescription('Dibuat oleh Sistem Informasi Manajemen Pendidikan Dasar');

            // Headers
            $headers = ['NO', 'NAMA SEKOLAH', 'NPSN', 'NAMA LENGKAP GURU', 'NIP', 'STATUS KEPEGAWAIAN', 'TINGKAT PENDIDIKAN', 'NO. TELEPON'];
            $lastCol = 'H';

            // Set headers
            foreach ($headers as $index => $header) {
                $sheet->setCellValue(chr(65 + $index) . '1', $header);
            }

            // Styling header
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '136E67']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ];
            $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray($headerStyle);
            $sheet->getRowDimension(1)->setRowHeight(30);

            // Set column widths
            $columnWidths = [
                'A' => 5,   // NO
                'B' => 35,  // NAMA SEKOLAH
                'C' => 15,  // NPSN
                'D' => 30,  // NAMA LENGKAP GURU
                'E' => 20,  // NIP
                'F' => 20,  // STATUS KEPEGAWAIAN
                'G' => 20,  // TINGKAT PENDIDIKAN
                'H' => 15   // NO. TELEPON
            ];
            foreach ($columnWidths as $col => $width) {
                $sheet->getColumnDimension($col)->setWidth($width);
            }

            // Data rows
            $row = 2;
            $no = 1;

            foreach ($teachers as $teacher) {
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $teacher->school->name ?? 'Tidak Diketahui');
                $sheet->setCellValue('C' . $row, $teacher->school->npsn ?? '');
                $sheet->setCellValue('D' . $row, $teacher->full_name ?? '');

                // Set format NIP sebagai text terlebih dahulu, lalu isi data
                $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('E' . $row, $teacher->nip ?? '');

                $sheet->setCellValue('F' . $row, $teacher->employment_status ?? '');
                $sheet->setCellValue('G' . $row, $teacher->education_level ?? '');

                // Set format Phone sebagai text terlebih dahulu, lalu isi data
                $sheet->getStyle('H' . $row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                $sheet->setCellValue('H' . $row, $teacher->phone ?? '');

                // Warna baris selang-seling
                if ($row % 2 == 0) {
                    $sheet->getStyle('A' . $row . ':H' . $row)->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('F8F9FA');
                }

                $row++;
            }

            // Set border untuk semua data
            $sheet->getStyle('A1:H' . ($row - 1))->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ]);

            // Set format NIP dan Phone sebagai text untuk seluruh kolom (redundant but safe)
            $sheet->getStyle('E2:E' . ($row - 1))->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
            $sheet->getStyle('H2:H' . ($row - 1))->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);

            // Set auto-filter
            $sheet->setAutoFilter('A1:H' . ($row - 1));

            // Freeze panes (header tetap saat scroll)
            $sheet->freezePane('A2');

            // Create writer
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

            // Set headers for download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            // Save to output
            $writer->save('php://output');
            exit();
        } catch (\Exception $e) {
            \Log::error('Error exporting teachers report: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat export: ' . $e->getMessage());
        }
    }

    /**
     * Laporan Statistik Siswa (Admin Dinas)
     */
    public function studentsReport()
    {
        $this->authorizeRole('admin_dinas');

        // Data siswa per sekolah dengan format yang diminta
        $schoolsWithStudents = School::withCount([
            'students as total_aktif' => function ($query) {
                $query->where('status_siswa', 'aktif');
            },
            'students as total_tamat' => function ($query) {
                $query->where('status_siswa', 'tamat');
            }
        ])
            ->having('total_aktif', '>', 0)
            ->orHaving('total_tamat', '>', 0)
            ->orderBy('name')
            ->paginate(10);

        // Statistik total siswa
        $totalStudents = Student::count();
        $totalAktif = Student::where('status_siswa', 'aktif')->count();
        $totalTamat = Student::where('status_siswa', 'tamat')->count();

        return view('admin.reports.students', compact(
            'schoolsWithStudents',
            'totalStudents',
            'totalAktif',
            'totalTamat'
        ));
    }

    /**
     * Laporan Kelulusan (Admin Dinas)
     */
    public function graduationReport()
    {
        $this->authorizeRole('admin_dinas');

        // Data kelulusan per sekolah dengan format yang diminta
        $schoolsWithGraduation = School::withCount([
            'students as total_lulus' => function ($query) {
                $query->where('status_siswa', 'tamat');
            },
            'students as total_tidak_lulus' => function ($query) {
                $query->where('status_siswa', 'tamat');
            }
        ])
            ->having('total_lulus', '>', 0)
            ->orHaving('total_tidak_lulus', '>', 0)
            ->orderBy('name')
            ->paginate(10);

        // Statistik total kelulusan
        $totalGraduates = Student::where('status_siswa', 'tamat')->count();
        $totalLulus = Student::where('status_siswa', 'tamat')->count();
        $totalTidakLulus = 0; // No graduation_status column, so set to 0

        return view('admin.reports.graduation', compact(
            'schoolsWithGraduation',
            'totalGraduates',
            'totalLulus',
            'totalTidakLulus'
        ));
    }

    /**
     * Export Laporan Kelulusan ke Excel
     */
    public function exportGraduation()
    {
        try {
            $this->authorizeRole('admin_dinas');

            // Set memory limit and timeout for exports
            ini_set('memory_limit', '512M');
            set_time_limit(300); // 5 minutes

            $filename = 'Laporan_Kelulusan_' . date('Y-m-d_H-i-s') . '.xlsx';

            \Log::info('Exporting graduation report to Excel');

            // Get schools with graduation counts
            $schoolsWithGraduation = School::withCount([
                'students as total_lulus' => function ($query) {
                    $query->where('status_siswa', 'tamat');
                },
                'students as total_tidak_lulus' => function ($query) {
                    $query->where('status_siswa', 'tamat');
                }
            ])
                ->having('total_lulus', '>', 0)
                ->orHaving('total_tidak_lulus', '>', 0)
                ->orderBy('name')
                ->get();

            \Log::info('Schools with graduation loaded: ' . $schoolsWithGraduation->count());

            // Create new Spreadsheet
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set metadata spreadsheet
            $spreadsheet->getProperties()
                ->setCreator('SIMPeDAS')
                ->setLastModifiedBy('SIMPeDAS')
                ->setTitle('Laporan Kelulusan')
                ->setSubject('Data Kelulusan Siswa')
                ->setDescription('Dibuat oleh Sistem Informasi Manajemen Pendidikan Dasar');

            // Headers
            $headers = ['NO', 'NAMA SEKOLAH', 'NPSN', 'JENJANG PENDIDIKAN', 'STATUS SEKOLAH', 'SISWA LULUS', 'SISWA TIDAK LULUS', 'TOTAL LULUSAN'];
            $lastCol = 'H';

            // Set headers
            foreach ($headers as $index => $header) {
                $sheet->setCellValue(chr(65 + $index) . '1', $header);
            }

            // Styling header
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '136E67']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ];
            $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray($headerStyle);
            $sheet->getRowDimension(1)->setRowHeight(30);

            // Set column widths
            $columnWidths = [
                'A' => 5,   // NO
                'B' => 35,  // NAMA SEKOLAH
                'C' => 15,  // NPSN
                'D' => 20,  // JENJANG PENDIDIKAN
                'E' => 20,  // STATUS SEKOLAH
                'F' => 15,  // SISWA LULUS
                'G' => 15,  // SISWA TIDAK LULUS
                'H' => 15   // TOTAL LULUSAN
            ];
            foreach ($columnWidths as $col => $width) {
                $sheet->getColumnDimension($col)->setWidth($width);
            }

            // Data rows
            $row = 2;
            $no = 1;

            foreach ($schoolsWithGraduation as $school) {
                $totalGraduates = $school->total_lulus + $school->total_tidak_lulus;

                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $school->name);
                $sheet->setCellValue('C' . $row, $school->npsn);
                $sheet->setCellValue('D' . $row, $school->education_level);
                $sheet->setCellValue('E' . $row, $school->status);
                $sheet->setCellValue('F' . $row, $school->total_lulus);
                $sheet->setCellValue('G' . $row, $school->total_tidak_lulus);
                $sheet->setCellValue('H' . $row, $totalGraduates);

                // Warna baris selang-seling
                if ($row % 2 == 0) {
                    $sheet->getStyle('A' . $row . ':H' . $row)->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('F8F9FA');
                }

                $row++;
            }

            // Set border untuk semua data
            $sheet->getStyle('A1:H' . ($row - 1))->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ]);

            // Set format angka sebagai text untuk seluruh kolom
            $sheet->getStyle('F2:H' . ($row - 1))->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);

            // Set auto-filter
            $sheet->setAutoFilter('A1:H' . ($row - 1));

            // Freeze panes (header tetap saat scroll)
            $sheet->freezePane('A2');

            // Create writer
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

            // Set headers for download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            // Save to output
            $writer->save('php://output');
            exit();
        } catch (\Exception $e) {
            \Log::error('Error exporting graduation report: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat export: ' . $e->getMessage());
        }
    }

    /**
     * Laporan Siswa Sekolah (Admin Sekolah)
     */
    public function schoolStudentsReport()
    {
        $this->authorizeRole('admin_sekolah');

        $user = Auth::user();
        $students = Student::where('sekolah_id', $user->school_id)
            ->with('school')
            ->orderBy('nama_lengkap')
            ->get();

        return view('admin.reports.school-students', compact('students'));
    }

    /**
     * Laporan Guru Sekolah (Admin Sekolah)
     */
    public function schoolTeachersReport()
    {
        $this->authorizeRole('admin_sekolah');

        $user = Auth::user();
        $teachers = Teacher::where('school_id', $user->school_id)
            ->with('school')
            ->orderBy('full_name')
            ->get();

        return view('admin.reports.school-teachers', compact('teachers'));
    }

    /**
     * Laporan Raport (Admin Sekolah)
     */
    public function schoolReportsReport()
    {
        $this->authorizeRole('admin_sekolah');

        $user = Auth::user();
        $reports = StudentReport::whereHas('student', function ($query) use ($user) {
            $query->where('school_id', $user->school_id);
        })
            ->with(['student.school'])
            ->orderBy('academic_year', 'desc')
            ->get();

        return view('admin.reports.school-reports', compact('reports'));
    }

    /**
     * Export Laporan Siswa Sekolah ke Excel (Admin Sekolah)
     */
    public function exportSchoolStudents()
    {
        try {
            $this->authorizeRole('admin_sekolah');

            // Set memory limit and timeout for exports
            ini_set('memory_limit', '512M');
            set_time_limit(300); // 5 minutes

            $user = Auth::user();
            $filename = 'Laporan_Siswa_Sekolah_' . $user->school->name . '_' . date('Y-m-d_H-i-s') . '.xlsx';

            \Log::info('Exporting school students report to Excel');

            // Get students data for the school
            $students = Student::where('sekolah_id', $user->school_id)
                ->with('school')
                ->orderBy('nama_lengkap')
                ->get();

            \Log::info('Students loaded: ' . $students->count());

            // Create new Spreadsheet
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set metadata spreadsheet
            $spreadsheet->getProperties()
                ->setCreator('SIMPeDAS')
                ->setLastModifiedBy('SIMPeDAS')
                ->setTitle('Laporan Siswa Sekolah')
                ->setSubject('Data Siswa Sekolah')
                ->setDescription('Dibuat oleh Sistem Informasi Manajemen Pendidikan Dasar');

            // Headers
            $headers = ['NO', 'NAMA LENGKAP', 'TEMPAT LAHIR', 'TANGGAL LAHIR', 'NISN', 'ROMBEL', 'STATUS SISWA', 'JENIS KELAMIN', 'AGAMA', 'ALAMAT'];
            $lastCol = 'J';

            // Set headers
            foreach ($headers as $index => $header) {
                $sheet->setCellValue(chr(65 + $index) . '1', $header);
            }

            // Styling header
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0d524a']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ];

            // Apply header style
            $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray($headerStyle);

            // Set row height for header
            $sheet->getRowDimension(1)->setRowHeight(25);

            // Set column widths
            $columnWidths = [
                'A' => 8,   // NO
                'B' => 25,  // NAMA LENGKAP
                'C' => 15,  // TEMPAT LAHIR
                'D' => 15,  // TANGGAL LAHIR
                'E' => 15,  // NISN
                'F' => 12,  // ROMBEL
                'G' => 15,  // STATUS SISWA
                'H' => 12,  // JENIS KELAMIN
                'I' => 12,  // AGAMA
                'J' => 30   // ALAMAT
            ];

            foreach ($columnWidths as $column => $width) {
                $sheet->getColumnDimension($column)->setWidth($width);
            }

            // Add data rows
            $row = 2;
            foreach ($students as $index => $student) {
                $sheet->setCellValue('A' . $row, $index + 1);
                $sheet->setCellValue('B' . $row, $student->nama_lengkap);
                $sheet->setCellValue('C' . $row, $student->tempat_lahir);
                $sheet->setCellValue('D' . $row, $student->tanggal_lahir->format('d/m/Y'));
                $sheet->setCellValue('E' . $row, $student->nisn);
                $sheet->setCellValue('F' . $row, $student->rombel);
                $sheet->setCellValue('G' . $row, ucfirst($student->status_siswa));
                $sheet->setCellValue('H' . $row, $student->jenis_kelamin_label);
                $sheet->setCellValue('I' . $row, $student->agama ?? '-');
                $sheet->setCellValue('J' . $row, $student->alamat ?? '-');

                $row++;
            }

            // Style data rows
            $dataStyle = [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ];

            // Apply data style
            if ($students->count() > 0) {
                $sheet->getStyle('A2:' . $lastCol . ($row - 1))->applyFromArray($dataStyle);
            }

            // Set row heights for data
            for ($i = 2; $i < $row; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(20);
            }

            // Create writer
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

            // Set headers for download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            // Save to output
            $writer->save('php://output');
            exit();
        } catch (\Exception $e) {
            \Log::error('Error exporting school students report: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat export: ' . $e->getMessage());
        }
    }

    /**
     * Export Laporan Guru Sekolah ke Excel (Admin Sekolah)
     */
    public function exportSchoolTeachers()
    {
        try {
            $this->authorizeRole('admin_sekolah');

            // Set memory limit and timeout for exports
            ini_set('memory_limit', '512M');
            set_time_limit(300); // 5 minutes

            $user = Auth::user();
            $filename = 'Laporan_Guru_Sekolah_' . $user->school->name . '_' . date('Y-m-d_H-i-s') . '.xlsx';

            \Log::info('Exporting school teachers report to Excel');

            // Get teachers data for the school
            $teachers = Teacher::where('school_id', $user->school_id)
                ->with('school')
                ->orderBy('full_name')
                ->get();

            \Log::info('Teachers loaded: ' . $teachers->count());

            // Create new Spreadsheet
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set metadata spreadsheet
            $spreadsheet->getProperties()
                ->setCreator('SIMPeDAS')
                ->setLastModifiedBy('SIMPeDAS')
                ->setTitle('Laporan Guru Sekolah')
                ->setSubject('Data Guru Sekolah')
                ->setDescription('Dibuat oleh Sistem Informasi Manajemen Pendidikan Dasar');

            // Headers
            $headers = ['NO', 'NAMA LENGKAP', 'NUPTK', 'NIP', 'TEMPAT LAHIR', 'TANGGAL LAHIR', 'JENIS KELAMIN', 'AGAMA', 'STATUS KEPEGAWAIAN', 'JABATAN', 'MATA PELAJARAN'];
            $lastCol = 'K';

            // Set headers
            foreach ($headers as $index => $header) {
                $sheet->setCellValue(chr(65 + $index) . '1', $header);
            }

            // Styling header
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '0d524a']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ];

            // Apply header style
            $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray($headerStyle);

            // Set row height for header
            $sheet->getRowDimension(1)->setRowHeight(25);

            // Set column widths
            $columnWidths = [
                'A' => 8,   // NO
                'B' => 25,  // NAMA LENGKAP
                'C' => 15,  // NUPTK
                'D' => 15,  // NIP
                'E' => 15,  // TEMPAT LAHIR
                'F' => 15,  // TANGGAL LAHIR
                'G' => 12,  // JENIS KELAMIN
                'H' => 12,  // AGAMA
                'I' => 18,  // STATUS KEPEGAWAIAN
                'J' => 15,  // JABATAN
                'K' => 25   // MATA PELAJARAN
            ];

            foreach ($columnWidths as $column => $width) {
                $sheet->getColumnDimension($column)->setWidth($width);
            }

            // Add data rows
            $row = 2;
            foreach ($teachers as $index => $teacher) {
                $sheet->setCellValue('A' . $row, $index + 1);
                $sheet->setCellValue('B' . $row, $teacher->full_name);
                $sheet->setCellValue('C' . $row, $teacher->nuptk ?? '-');
                $sheet->setCellValue('D' . $row, $teacher->nip ?? '-');
                $sheet->setCellValue('E' . $row, $teacher->birth_place ?? '-');
                $sheet->setCellValue('F' . $row, $teacher->birth_date ? $teacher->birth_date->format('d/m/Y') : '-');
                $sheet->setCellValue('G' . $row, $teacher->gender ?? '-');
                $sheet->setCellValue('H' . $row, $teacher->religion ?? '-');
                $sheet->setCellValue('I' . $row, $teacher->employment_status ?? '-');
                $sheet->setCellValue('J' . $row, $teacher->position ?? '-');
                $sheet->setCellValue('K' . $row, $teacher->subjects ?? '-');

                $row++;
            }

            // Style data rows
            $dataStyle = [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ];

            // Apply data style
            if ($teachers->count() > 0) {
                $sheet->getStyle('A2:' . $lastCol . ($row - 1))->applyFromArray($dataStyle);
            }

            // Set row heights for data
            for ($i = 2; $i < $row; $i++) {
                $sheet->getRowDimension($i)->setRowHeight(20);
            }

            // Create writer
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

            // Set headers for download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            // Save to output
            $writer->save('php://output');
            exit();
        } catch (\Exception $e) {
            \Log::error('Error exporting school teachers report: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat export: ' . $e->getMessage());
        }
    }

    /**
     * Export Laporan Siswa ke Excel
     */
    public function exportStudents()
    {
        try {
            $this->authorizeRole('admin_dinas');

            // Set memory limit and timeout for exports
            ini_set('memory_limit', '512M');
            set_time_limit(300); // 5 minutes

            $filename = 'Laporan_Siswa_' . date('Y-m-d_H-i-s') . '.xlsx';

            \Log::info('Exporting students report to Excel');

            // Get schools with student counts
            $schoolsWithStudents = School::withCount([
                'students as total_aktif' => function ($query) {
                    $query->where('status_siswa', 'aktif');
                },
                'students as total_tamat' => function ($query) {
                    $query->where('status_siswa', 'tamat');
                }
            ])
                ->having('total_aktif', '>', 0)
                ->orHaving('total_tamat', '>', 0)
                ->orderBy('name')
                ->get();

            \Log::info('Schools with students loaded: ' . $schoolsWithStudents->count());

            // Create new Spreadsheet
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set metadata spreadsheet
            $spreadsheet->getProperties()
                ->setCreator('SIMPeDAS')
                ->setLastModifiedBy('SIMPeDAS')
                ->setTitle('Laporan Siswa')
                ->setSubject('Data Siswa')
                ->setDescription('Dibuat oleh Sistem Informasi Manajemen Pendidikan Dasar');

            // Headers
            $headers = ['NO', 'NAMA SEKOLAH', 'NPSN', 'JENJANG PENDIDIKAN', 'STATUS SEKOLAH', 'SISWA AKTIF', 'SISWA TAMAT', 'TOTAL SISWA'];
            $lastCol = 'H';

            // Set headers
            foreach ($headers as $index => $header) {
                $sheet->setCellValue(chr(65 + $index) . '1', $header);
            }

            // Styling header
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '136E67']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ];
            $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray($headerStyle);
            $sheet->getRowDimension(1)->setRowHeight(30);

            // Set column widths
            $columnWidths = [
                'A' => 5,   // NO
                'B' => 35,  // NAMA SEKOLAH
                'C' => 15,  // NPSN
                'D' => 20,  // JENJANG PENDIDIKAN
                'E' => 20,  // STATUS SEKOLAH
                'F' => 15,  // SISWA AKTIF
                'G' => 15,  // SISWA TAMAT
                'H' => 15   // TOTAL SISWA
            ];
            foreach ($columnWidths as $col => $width) {
                $sheet->getColumnDimension($col)->setWidth($width);
            }

            // Data rows
            $row = 2;
            $no = 1;

            foreach ($schoolsWithStudents as $school) {
                $totalStudents = $school->total_aktif + $school->total_tamat;

                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $school->name);
                $sheet->setCellValue('C' . $row, $school->npsn);
                $sheet->setCellValue('D' . $row, $school->education_level);
                $sheet->setCellValue('E' . $row, $school->status);
                $sheet->setCellValue('F' . $row, $school->total_aktif);
                $sheet->setCellValue('G' . $row, $school->total_tamat);
                $sheet->setCellValue('H' . $row, $totalStudents);

                // Warna baris selang-seling
                if ($row % 2 == 0) {
                    $sheet->getStyle('A' . $row . ':H' . $row)->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('F8F9FA');
                }

                $row++;
            }

            // Set border untuk semua data
            $sheet->getStyle('A1:H' . ($row - 1))->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ]);

            // Set format NIP dan Phone sebagai text untuk seluruh kolom
            $sheet->getStyle('F2:H' . ($row - 1))->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);

            // Set auto-filter
            $sheet->setAutoFilter('A1:H' . ($row - 1));

            // Freeze panes (header tetap saat scroll)
            $sheet->freezePane('A2');

            // Create writer
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

            // Set headers for download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            // Save to output
            $writer->save('php://output');
            exit();
        } catch (\Exception $e) {
            \Log::error('Error exporting students report: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat export: ' . $e->getMessage());
        }
    }

    /**
     * Laporan Tenaga Kerja Non Pendidik (Admin Dinas)
     */
    public function nonTeachingStaffReport()
    {
        $this->authorizeRole('admin_dinas');

        // Data non teaching staff per sekolah dengan format yang diminta
        $schoolsWithStaff = School::withCount([
            'nonTeachingStaff as total_pns' => function ($query) {
                $query->where('status', 'Aktif')
                    ->where('employment_status', 'PNS');
            },
            'nonTeachingStaff as total_pppk' => function ($query) {
                $query->where('status', 'Aktif')
                    ->where('employment_status', 'PPPK');
            },
            'nonTeachingStaff as total_honorer' => function ($query) {
                $query->where('status', 'Aktif')
                    ->where('employment_status', 'Honorer');
            },
            'nonTeachingStaff as total_pty' => function ($query) {
                $query->where('status', 'Aktif')
                    ->where('employment_status', 'PTY');
            },
            'nonTeachingStaff as total_kontrak' => function ($query) {
                $query->where('status', 'Aktif')
                    ->where('employment_status', 'Kontrak');
            }
        ])
            ->having('total_pns', '>', 0)
            ->orHaving('total_pppk', '>', 0)
            ->orHaving('total_honorer', '>', 0)
            ->orHaving('total_pty', '>', 0)
            ->orHaving('total_kontrak', '>', 0)
            ->orderBy('name')
            ->paginate(10);

        // Statistik total non teaching staff
        $totalStaff = NonTeachingStaff::where('status', 'Aktif')->count();
        $totalPNS = NonTeachingStaff::where('status', 'Aktif')
            ->where('employment_status', 'PNS')
            ->count();
        $totalPPPK = NonTeachingStaff::where('status', 'Aktif')
            ->where('employment_status', 'PPPK')
            ->count();
        $totalHonorer = NonTeachingStaff::where('status', 'Aktif')
            ->where('employment_status', 'Honorer')
            ->count();
        $totalPTY = NonTeachingStaff::where('status', 'Aktif')
            ->where('employment_status', 'PTY')
            ->count();
        $totalKontrak = NonTeachingStaff::where('status', 'Aktif')
            ->where('employment_status', 'Kontrak')
            ->count();

        return view('admin.reports.non-teaching-staff', compact(
            'schoolsWithStaff',
            'totalStaff',
            'totalPNS',
            'totalPPPK',
            'totalHonorer',
            'totalPTY',
            'totalKontrak'
        ));
    }

    /**
     * Export Laporan Non Teaching Staff ke Excel
     */
    public function exportNonTeachingStaff()
    {
        try {
            $this->authorizeRole('admin_dinas');

            // Set memory limit and timeout for exports
            ini_set('memory_limit', '512M');
            set_time_limit(300); // 5 minutes

            $filename = 'Laporan_Tenaga_Kerja_Non_Pendidik_' . date('Y-m-d_H-i-s') . '.xlsx';

            \Log::info('Exporting non teaching staff report to Excel');

            // Get schools with staff counts
            $schoolsWithStaff = School::withCount([
                'nonTeachingStaff as total_pns' => function ($query) {
                    $query->where('status', 'Aktif')
                        ->where('employment_status', 'PNS');
                },
                'nonTeachingStaff as total_pppk' => function ($query) {
                    $query->where('status', 'Aktif')
                        ->where('employment_status', 'PPPK');
                },
                'nonTeachingStaff as total_honorer' => function ($query) {
                    $query->where('status', 'Aktif')
                        ->where('employment_status', 'Honorer');
                },
                'nonTeachingStaff as total_pty' => function ($query) {
                    $query->where('status', 'Aktif')
                        ->where('employment_status', 'PTY');
                },
                'nonTeachingStaff as total_kontrak' => function ($query) {
                    $query->where('status', 'Aktif')
                        ->where('employment_status', 'Kontrak');
                }
            ])
                ->having('total_pns', '>', 0)
                ->orHaving('total_pppk', '>', 0)
                ->orHaving('total_honorer', '>', 0)
                ->orHaving('total_pty', '>', 0)
                ->orHaving('total_kontrak', '>', 0)
                ->orderBy('name')
                ->get();

            \Log::info('Schools with staff loaded: ' . $schoolsWithStaff->count());

            // Create new Spreadsheet
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set metadata spreadsheet
            $spreadsheet->getProperties()
                ->setCreator('SIMPeDAS')
                ->setLastModifiedBy('SIMPeDAS')
                ->setTitle('Laporan Tenaga Kerja Non Pendidik')
                ->setSubject('Data Tenaga Kerja Non Pendidik')
                ->setDescription('Dibuat oleh Sistem Informasi Manajemen Pendidikan Dasar');

            // Headers
            $headers = ['NO', 'NAMA SEKOLAH', 'NPSN', 'JENJANG PENDIDIKAN', 'STATUS SEKOLAH', 'PNS', 'PPPK', 'HONORER', 'PTY', 'KONTRAK', 'TOTAL'];
            $lastCol = 'K';

            // Set headers
            foreach ($headers as $index => $header) {
                $sheet->setCellValue(chr(65 + $index) . '1', $header);
            }

            // Styling header
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '136E67']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ];
            $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray($headerStyle);
            $sheet->getRowDimension(1)->setRowHeight(30);

            // Set column widths
            $columnWidths = [
                'A' => 5,   // NO
                'B' => 35,  // NAMA SEKOLAH
                'C' => 15,  // NPSN
                'D' => 20,  // JENJANG PENDIDIKAN
                'E' => 20,  // STATUS SEKOLAH
                'F' => 15,  // PNS
                'G' => 15,  // PPPK
                'H' => 15,  // HONORER
                'I' => 15,  // PTY
                'J' => 15,  // KONTRAK
                'K' => 15   // TOTAL
            ];
            foreach ($columnWidths as $col => $width) {
                $sheet->getColumnDimension($col)->setWidth($width);
            }

            // Data rows
            $row = 2;
            $no = 1;

            foreach ($schoolsWithStaff as $school) {
                $totalStaff = $school->total_pns + $school->total_pppk + $school->total_honorer + $school->total_pty + $school->total_kontrak;

                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $school->name);
                $sheet->setCellValue('C' . $row, $school->npsn);
                $sheet->setCellValue('D' . $row, $school->education_level);
                $sheet->setCellValue('E' . $row, $school->status);
                $sheet->setCellValue('F' . $row, $school->total_pns);
                $sheet->setCellValue('G' . $row, $school->total_pppk);
                $sheet->setCellValue('H' . $row, $school->total_honorer);
                $sheet->setCellValue('I' . $row, $school->total_pty);
                $sheet->setCellValue('J' . $row, $school->total_kontrak);
                $sheet->setCellValue('K' . $row, $totalStaff);

                // Warna baris selang-seling
                if ($row % 2 == 0) {
                    $sheet->getStyle('A' . $row . ':K' . $row)->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('F8F9FA');
                }

                $row++;
            }

            // Set border untuk semua data
            $sheet->getStyle('A1:K' . ($row - 1))->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ]);

            // Set format angka sebagai text untuk seluruh kolom
            $sheet->getStyle('F2:K' . ($row - 1))->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);

            // Set auto-filter
            $sheet->setAutoFilter('A1:K' . ($row - 1));

            // Freeze panes (header tetap saat scroll)
            $sheet->freezePane('A2');

            // Create writer
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

            // Set headers for download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            // Save to output
            $writer->save('php://output');
            exit();
        } catch (\Exception $e) {
            \Log::error('Error exporting non teaching staff report: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat export: ' . $e->getMessage());
        }
    }

    /**
     * Export Laporan Sekolah ke Excel
     */
    public function exportSchools()
    {
        try {
            $this->authorizeRole('admin_dinas');

            // Set memory limit and timeout for exports
            ini_set('memory_limit', '512M');
            set_time_limit(300); // 5 minutes

            $filename = 'Laporan_Sekolah_' . date('Y-m-d_H-i-s') . '.xlsx';

            \Log::info('Exporting schools report to Excel');

            // Get schools data
            $schools = School::withCount(['teachers', 'students', 'nonTeachingStaff'])
                ->orderBy('name')
                ->get();

            \Log::info('Schools loaded: ' . $schools->count());

            // Create new Spreadsheet
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set metadata spreadsheet
            $spreadsheet->getProperties()
                ->setCreator('SIMPeDAS')
                ->setLastModifiedBy('SIMPeDAS')
                ->setTitle('Laporan Sekolah')
                ->setSubject('Data Sekolah')
                ->setDescription('Dibuat oleh Sistem Informasi Manajemen Pendidikan Dasar');

            // Headers
            $headers = ['NO', 'NAMA SEKOLAH', 'NPSN', 'JENJANG PENDIDIKAN', 'STATUS SEKOLAH', 'ALAMAT', 'GURU', 'SISWA', 'TENAGA PENDIDIK', 'TOTAL SDM'];
            $lastCol = 'J';

            // Set headers
            foreach ($headers as $index => $header) {
                $sheet->setCellValue(chr(65 + $index) . '1', $header);
            }

            // Styling header
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '136E67']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ];
            $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray($headerStyle);
            $sheet->getRowDimension(1)->setRowHeight(30);

            // Set column widths
            $columnWidths = [
                'A' => 5,   // NO
                'B' => 35,  // NAMA SEKOLAH
                'C' => 15,  // NPSN
                'D' => 20,  // JENJANG PENDIDIKAN
                'E' => 20,  // STATUS SEKOLAH
                'F' => 40,  // ALAMAT
                'G' => 15,  // GURU
                'H' => 15,  // SISWA
                'I' => 15,  // TENAGA PENDIDIK
                'J' => 15   // TOTAL SDM
            ];
            foreach ($columnWidths as $col => $width) {
                $sheet->getColumnDimension($col)->setWidth($width);
            }

            // Data rows
            $row = 2;
            $no = 1;

            foreach ($schools as $school) {
                $totalSDM = $school->teachers_count + $school->students_count + $school->non_teaching_staff_count;

                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $school->name);
                $sheet->setCellValue('C' . $row, $school->npsn);
                $sheet->setCellValue('D' . $row, $school->education_level);
                $sheet->setCellValue('E' . $row, $school->status);
                $sheet->setCellValue('F' . $row, $school->address);
                $sheet->setCellValue('G' . $row, $school->teachers_count);
                $sheet->setCellValue('H' . $row, $school->students_count);
                $sheet->setCellValue('I' . $row, $school->non_teaching_staff_count);
                $sheet->setCellValue('J' . $row, $totalSDM);

                // Warna baris selang-seling
                if ($row % 2 == 0) {
                    $sheet->getStyle('A' . $row . ':J' . $row)->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('F8F9FA');
                }

                $row++;
            }

            // Set border untuk semua data
            $sheet->getStyle('A1:J' . ($row - 1))->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],
            ]);

            // Set format angka sebagai text untuk seluruh kolom
            $sheet->getStyle('G2:J' . ($row - 1))->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);

            // Set auto-filter
            $sheet->setAutoFilter('A1:J' . ($row - 1));

            // Freeze panes (header tetap saat scroll)
            $sheet->freezePane('A2');

            // Create writer
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

            // Set headers for download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');

            // Save to output
            $writer->save('php://output');
            exit();
        } catch (\Exception $e) {
            \Log::error('Error exporting schools report: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat export: ' . $e->getMessage());
        }
    }

    /**
     * Helper method to authorize roles
     */
    private function authorizeRole($role)
    {
        if (!Auth::user()->hasRole($role)) {
            abort(403, 'Unauthorized action.');
        }
    }
}
