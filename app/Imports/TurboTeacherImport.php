<?php

namespace App\Imports;

use App\Models\Teacher;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Helpers\NormalizationHelper;

class TurboTeacherImport implements ToCollection, WithHeadingRow
{
    protected $results = [
        'success' => 0,
        'failed' => 0,
        'errors' => [],
        'warnings' => [],
    ];

    public function collection(Collection $rows)
    {
        $startTime = microtime(true);

        // TURBO performance settings
        set_time_limit(120);
        ini_set('memory_limit', '1024M');

        $user = Auth::user();
        Log::info('[TURBO_TEACHER] Starting TURBO import', [
            'rows' => $rows->count(),
            'user_id' => $user->id,
            'target' => '< 10 seconds'
        ]);

        // PRE-LOAD all data (single queries)
        $existingTeachers = $this->preloadExistingTeachers();
        $schoolMapping = $this->preloadSchoolMapping();

        // Prepare bulk data arrays
        $teacherInserts = [];
        $teacherUpdates = [];
        $deleteNuptks = [];
        $userInserts = [];
        $processedCount = 0;

        // Process all rows in memory (super fast)
        foreach ($rows as $index => $row) {
            if ($this->isEmptyRow($row)) continue;

            $row = $this->castRowData($row);
            // NORMALIZATION
            $row['jenis_kelamin'] = NormalizationHelper::normalizeGender($row['jenis_kelamin'] ?? null);
            $row['status_kepegawaian'] = NormalizationHelper::normalizeEmploymentStatus($row['status_kepegawaian'] ?? null);
            $row['aksi'] = NormalizationHelper::normalizeAction($row['aksi'] ?? null);

            // Fast validation (minimal checks)
            if (empty($row['nama_lengkap'])) {
                $this->addError($index, "Missing nama_lengkap");
                continue;
            }

            // Get school_id
            $schoolId = $this->getSchoolId($row, $user, $schoolMapping);
            if (!$schoolId) {
                $this->addError($index, "School not found");
                continue;
            }

            $action = strtoupper($row['aksi'] ?? 'CREATE');
            $identifier = $row['nuptk'] ?? $row['nip'] ?? $row['nama_lengkap'];

            switch ($action) {
                case 'CREATE':
                    if (!empty($row['nuptk']) && isset($existingTeachers[$row['nuptk']])) {
                        $this->addWarning($index, "Teacher already exists: " . $row['nuptk']);
                        continue 2;
                    }

                    $teacherInserts[] = $this->prepareTeacherData($row, $schoolId);

                    // Prepare user data if password provided
                    if (!empty($row['email']) && !empty($row['password_guru'])) {
                        $userInserts[] = [
                            'name' => $row['nama_lengkap'],
                            'email' => $row['email'],
                            'password' => \Hash::make($row['password_guru']),
                            'school_id' => $schoolId,
                            'teacher_nuptk' => $row['nuptk'], // Link to teacher
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                    break;

                case 'UPDATE':
                    if (!empty($row['nuptk']) && !isset($existingTeachers[$row['nuptk']])) {
                        $this->addError($index, "Teacher not found for update: " . $row['nuptk']);
                        continue 2;
                    }

                    $teacherUpdates[] = [
                        'nuptk' => $row['nuptk'],
                        'data' => $this->prepareTeacherData($row, $schoolId)
                    ];
                    break;

                case 'DELETE':
                    if (!empty($row['nuptk'])) {
                        if (!isset($existingTeachers[$row['nuptk']])) {
                            $this->addWarning($index, "Teacher not found for delete: " . $row['nuptk']);
                            continue 2;
                        }
                        $deleteNuptks[] = $row['nuptk'];
                    }
                    break;
            }

            $processedCount++;
        }

        $prepTime = microtime(true) - $startTime;
        Log::info('[TURBO_TEACHER] Data prepared in memory', [
            'inserts' => count($teacherInserts),
            'updates' => count($teacherUpdates),
            'deletes' => count($deleteNuptks),
            'prep_time' => number_format($prepTime * 1000, 2) . 'ms'
        ]);

        // All-or-nothing: abort if any validation errors were collected
        if (!empty($this->results['errors'])) {
            $this->results['failed'] = count($this->results['errors']);
            Log::error('[TURBO_TEACHER] Import aborted due to validation errors', [
                'errors_count' => $this->results['failed']
            ]);
            return;
        }

        // Execute all operations in single transaction (SUPER FAST!)
        DB::transaction(function () use ($teacherInserts, $teacherUpdates, $deleteNuptks, $userInserts) {
            $transactionStart = microtime(true);

            // 1. Bulk DELETE (single query)
            if (!empty($deleteNuptks)) {
                $deletedCount = Teacher::whereIn('nuptk', $deleteNuptks)->delete();
                $this->results['success'] += $deletedCount;
                Log::info('[TURBO_TEACHER] Bulk deleted', ['count' => $deletedCount]);
            }

            // 2. Bulk INSERT (single query)
            if (!empty($teacherInserts)) {
                DB::table('teachers')->insert($teacherInserts);
                $this->results['success'] += count($teacherInserts);
                Log::info('[TURBO_TEACHER] Bulk inserted', ['count' => count($teacherInserts)]);
            }

            // 3. Bulk UPDATE (batch operations)
            if (!empty($teacherUpdates)) {
                $this->bulkUpdateTeachers($teacherUpdates);
                $this->results['success'] += count($teacherUpdates);
                Log::info('[TURBO_TEACHER] Bulk updated', ['count' => count($teacherUpdates)]);
            }

            // 4. Bulk INSERT Users (if passwords provided)
            if (!empty($userInserts)) {
                // Link users to teachers after teachers are created
                $newTeachers = DB::table('teachers')
                    ->whereIn('nuptk', array_column($teacherInserts, 'nuptk'))
                    ->pluck('id', 'nuptk');

                $finalUserInserts = [];
                foreach ($userInserts as $user) {
                    if (isset($newTeachers[$user['teacher_nuptk']])) {
                        $user['teacher_id'] = $newTeachers[$user['teacher_nuptk']];
                        unset($user['teacher_nuptk']);
                        $finalUserInserts[] = $user;
                    }
                }

                if (!empty($finalUserInserts)) {
                    DB::table('users')->insert($finalUserInserts);

                    // Assign guru role to all new users
                    $newUsers = DB::table('users')
                        ->whereIn('email', array_column($finalUserInserts, 'email'))
                        ->pluck('id');

                    $roleId = DB::table('roles')->where('name', 'guru')->value('id');
                    if ($roleId) {
                        $roleAssignments = [];
                        foreach ($newUsers as $userId) {
                            $roleAssignments[] = [
                                'role_id' => $roleId,
                                'model_type' => 'App\\Models\\User',
                                'model_id' => $userId,
                            ];
                        }
                        DB::table('model_has_roles')->insert($roleAssignments);
                    }

                    Log::info('[TURBO_TEACHER] Bulk created users', ['count' => count($finalUserInserts)]);
                }
            }

            $transactionTime = microtime(true) - $transactionStart;
            Log::info('[TURBO_TEACHER] Transaction completed', [
                'time' => number_format($transactionTime * 1000, 2) . 'ms'
            ]);
        });

        $totalTime = microtime(true) - $startTime;
        $recordsPerSecond = $processedCount > 0 ? $processedCount / $totalTime : 0;

        Log::info('[TURBO_TEACHER] TURBO IMPORT COMPLETED!', [
            'total_time' => number_format($totalTime, 2) . 's',
            'records_per_second' => number_format($recordsPerSecond, 0),
            'success' => $this->results['success'],
            'failed' => count($this->results['errors']),
            'target_10s_achieved' => $totalTime < 10 ? 'YES ✅' : 'NO ❌',
            'performance_rating' => $totalTime < 5 ? 'INCREDIBLE' : ($totalTime < 10 ? 'EXCELLENT' : 'GOOD')
        ]);

        $this->results['failed'] = count($this->results['errors']);
    }

    protected function preloadExistingTeachers(): array
    {
        return DB::table('teachers')
            ->whereNotNull('nuptk')
            ->pluck('id', 'nuptk')
            ->toArray();
    }

    protected function preloadSchoolMapping(): array
    {
        return DB::table('schools')
            ->pluck('id', 'npsn')
            ->toArray();
    }

    protected function getSchoolId($row, $user, $schoolMapping): ?int
    {
        if ($user->hasRole('admin_sekolah')) {
            return $user->school_id;
        }

        // Admin dinas: get from NPSN
        $npsn = $row['npsn_sekolah'] ?? null;
        return $schoolMapping[$npsn] ?? null;
    }

    protected function prepareTeacherData($row, $schoolId): array
    {
        return [
            'sekolah_id' => $schoolId,
            'nama_lengkap' => $row['nama_lengkap'],
            'nuptk' => $row['nuptk'] ?? null,
            'nip' => $row['nip'] ?? null,
            'jenis_kelamin' => $row['jenis_kelamin'] ?? null,
            'tempat_lahir' => $row['tempat_lahir'] ?? null,
            'tanggal_lahir' => $row['tanggal_lahir'] ?? null,
            'agama' => $row['agama'] ?? null,
            'alamat' => $row['alamat'] ?? null,
            'telepon' => $row['telepon'] ?? null,
            'tingkat_pendidikan' => $row['tingkat_pendidikan'] ?? null,
            'jurusan_pendidikan' => $row['jurusan_pendidikan'] ?? null,
            'mata_pelajaran' => $row['mata_pelajaran'] ?? null,
            'status_kepegawaian' => $row['status_kepegawaian'] ?? null,
            'pangkat' => $row['pangkat'] ?? null,
            'jabatan' => $row['jabatan'] ?? null,
            'tmt' => $row['tmt'] ?? null,
            'status' => $row['status'] ?? 'aktif',
            'email' => $row['email'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    protected function bulkUpdateTeachers(array $updates): void
    {
        foreach ($updates as $update) {
            if (!empty($update['nuptk'])) {
                DB::table('teachers')
                    ->where('nuptk', $update['nuptk'])
                    ->update(array_merge($update['data'], ['updated_at' => now()]));
            }
        }
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
        if (isset($row['nuptk'])) {
            $row['nuptk'] = (string) $row['nuptk'];
        }
        if (isset($row['nip'])) {
            $row['nip'] = (string) $row['nip'];
        }
        if (isset($row['npsn_sekolah'])) {
            $row['npsn_sekolah'] = (string) $row['npsn_sekolah'];
        }

        return $row;
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
