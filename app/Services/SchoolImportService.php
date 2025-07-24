<?php

namespace App\Services;

use App\Imports\SchoolImport;
use App\Models\ImportLog;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class SchoolImportService
{
    public function processExcel(UploadedFile $file)
    {
        // Import data
        $import = new SchoolImport();
        Excel::import($import, $file);
        
        // Get results
        $results = $import->getResults();
        
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