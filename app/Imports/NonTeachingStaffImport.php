<?php

namespace App\Imports;

use App\Models\NonTeachingStaff;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Helpers\ReligionHelper;
use App\Helpers\NormalizationHelper;

class NonTeachingStaffImport implements ToCollection, WithHeadingRow, WithValidation
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
        \Log::info('NonTeachingStaffImport: Starting collection processing', [
            'total_rows' => $rows->count(),
            'user_id' => $user->id
        ]);

        // Increase execution time for large imports
        set_time_limit(300); // 5 minutes
        ini_set('memory_limit', '512M');

        // FASE 1: VALIDASI SEMUA DATA TERLEBIH DAHULU (tanpa menyimpan ke database)
        $validatedRows = [];
        $hasErrors = false;

        \Log::info('[STAFF_IMPORT] Fase 1: Validasi semua data...');

        // Pre-load existing staff to avoid N+1 queries
        $existingStaff = \App\Models\NonTeachingStaff::pluck('nip_nik', 'nip_nik')->toArray();
        $activeStaff = \App\Models\NonTeachingStaff::pluck('nip_nik', 'nip_nik')->toArray();

        foreach ($rows as $index => $row) {
            // Skip baris kosong
            if (collect($row)->filter()->isEmpty()) {
                continue;
            }

            // Skip baris yang tidak memiliki data penting (nama_lengkap atau nip_nik)
            if (empty($row['nama_lengkap']) && empty($row['nip_nik'])) {
                continue;
            }

            // CASTING
            if (isset($row['nip_nik'])) $row['nip_nik'] = (string) $row['nip_nik'];
            if (isset($row['nuptk'])) $row['nuptk'] = (string) $row['nuptk'];
            if (isset($row['npsn_sekolah'])) $row['npsn_sekolah'] = (string) $row['npsn_sekolah'];

            // NORMALIZATION - Tolerate common typos/variants
            $row['jenis_kelamin'] = NormalizationHelper::normalizeGender($row['jenis_kelamin'] ?? null);
            $row['status_ke_pegawaian'] = NormalizationHelper::normalizeEmploymentStatus($row['status_ke_pegawaian'] ?? null);
            $row['status'] = NormalizationHelper::normalizeStatus($row['status'] ?? null);
            $row['aksi'] = NormalizationHelper::normalizeAction($row['aksi'] ?? null);

            try {
                $action = strtoupper($row['aksi'] ?? 'CREATE');
                
                // Validasi data tanpa menyimpan ke database
                $isValid = $this->validateRowData($row, $index, $action, $user, $existingStaff, $activeStaff);
                if (!$isValid) {
                    $hasErrors = true;
                } else {
                    $validatedRows[] = ['row' => $row, 'index' => $index, 'action' => $action];
                }
            } catch (\Exception $e) {
                $this->addError($index, $e->getMessage());
                \Log::error('[STAFF_IMPORT] Exception pada baris ' . ($index + 2) . ': ' . $e->getMessage());
                $hasErrors = true;
            }
        }

        // Jika ada error, jangan lanjutkan ke database
        if ($hasErrors) {
            $this->results['failed'] = count($this->results['errors']);
            \Log::error('[STAFF_IMPORT] Import dibatalkan karena ada error. Total error: ' . count($this->results['errors']));
            \Log::error('[STAFF_IMPORT] Silakan perbaiki semua error di file Excel dan upload ulang.');
            return;
        }

        // FASE 2: SIMPAN KE DATABASE (hanya jika semua data valid)
        \Log::info('[STAFF_IMPORT] Fase 2: Simpan ke database...');

        foreach ($validatedRows as $validatedRow) {
            $row = $validatedRow['row'];
            $index = $validatedRow['index'];
            $action = $validatedRow['action'];

            // Set school_id (sudah divalidasi di fase 1)
            if ($user->hasRole('admin_sekolah')) {
                $row['school_id'] = $user->school_id;
            } else {
                $school = \App\Models\School::where('npsn', $row['npsn_sekolah'])->first();
                $row['school_id'] = $school->id;
            }

            // Execute database operation
            try {
                switch ($action) {
                    case 'CREATE':
                        $this->createStaff($row, $index);
                        break;
                    case 'UPDATE':
                        $this->updateStaff($row, $index);
                        break;
                    case 'DELETE':
                        $this->deleteStaff($row, $index);
                        break;
                }
                $this->results['success']++;
            } catch (\Exception $e) {
                $this->results['failed']++;
                $this->addError($index, $e->getMessage());
                \Log::error('[STAFF_IMPORT] Database error pada baris ' . ($index + 2) . ': ' . $e->getMessage());
            }
        }

        \Log::info('[STAFF_IMPORT] Import selesai', [
            'success' => $this->results['success'],
            'failed' => $this->results['failed'],
            'errors_count' => count($this->results['errors']),
            'warnings_count' => count($this->results['warnings'])
        ]);
    }

    protected function validateRowData($row, $index, $action, $user, $existingStaff, $activeStaff)
    {
        // Validasi berdasarkan aksi
        switch ($action) {
            case 'CREATE':
                return $this->validateCreateData($row, $index, $user, $activeStaff);
            case 'UPDATE':
                return $this->validateUpdateData($row, $index, $user, $existingStaff);
            case 'DELETE':
                return $this->validateDeleteData($row, $index, $user, $existingStaff);
            default:
                $this->addError($index, "Aksi tidak valid: {$action}");
                return false;
        }
    }

    protected function validateCreateData($row, $index, $user, $activeStaff)
    {
        // Validasi field yang diperlukan untuk CREATE
        if (!$this->validateRequired($row, $index, $user)) {
            return false;
        }

        // Cek NIP/NIK sudah ada atau belum
        if (!empty($row['nip_nik'])) {
            if (isset($activeStaff[$row['nip_nik']])) {
                $this->addError($index, "NIP/NIK sudah terdaftar.", $row);
                return false;
            }
        }

        // Validasi format data
        return $this->validateDataFormats($row, $index);
    }

    protected function validateUpdateData($row, $index, $user, $existingStaff)
    {
        // Cari staff berdasarkan NIP/NIK
        if (empty($row['nip_nik'])) {
            $this->addError($index, "NIP/NIK wajib diisi untuk UPDATE");
            return false;
        }

        if (!isset($existingStaff[$row['nip_nik']])) {
            $this->addError($index, "Staff tidak ditemukan untuk update");
            return false;
        }

        // Validasi field yang diperlukan untuk update
        if (!$this->validateRequired($row, $index, $user)) {
            return false;
        }

        // Validasi format data
        return $this->validateDataFormats($row, $index);
    }

    protected function validateDeleteData($row, $index, $user, $existingStaff)
    {
        // Cari staff berdasarkan NIP/NIK
        if (empty($row['nip_nik'])) {
            $this->addError($index, "NIP/NIK wajib diisi untuk DELETE");
            return false;
        }

        if (!isset($existingStaff[$row['nip_nik']])) {
            $this->addError($index, "Staff tidak ditemukan untuk delete");
            return false;
        }

        return true;
    }

    protected function validateDataFormats($row, $index)
    {
        $hasError = false;

        // Validasi jenis kelamin
        if (!empty($row['jenis_kelamin']) && !in_array($row['jenis_kelamin'], ['Laki-laki', 'Perempuan'])) {
            $this->addError($index, "Jenis kelamin harus 'Laki-laki' atau 'Perempuan'.", $row);
            $hasError = true;
        }

        // Validasi agama
        if (!empty($row['agama']) && !ReligionHelper::isValidReligion($row['agama'])) {
            $this->addError($index, "Agama tidak valid. Pilih: Islam, Kristen, Katolik (atau Katholik), Hindu, Buddha, Konghucu.", $row);
            $hasError = true;
        }

        // Validasi status kepegawaian
        $validStatuses = ['PNS', 'PPPK', 'PTY', 'Honorer'];
        if (!empty($row['status_ke_pegawaian']) && !in_array($row['status_ke_pegawaian'], $validStatuses)) {
            $this->addError($index, "Status kepegawaian harus salah satu dari: " . implode(', ', $validStatuses), $row);
            $hasError = true;
        }

        return !$hasError;
    }

    protected function createStaff($row, $index)
    {
        // Validation already done in phase 1, just create the staff

        // Mapping field dari header bahasa Indonesia ke field database
        $data = [
            'school_id' => $row['school_id'] ?? null,
            'full_name' => $row['nama_lengkap'] ?? null,
            'nip_nik' => $row['nip_nik'] ?? null,
            'nuptk' => $row['nuptk'] ?? null,
            'birth_place' => $row['tempat_lahir'] ?? null,
            'birth_date' => $row['tanggal_lahir'] ?? null,
            'gender' => $row['jenis_kelamin'] ?? null,
            'religion' => ReligionHelper::normalizeReligion($row['agama'] ?? null),
            'address' => $row['alamat'] ?? null,
            'position' => $row['jabatan'] ?? null,
            'education_level' => $row['tingkat_pendidikan'] ?? null,
            'education_major' => $row['jurusan_pendidikan'] ?? null,
            'employment_status' => $row['status_ke_pegawaian'] ?? null,
            'rank' => $row['pangkat'] ?? null,
            'tmt' => $row['tmt'] ?? null,
            'status' => $row['status'] ?? null,
        ];

        $staff = NonTeachingStaff::create($data);

        // Create user account if password provided (manual password)
        if (!empty($row['email']) && !empty($row['password_admin'])) {
            try {
                $user = \App\Models\User::firstOrCreate(
                    ['email' => $row['email']],
                    [
                        'name' => $row['nama_lengkap'] ?? $staff->full_name,
                        'password' => \Illuminate\Support\Facades\Hash::make($row['password_admin']),
                        'school_id' => $staff->school_id,
                    ]
                );

                // Assign staff role if available
                if (\Spatie\Permission\Models\Role::where('name', 'staff')->exists()) {
                    if (!$user->hasRole('staff')) {
                        $user->assignRole('staff');
                    }
                }

                $user->school_id = $staff->school_id;
                $user->save();

                $this->addWarning($index, "User staff berhasil dibuat dengan password manual.");
            } catch (\Exception $e) {
                $this->addWarning($index, "Gagal membuat user staff: {$e->getMessage()}");
            }
        }
        return true;
    }

    protected function updateStaff($row, $index)
    {
        if (empty($row['nip_nik'])) {
            $this->addError($index, "NIP/NIK wajib diisi untuk update.", $row);
            return false;
        }
        $staff = NonTeachingStaff::where('nip_nik', $row['nip_nik'])->first();
        if (!$staff) {
            $this->addError($index, "Staff tidak ditemukan untuk update", $row);
            return false;
        }

        // Mapping field dari header bahasa Indonesia ke field database
        $data = [
            'full_name' => $row['nama_lengkap'] ?? $staff->full_name,
            'nuptk' => $row['nuptk'] ?? $staff->nuptk,
            'birth_place' => $row['tempat_lahir'] ?? $staff->birth_place,
            'birth_date' => $row['tanggal_lahir'] ?? $staff->birth_date,
            'gender' => $row['jenis_kelamin'] ?? $staff->gender,
            'religion' => $row['agama'] ?? $staff->religion,
            'address' => $row['alamat'] ?? $staff->address,
            'position' => $row['jabatan'] ?? $staff->position,
            'education_level' => $row['tingkat_pendidikan'] ?? $staff->education_level,
            'education_major' => $row['jurusan_pendidikan'] ?? $staff->education_major,
            'employment_status' => $row['status_ke_pegawaian'] ?? $staff->employment_status,
            'rank' => $row['pangkat'] ?? $staff->rank,
            'tmt' => $row['tmt'] ?? $staff->tmt,
            'status' => $row['status'] ?? $staff->status,
        ];

        $staff->update($data);
        return true;
    }

    protected function deleteStaff($row, $index)
    {
        if (empty($row['nip_nik'])) {
            $this->addError($index, "NIP/NIK wajib diisi untuk delete.", $row);
            return false;
        }
        $staff = NonTeachingStaff::where('nip_nik', $row['nip_nik'])->first();
        if (!$staff) {
            $this->addError($index, "Staff tidak ditemukan untuk penghapusan", $row);
            return false;
        }
        $staff->delete();
        return true;
    }

    protected function validateRequired($row, $index, $user)
    {
        $required = ['nip_nik', 'nama_lengkap', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'agama', 'alamat', 'jabatan', 'status_ke_pegawaian', 'status'];
        if ($user->hasRole('admin_dinas')) {
            $required[] = 'npsn_sekolah';
        }
        
        $hasError = false;
        foreach ($required as $field) {
            if (empty($row[$field])) {
                $this->addError($index, "Kolom '{$field}' wajib diisi.", $row);
                $hasError = true;
            }
        }

        // Validasi NPSN sekolah untuk admin dinas
        if ($user->hasRole('admin_dinas')) {
            if (!empty($row['npsn_sekolah'])) {
                $school = \App\Models\School::where('npsn', $row['npsn_sekolah'])->first();
                if (!$school) {
                    $this->addError($index, "NPSN sekolah tidak ditemukan di database.", $row);
                    $hasError = true;
                }
            } else {
                $this->addError($index, "Kolom NPSN_SEKOLAH wajib diisi untuk admin dinas.", $row);
                $hasError = true;
            }
        }

        return !$hasError;
    }

    protected function addError($index, $message, $row = null)
    {
        $info = '';
        if ($row) {
            $infoParts = [];
            if (!empty($row['nip_nik'])) $infoParts[] = 'NIP/NIK: ' . $row['nip_nik'];
            if (!empty($row['nama_lengkap'])) $infoParts[] = 'Nama: ' . $row['nama_lengkap'];
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

    public function rules(): array
    {
        return [
            '*.nip_nik' => ['nullable', 'max:20'],
            '*.nuptk' => ['nullable', 'max:20'],
            '*.nama_lengkap' => ['nullable', 'max:255'],
            '*.jenis_kelamin' => ['nullable', 'in:Laki-laki,Perempuan'],
            '*.tempat_lahir' => ['nullable', 'max:100'],
            '*.tanggal_lahir' => ['nullable', function ($attribute, $value, $fail) {
                if (!empty($value) && !$this->isValidDate($value)) {
                    $fail('Format tanggal tidak valid: ' . $value . '. Gunakan format DD/MM/YY atau YYYY-MM-DD.');
                }
            }],
            '*.agama' => ['nullable', 'max:50'],
            '*.alamat' => ['nullable'],
            '*.jabatan' => ['nullable', 'max:100'],
            '*.tingkat_pendidikan' => ['nullable', 'max:100'],
            '*.jurusan_pendidikan' => ['nullable', 'max:100'],
            '*.status_ke_pegawaian' => ['nullable', 'in:PNS,PPPK,GTY,PTY'],
            '*.pangkat' => ['nullable', 'max:50'],
            '*.tmt' => ['nullable', function ($attribute, $value, $fail) {
                if (!empty($value) && !$this->isValidDate($value)) {
                    $fail('Format TMT tidak valid: ' . $value . '. Gunakan format DD/MM/YY atau YYYY-MM-DD.');
                }
            }],
            '*.status' => ['nullable', 'in:Aktif,Tidak Aktif'],
            '*.npsn_sekolah' => ['nullable', 'max:20'],
        ];
    }
}
