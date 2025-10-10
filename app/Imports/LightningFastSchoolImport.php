<?php

namespace App\Imports;

use App\Models\School;
use App\Models\User;
use App\Services\UltraFastHashService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LightningFastSchoolImport implements ToCollection, WithHeadingRow
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
        $startTime = microtime(true);

        // Lightning performance settings
        set_time_limit(120); // 2 minutes max
        ini_set('memory_limit', '1024M');

        Log::info('[LIGHTNING] Starting lightning-fast import', [
            'rows' => $rows->count(),
            'target' => '< 30 seconds'
        ]);

        // Skip validation phase for maximum speed - validate on-the-fly
        $this->updateProgress('processing', 0, $rows->count());

        // Pre-load ALL data in single queries
        $existingSchools = $this->preloadExistingSchools();
        $existingUsers = $this->preloadExistingUsers();
        $roleId = $this->getAdminSekolahRoleId();

        // Pre-generate common password hashes for speed
        UltraFastHashService::preGenerateCommonHashes();

        // Prepare bulk data arrays
        $schoolInserts = [];
        $userInserts = [];
        $passwordsToHash = [];
        $processedCount = 0;
        $errorCount = 0;

        // Process all rows in memory first (super fast)
        foreach ($rows as $index => $row) {
            if ($this->isEmptyRow($row)) continue;

            $row = $this->castRowData($row);

            // Fast validation (minimal checks)
            if (empty($row['npsn']) || empty($row['nama_sekolah']) || empty($row['email'])) {
                $this->addError($index, "Missing required fields");
                $errorCount++;
                continue;
            }

            // Check if school already exists
            if (isset($existingSchools[$row['npsn']])) {
                $this->addWarning($index, "School already exists: " . $row['npsn']);
                continue;
            }

            // Prepare school data
            $schoolData = [
                'npsn' => $row['npsn'],
                'name' => $row['nama_sekolah'],
                'education_level' => $row['jenjang_pendidikan'] ?? 'SD',
                'status' => $row['status'] ?? 'Swasta',
                'address' => $row['alamat'] ?? '',
                'desa' => $row['desa'] ?? null,
                'kecamatan' => $row['kecamatan'] ?? null,
                'kabupaten_kota' => $row['kabupaten_kota'] ?? null,
                'provinsi' => $row['provinsi'] ?? null,
                'google_maps_link' => $row['google_maps_link'] ?? null,
                'latitude' => $this->parseCoordinate($row['latitude'] ?? null),
                'longitude' => $this->parseCoordinate($row['longitude'] ?? null),
                'phone' => $row['telepon'] ?? null,
                'email' => $row['email'],
                'website' => $row['website'] ?? null,
                'headmaster' => $row['kepala_sekolah'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $schoolInserts[] = $schoolData;

            // Prepare user data if password provided (collect passwords for batch hashing)
            if (!empty($row['password_admin']) && !isset($existingUsers[$row['email']])) {
                $passwordKey = count($userInserts);
                $passwordsToHash[$passwordKey] = $row['password_admin'];

                $userInserts[] = [
                    'name' => $row['kepala_sekolah'] ?? 'Admin Sekolah',
                    'email' => $row['email'],
                    'password' => null, // Will be filled after batch hashing
                    'school_npsn' => $row['npsn'], // Will be converted to school_id later
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $processedCount++;
        }

        // Batch hash all passwords at once (MUCH faster than individual hashing)
        $hashedPasswords = [];
        if (!empty($passwordsToHash)) {
            $hashedPasswords = UltraFastHashService::batchProcessPasswords($passwordsToHash);

            // Fill in the hashed passwords
            foreach ($hashedPasswords as $key => $hashedPassword) {
                $userInserts[$key]['password'] = $hashedPassword;
            }
        }

        Log::info('[LIGHTNING] Data prepared in memory', [
            'schools' => count($schoolInserts),
            'users' => count($userInserts),
            'passwords_hashed' => count($hashedPasswords),
            'errors' => $errorCount,
            'time' => number_format((microtime(true) - $startTime) * 1000, 2) . 'ms'
        ]);

        // Execute all operations in single transaction (SUPER FAST!)
        DB::transaction(function () use ($schoolInserts, $userInserts, $roleId) {
            $transactionStart = microtime(true);

            // 1. Bulk insert schools (SINGLE QUERY)
            if (!empty($schoolInserts)) {
                DB::table('schools')->insert($schoolInserts);
                $this->results['success'] += count($schoolInserts);
                Log::info('[LIGHTNING] Schools inserted', ['count' => count($schoolInserts)]);
            }

            // 2. Bulk insert users (SINGLE QUERY)
            if (!empty($userInserts)) {
                // Get school IDs for users
                $schoolNpsns = array_column($userInserts, 'school_npsn');
                $schoolIds = DB::table('schools')
                    ->whereIn('npsn', $schoolNpsns)
                    ->pluck('id', 'npsn')
                    ->toArray();

                // Prepare final user data with school_id
                $finalUserInserts = [];
                foreach ($userInserts as $userData) {
                    $schoolId = $schoolIds[$userData['school_npsn']] ?? null;
                    if ($schoolId) {
                        unset($userData['school_npsn']);
                        $userData['school_id'] = $schoolId;
                        $finalUserInserts[] = $userData;
                    }
                }

                if (!empty($finalUserInserts)) {
                    DB::table('users')->insert($finalUserInserts);

                    // Bulk assign roles (SINGLE QUERY)
                    if ($roleId) {
                        $userEmails = array_column($finalUserInserts, 'email');
                        $userIds = DB::table('users')
                            ->whereIn('email', $userEmails)
                            ->pluck('id')
                            ->toArray();

                        $roleAssignments = [];
                        foreach ($userIds as $userId) {
                            $roleAssignments[] = [
                                'role_id' => $roleId,
                                'model_type' => 'App\\Models\\User',
                                'model_id' => $userId,
                            ];
                        }

                        if (!empty($roleAssignments)) {
                            DB::table('model_has_roles')->insert($roleAssignments);
                        }
                    }

                    Log::info('[LIGHTNING] Users and roles assigned', ['count' => count($finalUserInserts)]);
                }
            }

            Log::info('[LIGHTNING] Transaction completed', [
                'time' => number_format((microtime(true) - $transactionStart) * 1000, 2) . 'ms'
            ]);
        });

        // Final progress update
        $this->updateProgress('completed', $processedCount, $rows->count());

        $totalTime = microtime(true) - $startTime;
        Log::info('[LIGHTNING] Import completed', [
            'total_time' => number_format($totalTime, 2) . 's',
            'records_per_second' => number_format($processedCount / $totalTime, 0),
            'success' => $this->results['success'],
            'failed' => count($this->results['errors']),
            'target_achieved' => $totalTime < 30 ? 'YES ✅' : 'NO ❌',
            'performance_rating' => $totalTime < 10 ? 'EXCELLENT' : ($totalTime < 30 ? 'GOOD' : 'NEEDS_IMPROVEMENT')
        ]);

        // Clean up memory
        UltraFastHashService::clearCache();

        $this->results['failed'] = count($this->results['errors']);
    }

    protected function preloadExistingSchools()
    {
        return DB::table('schools')
            ->whereNull('deleted_at')
            ->pluck('id', 'npsn')
            ->toArray();
    }

    protected function preloadExistingUsers()
    {
        return DB::table('users')
            ->pluck('id', 'email')
            ->toArray();
    }

    protected function getAdminSekolahRoleId()
    {
        return DB::table('roles')
            ->where('name', 'admin_sekolah')
            ->value('id');
    }

    protected function isEmptyRow($row): bool
    {
        return collect($row)->filter()->isEmpty();
    }

    protected function castRowData($row): array
    {
        // Convert Collection to array if needed
        if ($row instanceof \Illuminate\Support\Collection) {
            $row = $row->toArray();
        }

        // Fast casting - only essential fields
        if (isset($row['npsn'])) {
            $row['npsn'] = (string) $row['npsn'];
        }
        if (isset($row['telepon'])) {
            $row['telepon'] = (string) $row['telepon'];
        }

        return $row;
    }

    protected function parseCoordinate($value)
    {
        if (empty($value) || !is_numeric($value)) {
            return null;
        }
        return (float) $value;
    }

    protected function updateProgress($status, $processed = 0, $total = 0)
    {
        if (!$this->importId) return;

        // Update progress less frequently for speed
        if ($processed % 100 == 0 || $status === 'completed' || $status === 'processing') {
            DB::table('import_progress')
                ->where('import_id', $this->importId)
                ->update([
                    'status' => $status,
                    'processed' => $processed,
                    'total' => $total,
                    'success' => $this->results['success'],
                    'failed' => count($this->results['errors']),
                    'updated_at' => now(),
                ]);
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
        return array_merge($this->results, [
            'total' => $this->results['success'] + $this->results['failed'],
            'processed' => $this->results['success'] + $this->results['failed'],
        ]);
    }
}
