<?php

namespace App\Services;

use App\Imports\TeacherImport;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class TeacherImportService
{
    public function processExcel(UploadedFile $file)
    {
        $import = new TeacherImport();
        Excel::import($import, $file);
        $results = $import->getResults();
        // Optional: log import
        return $results;
    }
}
