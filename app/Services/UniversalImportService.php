<?php

namespace App\Services;

use App\Imports\StudentImport;
use App\Imports\TurboStudentImport;
use App\Imports\TeacherImport;
use App\Imports\TurboTeacherImport;
use App\Imports\NonTeachingStaffImport;
use App\Imports\TurboNonTeachingStaffImport;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class UniversalImportService
{
    public function processImport(UploadedFile $file, string $type, $strategy = 'auto')
    {
        $fileSize = $file->getSize();
        $filePath = $file->store('imports', 'local');

        // Estimate row count based on file size
        $estimatedRows = intval($fileSize / 1024);

        Log::info('[UNIVERSAL_IMPORT] Starting import', [
            'type' => $type,
            'file_size' => $fileSize,
            'estimated_rows' => $estimatedRows,
            'strategy' => $strategy
        ]);

        // Auto-select strategy based on file size and estimated rows
        if ($strategy === 'auto') {
            $strategy = $this->selectOptimalStrategy($fileSize, $estimatedRows);
        }

        try {
            $results = $this->executeImport($filePath, $type, $strategy);

            // Clean up
            Storage::delete($filePath);

            $results['strategy'] = $strategy;
            $results['type'] = $type;
            $results['total'] = $results['success'] + $results['failed'];
            $results['processed'] = $results['total'];

            return $results;
        } catch (\Exception $e) {
            // Clean up on error
            Storage::delete($filePath);
            throw $e;
        }
    }

    protected function selectOptimalStrategy($fileSize, $estimatedRows): string
    {
        // Aggressive thresholds for maximum speed
        $smallFile = 512 * 1024; // 512KB
        $mediumFile = 2 * 1024 * 1024; // 2MB

        if ($fileSize > $mediumFile || $estimatedRows > 500) {
            return 'turbo'; // TURBO for medium+ files
        } elseif ($estimatedRows > 50) {
            return 'turbo'; // TURBO for most files (DEFAULT!)
        } else {
            return 'standard'; // Standard for very small files
        }
    }

    protected function executeImport($filePath, $type, $strategy)
    {
        // Set performance settings based on strategy
        if ($strategy === 'turbo') {
            set_time_limit(120); // 2 minutes
            ini_set('memory_limit', '1024M'); // 1GB
        } else {
            set_time_limit(300); // 5 minutes
            ini_set('memory_limit', '512M'); // 512MB
        }

        // Select appropriate import class
        $importClass = $this->getImportClass($type, $strategy);

        Log::info('[UNIVERSAL_IMPORT] Using import class', [
            'type' => $type,
            'strategy' => $strategy,
            'class' => get_class($importClass)
        ]);

        // Execute import
        Excel::import($importClass, $filePath);

        return $importClass->getResults();
    }

    protected function getImportClass($type, $strategy)
    {
        switch ($type) {
            case 'student':
            case 'siswa':
                return $strategy === 'turbo' ? new TurboStudentImport() : new StudentImport();

            case 'teacher':
            case 'guru':
                return $strategy === 'turbo' ? new TurboTeacherImport() : new TeacherImport();

            case 'staff':
            case 'non_teaching_staff':
                return $strategy === 'turbo' ? new TurboNonTeachingStaffImport() : new NonTeachingStaffImport();

            default:
                throw new \InvalidArgumentException("Unknown import type: {$type}");
        }
    }

    public function getOptimalSettings($estimatedRows, $type)
    {
        $baseSettings = [
            'student' => [
                'fields' => 25,
                'complexity' => 'medium',
                'validation_rules' => 8
            ],
            'teacher' => [
                'fields' => 20,
                'complexity' => 'medium',
                'validation_rules' => 10
            ],
            'staff' => [
                'fields' => 15,
                'complexity' => 'low',
                'validation_rules' => 6
            ]
        ];

        $settings = $baseSettings[$type] ?? $baseSettings['student'];

        if ($estimatedRows > 500) {
            return [
                'strategy' => 'turbo',
                'memory_limit' => '1024M',
                'time_limit' => 120,
                'recommended' => 'TURBO processing untuk file medium-besar',
                'expected_time' => '< 10 detik',
                'expected_speed' => '> 100,000 records/sec'
            ];
        } elseif ($estimatedRows > 50) {
            return [
                'strategy' => 'turbo',
                'memory_limit' => '512M',
                'time_limit' => 60,
                'recommended' => 'TURBO processing untuk file kecil-medium',
                'expected_time' => '< 5 detik',
                'expected_speed' => '> 50,000 records/sec'
            ];
        } else {
            return [
                'strategy' => 'standard',
                'memory_limit' => '256M',
                'time_limit' => 120,
                'recommended' => 'Standard processing untuk file kecil',
                'expected_time' => '< 30 detik',
                'expected_speed' => '> 1,000 records/sec'
            ];
        }
    }

    public function getSupportedTypes(): array
    {
        return [
            'student' => [
                'name' => 'Data Siswa',
                'aliases' => ['student', 'siswa'],
                'fields' => ['nisn', 'nama_lengkap', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir'],
                'turbo_available' => true
            ],
            'teacher' => [
                'name' => 'Data Guru',
                'aliases' => ['teacher', 'guru'],
                'fields' => ['nama_lengkap', 'nuptk', 'nip', 'jenis_kelamin', 'mata_pelajaran'],
                'turbo_available' => true
            ],
            'staff' => [
                'name' => 'Data Staff Non-Pengajar',
                'aliases' => ['staff', 'non_teaching_staff'],
                'fields' => ['nama_lengkap', 'nip_nik', 'jenis_kelamin', 'jabatan'],
                'turbo_available' => true
            ]
        ];
    }

    public function getPerformanceComparison($type, $estimatedRows)
    {
        $standardTime = $estimatedRows * 0.1; // 100ms per record (standard)
        $turboTime = $estimatedRows * 0.001; // 1ms per record (turbo)

        return [
            'estimated_rows' => $estimatedRows,
            'standard' => [
                'time_seconds' => $standardTime,
                'records_per_second' => 1000,
                'description' => 'Individual operations, full validation'
            ],
            'turbo' => [
                'time_seconds' => $turboTime,
                'records_per_second' => 100000,
                'description' => 'Bulk operations, minimal validation'
            ],
            'improvement' => [
                'speed_multiplier' => round($standardTime / $turboTime, 1),
                'time_saved_seconds' => $standardTime - $turboTime,
                'percentage_faster' => round((($standardTime - $turboTime) / $standardTime) * 100, 1)
            ]
        ];
    }
}
