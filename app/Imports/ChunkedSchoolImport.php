<?php

namespace App\Imports;

use App\Models\School;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;

class ChunkedSchoolImport implements ToCollection, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    protected $results = [
        'success' => 0,
        'failed' => 0,
        'errors' => [],
        'warnings' => [],
    ];

    protected $existingSchools = [];
    protected $activeSchools = [];

    public function __construct()
    {
        // Pre-load data once
        $this->existingSchools = School::withTrashed()->pluck('npsn', 'npsn')->toArray();
        $this->activeSchools = School::pluck('npsn', 'npsn')->toArray();
    }

    public function collection(Collection $rows)
    {
        Log::info('[CHUNKED_IMPORT] Processing chunk of ' . $rows->count() . ' rows');

        $validatedRows = [];
        $hasErrors = false;

        // Validate chunk
        foreach ($rows as $index => $row) {
            if (collect($row)->filter()->isEmpty()) {
                continue;
            }

            $action = strtoupper($row['aksi'] ?? 'CREATE');
            $validActions = ['CREATE', 'UPDATE', 'DELETE'];

            if (empty($row['npsn']) && !in_array($action, $validActions)) {
                continue;
            }

            // Cast data types
            if (isset($row['npsn'])) {
                $row['npsn'] = (string) $row['npsn'];
            }
            if (isset($row['telepon'])) {
                $row['telepon'] = (string) $row['telepon'];
            }

            try {
                $isValid = $this->validateRowData($row, $index, $action);
                if (!$isValid) {
                    $hasErrors = true;
                } else {
                    $validatedRows[] = ['row' => $row, 'index' => $index, 'action' => $action];
                }
            } catch (\Exception $e) {
                $this->addError($index, $e->getMessage());
                $hasErrors = true;
            }
        }

        if ($hasErrors) {
            Log::error('[CHUNKED_IMPORT] Chunk has errors, skipping');
            return;
        }

        // Process chunk
        $this->processChunk($validatedRows);
    }

    protected function validateRowData($row, $index, $action)
    {
        switch ($action) {
            case 'CREATE':
                return $this->validateCreateData($row, $index);
            case 'UPDATE':
                return $this->validateUpdateData($row, $index);
            case 'DELETE':
                return $this->validateDeleteData($row, $index);
            default:
                $this->addError($index, "Invalid action: {$action}");
                return false;
        }
    }

    protected function validateCreateData($row, $index)
    {
        $required = ['npsn', 'nama_sekolah', 'jenjang_pendidikan', 'status', 'alamat', 'email'];
        foreach ($required as $field) {
            if (empty($row[$field])) {
                $this->addError($index, "Required field '{$field}' is empty");
                return false;
            }
        }

        if (isset($this->activeSchools[$row['npsn']])) {
            $this->addError($index, "NPSN already exists: " . $row['npsn']);
            return false;
        }

        return true;
    }

    protected function validateUpdateData($row, $index)
    {
        if (empty($row['npsn'])) {
            $this->addError($index, "NPSN required for UPDATE");
            return false;
        }

        if (!isset($this->existingSchools[$row['npsn']])) {
            $this->addError($index, "School not found for update: " . $row['npsn']);
            return false;
        }

        return true;
    }

    protected function validateDeleteData($row, $index)
    {
        if (empty($row['npsn'])) {
            $this->addError($index, "NPSN required for DELETE");
            return false;
        }

        if (!isset($this->existingSchools[$row['npsn']])) {
            $this->addError($index, "School not found for deletion: " . $row['npsn']);
            return false;
        }

        return true;
    }

    protected function processChunk($validatedRows)
    {
        DB::transaction(function () use ($validatedRows) {
            foreach ($validatedRows as $validatedData) {
                $row = $validatedData['row'];
                $action = $validatedData['action'];

                try {
                    switch ($action) {
                        case 'CREATE':
                            $this->createSchool($row);
                            break;
                        case 'UPDATE':
                            $this->updateSchool($row);
                            break;
                        case 'DELETE':
                            $this->deleteSchool($row);
                            break;
                    }
                    $this->results['success']++;
                } catch (\Exception $e) {
                    $this->results['failed']++;
                    Log::error('[CHUNKED_IMPORT] Error processing row: ' . $e->getMessage());
                }
            }
        });
    }

    protected function createSchool($row)
    {
        $existing = School::withTrashed()->where('npsn', $row['npsn'])->first();

        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore();
                $school = $existing;
                $school->update($this->prepareSchoolData($row));
            } else {
                return; // Already exists
            }
        } else {
            $school = School::create($this->prepareSchoolData($row));
        }

        // Create admin user if password provided
        if (!empty($row['password_admin'])) {
            User::create([
                'name' => $row['kepala_sekolah'] ?? 'Admin Sekolah',
                'email' => $row['email'],
                'password' => Hash::make($row['password_admin']),
                'school_id' => $school->id,
            ])->assignRole('admin_sekolah');
        }
    }

    protected function updateSchool($row)
    {
        $school = School::where('npsn', $row['npsn'])->first();
        if (!$school) return;

        $school->update($this->prepareSchoolData($row));

        // Update admin user if password provided
        if (!empty($row['password_admin'])) {
            $user = User::updateOrCreate(
                ['email' => $row['email']],
                [
                    'name' => $row['kepala_sekolah'] ?? 'Admin Sekolah',
                    'password' => Hash::make($row['password_admin']),
                    'school_id' => $school->id,
                ]
            );

            if (!$user->hasRole('admin_sekolah')) {
                $user->assignRole('admin_sekolah');
            }
        }
    }

    protected function deleteSchool($row)
    {
        $school = School::where('npsn', $row['npsn'])->first();
        if (!$school) return;

        $school->users()->update(['school_id' => null]);
        $school->delete();
    }

    protected function prepareSchoolData($row)
    {
        return [
            'name' => $row['nama_sekolah'],
            'education_level' => $row['jenjang_pendidikan'],
            'status' => $row['status'],
            'address' => $row['alamat'],
            'desa' => $row['desa'] ?? null,
            'kecamatan' => $row['kecamatan'] ?? null,
            'kabupaten_kota' => $row['kabupaten_kota'] ?? null,
            'provinsi' => $row['provinsi'] ?? null,
            'google_maps_link' => $row['google_maps_link'] ?? null,
            'latitude' => $row['latitude'] ?? null,
            'longitude' => $row['longitude'] ?? null,
            'phone' => $row['telepon'] ?? null,
            'email' => $row['email'],
            'headmaster' => $row['kepala_sekolah'] ?? null,
        ];
    }

    protected function addError($index, $message)
    {
        $this->results['errors'][] = "Row " . ($index + 2) . ": {$message}";
    }

    public function getResults()
    {
        return $this->results;
    }

    public function chunkSize(): int
    {
        return 100; // Process 100 rows at a time
    }

    public function batchSize(): int
    {
        return 50; // Insert 50 records at a time
    }
}
