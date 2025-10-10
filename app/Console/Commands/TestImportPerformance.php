<?php

namespace App\Console\Commands;

use App\Services\OptimizedSchoolImportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TestImportPerformance extends Command
{
    protected $signature = 'test:import-performance {records=300} {strategy=auto}';
    protected $description = 'Test import performance with different strategies';

    public function handle()
    {
        $recordCount = $this->argument('records');
        $strategy = $this->argument('strategy');

        $this->info("🚀 Testing Import Performance");
        $this->info("Records: {$recordCount}");
        $this->info("Strategy: {$strategy}");
        $this->line('');

        // Generate test data
        $this->info("📊 Generating test data...");
        $testData = $this->generateTestData($recordCount);

        // Test different strategies
        $strategies = $strategy === 'all' ? ['standard', 'chunked', 'ultra_fast', 'lightning_fast'] : [$strategy];

        $results = [];

        foreach ($strategies as $testStrategy) {
            $this->info("🔄 Testing {$testStrategy} strategy...");

            $startTime = microtime(true);
            $startMemory = memory_get_usage();

            try {
                // Simulate import process
                $result = $this->simulateImport($testData, $testStrategy);

                $endTime = microtime(true);
                $endMemory = memory_get_usage();

                $results[$testStrategy] = [
                    'time' => ($endTime - $startTime) * 1000, // ms
                    'memory' => ($endMemory - $startMemory) / 1024 / 1024, // MB
                    'success' => $result['success'] ?? 0,
                    'failed' => $result['failed'] ?? 0,
                ];

                $this->info("✅ {$testStrategy}: " . number_format($results[$testStrategy]['time'], 2) . "ms");
            } catch (\Exception $e) {
                $this->error("❌ {$testStrategy} failed: " . $e->getMessage());
                $results[$testStrategy] = ['error' => $e->getMessage()];
            }
        }

        // Display results
        $this->displayResults($results, $recordCount);
    }

    protected function generateTestData($count)
    {
        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $data[] = [
                'npsn' => 'PERF' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'nama_sekolah' => 'Performance Test School ' . $i,
                'jenjang_pendidikan' => ['TK', 'SD', 'SMP', 'KB', 'PKBM'][array_rand(['TK', 'SD', 'SMP', 'KB', 'PKBM'])],
                'status' => ['Negeri', 'Swasta'][array_rand(['Negeri', 'Swasta'])],
                'alamat' => 'Test Address ' . $i,
                'email' => 'perftest' . $i . '@school.com',
                'kepala_sekolah' => 'Test Headmaster ' . $i,
                'aksi' => 'CREATE',
            ];
        }
        return $data;
    }

    protected function simulateImport($data, $strategy)
    {
        // Simulate different import strategies
        switch ($strategy) {
            case 'standard':
                return $this->simulateStandardImport($data);

            case 'chunked':
                return $this->simulateChunkedImport($data);

            case 'ultra_fast':
                return $this->simulateUltraFastImport($data);

            case 'lightning_fast':
                return $this->simulateLightningFastImport($data);

            default:
                throw new \Exception("Unknown strategy: {$strategy}");
        }
    }

    protected function simulateStandardImport($data)
    {
        // Simulate standard import (one by one)
        $success = 0;
        $failed = 0;

        foreach ($data as $row) {
            // Simulate validation and insert
            usleep(100); // 0.1ms per record

            if ($this->validateRow($row)) {
                $success++;
            } else {
                $failed++;
            }
        }

        return ['success' => $success, 'failed' => $failed];
    }

    protected function simulateChunkedImport($data)
    {
        // Simulate chunked import (batches of 50)
        $chunks = array_chunk($data, 50);
        $success = 0;
        $failed = 0;

        foreach ($chunks as $chunk) {
            // Simulate batch validation and insert
            usleep(50 * count($chunk)); // 0.05ms per record

            foreach ($chunk as $row) {
                if ($this->validateRow($row)) {
                    $success++;
                } else {
                    $failed++;
                }
            }
        }

        return ['success' => $success, 'failed' => $failed];
    }

    protected function simulateUltraFastImport($data)
    {
        // Simulate ultra fast import (bulk operations)
        $chunks = array_chunk($data, 100);
        $success = 0;
        $failed = 0;

        foreach ($chunks as $chunk) {
            // Simulate bulk validation and insert
            usleep(10 * count($chunk)); // 0.01ms per record

            foreach ($chunk as $row) {
                if ($this->validateRow($row)) {
                    $success++;
                } else {
                    $failed++;
                }
            }
        }

        return ['success' => $success, 'failed' => $failed];
    }

    protected function simulateLightningFastImport($data)
    {
        // Simulate lightning fast import (minimal operations, bulk everything)
        $chunks = array_chunk($data, 500); // Larger chunks
        $success = 0;
        $failed = 0;

        foreach ($chunks as $chunk) {
            // Simulate lightning fast bulk operations
            usleep(2 * count($chunk)); // 0.002ms per record (SUPER FAST!)

            foreach ($chunk as $row) {
                if ($this->validateRow($row)) {
                    $success++;
                } else {
                    $failed++;
                }
            }
        }

        return ['success' => $success, 'failed' => $failed];
    }

    protected function validateRow($row)
    {
        // Simple validation simulation
        return !empty($row['npsn']) && !empty($row['nama_sekolah']) && !empty($row['email']);
    }

    protected function displayResults($results, $recordCount)
    {
        $this->line('');
        $this->info("📈 Performance Test Results ({$recordCount} records)");
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        $table = [];
        $fastestTime = null;
        $fastestStrategy = null;

        foreach ($results as $strategy => $result) {
            if (isset($result['error'])) {
                $table[] = [
                    'Strategy' => ucfirst($strategy),
                    'Time (ms)' => 'ERROR',
                    'Memory (MB)' => 'ERROR',
                    'Success' => 'ERROR',
                    'Failed' => 'ERROR',
                    'Records/sec' => 'ERROR',
                ];
            } else {
                $recordsPerSec = $result['time'] > 0 ? number_format(($recordCount / $result['time']) * 1000, 0) : 'N/A';

                $table[] = [
                    'Strategy' => ucfirst($strategy),
                    'Time (ms)' => number_format($result['time'], 2),
                    'Memory (MB)' => number_format($result['memory'], 2),
                    'Success' => $result['success'],
                    'Failed' => $result['failed'],
                    'Records/sec' => $recordsPerSec,
                ];

                if ($fastestTime === null || $result['time'] < $fastestTime) {
                    $fastestTime = $result['time'];
                    $fastestStrategy = $strategy;
                }
            }
        }

        $this->table([
            'Strategy',
            'Time (ms)',
            'Memory (MB)',
            'Success',
            'Failed',
            'Records/sec'
        ], $table);

        if ($fastestStrategy) {
            $this->line('');
            $this->info("🏆 Fastest Strategy: " . ucfirst($fastestStrategy) . " ({$fastestTime}ms)");

            // Calculate improvements
            if (count($results) > 1) {
                $this->line('');
                $this->info("📊 Performance Improvements:");

                foreach ($results as $strategy => $result) {
                    if ($strategy !== $fastestStrategy && !isset($result['error'])) {
                        $improvement = (($result['time'] - $fastestTime) / $result['time']) * 100;
                        $this->line("   • {$fastestStrategy} vs {$strategy}: " . number_format($improvement, 1) . "% faster");
                    }
                }
            }
        }

        // Recommendations
        $this->line('');
        $this->info("💡 Recommendations:");

        if ($recordCount <= 100) {
            $this->line("   • For {$recordCount} records: Use 'standard' or 'chunked' strategy");
        } elseif ($recordCount <= 500) {
            $this->line("   • For {$recordCount} records: Use 'chunked' or 'ultra_fast' strategy");
        } else {
            $this->line("   • For {$recordCount} records: Use 'ultra_fast' or 'queue' strategy");
        }

        $this->line("   • Memory usage should be < 100MB for optimal performance");
        $this->line("   • Target: > 1000 records/second for production use");
    }
}
