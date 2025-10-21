<?php

namespace App\Imports;

use App\Models\Teacher;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Helpers\ReligionHelper;
use App\Helpers\NormalizationHelper;

class TeacherImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $results = [
        'success' => 0,
        'failed' => 0,
        'errors' => [],
        'warnings' => [],
    ];

    public function collection(Collection $rows)
    {
        $user = Auth::user();
        \Log::info('TeacherImport: Starting collection processing', [
            'total_rows' => $rows->count(),
            'user_id' => $user ? $user->id : 'unknown'
        ]);

        // Increase execution time for large imports
        set_time_limit(300); // 5 minutes
        ini_set('memory_limit', '512M');

        // Validasi template di baris pertama
        if ($rows->count() > 0) {
            $firstRow = $rows->first();
            $availableColumns = array_keys(is_array($firstRow) ? $firstRow : $firstRow->toArray());
            $expectedColumns = ['aksi', 'npsn_sekolah', 'nama', 'nuptk', 'jk', 'tempat_lahir', 'tanggal_lahir', 'nip', 'status_kepegawaian', 'jenis_ptk', 'gelar_depan', 'gelar_belakang', 'jenjang', 'jurusan_prodi', 'sertifikasi', 'tmt_kerja', 'tugas_tambahan', 'mengajar', 'jam_tugas_tambahan', 'jjm', 'total_jjm', 'siswa', 'kompetensi'];

            $missingColumns = array_diff($expectedColumns, $availableColumns);
            if (!empty($missingColumns)) {
                $this->addError(0, "Template tidak sesuai! Kolom yang hilang: " . implode(', ', $missingColumns) . ". Silakan download template terbaru.", $firstRow);
                $this->results['failed']++;
                return;
            }

            \Log::info('TeacherImport: Template validation passed', [
                'available_columns' => $availableColumns,
                'expected_columns' => $expectedColumns
            ]);
        }

        // FASE 1: VALIDASI SEMUA DATA TERLEBIH DAHULU (tanpa menyimpan ke database)
        $validatedRows = [];
        $hasErrors = false;

        \Log::info('[TEACHER_IMPORT] Fase 1: Validasi semua data...');

        // Pre-load existing teachers to avoid N+1 queries
        $existingTeachers = \App\Models\Teacher::pluck('nuptk', 'nuptk')->toArray();
        $activeTeachers = \App\Models\Teacher::pluck('nuptk', 'nuptk')->toArray();

        foreach ($rows as $index => $row) {
            // Skip baris kosong
            if (collect($row)->filter()->isEmpty()) {
                continue;
            }

            // Skip baris yang tidak memiliki data penting (nuptk atau nama_lengkap)
            if (empty($row['nuptk']) && empty($row['nama_lengkap'])) {
                continue;
            }

            // CASTING
            if (isset($row['nuptk'])) $row['nuptk'] = (string) $row['nuptk'];
            if (isset($row['nip'])) $row['nip'] = (string) $row['nip'];
            if (isset($row['npsn_sekolah'])) $row['npsn_sekolah'] = (string) $row['npsn_sekolah'];

            // NORMALIZATION - Tolerate common typos/variants
            $row['jk'] = NormalizationHelper::normalizeGender($row['jk'] ?? null);
            $row['status_kepegawaian'] = NormalizationHelper::normalizeEmploymentStatus($row['status_kepegawaian'] ?? null);
            $row['aksi'] = NormalizationHelper::normalizeAction($row['aksi'] ?? null);

            try {
                $action = strtoupper($row['aksi'] ?? 'CREATE');
                
                // Validasi data tanpa menyimpan ke database
                $isValid = $this->validateRowData($row, $index, $action, $user, $existingTeachers, $activeTeachers);
                if (!$isValid) {
                    $hasErrors = true;
                } else {
                    $validatedRows[] = ['row' => $row, 'index' => $index, 'action' => $action];
                }
            } catch (\Exception $e) {
                $this->addError($index, $e->getMessage());
                \Log::error('[TEACHER_IMPORT] Exception pada baris ' . ($index + 2) . ': ' . $e->getMessage());
                $hasErrors = true;
            }
        }

        // Jika ada error, jangan lanjutkan ke database
        if ($hasErrors) {
            $this->results['failed'] = count($this->results['errors']);
            \Log::error('[TEACHER_IMPORT] Import dibatalkan karena ada error. Total error: ' . count($this->results['errors']));
            \Log::error('[TEACHER_IMPORT] Silakan perbaiki semua error di file Excel dan upload ulang.');
            return;
        }

        // FASE 2: SIMPAN KE DATABASE (hanya jika semua data valid)
        \Log::info('[TEACHER_IMPORT] Fase 2: Simpan ke database...');

        foreach ($validatedRows as $validatedRow) {
            $row = $validatedRow['row'];
            $index = $validatedRow['index'];
            $action = $validatedRow['action'];

            // Set school_id (sudah divalidasi di fase 1)
            if ($user && $user->hasRole('admin_sekolah')) {
                $row['school_id'] = $user->school_id;
            } else {
                $school = \App\Models\School::where('npsn', $row['npsn_sekolah'])->first();
                $row['school_id'] = $school->id;
            }

            // Parse dates (sudah divalidasi di fase 1)
            if (isset($row['tanggal_lahir'])) {
                $row['tanggal_lahir'] = $this->parseDate($row['tanggal_lahir']);
            }
            if (isset($row['tmt'])) {
                $row['tmt'] = $this->parseDate($row['tmt']);
            }

            // Execute database operation
            try {
                switch ($action) {
                    case 'CREATE':
                        $this->createTeacher($row, $index);
                        break;
                    case 'UPDATE':
                        $this->updateTeacher($row, $index);
                        break;
                    case 'DELETE':
                        $this->deleteTeacher($row, $index);
                        break;
                }
                $this->results['success']++;
            } catch (\Exception $e) {
                $this->results['failed']++;
                $this->addError($index, $e->getMessage());
                \Log::error('[TEACHER_IMPORT] Database error pada baris ' . ($index + 2) . ': ' . $e->getMessage());
            }
        }

        \Log::info('[TEACHER_IMPORT] Import selesai', [
            'success' => $this->results['success'],
            'failed' => $this->results['failed'],
            'errors_count' => count($this->results['errors']),
            'warnings_count' => count($this->results['warnings'])
        ]);
    }

    protected function validateRowData($row, $index, $action, $user, $existingTeachers, $activeTeachers)
    {
        // Validasi berdasarkan aksi
        switch ($action) {
            case 'CREATE':
                return $this->validateCreateData($row, $index, $user, $activeTeachers);
            case 'UPDATE':
                return $this->validateUpdateData($row, $index, $user, $existingTeachers);
            case 'DELETE':
                return $this->validateDeleteData($row, $index, $user, $existingTeachers);
            default:
                $this->addError($index, "Aksi tidak valid: {$action}");
                return false;
        }
    }

    protected function validateCreateData($row, $index, $user, $activeTeachers)
    {
        // Validasi field yang diperlukan untuk CREATE
        if (!$this->validateRequired($row, $index, $user)) {
            return false;
        }

        // Cek NUPTK sudah ada atau belum
        if (!empty($row['nuptk'])) {
            if (isset($activeTeachers[$row['nuptk']])) {
                $this->addError($index, "NUPTK sudah terdaftar.", $row);
                return false;
            }
        }

        // Validasi format data
        return $this->validateDataFormats($row, $index);
    }

    protected function validateUpdateData($row, $index, $user, $existingTeachers)
    {
        // Cari guru berdasarkan NUPTK (diperlukan sebagai identifier untuk UPDATE)
        if (empty($row['nuptk'])) {
            $this->addError($index, "NUPTK wajib diisi untuk UPDATE (sebagai identifier)");
            return false;
        }

        if (!isset($existingTeachers[$row['nuptk']])) {
            $this->addError($index, "Guru tidak ditemukan untuk update");
            return false;
        }

        // Validasi field yang diperlukan untuk update
        if (!$this->validateRequired($row, $index, $user)) {
            return false;
        }

        // Validasi format data
        return $this->validateDataFormats($row, $index);
    }

    protected function validateDeleteData($row, $index, $user, $existingTeachers)
    {
        // Cari guru berdasarkan NUPTK (diperlukan sebagai identifier untuk DELETE)
        if (empty($row['nuptk'])) {
            $this->addError($index, "NUPTK wajib diisi untuk DELETE (sebagai identifier)");
            return false;
        }

        if (!isset($existingTeachers[$row['nuptk']])) {
            $this->addError($index, "Guru tidak ditemukan untuk delete");
            return false;
        }

        return true;
    }

    protected function validateRequired($row, $index, $user)
    {
        $requiredColumns = ['nama']; // Only required fields - NUPTK is optional
        
        if ($user && $user->hasRole('admin_dinas')) {
            $requiredColumns[] = 'npsn_sekolah';
        }

        $missingColumns = [];
        foreach ($requiredColumns as $col) {
            if (!isset($row[$col]) || empty($row[$col])) {
                $missingColumns[] = $col;
            }
        }

        if (!empty($missingColumns)) {
            $this->addError($index, "Kolom yang diperlukan tidak ditemukan atau kosong: " . implode(', ', $missingColumns) . ". Pastikan menggunakan template yang benar.", $row);
            return false;
        }

        // Validasi NPSN sekolah untuk admin dinas
        if ($user && $user->hasRole('admin_dinas')) {
            if (!empty($row['npsn_sekolah'])) {
                $school = \App\Models\School::where('npsn', $row['npsn_sekolah'])->first();
                if (!$school) {
                    $this->addError($index, "NPSN sekolah tidak ditemukan di database.", $row);
                    return false;
                }
            } else {
                $this->addError($index, "Kolom NPSN_SEKOLAH wajib diisi untuk admin dinas.", $row);
                return false;
            }
        }

        return true;
    }

    protected function validateDataFormats($row, $index)
    {
        $hasError = false;

        // Validasi jenis kelamin (optional, flexible)
        if (!empty($row['jk']) && !in_array($row['jk'], ['L', 'P'])) {
            $this->addError($index, "Jenis kelamin harus 'L' atau 'P'.", $row);
            $hasError = true;
        }

        // No strict validation for other fields - allow free input as per Dapodik

        // Validasi format tanggal
        if (!empty($row['tanggal_lahir'])) {
            $tanggalLahir = $this->parseDate($row['tanggal_lahir']);
            if (!$tanggalLahir) {
                $this->addError($index, "Format tanggal lahir tidak valid: '{$row['tanggal_lahir']}'. Gunakan format YYYY-MM-DD (contoh: 1985-07-10) atau DD/MM/YY (contoh: 10/07/85).", $row);
                $hasError = true;
            }
        }

        if (!empty($row['tmt_kerja'])) {
            $tmt = $this->parseDate($row['tmt_kerja']);
            if (!$tmt) {
                $this->addError($index, "Format TMT Kerja tidak valid: '{$row['tmt_kerja']}'. Gunakan format YYYY-MM-DD (contoh: 2010-01-01) atau DD/MM/YY (contoh: 01/01/10).", $row);
                $hasError = true;
            }
        }

        return !$hasError;
    }

    protected function createTeacher($row, $index)
    {
        $user = Auth::user();
        if (!$this->validateRequired($row, $index, $user)) return false;
        $existing = Teacher::where('nuptk', $row['nuptk'])->first();
        if ($existing) {
            $this->addError($index, "NUPTK sudah terdaftar.", $row);
            return false;
        }
        // Mapping field dari header Dapodik ke field database
        $data = [
            'school_id' => $row['school_id'] ?? null,
            'full_name' => $row['nama'] ?? null,
            'nuptk' => $row['nuptk'] ?? null,
            'gender' => $row['jk'] ?? null,
            'birth_place' => $row['tempat_lahir'] ?? null,
            'birth_date' => $row['tanggal_lahir'] ?? null,
            'nip' => $row['nip'] ?? null,
            'employment_status' => $row['status_kepegawaian'] ?? null,
            'jenis_ptk' => $row['jenis_ptk'] ?? null,
            'gelar_depan' => $row['gelar_depan'] ?? null,
            'gelar_belakang' => $row['gelar_belakang'] ?? null,
            'jenjang' => $row['jenjang'] ?? null,
            'education_major' => $row['jurusan_prodi'] ?? null,
            'sertifikasi' => $row['sertifikasi'] ?? null,
            'tmt' => $row['tmt_kerja'] ?? null,
            'tugas_tambahan' => $row['tugas_tambahan'] ?? null,
            'mengajar' => $row['mengajar'] ?? null,
            'jam_tugas_tambahan' => $row['jam_tugas_tambahan'] ?? null,
            'jjm' => $row['jjm'] ?? null,
            'total_jjm' => $row['total_jjm'] ?? null,
            'siswa' => $row['siswa'] ?? null,
            'kompetensi' => $row['kompetensi'] ?? null,
        ];

        $teacher = Teacher::create($data);

        // User creation removed - focus on Dapodik data only
        return true;
    }

    protected function updateTeacher($row, $index)
    {
        if (empty($row['nuptk'])) {
            $this->addError($index, "NUPTK wajib diisi untuk update.", $row);
            return false;
        }
        $teacher = Teacher::where('nuptk', $row['nuptk'])->first();
        if (!$teacher) {
            $this->addError($index, "Guru tidak ditemukan untuk update", $row);
            return false;
        }
        // Mapping field dari header Dapodik ke field database
        $data = [
            'full_name' => $row['nama'] ?? $teacher->full_name,
            'gender' => $row['jk'] ?? $teacher->gender,
            'birth_place' => $row['tempat_lahir'] ?? $teacher->birth_place,
            'birth_date' => $row['tanggal_lahir'] ?? $teacher->birth_date,
            'nip' => $row['nip'] ?? $teacher->nip,
            'employment_status' => $row['status_kepegawaian'] ?? $teacher->employment_status,
            'jenis_ptk' => $row['jenis_ptk'] ?? $teacher->jenis_ptk,
            'gelar_depan' => $row['gelar_depan'] ?? $teacher->gelar_depan,
            'gelar_belakang' => $row['gelar_belakang'] ?? $teacher->gelar_belakang,
            'jenjang' => $row['jenjang'] ?? $teacher->jenjang,
            'education_major' => $row['jurusan_prodi'] ?? $teacher->education_major,
            'sertifikasi' => $row['sertifikasi'] ?? $teacher->sertifikasi,
            'tmt' => $row['tmt_kerja'] ?? $teacher->tmt,
            'tugas_tambahan' => $row['tugas_tambahan'] ?? $teacher->tugas_tambahan,
            'mengajar' => $row['mengajar'] ?? $teacher->mengajar,
            'jam_tugas_tambahan' => $row['jam_tugas_tambahan'] ?? $teacher->jam_tugas_tambahan,
            'jjm' => $row['jjm'] ?? $teacher->jjm,
            'total_jjm' => $row['total_jjm'] ?? $teacher->total_jjm,
            'siswa' => $row['siswa'] ?? $teacher->siswa,
            'kompetensi' => $row['kompetensi'] ?? $teacher->kompetensi,
        ];

        $teacher->update($data);
        return true;
    }

    protected function deleteTeacher($row, $index)
    {
        if (empty($row['nuptk'])) {
            $this->addError($index, "NUPTK wajib diisi untuk delete.", $row);
            return false;
        }
        $teacher = Teacher::where('nuptk', $row['nuptk'])->first();
        if (!$teacher) {
            $this->addError($index, "Guru tidak ditemukan untuk penghapusan", $row);
            return false;
        }
        $teacher->delete();
        return true;
    }


    protected function isValidDate($dateValue)
    {
        if (empty($dateValue)) return true; // Allow empty dates

        // Handle Excel serial number (integer)
        if (is_numeric($dateValue)) {
            try {
                $excelDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateValue);
                if ($excelDate) {
                    return true;
                }
            } catch (\Exception $e) {
                // If Excel conversion fails, continue with other methods
            }
        }

        // Convert to string for other format checks
        $dateString = trim((string) $dateValue);

        // Coba berbagai format tanggal
        $formats = [
            'Y-m-d',           // 1985-07-10
            'd/m/Y',           // 10/07/1985
            'd/m/y',           // 10/07/85
            'd-m-Y',           // 10-07-1985
            'd-m-y',           // 10-07-85
            'Y/m/d',           // 1985/07/10
            'd.m.Y',           // 10.07.1985
            'd.m.y',           // 10.07.85
        ];

        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $dateString);
            if ($date !== false) {
                return true;
            }
        }

        // Jika semua format gagal, coba dengan strtotime sebagai fallback
        $timestamp = strtotime($dateString);
        if ($timestamp !== false) {
            return true;
        }

        return false;
    }

    protected function parseDate($dateString)
    {
        if (empty($dateString)) return null;

        // Trim whitespace
        $dateString = trim($dateString);

        // Handle Excel date conversion - jika Excel mengkonversi tanggal ke format lokal
        // Cek apakah ini adalah format Excel (serial number atau format lokal)
        if (is_numeric($dateString)) {
            // Jika ini adalah serial number Excel (seperti 29584 untuk 1980-12-12)
            $excelDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($dateString);
            if ($excelDate) {
                return $excelDate->format('Y-m-d');
            }
        }

        // Coba berbagai format tanggal
        $formats = [
            'Y-m-d',           // 1985-07-10
            'd/m/Y',           // 10/07/1985
            'd/m/y',           // 10/07/85
            'd-m-Y',           // 10-07-1985
            'd-m-y',           // 10-07-85
            'Y/m/d',           // 1985/07/10
            'd.m.Y',           // 10.07.1985
            'd.m.y',           // 10.07.85
        ];

        foreach ($formats as $format) {
            $date = \DateTime::createFromFormat($format, $dateString);
            if ($date !== false) {
                // Jika tahun 2 digit, asumsikan 19xx untuk tahun < 50, 20xx untuk tahun >= 50
                if (strpos($format, 'y') !== false) {
                    $year = $date->format('Y');
                    if ($year < 1950) {
                        $date->add(new \DateInterval('P100Y')); // Tambah 100 tahun
                    }
                }
                return $date->format('Y-m-d');
            }
        }

        // Jika semua format gagal, coba dengan strtotime sebagai fallback
        $timestamp = strtotime($dateString);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }

        return null;
    }

    protected function addError($index, $message, $row = null)
    {
        $info = '';
        if ($row) {
            $infoParts = [];
            if (!empty($row['nuptk'])) $infoParts[] = 'NUPTK: ' . $row['nuptk'];
            if (!empty($row['nama'])) $infoParts[] = 'Nama: ' . $row['nama'];
            if ($infoParts) $info = ' (' . implode(', ', $infoParts) . ')';
        }
        $this->results['errors'][] = "Baris " . ($index + 2) . "{$info}: {$message}";
    }

    protected function addWarning($index, $message)
    {
        $this->results['warnings'][] = "Baris " . ($index + 2) . ": {$message}";
    }

    public function getResults()
    {
        return $this->results;
    }

    public function rules(): array
    {
        return [
            // Basic validation - most fields are optional and flexible
            '*.nuptk' => ['nullable', 'max:20'],
            '*.nama' => ['nullable', 'max:255'],
            '*.jk' => ['nullable', 'in:L,P'],
            '*.tempat_lahir' => ['nullable', 'max:100'],
            '*.tanggal_lahir' => ['nullable', function ($attribute, $value, $fail) {
                if (!empty($value) && !$this->isValidDate($value)) {
                    $fail('Format tanggal tidak valid: ' . $value . '. Gunakan format DD/MM/YY atau YYYY-MM-DD.');
                }
            }],
            '*.nip' => ['nullable', 'max:20'],
            '*.tmt_kerja' => ['nullable', function ($attribute, $value, $fail) {
                if (!empty($value) && !$this->isValidDate($value)) {
                    $fail('Format TMT Kerja tidak valid: ' . $value . '. Gunakan format DD/MM/YY atau YYYY-MM-DD.');
                }
            }],
            '*.jam_tugas_tambahan' => ['nullable', 'integer', 'min:0'],
            '*.jjm' => ['nullable', 'integer', 'min:0'],
            '*.total_jjm' => ['nullable', 'integer', 'min:0'],
            '*.siswa' => ['nullable', 'integer', 'min:0'],
            '*.npsn_sekolah' => ['nullable', 'max:20'],
        ];
    }
}
