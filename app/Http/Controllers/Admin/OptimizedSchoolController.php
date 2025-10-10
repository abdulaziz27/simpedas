<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\OptimizedSchoolImport;
use App\Imports\ChunkedSchoolImport;
use App\Jobs\ProcessSchoolImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class OptimizedSchoolController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240', // 10MB max
            'import_type' => 'required|in:optimized,chunked,queue'
        ]);

        $file = $request->file('file');
        $importType = $request->input('import_type');

        Log::info('[OPTIMIZED_IMPORT] Starting import', [
            'type' => $importType,
            'file_size' => $file->getSize(),
            'user_id' => auth()->id()
        ]);

        try {
            switch ($importType) {
                case 'optimized':
                    return $this->handleOptimizedImport($file);
                case 'chunked':
                    return $this->handleChunkedImport($file);
                case 'queue':
                    return $this->handleQueueImport($file);
                default:
                    return back()->with('error', 'Invalid import type');
            }
        } catch (\Exception $e) {
            Log::error('[OPTIMIZED_IMPORT] Import failed: ' . $e->getMessage());
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    protected function handleOptimizedImport($file)
    {
        $startTime = microtime(true);

        $import = new OptimizedSchoolImport();
        Excel::import($import, $file);

        $endTime = microtime(true);
        $executionTime = round($endTime - $startTime, 2);

        $results = $import->getResults();

        Log::info('[OPTIMIZED_IMPORT] Completed', [
            'execution_time' => $executionTime . 's',
            'success' => $results['success'],
            'failed' => $results['failed'],
            'errors' => count($results['errors'])
        ]);

        return back()->with(
            'success',
            "Optimized import completed in {$executionTime}s. " .
                "Success: {$results['success']}, Failed: {$results['failed']}"
        );
    }

    protected function handleChunkedImport($file)
    {
        $startTime = microtime(true);

        $import = new ChunkedSchoolImport();
        Excel::import($import, $file);

        $endTime = microtime(true);
        $executionTime = round($endTime - $startTime, 2);

        $results = $import->getResults();

        Log::info('[CHUNKED_IMPORT] Completed', [
            'execution_time' => $executionTime . 's',
            'success' => $results['success'],
            'failed' => $results['failed'],
            'errors' => count($results['errors'])
        ]);

        return back()->with(
            'success',
            "Chunked import completed in {$executionTime}s. " .
                "Success: {$results['success']}, Failed: {$results['failed']}"
        );
    }

    protected function handleQueueImport($file)
    {
        // Store file temporarily
        $filename = 'import_' . time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('temp', $filename);

        // Read and validate data first
        $import = new OptimizedSchoolImport();
        $data = Excel::toCollection($import, $file)->first();

        // Prepare validated data for queue
        $validatedRows = [];
        $existingSchools = \App\Models\School::withTrashed()->pluck('npsn', 'npsn')->toArray();
        $activeSchools = \App\Models\School::pluck('npsn', 'npsn')->toArray();

        foreach ($data as $index => $row) {
            if (collect($row)->filter()->isEmpty()) continue;

            $action = strtoupper($row['aksi'] ?? 'CREATE');
            $validatedRows[] = [
                'row' => $row,
                'index' => $index,
                'action' => $action
            ];
        }

        // Dispatch job
        ProcessSchoolImport::dispatch($validatedRows, auth()->id());

        // Clean up temp file
        Storage::delete($path);

        Log::info('[QUEUE_IMPORT] Job dispatched', [
            'total_records' => count($validatedRows),
            'user_id' => auth()->id()
        ]);

        return back()->with(
            'success',
            "Import job dispatched successfully. " .
                "Processing {$validatedRows} records in background. " .
                "Check logs for progress updates."
        );
    }

    public function showImportOptions()
    {
        return view('admin.schools.optimized-import', [
            'education_levels' => config('school.education_levels'),
            'statuses' => config('school.status')
        ]);
    }
}
