<?php

namespace App\Services;

use App\Imports\SchoolImport;
use App\Imports\UltraFastSchoolImport;
use App\Jobs\ProcessSchoolImportJob;
use App\Models\ImportProgress;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class OptimizedSchoolImportService
{
    public function processExcel(UploadedFile $file, $importId = null, $strategy = 'auto')
    {
        $fileSize = $file->getSize();
        $filePath = $file->store('imports', 'local');

        // Estimate row count based on file size (rough estimate: 1KB per row)
        $estimatedRows = intval($fileSize / 1024);

        Log::info('[OPTIMIZED_SERVICE] Starting import', [
            'file_size' => $fileSize,
            'estimated_rows' => $estimatedRows,
            'strategy' => $strategy
        ]);

        // Auto-select strategy based on file size and estimated rows
        if ($strategy === 'auto') {
            $strategy = $this->selectOptimalStrategy($fileSize, $estimatedRows);
        }

        switch ($strategy) {
            case 'queue':
                return $this->processViaQueue($filePath, $importId);

            case 'ultra_fast':
                return $this->processUltraFast($filePath, $importId);

            case 'chunked':
                return $this->processChunked($filePath, $importId);

            default:
                return $this->processStandard($filePath, $importId);
        }
    }

    protected function selectOptimalStrategy($fileSize, $estimatedRows): string
    {
        // File size thresholds
        $smallFile = 1024 * 1024; // 1MB
        $mediumFile = 5 * 1024 * 1024; // 5MB
        $largeFile = 20 * 1024 * 1024; // 20MB

        if ($fileSize > $largeFile || $estimatedRows > 1000) {
            return 'queue'; // Background processing for large files
        } elseif ($fileSize > $mediumFile || $estimatedRows > 500) {
            return 'ultra_fast'; // Ultra fast processing for medium files
        } elseif ($fileSize > $smallFile || $estimatedRows > 100) {
            return 'chunked'; // Chunked processing for small-medium files
        } else {
            return 'standard'; // Standard processing for small files
        }
    }

    protected function processViaQueue($filePath, $importId)
    {
        Log::info('[OPTIMIZED_SERVICE] Using queue strategy');

        // Dispatch to queue for background processing
        ProcessSchoolImportJob::dispatch($filePath, $importId, auth()->id())
            ->onQueue('imports')
            ->delay(now()->addSeconds(2)); // Small delay to ensure UI is ready

        return [
            'strategy' => 'queue',
            'message' => 'Import diproses di background. Anda akan melihat progress secara real-time.',
            'total' => 0,
            'processed' => 0,
            'success' => 0,
            'failed' => 0,
            'errors' => [],
            'warnings' => []
        ];
    }

    protected function processUltraFast($filePath, $importId)
    {
        Log::info('[OPTIMIZED_SERVICE] Using ultra fast strategy');

        // Set optimal settings for ultra fast processing
        set_time_limit(600); // 10 minutes
        ini_set('memory_limit', '1024M'); // 1GB

        $import = new UltraFastSchoolImport();
        $import->setImportId($importId);

        Excel::import($import, $filePath);

        // Clean up
        Storage::delete($filePath);

        $results = $import->getResults();
        $results['strategy'] = 'ultra_fast';
        $results['total'] = $results['success'] + $results['failed'];
        $results['processed'] = $results['total'];

        return $results;
    }

    protected function processChunked($filePath, $importId)
    {
        Log::info('[OPTIMIZED_SERVICE] Using chunked strategy');

        // Set optimal settings for chunked processing
        set_time_limit(300); // 5 minutes
        ini_set('memory_limit', '512M'); // 512MB

        $import = new UltraFastSchoolImport();
        $import->setImportId($importId);

        // Process with chunking
        Excel::import($import, $filePath);

        // Clean up
        Storage::delete($filePath);

        $results = $import->getResults();
        $results['strategy'] = 'chunked';
        $results['total'] = $results['success'] + $results['failed'];
        $results['processed'] = $results['total'];

        return $results;
    }

    protected function processStandard($filePath, $importId)
    {
        Log::info('[OPTIMIZED_SERVICE] Using standard strategy');

        $import = new SchoolImport();
        $import->setImportId($importId);

        Excel::import($import, $filePath);

        // Clean up
        Storage::delete($filePath);

        $results = $import->getResults();
        $results['strategy'] = 'standard';
        $results['total'] = $results['success'] + $results['failed'];
        $results['processed'] = $results['total'];

        return $results;
    }

    public function getImportProgress($importId)
    {
        $progress = ImportProgress::where('import_id', $importId)->first();

        if (!$progress) {
            return [
                'status' => 'not_found',
                'message' => 'Import progress not found'
            ];
        }

        return [
            'status' => $progress->status,
            'total' => $progress->total,
            'processed' => $progress->processed,
            'success' => $progress->success,
            'failed' => $progress->failed,
            'errors' => $progress->errors ?? [],
            'warnings' => $progress->warnings ?? [],
            'progress_percentage' => $progress->progress_percentage,
            'elapsed_time' => $progress->elapsed_time,
            'started_at' => $progress->started_at,
            'completed_at' => $progress->completed_at,
        ];
    }

    public function createImportProgress($importId, $userId, $strategy = null)
    {
        return ImportProgress::create([
            'import_id' => $importId,
            'user_id' => $userId,
            'status' => 'initializing',
            'total' => 0,
            'processed' => 0,
            'success' => 0,
            'failed' => 0,
            'errors' => [],
            'warnings' => [],
            'started_at' => now(),
        ]);
    }

    public function getOptimalSettings($estimatedRows)
    {
        if ($estimatedRows > 1000) {
            return [
                'strategy' => 'queue',
                'chunk_size' => 100,
                'memory_limit' => '2048M',
                'time_limit' => 1800,
                'recommended' => 'Background processing untuk file besar'
            ];
        } elseif ($estimatedRows > 500) {
            return [
                'strategy' => 'ultra_fast',
                'chunk_size' => 100,
                'memory_limit' => '1024M',
                'time_limit' => 600,
                'recommended' => 'Ultra fast processing untuk file medium'
            ];
        } elseif ($estimatedRows > 100) {
            return [
                'strategy' => 'chunked',
                'chunk_size' => 50,
                'memory_limit' => '512M',
                'time_limit' => 300,
                'recommended' => 'Chunked processing untuk file kecil-medium'
            ];
        } else {
            return [
                'strategy' => 'standard',
                'chunk_size' => 25,
                'memory_limit' => '256M',
                'time_limit' => 120,
                'recommended' => 'Standard processing untuk file kecil'
            ];
        }
    }
}
