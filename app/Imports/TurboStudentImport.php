<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\School;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Helpers\NormalizationHelper;

class TurboStudentImport implements ToCollection, WithHeadingRow
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
        Log::info('[TURBO_STUDENT] Starting TURBO import', [
            'rows' => $rows->count(),
            'user_id' => $user->id,
            'target' => '< 10 seconds'
        ]);

        // PRE-LOAD all data (single queries)
        $existingStudents = $this->preloadExistingStudents();
        $schoolMapping = $this->preloadSchoolMapping();

        // Prepare bulk data arrays
        $studentInserts = [];
        $studentUpdates = [];
        $deleteNisns = [];
        $processedCount = 0;

        // Process all rows in memory (super fast)
        foreach ($rows as $index => $row) {
            if ($this->isEmptyRow($row)) continue;

            $row = $this->castRowData($row);
            // NORMALIZATION
            $row['jenis_kelamin'] = NormalizationHelper::normalizeGender($row['jenis_kelamin'] ?? null);
            $row['aksi'] = NormalizationHelper::normalizeAction($row['aksi'] ?? null);
            if (array_key_exists('kip', $row)) {
                $row['kip'] = NormalizationHelper::normalizeYesNo($row['kip']);
            }

            // Fast validation (minimal checks)
            if (empty($row['nisn']) || empty($row['nama_lengkap'])) {
                $this->addError($index, "Missing NISN or nama_lengkap");
                continue;
            }

            // Get school_id
            $schoolId = $this->getSchoolId($row, $user, $schoolMapping);
            if (!$schoolId) {
                $this->addError($index, "School not found");
                continue;
            }

            $action = strtoupper($row['aksi'] ?? 'CREATE');

            switch ($action) {
                case 'CREATE':
                    if (isset($existingStudents[$row['nisn']])) {
                        $this->addWarning($index, "Student already exists: " . $row['nisn']);
                        continue 2;
                    }

                    $studentInserts[] = $this->prepareStudentData($row, $schoolId);
                    break;

                case 'UPDATE':
                    if (!isset($existingStudents[$row['nisn']])) {
                        $this->addError($index, "Student not found for update: " . $row['nisn']);
                        continue 2;
                    }

                    $studentUpdates[] = [
                        'nisn' => $row['nisn'],
                        'data' => $this->prepareStudentData($row, $schoolId)
                    ];
                    break;

                case 'DELETE':
                    if (!isset($existingStudents[$row['nisn']])) {
                        $this->addWarning($index, "Student not found for delete: " . $row['nisn']);
                        continue 2;
                    }

                    $deleteNisns[] = $row['nisn'];
                    break;
            }

            $processedCount++;
        }

        $prepTime = microtime(true) - $startTime;
        Log::info('[TURBO_STUDENT] Data prepared in memory', [
            'inserts' => count($studentInserts),
            'updates' => count($studentUpdates),
            'deletes' => count($deleteNisns),
            'prep_time' => number_format($prepTime * 1000, 2) . 'ms'
        ]);

        // All-or-nothing: abort if any validation errors were collected
        if (!empty($this->results['errors'])) {
            $this->results['failed'] = count($this->results['errors']);
            Log::error('[TURBO_STUDENT] Import aborted due to validation errors', [
                'errors_count' => $this->results['failed']
            ]);
            return;
        }

        // Execute all operations in single transaction (SUPER FAST!)
        DB::transaction(function () use ($studentInserts, $studentUpdates, $deleteNisns) {
            $transactionStart = microtime(true);

            // 1. Bulk DELETE (single query)
            if (!empty($deleteNisns)) {
                $deletedCount = Student::whereIn('nisn', $deleteNisns)->delete();
                $this->results['success'] += $deletedCount;
                Log::info('[TURBO_STUDENT] Bulk deleted', ['count' => $deletedCount]);
            }

            // 2. Bulk INSERT (single query)
            if (!empty($studentInserts)) {
                DB::table('students')->insert($studentInserts);
                $this->results['success'] += count($studentInserts);
                Log::info('[TURBO_STUDENT] Bulk inserted', ['count' => count($studentInserts)]);
            }

            // 3. Bulk UPDATE (batch operations)
            if (!empty($studentUpdates)) {
                $this->bulkUpdateStudents($studentUpdates);
                $this->results['success'] += count($studentUpdates);
                Log::info('[TURBO_STUDENT] Bulk updated', ['count' => count($studentUpdates)]);
            }

            $transactionTime = microtime(true) - $transactionStart;
            Log::info('[TURBO_STUDENT] Transaction completed', [
                'time' => number_format($transactionTime * 1000, 2) . 'ms'
            ]);
        });

        $totalTime = microtime(true) - $startTime;
        $recordsPerSecond = $processedCount > 0 ? $processedCount / $totalTime : 0;

        Log::info('[TURBO_STUDENT] TURBO IMPORT COMPLETED!', [
            'total_time' => number_format($totalTime, 2) . 's',
            'records_per_second' => number_format($recordsPerSecond, 0),
            'success' => $this->results['success'],
            'failed' => count($this->results['errors']),
            'target_10s_achieved' => $totalTime < 10 ? 'YES ✅' : 'NO ❌',
            'performance_rating' => $totalTime < 5 ? 'INCREDIBLE' : ($totalTime < 10 ? 'EXCELLENT' : 'GOOD')
        ]);

        $this->results['failed'] = count($this->results['errors']);
    }

    protected function preloadExistingStudents(): array
    {
        return DB::table('students')
            ->pluck('id', 'nisn')
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

    protected function prepareStudentData($row, $schoolId): array
    {
        return [
            'sekolah_id' => $schoolId,
            'nisn' => $row['nisn'],
            'nipd' => $row['nipd'] ?? null,
            'nama_lengkap' => $row['nama_lengkap'],
            'jenis_kelamin' => $row['jenis_kelamin'] ?? null,
            'tempat_lahir' => $row['tempat_lahir'] ?? null,
            'tanggal_lahir' => $row['tanggal_lahir'] ?? null,
            'agama' => $row['agama'] ?? null,
            'rombel' => $row['rombel'] ?? null,
            'status_siswa' => $row['status_siswa'] ?? 'aktif',
            'alamat' => $row['alamat'] ?? null,
            'kelurahan' => $row['kelurahan'] ?? null,
            'kecamatan' => $row['kecamatan'] ?? null,
            'kode_pos' => $row['kode_pos'] ?? null,
            'nama_ayah' => $row['nama_ayah'] ?? null,
            'pekerjaan_ayah' => $row['pekerjaan_ayah'] ?? null,
            'nama_ibu' => $row['nama_ibu'] ?? null,
            'pekerjaan_ibu' => $row['pekerjaan_ibu'] ?? null,
            'anak_ke' => $row['anak_ke'] ?? null,
            'jumlah_saudara' => $row['jumlah_saudara'] ?? null,
            'no_hp' => $row['no_hp'] ?? null,
            'kip' => $this->convertKipToBoolean($row['kip'] ?? null),
            'transportasi' => $row['transportasi'] ?? null,
            'jarak_rumah_sekolah' => $row['jarak_rumah_sekolah'] ?? null,
            'tinggi_badan' => $row['tinggi_badan'] ?? null,
            'berat_badan' => $row['berat_badan'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    protected function bulkUpdateStudents(array $updates): void
    {
        foreach ($updates as $update) {
            DB::table('students')
                ->where('nisn', $update['nisn'])
                ->update(array_merge($update['data'], ['updated_at' => now()]));
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
        if (isset($row['nisn'])) {
            $row['nisn'] = (string) $row['nisn'];
        }
        if (isset($row['npsn_sekolah'])) {
            $row['npsn_sekolah'] = (string) $row['npsn_sekolah'];
        }

        return $row;
    }

    protected function convertKipToBoolean($value)
    {
        if (empty($value)) return null;

        $value = strtolower(trim($value));
        if (in_array($value, ['ya', 'yes', '1', 'true'])) return true;
        if (in_array($value, ['tidak', 'no', '0', 'false'])) return false;

        return null;
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
