<?php

namespace App\Imports;

use App\Models\NonTeachingStaff;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TurboNonTeachingStaffImport implements ToCollection, WithHeadingRow
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
        Log::info('[TURBO_STAFF] Starting TURBO import', [
            'rows' => $rows->count(),
            'user_id' => $user->id,
            'target' => '< 10 seconds'
        ]);

        // PRE-LOAD all data (single queries)
        $existingStaff = $this->preloadExistingStaff();
        $schoolMapping = $this->preloadSchoolMapping();

        // Prepare bulk data arrays
        $staffInserts = [];
        $staffUpdates = [];
        $deleteNips = [];
        $processedCount = 0;

        // Process all rows in memory (super fast)
        foreach ($rows as $index => $row) {
            if ($this->isEmptyRow($row)) continue;

            $row = $this->castRowData($row);

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
            $identifier = $row['nip_nik'] ?? $row['nama_lengkap'];

            switch ($action) {
                case 'CREATE':
                    if (!empty($row['nip_nik']) && isset($existingStaff[$row['nip_nik']])) {
                        $this->addWarning($index, "Staff already exists: " . $row['nip_nik']);
                        continue 2;
                    }

                    $staffInserts[] = $this->prepareStaffData($row, $schoolId);
                    break;

                case 'UPDATE':
                    if (!empty($row['nip_nik']) && !isset($existingStaff[$row['nip_nik']])) {
                        $this->addError($index, "Staff not found for update: " . $row['nip_nik']);
                        continue 2;
                    }

                    $staffUpdates[] = [
                        'nip_nik' => $row['nip_nik'],
                        'data' => $this->prepareStaffData($row, $schoolId)
                    ];
                    break;

                case 'DELETE':
                    if (!empty($row['nip_nik'])) {
                        if (!isset($existingStaff[$row['nip_nik']])) {
                            $this->addWarning($index, "Staff not found for delete: " . $row['nip_nik']);
                            continue 2;
                        }
                        $deleteNips[] = $row['nip_nik'];
                    }
                    break;
            }

            $processedCount++;
        }

        $prepTime = microtime(true) - $startTime;
        Log::info('[TURBO_STAFF] Data prepared in memory', [
            'inserts' => count($staffInserts),
            'updates' => count($staffUpdates),
            'deletes' => count($deleteNips),
            'prep_time' => number_format($prepTime * 1000, 2) . 'ms'
        ]);

        // Execute all operations in single transaction (SUPER FAST!)
        DB::transaction(function () use ($staffInserts, $staffUpdates, $deleteNips) {
            $transactionStart = microtime(true);

            // 1. Bulk DELETE (single query)
            if (!empty($deleteNips)) {
                $deletedCount = NonTeachingStaff::whereIn('nip_nik', $deleteNips)->delete();
                $this->results['success'] += $deletedCount;
                Log::info('[TURBO_STAFF] Bulk deleted', ['count' => $deletedCount]);
            }

            // 2. Bulk INSERT (single query)
            if (!empty($staffInserts)) {
                DB::table('non_teaching_staff')->insert($staffInserts);
                $this->results['success'] += count($staffInserts);
                Log::info('[TURBO_STAFF] Bulk inserted', ['count' => count($staffInserts)]);
            }

            // 3. Bulk UPDATE (batch operations)
            if (!empty($staffUpdates)) {
                $this->bulkUpdateStaff($staffUpdates);
                $this->results['success'] += count($staffUpdates);
                Log::info('[TURBO_STAFF] Bulk updated', ['count' => count($staffUpdates)]);
            }

            $transactionTime = microtime(true) - $transactionStart;
            Log::info('[TURBO_STAFF] Transaction completed', [
                'time' => number_format($transactionTime * 1000, 2) . 'ms'
            ]);
        });

        $totalTime = microtime(true) - $startTime;
        $recordsPerSecond = $processedCount > 0 ? $processedCount / $totalTime : 0;

        Log::info('[TURBO_STAFF] TURBO IMPORT COMPLETED!', [
            'total_time' => number_format($totalTime, 2) . 's',
            'records_per_second' => number_format($recordsPerSecond, 0),
            'success' => $this->results['success'],
            'failed' => count($this->results['errors']),
            'target_10s_achieved' => $totalTime < 10 ? 'YES ✅' : 'NO ❌',
            'performance_rating' => $totalTime < 5 ? 'INCREDIBLE' : ($totalTime < 10 ? 'EXCELLENT' : 'GOOD')
        ]);

        $this->results['failed'] = count($this->results['errors']);
    }

    protected function preloadExistingStaff(): array
    {
        return DB::table('non_teaching_staff')
            ->whereNotNull('nip_nik')
            ->pluck('id', 'nip_nik')
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

    protected function prepareStaffData($row, $schoolId): array
    {
        return [
            'sekolah_id' => $schoolId,
            'nama_lengkap' => $row['nama_lengkap'],
            'nip_nik' => $row['nip_nik'] ?? null,
            'jenis_kelamin' => $row['jenis_kelamin'] ?? null,
            'tempat_lahir' => $row['tempat_lahir'] ?? null,
            'tanggal_lahir' => $row['tanggal_lahir'] ?? null,
            'agama' => $row['agama'] ?? null,
            'alamat' => $row['alamat'] ?? null,
            'telepon' => $row['telepon'] ?? null,
            'tingkat_pendidikan' => $row['tingkat_pendidikan'] ?? null,
            'jabatan' => $row['jabatan'] ?? null,
            'status_kepegawaian' => $row['status_kepegawaian'] ?? null,
            'tmt' => $row['tmt'] ?? null,
            'status' => $row['status'] ?? 'aktif',
            'email' => $row['email'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    protected function bulkUpdateStaff(array $updates): void
    {
        foreach ($updates as $update) {
            if (!empty($update['nip_nik'])) {
                DB::table('non_teaching_staff')
                    ->where('nip_nik', $update['nip_nik'])
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
        if (isset($row['nip_nik'])) {
            $row['nip_nik'] = (string) $row['nip_nik'];
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
