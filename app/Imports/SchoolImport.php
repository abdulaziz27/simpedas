<?php

namespace App\Imports;

use App\Models\School;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SchoolImport implements ToCollection, WithHeadingRow, WithValidation
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

        // FASE 1: VALIDASI SEMUA DATA TERLEBIH DAHULU (tanpa menyimpan ke database)
        $validatedRows = [];
        $hasErrors = false;

        // Update progress - starting validation
        $this->updateProgress('validating', 0, $rows->count());

        // Count total rows for progress tracking
        $totalRows = $rows->count();

        \Log::info('[IMPORT] Fase 1: Validasi semua data...');

        // Pre-load all existing schools to avoid N+1 queries
        $existingSchools = \App\Models\School::withTrashed()->pluck('npsn', 'npsn')->toArray();
        $activeSchools = \App\Models\School::pluck('npsn', 'npsn')->toArray();

        foreach ($rows as $index => $row) {
            // Ensure array for key access and array_* functions
            if ($row instanceof \Illuminate\Support\Collection) {
                $row = $row->toArray();
            }
            // Skip baris yang semua kolomnya kosong
            if (collect($row)->filter()->isEmpty()) {
                continue;
            }
            // Skip baris petunjuk/template (bukan data sekolah)
            $action = strtoupper($row['aksi'] ?? '');
            $validActions = ['CREATE', 'UPDATE', 'DELETE'];
            if (empty($row['npsn']) && !in_array($action, $validActions)) {
                // Baris ini kemungkinan besar baris petunjuk, skip
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
                \Log::error('[IMPORT] Exception pada baris ' . ($index + 2) . ': ' . $e->getMessage());
                $hasErrors = true;
            }
        }

        // Jika ada error, jangan lanjutkan ke database
        if ($hasErrors) {
            $this->results['failed'] = count($this->results['errors']);
            \Log::error('[IMPORT] Import dibatalkan karena ada error. Total error: ' . count($this->results['errors']));
            \Log::error('[IMPORT] Silakan perbaiki semua error di file Excel dan upload ulang.');
            return;
        }

        // FASE 2: SIMPAN KE DATABASE (hanya jika semua data valid)
        \Log::info('[IMPORT] Fase 2: Semua data valid. Mulai menyimpan ke database...');
        \Log::info('[IMPORT] Total data yang akan diproses: ' . count($validatedRows));

        // Update progress - starting import
        $this->updateProgress('importing', 0, count($validatedRows));

        $processedCount = 0;

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

                // Update progress for every record
                $processedCount++;

                // Update progress less frequently for better performance
                if ($processedCount % 25 == 0 || $processedCount == count($validatedRows)) {
                    $this->updateProgress('importing', $processedCount, count($validatedRows));
                }

                // Progress logging every 100 records (reduced frequency)
                if ($processedCount % 100 == 0) {
                    $percentage = round(($processedCount / count($validatedRows)) * 100, 1);
                    \Log::info("[IMPORT] Progress: {$processedCount}/" . count($validatedRows) . " ({$percentage}%) - Success: {$this->results['success']}, Failed: {$this->results['failed']}");
                }
            } catch (\Exception $e) {
                $this->results['failed']++;
                $this->addError($index, $e->getMessage());
                \Log::error('[IMPORT] Exception saat menyimpan baris ' . ($index + 2) . ': ' . $e->getMessage());
            }
        }

        // Update final progress
        $this->updateProgress('completed', count($validatedRows), count($validatedRows));

        // Log hasil akhir error dan warning (optimized)
        \Log::info('[IMPORT] Selesai. Jumlah error: ' . count($this->results['errors']) . ', warning: ' . count($this->results['warnings']));

        // Only log errors/warnings if there are any (avoid empty JSON)
        if (!empty($this->results['errors'])) {
            \Log::info('[IMPORT] Errors: ' . implode('; ', array_slice($this->results['errors'], 0, 10)) .
                (count($this->results['errors']) > 10 ? '... dan ' . (count($this->results['errors']) - 10) . ' error lainnya' : ''));
        }
        if (!empty($this->results['warnings'])) {
            \Log::info('[IMPORT] Warnings: ' . implode('; ', array_slice($this->results['warnings'], 0, 10)) .
                (count($this->results['warnings']) > 10 ? '... dan ' . (count($this->results['warnings']) - 10) . ' warning lainnya' : ''));
        }
    }

    protected function updateProgress($status, $processed, $total)
    {
        if (!$this->importId) return;

        // Update progress in database
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

            // Reduced logging frequency for better performance
            if ($processed % 50 == 0 || $status === 'completed' || $status === 'error') {
                \Log::info('[IMPORT] Progress updated', [
                    'status' => $status,
                    'processed' => $processed,
                    'total' => $total,
                    'success' => $this->results['success'],
                    'failed' => $this->results['failed']
                ]);
            }
        } else {
            \Log::error('[IMPORT] Progress record not found for import_id: ' . $this->importId);
        }
    }

    protected function validateRowData($row, $index, $action, $existingSchools, $activeSchools)
    {
        // Validasi berdasarkan aksi
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
        // Validasi field yang diperlukan untuk CREATE
        if (!$this->validateRequired($row, $index)) {
            return false;
        }

        // Cek NPSN sudah ada atau belum (menggunakan pre-loaded data)
        if (!empty($row['npsn'])) {
            if (isset($activeSchools[$row['npsn']])) {
                $this->addError($index, "NPSN sudah terdaftar.", $row);
                return false;
            }
        }

        // Validasi panjang nama sekolah saja (tidak perlu warning spam)
        $this->validateSchoolName($row, $index);

        return true;
    }

    protected function validateUpdateData($row, $index, $existingSchools)
    {
        // Cari sekolah berdasarkan NPSN (menggunakan pre-loaded data)
        if (empty($row['npsn'])) {
            $this->addError($index, "ERROR KRITIS: NPSN wajib diisi untuk UPDATE");
            return false;
        }

        if (!isset($existingSchools[$row['npsn']])) {
            $this->addError($index, "ERROR KRITIS: Sekolah tidak ditemukan untuk update");
            return false;
        }

        // Validasi field yang diperlukan untuk update
        if (empty($row['nama_sekolah']) || empty($row['jenjang_pendidikan']) || empty($row['status']) || empty($row['alamat'])) {
            $this->addError($index, "ERROR KRITIS: Field wajib tidak lengkap untuk update");
            return false;
        }

        // Validasi education level
        if (!in_array($row['jenjang_pendidikan'], ['TK', 'SD', 'SMP', 'KB', 'PKBM'])) {
            $this->addError($index, "ERROR KRITIS: Jenjang pendidikan tidak valid");
            return false;
        }

        // Validasi status
        if (!in_array($row['status'], ['Negeri', 'Swasta'])) {
            $this->addError($index, "ERROR KRITIS: Status tidak valid");
            return false;
        }

        // Validasi panjang nama sekolah saja (tidak perlu warning spam)
        $this->validateSchoolName($row, $index);

        return true;
    }

    protected function validateDeleteData($row, $index, $existingSchools)
    {
        // Cari sekolah berdasarkan NPSN (menggunakan pre-loaded data)
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
        // Validasi sudah dilakukan di fase 1, langsung proses data

        // Cek NPSN, restore jika soft deleted, error jika aktif, create jika tidak ada
        $existing = School::withTrashed()->where('npsn', $row['npsn'])->first();
        if ($existing) {
            if ($existing->trashed()) {
                // Restore dan update data
                $existing->restore();
                $existing->update([
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
                ]);
            } else {
                // Seharusnya tidak sampai ke sini karena sudah divalidasi di fase 1
                \Log::error('[IMPORT] NPSN sudah terdaftar, tapi lolos validasi fase 1: ' . $row['npsn']);
                return false;
            }
        } else {
            // Buat sekolah baru
            $school = School::create([
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
            ]);
        }

        // Create admin sekolah account if password provided (optimized)
        if (!empty($row['password_admin'])) {
            try {
                $user = \App\Models\User::create([
                    'name' => $row['kepala_sekolah'] ?? 'Admin Sekolah',
                    'email' => $row['email'],
                    'password' => \Hash::make($row['password_admin']),
                    'school_id' => $school->id,
                ]);
                $user->assignRole('admin_sekolah');

                // Reduced logging for performance - only log every 50th record
                if ($index % 50 == 0) {
                    \Log::info("Admin sekolah accounts created via import. Progress: " . ($index + 1) . " records processed.");
                }
            } catch (\Exception $e) {
                \Log::error("Failed to create admin sekolah account via import: " . $e->getMessage());
                $this->addWarning($index, "Sekolah berhasil dibuat, tetapi gagal membuat akun admin sekolah. Silakan buat manual di User Management.");
            }
        }

        return true;
    }

    protected function updateSchool($row, $index)
    {
        // Validasi sudah dilakukan di fase 1, langsung proses data
        $school = School::where('npsn', $row['npsn'])->first();
        if (!$school) {
            // Seharusnya tidak sampai ke sini karena sudah divalidasi di fase 1
            \Log::error('[IMPORT] Sekolah tidak ditemukan untuk update, tapi lolos validasi fase 1: ' . $row['npsn']);
            return false;
        }

        // Update sekolah
        $school->update([
            'name' => $row['nama_sekolah'],
            'education_level' => $row['jenjang_pendidikan'],
            'status' => $row['status'],
            'address' => $row['alamat'],
            'desa' => $row['desa'] ?? $school->desa,
            'kecamatan' => $row['kecamatan'] ?? $school->kecamatan,
            'kabupaten_kota' => $row['kabupaten_kota'] ?? $school->kabupaten_kota,
            'provinsi' => $row['provinsi'] ?? $school->provinsi,
            'google_maps_link' => $row['google_maps_link'] ?? $school->google_maps_link,
            'latitude' => $row['latitude'] ?? $school->latitude,
            'longitude' => $row['longitude'] ?? $school->longitude,
            'phone' => $row['telepon'] ?? $school->phone,
            'email' => $row['email'] ?? $school->email,
            'website' => $row['website'] ?? $school->website,
            'headmaster' => $row['kepala_sekolah'] ?? $school->headmaster,
        ]);

        // Create or update admin sekolah account if password provided
        if (!empty($row['password_admin'])) {
            try {
                $user = \App\Models\User::updateOrCreate(
                    ['email' => $row['email']],
                    [
                        'name' => $row['kepala_sekolah'] ?? 'Admin Sekolah',
                        'password' => \Hash::make($row['password_admin']),
                        'school_id' => $school->id,
                    ]
                );

                if (!$user->hasRole('admin_sekolah')) {
                    $user->assignRole('admin_sekolah');
                }

                // Reduced logging for performance - only log every 50th record
                if ($index % 50 == 0) {
                    \Log::info("Admin sekolah accounts updated via import. Progress: " . ($index + 1) . " records processed.");
                }
            } catch (\Exception $e) {
                \Log::error("Failed to update admin sekolah account via import: " . $e->getMessage());
                $this->addWarning($index, "Sekolah berhasil diperbarui, tetapi gagal membuat/update akun admin sekolah. Silakan buat manual di User Management.");
            }
        }

        return true;
    }

    protected function deleteSchool($row, $index)
    {
        // Validasi sudah dilakukan di fase 1, langsung proses data
        $school = School::where('npsn', $row['npsn'])->first();
        if (!$school) {
            // Seharusnya tidak sampai ke sini karena sudah divalidasi di fase 1
            \Log::error('[IMPORT] Sekolah tidak ditemukan untuk delete, tapi lolos validasi fase 1: ' . $row['npsn']);
            return false;
        }

        // Detach users
        $school->users()->update(['school_id' => null]);

        // Soft delete
        $school->delete();
        return true;
    }

    protected function validateRequired($row, $index)
    {
        $required = ['npsn', 'nama_sekolah', 'jenjang_pendidikan', 'status', 'alamat', 'email'];
        $hasError = false;
        foreach ($required as $field) {
            if (empty($row[$field])) {
                $this->addError($index, "Kolom '{$field}' wajib diisi.", $row);
                $hasError = true;
            }
        }
        // Validasi panjang minimum untuk name (warning saja)
        if (!empty($row['nama_sekolah']) && strlen($row['nama_sekolah']) < 3) {
            $this->addWarning($index, "Nama sekolah sebaiknya minimal 3 karakter.");
        }
        // Validasi panjang maksimum
        if (!empty($row['npsn']) && strlen($row['npsn']) > 20) {
            $this->addError($index, "NPSN maksimal 20 karakter.", $row);
            $hasError = true;
        }
        if (!empty($row['nama_sekolah']) && strlen($row['nama_sekolah']) > 255) {
            $this->addWarning($index, "Nama sekolah sebaiknya maksimal 255 karakter.");
        }
        if (!empty($row['telepon']) && strlen($row['telepon']) > 20) {
            $this->addWarning($index, "Nomor telepon dipotong maksimal 20 karakter.", $row);
            $row['telepon'] = substr($row['telepon'], 0, 20);
        }
        if (!empty($row['kepala_sekolah']) && strlen($row['kepala_sekolah']) > 255) {
            $this->addWarning($index, "Nama kepala sekolah dipotong maksimal 255 karakter.", $row);
            $row['kepala_sekolah'] = substr($row['kepala_sekolah'], 0, 255);
        }

        // Validasi website format
        if (!empty($row['website'])) {
            if (!filter_var($row['website'], FILTER_VALIDATE_URL)) {
                $this->addWarning($index, "Format website tidak valid: " . $row['website']);
            }
            if (strlen($row['website']) > 255) {
                $this->addWarning($index, "Website URL dipotong maksimal 255 karakter.", $row);
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
        // Tidak perlu warning spam - petunjuk sudah ada di Excel template
        // Hanya validasi panjang nama jika diperlukan
        $name = $row['nama_sekolah'];

        if (!empty($name) && strlen($name) < 3) {
            $this->addWarning($index, "Nama sekolah sebaiknya minimal 3 karakter.");
        }

        if (!empty($name) && strlen($name) > 255) {
            $this->addWarning($index, "Nama sekolah sebaiknya maksimal 255 karakter.");
        }
    }

    protected function addError($index, $message, $row = null)
    {
        $info = '';
        if ($row) {
            $infoParts = [];
            if (!empty($row['npsn'])) $infoParts[] = 'NPSN: ' . $row['npsn'];
            if (!empty($row['nama_sekolah'])) $infoParts[] = 'Nama: ' . $row['nama_sekolah'];
            if ($infoParts) $info = ' (' . implode(', ', $infoParts) . ')';
        }
        $errorMsg = "Baris " . ($index + 2) . "{$info}: {$message}";
        $this->results['errors'][] = $errorMsg;
        \Log::error('[IMPORT] Error pada baris ' . ($index + 2) . ': ' . $errorMsg);
    }

    protected function addWarning($index, $message)
    {
        $this->results['warnings'][] = "Row " . ($index + 2) . ": {$message}";
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
