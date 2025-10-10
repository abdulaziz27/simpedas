<?php

namespace App\Imports;

use App\Models\School;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class FastSchoolImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $results = [
        'success' => 0,
        'failed' => 0,
        'errors' => [],
        'warnings' => [],
    ];

    protected $importId = null;

    public function setImportId($importId)
    {
        $this->importId = $importId;
    }

    public function collection(Collection $rows)
    {
        // Increase execution time for large imports
        set_time_limit(300); // 5 minutes
        ini_set('memory_limit', '512M');

        // Minimal logging for performance
        Log::info('[FAST_IMPORT] Starting import', ['total_rows' => $rows->count()]);

        // FASE 1: VALIDASI SEMUA DATA TERLEBIH DAHULU
        $validatedRows = [];
        $hasErrors = false;

        // Update progress - starting validation
        $this->updateProgress('validating', 0, $rows->count());

        // Pre-load all existing schools to avoid N+1 queries
        $existingSchools = School::withTrashed()->pluck('npsn', 'npsn')->toArray();
        $activeSchools = School::pluck('npsn', 'npsn')->toArray();

        foreach ($rows as $index => $row) {
            if (collect($row)->filter()->isEmpty()) {
                continue;
            }

            $action = strtoupper($row['aksi'] ?? '');
            $validActions = ['CREATE', 'UPDATE', 'DELETE'];
            if (empty($row['npsn']) && !in_array($action, $validActions)) {
                continue;
            }

            // CASTING: pastikan npsn dan telepon selalu string
            if (isset($row['npsn'])) {
                $row['npsn'] = (string) $row['npsn'];
            }
            if (isset($row['telepon'])) {
                $row['telepon'] = (string) $row['telepon'];
            }

            try {
                if (empty($action)) {
                    $action = 'CREATE';
                }

                // Validasi data tanpa menyimpan ke database
                $isValid = $this->validateRowData($row, $index, $action, $existingSchools, $activeSchools);
                if (!$isValid) {
                    $hasErrors = true;
                } else {
                    $validatedRows[] = ['row' => $row, 'index' => $index, 'action' => $action];
                }
            } catch (\Exception $e) {
                $this->addError($index, $e->getMessage());
                $hasErrors = true;
            }
        }

        // Jika ada error, jangan lanjutkan ke database
        if ($hasErrors) {
            $this->results['failed'] = count($this->results['errors']);
            Log::error('[FAST_IMPORT] Import dibatalkan karena ada error', ['error_count' => count($this->results['errors'])]);
            return;
        }

        // FASE 2: SIMPAN KE DATABASE
        Log::info('[FAST_IMPORT] Starting database operations', ['validated_rows' => count($validatedRows)]);
        $this->updateProgress('importing', 0, count($validatedRows));

        $processedCount = 0;
        $userCreationCount = 0;

        foreach ($validatedRows as $validatedData) {
            $row = $validatedData['row'];
            $index = $validatedData['index'];
            $action = $validatedData['action'];

            try {
                $result = false;
                switch ($action) {
                    case 'CREATE':
                        $result = $this->createSchool($row, $index);
                        break;
                    case 'UPDATE':
                        $result = $this->updateSchool($row, $index);
                        break;
                    case 'DELETE':
                        $result = $this->deleteSchool($row, $index);
                        break;
                }

                if ($result === true) {
                    $this->results['success']++;
                } else {
                    $this->results['failed']++;
                }

                $processedCount++;

                // Update progress every 20 records (reduced frequency)
                if ($processedCount % 20 == 0 || $processedCount == count($validatedRows)) {
                    $this->updateProgress('importing', $processedCount, count($validatedRows));
                }

                // Minimal progress logging every 100 records
                if ($processedCount % 100 == 0) {
                    $percentage = round(($processedCount / count($validatedRows)) * 100, 1);
                    Log::info("[FAST_IMPORT] Progress: {$processedCount}/" . count($validatedRows) . " ({$percentage}%)");
                }
            } catch (\Exception $e) {
                $this->results['failed']++;
                $this->addError($index, $e->getMessage());
            }
        }

        // Update final progress
        $this->updateProgress('completed', count($validatedRows), count($validatedRows));

        // Minimal final logging
        Log::info('[FAST_IMPORT] Completed', [
            'success' => $this->results['success'],
            'failed' => $this->results['failed'],
            'errors_count' => count($this->results['errors']),
            'warnings_count' => count($this->results['warnings'])
        ]);
    }

    protected function updateProgress($status, $processed, $total)
    {
        if (!$this->importId) return;

        $progress = \App\Models\ImportProgress::where('import_id', $this->importId)->first();

        if ($progress) {
            $progress->update([
                'status' => $status,
                'processed' => $processed,
                'total' => $total,
                'success' => $this->results['success'],
                'failed' => $this->results['failed'],
                'errors' => $this->results['errors'],
                'warnings' => $this->results['warnings'],
            ]);
        }
    }

    protected function validateRowData($row, $index, $action, $existingSchools, $activeSchools)
    {
        switch ($action) {
            case 'CREATE':
                return $this->validateCreateData($row, $index, $existingSchools, $activeSchools);
            case 'UPDATE':
                return $this->validateUpdateData($row, $index, $existingSchools);
            case 'DELETE':
                return $this->validateDeleteData($row, $index, $existingSchools);
            default:
                $this->addError($index, "Aksi tidak valid: {$action}");
                return false;
        }
    }

    protected function validateCreateData($row, $index, $existingSchools, $activeSchools)
    {
        if (!$this->validateRequired($row, $index)) {
            return false;
        }

        if (!empty($row['npsn']) && isset($activeSchools[$row['npsn']])) {
            $this->addError($index, "NPSN sudah terdaftar.");
            return false;
        }

        $this->validateSchoolName($row, $index);
        return true;
    }

    protected function validateUpdateData($row, $index, $existingSchools)
    {
        if (empty($row['npsn'])) {
            $this->addError($index, "ERROR KRITIS: NPSN wajib diisi untuk UPDATE");
            return false;
        }

        if (!isset($existingSchools[$row['npsn']])) {
            $this->addError($index, "ERROR KRITIS: Sekolah tidak ditemukan untuk update");
            return false;
        }

        if (empty($row['nama_sekolah']) || empty($row['jenjang_pendidikan']) || empty($row['status']) || empty($row['alamat'])) {
            $this->addError($index, "ERROR KRITIS: Field wajib tidak lengkap untuk update");
            return false;
        }

        if (!in_array($row['jenjang_pendidikan'], ['TK', 'SD', 'SMP', 'KB', 'PKBM'])) {
            $this->addError($index, "ERROR KRITIS: Jenjang pendidikan tidak valid");
            return false;
        }

        if (!in_array($row['status'], ['Negeri', 'Swasta'])) {
            $this->addError($index, "ERROR KRITIS: Status tidak valid");
            return false;
        }

        $this->validateSchoolName($row, $index);
        return true;
    }

    protected function validateDeleteData($row, $index, $existingSchools)
    {
        if (empty($row['npsn'])) {
            $this->addError($index, "ERROR KRITIS: NPSN wajib diisi untuk DELETE");
            return false;
        }

        if (!isset($existingSchools[$row['npsn']])) {
            $this->addError($index, "ERROR KRITIS: Sekolah tidak ditemukan untuk penghapusan");
            return false;
        }

        return true;
    }

    protected function createSchool($row, $index)
    {
        $existing = School::withTrashed()->where('npsn', $row['npsn'])->first();
        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore();
                $existing->update($this->prepareSchoolData($row));
                $school = $existing;
            } else {
                return false;
            }
        } else {
            $school = School::create($this->prepareSchoolData($row));
        }

        // Create admin sekolah account if password provided (minimal logging)
        if (!empty($row['password_admin'])) {
            try {
                $user = User::create([
                    'name' => $row['kepala_sekolah'] ?? 'Admin Sekolah',
                    'email' => $row['email'],
                    'password' => Hash::make($row['password_admin']),
                    'school_id' => $school->id,
                ]);
                $user->assignRole('admin_sekolah');
            } catch (\Exception $e) {
                $this->addWarning($index, "Sekolah berhasil dibuat, tetapi gagal membuat akun admin sekolah.");
            }
        }

        return true;
    }

    protected function updateSchool($row, $index)
    {
        $school = School::where('npsn', $row['npsn'])->first();
        if (!$school) {
            return false;
        }

        $school->update($this->prepareSchoolData($row));

        // Create or update admin sekolah account if password provided (minimal logging)
        if (!empty($row['password_admin'])) {
            try {
                $user = User::updateOrCreate(
                    ['email' => $row['email']],
                    [
                        'name' => $row['kepala_sekolah'] ?? 'Admin Sekolah',
                        'password' => Hash::make($row['password_admin']),
                        'school_id' => $school->id,
                    ]
                );

                if (!$user->hasRole('admin_sekolah')) {
                    $user->assignRole('admin_sekolah');
                }
            } catch (\Exception $e) {
                $this->addWarning($index, "Sekolah berhasil diperbarui, tetapi gagal membuat/update akun admin sekolah.");
            }
        }

        return true;
    }

    protected function deleteSchool($row, $index)
    {
        $school = School::where('npsn', $row['npsn'])->first();
        if (!$school) {
            return false;
        }

        $school->delete();
        return true;
    }

    protected function prepareSchoolData($row)
    {
        return [
            'npsn' => $row['npsn'],
            'name' => $row['nama_sekolah'],
            'education_level' => $row['jenjang_pendidikan'],
            'status' => $row['status'],
            'address' => $row['alamat'],
            'desa' => $row['desa'] ?? null,
            'kecamatan' => $row['kecamatan'] ?? null,
            'kabupaten_kota' => $row['kabupaten_kota'] ?? null,
            'provinsi' => $row['provinsi'] ?? null,
            'google_maps_link' => $row['google_maps_link'] ?? null,
            'latitude' => $row['latitude'] ?? null,
            'longitude' => $row['longitude'] ?? null,
            'phone' => $row['telepon'] ?? null,
            'email' => $row['email'] ?? null,
            'website' => $row['website'] ?? null,
            'headmaster' => $row['kepala_sekolah'] ?? null,
        ];
    }

    protected function validateRequired($row, $index)
    {
        $required = ['npsn', 'nama_sekolah', 'jenjang_pendidikan', 'status', 'alamat', 'email'];
        $hasError = false;
        foreach ($required as $field) {
            if (empty($row[$field])) {
                $this->addError($index, "Kolom '{$field}' wajib diisi.");
                $hasError = true;
            }
        }

        if (!empty($row['nama_sekolah']) && strlen($row['nama_sekolah']) < 3) {
            $this->addWarning($index, "Nama sekolah sebaiknya minimal 3 karakter.");
        }

        if (!empty($row['npsn']) && strlen($row['npsn']) > 20) {
            $this->addError($index, "NPSN maksimal 20 karakter.");
            $hasError = true;
        }

        if (!empty($row['nama_sekolah']) && strlen($row['nama_sekolah']) > 255) {
            $this->addWarning($index, "Nama sekolah sebaiknya maksimal 255 karakter.");
        }

        if (!empty($row['telepon']) && strlen($row['telepon']) > 20) {
            $this->addWarning($index, "Nomor telepon dipotong maksimal 20 karakter.");
            $row['telepon'] = substr($row['telepon'], 0, 20);
        }

        if (!empty($row['kepala_sekolah']) && strlen($row['kepala_sekolah']) > 255) {
            $this->addWarning($index, "Nama kepala sekolah dipotong maksimal 255 karakter.");
            $row['kepala_sekolah'] = substr($row['kepala_sekolah'], 0, 255);
        }

        // Validasi website format
        if (!empty($row['website'])) {
            if (!filter_var($row['website'], FILTER_VALIDATE_URL)) {
                $this->addWarning($index, "Format website tidak valid: " . $row['website']);
            }
            if (strlen($row['website']) > 255) {
                $this->addWarning($index, "Website URL dipotong maksimal 255 karakter.");
                $row['website'] = substr($row['website'], 0, 255);
            }
        }

        // Validasi koordinat
        if (!empty($row['latitude'])) {
            if (!is_numeric($row['latitude']) || $row['latitude'] < -90 || $row['latitude'] > 90) {
                $this->addWarning($index, "Latitude tidak valid (harus antara -90 sampai 90): " . $row['latitude']);
                $row['latitude'] = null;
            }
        }

        if (!empty($row['longitude'])) {
            if (!is_numeric($row['longitude']) || $row['longitude'] < -180 || $row['longitude'] > 180) {
                $this->addWarning($index, "Longitude tidak valid (harus antara -180 sampai 180): " . $row['longitude']);
                $row['longitude'] = null;
            }
        }

        return !$hasError;
    }

    protected function validateSchoolName($row, $index)
    {
        $name = $row['nama_sekolah'];

        if (!empty($name) && strlen($name) < 3) {
            $this->addWarning($index, "Nama sekolah sebaiknya minimal 3 karakter.");
        }

        if (!empty($name) && strlen($name) > 255) {
            $this->addWarning($index, "Nama sekolah sebaiknya maksimal 255 karakter.");
        }
    }

    protected function addError($index, $message)
    {
        $this->results['errors'][] = "Row " . ($index + 2) . ": " . $message;
    }

    protected function addWarning($index, $message)
    {
        $this->results['warnings'][] = "Row " . ($index + 2) . ": " . $message;
    }

    public function getResults()
    {
        return $this->results;
    }

    public function rules(): array
    {
        return [
            '*.npsn' => ['nullable', 'max:20'],
            '*.nama_sekolah' => ['nullable', 'string', 'min:3', 'max:255'],
            '*.jenjang_pendidikan' => ['nullable', 'string', 'in:TK,SD,SMP,KB,PKBM'],
            '*.status' => ['nullable', 'string', 'in:Negeri,Swasta'],
            '*.alamat' => ['nullable', 'string'],
            '*.desa' => ['nullable', 'string', 'max:100'],
            '*.kecamatan' => ['nullable', 'string', 'max:100'],
            '*.kabupaten_kota' => ['nullable', 'string', 'max:100'],
            '*.provinsi' => ['nullable', 'string', 'max:100'],
            '*.google_maps_link' => ['nullable', 'string', 'max:2000'],
            '*.latitude' => ['nullable', 'numeric', 'between:-90,90'],
            '*.longitude' => ['nullable', 'numeric', 'between:-180,180'],
            '*.telepon' => ['nullable', 'max:20'],
            '*.email' => ['nullable', 'max:255', function ($attribute, $value, $fail) {
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $fail('Format email tidak valid: ' . $value);
                }
            }],
            '*.website' => ['nullable', 'url', 'max:255'],
            '*.kepala_sekolah' => ['nullable', 'string', 'max:255'],
            '*.password_admin' => ['nullable', 'string', 'min:8'],
        ];
    }
}
