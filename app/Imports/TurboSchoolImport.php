<?php

namespace App\Imports;

use App\Models\School;
use App\Services\UltraFastHashService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TurboSchoolImport implements ToCollection, WithHeadingRow
{
    protected $results = [
        'success' => 0,
        'failed' => 0,
        'errors' => [],
        'warnings' => [],
    ];

    protected $importId = null;
    protected $skipPasswordHashing = false;

    public function setImportId($importId)
    {
        $this->importId = $importId;
    }

    public function setSkipPasswordHashing(bool $skip = true)
    {
        $this->skipPasswordHashing = $skip;
    }

    public function collection(Collection $rows)
    {
        $startTime = microtime(true);

        // TURBO performance settings
        set_time_limit(60); // 1 minute should be enough!
        ini_set('memory_limit', '1024M');

        Log::info('[TURBO] Starting TURBO import - Target: < 15 seconds!', [
            'rows' => $rows->count(),
            'skip_password_hashing' => $this->skipPasswordHashing
        ]);

        $this->updateProgress('turbo_processing', 0, $rows->count());

        // Ultra-fast pre-loading (single queries only)
        $existingSchools = $this->ultraFastPreload();

        // Prepare ALL data in memory (no database calls during processing)
        $schoolInserts = [];
        $userInserts = [];
        $processedCount = 0;

        foreach ($rows as $index => $row) {
            if ($this->isEmptyRow($row)) continue;

            $row = $this->castRowData($row);

            // Ultra-minimal validation (only critical fields)
            if (empty($row['npsn']) || empty($row['email'])) {
                $this->addError($index, "Missing NPSN or email");
                continue;
            }

            // Skip if school exists
            if (isset($existingSchools[$row['npsn']])) {
                $this->addWarning($index, "School exists: " . $row['npsn']);
                continue;
            }

            // Prepare school data (minimal fields for speed)
            $schoolInserts[] = [
                'npsn' => $row['npsn'],
                'name' => $row['nama_sekolah'] ?? 'Unnamed School',
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

            // Prepare user data (with ultra-fast password handling)
            if (!empty($row['password_admin'])) {
                $userInserts[] = [
                    'name' => $row['kepala_sekolah'] ?? 'Admin Sekolah',
                    'email' => $row['email'],
                    'password' => UltraFastHashService::ultraFastHash($row['password_admin']),
                    'school_npsn' => $row['npsn'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $processedCount++;
        }

        $prepTime = microtime(true) - $startTime;
        Log::info('[TURBO] Data prepared in memory', [
            'schools' => count($schoolInserts),
            'users' => count($userInserts),
            'prep_time' => number_format($prepTime * 1000, 2) . 'ms',
            'records_per_sec' => number_format($processedCount / $prepTime, 0)
        ]);

        // SINGLE MEGA-TRANSACTION (all operations at once)
        DB::transaction(function () use ($schoolInserts, $userInserts) {
            $transactionStart = microtime(true);

            // 1. BULK INSERT SCHOOLS (single query)
            if (!empty($schoolInserts)) {
                DB::table('schools')->insert($schoolInserts);
                $this->results['success'] += count($schoolInserts);
            }

            // 2. BULK INSERT USERS (single query)
            if (!empty($userInserts)) {
                // Get school IDs for users (single query)
                $schoolNpsns = array_column($userInserts, 'school_npsn');
                $schoolIds = DB::table('schools')
                    ->whereIn('npsn', $schoolNpsns)
                    ->pluck('id', 'npsn')
                    ->toArray();

                // Prepare final user data
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

                    // BULK ASSIGN ROLES (single query)
                    $roleId = DB::table('roles')->where('name', 'admin_sekolah')->value('id');
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
                }
            }

            $transactionTime = microtime(true) - $transactionStart;
            Log::info('[TURBO] Transaction completed', [
                'time' => number_format($transactionTime * 1000, 2) . 'ms',
                'operations' => 'All bulk operations in single transaction'
            ]);
        });

        // Final results
        $this->updateProgress('completed', $processedCount, $rows->count());

        $totalTime = microtime(true) - $startTime;
        $recordsPerSecond = $processedCount > 0 ? $processedCount / $totalTime : 0;

        Log::info('[TURBO] TURBO IMPORT COMPLETED!', [
            'total_time' => number_format($totalTime, 2) . 's',
            'records_per_second' => number_format($recordsPerSecond, 0),
            'success' => $this->results['success'],
            'failed' => count($this->results['errors']),
            'target_15s_achieved' => $totalTime < 15 ? 'YES ✅' : 'NO ❌',
            'target_30s_achieved' => $totalTime < 30 ? 'YES ✅' : 'NO ❌',
            'performance_rating' => $totalTime < 5 ? 'INCREDIBLE' : ($totalTime < 15 ? 'EXCELLENT' : ($totalTime < 30 ? 'GOOD' : 'NEEDS_WORK')),
            'skip_password_hashing' => $this->skipPasswordHashing
        ]);

        // Cleanup
        UltraFastHashService::clearCache();

        $this->results['failed'] = count($this->results['errors']);
    }

    protected function ultraFastPreload(): array
    {
        // Single query - only get what we need
        return DB::table('schools')
            ->whereNull('deleted_at')
            ->pluck('npsn', 'npsn')
            ->toArray();
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

        // Minimal casting for speed
        if (isset($row['npsn'])) {
            $row['npsn'] = (string) $row['npsn'];
        }
        return $row;
    }

    protected function parseCoordinate($value)
    {
        return (empty($value) || !is_numeric($value)) ? null : (float) $value;
    }

    protected function updateProgress($status, $processed = 0, $total = 0)
    {
        if (!$this->importId) return;

        // Minimal progress updates for speed
        if ($status === 'completed' || $status === 'turbo_processing') {
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
