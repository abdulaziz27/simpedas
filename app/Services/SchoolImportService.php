<?php

namespace App\Services;

use App\Imports\SchoolImport;
use App\Models\ImportLog;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class SchoolImportService
{
    public function processExcel(UploadedFile $file, $importId = null)
    {
        // Import data with progress tracking
        $import = new SchoolImport();
        $import->setImportId($importId);
        Excel::import($import, $file);

        // Get results
        $results = $import->getResults();

        // Add total and processed counts
        $results['total'] = $results['success'] + $results['failed'];
        $results['processed'] = $results['success'] + $results['failed'];

        // Log import
        $this->logImport($file->getClientOriginalName(), $results);

        return $results;
    }

    protected function logImport($filename, $results)
    {
        ImportLog::create([
            'user_id' => Auth::id(),
            'filename' => $filename,
            'total_rows' => $results['success'] + $results['failed'],
            'successful_rows' => $results['success'],
            'errors' => json_encode($results['errors']),
            'warnings' => json_encode($results['warnings']),
            'type' => 'school',
        ]);
    }
}
