<?php

namespace App\Services;

use App\Imports\StudentImport;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class StudentImportService
{
    public function processExcel(UploadedFile $file)
    {
        $import = new StudentImport();
        Excel::import($import, $file);
        $results = $import->getResults();
        // Optional: log import
        return $results;
    }
}
