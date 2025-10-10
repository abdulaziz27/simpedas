<?php

namespace App\Imports;

use App\Models\School;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class UltraFastSchoolImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    protected $results = [
        'success' => 0,
        'failed' => 0,
        'errors' => [],
        'warnings' => [],
    ];

    protected $importId = null;
    protected $existingSchools = [];
    protected $activeSchools = [];
    protected $existingUsers = [];

    public function setImportId($importId)
    {
        $this->importId = $importId;
    }

    public function chunkSize(): int
    {
        return 100; // Process 100 rows at a time
    }

    public function collection(Collection $rows)
    {
        // Ultra performance settings
        set_time_limit(600); // 10 minutes
        ini_set('memory_limit', '1024M'); // 1GB

        Log::info('[ULTRA_FAST] Processing chunk', ['rows' => $rows->count()]);

        // Pre-load data once per chunk (not per row!)
        $this->preloadData();

        // Group operations by type for bulk processing
        $createData = [];
        $updateData = [];
        $deleteNpsns = [];
        $userCreateData = [];
        $userUpdateData = [];

        // Phase 1: Validate and group operations
        foreach ($rows as $index => $row) {
            if ($this->isEmptyRow($row)) continue;

            $row = $this->castRowData($row);
            $action = strtoupper($row['aksi'] ?? 'CREATE');

            if ($this->validateRow($row, $index, $action)) {
                switch ($action) {
                    case 'CREATE':
                        $schoolData = $this->prepareSchoolData($row);
                        $createData[] = $schoolData;

                        if (!empty($row['password_admin'])) {
                            $userCreateData[] = $this->prepareUserData($row, null);
                        }
                        break;

                    case 'UPDATE':
                        $schoolData = $this->prepareSchoolData($row);
                        $schoolData['npsn_key'] = $row['npsn']; // For WHERE clause
                        $updateData[] = $schoolData;

                        if (!empty($row['password_admin'])) {
                            $userUpdateData[] = $this->prepareUserData($row, null);
                        }
                        break;

                    case 'DELETE':
                        $deleteNpsns[] = $row['npsn'];
                        break;
                }
            }
        }

        // Phase 2: Execute bulk operations in single transaction
        DB::transaction(function () use ($createData, $updateData, $deleteNpsns, $userCreateData, $userUpdateData) {

            // 1. Bulk Delete (fastest - single query)
            if (!empty($deleteNpsns)) {
                $deletedCount = School::whereIn('npsn', $deleteNpsns)->delete();
                $this->results['success'] += $deletedCount;
                Log::info('[ULTRA_FAST] Bulk deleted', ['count' => $deletedCount]);
            }

            // 2. Bulk Create Schools (single INSERT)
            if (!empty($createData)) {
                $this->bulkCreateSchools($createData);
                $this->results['success'] += count($createData);
            }

            // 3. Bulk Update Schools (single UPDATE per field)
            if (!empty($updateData)) {
                $this->bulkUpdateSchools($updateData);
                $this->results['success'] += count($updateData);
            }

            // 4. Bulk Create/Update Users
            if (!empty($userCreateData) || !empty($userUpdateData)) {
                $this->bulkProcessUsers($userCreateData, $userUpdateData);
            }
        });

        $this->updateProgress();

        Log::info('[ULTRA_FAST] Chunk completed', [
            'success' => $this->results['success'],
            'failed' => $this->results['failed']
        ]);
    }

    protected function preloadData()
    {
        // Single query to get all existing schools
        $this->existingSchools = DB::table('schools')
            ->select('npsn', 'id', 'deleted_at')
            ->get()
            ->keyBy('npsn')
            ->toArray();

        $this->activeSchools = collect($this->existingSchools)
            ->filter(fn($school) => is_null($school->deleted_at))
            ->pluck('id', 'npsn')
            ->toArray();

        // Pre-load existing users by email
        $this->existingUsers = DB::table('users')
            ->select('email', 'id', 'school_id')
            ->get()
            ->keyBy('email')
            ->toArray();
    }

    protected function bulkCreateSchools(array $createData)
    {
        if (empty($createData)) return;

        // Prepare data for bulk insert
        $insertData = [];
        $now = now();

        foreach ($createData as $data) {
            $insertData[] = array_merge($data, [
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Single bulk insert
        DB::table('schools')->insert($insertData);

        Log::info('[ULTRA_FAST] Bulk created schools', ['count' => count($insertData)]);
    }

    protected function bulkUpdateSchools(array $updateData)
    {
        if (empty($updateData)) return;

        // Group updates by NPSN for efficient batch updates
        $updates = [];
        foreach ($updateData as $data) {
            $npsn = $data['npsn_key'];
            unset($data['npsn_key']);
            $updates[$npsn] = array_merge($data, ['updated_at' => now()]);
        }

        // Batch update using CASE WHEN for better performance
        $this->batchUpdateByNpsn($updates);

        Log::info('[ULTRA_FAST] Bulk updated schools', ['count' => count($updates)]);
    }

    protected function batchUpdateByNpsn(array $updates)
    {
        if (empty($updates)) return;

        $npsns = array_keys($updates);
        $fields = [
            'name',
            'education_level',
            'status',
            'address',
            'desa',
            'kecamatan',
            'kabupaten_kota',
            'provinsi',
            'google_maps_link',
            'latitude',
            'longitude',
            'phone',
            'email',
            'website',
            'headmaster',
            'updated_at'
        ];

        foreach ($fields as $field) {
            $cases = [];
            $hasValues = false;

            foreach ($updates as $npsn => $data) {
                if (isset($data[$field])) {
                    $value = is_null($data[$field]) ? 'NULL' : "'" . addslashes($data[$field]) . "'";
                    $cases[] = "WHEN npsn = '" . addslashes($npsn) . "' THEN " . $value;
                    $hasValues = true;
                }
            }

            if ($hasValues) {
                $sql = "UPDATE schools SET {$field} = CASE " . implode(' ', $cases) . " END WHERE npsn IN ('" . implode("','", array_map('addslashes', $npsns)) . "')";
                DB::statement($sql);
            }
        }
    }

    protected function bulkProcessUsers(array $createData, array $updateData)
    {
        // Get school IDs for newly created schools
        $schoolNpsns = array_merge(
            array_column($createData, 'school_npsn'),
            array_column($updateData, 'school_npsn')
        );

        $schoolIds = DB::table('schools')
            ->whereIn('npsn', $schoolNpsns)
            ->pluck('id', 'npsn')
            ->toArray();

        // Bulk create users
        if (!empty($createData)) {
            $insertUsers = [];
            $now = now();

            foreach ($createData as $userData) {
                $schoolId = $schoolIds[$userData['school_npsn']] ?? null;
                if ($schoolId) {
                    $insertUsers[] = [
                        'name' => $userData['name'],
                        'email' => $userData['email'],
                        'password' => $userData['password'],
                        'school_id' => $schoolId,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }

            if (!empty($insertUsers)) {
                DB::table('users')->insert($insertUsers);

                // Bulk assign roles
                $userIds = DB::table('users')
                    ->whereIn('email', array_column($insertUsers, 'email'))
                    ->pluck('id')
                    ->toArray();

                $roleAssignments = [];
                $roleId = DB::table('roles')->where('name', 'admin_sekolah')->value('id');

                foreach ($userIds as $userId) {
                    $roleAssignments[] = [
                        'role_id' => $roleId,
                        'model_type' => 'App\\Models\\User',
                        'model_id' => $userId,
                    ];
                }

                DB::table('model_has_roles')->insert($roleAssignments);
            }
        }

        // Bulk update users (using upsert for efficiency)
        if (!empty($updateData)) {
            foreach ($updateData as $userData) {
                $schoolId = $schoolIds[$userData['school_npsn']] ?? null;
                if ($schoolId) {
                    DB::table('users')
                        ->updateOrInsert(
                            ['email' => $userData['email']],
                            [
                                'name' => $userData['name'],
                                'password' => $userData['password'],
                                'school_id' => $schoolId,
                                'updated_at' => now(),
                            ]
                        );
                }
            }
        }

        Log::info('[ULTRA_FAST] Bulk processed users', [
            'created' => count($createData),
            'updated' => count($updateData)
        ]);
    }

    protected function validateRow($row, $index, $action): bool
    {
        // Fast validation - only essential checks
        if (empty($row['npsn']) || empty($row['nama_sekolah']) || empty($row['email'])) {
            $this->addError($index, "Missing required fields");
            return false;
        }

        if ($action === 'CREATE' && isset($this->activeSchools[$row['npsn']])) {
            $this->addError($index, "NPSN already exists");
            return false;
        }

        if (($action === 'UPDATE' || $action === 'DELETE') && !isset($this->existingSchools[$row['npsn']])) {
            $this->addError($index, "School not found for {$action}");
            return false;
        }

        return true;
    }

    protected function prepareSchoolData($row): array
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
            'email' => $row['email'],
            'website' => $row['website'] ?? null,
            'headmaster' => $row['kepala_sekolah'] ?? null,
        ];
    }

    protected function prepareUserData($row, $schoolId): array
    {
        return [
            'name' => $row['kepala_sekolah'] ?? 'Admin Sekolah',
            'email' => $row['email'],
            'password' => Hash::make($row['password_admin']),
            'school_npsn' => $row['npsn'], // Will be converted to school_id later
        ];
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

        // Cast important fields to proper types
        if (isset($row['npsn'])) {
            $row['npsn'] = (string) $row['npsn'];
        }
        if (isset($row['telepon'])) {
            $row['telepon'] = (string) $row['telepon'];
        }
        if (isset($row['latitude'])) {
            $row['latitude'] = is_numeric($row['latitude']) ? (float) $row['latitude'] : null;
        }
        if (isset($row['longitude'])) {
            $row['longitude'] = is_numeric($row['longitude']) ? (float) $row['longitude'] : null;
        }

        return $row;
    }

    protected function updateProgress()
    {
        if (!$this->importId) return;

        $progress = DB::table('import_progress')
            ->where('import_id', $this->importId)
            ->first();

        if ($progress) {
            DB::table('import_progress')
                ->where('import_id', $this->importId)
                ->update([
                    'success' => DB::raw('success + ' . $this->results['success']),
                    'failed' => DB::raw('failed + ' . $this->results['failed']),
                    'processed' => DB::raw('processed + ' . ($this->results['success'] + $this->results['failed'])),
                    'updated_at' => now(),
                ]);
        }
    }

    protected function addError($index, $message)
    {
        $this->results['errors'][] = "Row " . ($index + 2) . ": " . $message;
        $this->results['failed']++;
    }

    public function getResults()
    {
        return $this->results;
    }
}
