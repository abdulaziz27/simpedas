<?php

namespace App\Jobs;

use App\Imports\UltraFastSchoolImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ProcessSchoolImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $importId;
    protected $userId;

    public $timeout = 1800; // 30 minutes
    public $tries = 3;

    public function __construct($filePath, $importId, $userId)
    {
        $this->filePath = $filePath;
        $this->importId = $importId;
        $this->userId = $userId;
    }

    public function handle()
    {
        Log::info('[QUEUE_IMPORT] Starting background import', [
            'import_id' => $this->importId,
            'user_id' => $this->userId,
            'file_path' => $this->filePath
        ]);

        try {
            // Set higher limits for background processing
            set_time_limit(1800); // 30 minutes
            ini_set('memory_limit', '2048M'); // 2GB

            // Update progress to processing
            $this->updateProgress('processing', 0, 0);

            // Process the import
            $import = new UltraFastSchoolImport();
            $import->setImportId($this->importId);

            Excel::import($import, $this->filePath);

            // Get results
            $results = $import->getResults();

            // Update final progress
            $this->updateProgress('completed', $results['success'] + $results['failed'], $results['success'] + $results['failed'], $results);

            // Clean up temporary file
            if (Storage::exists($this->filePath)) {
                Storage::delete($this->filePath);
            }

            Log::info('[QUEUE_IMPORT] Import completed successfully', [
                'import_id' => $this->importId,
                'success' => $results['success'],
                'failed' => $results['failed']
            ]);
        } catch (\Exception $e) {
            Log::error('[QUEUE_IMPORT] Import failed', [
                'import_id' => $this->importId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->updateProgress('error', 0, 0, ['errors' => [$e->getMessage()]]);

            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }

    protected function updateProgress($status, $processed, $total, $results = null)
    {
        $updateData = [
            'status' => $status,
            'processed' => $processed,
            'total' => $total,
            'updated_at' => now(),
        ];

        if ($results) {
            $updateData['success'] = $results['success'] ?? 0;
            $updateData['failed'] = $results['failed'] ?? 0;
            $updateData['errors'] = json_encode($results['errors'] ?? []);
            $updateData['warnings'] = json_encode($results['warnings'] ?? []);
        }

        if ($status === 'completed') {
            $updateData['completed_at'] = now();
        }

        \DB::table('import_progress')
            ->where('import_id', $this->importId)
            ->update($updateData);
    }

    public function failed(\Exception $exception)
    {
        Log::error('[QUEUE_IMPORT] Job failed permanently', [
            'import_id' => $this->importId,
            'error' => $exception->getMessage()
        ]);

        $this->updateProgress('failed', 0, 0, ['errors' => [$exception->getMessage()]]);
    }
}
